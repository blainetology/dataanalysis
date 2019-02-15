<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\User;   
use App\Log;   
use App\Spreadsheet;   
use App\SpreadsheetColumn;   
use App\SpreadsheetContent;   

class AdminSpreadsheetController extends Controller
{

    public $keyOnly = ['required'];

    public function index()
    {
        if(!\Auth::user()->isEditor())
            abort(401);

        $data = [
            'spreadsheets' => Spreadsheet::all(),
            'isAdminView'   => true
        ];
        return view('admin.spreadsheets.index',$data);
    }

    public function create()
    {
        //
        $data = [
            'clients' => [0=>'--choose client--']+Client::withTrashed()->orderBy('business_name')->get()->pluck('business_name','id')->toArray(),
            'letters' => SpreadsheetColumn::$columnLetters,
            'input'   => ['column'=>[]],
            'isAdminView'   => true
        ];
        return view('admin.spreadsheets.create',$data);
    }

    public function store(Request $request)
    {
        $input = \Request::all();
        if(empty($input['sorting_col']))
            $input['sorting_col']=1;
        $spreadsheet = Spreadsheet::create($input);

        if($file = $request->file('csv')){
            if($request->get('replace') == 1)
                SpreadsheetContent::where('spreadsheet_id',$spreadsheet->id)->where('revision_id',0)->delete();

            $columns = [];
            if (($handle = fopen($file->path(), "r")) !== FALSE) {
                $data = fgetcsv($handle, 1000, ",");
                foreach($data as $index=>$col_name){
                    $columns[$index+1]=['name'=>$col_name,'values'=>[]];
                }
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    foreach($data as $index=>$col_val){
                        $col_val = trim($col_val);
                        if(!empty($col_val))
                            $columns[$index+1]['values'][$col_val] = $col_val;
                    }
                }
                foreach($columns as $column_id=>$column){
                    if(!empty($column['name'])){
                        $validation = ['required'=>1];
                        if(count($column['values']) < 50)
                            $validation['in'] = implode(",", $column['values']);
                        $data = [
                            'spreadsheet_id'    => $spreadsheet->id,
                            'column_id'         => $column_id,
                            'label'             => $column['name'],
                            'type'              => 'string',
                            'validation'        => json_encode($validation),
                            'conditional'       => '[]',
                            'normalize'         => '{"case":"as-is"}'
                        ];
                        SpreadsheetColumn::create($data);
                    }
                }
                fclose($handle);
            }
            Log::logspreadsheet($spreadsheet->id,'created');
            return redirect()->route('adminspreadsheets.edit',$spreadsheet->id);
        }



