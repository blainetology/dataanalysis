@extends('layouts.app')

@section('content')
@include('client.sidebar')
<div style="margin-left:200px; position: relative;">
    <div id="page-header" style="height:52px; position: fixed; left:200px; top:50px; padding-top:10px; border-bottom:1px solid #EEE; background: #F9F9F9; z-index: 100; width: 100%; box-sizing: border-box;">
        <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <h3 class="text-info" style="margin-bottom:0; padding-bottom:0; margin-top:4px; font-size: 1.5em;">{{ $report->name }} Report</h3>
            </div>
            <div class="col-md-4">
                <form method="GET" style="margin:0; padding:0;">
                        <div class="col-md-3" style="padding:2px;">
                            {{ Form::text('start_date',\Request::get('start_date',date('Y').'-01-01'),['name'=>'start_date','class'=>'form-control input-sm','id'=>'start_date'])}}
                        </div>
                        <div class="col-md-1 text-center" style="padding:2px; padding-top:6px;">
                        <strong>thru</strong>
                        </div>
                        <div class="col-md-3" style="padding:2px;">
                            {{ Form::text('end_date',\Request::get('end_date',date('Y-m-d')),['name'=>'end_date','class'=>'form-control input-sm','id'=>'end_date'])}}
                        </div>
                        <div class="col-md-2" style="padding:2px;">
                            <input type="submit" value="Filter" class="btn btn-info btn-sm">
                        </div>
                        <div class="col-md-2" style="padding:2px;">
                            <a href="/reports/generate/{{$report->client_id}}?{{ $_SERVER['QUERY_STRING'] }}" class="btn btn-success btn-sm"><i class="fa fa-download" aria-hidden="true"></i>  generate pdf</a>
                        </div>
                </form>
            </div>
        </div>
        </div>
    </div>     
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12" style="height:51px;"></div>
            <div class="col-md-12">
                @include('client.reports.includes.'.$report->template->file)
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="/css/datepicker.css" rel="stylesheet" >
<style>
</style>
@append

@section('scripts')
<script src="/js/datepicker.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#page-header').width($(window).width()-200);
        $(window).on('resize',function(){
            $('#page-header').width($(window).width()-200);
        });
        $('#start_date').datepicker({format: 'yyyy-mm-dd'});
        $('#end_date').datepicker({format: 'yyyy-mm-dd'});
        $('#spreadsheetContainer').height($(window).height()-180);
        $(window).on('resize',function(){
            $('#spreadsheetContainer').height($(window).height()-180);
        });
    });
</script>
@append