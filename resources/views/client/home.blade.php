@extends('layouts.app')

@section('content')
@include('client.sidebar')
<div style="margin-left:200px; position: relative;">
    <div id="page-header" style="height:52px; position: fixed; left:200px; top:50px; padding-top:10px; border-bottom:1px solid #EEE; background: #F9F9F9; z-index: 100; width:100%; box-sizing: border-box;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-8">
                    <h3 class="text-info" style="margin-bottom:0; padding-bottom:0; margin-top:4px; font-size: 1.5em;">Client Dashboard</h3>
                </div>
            </div>
        </div>
    </div>     
    <div class="container-fluid">
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
                <div class="row">
                    <div class="col-md-12">
                        <div class="panel panel-primary">
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

            <div class="col-md-4">
                <div class="panel panel-primary">
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
</div>
@endsection
