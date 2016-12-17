<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;
use App\User;
use App\Spreadsheet;

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
                'clients'   => Client::all(),
                'users'     => User::all(),
                'spreadsheets'  => Spreadsheet::all()
            ];
            return view('admin.home',$data);
        }
        else{
            $data = [
                'spreadsheets'  => Spreadsheet::where('client_id',\Auth::user()->client_id)->get(),
                'users'         => User::where('client_id',\Auth::user()->client_id)->get()
            ];
            return view('client.home',$data);
        }
    }
}
