<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\User;   
use App\Log;   
use App\Report;   
use App\ReportTemplate;
use App\Spreadsheet;   
use App\SpreadsheetColumn;   
use App\SpreadsheetContent;   

class ReportsController extends Controller
{

    public function index()
    {
        if(!\Auth::user()->isEditor())
            abort(401);

        $data = [
            'reports' => Report::all(),
            'spreadsheets' => Spreadsheet::all(),
            'isAdminView'   => true
        ];
        return view('admin.reports.index',$data);
    }

    public function create()
    {
        $input = \Request::old();
        $input['rules'] = [];
        $data = [
            'input' => $input,
            'clients' => [0=>'--choose client--']+Client::withTrashed()->get()->pluck('business_name','id')->toArray(),
            'templates' => [0=>'--choose template--']+ReportTemplate::all()->pluck('name','id')->toArray(),
            'isAdminView'   => true
        ];
        return view('admin.reports.create',$data);
    }

    public function store(Request $request)
    {
        $input = \Request::all();
        $input['rules'] = '[]';
        $input['opened_at'] = \DB::raw('NOW()');
        #print_r($input);
        #exit;
        $report = Report::create($input);
        Log::logreport($report->id,'created');
        return redirect()->route('reports.edit',$report->id);
    }

    public function show($id)
    {
        //
        $report = Report::find($id);
        $data = [
            'client' => Client::find($report->client_id),
            'report' => $report,
            $report->template->file => ReportTemplate::getContent($report->template->file,$report->rules),
            'client_reports' => Report::where('client_id',$report->client_id)->active()->orderBy('list_order','asc')->get(),
            'client_spreadsheets' => Spreadsheet::where('client_id',$report->client_id)->active()->orderBy('list_order','asc')->get()
        ];
        if(!\Auth::user()->isAdmin() && !\Auth::user()->isEditor()){
            $report->opened_at = \DB::raw('NOW()');
            $report->save();
        }
        Log::logreport($report->id,'viewed');
        return view('client.reports.show',$data);
    }

    public function edit($id)
    {
        //
        $report = Report::find($id);
        $input = $report->toArray();
        $input['rules'] = json_decode($report->rules,true);
        $data = [
            'input' => $input,
            'clients' => [0=>'--choose client--']+Client::withTrashed()->get()->pluck('business_name','id')->toArray(),
            'templates' => [0=>'--choose template--']+ReportTemplate::all()->pluck('name','id')->toArray(),
            'spreadsheets' => Spreadsheet::with('columns')->where('client_id',$report->client_id)->get(),
            'file' => $report->template->file,
            'isAdminView'   => true
        ];
        return view('admin.reports.create',$data);
    }

    public function update(Request $request, $id)
    {
        $input = \Request::all();

        if(!empty($input['rules'])){
            $availableColumns = \App\SpreadsheetColumn::where('spreadsheet_id',$input['rules']['spreadsheet'])->pluck('label','column');
            // clean up the columns input
            $temp = [];
            foreach(explode("\n",trim($input['rules']['columns'])) as $column){
                $row = explode('||',$column);

                if(in_array(strtoupper(trim($row[0])), \App\SpreadsheetColumn::$columnLetters))
                    $index = array_search(strtoupper(trim($row[0])),\App\SpreadsheetColumn::$columnLetters);
                else
                    $index = trim($row[0]);

                if(!empty($row[1]))
                    $type = trim($row[1]);
                else
                    $type = 'numeric';

                if(isset($row[2]) && !empty(trim($row[2])))
                    $label = trim($row[2]);
                elseif(isset($availableColumns[$index]))
                    $label = $availableColumns[$index];
                else
                    $label = $index;

                $total = (!isset($row[3]) || (isset($row[3]) && strtoupper(trim($row[3])) != 'NONE' && strtoupper(trim($row[3])) != 'COUNT')) ? 'total' : strtolower($row[3]);

                $if = '';
                if(!empty($row[4])){
                    $ifs = explode(' ',trim($row[4]));
                    if(isset($ifs[0]) && isset($ifs[1])){
                        $ifs[0] = trim($ifs[0]);
                        $ifs[1] = trim($ifs[1]);
                        $ifs[1] = trim($ifs[1],'"');
                        echo $ifs[1]." ".(float)$ifs[1]."<br/>";
                        if($ifs[1] === (string)(float)$ifs[1]){
                            echo 'matched'."<br/>";
                            $ifs[1] = (float)$ifs[1];
                        }
                        else{
                            echo 'no match'."<br/>";
                            $ifs[1] = '"'.$ifs[1].'"';
                        }
                        $if = $ifs[0]." ".$ifs[1];
                        echo $if."<br/>";                   
                    }
                }

                $temp[] = trim(implode(' || ',[trim($row[0]),trim($type),trim($label),trim($total),$if]));
            }
            $input['rules']['columns'] = implode("\n",$temp);
            $input['rules'] = json_encode($input['rules']);
        }

        $report = Report::find($id);
        $report->update($input);
        Log::logreport($report->id,'updated');
        return redirect()->route('reports.index');
    }

    public function destroy($id)
    {
        //
        $report = Report::find($id);
        $report->delete();
        Log::logreport($report->id,'deleted');
        return redirect()->route('reports.index');
    }

    public function duplicate($id)
    {
        $report = Report::find($id);
        $input = $report->toArray();
        $input['rules'] = json_decode($report->rules,true);
        $data = [
            'input' => $input,
            'clients' => [0=>'--choose client--']+Client::all()->pluck('business_name','id')->toArray(),
            'templates' => [0=>'--choose template--']+ReportTemplate::all()->pluck('name','id')->toArray(),
            'spreadsheets' => Spreadsheet::with('columns')->where('client_id',$report->client_id)->get(),
            'file' => $report->template->file,
            'isAdminView'   => true,
            'duplicate' => true
        ];
        return view('admin.reports.create',$data);
    }

    public function generate($id)
    {
        $reports = Report::where('client_id',$id)->where('active',1)->orderBy('list_order','asc')->take(2)->get();
        $client = Client::find($id);
        $data = [
            'client' => $client,
            'reports' => $reports,
            'start' => \Request::get('start_date',date('Y').'-01-01'),
            'end' => \Request::get('end_date',date('Y-m-d'))
        ];
        foreach($reports as $report){
            if($report->template->pdf == 1){
                if(!isset($data[$report->template->file]))
                    $data[$report->template->file] = ReportTemplate::getContent($report->template->file,$report->rules);
            }
        }
        #return view('client.reports.generate',$data);
        $pdf = \PDF::loadView('client.reports.generate', $data);
        #$pdf = \PDF::loadFile('http://data.app/reports/generatepreview/'.$id.'/?'.$_SERVER['QUERY_STRING'], $data);
        return $pdf->download('track_that_'.str_slug($client->business_name).'_report.pdf');
        return view('client.reports.generate',$data);
    }

    public function generatepreview($id)
    {
        $reports = Report::where('client_id',$id)->where('active',1)->orderBy('list_order','asc')->take(2)->get();
        $client = Client::find($id);
        $data = [
            'client' => $client,
            'reports' => $reports,
            'start' => \Request::get('start_date',date('Y').'-01-01'),
            'end' => \Request::get('end_date',date('Y-m-d'))
        ];
        foreach($reports as $report){
            if($report->template->pdf == 1){
                if(!isset($data[$report->template->file]))
                    $data[$report->template->file] = ReportTemplate::getContent($report->template->file,$report->rules);
            }
        }
        return view('client.reports.generate',$data);
    }

}
