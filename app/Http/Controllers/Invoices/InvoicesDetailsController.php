<?php

namespace App\Http\Controllers\Invoices;

use App\Http\Controllers\Controller;
use App\Models\InvoiceAttachment;
use Illuminate\Http\Request;
use App\Models\InvoiceDetaile;
use App\Models\Invoice;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use File;
use DB;

class InvoicesDetailsController extends Controller
{
    public function getInvoiceDetails($invoice_id){
        $invoice = Invoice::where('id',$invoice_id)->withTrashed()->with('section')->first();
        $details = InvoiceDetaile::where('id_invoice',$invoice_id)->get();
        $attachments = InvoiceAttachment::where('invoice_id',$invoice_id)->get();

        //make notification as read
        $notifications = DB::table('notifications')->where("data->id",'=',$invoice_id);

        if($notifications != '') {

            $notifications->update([
                'read_at' => Carbon::now()
            ]);
        }

        return view('invoices.details_invoices',compact('invoice','details','attachments'));
    }

    public function open_file($invoice_number,$file_name){

        $files = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number.'/'.$file_name);

        return response()->file($files);


    }

    public function get_file($invoice_number,$file_name){

        $files = Storage::disk('public_uploads')->getDriver()->getAdapter()->applyPathPrefix($invoice_number.'/'.$file_name);

        return response()->download($files);

    }

    public function destroy(Request $request)
    {
        $invoices = InvoiceAttachment::findOrFail($request->id_file);
        $invoices->delete();
        Storage::disk('public_uploads')->delete($request->invoice_number.'/'.$request->file_name);
        session()->flash('delete', 'تم حذف المرفق بنجاح');
        return back();
    }
}
