<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\User;   
use App\Log;   
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
            $validations['col'.$column->column] = implode('|',$temp);
            if(!empty($column->conditional))
                $conditionals['col'.$column->column] = $column->conditional;
            $column->distincts = SpreadsheetContent::distinct('col'.$column->column)->where('spreadsheet_id',$spreadsheet->id)->pluck('col'.$column->column,'col'.$column->column);
            $columns[$column->column] = $column;
        }
        $field_ids = [];
        foreach($spreadsheet->content as $content){
            $field_ids[] = $content->id;
        }
        $data = [
            'client' => Client::find($spreadsheet->client_id),
            'spreadsheet' => $spreadsheet,
            'columns' => $columns,
            'validations' => $validations,
            'conditionals' => $conditionals,
            'max' => $spreadsheet->columns->max()->column,
            'letters' => SpreadsheetColumn::$columnLetters,
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
        #print_r($input);
        #exit;
        $field_ids=[];
        foreach(explode(',',$input['field_ids']) as $field_id)
            $field_ids[] = $field_id;
        $spreadsheet = Spreadsheet::find($id);
        $spreadsheet->update($input);
        SpreadsheetContent::where('spreadsheet_id',$id)->whereIn('id',$field_ids)->delete();

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
            $validations['col'.$column->column] = implode('|',$temp);
        }
        foreach($input['content'] as $key => $content){
            $content['spreadsheet_id'] = $id;
            $content['added_by'] = \Auth::user()->id;
            $content['revision_id'] = 0;
            $validator = \Validator::make($content, $validations);
            if ($validator->fails()){
                $content['validated']=0;
            }
            else
                $content['validated']=1;

            if(!empty($content['col1']))
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
            $column->distincts = SpreadsheetContent::distinct('col'.$column->column)->where('spreadsheet_id',$spreadsheet->id)->pluck('col'.$column->column,'col'.$column->column);
            $columns[$column->column] = $column;
        }
        $data = [
            'client' => Client::find($spreadsheet->client_id),
            'spreadsheet' => $spreadsheet,
            'columns' => $columns,
            'max' => $spreadsheet->columns->max()->column,
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
            for($x=1;$x<=$spreadsheet->columns->max()->column;$x++){
                $row[] = $content["col$x"];
            }
            fputcsv($output, $row);
        }
        Log::logspreadsheet($spreadsheet->id,'exported');
        return "";
     }

}
