<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Invoice extends Model
{
    use SoftDeletes;

    protected $table = "invoices";
    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'payment_date',
        'due_date',
        'product',
        'section_id',
        'discount',
        'amount_collection',
        'amount_commission',
        'rate_vat',
        'value_vat',
        'total',
        'status',
        'value_status',
        'note',
        'user'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function section(){
        return $this->belongsTo('App\Models\Section','section_id','id');
    }


}





