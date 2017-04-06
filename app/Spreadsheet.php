<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Spreadsheet extends Model
{
    //
    protected $fillable = ['name','client_id','active','list_order','sorting_col'];

    public function client(){
        return $this->belongsTo('\App\Client','client_id')->withTrashed();
    }
    public function columns(){
        return $this->hasMany('\App\SpreadsheetColumn','spreadsheet_id');
    }
    public function content(){
        $content = $this->hasMany('\App\SpreadsheetContent','spreadsheet_id')->where('revision_id',0)->orderBy('col'.\Request::get('sort_col',$this->sorting_col),'asc');
        foreach(\Request::input('filter',[]) as $col=>$filter){
            if(is_array($filter)){
                if(!empty($filter['min']) && !empty($filter['max']))
                    $content = $content->whereBetween($col,[$filter['min'],$filter['max']]);
                elseif(!empty($filter['min']))
                    $content = $content->where($col,'>=',$filter['min']);
                elseif(!empty($filter['max']))
                    $content = $content->where($col,'<=',$filter['max']);
            }
            else
                $content = $content->where($col,$filter);
        }
        return $content;
    }


    // SCOPES
    public function scopeActive($query){
        return $query->where('active',1);
    }
    public function scopeInactive($query){
        return $query->where('active',1);
    }

    // CUSTOM METHODS
    public function isActive(){
        return !empty($this->active);
    }

    public function displayname(){
      return trim($this->name);
    }

}
