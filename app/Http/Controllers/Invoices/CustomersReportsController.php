<?php

namespace App\Http\Controllers\Invoices;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\Section;

class CustomersReportsController extends Controller
{
    public function index(){
        $sections = Section::select('id','section_name')->get();
        return view('reports.customers_report',compact('sections'));
    }

    public function Search_customers(Request $request){
        $sections = Section::select('id','section_name')->get();
// في حالة البحث بدون التاريخ
        if($request->section && $request->product && $request->start_at=='' && $request->end_at==''){
            $invoices = $this->selection()->where('section_id',$request->section)->where('product',$request->product)->get();
            return view('reports.customers_report',compact('sections'))->withDetails($invoices);
        }

// في حالة البحث بتاريخ
        else{
            $start_at = date($request->start_at);
            $end_at = date($request->end_at);
            $invoices = $this->selection()->where('section_id',$request->section)
                                            ->where('product',$request->product)
                                            ->whereBetween('invoice_date',[$start_at,$end_at])
                                            ->get();
            return view('reports.customers_report',compact('sections'))->withDetails($invoices);
        }
    }

    protected function selection(){
        $data = Invoice::select('id','invoice_number','invoice_date','due_date','product',
            'section_id','discount','value_vat','rate_vat','total','status','value_status')->with('section');
        return $data;
    }
}
