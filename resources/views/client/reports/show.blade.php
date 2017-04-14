@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
        <h3>Reports</h3>
        </div>        
    </div>
    <div class="row">
        <div class="col-md-12">
            <span class="pull-right" id="action_bar">
                <a href="/reports/generate/{{$report->client_id}}" class="btn btn-info btn-sm" id="exportbutton"><i class="fa fa-download" aria-hidden="true"></i> generate pdf</a>
            </span>
            <ul class="nav nav-tabs">
                @foreach($client_reports as $rep)
                <li role="presentation" {!! ($report->id == $rep->id) ? 'class="active"' : '' !!}><a href="/reports/{{ $rep->id }}?{{ $_SERVER['QUERY_STRING'] }}">{{$rep->label}}</a></li>
                @endforeach
            </ul>

        </div>
    </div>
</div>
<br/>
<div class="container">
    <div class="row">
        <form method="GET">
        <div class="col-md-3">
            <label>Start Date:</label><br/>
            {{ Form::text('start_date',\Request::get('start_date',date('Y').'-01-01'),['name'=>'start_date','class'=>'form-control','id'=>'start_date'])}}
        </div>
        <div class="col-md-3">
            <label>End Date:</label><br/>
            {{ Form::text('end_date',\Request::get('end_date',date('Y').'-12-31'),['name'=>'end_date','class'=>'form-control','id'=>'end_date'])}}
        </div>
        <div class="col-md-2">
            <label>&nbsp;</label><br/>
            <input type="submit" value="Filter Dates" class="btn btn-info btn-md">
        </div>
        </form>
    </div>
    <div class="row">
        <div class="col-md-12">
        @include('client.reports.includes.'.$report->template->file)
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="/css/datepicker.css" rel="stylesheet" >
<style>
#spreadsheet{background:transparent;}
#spreadsheet thead th{font-size:12px; line-height: 16px;}
#spreadsheet tbody td{ font-size:10px !important;}
#spreadsheet tbody td .input-group-addon{border:none !important; border-radius:0 !important; color:#777 !important; padding:6px 6px; background: transparent; font-size:12px !important;}
#spreadsheet tbody th{padding:4px !important; font-size:11px;}
#spreadsheet tfoot td{font-family: Arial, sans-serif;}
.sheet_cell{font-family:arial; margin:0; padding:0 3px; width: 100%; min-width: 125px; height: 26px; border:1px solid transparent; outline: none; background:transparent; font-size:12px !important;}
.sheet_cell:focus{border:1px solid #999;}
.filter-input{font-weight: 100; width:48%; min-width:55px; display:inline-block; font-size: 10px; padding:2px 4px; height:24px; margin-top:2px;}
</style>
@append

@section('scripts')
<script src="/js/datepicker.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#start_date').datepicker({format: 'yyyy-mm-dd'});
        $('#end_date').datepicker({format: 'yyyy-mm-dd'});
        $('#spreadsheetContainer').height($(window).height()-180);
        $(window).on('resize',function(){
            $('#spreadsheetContainer').height($(window).height()-180);
        });
    });
</script>
@append