        foreach($input['column'] as $key => $column){
            $column['spreadsheet_id'] = $spreadsheet->id;
            $column['column_id'] = $key;
            $validation = [];
            foreach($column['validation'] as $key2=>$value){
                $value = trim($value);
                if($key2=='in'){
                    if($value != ""){
                        $values = explode(',',$value);
                        $temp = [];
                        foreach($values as $val){
                            $temp[] = trim($val);
                        }
                        $validation[$key2]=implode(',',$temp);
                    }
                }
                else{
                    if($value != "" || $value===0)
                        $validation[$key2]=trim($value);
                }
            }
            $column['validation'] = json_encode($validation);
            $conditional = [];
            foreach($column['conditional'] as $key=>$value){
                if(trim($value) != "")
                    $conditional[$key]=trim($value);
            }
            $column['conditional'] = json_encode($conditional);
            $normalize = [];
            foreach($column['normalize'] as $key=>$value){
                if(trim($value) != "")
                    $normalize[$key]=trim($value);
            }
            $column['normalize'] = json_encode($normalize);
            if(!empty($column['label']))
                SpreadsheetColumn::create($column);
        }
        Log::logspreadsheet($spreadsheet->id,'created');
        return redirect()->route('adminspreadsheets.index');
    }

    public function show($id)
    {
        //
        $spreadsheet = Spreadsheet::find($id);
        $columns = [];
        foreach($spreadsheet->columns as $column)
            $columns[$column->column_id] = $column;
        $data = [
            'client' => Client::find($spreadsheet->client_id),
            'spreadsheet' => $spreadsheet,
            'columns' => $columns,
            'max' => $spreadsheet->columns->max()->column_id,
            'letters' => SpreadsheetColumn::$columnLetters,
            'client_spreadsheets' => Spreadsheet::where('client_id',$spreadsheet->client_id)->get()
        ];
        Log::logspreadsheet($spreadsheet->id,'viewed');
        return view('client.spreadsheet.edit',$data);
    }

    public function edit($id)
    {
        //
        $spreadsheet = Spreadsheet::find($id);
        $input = $spreadsheet->toArray();
        $input['column']=[];
        foreach($spreadsheet->columns as $column){
            $column->validation = json_decode($column->validation);
            $column->conditional = json_decode($column->conditional);
            $column->normalize = json_decode($column->normalize);
            $input['column'][$column->column_id] = $column->toArray();
        }
        $data = [
            'input' => $input,
            'clients' => [0=>'--choose client--']+Client::withTrashed()->get()->pluck('business_name','id')->toArray(),
            'letters' => SpreadsheetColumn::$columnLetters,
            'isAdminView'   => true
        ];
        return view('admin.spreadsheets.create',$data);
    }

    public function update(Request $request, $id)
    {
        //
        $input = \Request::all();
        if(empty($input['sorting_col']))
            $input['sorting_col']=1;
        #print_r($input);
        #exit;
        $spreadsheet = Spreadsheet::find($id);
        $spreadsheet->update($input);
        SpreadsheetColumn::where('spreadsheet_id',$id)->delete();
        $mapping = [];
        foreach($input['column'] as $key => $column){
            $mapping[$key] = $column['col_val'];
            $column['spreadsheet_id'] = $spreadsheet->id;
            $column['column_id'] = $column['col_val'];
            $validation = [];
            foreach($column['validation'] as $key2=>$value){
                $value = trim($value);
                if($key2=='in'){
                    if($value != ""){
                        $values = explode(',',$value);
                        $temp = [];
                        foreach($values as $val){
                            $temp[] = trim($val);
                        }
                        $validation[$key2]=implode(',',$temp);
                    }
                }
                else{
                    if($value != "" || $value===0)
                        $validation[$key2]=trim($value);
                }
            }
            $column['validation'] = json_encode($validation);
            $conditional = [];
            foreach($column['conditional'] as $key=>$value){
                if(trim($value) != "")
                    $conditional[$key]=trim($value);
            }
            $column['conditional'] = json_encode($conditional);
            $normalize = [];
            foreach($column['normalize'] as $key=>$value){
                if(trim($value) != "")
                    $normalize[$key]=trim($value);
            }
            $column['normalize'] = json_encode($normalize);
            if(!empty($column['label']))
                SpreadsheetColumn::create($column);
        }
        $old = [];
        $new = [];
        foreach(SpreadsheetContent::where('spreadsheet_id',$spreadsheet->id)->get() as $content){
            $old = $content->toArray();
            $new = $old;
            foreach($mapping as $oldID=>$newID)
                $new['col'.$newID] = $old['col'.$oldID];
            $content->timestamps = false;
            $content->fill($new);
            $content->save();
        }
        $spreadsheet->touch();
        Log::logspreadsheet($spreadsheet->id,'updated');
        return redirect()->route('adminspreadsheets.index');
    }

    public function destroy($id)
    {
        //
        $spreadsheet = Spreadsheet::find($id);
        SpreadsheetColumn::where('spreadsheet_id',$spreadsheet->id)->delete();        
        SpreadsheetContent::where('spreadsheet_id',$spreadsheet->id)->delete();
        Log::logspreadsheet($spreadsheet->id,'deleted');
        $spreadsheet->delete();
        return redirect()->route('adminspreadsheets.index');
    }

    public function duplicate($id)
    {
        $spreadsheet = Spreadsheet::find($id);
        $input = $spreadsheet->toArray();
        foreach($spreadsheet->columns as $column){
            $column->validation = json_decode($column->validation);
            $input['column'][$column->column_id] = $column->toArray();
        }
        $data = [
            'input' => $input,
            'clients' => [0=>'--choose client--']+Client::all()->pluck('business_name','id')->toArray(),
            'letters' => SpreadsheetColumn::$columnLetters,
            'duplicate' => true
        ];
        return view('admin.spreadsheets.create',$data);
    }

    public function import($id)
    {
        $spreadsheet = Spreadsheet::find($id);
        $input = $spreadsheet->toArray();
        foreach($spreadsheet->columns as $column){
            $column->validation = json_decode($column->validation);
            $input['column'][$column->column_id] = $column->toArray();
        }
        $data = [
            'spreadsheet' => $spreadsheet,
            'letters' => SpreadsheetColumn::$columnLetters,
        ];
        return view('admin.spreadsheets.import',$data);
    }

    public function importupload(Request $request, $id)
    {
        $spreadsheet = Spreadsheet::find($id);

        $validations = [];
        $case = [];
        $defaults = [];
        $datefields = [];
        $numericfields = [];
        foreach($spreadsheet->columns as $column){
            if($column->type == 'date')
                $datefields[]=$column->column_id;
            if($column->type == 'currency' || $column->type == 'numeric')
                $numericfields[]=$column->column_id;
            $column->validation = json_decode($column->validation,true);
            $temp=[];
            $temp[] = str_replace(['currency','notes'], ['numeric','string'], $column->type);
            foreach($column->validation as $key=>$value){
                if(in_array($key, $this->keyOnly)){
                    if($value != 0)
                        $temp[]=$key;
                }
                else
                    $temp[]=$key.":".$value;
            }
            $validations['col'.$column->column_id] = implode('|',$temp);

            $column->normalize = json_decode($column->normalize,true);
            if(isset($column->normalize['case']))
                $case['col'.$column->column_id] = trim($column->normalize['case']);
            if(isset($column->normalize['default']))
                $defaults['col'.$column->column_id] = trim($column->normalize['default']);
        }
        $file = $request->file('csv');
        if($request->get('replace') == 1)
            SpreadsheetContent::where('spreadsheet_id',$spreadsheet->id)->where('revision_id',0)->delete();
        $row = 1;
        if (($handle = fopen($file->path(), "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if($row==1 && $request->get('skipfirst') == 1){
                }
                else{
                    $col=1;
                    $content=['spreadsheet_id'=>$spreadsheet->id,'added_by'=>\Auth::user()->id,'revision_id'=>0,'validated'=>1];
                    foreach($data as $field){
                        if($field !== ""){
                            if(in_array($col, $datefields))
                                $field = !empty($field) ? date('Y-m-d',strtotime($field)) : null;
                            if(in_array($col, $numericfields))
                                $field = preg_replace("/[^0-9.]/", "", $field );
                        }
                        $content['col'.$col]=$field;
                        $col++;
                    }
                    foreach($content as $col_id=>$col_value){
                        if(empty($col_value) && $col_value !== 0 && isset($defaults[$col_id]) && $defaults[$col_id] !== "")
                            $col_value=$defaults[$col_id];
                        if(isset($case[$col_id]) && $case[$col_id] !== ""){
                            if($case[$col_id] == 'lower')
                                $col_value=strtolower($col_value);
                            elseif($case[$col_id] == 'upper')
                                $col_value=strtoupper($col_value);
                        }
                        $content[$col_id] = trim($col_value);
                    }
                    $content['errors'] = null;
                    $validator = \Validator::make($content, $validations);
                    if ($validator->fails()){
                        $content['validated']=0;
                        $content['errors'] = json_encode($validator->errors()->toArray());
                    }
                    else
                        $content['validated']=1;
                     if(!empty($content['col1']) || !empty($content['col2']) || !empty($content['col3']) || !empty($content['col4']))
                        SpreadsheetContent::create($content);
                }
                $row++;
            }
            fclose($handle);
        }
        Log::logspreadsheet($spreadsheet->id,'imported');
        return redirect()->route('adminspreadsheets.index');
    }

}
