<?php

namespace App\Http\Controllers\Invoices;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\Invoice;
use App\Models\InvoiceAttachment;

class InvoiceArchiveController extends Controller
{
    public function index(){
        $archive_invoices = Invoice::onlyTrashed()->with('section')->get();
        return view('invoices.archive_invoices',compact('archive_invoices'));
    }

    public function updateArchive(Request $request){
        $invoice_id = $request->invoice_id;
        $invoices = Invoice::where('id',$invoice_id)->withTrashed()->restore();
        session()->flash('restore_invoice');
        return redirect()->route('get.invoices');
    }

    public function destroyforce(Request $request){
        $invoice_id = $request->invoice_id;
        $invoice = Invoice::withTrashed()->find($invoice_id);
        $details = InvoiceAttachment::where('invoice_id', $invoice_id)->first();
        if (!empty($details->invoice_number)) {

            Storage::disk('public_uploads')->deleteDirectory($details->invoice_number);
        }
        $invoice->forceDelete();
        session()->flash('delete_invoice');
        return redirect()->route('invoiceArchive.get');
    }
}
