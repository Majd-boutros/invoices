<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = "products";
    protected $fillable = [
        'product_name',
        'description',
        'section_id'
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function section(){
        return $this->belongsTo('App\Models\Section','section_id','id');
    }

}
