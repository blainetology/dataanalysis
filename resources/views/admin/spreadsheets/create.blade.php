@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">

        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><h2 class="panel-title">{{ !empty($input['id']) ? 'Update' : 'Create' }} Spreadsheet</h2></div>

                <div class="panel-body">

                @if(!empty($input['id']))
                {{ Form::open(['route'=>['spreadsheets.update',$input['id']], 'method'=>'PUT']) }}
                @else
                {{ Form::open(['route'=>'spreadsheets.store', 'method'=>'POST']) }}
                @endif
                <div class="row"> 
                    <div class="col-md-5" >
                        <h5>Spreadsheet Name</h5>
                        <div class="form-group"> 
                        {{ Form::text('name',(!empty($input['name']) ? $input['name'] : null),['id'=>'name', 'class'=>'form-control', 'placeholder'=>'Name'])}}
                        </div>
                        <h5>Client</h5>
                        <div class="form-group"> 
                        {{ Form::select('client',$clients,(!empty($input['client_id']) ? $input['client_id'] : null),['name'=>'client_id', 'id'=>'client_id', 'class'=>'form-control'])}}
                        </div>
                        <h5>Status</h5>
                        <div class="form-group"> 
                        {{ Form::select('active',['1'=>'Active','0'=>'Inactive'],(!empty($input['active']) ? 1 : 0),['name'=>'active', 'id'=>'active', 'class'=>'form-control'])}}
                        </div>
                        {{ Form::submit('Save Spreadsheet',['class'=>'btn btn-primary']) }}
                    </div>
                    <div class="col-md-6 col-md-offset-1" >
                        <h5>Column Labels</h5>
                        @for($x=1; $x<=26; $x++)
                            <?php $letter = $letters[$x]; ?>
                            <div class="row">
                                <div class="col-sm-1">{{ $letter }}</div>
                                <div class="col-sm-11">{{ Form::text('label',(!empty($input['column'][$x]) ? $input['column'][$x]['label'] : null),['name'=>"column[$x][label]", 'class'=>'form-control', 'placeholder'=>'Column Label'])}}</div>
                            </div>
                        @endfor
                    </div>
                </div>
                {{ Form::close() }}

                </div>
            </div>
        </div>

    </div>
</div>
@endsection
