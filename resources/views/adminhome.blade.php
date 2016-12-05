@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><strong>Clients <span class="label label-default">{{ $clients->count() }}</span></strong></div>

                <div class="panel-body">
                    @if($clients->count()==0)
                        No clients
                    @else
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Business Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($clients as $client)
                                    <tr>
                                        <td>{{ $client->business_name }}</td>
                                        <td class="no-stretch">
                                            <a href="{{ route('clients.edit',$client->id) }}" class="btn btn-xs btn-warning">edit</a>
                                            <a href="{{ route('clients.destroy',$client->id) }}" class="btn btn-xs btn-danger">delete</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    <br/>
                    <a href="{{ route('clients.create') }}" class="btn btn-primary">Create Client</a>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><strong>Users <span class="label label-default">{{ $clients->count() }}</span></strong></div>

                <div class="panel-body">
                    @if($users->count()==0)
                        No users
                    @else
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td class="no-stretch">
                                            <a href="{{ route('users.edit',$user->id) }}" class="btn btn-xs btn-warning">edit</a>
                                            <a href="{{ route('users.destroy',$user->id) }}" class="btn btn-xs btn-danger">delete</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    <br/>
                    <a href="{{ route('users.create') }}" class="btn btn-primary">Create User</a>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
