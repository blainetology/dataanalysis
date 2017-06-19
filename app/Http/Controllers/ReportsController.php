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
            'report_'.$report->id => ReportTemplate::getContent($report->template->file,$report->rules),
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

    public function excel($id)
    {
        $report = Report::where('active',1)->where('id',$id)->orderBy('list_order','asc')->first();
        $client = Client::find($report->client_id);
        $data = [
            'client' => $client,
            'report' => $report,
            'start' => \Request::get('start_date',date('Y').'-01-01'),
            'end' => \Request::get('end_date',date('Y-m-d'))
        ];
        $data['report_'.$report->id] = ReportTemplate::getContent($report->template->file,$report->rules);

        \Excel::create(str_slug($report->name.'  '.$data['start'].' thru '.$data['end'],'_'), function($excel) use ($data) {
            $content = $data['report_'.$data['report']->id];
            $content['template'] = $data['report']->template->file;
            $content['report_name'] = $data['report']->name;
            $content['report_dates'] = $data['start'].' thru '.$data['end'];
            $content['col_format'] = $this->setColumnFormats($content['columns']);

            $excel->sheet('All Together', function($sheet) use ($content) {
                $sheet->setColumnFormat($content['col_format']);
                $sheet->setStyle(['font' => ['name' => 'Arial', 'size' => 10, 'bold' => false]]);
                $sheet->loadView('client.reports.includes.'.$content['template'].'_excel_partial',['report_name'=>$content['report_name'],'report_dates'=>$content['report_dates'],'data'=>$content['all'],'columns'=>$content['columns'],'header'=>'All Together','subheader'=>'']);
                for($x=1; $x<=count( $content['columns'] )+(!empty($content['all']['months']) || !empty($content['all']['weeks']) ? 1 : 0); $x++){
                    for($y=3; $y<=( (!empty($content['all']['months']) ? count($content['all']['months'])+1 : 0) + (!empty($content['all']['weeks']) ? count($content['all']['weeks']) + 1 : 0) + 5 ); $y++){
                        $sheet->cells(\App\SpreadsheetColumn::$columnLetters[$x].$y, function($cells) {
                            $cells->setBorder('thin', 'thin', 'thin', 'thin');
                        });
                    }
                }
                #print_r($content['col_format']);
                #exit;
            });

            foreach($content['sections'] as $sections){
                foreach($sections['data'] as $name=>$section){
                    $excel->sheet(substr(substr($sections['label'],0,14).'... '.ucwords($name),0,31), function($sheet) use ($content,$section,$name,$sections) {
                        $sheet->setColumnFormat($content['col_format']);
                        $sheet->setStyle(['font' => ['name' => 'Arial', 'size' => 10, 'bold' => false]]);
                        $sheet->loadView('client.reports.includes.'.$content['template'].'_excel_partial',['report_name'=>$content['report_name'],'report_dates'=>$content['report_dates'],'data'=>$section,'columns'=>$content['columns'],'header'=>$sections['label'],'subheader'=>$name]);
                        for($x=1; $x<=count( $content['columns'] )+(!empty($section['months']) || !empty($section['weeks']) ? 1 : 0); $x++){
                            for($y=3; $y<=( (!empty($section['months']) ? count($section['months'])+1 : 0) + (!empty($section['weeks']) ? count($section['weeks']) + 1 : 0) + 5 ); $y++){
                                $sheet->cells(\App\SpreadsheetColumn::$columnLetters[$x].$y, function($cells) {
                                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                                });
                            }
                        }
                    });
                }
            }
            $excel->setActiveSheetIndex(0);
        })->export('xls');
    }

    public function pdf($id)
    {
        $reports = Report::where('client_id',$id)->where('active',1)->orderBy('list_order','asc')->take(2)->get();
        $report = Report::where('active',1)->where('id',$id)->first();
        $client = Client::find($report->client_id);
        $data = [
            'client' => $client,
            'reports' => $reports,
            'report' => $report,
            'start' => \Request::get('start_date',date('Y').'-01-01'),
            'end' => \Request::get('end_date',date('Y-m-d'))
        ];
        $data['report_'.$report->id] = ReportTemplate::getContent($report->template->file,$report->rules);

        $pdf = \PDF::loadView('client.reports.generate', $data);
        $pdf->setPaper('letter', 'landscape');
        return $pdf->download(str_slug($report->name.'  '.$data['start'].' thru '.$data['end'],'_').'.pdf');
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
                    $data['report_'.$report->id] = ReportTemplate::getContent($report->template->file,$report->rules);
            }
        }
        return view('client.reports.generate',$data);
    }

    private function setColumnFormats($columns){
        $x=2;
        $cols = [];
        foreach($columns as $index=>$column){
            $letter = \App\SpreadsheetColumn::$columnLetters[$x];
            if($column['type']=='integer')
                $cols[$letter] = '0';
            else if($column['type']=='dollar')
                $cols[$letter] = '$#,##0.00';
            else if($column['type']=='numeric')
                $cols[$letter] = '#,##0.00';
            else if($column['type']=='percent')
                $cols[$letter] = '0.0%';
            else
                $cols[$letter] = 'General';

            $x++;
        }
        return $cols;

    }

}
