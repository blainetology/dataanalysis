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
                                            <a href="{{ route('clientspreadsheets.edit',$spreadsheet->id) }}" class="btn btn-xs btn-success">data entry</a>
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
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->displayname() }}</td>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    <br/>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
