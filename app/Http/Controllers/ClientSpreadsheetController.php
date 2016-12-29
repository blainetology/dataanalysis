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
            $content['year'] = date('Y');
            $content['month'] = date('n');
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
}
