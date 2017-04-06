<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    //
    use SoftDeletes;
    protected $fillable = ['business_name'];



    public function users(){
        return $this->hasMany('\App\User','client_id');
    }
    public function spreadsheets(){
        return $this->hasMany('\App\Spreadsheet','client_id');
    }

    public function displayname(){
      return trim($this->business_name);
    }

}
