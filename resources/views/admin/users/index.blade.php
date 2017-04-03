@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>User Admin</h2>
        </div>
    </div>
    <div class="row">
        {{-- left column --}}
        <div class="col-md-12">

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
                                <th>Title</th>
                                <th>Client</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                                <tr>
                                    <td {!! $user->trashed() ? 'style="text-decoration:line-through !important;"' : '' !!}>{{ $user->displayname() }}</td>
                                    <td {!! $user->trashed() ? 'style="text-decoration:line-through !important;"' : '' !!}>{{ $user->title }}</td>
                                    <td>
                                        @if($user->admin == 1)
                                            <div class="label label-success">admin</div>
                                        @elseif($user->editor == 1)
                                            <div class="label label-warning">editor</div>
                                        @else
                                            <div {!! $user->trashed() || $user->client->trashed() ? 'style="text-decoration:line-through !important;"' : '' !!}>{!! $user->client ? $user->client->business_name : '<div class="label label-danger">none</div>' !!}</div>
                                        @endif
                                    </td>
                                    <td {!! $user->trashed() ? 'style="text-decoration:line-through !important;"' : '' !!}>{{ $user->last_login }}</td>
                                    <td class="no-stretch">
                                        @if($user->trashed())
                                            <a href="{{ route('adminusers.restore',$user->id) }}" class="btn btn-xs btn-success">restore</a>
                                        @else
                                            @if($user->admin == 0 && $user->editor == 0)
                                                <a href="{{ route('adminusers.edit',$user->id) }}" class="btn btn-xs btn-warning"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>
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

        </div>

    </div>
</div>
@endsection
