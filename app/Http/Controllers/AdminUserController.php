<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewUser;
use App\Client;
use App\User;

class AdminUserController extends Controller
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
        //
        $data = [
            'input' => \Request::old(),
            'clients' => [0=>'--choose client--'] + Client::all()->pluck('business_name','id')->toArray()
        ];
        return view('admin.users.create',$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $input = \Request::all();
        $password = substr(md5(time()), rand(1,10),10);
        $input['password'] = \Hash::make($password);
        if($input['type']=='editor')
            $input['client_id'] = 0;
        $input['admin'] = 0;
        $user = User::create($input);
        if($input['type']=='editor')
            $user->editor = 1;
        else
            $user->editor = 0;
        $user->save();

        $data = [];
        $data['password'] = $password;
        $data['email'] = $input['email'];
        $data['name'] = $input['first_name'];
        Mail::to($input['email'])->send(new NewUser($data));

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
        $data = [
            'input' => User::find($id)->toArray(),
            'clients' => [0=>'--choose client--']+Client::all()->pluck('business_name','id')->toArray()
        ];
        return view('admin.users.create',$data);
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
        if($input['type']=='editor')
            $input['client_id'] = 0;
        $user = User::find($id);
        $user->fill($input);
        if($input['type']=='editor')
            $user->editor = 1;
        else
            $user->editor = 0;
        $user->save();
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
    }
}
