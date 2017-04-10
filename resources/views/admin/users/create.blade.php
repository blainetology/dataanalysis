@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{ !empty($input['id']) ? 'Update' : 'Create' }} User</div>

                <div class="panel-body">

                    @if(!empty($input['id']))
                        {{ Form::open(['route'=>['adminusers.update',$input['id']], 'method'=>'PUT']) }}
                    @else
                        {{ Form::open(['route'=>'adminusers.store', 'method'=>'POST']) }}
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::text('first_name',(!empty($input['first_name']) ? $input['first_name'] : null),['id'=>'first_name', 'class'=>'form-control', 'placeholder'=>'First Name'])}}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::text('last_name',(!empty($input['last_name']) ? $input['last_name'] : null),['id'=>'last_name', 'class'=>'form-control', 'placeholder'=>'Last Name'])}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::text('email',(!empty($input['email']) ? $input['email'] : null),['id'=>'email', 'class'=>'form-control', 'placeholder'=>'Email Address'])}}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                {{ Form::text('title',(!empty($input['title']) ? $input['title'] : null),['id'=>'title', 'class'=>'form-control', 'placeholder'=>'Job Title'])}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        @if(!empty($input['admin']))
                            <div class="col-md-12">
                                <h3 class="text-danger">This is an admin account</h3>
                            </div>
                        @else
                            @if(\Auth::user()->isAdmin())
                            <div class="col-md-5">
                                <div class="form-group">
                                    <h4>Account Type</h4>
                                    {{ Form::select('account_type',['client'=>'Client account','editor'=>'Track That Advisor employee'],(!empty($input['editor']) ? 'editor' : null),['name'=>'type', 'id'=>'type', 'class'=>'form-control'])}}
                                </div>
                            </div>
                            @else
                                <input type="hidden" name="type" value="client">
                            @endif
                            <div class="col-md-7">
                                <div class="form-group" id="client_select">
                                    <h4>Client</h4>
                                    {{ Form::select('client',$clients,(!empty($input['client_id']) ? $input['client_id'] : null),['name'=>'client_id', 'id'=>'client_id', 'class'=>'form-control'])}}
                                </div>
                            </div>
                        @endif
                    </div>
                    {{ Form::submit('Save User',['class'=>'btn btn-primary']) }}
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
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
