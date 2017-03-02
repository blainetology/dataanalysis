@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">

        <div class="col-md-12">
            <div class="panel panel-default">
                <div class="panel-heading"><h2 class="panel-title">{{ !empty($duplicate) ? 'Duplicate' : (!empty($input['id']) ? 'Update' : 'Create') }} Spreadsheet</h2></div>

                <div class="panel-body">

                @if(!empty($input['id']) && empty($duplicate))
                {{ Form::open(['route'=>['adminspreadsheets.update',$input['id']], 'method'=>'PUT']) }}
                @else
                {{ Form::open(['route'=>'adminspreadsheets.store', 'method'=>'POST']) }}
                @endif
                <div class="row"> 
                    <div class="col-md-6" >
                        <div class="row">
                            <div class="col-lg-12">
                                <h5>Spreadsheet Name</h5>
                                <div class="form-group"> 
                                {{ Form::text('name',(!empty($input['name']) ? $input['name'] : null),['id'=>'name', 'class'=>'form-control', 'placeholder'=>'Name'])}}
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <h5>Client</h5>
                                <div class="form-group"> 
                                {{ Form::select('client',$clients,(!empty($input['client_id']) ? $input['client_id'] : null),['name'=>'client_id', 'id'=>'client_id', 'class'=>'form-control'])}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" >
                        <div class="row">
                            <div class="col-md-6">
                                <h5>List Order</h5>
                                <div class="form-group"> 
                                {{ Form::text('list_order',$input['list_order'],['name'=>'list_order', 'id'=>'list_order', 'class'=>'form-control'])}}
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5>Default Sort Column</h5>
                                <div class="form-group"> 
                                {{ Form::select('sorting_col',$letters,$input['sorting_col'],['name'=>'sorting_col', 'id'=>'sorting_col', 'class'=>'form-control'])}}
                                </div>
                            </div>
                        </div>
                        <h5>Status</h5>
                        <div class="form-group"> 
                        {{ Form::select('active',['1'=>'Active','0'=>'Inactive'],(!empty($input['active']) ? 1 : 0),['name'=>'active', 'id'=>'active', 'class'=>'form-control'])}}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        {{ Form::submit('Save Spreadsheet',['class'=>'btn btn-primary']) }}
                        <br/><br/>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <h4>Column Labels</h4>
                        <div class="row" id="column-list">
                        @for($x=1; $x<=(count($input['column']) > 0 ? count($input['column']) : 2); $x++)
                            <?php $letter = $letters[$x]; ?>
                            <div class="col-lg-6 sortable" data-col="{{ $x }}" style="background:#FFF;">
                            <input type="hidden" name="column[{{$x}}][orig_val]" id="orig_val_{{ $x }}" value="{{ $x }}">
                            <input type="hidden" name="column[{{$x}}][new_val]" id="new_val_{{ $x }}" value="{{ $x }}">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon{{$x}}">{{ $letter }}</span>
                                        {{ Form::text('label',(!empty($input['column'][$x]) ? $input['column'][$x]['label'] : null),['name'=>"column[$x][label]", 'class'=>'form-control', 'placeholder'=>'Column Label','aria-describedby'=>'basic-addon'.$x])}}
                                    </div>
                                </div>
                                <div class="col-lg-11 col-lg-offset-1">
                                    <div class="input-group">
                                        <span class="input-group-addon" id="basic-addon{{$x}}1"><div class="col-validation-label">Data Type</div></span>
                                        {{ Form::select('type',\App\SpreadsheetColumn::$fieldtypes,(!empty($input['column'][$x]) && !empty($input['column'][$x]['type']) ? $input['column'][$x]['type'] : null),['name'=>"column[$x][type]", 'class'=>'form-control input-sm','aria-describedby'=>'basic-addon'.$x.'1'])}}
                                    </div>
                                </div>
                                <div class="col-lg-11 col-lg-offset-1"><h5>Validations</h5></div>
                                <div class="col-lg-11 col-lg-offset-1">
                                    <div class="input-group">
                                        <span class="input-group-addon " id="basic-addon{{$x}}0"><div class="col-validation-label">Required</div></span>
                                        {{ Form::select('type',['1'=>'Yes','0'=>'No'],(!empty($input['column'][$x]) && !empty($input['column'][$x]['validation']->required) ? $input['column'][$x]['validation']->required : null),['name'=>"column[$x][validation][required]", 'class'=>'form-control input-sm','aria-describedby'=>'basic-addon'.$x.'0'])}}
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
                            <br/><br/>
                            </div>
                        @endfor
                        </div>
                        <div class="row">
                            <div class="col-xs-6 text-left">{{ Form::submit('Save Spreadsheet',['class'=>'btn btn-primary']) }}</div>
                            <div class="col-xs-6 text-right"><a href="javascript:newcolumn()" class="btn btn-success btn-sm">Add Column</a></div>
                        </div>
                    </div>
                </div>
                {{ Form::close() }}
                <div id="nextcolumnbase" style="display:none;">
                    <div class="col-lg-6 sortable" data-col="||x||">
                        <input type="hidden" name="column[||x||][orig_val]" value="||x||">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon||x||">||letter||</span>
                                    {{ Form::text('label',null,['name'=>"column[||x||][label]", 'class'=>'form-control', 'placeholder'=>'Column Label','aria-describedby'=>'basic-addon||x||'])}}
                                </div>
                            </div>
                            <div class="col-lg-11 col-lg-offset-1">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon||x||1"><div class="col-validation-label">Data Type</div></span>
                                    {{ Form::select('type',\App\SpreadsheetColumn::$fieldtypes,null,['name'=>"column[||x||][type]", 'class'=>'form-control input-sm','aria-describedby'=>'basic-addon||x||1'])}}
                                </div>
                            </div>
                                <div class="col-lg-11 col-lg-offset-1"><h5>Validations</h5></div>
                            <div class="col-lg-11 col-lg-offset-1">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon||x||0"><div class="col-validation-label">Required</div></span>
                                    {{ Form::select('type',['1'=>'Yes','0'=>'No'],null,['name'=>"column[||x||][validation][required]", 'class'=>'form-control input-sm','aria-describedby'=>'basic-addon||x||0'])}}
                                </div>
                            </div>
                            <div class="col-lg-11 col-lg-offset-1">
                                <div class="input-group">
                                    <span class="input-group-addon" id="basic-addon||x||2"><div class="col-validation-label">Select from List</div></span>
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
                        <br/><br/>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@section('styles')
<link href="/css/jquery-ui.theme.min.css" rel="stylesheet" >
@append

@section('scripts')
<script src="/js/jquery-ui.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#column-list').sortable({placeholder: "sortable-placeholder"});
    });
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