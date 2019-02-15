<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\User;   
use App\Log;   
use App\Tracker;   
use App\Report;   
use App\Spreadsheet;   
use App\SpreadsheetContent;   
use App\SpreadsheetColumn;   

class ClientSpreadsheetController extends Controller
{

    public $keyOnly = ['required'];

    public function edit($id)
    {
        //
        $spreadsheet = Spreadsheet::find($id);
        if(empty($_SERVER['QUERY_STRING'])){
            $column = SpreadsheetColumn::where('spreadsheet_id',$spreadsheet->id)->where('column_id',$spreadsheet->sorting_col)->first(); 

            if($column){
                $col = $column->column_id;
                $type = $column->type;
            }
            else{
                $col = 1;   
                $type = 'string';
            }
            $max = SpreadsheetContent::where('spreadsheet_id',$spreadsheet->id)->max('col'.$col);
            if($type == 'date')
                $query = 'filter[col'.$col.'][min]='.(date('Y-m-d',strtotime($max)-(60*60*24*39))).'&filter[col'.$col.'][max]='.$max.'&sort_col='.$col;
            else
                $query = 'filter[col'.$col.']='.$max.'&sort_col='.$col;
            return redirect($_SERVER['REQUEST_URI'].'?'.$query);
        }
        $columns = [];
        $validations = [];
        $conditionals = [];
        $queryvars = explode('&',$_SERVER['QUERY_STRING']);
        $temp = [];
        foreach($queryvars as $queryvar){
            if(!empty($queryvar) && !strstr($queryvar, 'sort_col='))
                $temp[] = $queryvar;
        }
        $queryvars = implode('&',$temp);
        foreach($spreadsheet->columns as $column){
            $column->validation = json_decode($column->validation,true);
            $column->conditional = json_decode($column->conditional,true);
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
            if(!empty($column->conditional) && !empty($column->conditional['if']) && !empty($column->conditional['then']) && !empty($column->conditional['else']))
                $conditionals['col'.$column->column_id] = $column->conditional;
            $column->distincts = SpreadsheetContent::distinct('col'.$column->column_id)->where('spreadsheet_id',$spreadsheet->id)->pluck('col'.$column->column_id,'col'.$column->column_id);
            $columns[$column->column_id] = $column;
        }
        $field_ids = [];
        foreach($spreadsheet->content as $content){
            $field_ids[] = $content->id;
        }
        $letters = SpreadsheetColumn::$columnLetters;
        $revletters = SpreadsheetColumn::$columnLetters;
        krsort($revletters);
        $search=[];
        $replace=[];
        foreach($revletters as $key=>$letter){
            $search[]='col'.$key;
            $replace[]=isset($columns[$key]) ? $columns[$key]->label : 'Column '.$letter;
        }
        $data = [
            'client' => Client::find($spreadsheet->client_id),
            'spreadsheet' => $spreadsheet,
            'columns' => $columns,
            'validations' => $validations,
            'conditionals' => $conditionals,
            'max' => $spreadsheet->columns->max()->column_id,
            'letters' => $letters,
            'search' => $search,
            'replace' => $replace,
            'client_reports' => Report::where('client_id',$spreadsheet->client_id)->orderBy('list_order','asc')->get(),
            'client_trackers' => Tracker::where('client_id',$spreadsheet->client_id)->active()->orderBy('list_order','asc')->get(),
            'client_spreadsheets' => Spreadsheet::where('client_id',$spreadsheet->client_id)->active()->orderBy('list_order','asc')->get(),
            'counts' => [],
            'queryvars' => $queryvars,
            'field_ids' => implode(',',$field_ids),
            'sort_col' => \Request::get('sort_col',$spreadsheet->sorting_col)
        ];
        #return $data;
        return view('client.spreadsheet.edit',$data);
     }

    public function update(Request $request, $id)
    {
        //
        $input = \Request::all();

        unset($input['content'][ count($input['content']) ]);

        $field_ids=[];
        foreach(explode(',',$input['field_ids']) as $field_id)
            $field_ids[] = $field_id;
        $spreadsheet = Spreadsheet::find($id);
        $spreadsheet->update($input);
        SpreadsheetContent::where('spreadsheet_id',$id)->whereIn('id',$field_ids)->delete();

        $case = [];
        $defaults = [];
        $validations = [];
        foreach($spreadsheet->columns as $column){
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
        foreach($input['content'] as $key => $content){
            foreach($content as $col_id=>$col_value){
                if(empty($col_value) && $col_value !== 0 && isset($defaults[$col_id]) && $defaults[$col_id] !== "")
                    $col_value=$defaults[$col_id];
                if(isset($case[$col_id]) && $case[$col_id] !== ""){
                    if($case[$col_id] == 'lower')
                        $col_value=strtolower($col_value);
                    elseif($case[$col_id] == 'upper')
                        $col_value=strtoupper($col_value);
                }
                $content[$col_id] = $col_value;
            }
            $content['spreadsheet_id'] = $id;
            $content['added_by'] = \Auth::user()->id;
            $content['revision_id'] = 0;
            $content['errors'] = null;
            $validator = \Validator::make($content, $validations);
            if ($validator->fails()){
                $content['validated']=0;
                $content['errors'] = json_encode($validator->errors()->toArray());
            }
            else
                $content['validated']=1;
            SpreadsheetContent::create($content);
        }
        $spreadsheet->touch();
        Log::logspreadsheet($spreadsheet->id,'edited');
        return redirect()->route('clientspreadsheets.edit',['id'=>$id]);
    }

    public function export($id)
    {
        //
        $spreadsheet = Spreadsheet::find($id);
        $columns = [];
        foreach($spreadsheet->columns as $column){
            $column->validation = json_decode($column->validation,true);
            $column->distincts = SpreadsheetContent::distinct('col'.$column->column_id)->where('spreadsheet_id',$spreadsheet->id)->pluck('col'.$column->column_id,'col'.$column->column_id);
            $columns[$column->column_id] = $column;
        }
        $data = [
            'client' => Client::find($spreadsheet->client_id),
            'spreadsheet' => $spreadsheet,
            'columns' => $columns,
            'max' => $spreadsheet->columns->max()->column_id,
            'letters' => SpreadsheetColumn::$columnLetters,
            'client_spreadsheets' => Spreadsheet::where('client_id',$spreadsheet->client_id)->get(),
            'counts' => []
        ];
        #return $data;

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.str_slug($spreadsheet->name,'_').'.csv');

        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');

        // output the column headings
        fputcsv($output, $spreadsheet->columns->pluck('label')->toArray());
        foreach($spreadsheet->content->toArray() as $content){
            $row = [];
            for($x=1;$x<=$spreadsheet->columns->max()->column_id;$x++){
                $row[] = $content["col$x"];
            }
            fputcsv($output, $row);
        }
        Log::logspreadsheet($spreadsheet->id,'exported');
        return "";
     }

}
