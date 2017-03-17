@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Account Settings</h2>
        </div>
    </div>

    {{ Form::open(['route'=>['settings.update',$user->id], 'method'=>'PUT']) }}
    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-default">
                <div class="panel-heading">User Profile</div>

                <div class="panel-body">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::text('first_name',$user->first_name,['id'=>'first_name', 'class'=>'form-control', 'placeholder'=>'First Name'])}}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::text('last_name',$user->last_name,['id'=>'last_name', 'class'=>'form-control', 'placeholder'=>'Last Name'])}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::text('email',$user->email,['id'=>'email', 'class'=>'form-control', 'placeholder'=>'Email Address'])}}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::text('title',$user->title,['id'=>'title', 'class'=>'form-control', 'placeholder'=>'Job Title'])}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <br/>

            <div class="panel panel-default">
                <div class="panel-heading">Update Password</div>

                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::password('password',['id'=>'password', 'class'=>'form-control', 'placeholder'=>'new password'])}}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::password('password2',['id'=>'password2', 'class'=>'form-control', 'placeholder'=>'confirm password'])}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{ Form::submit('Update User',['class'=>'btn btn-primary']) }}
    {{ Form::close() }}
</div>
@endsection
@section('scripts')
<script>
$(document).ready(function(){
    if($('#type').val() == 'editor')
      $('#client_select').addClass('hidden');
    else
      $('#client_select').removeClass('hidden');
   $('#type').on('change',function(){
     if($('#type').val() == 'editor')
       $('#client_select').addClass('hidden');
     else
       $('#client_select').removeClass('hidden');
   })
});
</script>
@append
