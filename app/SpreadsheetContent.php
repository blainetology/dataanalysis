<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpreadsheetContent extends Model
{
    //


    protected $fillable = ['spreadsheet_id','added_by','year','month'];

    public function __construct(){
        print_r($this->fillable);
        #for($x=1;$x<=26;$x++)
        #    $this->fillable[] = 'col'.$x;
    }
}
