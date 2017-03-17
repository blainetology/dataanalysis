@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <h2>Client Admin</h2>
        </div>
    </div>
    <div class="row">
        {{-- left column --}}
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

        </div>

    </div>
</div>
@endsection
