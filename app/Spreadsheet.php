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
        $content = $this->hasMany('\App\SpreadsheetContent','spreadsheet_id')->where('revision_id',0);
        foreach(\Request::input('filter',[]) as $col=>$filter){
            if(is_array($filter)){
                if(!empty($filter['start']) && !empty($filter['end']))
                    $content = $content->whereBetween($col,[$filter['start'],$filter['end']]);
                elseif(!empty($filter['start']))
                    $content = $content->where($col,'>=',$filter['start']);
                elseif(!empty($filter['end']))
                    $content = $content->where($col,'<=',$filter['end']);
            }
            else
                $content = $content->where($col,$filter);
        }
        return $content;
    }

}
