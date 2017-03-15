@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        {{ Form::open(['route'=>['clientspreadsheets.update',$spreadsheet->id],'method'=>'PUT','onsubmit'=>'sheetupdated=false']) }}
        <input type="hidden" name="sort_col" id="sort_col" value="{{$sort_col}}">
        <input type="hidden" name="field_ids" id="field_ids" value="{{$field_ids}}">
        <div class="col-md-12">
            <span class="pull-right" id="action_bar">
                <a href="/client/spreadsheets/{{$spreadsheet->id}}/export?{{$_SERVER['QUERY_STRING']}}" class="btn btn-info btn-sm" id="exportbutton"><i class="fa fa-download" aria-hidden="true"></i> export to csv</a>
                @if(!empty(\Request::input('filter',[])))
                <a href="/client/spreadsheets/{{$spreadsheet->id}}/edit" class="btn btn-warning btn-sm" id="clearfilters"><i class="fa fa-refresh" aria-hidden="true"></i> reset filters</a>
                @endif
                <button class="btn btn-success btn-sm hidden" id="savebutton" type="submit"><i class="fa fa-floppy-o" aria-hidden="true"></i> save changes</button>
            </span>
            <ul class="nav nav-tabs">
                @foreach($client_spreadsheets as $sheet)
                <li role="presentation" {!! ($spreadsheet->id == $sheet->id) ? 'class="active"' : '' !!}><a href="/client/spreadsheets/{{ $sheet->id }}/edit">{{$sheet->name}}</a></li>
                @endforeach
            </ul>

        </div>
        <div class="col-md-12" style="padding:3px;">
            <pre class="hidden">
            {{ print_r($validations,true) }}
            </pre>
            <div id="spreadsheetContainer" style="overflow:auto; width:100%; height:84px;">
                <table id="spreadsheet" class="table table-bordered table-striped table-condensed" style="margin-bottom:5px;">
                    <thead>
                        <tr>
                            <td class="bg-info0"></td>
                            @for($x=1; $x<=$max; $x++)
                                <th class="bg-info no-stretch col{{$x}}" style="width:{{round(100/$max)}}%; vertical-align:top; padding-top:2px;">
                                    <div class="small text-info" style="font-weight:100;{{ $sort_col==$x ? ' text-decoration:underline;' : ''}}"><a href="?{{$queryvars}}&sort_col={{$x}}">Column {{$letters[$x]}}</a></div>
                                    {{ isset($columns[$x]) ? $columns[$x]['label'] : '' }}
                                    @if(\Auth::user()->isEditor())
                                        <br/>
                                        @if($columns[$x]->type=='date')
                                            <input name="filter[col{{$x}}][min]" class="form-control input-sm filter-input type_date" onchange="applyFilter()" value="{{\Request::input('filter.col'.$x.'.min')}}" placeholder="min">
                                            <input name="filter[col{{$x}}][max]" class="form-control input-sm filter-input type_date" onchange="applyFilter()" value="{{\Request::input('filter.col'.$x.'.max')}}" placeholder="max">
                                        @elseif($columns[$x]->type=='numeric' || $columns[$x]->type=='integer' || $columns[$x]->type=='currency')
                                            <input name="filter[col{{$x}}][min]" class="form-control input-sm filter-input type_{{$columns[$x]->type}}" onchange="applyFilter()" value="{{\Request::input('filter.col'.$x.'.min')}}" placeholder="min" style="min-width:40px;">
                                             <input name="filter[col{{$x}}][max]" class="form-control input-sm filter-input type_{{$columns[$x]->type}}" onchange="applyFilter()" value="{{\Request::input('filter.col'.$x.'.max')}}" placeholder="max" style="min-width:40px;">
                                        @elseif($columns[$x]->type!='notes')
                                            <select name="filter[col{{$x}}]" class="form-control input-sm filter-input" onchange="applyFilter()" style="height:24px; min-width: 110px;">
                                                <option value="0">--no filter--</option>
                                                @if(count($columns[$x]->distincts))
                                                <optgroup label="filters"> 
                                                @foreach($columns[$x]->distincts as $key => $value)
                                                <option value="{{$key}}" {{\Request::input('filter.col'.$x) == $key && $key != "" ? 'selected' : ''}}>{{$value}}</option> 
                                                @endforeach
                                                </optgroup>
                                                @endif
                                            </select>
                                        @endif
                                    @endif
                                </th>
                                <?php
                                if(in_array($columns[$x]->type, ['numeric','integer','currency']))
                                    $counts[$x]=null;
                                else  
                                    $counts[$x]=[];
                                ?>
                            @endfor
                        </tr>
                    </thead>
                    <tbody>
                        @for($y=1; $y<=($spreadsheet->content ? $spreadsheet->content->count()+1 : 1); $y++)
                            <?php
                            $content = null;
                            if(isset($spreadsheet->content[($y-1)]))
                                $content = $spreadsheet->content[($y-1)];
                            ?>
                            <tr id="tr{{$y}}">
                                <th class="nostretch no-stretch bg-info row{{$y}} col0 {{$content && $content->validated==0 ? 'bg-danger' : ''}}" id="th{{$y}}" {!! $content ? 'title="Entered by '.$content->user->displayname().' on '.date('Y-m-d @ h:ia',strtotime($content['created_at'])).'"' : '' !!} >{{$y}}</th>
                                @for($x=1; $x<=$max; $x++)
                                    <td style="padding:0;" class="row{{$y}} col{{$x}} {{$content && $content->validated==0 ? 'bg-danger' : ''}}">
                                        @if($columns[$x]->type=='currency')
                                        <div class="input-group"><div class="input-group-addon"><i class="fa fa-usd" aria-hidden="true"></i></div>{!! \App\SpreadsheetColumn::sheetCell($columns[$x],$content,$x,$y) !!}</div>
                                        @elseif($columns[$x]->type=='date')
                                        <div class="input-group"><div class="input-group-addon "><i class="fa fa-calendar" aria-hidden="true"></i></div>{!! \App\SpreadsheetColumn::sheetCell($columns[$x],$content,$x,$y) !!}</div>
                                        @else
                                        {!! \App\SpreadsheetColumn::sheetCell($columns[$x],$content,$x,$y) !!}
                                        @endif
                                    </td>
                                    <?php
                                    if(isset($content['col'.$x])){
                                        if(in_array($columns[$x]->type, ['integer'])){
                                            if(!$counts[$x])
                                                $counts[$x] = (int)$content['col'.$x];
                                            else 
                                                $counts[$x] += (int)$content['col'.$x];
                                        }
                                        elseif(in_array($columns[$x]->type, ['currency','numeric'])){
                                            if(!$counts[$x])
                                                $counts[$x] = $content['col'.$x];
                                            else 
                                                $counts[$x] += $content['col'.$x];
                                        }
                                        elseif($content['col'.$x] != ""){
                                            if(isset($counts[$x][$content['col'.$x]]))
                                                $counts[$x][$content['col'.$x]]++;
                                            else
                                                $counts[$x][$content['col'.$x]]=1;
                                        }
                                    }
                                    ?>
                                @endfor
                                <td>
                                @if($y == ($spreadsheet->content ? $spreadsheet->content->count()+1 : 1))
                                <a href="javascript:newRow({{$y}})" class="text-success new-row hidden" id="newRow{{$y}}"><i class="fa fa-plus" aria-hidden="true"></i></a>
                                <a href="javascript:delRow({{$y}})" class="text-danger del-row hidden" id="delRow{{$y}}"><i class="fa fa-ban" aria-hidden="true"></i></a>
                                @else
                                <a href="javascript:delRow({{$y}})" class="text-danger del-row" id="delRow{{$y}}"><i class="fa fa-ban" aria-hidden="true"></i></a>
                                @endif
                                </td>
                            </tr>
                        @endfor
                   </tbody>
                    <tfoot>
                        <tr>
                            <td class="bg-info"></td>
                            @for($x=1; $x<=$max; $x++)
                              <td class="small bg-warning col{{$x}}" id="totals{{$x}}">
                                @if(in_array($columns[$x]->type, ['numeric','integer']))
                                    @if($counts[$x])
                                        {{ $counts[$x] }}
                                    @endif
                                @elseif($columns[$x]->type == 'currency')
                                    @if($counts[$x])
                                        ${{ number_format($counts[$x],2) }}
                                    @endif
                                @elseif($columns[$x]->type != 'notes')
                                    @foreach($counts[$x] as $key=>$value)
                                    ({{$value}}) {{$key}}<br/>
                                    @endforeach
                                @endif
                              </td>
                            @endfor
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>
<table id="newrow" style="display:none;">
    <tr id="row||row||">
        <th class="nostretch no-stretch bg-info row||row|| col0" id="th||row||" {!! $content ? 'title="Entered by '.\Auth::user()->displayname().' on '.date('Y-m-d @ h:ia').'"' : '' !!} >||row||</th>
        @for($x=1; $x<=$max; $x++)
        <td style="padding:0;" class="row||row|| col{{$x}}">
            @if($columns[$x]->type=='currency')
            <div class="input-group"><div class="input-group-addon"><i class="fa fa-usd" aria-hidden="true"></i></div>{!! \App\SpreadsheetColumn::sheetCell($columns[$x],$content,$x,'||row||') !!}</div>
            @elseif($columns[$x]->type=='date')
            <div class="input-group"><div class="input-group-addon "><i class="fa fa-calendar" aria-hidden="true"></i></div>{!! \App\SpreadsheetColumn::sheetCell($columns[$x],$content,$x,'||row||') !!}</div>
            @else
            {!! \App\SpreadsheetColumn::sheetCell($columns[$x],$content,$x,'||row||') !!}
            @endif
        </td>
        <?php
        if(isset($content['col'.$x])){
            if(isset($counts[$x][$content['col'.$x]])){
                $counts[$x][$content['col'.$x]]++;
            }
            else
                $counts[$x][$content['col'.$x]]=1;
        }
        ?>
        @endfor
        <td><a href="javascript:delRow(||row||)" class="text-danger hidden del-row" id="delRow||row||"><i class="fa fa-ban" aria-hidden="true"></i></a><a href="javascript:newRow(||row||)" class="text-success new-row hidden" id="newRow||row||"><i class="fa fa-plus" aria-hidden="true"></i></a></td>
    </tr>
