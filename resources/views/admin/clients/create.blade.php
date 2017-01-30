@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{ !empty($input['id']) ? 'Update' : 'Create' }} Client</div>

                <div class="panel-body">

                @if(!empty($input['id']))
                    {{ Form::open(['route'=>['adminclients.update',$input['id']], 'method'=>'PUT']) }}
                @else
                    {{ Form::open(['route'=>'adminclients.store', 'method'=>'POST']) }}
                @endif
                    <div class="form-group"> 
                    {{ Form::text('business_name',(!empty($input) ? $input['business_name'] : ''),['id'=>'business_name', 'class'=>'form-control', 'placeholder'=>'Business Name'])}}
                    </div>
                    {{ Form::submit('Save Client',['class'=>'btn btn-primary']) }}
                {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
