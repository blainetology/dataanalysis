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
                    <div class="panel panel-default">
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
                                            <td>{{ $client->business_name }}</td>
                                            <td>{{ $client->users->count() }}</td>
                                            <td>{{ $client->spreadsheets->count() }}</td>
                                            <td class="no-stretch">
                                                <a href="{{ route('adminclients.edit',$client->id) }}" class="btn btn-xs btn-warning" title="edit client"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                @if($client->trashed())
                                                    <a href="{{ route('adminclients.edit',$client->id) }}" class="btn btn-xs btn-success" title="restore client"><i class="fa fa-reply" aria-hidden="true"></i></a>
                                                @else
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
                            <a href="{{ route('adminusers.create') }}" class="btn btn-sm btn-primary pull-right">Create User</a>
                            <strong style="font-size:1.3em;" class="text-info">Users <span class="label label-success">{{ $users->count() }}</span></strong>
                        </div>

                        @if($users->count()==0)
                            <div class="panel-body">
                               <h4> No users</h4>
                            </div>
                        @else
                            <table class="table table-striped">
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
                                            <td>{{ $user->displayname() }}</td>
                                            <td>
                                                @if($user->admin == 1)
                                                <div class="label label-success">admin</div>
                                                @elseif($user->editor == 1)
                                                <div class="label label-warning">editor</div>
                                                @else
                                                {!! $user->client ? $user->client->business_name : '<div class="label label-danger">none</div>' !!}
                                                @endif
                                            </td>
                                            <td>{{ $user->last_login }}</td>
                                            <td class="no-stretch">
                                                <a href="{{ route('adminusers.edit',$user->id) }}" class="btn btn-xs btn-warning"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
                                                {{ Form::open(['route'=>['adminusers.destroy',$user->id],'method'=>'DELETE','style'=>'display:inline-block', 'onsubmit'=>"return confirm('Delete user \"".addslashes($user->displayname())."\"?');" ]) }}
                                                <button title="delete user" class="btn btn-xs btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
                                                {{ Form::close() }}
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
                                            <td>{{ $spreadsheet->name }}</td>
                                            <td>
                                                {{ $spreadsheet->client ? $spreadsheet->client->business_name : '---' }}
                                            </td>
                                            <td>{{ $spreadsheet->content->count() }}</td>
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

        {{-- right column --}}
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading"><strong>Stats</strong></div>

                        <div class="panel-body">
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
