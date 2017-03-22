<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    //
    use SoftDeletes;
    protected $fillable = ['name','client_id','template_id','rules','active'];

    public function client(){
        return $this->belongsTo('\App\Client','client_id');
    }
    public function template(){
        return $this->belongsTo('\App\ReportTemplate','template_id');
    }

}
