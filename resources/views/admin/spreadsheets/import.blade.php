@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">

        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><h2 class="panel-title">Import CSV Into Spreadsheet</h2></div>

                <div class="panel-body">

                {{ Form::open(['route'=>['adminspreadsheetimport',$spreadsheet->id], 'method'=>'POST', 'files'=>true, 'onsubmit'=>'onSubmitImport(this)']) }}
                <div class="row"> 
                    <div class="col-md-6" >
                        <div class="row">
                            <div class="col-lg-12">
                                <h4>Importing Into</h4> 
                                <strong class="text-danger">{{ $spreadsheet->client->business_name }} &nbsp; &gt;&gt; &nbsp; {{ $spreadsheet->name }} spreadsheet</strong>
                                <br/><br/>
                                <h4>Choose CSV File to Import</h4>
                                <div class="form-group"> 
                                {{ Form::file('csv',['id'=>'csv', 'class'=>'form-control', 'placeholder'=>'Name'])}}
                                </div>
                                <br/>
                                <h5>Would you like to append this data to your existing spreadsheet records, or completely replace the existing spreadsheet content with the data?</h5>
                                <div class="form-group"> 
                                {{ Form::select('replace',[0=>'Append data',1=>'Replace data'], null, ['class'=>'form-control', 'id'=>'replace'])}}
                                </div>
                                <br/>
                                <h5>{{ Form::checkbox('skipfirst',1, null, ['id'=>'skipfirst'])}} Skip the first row of data</h5>
                                <br/>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" >
                        <div class="row">
                            <div class="col-lg-12">
                                
                            
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        {{ Form::submit('Import Data',['class'=>'btn btn-primary']) }}
                        <br/><br/>
                    </div>
                </div>
                {{ Form::close() }}
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    var letters = ['','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
    function onSubmitImport(that){
        if($('#replace').val()==1){
            return confirm('Are you sure you want to completely replace the current data?');
        }
        return true;
    }
</script>
@append