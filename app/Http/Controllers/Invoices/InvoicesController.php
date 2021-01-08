<?php

namespace App\Http\Controllers\Invoices;

use App\Exports\InvoicesExport;
use App\Notifications\Add_invoice_new;
use Maatwebsite\Excel\Facades\Excel;
use App\Notifications\AddInvoice;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Section;
use App\Models\Product;
use App\Models\InvoiceDetaile;
use App\Models\InvoiceAttachment;
use App\Models\User;
use Carbon\Carbon;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use DB;


class InvoicesController extends Controller
{

    public function index(){
        $invoices = Invoice::select('id','invoice_number','invoice_date','due_date','product','section_id',
            'discount','rate_vat','value_vat','total','value_status','status','note'
        )->with('section')->get();
        return view('invoices.invoices',compact('invoices'));
    }

    public function create(){
        $sections = Section::select('id','section_name')->get();
        return view('invoices.add_invoice',compact('sections'));
    }

    public function getProducts($section_id){
        $products = Product::select('id','product_name')->where('section_id',$section_id)->get();
        return json_encode($products);
    }

    public function store(Request $request){
        $invoice_id = null;
        $data1 = [];
        $data2 = [];
        $data3 = [];
        $data1 = [
            'invoice_number' => $request->invoice_number,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'product' => $request->product,
            'section_id' => $request->section,
            'amount_collection' => $request->amount_collection,
            'amount_commission' => $request->amount_commission,
            'discount' => $request->discount,
            'value_vat' => $request->value_vat,
            'rate_vat' => $request->rate_vat,
            'total' => $request->total,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
            'note' => $request->note,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];

        $invoice_id = Invoice::insertGetId($data1);

        $data2 = [
            'id_invoice' => $invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'Section' => $request->section,
            'status' => 'غير مدفوعة',
            'value_status' => 2,
            'note' => $request->note,
            'user' => auth()->user()->name,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];

        $invoice_detaile = InvoiceDetaile::insert($data2);


        if ($request->hasFile('pic')) {

            $invoice_id = $invoice_id;
            $image = $request->file('pic');
            $file_name = $image->getClientOriginalName();
            $invoice_number = $request->invoice_number;

            $data3 = [
                'file_name' => $file_name,
                'invoice_number' => $invoice_number,
                'created_by' => auth()->user()->name,
                'invoice_id' => $invoice_id,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];

            $invoice_attachments = InvoiceAttachment::insert($data3);

            // move pic
            $imageName = $request->pic->getClientOriginalName();
            $request->pic->move(public_path('Attachments/' . $invoice_number), $imageName);
        }

        //Email Notification
        $user_id = auth()->user()->id;
        $user = User::where('id',$user_id)->first();
        Notification::send($user, new AddInvoice($invoice_id));


        //Database Notification
        $user_name = auth()->user()->name;
        $invoice = Invoice::where('id',$invoice_id)->first();

        $users = User::where('id','!=',$user_id)->get();

        Notification::send($users,new Add_invoice_new($invoice,$user_name));

        $title = "تم اضإفة فاتورة جديدة بواسطة : $user_name" ;


            $this->sendNotificationToMultiDevice($user_id,$invoice_id,$title,Carbon::now(),null,null);

       

        session()->flash('Add','تم اضافة الفاتورة بنجاح');
        return redirect()->route('create.invoices');

    }

    public function edit($invoice_id){
        $invoice = Invoice::find($invoice_id);
        $sections = Section::select('id','section_name')->get();
        return view('invoices.edit_invoice',compact('invoice','sections'));
    }

    public function update(Request $request){
        $invoice_id = $request->invoice_id;
        $invoice = Invoice::findOrFail($invoice_id);
        $data=[
            'invoice_number'=>$request->invoice_number,
            'invoice_date'=>$request->invoice_date,
            'due_date'=>$request->due_date,
            'product'=>$request->product,
            'section_id'=>$request->section,
            'amount_collection'=>$request->amount_collection,
            'amount_commission'=>$request->amount_commission,
            'discount'=>$request->discount,
            'value_vat'=>$request->value_vat,
            'rate_vat'=>$request->rate_vat,
            'total'=>$request->total,
            'note'=>$request->note,
            'updated_at'=>Carbon::now()
        ];
        $invoice->update($data);
        session()->flash('edit', 'تم تعديل الفاتورة بنجاح');
        return redirect()->route('edit.invoice',$invoice_id);
    }

    public function destroy(Request $request){
        $invoice_id = $request->invoice_id;
        $invoice = Invoice::find($invoice_id);
        if($request->has('page_id') && $request->page_id==2){

            $invoice->delete();
            session()->flash('archive_invoice');
            return redirect()->route('invoiceArchive.get');

        }else {
            $details = InvoiceAttachment::where('invoice_id', $invoice_id)->first();
            if (!empty($details->invoice_number)) {

                Storage::disk('public_uploads')->deleteDirectory($details->invoice_number);
            }
            $invoice->forceDelete();
            session()->flash('delete_invoice');
            return redirect()->route('get.invoices');
        }
    }

