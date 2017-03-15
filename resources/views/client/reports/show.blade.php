@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <span class="pull-right" id="action_bar">
                <a href="/reports/{{$report->id}}/export?{{$_SERVER['QUERY_STRING']}}" class="btn btn-info btn-sm" id="exportbutton"><i class="fa fa-download" aria-hidden="true"></i> export to csv</a>
            </span>
            <ul class="nav nav-tabs">
                @foreach($client_reports as $rep)
                <li role="presentation" {!! ($report->id == $rep->id) ? 'class="active"' : '' !!}><a href="/client/reports/{{ $rep->id }}/edit">{{$rep->name}}</a></li>
                @endforeach
            </ul>

        </div>
        <div class="col-md-12" style="padding:3px;">
            {{ print_r($report->rules,true) }}
            {{ print_r($content,true) }}
            <div id="spreadsheetContainer" style="overflow:auto; width:100%; height:84px;">
                <table id="spreadsheet" class="table table-bordered table-striped table-condensed" style="margin-bottom:5px;">
                    <thead>
                        <tr>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr>
                        </tr>
                    </tfoot>
                </table>
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
        $('#spreadsheetContainer').height($(window).height()-150);
        $(window).on('resize',function(){
            $('#spreadsheetContainer').height($(window).height()-150);
        });
    });
</script>
@append