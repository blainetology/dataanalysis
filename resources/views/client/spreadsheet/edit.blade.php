@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <p>Client: <strong>{{ $client->business_name }}</strong></p>
            <ul class="nav nav-tabs">
                @foreach($client_spreadsheets as $sheet)
                <li role="presentation" {!! ($spreadsheet->id == $sheet->id) ? 'class="active"' : '' !!}><a href="/client/spreadsheets/{{ $sheet->id }}/edit">{{$sheet->name}}</a></li>
                @endforeach
            </ul>
            {{ Form::open(['route'=>['clientspreadsheets.update',$spreadsheet->id],'method'=>'PUT','onsubmit'=>'sheetupdated=false']) }}
            <div class="row">
                <div class="col-lg-12">
                    <div class="" id="action_bar" style="padding:10px;">
                        <a href="/client/spreadsheets/{{$spreadsheet->id}}/export?{{$_SERVER['QUERY_STRING']}}" class="btn btn-info btn-sm" id="exportbutton">export data</a>
                        <a href="/client/spreadsheets/{{$spreadsheet->id}}/edit" class="btn btn-warning btn-sm" id="clearfilters">clear filters</a>
                        {{ Form::submit('save changes',['class'=>'btn btn-success btn-sm hidden','id'=>'savebutton']) }}
                    </div>
                </div>
            </div>
            <div style="overflow:auto; width:100%;">
                <table id="spreadsheet" class="table table-bordered table-striped table-condensed" style="margin-bottom:5px;">
                    <thead>
                        <tr>
                            <td></td>
                            @for($x=1; $x<=$max; $x++)
                                <th class="bg-info no-stretch" style="width:{{round(100/$max)}}%; vertical-align:top; padding-top:2px;">
                                    @if(\Auth::user()->isEditor())
                                    <div class="small"><em class="text-info" style="font-weight:100;">Column {{$letters[$x]}}</em></div>
                                    @endif
                                    {{ isset($columns[$x]) ? $columns[$x]['label'] : '' }}
                                    @if(\Auth::user()->isEditor())
                                        <br/>
                                        @if($columns[$x]->type=='date')
                                            <input name="filter[col{{$x}}][start]" class="form-control input-sm filter-input type_date" onchange="applyFilter()" value="{{\Request::input('filter.col'.$x.'.start')}}" placeholder="min date" style="width:48%; min-width:70px; display:inline-block; font-size: 11px; padding:2px 4px; height:24px; margin-top:2px;">
                                            <input name="filter[col{{$x}}][end]" class="form-control input-sm filter-input type_date" onchange="applyFilter()" value="{{\Request::input('filter.col'.$x.'.end')}}" placeholder="max date" style="width:48%; min-width:70px; display:inline-block; font-size: 11px; padding:2px 4px; height:24px; margin-top:2px;">
                                        @elseif($columns[$x]->type!='notes')
                                            <select name="filter[col{{$x}}]" class="form-control input-sm filter-input" onchange="applyFilter()" style="font-size: 11px; min-width: 110px; padding:2px 4px; height:24px; margin-top:2px;">
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
                            <tr>
                                <th class="nostretch no-stretch bg-info" id="th{{$y}}" {!! $content ? 'title="Entered by '.$content->user->displayname().' on '.date('Y-m-d @ h:ia',strtotime($content['created_at'])).'"' : '' !!} >{{$y}}</th>
                                @for($x=1; $x<=$max; $x++)
                                    <td style="padding:0;">
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
                                        if(in_array($columns[$x]->type, ['numeric','integer','currency'])){
                                            if(!$counts[$x])
                                                $counts[$x] = (int)$content['col'.$x];
                                            else 
                                                $counts[$x] += (int)$content['col'.$x];
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
                            </tr>
                        @endfor
                   </tbody>
                    <tfoot>
                        <tr class="bg-warning">
                            <td></td>
                            @for($x=1; $x<=$max; $x++)
                              <td class="small">
                                @if(in_array($columns[$x]->type, ['numeric','integer']))
                                    @if($counts[$x])
                                        {{ $counts[$x] }}
                                    @endif
                                @elseif($columns[$x]->type == 'currency')
                                    @if($counts[$x])
                                        ${{ number_format($counts[$x],2) }}
                                    @endif
                                @else
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
            {{ Form::close() }}
        </div>
    </div>
</div>
<table id="newrow" style="display:none;">
    <tr>
        <th class="nostretch no-stretch bg-info" id="th||row||" {!! $content ? 'title="Entered by '.\Auth::user()->displayname().' on '.date('Y-m-d @ h:ia').'"' : '' !!} >||row||</th>
        @for($x=1; $x<=$max; $x++)
        <td style="padding:0;">
            @if($columns[$x]->type=='currency')
            <div class="input-group"><div class="input-group-addon"><i class="fa fa-usd" aria-hidden="true"></i></div>{!! \App\SpreadsheetColumn::sheetCell($columns[$x],$content,$x,'||row||') !!}</div>
            @elseif($columns[$x]->type=='date')
            <div class="input-group"><div class="input-group-addon "><i class="fa fa-calendar" aria-hidden="true"></i></div>{!! \App\SpreadsheetColumn::sheetCell($columns[$x],$content,$x,'||row||') !!}</div>
            @else
            {!! \App\SpreadsheetColumn::sheetCell($columns[$x],$content,$x,$y) !!}
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
    </tr>
</table>
@endsection

@section('styles')
<link href="/css/datepicker.css" rel="stylesheet" >
<style>
#spreadsheet{background:#FFF;}
#spreadsheet thead th{font-size:13px; line-height: 16px;}
#spreadsheet tbody td{}
#spreadsheet tfoot td{font-family: Arial, sans-serif;}
</style>
@append

@section('scripts')
<script src="/js/datepicker.js"></script>
<script type="text/javascript">
    var sheetupdated = false;
    var lastrow = "{{($spreadsheet->content ? $spreadsheet->content->count()+1 : 1)}}";
    $(document).ready(function(){
        function bindcells(){
            $('.sheet_cell').not($('.bound')).on('change',function(){
                var cell = this;
                var val = $(cell).val();
                var row = $(cell).data('row-id');
                var col = $(cell).data('col-id');
                $('#th'+row).removeClass('bg-info').addClass('bg-danger');
                $('#action_bar').removeClass('bg-warning').addClass('bg-success');
                $('#savebutton').removeClass('hidden');
                $('#exportbutton').addClass('hidden');
                sheetupdated=true;
                if(row==lastrow){
                    console.log('lastrow');
                    lastrow++;
                    var content = $('#newrow tr').html();
                    content = content.replace(/\|\|row\|\|/g,lastrow);
                    console.log(content);
                    $('#spreadsheet tbody').append('<tr>'+content+'</tr>');
                    bindcells();
                }
                $(cell).attr('bound','true');
                if($(cell).hasClass('type_currency'))
                    $(cell).val((val*1).toFixed(2));
            });            
            $('.sheet_cell').not($('.bound')).attr('bound','true');
            $('.type_date').not($('.bound')).datepicker({format: 'yyyy-mm-dd'});
            $('.sheet_cell.type_currency').each(function(){
                var val = $(this).val();
                val = val*1;
                console.log(val);
                if(val!="")
                    $(this).val(val.toFixed(2));
            })
        }
        bindcells();
        $(window).bind('beforeunload', function(){ 
            if(sheetupdated)
                return 'Do you want to save your work before leaving?';
            else
                return undefined;  
        });
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
        console.log(params);
        location.href="?"+params;
    }
</script>
@append