@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Create Client</div>

                <div class="panel-body">

                {{ Form::open(['route'=>'clients.store', 'method'=>'POST']) }}
                    <div class="form-group"> 
                    {{ Form::text('business_name',null,['id'=>'business_name', 'class'=>'form-control', 'placeholder'=>'Business Name'])}}
                    </div>
                    {{ Form::submit('Add Client',['class'=>'btn btn-primary']) }}
                {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
