<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ReportTemplate extends Model
{
    //
    public $timestamps = false;
    protected $fillable = ['name','file','active'];

    public function report(){
        return $this->hasMany('\App\Report','template_id');
    }

}
