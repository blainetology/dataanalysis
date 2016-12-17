@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{ !empty($input['id']) ? 'Update' : 'Create' }} User</div>

                <div class="panel-body">

                @if(!empty($input['id']))
                {{ Form::open(['route'=>['users.update',$input['id']], 'method'=>'PUT']) }}
                @else
                {{ Form::open(['route'=>'users.store', 'method'=>'POST']) }}
                @endif
                    <div class="form-group"> 
                    {{ Form::text('name',(!empty($input['name']) ? $input['name'] : null),['id'=>'name', 'class'=>'form-control', 'placeholder'=>'Name'])}}
                    </div>
                    <div class="form-group"> 
                    {{ Form::text('email',(!empty($input['email']) ? $input['email'] : null),['id'=>'email', 'class'=>'form-control', 'placeholder'=>'Email Address'])}}
                    </div>
                    <div class="form-group"> 
                    {{ Form::select('client',$clients,(!empty($input['client_id']) ? $input['client_id'] : null),['name'=>'client_id', 'id'=>'client_id', 'class'=>'form-control'])}}
                    </div>
                    {{ Form::submit('Save User',['class'=>'btn btn-primary']) }}
                {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
