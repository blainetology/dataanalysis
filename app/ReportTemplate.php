<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class ReportTemplate extends Model
{
    //
    public $timestamps = false;
    protected $fillable = ['name','file','active'];

    public static $start;
    public static $end;

    public function report(){
        return $this->hasMany('\App\Report','template_id');
    }


    // custom reports

    public static function getContent($template,$rules){
        self::$start = \Request::get('start_date',date('Y').'-01-01');
        self::$end = \Request::get('end_date',date('Y-m-d'));

        return self::$template($rules);
    }

    
    // PEOPLE THAT HAVE SET APPOINTMENTS
    public static function people_set_aptmt($rules){
        $rules = json_decode($rules,true);
        $spreadsheet_id = $rules['spreadsheet'];
        $date = 'col'.array_search(strtoupper($rules['date']),\App\SpreadsheetColumn::$columnLetters);
        $set = 'col'.array_search(strtoupper($rules['set']),\App\SpreadsheetColumn::$columnLetters);
        $kept = 'col'.array_search(strtoupper($rules['kept']),\App\SpreadsheetColumn::$columnLetters);
        $allcount = \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[self::$start,self::$end])->count();
        $setcount = \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[self::$start,self::$end])->where($set,'yes')->count();
        $keptcount = \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[self::$start,self::$end])->where($kept,'yes')->count();

        $advisors = null;
        if(!empty($rules['advisor'])){
            $advisor = 'col'.array_search(strtoupper($rules['advisor']),\App\SpreadsheetColumn::$columnLetters);
            $results = \App\SpreadsheetContent::select($advisor)->distinct()->where('spreadsheet_id',$spreadsheet_id)->get();
            $advisors = [];
            foreach($results as $row){
                if(!isset($advisors[$row->$advisor]))
                    $advisors[$row->$advisor] = ['all'=>0,'set'=>0,'kept'=>0];

                $advisors[$row->$advisor]['all']= \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[self::$start,self::$end])->where($advisor,$row->$advisor)->count();
                $advisors[$row->$advisor]['set']=\App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[self::$start,self::$end])->where($advisor,$row->$advisor)->where($set,'yes')->count();
                $advisors[$row->$advisor]['kept']=\App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[self::$start,self::$end])->where($advisor,$row->$advisor)->where($kept,'yes')->count();
            }
        }

        return ['all'=>$allcount,'set'=>$setcount,'kept'=>$keptcount,'advisors'=>$advisors];
    }

    // TOTAL AMOUNT WRITTEN
    public static function total_amt_written($rules){
        $rules = json_decode($rules,true);
        $spreadsheet_id = $rules['spreadsheet'];
        $date = 'col'.array_search(strtoupper($rules['date']),\App\SpreadsheetColumn::$columnLetters);
        $month = 'col'.array_search(strtoupper($rules['month']),\App\SpreadsheetColumn::$columnLetters);
        $fia = 'col'.array_search(strtoupper($rules['fia']),\App\SpreadsheetColumn::$columnLetters);
        $aum = 'col'.array_search(strtoupper($rules['aum']),\App\SpreadsheetColumn::$columnLetters);
        $life = 'col'.array_search(strtoupper($rules['life']),\App\SpreadsheetColumn::$columnLetters);
        $results = \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[self::$start,self::$end])->orderBy($date,'asc')->get();
        $all = ['fia'=>0,'aum'=>0,'life'=>0];
        $months = [];
        foreach($results as $row){
            $all['fia']+=$row->$fia;
            $all['aum']+=$row->$aum;
            $all['life']+=$row->$life;
            if(!isset($months[$row->$month])){
                $months[$row->$month] = ['fia'=>$row->$fia,'aum'=>$row->$aum,'life'=>$row->$life];
            }
            else{
                $months[$row->$month]['fia']+=$row->$fia;
                $months[$row->$month]['aum']+=$row->$aum;
                $months[$row->$month]['life']+=$row->$life;
            }
        }

        $advisors = null;
        if(!empty($rules['advisor'])){
            $advisor = 'col'.array_search(strtoupper($rules['advisor']),\App\SpreadsheetColumn::$columnLetters);
            $advisors = [];
            foreach($results as $row){
                if(!isset($advisors[$row->$advisor]))
                    $advisors[$row->$advisor] = ['months'=>[],'all'=>['fia'=>0,'aum'=>0,'life'=>0]];
                $advisors[$row->$advisor]['all']['fia']+=$row->$fia;
                $advisors[$row->$advisor]['all']['aum']+=$row->$aum;
                $advisors[$row->$advisor]['all']['life']+=$row->$life;
                if(!isset($advisors[$row->$advisor]['months'][$row->$month])){
                    $advisors[$row->$advisor]['months'][$row->$month] = ['fia'=>$row->$fia,'aum'=>$row->$aum,'life'=>$row->$life];
                }
                else{
                    $advisors[$row->$advisor]['months'][$row->$month]['fia']+=$row->$fia;
                    $advisors[$row->$advisor]['months'][$row->$month]['aum']+=$row->$aum;
                    $advisors[$row->$advisor]['months'][$row->$month]['life']+=$row->$life;
                }
            }
        }

        $weeks = null;
        if(!empty($rules['week']) && ($rules['week']=='sun' || $rules['week']=='mon') ){
            $weeks = [];

            $timestamp = strtotime(self::$start);
            $endtimestamp = strtotime(self::$end);
            for($x=$timestamp;$x<=$endtimestamp;$x+=(60*60*24*7)){
                if($rules['week']=='sun')
                    $starttimestamp = $x-(date('w',$x)*60*60*24);
                elseif($rules['week']=='mon')
                    $starttimestamp = $x-( (date('N',$x)-1)*60*60*24);
                $slug = date("Y-m-d",$starttimestamp);
                $weeks[$slug] = ['start'=>date('m/d/Y',$starttimestamp),'end'=>date('m/d/Y',$starttimestamp+(60*60*24*6)),'fia'=>0,'aum'=>0,'life'=>0];
            }
            foreach($results as $row){
                $timestamp = strtotime($row->$date);
                if($rules['week']=='sun')
                    $timestamp = $timestamp-(date('w',$timestamp)*60*60*24);
                elseif($rules['week']=='mon')
                    $timestamp = $timestamp-( (date('N',$timestamp)-1)*60*60*24);
                $slug = date("Y-m-d",$timestamp);
                #$slug = date("Y",$timestamp).'-'.date('W',$timestamp);
                if(!isset($weeks[$slug]))
                    $weeks[$slug] = ['start'=>date('m/d/Y',$timestamp),'end'=>date('m/d/Y',$timestamp+(60*60*24*6)),'fia'=>0,'aum'=>0,'life'=>0];
                $weeks[$slug]['fia']+=$row->$fia;
                $weeks[$slug]['aum']+=$row->$aum;
                $weeks[$slug]['life']+=$row->$life;
            }
        }

        return ['all'=>$all,'months'=>$months,'advisors'=>$advisors,'weeks'=>$weeks];
    }

    public static function total_amt_pending($rules){
        $rules = json_decode($rules,true);
        $spreadsheet_id = $rules['spreadsheet'];
        $date = 'col'.array_search(strtoupper($rules['date']),\App\SpreadsheetColumn::$columnLetters);
        $written = 'col'.array_search(strtoupper($rules['written']),\App\SpreadsheetColumn::$columnLetters);
        $conditional = 'col'.array_search(strtoupper($rules['conditional']),\App\SpreadsheetColumn::$columnLetters);
        $value = $rules['value'];
        $results = \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->where($conditional,$value)->whereBetween($date,[self::$start,self::$end])->get();
        $total = 0;
        foreach($results as $row){
            if(!empty($row->$written))
                $total += $row->$written;
        }
        return ['total'=>$total];
    }

    public static function total_amt_issued($rules){
        $rules = json_decode($rules,true);
        $spreadsheet_id = $rules['spreadsheet'];
        $date = 'col'.array_search(strtoupper($rules['date']),\App\SpreadsheetColumn::$columnLetters);
        $month = 'col'.array_search(strtoupper($rules['month']),\App\SpreadsheetColumn::$columnLetters);
        $fia = 'col'.array_search(strtoupper($rules['fia']),\App\SpreadsheetColumn::$columnLetters);
        $aum = 'col'.array_search(strtoupper($rules['aum']),\App\SpreadsheetColumn::$columnLetters);
        $results = \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[self::$start,self::$end])->orderBy($date,'asc')->get();
        $sources = null;
        $issued = ["fia"=>0,"aum"=>0,"total"=>0];

        foreach($results as $row){
            $issued['fia']+=$row->$fia;
            $issued['aum']+=$row->$aum;
            $issued['total']+=$row->$fia;
            $issued['total']+=$row->$aum;
        }

        if(!empty($rules['source'])){
            $sources = [];
            $source = 'col'.array_search(strtoupper($rules['source']),\App\SpreadsheetColumn::$columnLetters);
            foreach($results as $row){
                if(!isset($sources[$row->$source]))
                    $sources[$row->$source] = ["fia"=>0,"aum"=>0,"total"=>0];
                $sources[$row->$source]['fia']+=$row->$fia;
                $sources[$row->$source]['aum']+=$row->$aum;
                $sources[$row->$source]['total']+=$row->$fia;
                $sources[$row->$source]['total']+=$row->$aum;
            }
        }

        $weeks = null;
        if(!empty($rules['week']) && ($rules['week']=='sun' || $rules['week']=='mon') ){
            $weeks = [];

            $timestamp = strtotime(self::$start);
            $endtimestamp = strtotime(self::$end);
            for($x=$timestamp;$x<=$endtimestamp;$x+=(60*60*24*7)){
                if($rules['week']=='sun')
                    $starttimestamp = $x-(date('w',$x)*60*60*24);
                elseif($rules['week']=='mon')
                    $starttimestamp = $x-( (date('N',$x)-1)*60*60*24);
                $slug = date("Y-m-d",$starttimestamp);
                $weeks[$slug] = ['start'=>date('m/d/Y',$starttimestamp),'end'=>date('m/d/Y',$starttimestamp+(60*60*24*6)),'fia'=>0,'aum'=>0];
            }
            foreach($results as $row){
                $timestamp = strtotime($row->$date);
                if($rules['week']=='sun')
                    $timestamp = $timestamp-(date('w',$timestamp)*60*60*24);
                elseif($rules['week']=='mon')
                    $timestamp = $timestamp-( (date('N',$timestamp)-1)*60*60*24);
                $slug = date("Y-m-d",$timestamp);
                #$slug = date("Y",$timestamp).'-'.date('W',$timestamp);
                if(!isset($weeks[$slug]))
                    $weeks[$slug] = ['start'=>date('m/d/Y',$timestamp),'end'=>date('m/d/Y',$timestamp+(60*60*24*6)),'fia'=>0,'aum'=>0];
                $weeks[$slug]['fia']+=$row->$fia;
                $weeks[$slug]['aum']+=$row->$aum;
            }
        }

        return ['issued'=>$issued,'sources'=>$sources,'weeks'=>$weeks];
    }

    public static function seminar_business_attained($rules){
        $rules = json_decode($rules,true);
        $spreadsheet_id = $rules['spreadsheet'];
        $date = 'col'.array_search(strtoupper($rules['date']),\App\SpreadsheetColumn::$columnLetters);
        if(!empty($rules['location']))
            $location = 'col'.array_search(strtoupper($rules['location']),\App\SpreadsheetColumn::$columnLetters);
        else
            $location = null;
        $month = 'col'.array_search(strtoupper($rules['month']),\App\SpreadsheetColumn::$columnLetters);
        $fia = 'col'.array_search(strtoupper($rules['fia']),\App\SpreadsheetColumn::$columnLetters);
        $aum = 'col'.array_search(strtoupper($rules['aum']),\App\SpreadsheetColumn::$columnLetters);
        $life = 'col'.array_search(strtoupper($rules['life']),\App\SpreadsheetColumn::$columnLetters);
        $results = \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[self::$start,self::$end])->orderBy($date,'asc')->get();
        $all = ['fia'=>0,'aum'=>0,'life'=>0];
        $months = [];
        foreach($results as $row){
            $all['fia']+=$row->$fia;
            $all['aum']+=$row->$aum;
            $all['life']+=$row->$life;
            if(!isset($months[$row->$month])){
                $months[$row->$month] = ['fia'=>$row->$fia,'aum'=>$row->$aum,'life'=>$row->$life];
            }
            else{
                $months[$row->$month]['fia']+=$row->$fia;
                $months[$row->$month]['aum']+=$row->$aum;
                $months[$row->$month]['life']+=$row->$life;
            }
        }

        $seminars = null;
        if(!empty($rules['seminar'])){
            $seminar = 'col'.array_search(strtoupper($rules['seminar']),\App\SpreadsheetColumn::$columnLetters);
            $seminars = [];
            foreach($results as $row){
                $index = $row->$seminar.($location ? ' - '.$row->$location : '').' - '.date('m/d/Y', strtotime($row->$date));
                if(!isset($seminars[$index]))
                    $seminars[$index] = ['months'=>[],'all'=>['fia'=>0,'aum'=>0,'life'=>0]];
                $seminars[$index]['all']['fia']+=$row->$fia;
                $seminars[$index]['all']['aum']+=$row->$aum;
                $seminars[$index]['all']['life']+=$row->$life;
                if(!isset($seminars[$index]['months'][$row->$month])){
                    $seminars[$index]['months'][$row->$month] = ['fia'=>$row->$fia,'aum'=>$row->$aum,'life'=>$row->$life];
                }
                else{
                    $seminars[$index]['months'][$row->$month]['fia']+=$row->$fia;
                    $seminars[$index]['months'][$row->$month]['aum']+=$row->$aum;
                    $seminars[$index]['months'][$row->$month]['life']+=$row->$life;
                }
            }
        }
        return ['all'=>$all,'months'=>$months,'seminars'=>$seminars];
    }


}
