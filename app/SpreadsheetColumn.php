<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpreadsheetColumn extends Model
{
    //

    public static $columnLetters = ['','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];

    public $fillable = ['spreadsheet_id','column','label','validation'];

    public static $fieldtypes = ['string'=>'string','integer'=>'integer','date'=>'date','email'=>'email','alpha'=>'alpha','alpha_num'=>'alphanumeric','alpha_dash'=>'alphanumeric w/ dashes'];

}
