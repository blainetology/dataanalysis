<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpreadsheetColumn extends Model
{
    //

    public static $columnLetters = ['','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'];

    public $fillable = ['spreadsheet_id','column','label','validation','type'];

    public static $fieldtypes = ['string'=>'text','numeric'=>'numeric','date'=>'date','currency'=>'currency','email'=>'email','notes'=>'notes (not sortable)','alpha'=>'alpha','alpha_num'=>'alphanumeric','alpha_dash'=>'alphanumeric w/ dashes'];

    public static function sheetCell($column,$content,$x,$y){
    	$output="";
        if(!empty($column->validation['in'])){
	        $output.='<select class="sheet_cell type_'.$column->type.'" id="content_'.$y.'_'.$x.'" data-row-id="'.$y.'" data-col-id="'.$x.'" data-type="'.$column->type.'" value="'.($content ? $content['col'.$x] : '').'" name="content['.$y.'][col'.$x.']"><option value=""></option>';
	        foreach(explode(',',$column->validation['in']) as $option)
	            $output.='<option value="'.trim($option).'" '.($content && $content['col'.$x] == trim($option) ? 'selected' : '').'>'.trim($option).'</option>';
	        $output.='</select>';
	    }
        else
	        $output.='<input class="sheet_cell type_'.$column->type.'" type="text" id="content_'.$y.'_'.$x.'" data-row-id="'.$y.'" data-col-id="'.$x.'" data-type="'.$column->type.'" value="'.($content ? $content['col'.$x] : '').'" name="content['.$y.'][col'.$x.']">';
	    return $output;
    }

}
