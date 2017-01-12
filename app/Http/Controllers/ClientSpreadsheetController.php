<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\User;   
use App\Spreadsheet;   
use App\SpreadsheetContent;   
use App\SpreadsheetColumn;   

class ClientSpreadsheetController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public $keyOnly = ['required'];

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = \Request::all();
        $spreadsheet = Spreadsheet::create($input);
        SpreadsheetColumn::where('spreadsheet_id',$id)->delete();
        foreach($input['column'] as $key => $column){
            $column['spreadsheet_id'] = $id;
            $column['column'] = $key;
            if(!empty($column['label']))
                SpreadsheetColumn::create($column);
        }
        return redirect('/');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $spreadsheet = Spreadsheet::find($id);
        $columns = [];
        $validations = [];
        $queryvars = explode('&',$_SERVER['QUERY_STRING']);
        $temp = [];
        foreach($queryvars as $queryvar){
            if(!empty($queryvar) && !strstr($queryvar, 'sort_col='))
                $temp[] = $queryvar;
        }
        $queryvars = implode('&',$temp);
        foreach($spreadsheet->columns as $column){
            $column->validation = json_decode($column->validation,true);
/*            $distincts = SpreadsheetContent::select('col'.$column->column.' AS col', \DB::raw('COUNT(id) AS qty'))->where('spreadsheet_id',$spreadsheet->id)->groupBy('col'.$column->column)->get();
            $temp = [];
            foreach($distincts as $distinct)
                $temp[$distinct->col] = $distinct->col." (".$distinct->qty.")";
            $column->distincts = $temp;
*/          
            $temp=[];
            $temp[] = str_replace('currency', 'numeric', $column->type);
            foreach($column->validation as $key=>$value){
                if(in_array($key, $this->keyOnly))
                    $temp[]=$key;
                else
                    $temp[]=$key.":".$value;
            }
            $validations['col'.$column->column] = implode('|',$temp);
            $column->distincts = SpreadsheetContent::distinct('col'.$column->column)->where('spreadsheet_id',$spreadsheet->id)->pluck('col'.$column->column,'col'.$column->column);
            $columns[$column->column] = $column;
        }
        $data = [
            'client' => Client::find($spreadsheet->client_id),
            'spreadsheet' => $spreadsheet,
            'columns' => $columns,
            'validations' => $validations,
            'max' => $spreadsheet->columns->max()->column,
            'letters' => SpreadsheetColumn::$columnLetters,
            'client_spreadsheets' => Spreadsheet::where('client_id',$spreadsheet->client_id)->get(),
            'counts' => [],
            'queryvars' => $queryvars
        ];
        #return $data;
        return view('client.spreadsheet.edit',$data);
     }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $input = \Request::all();
        #print_r($input);
        #exit;
        $spreadsheet = Spreadsheet::find($id)->update($input);
        SpreadsheetContent::where('spreadsheet_id',$id)->delete();
        foreach($input['content'] as $key => $content){
            $content['spreadsheet_id'] = $id;
            $content['added_by'] = \Auth::user()->id;
            $content['revision_id'] = 0;
            if(!empty($content['col1']))
                SpreadsheetContent::create($content);
        }
        return redirect()->route('clientspreadsheets.edit',['id'=>$id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function export($id)
    {
        //
        $spreadsheet = Spreadsheet::find($id);
        $columns = [];
        foreach($spreadsheet->columns as $column){
            $column->validation = json_decode($column->validation,true);
/*            $distincts = SpreadsheetContent::select('col'.$column->column.' AS col', \DB::raw('COUNT(id) AS qty'))->where('spreadsheet_id',$spreadsheet->id)->groupBy('col'.$column->column)->get();
            $temp = [];
            foreach($distincts as $distinct)
                $temp[$distinct->col] = $distinct->col." (".$distinct->qty.")";
            $column->distincts = $temp;
*/            
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
        header('Content-Disposition: attachment; filename=data.csv');

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
        return "";
     }

}