    public function getPaymentStatus($invoice_id){
        $invoices = Invoice::find($invoice_id);
        return view('invoices.status_update',compact('invoices'));
    }

    public function getPaymentStatusUpdate(Request $request,$invoice_id){
        $invoices = Invoice::findOrFail($invoice_id);

        $value_status = null;

        if ($request->status === 'مدفوعة'){
            $value_status = 1;
        }
        elseif($request->status === 'مدفوعة جزئيا'){
            $value_status = 3;
        }else{
            $value_status = 2;
        }


        $invoices->update([
            'value_status' => $value_status,
            'status' => $request->status,
            'payment_date' => $request->payment_date,
        ]);
        InvoiceDetaile::insert([
            'id_Invoice' => $request->invoice_id,
            'invoice_number' => $request->invoice_number,
            'product' => $request->product,
            'section' => $request->section,
            'status' => $request->status,
            'value_status' => $value_status,
            'note' => $request->note,
            'payment_date' => $request->payment_date,
            'user' => auth()->user()->name,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        session()->flash('Status_Update');
        return redirect()->route('get.invoices');
    }

    public function Invoice_Paid(){
        $paid_invoices = Invoice::select('id','invoice_number','invoice_date','due_date','product','section_id',
            'discount','rate_vat','value_vat','total','value_status','status','note'
        )->where('value_status',1)->with('section')->get();
        return view('invoices.invoices_paid',compact('paid_invoices'));
    }
    public function Invoice_UnPaid(){
        $unpaid_invoices = Invoice::select('id','invoice_number','invoice_date','due_date','product','section_id',
            'discount','rate_vat','value_vat','total','value_status','status','note'
        )->where('value_status',2)->with('section')->get();
        return view('invoices.invoices_unpaid',compact('unpaid_invoices'));
    }
    public function Invoice_Partial(){
        $partial_invoices = Invoice::select('id','invoice_number','invoice_date','due_date','product','section_id',
            'discount','rate_vat','value_vat','total','value_status','status','note'
        )->where('value_status',3)->with('section')->get();
        return view('invoices.invoices_Partial',compact('partial_invoices'));
    }

    public function print($invoice_id){
        $invoice = Invoice::where('id',$invoice_id)->with('section')->first();
        return view('invoices.print_invoice',compact('invoice'));
    }

    public function export()
    {
        return Excel::download(new InvoicesExport, 'invoices.xlsx');
    }


    public function markAsRead(){

        auth()->user()->unreadNotifications->markAsRead();
        //return redirect()->back();
        return response()->json([
            'msg' => 'Done !'
        ]);
    }

    public function saveToken(Request $request)
    {
       // auth()->user()->update(['device_token'=>$request->token]);

        $user = User::where('id',auth()->user()->id)->first();
        $user->update([
            'device_token' => $request->token
        ]);

        return response()->json([
            'data' => $request
        ]);
        //return response()->json(['token saved successfully.']);
    }

    protected function sendNotificationTosingleDevice($token=null,$title=null,$body=null,$icon,$click_action){

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($body)
            ->setSound('default')
            ->setBadge(1)
            ->setIcon($icon)
            ->setClickAction($click_action);


        $dataBuilder = new PayloadDataBuilder();
       // $dataBuilder->addData(['a_data' => 'my_data']);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $token = $token;

        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);



        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

// return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();

// return Array (key : oldToken, value : new token - you must change the token in your database)
        $downstreamResponse->tokensToModify();

// return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();

// return Array (key:token, value:error) - in production you should remove from your database the tokens
        $downstreamResponse->tokensWithError();
    }

    protected function sendNotificationToMultiDevice($user_add_invoice,$invoice_id,$title=null,$body=null,$icon,$click_action){

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder->setBody($body)
            ->setSound('default')
            ->setBadge(1)
            ->setIcon($icon)
            ->setClickAction($click_action);

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['invoice_id' => $invoice_id]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        // You must change it to get your tokens

        $tokens = User::where('id','!=',$user_add_invoice)->pluck('device_token')->toArray();

        $downstreamResponse = FCM::sendTo($tokens, $option, $notification, $data);

        $downstreamResponse->numberSuccess();
        $downstreamResponse->numberFailure();
        $downstreamResponse->numberModification();

        // return Array - you must remove all this tokens in your database
        $downstreamResponse->tokensToDelete();

        // return Array (key : oldToken, value : new token - you must change the token in your database)
        $downstreamResponse->tokensToModify();

        // return Array - you should try to resend the message to the tokens in the array
        $downstreamResponse->tokensToRetry();

        // return Array (key:token, value:error) - in production you should remove from your database the tokens present in this array
        $downstreamResponse->tokensWithError();

    }

    public function getUnreadNotificationsall(){
        $unreadNotifications = DB::table('notifications')->where('read_at',null)->get();

        $count = $unreadNotifications->count();

        return response()->json([
            'count' => $count,
            'data' => $unreadNotifications
        ]);
    }
   
    public function getUnreadNotifications(){
        $unreadNotifications = DB::table('notifications')->where('read_at',null)->count();

        return response()->json([
            'data' => $unreadNotifications
        ]);
    }



}
