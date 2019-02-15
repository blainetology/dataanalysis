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

        // setup which columns and sections to display in the reports table
        $availableColumns = \App\SpreadsheetColumn::where('spreadsheet_id',$spreadsheet_id)->pluck('label','column_id');
        $columns = self::setColumns($rules);
        $sections = self::setSections($rules,$spreadsheet_id);

        // now set the search params for later for columns with custom equations
        $search = ['count'];
        for($x=count(\App\SpreadsheetColumn::$columnLetters)-1;$x>0;$x--){
            $search[] = \App\SpreadsheetColumn::$columnLetters[$x];
        }

        // let's get the content from the database
        $results = \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[self::$start,self::$end])->orderBy($date,'desc')->get();
        // purge records if conditional check is setup
        if($conditional && $operator && $value){
            foreach($results as $index=>$row){
                if(!self::compareStrings($row->$conditional,$value,$operator)){
                    $results->forget($index);
                }                
            }
        }

        // let's total it all together first
        $all = ['all'=>['count'=>0,'cols'=>[]]];
        foreach($columns as $key=>$label)
            $all['all']['cols'][$key] = 0;

        // total all the rows together
        foreach($results as $row){
            $all['all'] = self::totalColumns($all['all'],$row,$columns);
        }

        // set the values for columns with custom equations
        $all['all'] = self::totalEquations($all['all'],$columns,$search,$availableColumns);

        // and break it down by months
        $months = null;
        if(!empty($rules['month'])){
            $months = [];

            // setup all the month rows with values of 0 to start
            $starttimestamp = strtotime(self::$start);
            $endtimestamp = strtotime(self::$end);
            for($x=$endtimestamp;$x>=$starttimestamp;$x-=(60*60*24*28)){
                $slug = date("F Y",$x);
                if(!isset($months[$slug])){
                    $months[$slug] = ['count'=>0,'cols'=>[]];
                    foreach($columns as $key=>$label)
                        $months[$slug]['cols'][$key] = 0;
                }
            }

            // total each month row
            foreach($results as $row){
                $row->month = date('F Y',strtotime($row->$date));
                if(!isset($months[$row->month])){
                    $months[$row->month] = ['count'=>0,'cols'=>[]];
                    foreach($columns as $key=>$label)
                        $months[$row->month]['cols'][$key] = 0;
                }
                $months[$row->month] = self::totalColumns($months[$row->month],$row,$columns);
            }

            // set the values for columns with custom equations
            foreach($months as $month=>$data){
                $months[$month] = self::totalEquations($months[$month],$columns,$search,$availableColumns);
            }
            $all['months'] = $months;
        }

        // and break it down by weeks
        $weeks = null;
        if(!empty($rules['week'])){
            $weeks = [];

            // setup all the week rows with values of 0 to start
            $timestamp = strtotime(self::$start);
            $endtimestamp = strtotime(self::$end);
            for($x=$endtimestamp;$x>=$timestamp;$x-=(60*60*24*7)){
                if($rules['week']=='SUN')
                    $starttimestamp = $x-(date('w',$x)*60*60*24);
                elseif($rules['week']=='MON')
                    $starttimestamp = $x-( (date('N',$x)-1)*60*60*24);
                $slug = date("Y-m-d",$starttimestamp);
                $weeks[$slug] = ['start'=>date('m/d/Y',$starttimestamp),'end'=>date('m/d/Y',$starttimestamp+(60*60*24*6)),'count'=>0,'cols'=>[]];
                foreach($columns as $key=>$label)
                    $weeks[$slug]['cols'][$key]=0;
            }
            // total each week row
            foreach($results as $row){
                $timestamp = strtotime($row->$date);
                if($rules['week']=='SUN')
                    $timestamp = $timestamp-(date('w',$timestamp)*60*60*24);
                elseif($rules['week']=='MON')
                    $timestamp = $timestamp-( (date('N',$timestamp)-1)*60*60*24);
                $slug = date("Y-m-d",$timestamp);
                #$slug = date("Y",$timestamp).'-'.date('W',$timestamp);
                if(isset($weeks[$slug]))
                    $weeks[$slug] = self::totalColumns($weeks[$slug],$row,$columns);
            }

            // set the values for columns with custom equations
            foreach($weeks as $week=>$data){
                $weeks[$week] = self::totalEquations($weeks[$week],$columns,$search,$availableColumns);
            }
            $all['weeks'] = $weeks;
        }

        // now by sections
        foreach($sections as $id=>$section){
            $col = 'col'.$id;
            foreach($results as $row){
                // all
                if(!isset($sections[$id]['data'][$row->$col]['all']['count']))
                    $sections[$id]['data'][$row->$col]['all']['count']=0;
                foreach($columns as $key=>$label){
                    if(!isset($sections[$id]['data'][$row->$col]['all']['cols'][$key]))
                        $sections[$id]['data'][$row->$col]['all']['cols'][$key]=0;
                }
                $sections[$id]['data'][$row->$col]['all'] = self::totalColumns($sections[$id]['data'][$row->$col]['all'],$row,$columns);

                //month
                if(!empty($rules['month']) && !empty($rules['monthsections'])){
                    // setup all the month rows with values of 0 to start
                    $starttimestamp = strtotime(self::$start);
                    $endtimestamp = strtotime(self::$end);
                    for($x=$endtimestamp;$x>=$starttimestamp;$x-=(60*60*24*28)){
                        $slug = date("F Y",$x);
                        if(!isset($sections[$id]['data'][$row->$col]['months'][$slug])){
                            $sections[$id]['data'][$row->$col]['months'][$slug] = ['count'=>0,'cols'=>[]];
                            foreach($columns as $key=>$label)
                                $sections[$id]['data'][$row->$col]['months'][$slug]['cols'][$key] = 0;
                        }
                    }
                    $slug = date('F Y',strtotime($row->$date));
                    $sections[$id]['data'][$row->$col]['months'][$slug] = self::totalColumns($sections[$id]['data'][$row->$col]['months'][$row->month],$row,$columns);
                }

                // weeks
                if(!empty($rules['week']) && !empty($rules['weeksections'])){
                    // setup all the week rows with values of 0 to start
                    $timestamp = strtotime(self::$start);
                    $endtimestamp = strtotime(self::$end);
                    for($x=$endtimestamp;$x>=$timestamp;$x-=(60*60*24*7)){
                        if($rules['week']=='SUN')
                            $starttimestamp = $x-(date('w',$x)*60*60*24);
                        elseif($rules['week']=='MON')
                            $starttimestamp = $x-( (date('N',$x)-1)*60*60*24);
                        $slug = date("Y-m-d",$starttimestamp);
                        if(!isset($sections[$id]['data'][$row->$col]['weeks'][$slug])){
                            $sections[$id]['data'][$row->$col]['weeks'][$slug] = ['start'=>date('m/d/Y',$starttimestamp),'end'=>date('m/d/Y',$starttimestamp+(60*60*24*6)),'count'=>0,'cols'=>[]];
                            foreach($columns as $key=>$label)
                                $sections[$id]['data'][$row->$col]['weeks'][$slug]['cols'][$key]=0;
                        }
                    }
                    $timestamp = strtotime($row->$date);
                    if($rules['week']=='SUN')
                        $timestamp = $timestamp-(date('w',$timestamp)*60*60*24);
                    elseif($rules['week']=='MON')
                        $timestamp = $timestamp-( (date('N',$timestamp)-1)*60*60*24);
                    $slug = date("Y-m-d",$timestamp);
                    if(isset($sections[$id]['data'][$row->$col]['weeks'][$slug]))
                        $sections[$id]['data'][$row->$col]['weeks'][$slug] = self::totalColumns($sections[$id]['data'][$row->$col]['weeks'][$slug],$row,$columns);
                }
            }
        }
        // now set the columns with custom equations
        foreach($sections as $id=>$section){
            foreach($section['data'] as $id2=>$section2){
                if(isset($section2['all'])){
                    $sections[$id]['data'][$id2]['all'] = self::totalEquations($sections[$id]['data'][$id2]['all'],$columns,$search,$availableColumns);
                }
                if(isset($section2['months'])){
                    foreach($section2['months'] as $month=>$data)
                        $sections[$id]['data'][$id2]['months'][$month] = self::totalEquations($sections[$id]['data'][$id2]['months'][$month],$columns,$search,$availableColumns);
                }
                if(isset($section2['weeks'])){
                    foreach($section2['weeks'] as $week=>$data)
                        $sections[$id]['data'][$id2]['weeks'][$week] = self::totalEquations($sections[$id]['data'][$id2]['weeks'][$week],$columns,$search,$availableColumns);
                }
            }

        }
        return ['columns'=>$columns,'all'=>$all,'sections'=>$sections,'rules'=>$rules];
    }

    // MAPPED AMOUNTS
    public static function mapped_totals($rules){

        $rules = json_decode($rules,true);
        $spreadsheet_id = $rules['spreadsheet'];
        $date = 'col'.array_search(strtoupper($rules['date']),\App\SpreadsheetColumn::$columnLetters);
        $min=[];
        $max=[];

        $conditional = $operator = $value = null;
        if(!empty($rules['conditional']) && !empty($rules['operator']) && $rules['value'] != ""){
            $conditional = 'col'.array_search(strtoupper($rules['conditional']),\App\SpreadsheetColumn::$columnLetters);
            $operator = $rules['operator'];
            $value = $rules['value'];
        }

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

        // setup which columns and spreadsheets to display in the reports table
        $availableColumns = \App\SpreadsheetColumn::where('spreadsheet_id',$spreadsheet_id)->pluck('label','column_id');
        $columns = self::setColumns($rules);
        $sections = self::setSections($rules,$spreadsheet_id);

        // now set the search params for later for columns with custom equations
        $search = ['count'];
        for($x=count(\App\SpreadsheetColumn::$columnLetters)-1;$x>0;$x--){
            $search[] = \App\SpreadsheetColumn::$columnLetters[$x];
        }

        // let's get the content from the database
        $results = \App\SpreadsheetContent::where('spreadsheet_id',$spreadsheet_id)->whereBetween($date,[self::$start,self::$end])->orderBy($date,'desc')->get();
        // purge records if conditional check is setup
        if($conditional && $operator && $value){
            foreach($results as $index=>$row){
                if(!self::compareStrings($row->$conditional,$value,$operator)){
                    $results->forget($index);
                }                
            }
        }

        $all = [];
        foreach($results as $row){
            $geo_replace = [];
            foreach($geo_cols as $letter=>$l_col)
                $geo_replace[]=$row->{$l_col};
            $location = str_replace($geo_search, $geo_replace, $rules['location']);
            $all['data']['color']=0;
            if(!isset($all['data'][$location]['count']))
                $all['data'][$location]['count']=0;
            if(!isset($all['all']['count']))
                $all['all']['count']=0;
            foreach($columns as $key=>$label){
                if(!isset($all['data'][$location]['geocode']))
                    $all['data'][$location]['geocode'] = $geo_codes[$location] = self::getGeocodeValue($location,$geo_codes);
                if(!isset($all['all']['cols'][$key]))
                    $all['all']['cols'][$key]=0;
                if(!isset($all['data'][$location]['cols'][$key]))
                    $all['data'][$location]['cols'][$key]=0;

                $all['all']['cols'][$key] += $row->{$key};
                $all['data'][$location]['cols'][$key] += $row->{$key};
            }
            $all['data'][$location]['count']++;
            $all['all']['count']++;
        }
        // now set the columns with custom equations
        foreach($all['all'] as $chunk)
            $all['all'] = self::totalEquations($all['all'],$columns,$search,$availableColumns);
        foreach($all['data'] as $location=>$chunk){
            if($location!='color'){
                $all['data'][$location] = self::totalEquations($all['data'][$location],$columns,$search,$availableColumns);
                // now set the max/min for the all total values
                foreach($all['data'][$location]['cols'] as $key=>$col){
                    if(!isset($min[$key]))
                        $min[$key]=$col;
                    if(!isset($max[$key]))
                        $max[$key]=$col;
                    if($col < $min[$key])
                        $min[$key] = $col;
                    if($col > $max[$key])
                        $max[$key] = $col;
                }
            }
        }

        // now by sections
        foreach($sections as $id=>$section){
            $color=1;
            $col = 'col'.$id;
            foreach($results as $row){
                $geo_replace = [];
                foreach($geo_cols as $letter=>$l_col)
                    $geo_replace[]=$row->{$l_col};
                $location = str_replace($geo_search, $geo_replace, $rules['location']);
                if(!isset($sections[$id]['data'][$row->$col]['color'])){
                    $sections[$id]['data'][$row->$col]['color']=$color;
                    $color++;
                }
                if(!isset($sections[$id]['all'][$row->$col]['count']))
                    $sections[$id]['all'][$row->$col]['count']=0;
                if(!isset($sections[$id]['data'][$row->$col][$location]['count']))
                    $sections[$id]['data'][$row->$col][$location]['count']=0;
                foreach($columns as $key=>$label){
                    if(!isset($sections[$id]['data'][$row->$col][$location]['geocode']))
                        $sections[$id]['data'][$row->$col][$location]['geocode'] = $geo_codes[$location] = self::getGeocodeValue($location,$geo_codes);
                    if(!isset($sections[$id]['all'][$row->$col]['cols'][$key]))
                        $sections[$id]['all'][$row->$col]['cols'][$key]=0;
                    if(!isset($sections[$id]['data'][$row->$col][$location]['cols'][$key]))
                        $sections[$id]['data'][$row->$col][$location]['cols'][$key]=0;

                    $sections[$id]['all'][$row->$col]['cols'][$key] += $row->{$key};
                    $sections[$id]['data'][$row->$col][$location]['cols'][$key] += $row->{$key};
                }
                $sections[$id]['all'][$row->$col]['count']++;
                $sections[$id]['data'][$row->$col][$location]['count']++;
            }
        }

        // now set the columns with custom equations
        foreach($sections as $id=>$section){
            foreach($section['data'] as $id2=>$section2){
                foreach($section2 as $location=>$chunk){
                    if($location!='color'){
                        $sections[$id]['data'][$id2][$location] = self::totalEquations($sections[$id]['data'][$id2][$location],$columns,$search,$availableColumns);
                        // now set the max/min for the section values
                        foreach($sections[$id]['data'][$id2][$location]['cols'] as $key=>$col){
                            if(!isset($min[$key]))
                                $min[$key]=$col;
                            if(!isset($max[$key]))
                                $max[$key]=$col;
                            if($col < $min[$key])
                                $min[$key] = $col;
                            if($col > $max[$key])
                                $max[$key] = $col;
                        }
                    }
                    $sections[$id]['all'][$id2] = self::totalEquations($sections[$id]['all'][$id2],$columns,$search,$availableColumns);
                }
            }
        }
        return ['columns'=>$columns,'all'=>$all,'sections'=>$sections,'min'=>$min,'max'=>$max,'rules'=>$rules];
    }

    private static function curl($url,$post=NULL){
        $c = curl_init();
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_URL, $url);
        $contents = curl_exec($c);
        curl_close($c);

        if($contents) 
            return $contents;
        return false;
    }
    private static function geocode($address){
        $url = "https://maps.google.com/maps/api/geocode/json?sensor=false&key=AIzaSyADHSrojKFkUvVCmQrh1yfkPNhC25xLIzE&address=".urlencode($address);
        $resp_json = self::curl($url);
        $resp = json_decode($resp_json, true);
        if($resp['status']='OK' && !empty($resp['results'][0])){
            $resp['results'][0]['geometry']['location']['longitude'] = $resp['results'][0]['geometry']['location']['lng'];
            $resp['results'][0]['geometry']['location']['latitude'] = $resp['results'][0]['geometry']['location']['lat'];
            unset($resp['results'][0]['geometry']['location']['lat']);
            unset($resp['results'][0]['geometry']['location']['lng']);
            return $resp['results'][0]['geometry']['location'];
        }
        return false;
    }
    private static function getGeocodeValue($location,$geo_codes){
        if(isset($geo_codes[$location]))
            return $geo_codes[$location];
        else{
             $geocode = Geocode::where('address',$location)->whereNotNull('latitude')->whereNotNull('longitude')->first();
             if($geocode){
                $geo_codes[$location] = ['latitude'=>$geocode->latitude,'longitude'=>$geocode->longitude];
             }
             else{
                $geocode = self::geocode($location);
                 if($geocode){
                    $geo_codes[$location] = $geocode;
                    Geocode::create(['address'=>$location,'latitude'=>$geocode['latitude'],'longitude'=>$geocode['longitude']]);
                 }
             }
            return $geo_codes[$location];
        }
        return false;
    }

    private static function setColumns($rules){
        $temp=[];
        $columns = explode("\n",$rules['columns']);
        foreach($columns as $column){
            $row = explode('||',$column);

             if(in_array(strtoupper(trim($row[0])), \App\SpreadsheetColumn::$columnLetters))
                $index = 'col'.array_search(strtoupper(trim($row[0])),\App\SpreadsheetColumn::$columnLetters);
            else
                $index = trim($row[0]);

           $temp[(string)$index] = ['equation'=>trim($row[0]),'type'=>trim($row[1]),'label'=>trim($row[2]), 'total'=>trim($row[3]), 'check'=>(!empty($row[4]) ? trim($row[4]) : '')];
        }
        return $temp;
    }
    private static function setSections($rules,$spreadsheet_id){
        $sections=[];
        $letters = explode(',',$rules['sections']);
        foreach($letters as $letter){
            $index = array_search(strtoupper(trim($letter)),\App\SpreadsheetColumn::$columnLetters);
            $sections[$index] = $index;
        }
        $results = \App\SpreadsheetColumn::where('spreadsheet_id',$spreadsheet_id)->whereIn('column_id',$sections)->get();
        foreach($results as $row)
            $sections[$row->column_id] = ['label'=>$row->label,'data'=>[]];
        return $sections;        
    }
    private static function totalColumns($array,$row,$columns){
        foreach($columns as $key=>$column){
            if(!empty($column['check'])){
                $check = explode(' ',trim($column['check']),2);
                if(!isset($check[1]))
                    $check[1]="";
                if(self::compareStrings($row->{$key},$check[1],$check[0])){                    
                    if($column['total'] == 'count')
                        $array['cols'][$key]++;
                    elseif($column['total'] == 'total')
                        $array['cols'][$key] += $row->{$key};
                }
            }
            else{
                if($column['total'] == 'count')
                    $array['cols'][$key]++;
                elseif($column['total'] == 'total')
                    $array['cols'][$key] += $row->{$key};
            }
        }
        $array['count']++;
        return $array;
    }
    private static function totalEquations($array,$columns,$search,$availableColumns){
        $replace = [(int)$array['count']];
        for($x=count(\App\SpreadsheetColumn::$columnLetters)-1;$x>0;$x--){
            if(isset($array['cols']['col'.$x]))
                $replace[] = $array['cols']['col'.$x];
            else
                $replace[] = 0;
        }
        foreach($columns as $key=>$column){
            if(!isset($availableColumns[str_replace('col','',$key)])){
                $value = str_replace($search,$replace,$key);
                $array['cols'][$key] = @self::calculate($value);
            }
        }
        return $array;
    }

    private static function compareStrings($var1,$var2,$operator){
        $var2 = trim($var2);
        $var2 = trim($var2,'"\'');
        if(is_numeric($var2)){
            $var1 = (float)$var1;
            $var2 = (float)$var2;
        }
        else{
            $var1 = strtoupper($var1);
            $var2 = strtoupper($var2);
        }
        $operator = trim($operator);
        if($operator == '==' || $operator == '=')
            return ($var1 == $var2);
        if($operator == '>')
            return ($var1 > $var2);
        if($operator == '>=')
            return ($var1 >= $var2);
        if($operator == '<')
            return ($var1 < $var2);
        if($operator == '<=')
            return ($var1 <= $var2);
        if($operator == '!=')
            return ($var1 != $var2);
        return false;
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
                if($i > self::PARENTHESIS_DEPTH)
                    break;
            }
            //  Calculate the result
            if(preg_match(self::PATTERN, $input, $match)){
                $return = @self::compute($match[0]);
                if(strtoupper($return) == 'NAN')
                    return null;
                return $return;    
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
        if(is_numeric($input[1]))
            return $input[1];
        elseif(preg_match(self::PATTERN, $input[1], $match))
            return self::compute($match[0]);
        return 0;
    }
}