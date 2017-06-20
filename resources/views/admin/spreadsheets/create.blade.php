@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
    <br/>
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading"><strong style="font-size:1.3em;" class="text-info">{{ !empty($duplicate) ? 'Duplicate' : (!empty($input['id']) ? 'Update' : 'Create') }} Spreadsheet</strong></div>

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
                                    {{ Form::text('list_order',(!empty($input['list_order']) ? $input['list_order'] : null),['name'=>'list_order', 'id'=>'list_order', 'class'=>'form-control'])}}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h5>Default Sort Column</h5>
                                    <div class="form-group"> 
                                    {{ Form::select('sorting_col',$letters,(!empty($input['sorting_col']) ? $input['sorting_col'] : null),['name'=>'sorting_col', 'id'=>'sorting_col', 'class'=>'form-control'])}}
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
                            <br/>
                        </div>
                    </div>
                    <hr/>
                    <div class="row">
                        <div class="col-lg-12">
                            <h4>Columns</h4>
                            <p class="text-danger">Click and drag the letters to rearrange the column order.</p>
                            <div class="row" id="column-list">
                            @for($x=1; $x<=(count($input['column']) > 0 ? count($input['column']) : 2); $x++)
                                <?php $letter = $letters[$x]; ?>
                                <div class="col-lg-12 sortable" data-col="{{ $x }}">

                                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                        <div class="panel panel-default">
                                            <div class="panel-heading" role="tab" id="heading{{ $x }}">
                                                <div class="input-group">
                                                    <span class="input-group-addon input-group-letter" id="basic-addon-{{$x}}">{{ $letter }}</span>
                                                    {{ Form::text('label',(!empty($input['column'][$x]) ? $input['column'][$x]['label'] : null),['name'=>"column[$x][label]", 'class'=>'form-control', 'placeholder'=>'Column Label','aria-describedby'=>'basic-addon'.$x, 'id'=>'input-label-'.$x, 'onFocus'=>"$('.collapse').not($('#collapse".$x."')).collapse('hide'); $('#collapse".$x."').collapse('show')"])}}
                                                </div>
                                            </div>
                                            <div id="collapse{{ $x }}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading{{ $x }}">
                                                <div class="panel-body">

                                                    <input type="hidden" name="column[{{$x}}][col_val]" id="col_val_{{ $x }}" value="{{ $x }}">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="input-group">
                                                                <span class="input-group-addon" id="basic-addon-{{$x}}-1"><div class="col-validation-label">Data Type</div></span>
                                                                {{ Form::select('type',\App\SpreadsheetColumn::$fieldtypes,(!empty($input['column'][$x]) && !empty($input['column'][$x]['type']) ? $input['column'][$x]['type'] : null),['name'=>"column[$x][type]", 'class'=>'form-control input-sm','aria-describedby'=>'basic-addon'.$x.'1'])}}
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12"><h5>Conditional</h5></div>
                                                        <div class="col-lg-12">
                                                            <div class="input-group">
                                                                <span class="input-group-addon " id="basic-addon-{{$x}}-2">If</span>
                                                                {{ Form::text('if',(!empty($input['column'][$x]) && !empty($input['column'][$x]['conditional']->if) ? $input['column'][$x]['conditional']->if : null),['name'=>"column[$x][conditional][if]", 'class'=>'form-control input-sm', 'placeholder'=>'a=yes, b=yes','aria-describedby'=>'basic-addon'.$x.'2'])}}
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="input-group">
                                                                <span class="input-group-addon " id="basic-addon-{{$x}}-3">Then</span>
                                                                {{ Form::text('then',(!empty($input['column'][$x]) && !empty($input['column'][$x]['conditional']->then) ? $input['column'][$x]['conditional']->then : null),['name'=>"column[$x][conditional][then]", 'class'=>'form-control input-sm', 'placeholder'=>'value if true','aria-describedby'=>'basic-addon'.$x.'3'])}}
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-6">
                                                            <div class="input-group">
                                                                <span class="input-group-addon " id="basic-addon-{{$x}}-4">Else</span>
                                                                {{ Form::text('else',(!empty($input['column'][$x]) && !empty($input['column'][$x]['conditional']->else) ? $input['column'][$x]['conditional']->else : null),['name'=>"column[$x][conditional][else]", 'class'=>'form-control input-sm', 'placeholder'=>'value if false','aria-describedby'=>'basic-addon'.$x.'4'])}}
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12"><h5>Validations</h5></div>
                                                        <div class="col-lg-12">
                                                            <div class="input-group">
                                                                <span class="input-group-addon " id="basic-addon-{{$x}}-5"><div class="col-validation-label">Required</div></span>
                                                                {{ Form::select('type',['1'=>'Yes','0'=>'No'],(!empty($input['column'][$x]) && !empty($input['column'][$x]['validation']) && !empty($input['column'][$x]['validation']->required) ? $input['column'][$x]['validation']->required : null),['name'=>"column[$x][validation][required]", 'class'=>'form-control input-sm','aria-describedby'=>'basic-addon'.$x.'5'])}}
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="input-group">
                                                                <span class="input-group-addon" id="basic-addon-{{$x}}-6"><div class="col-validation-label">Select from List</div></span>
                                                                {{ Form::text('select',(!empty($input['column'][$x]) && !empty($input['column'][$x]['validation']) && !empty($input['column'][$x]['validation']->in) ? $input['column'][$x]['validation']->in : null),['name'=>"column[$x][validation][in]", 'class'=>'form-control input-sm', 'placeholder'=>'comma separated values','aria-describedby'=>'basic-addon'.$x.'6'])}}
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="input-group">
                                                                <span class="input-group-addon" id="basic-addon-{{$x}}-7"><div class="col-validation-label">Min Value</div></span>
                                                                {{ Form::text('min',(!empty($input['column'][$x]) && !empty($input['column'][$x]['validation']) && !empty($input['column'][$x]['validation']->min) ? $input['column'][$x]['validation']->min : null),['name'=>"column[$x][validation][min]", 'class'=>'form-control input-sm','aria-describedby'=>'basic-addon'.$x.'7'])}}
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="input-group">
                                                                <span class="input-group-addon" id="basic-addon-{{$x}}-8"><div class="col-validation-label">Max Value</div></span>
                                                                {{ Form::text('max',(!empty($input['column'][$x]) && !empty($input['column'][$x]['validation']) && !empty($input['column'][$x]['validation']->max) ? $input['column'][$x]['validation']->max : null),['name'=>"column[$x][validation][max]", 'class'=>'form-control input-sm','aria-describedby'=>'basic-addon'.$x.'8'])}}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
                        <div class="col-lg-12 sortable" data-col="||x||">

                            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                                <div class="panel panel-default">
                                    <div class="panel-heading" role="tab" id="heading||x||">
                                        <div class="input-group">
                                            <span class="input-group-addon input-group-letter" id="basic-addon-||x||">||letter||</span>
                                            {{ Form::text('label',null,['name'=>"column[||x||][label]", 'class'=>'form-control', 'placeholder'=>'Column Label','aria-describedby'=>'basic-addon-||x||', 'id'=>'input-label-||x||', 'onFocus'=>"$('.collapse').not($('#collapse||x||')).collapse('hide'); $('#collapse||x||').collapse('show')"])}}
                                        </div>
                                    </div>
                                    <div id="collapse||x||" class="panel-collapse collapse" role="tabpanel" aria-labelledby="heading||x||">
                                        <div class="panel-body">

                                            <input type="hidden" name="column[||x||][col_val]" id="col_val_||x||" value="||x||">
                                            <div class="row">
                                                <div class="col-lg-12">
                                                    <div class="input-group">
                                                        <span class="input-group-addon" id="basic-addon-||x||-1"><div class="col-validation-label">Data Type</div></span>
                                                        {{ Form::select('type',\App\SpreadsheetColumn::$fieldtypes,null,['name'=>"column[||x||][type]", 'class'=>'form-control input-sm','aria-describedby'=>'basic-addon-||x||-1'])}}
                                                    </div>
                                                </div>
                                                <div class="col-lg-12"><h5>Conditional</h5></div>
                                                <div class="col-lg-12">
                                                    <div class="input-group">
                                                        <span class="input-group-addon " id="basic-addon-||x||-2">If</span>
                                                        {{ Form::text('if',null,['name'=>"column[||x||][conditional][if]", 'class'=>'form-control input-sm', 'placeholder'=>'a=yes, b=yes','aria-describedby'=>'basic-addon-||x||-2'])}}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="input-group">
                                                        <span class="input-group-addon " id="basic-addon-||x||-3">Then</span>
                                                        {{ Form::text('then',null,['name'=>"column||x||][conditional][then]", 'class'=>'form-control input-sm', 'placeholder'=>'value if true','aria-describedby'=>'basic-addon-||x||-3'])}}
                                                    </div>
                                                </div>
                                                <div class="col-lg-6">
                                                    <div class="input-group">
                                                        <span class="input-group-addon " id="basic-addon-||x||-4">Else</span>
                                                        {{ Form::text('else',null,['name'=>"column[||x||][conditional][else]", 'class'=>'form-control input-sm', 'placeholder'=>'value if false','aria-describedby'=>'basic-addon-||x||-4'])}}
                                                    </div>
                                                </div>
                                                 <div class="col-lg-12"><h5>Validations</h5></div>
                                                    <div class="col-lg-12">
                                                        <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon-||x||-5"><div class="col-validation-label">Required</div></span>
                                                            {{ Form::select('type',['1'=>'Yes','0'=>'No'],null,['name'=>"column[||x||][validation][required]", 'class'=>'form-control input-sm','aria-describedby'=>'basic-addon-||x||-5'])}}
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon-||x||-6"><div class="col-validation-label">Select from List</div></span>
                                                            {{ Form::text('select',null,['name'=>"column[||x||][validation][in]", 'class'=>'form-control input-sm', 'placeholder'=>'comma separated values','aria-describedby'=>'basic-addon-||x||-6'])}}
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon-||x||-7"><div class="col-validation-label">Min Value</div></span>
                                                            {{ Form::text('min',null,['name'=>"column[||x||][validation][min]", 'class'=>'form-control input-sm','aria-describedby'=>'basic-addon-||x||-7'])}}
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-12">
                                                        <div class="input-group">
                                                            <span class="input-group-addon" id="basic-addon-||x||-8"><div class="col-validation-label">Max Value</div></span>
                                                            {{ Form::text('max',null,['name'=>"column[||x||][validation][max]", 'class'=>'form-control input-sm','aria-describedby'=>'basic-addon-||x||-8'])}}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<link href="/css/jquery-ui.theme.min.css" rel="stylesheet" >
<style type="text/css">
    .panel-group{margin-bottom:5px !important;}
    .input-group-letter{cursor:move;}
</style>
@append

@section('scripts')
<script src="/js/jquery-ui.min.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#column-list').sortable({
            placeholder: "sortable-placeholder",
            stop: function( event, ui ){
                var x=1;
                $('#column-list .sortable').each(function(index,value){
                    var col = $(this).data('col');
                    var letter = letters[x];
                    console.log(x,col);
                    $('#col_val_'+col).val(x);
                    $('#basic-addon-'+col).html(letter);
                    x++;
                });
            }
        });
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