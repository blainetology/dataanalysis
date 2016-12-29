<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpreadsheetContent extends Model
{
    //


    protected $guarded = ['id','created_at','updated_at'];

    public function user(){
    	return $this->belongsTo('\App\User','added_by');
    }
}
