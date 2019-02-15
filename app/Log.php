<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    //
    public $timestamps = false;

    protected $fillable = ['user_id','model','model_id','action','created_at'];



    public function auth(){
        return $this->belongsTo('\App\User','user_id')->withTrashed();
    }
    public function user(){
        return $this->belongsToMany('\App\User','logs','id','model_id')->wherePivot('model','user')->withTrashed();
    }
    public function client(){
        return $this->belongsToMany('\App\Client','logs','id','model_id')->wherePivot('model','client')->withTrashed();
    }
    public function spreadsheet(){
        return $this->belongsToMany('\App\Spreadsheet','logs','id','model_id')->wherePivot('model','spreadsheet');
    }
    public function report(){
        return $this->belongsToMany('\App\Report','logs','id','model_id')->wherePivot('model','report');
    }
    public function tracker(){
        return $this->belongsToMany('\App\Tracker','logs','id','model_id')->wherePivot('model','tracker');
    }


    public static function loguser($id,$action){
    	self::create(['user_id'=>\Auth::user()->id,'model'=>'user','model_id'=>$id,'action'=>$action, 'created_at'=>\DB::raw('CURRENT_TIMESTAMP')]);
    }
    public static function logreport($id,$action){
    	self::create(['user_id'=>\Auth::user()->id,'model'=>'report','model_id'=>$id,'action'=>$action, 'created_at'=>\DB::raw('CURRENT_TIMESTAMP')]);
    }
    public static function logtracker($id,$action){
        self::create(['user_id'=>\Auth::user()->id,'model'=>'tracker','model_id'=>$id,'action'=>$action, 'created_at'=>\DB::raw('CURRENT_TIMESTAMP')]);
    }
    public static function logspreadsheet($id,$action){
    	self::create(['user_id'=>\Auth::user()->id,'model'=>'spreadsheet','model_id'=>$id,'action'=>$action, 'created_at'=>\DB::raw('CURRENT_TIMESTAMP')]);
    }
    public static function logclient($id,$action){
    	self::create(['user_id'=>\Auth::user()->id,'model'=>'client','model_id'=>$id,'action'=>$action, 'created_at'=>\DB::raw('CURRENT_TIMESTAMP')]);
    }

}
