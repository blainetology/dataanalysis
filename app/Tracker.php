<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tracker extends Model
{
    //
    protected $fillable = ['name','label','client_id','rules','data','active','list_order','opened_at'];

    public function client(){
        return $this->belongsTo('\App\Client','client_id')->withTrashed();
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

    public function displayname(){
      return trim($this->label);
    }

    public static function lettersToLabel($letters,$row,$indices){
        $search = $indices;
        $columnLetters = \App\SpreadsheetColumn::$columnLetters;
        $letters = strtoupper($letters);
        $label = "";
        for($x=0;$x<strlen($letters);$x++){
            $letter=$letters[$x];
            if($col = array_search($letter, $columnLetters)){
                if(isset($row['col'.$col]))
                    $label.=$row['col'.$col];
            }
            else
                $label.=$letter;
        }
        return trim($label);
    }
    public static function lettersToValues($column,$columns,$indices){
        $search = ['count','start_date','end_date'];
        $columnLetters = \App\SpreadsheetColumn::$columnLetters;
        unset($columnLetters[0]);
        $columnLetters = array_reverse($columnLetters);
        foreach($indices as $letter)
            $search[] = $letter;
        $replace = [$column['count'],\Request::get('start_date',date('Y-01-01')),\Request::get('end_date',date('Y-m-d'))];
        foreach($indices as $index){
            if(isset($columns[$index]))
                $replace[$index]=$columns[$index]['value'];
            else
                $replace[$index]=0;
        }
        $equation = str_replace($search, $replace, $column['rules'][4]);
        return self::calculate($equation);
    }

    public static function compareStrings($var1,$var2,$operator){
        $var1 = trim($var1);
        $var1 = trim($var1,'"\'');
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