</table>
@endsection

@section('styles')
<link href="/css/datepicker.css" rel="stylesheet" >
<style>
#spreadsheet{background:transparent;}
#spreadsheet thead th{font-size:12px; line-height: 16px;}
#spreadsheet tbody td{ font-size:10px !important;}
#spreadsheet tbody td .input-group-addon{border:none !important; border-radius:0 !important; color:#777 !important; padding:6px 6px; background: transparent; font-size:12px !important;}
#spreadsheet tbody th{padding:4px !important; font-size:11px;}
#spreadsheet tfoot td{font-family: Arial, sans-serif;}
.sheet_cell{font-family:arial; margin:0; padding:0 3px; width: 100%; min-width: 125px; height: 26px; border:1px solid transparent; outline: none; background:transparent; font-size:12px !important;}
.sheet_cell:focus{border:1px solid #999;}
.filter-input{font-weight: 100; width:48%; min-width:55px; display:inline-block; font-size: 10px; padding:2px 4px; height:24px; margin-top:2px;}
</style>
@append

@section('scripts')
<script src="/js/datepicker.js"></script>
<script type="text/javascript">
    function checkConditionals(rowNum){
        <?php
            foreach($conditionals as $col=>$conditional){
                $col=trim(str_replace('col', '', $col));
                $ifs = explode(',',$conditional['if']);
                echo 'if(';
                $first=true;
                foreach($ifs as $if){
                    $if=trim($if);
                    $check=explode('=',$if);
                    if(!$first)
                        echo ' && ';
                    echo '$("#content_"+rowNum+"_'.array_search(strtoupper($check[0]),\app\SpreadsheetColumn::$columnLetters).'").val()=="'.addslashes($check[1]).'"';
                    $first=false;
                }
                echo '){ $("#content_"+rowNum+"_'.$col.'").val("'.$conditional['then'].'"); } else{ $("#content_"+rowNum+"_'.$col.'").val("'.$conditional['else'].'"); }'."\n";
            }
        ?>
    }

    var sheetupdated = false;
    var lastrow = "{{($spreadsheet->content ? $spreadsheet->content->count()+1 : 1)}}";
    var lastCellValue = "";
    $(document).ready(function(){
        $('#spreadsheetContainer').height($(window).height()-150);
        $(window).on('resize',function(){
            $('#spreadsheetContainer').height($(window).height()-150);
        })
        function bindcells(){
            $('.sheet_cell').not($('.bound')).on('focus',function(){
                lastCellValue = $(this).val();
                $(this).select();
            });
            $('.sheet_cell').not($('.bound')).on('change',function(){
                var cell = this;
                var val = $(cell).val();
                var row = $(cell).data('row-id');
                var col = $(cell).data('col-id');
                var type = $(cell).data('type');
                $('#th'+row).removeClass('bg-info').removeClass('bg-danger').addClass('bg-success');
                $('#savebutton').removeClass('hidden');
                $('#exportbutton').addClass('hidden');

                sheetupdated=true;
                if(row==lastrow){
                    $('#spreadsheet .new-row').addClass('hidden');
                    $('#spreadsheet .del-row').removeClass('hidden');
                    lastrow++;
                    var content = $('#newrow tr').html();
                    content = content.replace(/\|\|row\|\|/g,lastrow);
                    $('#spreadsheet tbody').append('<tr id="tr'+lastrow+'">'+content+'</tr>');
                    bindcells();
                }
                $(cell).attr('bound','true');
                if($(cell).hasClass('type_currency')){
                    val = val.replace(/\$/g,'');
                    var parsedval = (parseFloat(val) ? parseFloat(val) : 0 ).toFixed(2);
                    $(cell).val(parsedval);
                }
                var totals = {};
                $('tbody td.col'+col+' .sheet_cell').each(function(){
                    var cell = this;
                    var val = $(cell).val();
                    if(val != ""){
                        if(type=='currency' || type=='numeric'){
                            if(totals[1])
                                totals[1]+=parseFloat(val);
                            else
                                totals[1]=parseFloat(val);
                        }
                        else if(type != 'notes'){
                            if(totals[val])
                                totals[val]++;
                            else
                                totals[val]=1;
                        }
                    }
                });
            
                $('#totals'+col).html('');
                if(type=='currency' || type=='numeric'){
                    if(type=='currency')
                        $('#totals'+col).append("$"+totals[1].toFixed(2).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                    else
                        $('#totals'+col).append(totals[1].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ","));
                }
                else if(type != 'notes'){
                    $.each(totals,function(key,value){
                        $('#totals'+col).append('('+value+') '+key+'<br/>');
                    });
                }
                checkConditionals(row);

            });            
            $('.sheet_cell').not($('.bound')).attr('bound','true');
            $('.type_date').not($('.bound')).datepicker({format: 'yyyy-mm-dd'});
        }
        bindcells();
        $(window).bind('beforeunload', function(){ 
            if(sheetupdated)
                return 'Do you want to save your work before leaving?';
            else
                return undefined;  
        });

        $('.sheet_cell.type_currency').each(function(){
            var val = $(this).val();
            var row = $(this).data('row-id');
            var col = $(this).data('col-id');
            if(val != ""){
                var parsedval = (parseFloat(val) ? parseFloat(val) : 0 ).toFixed(2);
                $(this).val(parsedval);
            }
        })

    });
    function applyFilter(){
        var params = "";
        $('.filter-input').each(function(){
            if($(this).val() != "" && $(this).val() != "0"){
                if(params != "")
                    params=params+'&';
                params=params+$(this).attr('name')+"="+$(this).val();
            }
        });
        params=params+"&sort_col="+$('#sort_col').val();
        location.href="?"+params;
    }
    function delRow(id){
        $('#tr'+id).remove();
        $('#savebutton').removeClass('hidden');
        $('#exportbutton').addClass('hidden');
    }
</script>
@append