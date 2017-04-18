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


    // custom reports

    public static function getContent($template,$rules){
        return self::$template($rules);
    }

    

    public static function people_set_aptmt($rules){
        $rules = json_decode($rules,true);
        $spreadsheet_id = $rules['spreadsheet'];
        $date = 'col'.array_search(strtoupper($rules['date']),\App\SpreadsheetColumn::$columnLetters);
        $set = 'col'.array_search(strtoupper($rules['set']),\App\SpreadsheetColumn::$columnLetters);
        $kept = 'col'.array_search(strtoupper($rules['kept']),\App\SpreadsheetColumn::$columnLetters);
        $allcount = \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[\Request::get('start_date',date('Y').'-01-01'),\Request::get('end_date',date('Y').'-12-31')])->count();
        $setcount = \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[\Request::get('start_date',date('Y').'-01-01'),\Request::get('end_date',date('Y').'-12-31')])->where($set,'yes')->count();
        $keptcount = \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[\Request::get('start_date',date('Y').'-01-01'),\Request::get('end_date',date('Y').'-12-31')])->where($kept,'yes')->count();
        return ['all'=>$allcount,'set'=>$setcount,'kept'=>$keptcount];
    }
    public static function total_amt_written($rules){
        $rules = json_decode($rules,true);
        $spreadsheet_id = $rules['spreadsheet'];
        $date = 'col'.array_search(strtoupper($rules['date']),\App\SpreadsheetColumn::$columnLetters);
        $month = 'col'.array_search(strtoupper($rules['month']),\App\SpreadsheetColumn::$columnLetters);
        $fia = 'col'.array_search(strtoupper($rules['fia']),\App\SpreadsheetColumn::$columnLetters);
        $aum = 'col'.array_search(strtoupper($rules['aum']),\App\SpreadsheetColumn::$columnLetters);
        $life = 'col'.array_search(strtoupper($rules['life']),\App\SpreadsheetColumn::$columnLetters);
        $results = \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[\Request::get('start_date',date('Y').'-01-01'),\Request::get('end_date',date('Y').'-12-31')])->orderBy($date,'asc')->get();
        $all = ['fia'=>0,'aum'=>0,'life'=>0];
        $months = [];
        foreach($results as $row){
            $all['fia']+=$row->$fia;
            $all['aum']+=$row->$aum;
            $all['life']+=$row->$life;
            if(!isset($months[$row->$month])){
                $months[$row->$month] = ['fia'=>$row->$fia,'aum'=>$row->$aum,'life'=>$row->life];
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
                    $advisors[$row->$advisor]['months'][$row->$month] = ['fia'=>$row->$fia,'aum'=>$row->$aum,'life'=>$row->life];
                }
                else{
                    $advisors[$row->$advisor]['months'][$row->$month]['fia']+=$row->$fia;
                    $advisors[$row->$advisor]['months'][$row->$month]['aum']+=$row->$aum;
                    $advisors[$row->$advisor]['months'][$row->$month]['life']+=$row->$life;
                }
            }
        }

        return ['all'=>$all,'months'=>$months,'advisors'=>$advisors];
    }
    public static function total_amt_pending($rules){
        $rules = json_decode($rules,true);
        $spreadsheet_id = $rules['spreadsheet'];
        $date = 'col'.array_search(strtoupper($rules['date']),\App\SpreadsheetColumn::$columnLetters);
        $written = 'col'.array_search(strtoupper($rules['written']),\App\SpreadsheetColumn::$columnLetters);
        $conditional = 'col'.array_search(strtoupper($rules['conditional']),\App\SpreadsheetColumn::$columnLetters);
        $value = $rules['value'];
        $results = \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->where($conditional,$value)->whereBetween($date,[\Request::get('start_date',date('Y').'-01-01'),\Request::get('end_date',date('Y').'-12-31')])->get();
        $total = 0;
        foreach($results as $row){
            if(!empty($row->$written))
                $total += $row->$written;
        }
        return ['total'=>$total];
    }
    public static function total_amt_written_advisor($rules){
        $rules = json_decode($rules,true);
        $spreadsheet_id = $rules['spreadsheet'];
        $advisor = 'col'.array_search(strtoupper($rules['advisor']),\App\SpreadsheetColumn::$columnLetters);
        $date = 'col'.array_search(strtoupper($rules['date']),\App\SpreadsheetColumn::$columnLetters);
        $month = 'col'.array_search(strtoupper($rules['month']),\App\SpreadsheetColumn::$columnLetters);
        $fia = 'col'.array_search(strtoupper($rules['fia']),\App\SpreadsheetColumn::$columnLetters);
        $aum = 'col'.array_search(strtoupper($rules['aum']),\App\SpreadsheetColumn::$columnLetters);
        $life = 'col'.array_search(strtoupper($rules['life']),\App\SpreadsheetColumn::$columnLetters);
        $results = \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[\Request::get('start_date',date('Y').'-01-01'),\Request::get('end_date',date('Y').'-12-31')])->orderBy($date,'asc')->get();
        $advisors = [];
        foreach($results as $row){
            if(!isset($advisors[$row->$advisor]))
                $advisors[$row->$advisor] = ['months'=>[],'all'=>['fia'=>0,'aum'=>0,'life'=>0]];
            $advisors[$row->$advisor]['all']['fia']+=$row->$fia;
            $advisors[$row->$advisor]['all']['aum']+=$row->$aum;
            $advisors[$row->$advisor]['all']['life']+=$row->$life;
            if(!isset($advisors[$row->$advisor]['months'][$row->$month])){
                $advisors[$row->$advisor]['months'][$row->$month] = ['fia'=>$row->$fia,'aum'=>$row->$aum,'life'=>$row->life];
            }
            else{
                $advisors[$row->$advisor]['months'][$row->$month]['fia']+=$row->$fia;
                $advisors[$row->$advisor]['months'][$row->$month]['aum']+=$row->$aum;
                $advisors[$row->$advisor]['months'][$row->$month]['life']+=$row->$life;
            }
        }
        return ['advisors'=>$advisors];
    }
    public static function total_amt_issued($rules){
        $rules = json_decode($rules,true);
        $spreadsheet_id = $rules['spreadsheet'];
        $date = 'col'.array_search(strtoupper($rules['date']),\App\SpreadsheetColumn::$columnLetters);
        $month = 'col'.array_search(strtoupper($rules['month']),\App\SpreadsheetColumn::$columnLetters);
        $fia = 'col'.array_search(strtoupper($rules['fia']),\App\SpreadsheetColumn::$columnLetters);
        $aum = 'col'.array_search(strtoupper($rules['aum']),\App\SpreadsheetColumn::$columnLetters);
        $results = \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[\Request::get('start_date',date('Y').'-01-01'),\Request::get('end_date',date('Y').'-12-31')])->orderBy($date,'asc')->get();
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
        return ['issued'=>$issued,'sources'=>$sources];
    }
    public static function total_amt_issued_source($rules){
        $rules = json_decode($rules,true);
        $spreadsheet_id = $rules['spreadsheet'];
        $source = 'col'.array_search(strtoupper($rules['source']),\App\SpreadsheetColumn::$columnLetters);
        $date = 'col'.array_search(strtoupper($rules['date']),\App\SpreadsheetColumn::$columnLetters);
        $month = 'col'.array_search(strtoupper($rules['month']),\App\SpreadsheetColumn::$columnLetters);
        $fia = 'col'.array_search(strtoupper($rules['fia']),\App\SpreadsheetColumn::$columnLetters);
        $results = \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[\Request::get('start_date',date('Y').'-01-01'),\Request::get('end_date',date('Y').'-12-31')])->orderBy($date,'asc')->get();
        $sources = [];
        foreach($results as $row){
            if(!isset($sources[$row->$source]))
                $sources[$row->$source] = 0;
            $sources[$row->$source]+=$row->$fia;
        }
        return ['sources'=>$sources];
    }
    public static function seminar_business_attained($rules){
        $rules = json_decode($rules,true);
        return ['rules'=>$rules];
    }


}
