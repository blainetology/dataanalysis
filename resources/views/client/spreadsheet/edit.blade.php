@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <p>Client: <strong>{{ $client->business_name }}</strong></p>
            <ul class="nav nav-tabs">
                @foreach($client_spreadsheets as $sheet)
                <li role="presentation" {!! ($spreadsheet->id == $sheet->id) ? 'class="active"' : '' !!}><a href="/admin/spreadsheets/{{ $sheet->id }}">{{$sheet->name}}</a></li>
                @endforeach
            </ul>
            {{ Form::open(['action'=>'AdminSpreadsheetController@store']) }}
            <div class="row">
                <div class="col-lg-12">
                    <div class="bg-success" style="padding:10px;">
                        {{ Form::submit('save changes',['class'=>'btn btn-success btn-sm']) }}
                    </div>
                </div>
            </div>
            <div style="overflow:auto; width:100%;">
                <table id="spreadsheet" class="table table-bordered table-condensed">
                    <thead>
                        <tr>
                            <td></td>
                            @for($x=1; $x<=$max; $x++)
                              <th class="bg-info">
                                <small><em class="text-info" style="font-weight:100;">Column {{$letters[$x]}}</em></small><br/>
                                {{ isset($columns[$x]) ? $columns[$x]['label'] : '' }}
                              </th>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @for($y=1; $y<=20; $y++)
                            <tr>
                                <th class="nostretch no-stretch bg-info">{{$y}}</th>
                                @for($x=1; $x<=$max; $x++)
                                    <td style="padding:0;"><input class="sheet_cell" type="text" id="content_{{$y}}_{{$x}}" name="content[{{$y}}][{{$x}}]"></td>
                                @endfor
                            </tr>
                        @endfor
                   </tbody>
                </table>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</div>
@endsection
@section('styles')
<style>
#spreadsheet{background:#FFF;}
#spreadsheet tbody td{}
</style>
@append
