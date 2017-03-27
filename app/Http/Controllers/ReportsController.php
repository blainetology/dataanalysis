<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\User;   
use App\Report;   
use App\ReportTemplate;
use App\Spreadsheet;   
use App\SpreadsheetColumn;   
use App\SpreadsheetContent;   

class ReportsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(!\Auth::user()->isEditor())
            abort(401);

        $data = [
            'reports' => Report::withTrashed()->get(),
            'spreadsheets' => Spreadsheet::all(),
            'isAdminView'   => true
        ];
        return view('admin.reports.index',$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $data = [
            'clients' => [0=>'--choose client--']+Client::all()->pluck('business_name','id')->toArray(),
            'letters' => SpreadsheetColumn::$columnLetters,
            'input'   => ['column'=>[]],
            'isAdminView'   => true
        ];
        return view('admin.spreadsheets.create',$data);
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
        foreach($input['column'] as $key => $column){
            $column['spreadsheet_id'] = $spreadsheet->id;
            $column['column'] = $key;
            $validation = [];
            foreach($column['validation'] as $key=>$value){
                if(trim($value) != "")
                    $validation[$key]=trim($value);
            }
            $column['validation'] = json_encode($validation);
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
        $report = Report::find($id);
/*        $report->rules = json_decode($report->rules);
        $content = SpreadsheetContent::where('spreadsheet_id',$report->rules->spreadsheet);
        $content->addSelect($distinct);
        foreach($report->rules->columns as $col) {
            $content->addSelect('col'.array_search(strtoupper($col),SpreadsheetColumn::$columnLetters));
        }
        $contents = $content->get();
        $temp = [];
        foreach($contents as $row){
            print_r($row->toArray());
            foreach($row->toArray() as $key=>$value){
                if(empty($temp[$row->$distinct][$key]))
                    $temp[$row->$distinct][$key] = 0;
                else
                    $temp[$row->$distinct][$key] += $value;
            }
        }
*/        $data = [
            'client' => Client::find($report->client_id),
            'report' => $report,
            $report->template->file => ReportTemplate::getContent($report->template->file,$report->rules),
            'client_reports' => Report::where('client_id',$report->client_id)->get()
        ];
        return view('client.reports.show',$data);
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
        $input = $spreadsheet->toArray();
        foreach($spreadsheet->columns as $column){
            $column->validation = json_decode($column->validation);
            $column->conditional = json_decode($column->conditional);
            $input['column'][$column->column] = $column->toArray();
        }
        $data = [
            'input' => $input,
            'clients' => [0=>'--choose client--']+Client::all()->pluck('business_name','id')->toArray(),
            'letters' => SpreadsheetColumn::$columnLetters,
            'isAdminView'   => true
        ];
        return view('admin.spreadsheets.create',$data);
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
        $spreadsheet = Spreadsheet::find($id);
        $spreadsheet->update($input);
        SpreadsheetColumn::where('spreadsheet_id',$id)->delete();
        $mapping = [];
        foreach($input['column'] as $key => $column){
            $mapping[$key] = $column['col_val'];
            $column['spreadsheet_id'] = $spreadsheet->id;
            $column['column'] = $column['col_val'];
            $validation = [];
            foreach($column['validation'] as $key=>$value){
                if($key=='in'){
                    $value = trim($value);
                    if($value != ""){
                        $values = explode(',',$value);
                        $temp = [];
                        foreach($values as $val){
                            $temp[] = trim($val);
                        }
                        $validation[$key]=implode(',',$temp);
                    }
                }
                else{
                    if(trim($value) != "")
                        $validation[$key]=trim($value);
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
        return redirect('/');
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
        $spreadsheet = Spreadsheet::find($id);
        SpreadsheetColumn::where('spreadsheet_id',$spreadsheet->id);        
        SpreadsheetContent::where('spreadsheet_id',$spreadsheet->id);
        $spreadsheet->delete();
        return redirect('/');
    }

    /**
     * Show the form for duplicating the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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

    /**
     * Show the form for importing csv.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generate($id)
    {
        $reports = Report::where('client_id',$id)->get();
        $data = [
            'client' => Client::find($id),
            'reports' => $reports
        ];
        foreach($reports as $report){
            $data[$report->template->file] = ReportTemplate::getContent($report->template->file,$report->rules);
        }
        $pdf = \PDF::loadView('client.reports.generate', $data);
        return $pdf->download('trackthatreport.pdf');
        return view('client.reports.generate',$data);
    }

}
