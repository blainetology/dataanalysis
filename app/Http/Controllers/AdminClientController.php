<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Client;

class AdminClientController extends Controller{

    public function index(){
        if(!\Auth::user()->isEditor())
            abort(401);

        $data = [
            'clients' => Client::withTrashed()->get(),
            'isAdminView'   => true
        ];
        return view('admin.clients.index',$data);
    }

    public function create(){
        $data = [
            'input' => \Request::old(),
            'isAdminView'   => true
        ];
        return view('admin.clients.create',$data);
    }

    public function store(Request $request){
        $input = \Request::all();
        Client::create($input);
        return redirect()->route('adminclients.index');

    }

    public function edit($id){
        //
        $data = [
            'input' => Client::find($id)->toArray(),
            'isAdminView'   => true
        ];
        return view('admin.clients.create',$data);
    }

    public function update(Request $request, $id){
        //
        $input = \Request::all();
        $client = Client::find($id);
        $client->fill($input);
        $client->save();
        return redirect()->route('adminclients.index');
    }

    public function destroy($id){
        //
        Client::find($id)->delete();
        return redirect()->route('adminclients.index');
    }

    public function restore($id){
        Client::withTrashed()->where('id',$id)->restore();
        return redirect()->route('adminclients.index');
    }
}
