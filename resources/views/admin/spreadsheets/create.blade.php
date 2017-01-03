@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">

        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><h2 class="panel-title">{{ !empty($input['id']) ? 'Update' : 'Create' }} Spreadsheet</h2></div>

                <div class="panel-body">

                @if(!empty($input['id']))
                {{ Form::open(['route'=>['adminspreadsheets.update',$input['id']], 'method'=>'PUT']) }}
                @else
                {{ Form::open(['route'=>'adminspreadsheets.store', 'method'=>'POST']) }}
                @endif
                <div class="row"> 
                    <div class="col-md-5" >
                        <h5>Spreadsheet Name</h5>
                        <div class="form-group"> 
                        {{ Form::text('name',(!empty($input['name']) ? $input['name'] : null),['id'=>'name', 'class'=>'form-control', 'placeholder'=>'Name'])}}
                        </div>
                        <h5>Client</h5>
                        <div class="form-group"> 
                        {{ Form::select('client',$clients,(!empty($input['client_id']) ? $input['client_id'] : null),['name'=>'client_id', 'id'=>'client_id', 'class'=>'form-control'])}}
                        </div>
                        <h5>Status</h5>
                        <div class="form-group"> 
                        {{ Form::select('active',['1'=>'Active','0'=>'Inactive'],(!empty($input['active']) ? 1 : 0),['name'=>'active', 'id'=>'active', 'class'=>'form-control'])}}
                        </div>
                        {{ Form::submit('Save Spreadsheet',['class'=>'btn btn-primary']) }}
                    </div>
                    <div class="col-md-6 col-md-offset-1">
                        <h5>Column Labels</h5>
                        <div id="column-list">
                        @for($x=1; $x<=(count($input['column']) > 0 ? count($input['column']) : 2); $x++)
                            <?php $letter = $letters[$x]; ?>
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon{{$x}}">{{ $letter }}</span>
                                        {{ Form::text('label',(!empty($input['column'][$x]) ? $input['column'][$x]['label'] : null),['name'=>"column[$x][label]", 'class'=>'form-control', 'placeholder'=>'Column Label','aria-describedby'=>'basic-addon'.$x])}}
                                    </div>
                                </div>
                                <div class="col-lg-11 col-lg-offset-1">
                                    <div class="input-group">
                                        <span class="input-group-addon " id="basic-addon{{$x}}0"><div class="col-validation-label">Required</div></span>
                                        {{ Form::select('type',['1'=>'Yes','0'=>'No'],(!empty($input['column'][$x]) && !empty($input['column'][$x]['validation']->required) ? $input['column'][$x]['validation']->required : null),['name'=>"column[$x][validation][required]", 'class'=>'form-control input-sm','aria-describedby'=>'basic-addon'.$x.'0'])}}
                                    </div>
                                </div>
                                <div class="col-lg-11 col-lg-offset-1">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon{{$x}}1"><div class="col-validation-label">Data Type</div></span>
                                        {{ Form::select('type',\App\SpreadsheetColumn::$fieldtypes,(!empty($input['column'][$x]) && !empty($input['column'][$x]['validation']->type) ? $input['column'][$x]['validation']->type : null),['name'=>"column[$x][validation][type]", 'class'=>'form-control input-sm','aria-describedby'=>'basic-addon'.$x.'1'])}}
                                    </div>
                                </div>
                                <div class="col-lg-11 col-lg-offset-1">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon{{$x}}2"><div class="col-validation-label">Select from List</div></span>
                                        {{ Form::text('select',(!empty($input['column'][$x]) && !empty($input['column'][$x]['validation']->in) ? $input['column'][$x]['validation']->in : null),['name'=>"column[$x][validation][in]", 'class'=>'form-control input-sm', 'placeholder'=>'comma separated values','aria-describedby'=>'basic-addon'.$x.'2'])}}
                                    </div>
                                </div>
                                <div class="col-lg-11 col-lg-offset-1">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon{{$x}}3"><div class="col-validation-label">Min Value</div></span>
                                        {{ Form::text('min',(!empty($input['column'][$x]) && !empty($input['column'][$x]['validation']->min) ? $input['column'][$x]['validation']->min : null),['name'=>"column[$x][validation][min]", 'class'=>'form-control input-sm','aria-describedby'=>'basic-addon'.$x.'3'])}}
                                    </div>
                                </div>
                                <div class="col-lg-11 col-lg-offset-1">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon{{$x}}4"><div class="col-validation-label">Max Value</div></span>
                                        {{ Form::text('max',(!empty($input['column'][$x]) && !empty($input['column'][$x]['validation']->max) ? $input['column'][$x]['validation']->max : null),['name'=>"column[$x][validation][max]", 'class'=>'form-control input-sm','aria-describedby'=>'basic-addon'.$x.'4'])}}
                                    </div>
                                </div>
                            </div>
                            <br/>
                        @endfor
                        </div>
                        <div class="row">
                            <div class="col-lg-12 text-right"><a href="javascript:newcolumn()" class="btn btn-success btn-sm">Add Column</a></div>
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
                <div id="nextcolumnbase" style="display:none;">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon||x||">||letter||</span>
                                    {{ Form::text('label',null,['name'=>"column[||x||][label]", 'class'=>'form-control', 'placeholder'=>'Column Label','aria-describedby'=>'basic-addon||x||'])}}
                                </div>
                            </div>
                            <div class="col-lg-11 col-lg-offset-1">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon||x||0"><div class="col-validation-label">Required</div></span>
                                    {{ Form::select('type',['1'=>'Yes','0'=>'No'],null,['name'=>"column[||x||][validation][required]", 'class'=>'form-control input-sm','aria-describedby'=>'basic-addon||x||0'])}}
                                </div>
                            </div>
                            <div class="col-lg-11 col-lg-offset-1">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon||x||1"><div class="col-validation-label">Type</div></span>
                                    {{ Form::select('type',\App\SpreadsheetColumn::$fieldtypes,null,['name'=>"column[||x||][validation][type]", 'class'=>'form-control input-sm','aria-describedby'=>'basic-addon||x||1'])}}
                                </div>
                            </div>
                            <div class="col-lg-11 col-lg-offset-1">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon||x||2"><div class="col-validation-label">Select</div></span>
                                    {{ Form::text('select',null,['name'=>"column[||x||][validation][in]", 'class'=>'form-control input-sm', 'placeholder'=>'comma separated values','aria-describedby'=>'basic-addon||x||2'])}}
                                </div>
                            </div>
                            <div class="col-lg-11 col-lg-offset-1">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon||x||3"><div class="col-validation-label">Min Value</div></span>
                                    {{ Form::text('min',null,['name'=>"column[||x||][validation][min]", 'class'=>'form-control input-sm','aria-describedby'=>'basic-addon||x||3'])}}
                                </div>
                            </div>
                            <div class="col-lg-11 col-lg-offset-1">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon||x||4"><div class="col-validation-label">Max Value</div></span>
                                    {{ Form::text('max',null,['name'=>"column[||x||][validation][max]", 'class'=>'form-control input-sm','aria-describedby'=>'basic-addon||x||4'])}}
                                </div>
                            </div>
                        </div>
                        <br/>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script type="text/javascript">
    var letters = ['','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'];
    var columnnext = {{(count($input['column']) > 0 ? count($input['column'])+1 : 3)}};
    function newcolumn(){
        var content = $('#nextcolumnbase').html();
        content = content.replace(/\|\|x\|\|/g,columnnext);
        content = content.replace(/\|\|letter\|\|/g,letters[columnnext]);
        $('#column-list').append(content);
        columnnext++;
    }
</script>
@append