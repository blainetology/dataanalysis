<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;   

class SettingsController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $user = \Auth::user();
        $data = [
            'user' => $user,
            'isAdminView'   => \Auth::user()->isEditor()
        ];
        return view('settings.edit',$data);
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
        #print_r($input);
        #exit;
        $user = \Auth::user();
        $user->fill($request->toArray());
        $user->save();

        if(!empty($request->password) && !empty($request->password2) && $request->password==$request->password2){
            $user->password = \Hash::make($request->password);
            $user->save();
        }

        return redirect('/');
    }
}
