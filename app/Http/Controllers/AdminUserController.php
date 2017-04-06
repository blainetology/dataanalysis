<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewUser;
use App\Client;
use App\User;
use App\Log;   

class AdminUserController extends Controller
{

    public function index()
    {
        if(!\Auth::user()->isEditor())
            abort(401);

        $data = [
            'users' => User::withTrashed()->get(),
            'isAdminView'   => true
        ];
        return view('admin.users.index',$data);
    }

    public function create()
    {
        //
        $data = [
            'input' => \Request::old(),
            'clients' => [0=>'--choose client--'] + Client::all()->pluck('business_name','id')->toArray(),
            'isAdminView'   => true
        ];
        return view('admin.users.create',$data);
    }

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
        Log::loguser($user->id,'created');

        return redirect()->route('adminusers.index');

    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
        $data = [
            'input' => User::find($id)->toArray(),
            'clients' => [0=>'--choose client--']+Client::all()->pluck('business_name','id')->toArray(),
            'isAdminView'   => true
        ];
        return view('admin.users.create',$data);
    }

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
        Log::loguser($user->id,'updated');
        return redirect()->route('adminusers.index');
    }

   public function destroy($id)
    {
        //
        $user = User::find($id);
        if($user)
            $user->delete();
        Log::loguser($user->id,'deleted');
        return redirect()->route('adminusers.index');
    }

    public function restore($id){
        User::withTrashed()->where('id',$id)->restore();
        Log::loguser($user->id,'restored');
        return redirect()->route('adminusers.index');
    }
}
