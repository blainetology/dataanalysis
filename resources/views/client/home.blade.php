@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Client Dashboard</h2>
        </div>
        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><strong>My Spreadsheets <span class="label label-default">{{ $spreadsheets->count() }}</span></strong></div>

                <div class="panel-body">
                    @if($spreadsheets->count()==0)
                        No spreadsheets
                    @else
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($spreadsheets as $spreadsheet)
                                    <tr>
                                        <td>{{ $spreadsheet->name }}</td>
                                        <td class="no-stretch">
                                            <a href="{{ route('spreadsheets.edit',$client->id) }}" class="btn btn-xs btn-warning">edit</a>
                                            <a href="{{ route('spreadsheets.destroy',$client->id) }}" class="btn btn-xs btn-danger">delete</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="panel panel-default">
                <div class="panel-heading"><strong>Account Users <span class="label label-default">{{ $users->count() }}</span></strong></div>

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
