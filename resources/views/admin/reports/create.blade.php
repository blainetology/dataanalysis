@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{ !empty($input['id']) ? 'Update' : 'Create' }} Report</div>

                <div class="panel-body">

                    @if(!empty($input['id']))
                        {{ Form::open(['route'=>['reports.update',$input['id']], 'method'=>'PUT']) }}
                    @else
                        {{ Form::open(['route'=>'reports.store', 'method'=>'POST']) }}
                    @endif
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Report Name</label>
                                {{ Form::text('name',(!empty($input['name']) ? $input['name'] : null),['id'=>'name', 'name'=>'name', 'class'=>'form-control', 'placeholder'=>'Name'])}}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Tab Label</label>
                                {{ Form::text('tab label',(!empty($input['label']) ? $input['label'] : null),['id'=>'label', 'name'=>'label', 'class'=>'form-control', 'placeholder'=>'Tab Label'])}}
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
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Rules</h4>
                        </div>
                    </div> 
                    @include('admin.reports.includes.'.$file)
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
   })
});
</script>
@append
