@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Report Admin</h2>
        </div>
    </div>
    <div class="row">
        {{-- left column --}}
        <div class="col-md-12">

                    {{-- reports --}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <a href="{{ route('reports.create') }}" class="btn btn-sm btn-primary pull-right">Create Report</a>
                            <strong style="font-size:1.3em;" class="text-info">Reports <span class="label label-success">{{ $reports->count() }}</span></strong>
                        </div>

                        @if($reports->count()==0)
                            <div class="panel-body">
                                <h4>No reports</h4>
                            </div>
                        @else
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Client</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $report)
                                        <tr>
                                            <td>{{ $report->name }}</td>
                                            <td>
                                                {{ $report->client ? $report->client->business_name : '---' }}
                                            </td>
                                            <td class="no-stretch">
                                                <a href="{{ route('reports.show',$report->id) }}" title="enter data into report" class="btn btn-xs btn-success">view</a>
                                                <a href="{{ route('reports.edit',$report->id) }}" title="edit report settings" class="btn btn-xs btn-warning"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                {{ Form::open(['route'=>['reports.destroy',$report->id],'method'=>'DELETE','style'=>'display:inline-block', 'onsubmit'=>"return confirm('Delete \"".addslashes($report->name)."\" report?');" ]) }}
                                                <button title="delete report" class="btn btn-xs btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
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
