<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\User;
use App\Spreadsheet;
use App\Report;

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
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(\Auth::user()->isEditor()){
            $data = [
                'clients'   => Client::withTrashed()->get(),
                'users'     => User::withTrashed()->get(),
                'reports'     => Report::get(),
                'spreadsheets'  => Spreadsheet::all(),
                'isAdminView'   => true
            ];
            return view('admin.home',$data);
        }
        else{
            $data = [
                'spreadsheets'  => Spreadsheet::where('client_id',(\Auth::user()->client ? \Auth::user()->client->id : '-1'))->get(),
                'users'         => User::where('client_id',\Auth::user()->client_id)->where('client_id','!=',0)->where('admin',0)->where('editor',0)->get()
            ];
            return view('client.home',$data);
        }
    }
}
