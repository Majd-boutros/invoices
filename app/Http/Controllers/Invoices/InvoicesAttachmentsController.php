<?php

namespace App\Http\Controllers\Invoices;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InvoiceAttachment;
use Carbon\Carbon;

class InvoicesAttachmentsController extends Controller
{
    public function addAttachments(Request $request){
        $this->validate($request,[
            'file_name' => 'mimes:pdf,jpeg,png,jpg'
        ],[
            'file_name.mimes' => 'صيغة المرفق يجب ان تكون   pdf, jpeg , png , jpg'
        ]);

        $file = $request->file('file_name');
        $file_name = $file->getClientOriginalName();

        $data = [
            'file_name' => $file_name,
            'invoice_number' => $request->invoice_number,
            'created_by' => auth()->user()->name,
            'invoice_id' => $request->invoice_id,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ];

        $attachment = InvoiceAttachment::insert($data);

        //move file
        $file->move(public_path('Attachments/'. $request->invoice_number), $file_name);


        session()->flash('Add', 'تم اضافة المرفق بنجاح');
        return redirect()->route('getDetails.invoices',$request->invoice_id);
    }
}
