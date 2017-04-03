@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        @if(!\Auth::user()->client)
        <div class="col-md-12 text-center">
            <h4>&nbsp;</h4>
            <h4>&nbsp;</h4>
            <h4>&nbsp;</h4>
            <h4>&nbsp;</h4>
            <h4>&nbsp;</h4>
            <h4>&nbsp;</h4>
            <h4>&nbsp;</h4>
            <h4 class="text-danger">This account is not associated with any client</h4>
            <h4>&nbsp;</h4>
            <h4>&nbsp;</h4>
            <h4>&nbsp;</h4>
            <h4>&nbsp;</h4>
            <h4>&nbsp;</h4>
            <h4>&nbsp;</h4>
            <h4>&nbsp;</h4>
            <h4>&nbsp;</h4>
            <h4>&nbsp;</h4>
        </div>
        @else
        <div class="col-md-12">
            <h2>Client Dashboard</h2>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"><strong>My Spreadsheets <span class="label label-default">{{ $spreadsheets->count() }}</span></strong></div>

                <div class="panel-body">
                    @if($spreadsheets->count()==0)
                        No spreadsheets
                    @else
                        <table class="table table-striped">
                            <tbody>
                                @foreach($spreadsheets as $spreadsheet)
                                    <tr>
                                        <td><a href="{{ route('clientspreadsheets.edit',$spreadsheet->id) }}">{{ $spreadsheet->name }}</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"><strong>My Reports <span class="label label-default">{{ $spreadsheets->count() }}</span></strong></div>

                <div class="panel-body">
                    @if($reports->count()==0)
                        No reports
                    @else
                        <table class="table table-striped">
                            <tbody>
                                @foreach($reports as $report)
                                    <tr>
                                        <td><a href="{{ route('reports.show',$report->id) }}">{{ $report->name }}</a></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading"><strong>Account Users <span class="label label-default">{{ $users->count() }}</span></strong></div>

                <div class="panel-body">
                    @if($users->count()==0)
                        No users
                    @else
                        <table class="table table-striped">
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->displayname() }} <small>({{ $user->email }})</small></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                    <br/>
                </div>
            </div>
        </div>
        @endif

    </div>
</div>
@endsection
