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
            $data = [
                'spreadsheets'  => Spreadsheet::where('client_id',(\Auth::user()->client ? \Auth::user()->client->id : '-1'))->active()->get(),
                'reports'       => Report::where('client_id',(\Auth::user()->client ? \Auth::user()->client->id : '-1'))->active()->get(),
                'users'         => User::where('client_id',\Auth::user()->client_id)->where('client_id','!=',0)->where('admin',0)->where('editor',0)->get(),
                'client'        => \Auth::user()->client
            ];
            return view('client.home',$data);
        }
    }
}
