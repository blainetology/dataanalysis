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
            'clients' => [0=>'--choose client--']+Client::withTrashed()->get()->pluck('business_name','id')->toArray(),
            'letters' => SpreadsheetColumn::$columnLetters,
            'input'   => ['column'=>[]],
            'isAdminView'   => true
        ];
        return view('admin.spreadsheets.create',$data);
    }

    public function store(Request $request)
    {
        $input = \Request::all();
        $spreadsheet = Spreadsheet::create($input);
        foreach($input['column'] as $key => $column){
            $column['spreadsheet_id'] = $spreadsheet->id;
            $column['column'] = $key;
            $validation = [];
            foreach($column['validation'] as $key=>$value){
                if(trim($value) != "")
                    $validation[$key]=trim($value);
            }
            $column['validation'] = json_encode($validation);
            $column['conditional'] = json_encode($column['conditional']);
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
            $columns[$column->column] = $column;
        $data = [
            'client' => Client::find($spreadsheet->client_id),
            'spreadsheet' => $spreadsheet,
            'columns' => $columns,
            'max' => $spreadsheet->columns->max()->column,
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
        foreach($spreadsheet->columns as $column){
            $column->validation = json_decode($column->validation);
            $column->conditional = json_decode($column->conditional);
            $input['column'][$column->column] = $column->toArray();
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
        #print_r($input);
        #exit;
        $spreadsheet = Spreadsheet::find($id);
        $spreadsheet->update($input);
        SpreadsheetColumn::where('spreadsheet_id',$id)->delete();
        $mapping = [];
        foreach($input['column'] as $key => $column){
            $mapping[$key] = $column['col_val'];
            $column['spreadsheet_id'] = $spreadsheet->id;
            $column['column'] = $column['col_val'];
            $validation = [];
            foreach($column['validation'] as $key2=>$value){
                if($key2=='in'){
                    $value = trim($value);
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
                    if(trim($value) != "" || $value=0)
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
            $input['column'][$column->column] = $column->toArray();
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
            $input['column'][$column->column] = $column->toArray();
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
        $datefields = [];
        foreach($spreadsheet->columns as $column){
            if($column->type == 'date')
                $datefields[]=$column->column;
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
            $validations['col'.$column->column] = implode('|',$temp);
        }
        print_r($datefields);
        exit;
        
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
                        if(in_array($col, $datefields))
                            $field = date('Y-m-d',strtotime($field));
                        $content['col'.$col]=$field;
                        $col++;
                    }
                    $validator = \Validator::make($content, $validations);
                    if ($validator->fails())
                        $content['validated']=0;
                    else
                        $content['validated']=1;
                    SpreadsheetContent::create($content);
                }
                $row++;
            }
            fclose($handle);
        }
        Log::logspreadsheet($spreadsheet->id,'imported');
        return redirect('/');
    }

}
