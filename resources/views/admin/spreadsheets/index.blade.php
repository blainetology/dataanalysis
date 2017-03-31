@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Spreadsheet Admin</h2>
        </div>
    </div>
    <div class="row">
        {{-- left column --}}
        <div class="col-md-12">

                    {{-- spreadsheets --}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="{{ route('adminspreadsheets.create') }}" class="btn btn-sm btn-primary pull-right">Create Spreadsheet</a>
                            <strong style="font-size:1.3em;" class="text-info">Spreadsheets <span class="label label-success">{{ $spreadsheets->count() }}</span></strong>
                        </div>

                        @if($spreadsheets->count()==0)
                            <div class="panel-body">
                                <h4>No spreadsheets</h4>
                            </div>
                        @else
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Client</th>
                                        <th>Rows</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($spreadsheets as $spreadsheet)
                                        <tr>
                                            <td {!! !$spreadsheet->isActive() ? 'style="text-decoration:line-through !important;"' : '' !!}>{{ $spreadsheet->name }}</td>
                                            <td {!! !$spreadsheet->isActive() ? 'style="text-decoration:line-through !important;"' : '' !!}>
                                                {{ $spreadsheet->client ? $spreadsheet->client->business_name : '---' }}
                                            </td>
                                            <td {!! !$spreadsheet->isActive() ? 'style="text-decoration:line-through !important;"' : '' !!}>{{ $spreadsheet->content->count() }}</td>
                                            <td class="no-stretch">
                                                <a href="{{ route('clientspreadsheets.edit',$spreadsheet->id) }}" title="enter data into speadsheet" class="btn btn-xs btn-success">data entry</a>
                                                <a href="{{ route('adminspreadsheetimport',$spreadsheet->id) }}" title="upload csv file" class="btn btn-xs btn-primary"><i class="fa fa-upload" aria-hidden="true"></i></a>
                                                <a href="{{ route('adminspreadsheetduplicate',$spreadsheet->id) }}" title="duplicate spreadsheet" class="btn btn-xs btn-info"><i class="fa fa-clone" aria-hidden="true"></i></a>
                                                <a href="{{ route('adminspreadsheets.edit',$spreadsheet->id) }}" title="edit spreadsheet settings" class="btn btn-xs btn-warning"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                {{ Form::open(['route'=>['adminspreadsheets.destroy',$spreadsheet->id],'method'=>'DELETE','style'=>'display:inline-block', 'onsubmit'=>"return confirm('Delete \"".addslashes($spreadsheet->name)."\" spreadsheet?');" ]) }}
                                                <button title="delete spreadsheet" class="btn btn-xs btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                                                {{ Form::close() }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

        </div>

    </div>
</div>
@endsection
