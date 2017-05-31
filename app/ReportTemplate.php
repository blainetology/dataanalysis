<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

use App\Geocode;

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

        $sources = null;
        if(!empty($rules['source'])){
            $source = 'col'.array_search(strtoupper($rules['source']),\App\SpreadsheetColumn::$columnLetters);
            $results = \App\SpreadsheetContent::select($source)->distinct()->where('spreadsheet_id',$spreadsheet_id)->get();
            $sources = [];
            foreach($results as $row){
                if(!isset($sources[$row->$source]))
                    $sources[$row->$source] = ['all'=>0,'set'=>0,'kept'=>0];

                $sources[$row->$source]['all']= \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[self::$start,self::$end])->where($source,$row->$source)->count();
                $sources[$row->$source]['set']=\App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[self::$start,self::$end])->where($source,$row->$source)->where($set,'yes')->count();
                $sources[$row->$source]['kept']=\App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[self::$start,self::$end])->where($source,$row->$source)->where($kept,'yes')->count();
            }
        }

        $seminars = null;
        if(!empty($rules['source'])){
            $seminar_date = 'col'.array_search(strtoupper($rules['seminar_date']),\App\SpreadsheetColumn::$columnLetters);
            $seminar_type = 'col'.array_search(strtoupper($rules['seminar_type']),\App\SpreadsheetColumn::$columnLetters);
            $results = \App\SpreadsheetContent::select($seminar_type,$seminar_date)->distinct()->where('spreadsheet_id',$spreadsheet_id)->get();
            $seminars = [];
            foreach($results as $row){
                if(!isset($seminars[$row->$seminar_type.'-'.$row->$seminar_date]))
                    $seminars[$row->$seminar_type.'-'.$row->$seminar_date] = ['all'=>0,'set'=>0,'kept'=>0,'type'=>$row->$seminar_type,'date'=>date('m/d/Y',strtotime($row->$seminar_date))];

                $seminars[$row->$seminar_type.'-'.$row->$seminar_date]['all']= \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[self::$start,self::$end])->where($seminar_type,$row->$seminar_type)->where($seminar_date,$row->$seminar_date)->count();
                $seminars[$row->$seminar_type.'-'.$row->$seminar_date]['set']=\App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[self::$start,self::$end])->where($seminar_type,$row->$seminar_type)->where($seminar_date,$row->$seminar_date)->where($set,'yes')->count();
                $seminars[$row->$seminar_type.'-'.$row->$seminar_date]['kept']=\App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[self::$start,self::$end])->where($seminar_type,$row->$seminar_type)->where($seminar_date,$row->$seminar_date)->where($kept,'yes')->count();
            }
        }
        return ['all'=>$allcount,'set'=>$setcount,'kept'=>$keptcount,'advisors'=>$advisors,'sources'=>$sources,'seminars'=>$seminars];
    }

    // TOTAL AMOUNTS
    public static function total_amounts($rules){
        $rules = json_decode($rules,true);
        $spreadsheet_id = $rules['spreadsheet'];
        $date = 'col'.array_search(strtoupper($rules['date']),\App\SpreadsheetColumn::$columnLetters);

        $conditional = $operator = $value = null;

        if(!empty($rules['conditional']) && !empty($rules['operator']) && $rules['value'] != ""){
            $conditional = 'col'.array_search(strtoupper($rules['conditional']),\App\SpreadsheetColumn::$columnLetters);
            $operator = $rules['operator'];
            $value = $rules['value'];
        }

        // setup which columns to display in the reports table
        $availableColumns = \App\SpreadsheetColumn::where('spreadsheet_id',$spreadsheet_id)->pluck('label','column');

        $temp=[];
        $columns = explode("\n",$rules['columns']);
        foreach($columns as $column){
            $row = explode('||',$column);

             if(in_array(strtoupper(trim($row[0])), \App\SpreadsheetColumn::$columnLetters))
                $index = 'col'.array_search(strtoupper(trim($row[0])),\App\SpreadsheetColumn::$columnLetters);
            else
                $index = trim($row[0]);

           $temp[(string)$index] = ['equation'=>trim($row[0]),'type'=>trim($row[1]),'label'=>trim($row[2]), 'total'=>trim($row[3])];
        }
        $columns = $temp;

        // setup which sections to display
        $sections=[];
        $letters = explode(',',$rules['sections']);
        foreach($letters as $letter){
            $index = array_search(strtoupper(trim($letter)),\App\SpreadsheetColumn::$columnLetters);
            $sections[$index] = $index;
            $results = \App\SpreadsheetColumn::where('spreadsheet_id',$spreadsheet_id)->whereIn('column',$sections)->get();
            foreach($results as $row)
                $sections[$row->column] = ['label'=>$row->label,'data'=>[]];
        }

        // let's get the content from the database
        $query = \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id);
        if($conditional && $operator && $value)
            $query = $query->where($conditional,$operator,$value);
        $results = $query->whereBetween($date,[self::$start,self::$end])->orderBy($date,'desc')->get();
        
        // let's total it all together first
        $all = ['all'=>['count'=>0,'cols'=>[]]];
        foreach($columns as $key=>$label)
            $all['all']['cols'][$key] = 0;

        foreach($results as $row){
            foreach($columns as $key=>$label)
                $all['all']['cols'][$key] += $row->{$key};
            $all['all']['count']++;
        }

        // now set the columns with custom equations
        $search = ['count'];
        $replace = [$all['all']['count']];
        for($x=count(\App\SpreadsheetColumn::$columnLetters)-1;$x>0;$x--){
            $search[] = \App\SpreadsheetColumn::$columnLetters[$x];
            if(isset($all['all']['cols']['col'.$x]))
                $replace[] = $all['all']['cols']['col'.$x];
            else
                $replace[] = "0";
        }
        foreach($columns as $key=>$column){
            if(!isset($availableColumns[str_replace('col','',$key)])){
                $value = str_replace($search,$replace,$key);
                $all['all']['cols'][$key] = @self::calculate($value);
            }
        }

        // and break it down by months
        $months = null;
        if(!empty($rules['month'])){
            $months = [];

            $starttimestamp = strtotime(self::$start);
            $endtimestamp = strtotime(self::$end);
            for($x=$endtimestamp;$x>=$starttimestamp;$x-=(60*60*24)){
                $slug = date("F Y",$x);
                if(!isset($months[$slug])){
                    $months[$slug] = ['count'=>0,'cols'=>[]];
                    foreach($columns as $key=>$label)
                        $months[$slug]['cols'][$key] = 0;
                }
            }


            foreach($results as $row){
                $row->month = date('F Y',strtotime($row->$date));
                if(!isset($months[$row->month])){
                    $months[$row->month] = ['count'=>0,'cols'=>[]];
                    foreach($columns as $key=>$label)
                        $months[$row->month]['cols'][$key] = 0;
                }
                foreach($columns as $key=>$label)
                    $months[$row->month]['cols'][$key] += $row->{$key};
                $months[$row->month]['count']++;
            }

            // now set the columns with custom equations
            foreach($months as $month=>$data){
                $replace = [$data['count']];
                for($x=count(\App\SpreadsheetColumn::$columnLetters)-1;$x>0;$x--){
                    if(isset($data['cols']['col'.$x]))
                        $replace[] = $data['cols']['col'.$x];
                    else
                        $replace[] = "0";
                }
                foreach($columns as $key=>$column){
                    if(!isset($availableColumns[str_replace('col','',$key)])){
                        $value = str_replace($search,$replace,$key);
                        $months[$month]['cols'][$key] = @self::calculate($value);
                    }
                }
            }
            $all['months'] = $months;
        }

        // and break it down by weeks
        $weeks = null;
        if(!empty($rules['week'])){
            $weeks = [];

            $timestamp = strtotime(self::$start);
            $endtimestamp = strtotime(self::$end);
            for($x=$timestamp;$x<=$endtimestamp;$x+=(60*60*24*7)){
                if($rules['week']=='SUN')
                    $starttimestamp = $x-(date('w',$x)*60*60*24);
                elseif($rules['week']=='MON')
                    $starttimestamp = $x-( (date('N',$x)-1)*60*60*24);
                $slug = date("Y-m-d",$starttimestamp);
                $weeks[$slug] = ['start'=>date('m/d/Y',$starttimestamp),'end'=>date('m/d/Y',$starttimestamp+(60*60*24*6)),'count'=>0,'cols'=>[]];
                foreach($columns as $key=>$label)
                    $weeks[$slug]['cols'][$key]=0;
            }
            foreach($results as $row){
                $timestamp = strtotime($row->$date);
                if($rules['week']=='SUN')
                    $timestamp = $timestamp-(date('w',$timestamp)*60*60*24);
                elseif($rules['week']=='MON')
                    $timestamp = $timestamp-( (date('N',$timestamp)-1)*60*60*24);
                $slug = date("Y-m-d",$timestamp);
                #$slug = date("Y",$timestamp).'-'.date('W',$timestamp);
                foreach($columns as $key=>$label)
                    $weeks[$slug]['cols'][$key]+=$row->{$key};
                $weeks[$slug]['count']++;
            }

            // now set the columns with custom equations
            foreach($weeks as $week=>$data){
                $replace = [$data['count']];
                for($x=count(\App\SpreadsheetColumn::$columnLetters)-1;$x>0;$x--){
                    if(isset($data['cols']['col'.$x]))
                        $replace[] = $data['cols']['col'.$x];
                    else
                        $replace[] = "0";
                }
                foreach($columns as $key=>$column){
                    if(!isset($availableColumns[str_replace('col','',$key)])){
                        $value = str_replace($search,$replace,$key);
                        $weeks[$week]['cols'][$key] = @self::calculate($value);
                    }
                }
            }
            $all['weeks'] = $weeks;
        }
        if($weeks)
            krsort($weeks);


        // now by sections
        foreach($sections as $id=>$section){
            $col = 'col'.$id;
            foreach($results as $row){
                $row->month = date('F Y',strtotime($row->$date));
                // all
                if(!isset($sections[$id]['data'][$row->$col]['all']['count']))
                    $sections[$id]['data'][$row->$col]['all']['count']=0;
                $sections[$id]['data'][$row->$col]['all']['count']++;
                foreach($columns as $key=>$label){
                    if(!isset($sections[$id]['data'][$row->$col]['all']['cols'][$key]))
                        $sections[$id]['data'][$row->$col]['all']['cols'][$key]=0;
                    $sections[$id]['data'][$row->$col]['all']['cols'][$key] += $row->{$key};
                }
                //month
                if(!empty($rules['month']) && !empty($rules['monthsections'])){

                    $starttimestamp = strtotime(self::$start);
                    $endtimestamp = strtotime(self::$end);
                    for($x=$endtimestamp;$x>=$starttimestamp;$x-=(60*60*24)){
                        $slug = date("F Y",$x);
                        if(!isset($sections[$id]['data'][$row->$col]['months'][$slug])){
                            $sections[$id]['data'][$row->$col]['months'][$slug] = ['count'=>0,'cols'=>[]];
                            foreach($columns as $key=>$label)
                                $sections[$id]['data'][$row->$col]['months'][$slug]['cols'][$key] = 0;
                        }
                    }

                    if(!isset($sections[$id]['data'][$row->$col]['months'][$row->month])){
                        $sections[$id]['data'][$row->$col]['months'][$row->month] = ['count'=>0,'cols'=>[]];
                        foreach($columns as $key=>$label)
                            $sections[$id]['data'][$row->$col]['months'][$row->month]['cols'][$key] = 0;
                    }
                    foreach($columns as $key=>$label)
                        $sections[$id]['data'][$row->$col]['months'][$row->month]['cols'][$key] += $row->{$key};
                    $sections[$id]['data'][$row->$col]['months'][$row->month]['count']++;
                }

                // weeks
                if(!empty($rules['week']) && !empty($rules['weeksections'])){

                    $timestamp = strtotime(self::$start);
                    $endtimestamp = strtotime(self::$end);
                    for($x=$timestamp;$x<=$endtimestamp;$x+=(60*60*24*7)){
                        if($rules['week']=='SUN')
                            $starttimestamp = $x-(date('w',$x)*60*60*24);
                        elseif($rules['week']=='MON')
                            $starttimestamp = $x-( (date('N',$x)-1)*60*60*24);
                        $slug = date("Y-m-d",$starttimestamp);
                        if(!isset($sections[$id]['data'][$row->$col]['weeks'][$slug]))
                            $sections[$id]['data'][$row->$col]['weeks'][$slug] = ['start'=>date('m/d/Y',$starttimestamp),'end'=>date('m/d/Y',$starttimestamp+(60*60*24*6)),'count'=>0,'cols'=>[]];
                        foreach($columns as $key=>$label){
                            if(!isset($sections[$id]['data'][$row->$col]['weeks'][$slug]['cols'][$key]))
                                $sections[$id]['data'][$row->$col]['weeks'][$slug]['cols'][$key]=0;
                        }
                    }
                    $timestamp = strtotime($row->$date);
                    if($rules['week']=='SUN')
                        $timestamp = $timestamp-(date('w',$timestamp)*60*60*24);
                    elseif($rules['week']=='MON')
                        $timestamp = $timestamp-( (date('N',$timestamp)-1)*60*60*24);
                    $slug = date("Y-m-d",$timestamp);
                    foreach($columns as $key=>$label)
                        $sections[$id]['data'][$row->$col]['weeks'][$slug]['cols'][$key] += $row->{$key};
                    $sections[$id]['data'][$row->$col]['weeks'][$slug]['count']++;
                }
                if(isset($sections[$id]['data'][$row->$col]['weeks']))
                    krsort($sections[$id]['data'][$row->$col]['weeks']);
            }
        }
        // now set the columns with custom equations
        foreach($sections as $id=>$section){
            foreach($section['data'] as $id2=>$section2){
                if(isset($section2['all'])){
                        $data = $section2['all'];
                        $replace = [$data['count']];
                        for($x=count(\App\SpreadsheetColumn::$columnLetters)-1;$x>0;$x--){
                            if(isset($data['cols']['col'.$x]))
                                $replace[] = $data['cols']['col'.$x];
                            else
                                $replace[] = "0";
                        }
                        foreach($columns as $key=>$column){
                            if(!isset($availableColumns[str_replace('col','',$key)])){
                                $value = str_replace($search,$replace,$key);
                                $sections[$id]['data'][$id2]['all']['cols'][$key] = @self::calculate($value);
                            }
                        }
                }
                if(isset($section2['months'])){
                    foreach($section2['months'] as $month=>$data){
                        $replace = [$data['count']];
                        for($x=count(\App\SpreadsheetColumn::$columnLetters)-1;$x>0;$x--){
                            if(isset($data['cols']['col'.$x]))
                                $replace[] = $data['cols']['col'.$x];
                            else
                                $replace[] = "0";
                        }
                        foreach($columns as $key=>$column){
                            if(!isset($availableColumns[str_replace('col','',$key)])){
                                $value = str_replace($search,$replace,$key);
                                $sections[$id]['data'][$id2]['months'][$month]['cols'][$key] = @self::calculate($value);
                            }
                        }
                    }
                }
                if(isset($section2['weeks'])){
                    foreach($section2['weeks'] as $week=>$data){
                        $replace = [$data['count']];
                        for($x=count(\App\SpreadsheetColumn::$columnLetters)-1;$x>0;$x--){
                            if(isset($data['cols']['col'.$x]))
                                $replace[] = $data['cols']['col'.$x];
                            else
                                $replace[] = "0";
                        }
                        foreach($columns as $key=>$column){
                            if(!isset($availableColumns[str_replace('col','',$key)])){
                                $value = str_replace($search,$replace,$key);
                                $sections[$id]['data'][$id2]['weeks'][$week]['cols'][$key] = @self::calculate($value);
                            }
                        }
                    }
                }
            }

        }
        return ['columns'=>$columns,'all'=>$all,'sections'=>$sections];
    }

    public static function mapped_totals($rules){

        $rules = json_decode($rules,true);
        $spreadsheet_id = $rules['spreadsheet'];
        $date = 'col'.array_search(strtoupper($rules['date']),\App\SpreadsheetColumn::$columnLetters);

        // figure out the address format for geo coding
        $rules['location'] = preg_replace('/([A-Z])/',"*$1*",trim($rules['location']));
        $location_format = preg_replace('/[^a-zA-Z0-9\* ]/',' ',$rules['location']);
        $location_format = explode(' ',$location_format);
        $geo_cols = [];
        $geo_codes = [];
        $geo_search = [];
        foreach($location_format as $l_col){
            if(!empty($l_col)){
                $geo_cols[$l_col] = 'col'.array_search(strtoupper(str_replace('*','',$l_col)),\App\SpreadsheetColumn::$columnLetters);
                $geo_search[]=$l_col;
            }
        }

        // setup which columns to display
        $columns=[];
        $letters = explode(',',$rules['columns']);
        foreach($letters as $letter){
            $index = array_search(strtoupper(trim($letter)),\App\SpreadsheetColumn::$columnLetters);
            $columns[(string)$index] = $index;
            $results = \App\SpreadsheetColumn::where('spreadsheet_id',$spreadsheet_id)->whereIn('column',$columns)->get();
            foreach($results as $row)
                $columns[(string)$row->column] = $row->label;
        }

        // setup which sections to display
        $sections=[];
        $letters = explode(',',$rules['sections']);
        foreach($letters as $letter){
            $index = array_search(strtoupper(trim($letter)),\App\SpreadsheetColumn::$columnLetters);
            $sections['col'.$index] = $index;
            $results = \App\SpreadsheetColumn::where('spreadsheet_id',$spreadsheet_id)->whereIn('column',$sections)->get();
            foreach($results as $row)
                $sections['col'.$row->column] = ['label'=>$row->label,'data'=>[]];
        }

        // let's get the content from the database
        $query = \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id);
        $results = $query->whereBetween($date,[self::$start,self::$end])->orderBy($date,'desc')->get();

        $all = [];
        foreach($results as $row){
            $geo_replace = [];
            foreach($geo_cols as $letter=>$l_col){
                $geo_replace[]=$row->{$l_col};
            }
            $location = str_replace($geo_search, $geo_replace, $rules['location']);
            foreach($columns as $key=>$label){
                if(!isset($all[$location]['geocode'])){
                    if(isset($geo_codes[$location]))
                        $all[$location]['geocode'] = $geo_codes[$location];
                    else{
                         $geocode = Geocode::where('address',$location)->whereNotNull('latitude')->whereNotNull('longitude')->first();
                         if($geocode){
                            $geo_codes[$location] = ['latitude'=>$geocode->latitude,'longitude'=>$geocode->longitude];
                            $all[$location]['geocode'] = $geo_codes[$location];
                         }
                         else{
                            $geocode = self::geocode($location);
                             if($geocode){
                                $geo_codes[$location] =$geocode;
                                $all[$location]['geocode'] = $geo_codes[$location];
                                Geocode::create(['address'=>$location,'latitude'=>$geocode['latitude'],'longitude'=>$geocode['longitude']]);
                             }
                         }
                    }
                }
                if(!isset($all[$location]['all']))
                    $all[$location]['all']=0;
                if(!isset($all[$location]['count']))
                    $all[$location]['count']=0;
                if(!isset($all[$location]['cols']['col'.$key]))
                    $all[$location]['cols']['col'.$key]=0;
                $all[$location]['cols']['col'.$key] += $row->{'col'.$key};
                $all[$location]['all'] += $row->{'col'.$key};
                $all[$location]['count']++;
                $all['color']=0;
            }
        }

        // now by sections
        foreach($sections as $id=>$section){
            $color=1;
            $col = $id;
            foreach($results as $row){
                $geo_replace = [];
                foreach($geo_cols as $letter=>$l_col){
                    $geo_replace[]=$row->{$l_col};
                }
                $location = str_replace($geo_search, $geo_replace, $rules['location']);
                if(!isset($sections[$id]['data'][$row->$col]['color'])){
                    $sections[$id]['data'][$row->$col]['color']=$color;
                    $color++;
                }
                foreach($columns as $key=>$label){
                    if(!isset($sections[$id]['data'][$row->$col][$location]['all']))
                        $sections[$id]['data'][$row->$col][$location]['all']=0;
                    if(!isset($sections[$id]['data'][$row->$col][$location]['count']))
                        $sections[$id]['data'][$row->$col][$location]['count']=0;
                    if(!isset($sections[$id]['data'][$row->$col][$location]['cols']['col'.$key]))
                        $sections[$id]['data'][$row->$col][$location]['cols']['col'.$key]=0;


                    if(!isset($sections[$id]['data'][$row->$col][$location]['geocode'])){
                        if(isset($geo_codes[$location]))
                            $sections[$id]['data'][$row->$col][$location]['geocode'] = $geo_codes[$location];
                        else{
                             $geocode = Geocode::where('address',$location)->whereNotNull('latitude')->whereNotNull('longitude')->first();
                             if($geocode){
                                $geo_codes[$location] = ['latitude'=>$geocode->latitude,'longitude'=>$geocode->longitude];
                                $sections[$id]['data'][$row->$col][$location]['geocode'] = $geo_codes[$location];
                             }
                             else{
                                $geocode = self::geocode($location);
                                 if($geocode){
                                    $geo_codes[$location] =$geocode;
                                    $sections[$id]['data'][$row->$col][$location]['geocode'] = $geo_codes[$location];
                                    Geocode::create(['address'=>$location,'latitude'=>$geocode['latitude'],'longitude'=>$geocode['longitude']]);
                                 }
                             }
                        }
                    }
     

                    $sections[$id]['data'][$row->$col][$location]['cols']['col'.$key] += $row->{'col'.$key};
                    $sections[$id]['data'][$row->$col][$location]['all'] += $row->{'col'.$key};
                    $sections[$id]['data'][$row->$col][$location]['count']++;
                }
            }
        }
        return ['columns'=>$columns,'all'=>$all,'sections'=>$sections];

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

    private static function curl($url,$post=NULL){
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $url);
        $contents = curl_exec($c);
        curl_close($c);

        if($contents) 
            return $contents;
        else 
            return false;
    }
    private static function geocode($address){
        
        $url = "https://maps.google.com/maps/api/geocode/json?sensor=false&key=AIzaSyADHSrojKFkUvVCmQrh1yfkPNhC25xLIzE&address=".urlencode($address);
        $resp_json = self::curl($url);
        $resp = json_decode($resp_json, true);

        if($resp['status']='OK'){
            $resp['results'][0]['geometry']['location']['longitude'] = $resp['results'][0]['geometry']['location']['lng'];
            $resp['results'][0]['geometry']['location']['latitude'] = $resp['results'][0]['geometry']['location']['lat'];
            unset($resp['results'][0]['geometry']['location']['lat']);
            unset($resp['results'][0]['geometry']['location']['lng']);
            return $resp['results'][0]['geometry']['location'];
        }else{
            return false;
        }
        
    }



    const PATTERN = '/(?:\-?\d+(?:\.?\d+)?[\+\-\*\/])+\-?\d+(?:\.?\d+)?/';

    const PARENTHESIS_DEPTH = 10;

    public static function calculate($input){
        if(strpos($input, '+') != null || strpos($input, '-') != null || strpos($input, '/') != null || strpos($input, '*') != null){
            //  Remove white spaces and invalid math chars
            $input = str_replace(',', '.', $input);
            $input = preg_replace('[^0-9\.\+\-\*\/\(\)]', '', $input);

            //  Calculate each of the parenthesis from the top
            $i = 0;
            while(strpos($input, '(') || strpos($input, ')')){
                $input = preg_replace_callback('/\(([^\(\)]+)\)/', 'self::callback', $input);

                $i++;
                if($i > self::PARENTHESIS_DEPTH){
                    break;
                }
            }

            //  Calculate the result
            if(preg_match(self::PATTERN, $input, $match)){
                return self::compute($match[0]);
            }

            return 0;
        }

        return $input;
    }

    private static function compute($input){
        $compute = create_function('', 'return '.$input.';');

        return 0 + $compute();
    }

    private static function callback($input){
        if(is_numeric($input[1])){
            return $input[1];
        }
        elseif(preg_match(self::PATTERN, $input[1], $match)){
            return self::compute($match[0]);
        }

        return 0;
    }




}
