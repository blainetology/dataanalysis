@extends('layouts.app')

@section('content')
<br/>
<div class="container">
    <div class="row">
        <div class="col-md-3">
            @if(!empty($spreadsheets))
            @foreach($spreadsheets as $spreadsheet)
                <div id="columnsList{{$spreadsheet->id}}" class="columnsList" style="display: none;">
                <h4><span class="text-danger">{{ $spreadsheet->id }}</span>: {{ $spreadsheet->name }}</h4>
                <small>
                @foreach($spreadsheet->columns as $column)
                 &nbsp; <strong class="text-danger">{{ \App\SpreadsheetColumn::$columnLetters[$column->column] }}</strong>: {{ $column->label }}<br/> 
                @endforeach
                </small> 
                </div>
            @endforeach
            @endif
        </div>
        <div class="col-md-9">
            <div class="panel panel-default">
                <div class="panel-heading"><h2 class="panel-title">{{ !empty($duplicate) ? 'Duplicate' : (!empty($input['id']) ? 'Update' : 'Create') }} Report</h2></div>

                <div class="panel-body">

                    @if(!empty($input['id']))
                        {{ Form::open(['route'=>['reports.update',$input['id']], 'method'=>'PUT']) }}
                    @else
                        {{ Form::open(['route'=>'reports.store', 'method'=>'POST']) }}
                    @endif
                    <div class="row">
                        <div class="col-md-5">
                            <div class="form-group">
                                <label>Report Name</label>
                                {{ Form::text('name',(!empty($input['name']) ? $input['name'] : null),['id'=>'name', 'name'=>'name', 'class'=>'form-control', 'placeholder'=>'Name'])}}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Tab Label</label>
                                {{ Form::text('tab label',(!empty($input['label']) ? $input['label'] : null),['id'=>'label', 'name'=>'label', 'class'=>'form-control', 'placeholder'=>'Tab Label'])}}
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label>Status</label>
                                {{ Form::select('active',['1'=>'Active','0'=>'Inactive'],(!empty($input['active']) ? 1 : 0),['name'=>'active', 'id'=>'active', 'class'=>'form-control'])}}
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Client</label>
                                {{ Form::select('client',$clients,(!empty($input['client_id']) ? $input['client_id'] : null),['id'=>'client_id', 'name'=>'client_id', 'class'=>'form-control'])}}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Template</label>
                                {{ Form::select('template',$templates,(!empty($input['template_id']) ? $input['template_id'] : null),['id'=>'template', 'name'=>'template_id', 'class'=>'form-control'])}}
                            </div>
                        </div>
                    </div>
                    @if(!empty($input['id']))
                    <br/>
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Template Data Fields</h4>
                        </div>
                    </div> 
                    @include('admin.reports.includes.'.$file)
                    @endif
                    <br/>
                    {{ Form::submit('Save Report',['class'=>'btn btn-primary']) }}
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
    });
    $('#spreadsheet').on('change',function(){
        var that = $(this);
        $('.columnsList').hide(0);
        $('#columnsList'+that.val()).show(0);
    });
    $('#columnsList'+$('#spreadsheet').val()).show(0);
});
</script>
@append
