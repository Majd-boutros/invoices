<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;

class  HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index()
    {

        $all_invoices = Invoice::count();
        $count_paid_invoices = Invoice::where('value_status',1)->count();
        $count_unpaid_invoices = Invoice::where('value_status',2)->count();
        $count_paid_partial = Invoice::where('value_status',3)->count();

        $raito_of_paid_invoices = null;
        $raito_of_unpaid_invoices = null;
        $raito_of_paid_partial_invoices = null;

        if($count_paid_invoices==0){
            $raito_of_paid_invoices = 0;
        }else{
            $raito_of_paid_invoices =  ($count_paid_invoices/$all_invoices)*100;
        }
        if($count_unpaid_invoices==0){
            $raito_of_unpaid_invoices = 0;
        }else{
            $raito_of_unpaid_invoices = ($count_unpaid_invoices/$all_invoices)*100;
        }
        if($count_paid_partial==0){
            $raito_of_paid_partial_invoices = 0;
        }else{
            $raito_of_paid_partial_invoices = ($count_paid_partial/$all_invoices)*100;
        }

        $chartjs = app()->chartjs
            ->name('barChartTest')
            ->type('bar')
            ->size(['width' => 350, 'height' => 200])
            ->labels(['الفواتير الغير المدفوعة', 'الفواتير المدفوعة','الفواتير المدفوعة جزئيا'])
            ->datasets([
                [
                    "label" => "الفواتير الغير المدفوعة",
                    'backgroundColor' => ['#ec5858'],
                    'data' => [$raito_of_unpaid_invoices]
                ],
                [
                    "label" => "الفواتير المدفوعة",
                    'backgroundColor' => ['#81b214'],
                    'data' => [$raito_of_paid_partial_invoices]
                ],
                [
                    "label" => "الفواتير المدفوعة جزئيا",
                    'backgroundColor' => ['#ff9642'],
                    'data' => [$raito_of_paid_partial_invoices]
                ],

            ])
            ->options([]);

        $chartjs2 = app()->chartjs
            ->name('pieChartTest')
            ->type('pie')
            ->size(['width' => 340, 'height' => 200])
            ->labels(['الفواتير الغير المدفوعة', 'الفواتير المدفوعة','الفواتير المدفوعة جزئيا'])
            ->datasets([
                [
                    'backgroundColor' => ['#ec5858', '#81b214','#ff9642'],
                    'data' => [$raito_of_unpaid_invoices,$raito_of_paid_invoices,$raito_of_paid_partial_invoices]
                ]
            ])
            ->options([]);

        return view('home', compact('chartjs','chartjs2'));
    }
}
