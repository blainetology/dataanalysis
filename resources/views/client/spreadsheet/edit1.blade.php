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
                <div style="overflow:auto; width:100%;">
                    <table id="spreadsheet" class="table table-bordered table-condensed">
                        <thead>
                            <tr>
                                <td></td>
                                @for($x=1; $x<=$max; $x++)
                                <th class="bg-info">{{$letters[$x]}}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th width="20" class="nostretch no-stretch bg-info">1</th>
                                @for($y=1; $y<=$max; $y++)
                                    <th class="bg-warning">{{ isset($columns[$y]) ? $columns[$y]['label'] : '' }}</th>
                                @endfor
                            </tr>
                            @for($x=2; $x<=20; $x++)
                                <tr>
                                    <th class="nostretch no-stretch bg-info">{{$x}}</th>
                                    @for($y=1; $y<=$max; $y++)
                                        <td style="padding:0;"><input class="sheet_cell" type="text" name="content[{{$x}}][{{$y}}]"></td>
                                    @endfor
                                </tr>
                            @endfor
                       </tbody>
                    </table>
                </div>
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
