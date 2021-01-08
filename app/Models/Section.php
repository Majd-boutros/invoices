<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = "sections";
    protected $fillable =[
        'section_name',
        'description',
        'created_by'
    ];
    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    public function products(){
        return $this->hasMany('App\Models\Product','id','section_id');
    }

    public function invoices(){
        return $this->hasMany('App\Models\Invoice','section_id','id');
    }

}
