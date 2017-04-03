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
            'reports' => Report::all(),
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

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = \Request::all();
        $input['rules'] = '[]';
        #print_r($input);
        #exit;
        $report = Report::create($input);
        return redirect()->route('reports.edit',$report->id);
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
        $data = [
            'client' => Client::find($report->client_id),
            'report' => $report,
            $report->template->file => ReportTemplate::getContent($report->template->file,$report->rules),
            'client_reports' => Report::active()->where('client_id',$report->client_id)->get()
        ];
        if(!\Auth::user()->isAdmin() && !\Auth::user()->isEditor()){
            $report->opened_at = \DB::raw('NOW()');
            $report->save();
        }
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
        $report = Report::find($id);
        $input = $report->toArray();
        $input['rules'] = json_decode($report->rules,true);
        $data = [
            'input' => $input,
            'clients' => [0=>'--choose client--']+Client::withTrashed()->get()->pluck('business_name','id')->toArray(),
            'templates' => [0=>'--choose template--']+ReportTemplate::all()->pluck('name','id')->toArray(),
            'file' => $report->template->file,
            'isAdminView'   => true
        ];
        return view('admin.reports.create',$data);
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
        $input = \Request::all();
        $input['rules'] = json_encode($input['rules']);
        #print_r($input);
        #exit;
        Report::find($id)->update($input);
        return redirect()->route('reports.index');
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
        $report = Report::find($id);
        $report->delete();
        return redirect()->route('reports.index');
    }

    /**
     * Show the form for duplicating the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function duplicate($id)
    {
        $report = Report::find($id);
        $input = $report->toArray();
        $input['rules'] = json_decode($report->rules,true);
        $data = [
            'input' => $input,
            'clients' => [0=>'--choose client--']+Client::all()->pluck('business_name','id')->toArray(),
            'templates' => [0=>'--choose template--']+ReportTemplate::all()->pluck('name','id')->toArray(),
            'file' => $report->template->file,
            'isAdminView'   => true,
            'duplicate' => true
        ];
        return view('admin.reports.create',$data);
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
        $client = Client::find($id);
        $data = [
            'client' => $client,
            'reports' => $reports
        ];
        foreach($reports as $report){
            $data[$report->template->file] = ReportTemplate::getContent($report->template->file,$report->rules);
        }
        $pdf = \PDF::loadView('client.reports.generate', $data);
        return $pdf->download('track_that_'.str_slug($client->business_name).'_report.pdf');
        return view('client.reports.generate',$data);
    }

}
