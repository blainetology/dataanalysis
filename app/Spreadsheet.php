<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Spreadsheet extends Model
{
    //
    protected $fillable = ['name','client_id','active'];

    public function client(){
        return $this->belongsTo('\App\Client','client_id');
    }
    public function columns(){
        return $this->hasMany('\App\SpreadsheetColumn','spreadsheet_id');
    }
    public function content(){
        return $this->hasMany('\App\SpreadsheetContent','spreadsheet_id')->where('revision_id',0)->orderBy('year','asc')->orderBy('month','asc');
    }

}
