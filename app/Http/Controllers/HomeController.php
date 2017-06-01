<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\User;
use App\Spreadsheet;
use App\Report;
use App\Log;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        \DB::statement('SET time_zone  = "America/Phoenix"');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->isEditor() || \Auth::user()->isAdmin()){
            $data = [
                'clients'   => Client::withTrashed()->get(),
                'users'     => User::withTrashed()->orderBy('last_login','desc')->take(10)->get(),
                'reports'     => Report::orderBy('opened_at','desc')->take(10)->get(),
                'spreadsheets'  => Spreadsheet::orderBy('updated_at','desc')->take(10)->get(),
                'logs'      => Log::orderBy('created_at','desc')->take(25)->get(),
                'isAdminView'   => true
            ];
            return view('admin.home',$data);
        }
        else{
            $client_id = (\Auth::user()->client ? \Auth::user()->client->id : '-1');
            $client_spreadsheets = Spreadsheet::where('client_id',$client_id)->active()->get();
            $client_reports = Report::where('client_id',$client_id)->active()->get();
            $users = User::where('client_id',$client_id)->where('client_id','!=',0)->where('admin',0)->where('editor',0)->get();
            $logs = Log::where(function($query) use ($client_spreadsheets){
                        $query->where('model', 'spreadsheet')->whereIn('model_id',$client_spreadsheets->pluck('id'));
                    })->orWhere(function($query) use ($client_reports){
                         $query->where('model', 'report')->whereIn('model_id',$client_reports->pluck('id'));
                    })->orderBy('created_at','desc')->take(25)->get();
            $data = [
                'users'         => $users,
                'client'        => \Auth::user()->client,
                'reports' => $client_reports,
                'spreadsheets' => $client_spreadsheets,
                'client_reports' => $client_reports,
                'client_spreadsheets' => $client_spreadsheets,
                'logs'      => $logs
            ];
            return view('client.home',$data);
        }
    }
}
