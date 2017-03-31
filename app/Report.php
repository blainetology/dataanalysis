<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    //
    protected $fillable = ['name','label','client_id','template_id','rules','active'];

    public function client(){
        return $this->belongsTo('\App\Client','client_id');
    }
    public function template(){
        return $this->belongsTo('\App\ReportTemplate','template_id');
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

}
