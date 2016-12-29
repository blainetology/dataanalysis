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
                                                <a href="{{ route('adminclients.show',$client->id) }}" class="btn btn-xs btn-info">view</a>
                                                <a href="{{ route('adminclients.edit',$client->id) }}" class="btn btn-xs btn-warning">edit</a>
                                                <a href="{{ route('adminclients.destroy',$client->id) }}" class="btn btn-xs btn-danger">delete</a>
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
                                                {{ $user->client ? $user->client->business_name : '---' }}
                                                @endif
                                            </td>
                                            <td>{{ $user->last_login }}</td>
                                            <td class="no-stretch">
                                                <a href="{{ route('adminusers.edit',$user->id) }}" class="btn btn-xs btn-warning">edit</a>
                                                <a href="{{ route('adminusers.destroy',$user->id) }}" class="btn btn-xs btn-danger">delete</a>
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
                                            <td class="no-stretch">
                                                <a href="{{ route('clientspreadsheets.edit',$spreadsheet->id) }}" class="btn btn-xs btn-success">data entry</a>
                                                <a href="{{ route('adminspreadsheets.edit',$spreadsheet->id) }}" class="btn btn-xs btn-warning">edit</a>
                                                <a href="{{ route('adminspreadsheets.destroy',$spreadsheet->id) }}" class="btn btn-xs btn-danger">delete</a>
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
