@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 col-lg-1 bg-info" style="height:100%; position: fixed; left:0; top:50px;">
            <h4>Spreadsheets</h4>
            @foreach($client_spreadsheets as $rep)
            <a href="/client/spreadsheets/{{ $rep->id }}/edit">{{$rep->name}}</a><br/>
            @endforeach
            <br/>
            <h4>Reports</h4>
            @foreach($client_reports as $rep)
            <a href="/reports/{{ $rep->id }}?{{ $_SERVER['QUERY_STRING'] }}">{{$rep->label}}</a><br/>
            @endforeach
            <br/>
            <a href="/reports/generate/{{$report->client_id}}?{{ $_SERVER['QUERY_STRING'] }}" class="btn btn-info btn-sm"><i class="fa fa-download" aria-hidden="true"></i> generate pdf</a>
            <br/><br/>
        </div>     
    </div>   
    <div class="row">
        <div class="col-md-10 col-offset-md-2 col-lg-11 col-offset-lg-1" style="height:52px; position: fixed; right:0; top:50px; padding-top:10px; border-bottom:1px solid #EEE; background: #F9F9F9; z-index: 100;">
            <div class="row">
                <div class="col-md-8">
                    <h3 class="text-info" style="margin-bottom:0; padding-bottom:0; margin-top:2px;">{{ $report->name }} Report</h3>
                </div>
                <div class="col-md-4">
                    <form method="GET" style="margin:0; padding:0;">
                            <div class="col-md-4" style="padding:2px;">
                                {{ Form::text('start_date',\Request::get('start_date',date('Y').'-01-01'),['name'=>'start_date','class'=>'form-control input-sm','id'=>'start_date'])}}
                            </div>
                            <div class="col-md-2 text-center" style="padding:2px; padding-top:6px;">
                            <strong>through</strong>
                            </div>
                            <div class="col-md-4" style="padding:2px;">
                                {{ Form::text('end_date',\Request::get('end_date',date('Y-m-d')),['name'=>'end_date','class'=>'form-control input-sm','id'=>'end_date'])}}
                            </div>
                            <div class="col-md-2" style="padding:2px;">
                                <input type="submit" value="Filter" class="btn btn-info btn-sm" style="width:100%;">
                            </div>
                    </form>
                </div>
            </div>
        </div>     
    </div>   
    <div class="row">
        <div class="col-md-2 col-lg-1">
        </div>
        <div class="col-md-10 col-lg-11">
            <div class="row">
                <div class="col-md-12" style="height:51px;">
                </div>
                <div class="col-md-12">
                    @include('client.reports.includes.'.$report->template->file)
                </div>
            </div>
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