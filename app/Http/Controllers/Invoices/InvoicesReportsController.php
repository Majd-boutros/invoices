<?php

namespace App\Http\Controllers\Invoices;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;

class InvoicesReportsController extends Controller
{
    public function index(){
        return view('reports.invoices_report');
    }

    public function Search_invoices(Request $request){
        $rdio = $request->rdio;
        // في حالة البحث بنوع الفاتورة
        if($rdio == 1){
            // في حالة عدم تحديد تاريخ
            $type = $request->type;
            if($type && $request->start_at =='' && $request->end_at ==''){
                //اظهار النتائج حسب نوع الفاتورة فقط
                $invoices = $this->selection()->where('status',$type)->get();
                return view('reports.invoices_report',compact('type'))->withDetails($invoices);
            }
            // في حالة تحديد تاريخ استحقاق
            else{
                //اظهار النتائج حسب نوع الفاتورة و تاريخ الاستحقاق (البداية و النهاية)
                $type = $request->type;
                $start_at = date($request->start_at);
                $end_at = date($request->end_at);
                $invoices = $this->selection()->where('status',$type)->whereBetween('invoice_Date',[$start_at,$end_at])->get();
                return view('reports.invoices_report',compact('type','start_at','end_at'))->withDetails($invoices);
            }

        }
        // في البحث برقم الفاتورة
        else{
            $invoice_number = $request->invoice_number;
            $invoices = $this->selection()->where('invoice_number',$invoice_number)->get();
            return view('reports.invoices_report')->withDetails($invoices);
        }
    }

    protected function selection(){
        $data = Invoice::select('id','invoice_number','invoice_date','due_date','product',
            'section_id','discount','value_vat','rate_vat','total','status','value_status')->with('section');
        return $data;
    }
}
