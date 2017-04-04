<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    //
    protected $fillable = ['user_id','model','model_id','action'];



    public function users(){
        return $this->hasMany('\App\User','client_id');
    }
    public function spreadsheets(){
        return $this->hasMany('\App\Spreadsheet','client_id');
    }


    public static function user($id,$action){
    	self::create(['user_id'=>\Auth::user()->id,'model'=>'User','model_id'=>$id,'action'=>$action]);
    }
    public static function report($id,$action){
    	self::create(['user_id'=>\Auth::user()->id,'model'=>'Report','model_id'=>$id,'action'=>$action]);
    }
    public static function spreadsheet($id,$action){
    	self::create(['user_id'=>\Auth::user()->id,'model'=>'Spreadsheet','model_id'=>$id,'action'=>$action]);
    }

}
