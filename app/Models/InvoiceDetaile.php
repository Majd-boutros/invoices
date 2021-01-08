<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceDetaile extends Model
{
    protected $table = "invoices_details";
    protected $fillable = [
        'id_invoice',
        'invoice_number',
        'product',
        'section',
        'status',
        'value_status',
        'payment_date',
        'note',
        'user'
    ];
    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}





