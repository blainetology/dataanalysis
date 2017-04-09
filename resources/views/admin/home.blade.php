@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Admin Dashboard</h2>
        </div>
    </div>
    <div class="row">
        {{-- left column --}}
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-12">

                    {{-- clients --}}
                    <div class="panel panel-default hidden">
                        <div class="panel-heading">
                            <a href="{{ route('adminclients.create') }}" class="btn btn-sm btn-primary pull-right">Create Client</a>
                            <strong style="font-size:1.3em;" class="text-info">Clients <span class="label label-success">{{ $clients->count() }}</span></strong>
                        </div>

                        @if($clients->count()==0)
                            <div class="panel-body">
                                <h4>No clients</h4>
                            </div>
                        @else
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Business Name</th>
                                        <th>Users</th>
                                        <th>Spreadsheets</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clients as $client)
                                        <tr>
                                            <td {!! $client->trashed() ? 'style="text-decoration:line-through !important;"' : '' !!}>{{ $client->business_name }}</td>
                                            <td {!! $client->trashed() ? 'style="text-decoration:line-through !important;"' : '' !!}>{{ $client->users->count() }}</td>
                                            <td {!! $client->trashed() ? 'style="text-decoration:line-through !important;"' : '' !!}>{{ $client->spreadsheets->count() }}</td>
                                            <td class="no-stretch">
                                                @if($client->trashed())
                                                    <a href="{{ route('adminclients.restore',$client->id) }}" class="btn btn-xs btn-success" title="restore client">restore</a>
                                                @else
                                                    <a href="{{ route('adminclients.edit',$client->id) }}" class="btn btn-xs btn-warning" title="edit client"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                    {{ Form::open(['route'=>['adminclients.destroy',$client->id],'method'=>'DELETE','style'=>'display:inline-block', 'onsubmit'=>"return confirm('Delete client \"".addslashes($client->business_name)."\"?');" ]) }}
                                                    <button title="delete client" class="btn btn-xs btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                                                    {{ Form::close() }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                    {{-- users --}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <strong style="font-size:1.1em;" class="text-info">Recent User Logins</strong>
                        </div>

                        @if($users->count()==0)
                            <div class="panel-body">
                               <h4> No users</h4>
                            </div>
                        @else
                            <table class="table table-striped table-condensed">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Client</th>
                                        <th>Last Login</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td {!! $user->trashed() ? 'style="text-decoration:line-through !important;"' : '' !!}>{{ $user->displayname() }}</td>
                                            <td>
                                                @if($user->admin == 1)
                                                    <div class="label label-success">admin</div>
                                                @elseif($user->editor == 1)
                                                    <div class="label label-warning">editor</div>
                                                @else
                                                    <div {!! $user->trashed() || $user->client->trashed() ? 'style="text-decoration:line-through !important;"' : '' !!}>{!! $user->client ? $user->client->business_name : '<div class="label label-danger">none</div>' !!}</div>
                                                @endif
                                            </td>
                                            <td class="no-stretch" {!! $user->trashed() ? 'style="text-decoration:line-through !important;"' : '' !!}>{{ $user->last_login }}</td>
                                            <td class="no-stretch">
                                                @if($user->trashed())
                                                    <a href="{{ route('adminusers.restore',$user->id) }}" class="btn btn-xs btn-success">restore</a>
                                                @else
                                                    <a href="{{ route('adminusers.edit',$user->id) }}" class="btn btn-xs btn-warning"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                    @if($user->admin == 0)
                                                    {{ Form::open(['route'=>['adminusers.destroy',$user->id],'method'=>'DELETE','style'=>'display:inline-block', 'onsubmit'=>"return confirm('Delete user \"".addslashes($user->displayname())."\"?');" ]) }}
                                                    <button title="delete user" class="btn btn-xs btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                                                    {{ Form::close() }}
                                                    @endif
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                    {{-- spreadsheets --}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <strong style="font-size:1.1em;" class="text-info">Recently Updated Spreadsheets</strong>
                        </div>

                        @if($spreadsheets->count()==0)
                            <div class="panel-body">
                                <h4>No spreadsheets</h4>
                            </div>
                        @else
                            <table class="table table-striped table-condensed">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Client</th>
                                        <th>Last Updated</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($spreadsheets as $spreadsheet)
                                        <tr>
                                            <td {!! !$spreadsheet->isActive() ? 'style="text-decoration:line-through !important;"' : '' !!}>{{ $spreadsheet->name }}</td>
                                            <td {!! !$spreadsheet->isActive() || $spreadsheet->client->trashed() ? 'style="text-decoration:line-through !important;"' : '' !!}>
                                                {{ $spreadsheet->client ? $spreadsheet->client->business_name : '---' }}
                                            </td>
                                            <td class="no-stretch" {!! !$spreadsheet->isActive() ? 'style="text-decoration:line-through;"' : '' !!}>{{ $spreadsheet->updated_at }}</td>
                                            <td class="no-stretch text-right">
                                                @if($spreadsheet->active == 1)
                                                <a href="{{ route('clientspreadsheets.edit',$spreadsheet->id) }}" title="enter data into speadsheet" class="btn btn-xs btn-success">data entry</a>
                                                @endif
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

                    {{-- reports --}}
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <strong style="font-size:1.1em;" class="text-info">Recently Viewed Reports</strong>
                        </div>

                        @if($reports->count()==0)
                            <div class="panel-body">
                                <h4>No reports</h4>
                            </div>
                        @else
                            <table class="table table-striped table-condensed">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Client</th>
                                        <th>Last Viewed</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($reports as $report)
                                        <tr>
                                            <td {!! !$report->isActive() ? 'style="text-decoration:line-through !important;"' : '' !!}>{{ $report->name }}</td>
                                            <td {!! !$report->isActive() || $report->client->trashed() ? 'style="text-decoration:line-through !important;"' : '' !!}>
                                                {{ $report->client ? $report->client->business_name : '---' }}
                                            </td>
                                            <td class="no-stretch">{{ $report->opened_at }}</td>
                                            <td class="no-stretch">
                                                @if($report->active == 1)
                                                <a href="{{ route('reports.show',$report->id) }}" title="view report" class="btn btn-xs btn-success">view</a>
                                                @endif
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

        {{-- right column --}}
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading"><strong>Activity Log</strong></div>

                        <table class="table table-striped table-condensed">
                        @foreach($logs as $log)
                            <tr>
                            <td class="small">
                            <?php 
                            $logmodel = $log->{$log->model}->first();
                            ?>
                            @if($log->action == 'login')
                            <i class="fa fa-sign-in" aria-hidden="true"></i>
                            @elseif($log->model=='user')
                            <i class="fa fa-user text-danger" aria-hidden="true"></i>
                            @elseif($log->model=='client')
                            <i class="fa fa-users text-warning" aria-hidden="true"></i>
                            @elseif($log->model=='report')
                            <i class="fa fa-bar-chart text-success" aria-hidden="true"></i>
                            @elseif($log->model=='spreadsheet')
                            <i class="fa fa-table text-info" aria-hidden="true"></i>
                            @endif
                            <small>{{ date('m/d/y h:iA',strtotime($log->created_at)) }}</small><br/>
                            <strong>{{ $log->auth ? $log->auth->displayname() : '[removed]' }}</strong> <em>{{ $log->action }}</em>
                            @if($log->action != 'login')
                            {{ $log->model }}
                            <strong>{{ $logmodel ? $logmodel->displayname() : '[removed]' }}</strong>
                            @endif
                            </td>
                            </tr>
                        @endforeach
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
