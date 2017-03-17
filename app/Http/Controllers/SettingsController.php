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
        $input = \Request::all();
        #print_r($input);
        #exit;
        $user = \Auth::user();
        $user->fill($input);
        $user->save();

        return redirect('/');
    }
}
