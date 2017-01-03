@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <p>Client: <strong>{{ $client->business_name }}</strong></p>
            <ul class="nav nav-tabs">
                @foreach($client_spreadsheets as $sheet)
                <li role="presentation" {!! ($spreadsheet->id == $sheet->id) ? 'class="active"' : '' !!}><a href="/admin/spreadsheets/{{ $sheet->id }}">{{$sheet->name}}</a></li>
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
                <table id="spreadsheet" class="table table-bordered table-striped table-condensed">
                    <thead>
                        <tr>
                            <td></td>
                            @for($x=1; $x<=$max; $x++)
                              <th class="bg-info">
                                @if(\Auth::user()->isEditor())
                                <small><em class="text-info" style="font-weight:100;">Column {{$letters[$x]}}</em></small><br/>
                                @endif
                                {{ isset($columns[$x]) ? $columns[$x]['label'] : '' }}
                                @if(\Auth::user()->isEditor())
                                <br/>
                                <select name="filter[col{{$x}}]" class="form-control input-sm filter-input" onchange="applyFilter()">
                                    <option value="0">--no filter--</option>
                                    @if(count($columns[$x]->distincts))
                                    <optgroup label="filters"> 
                                    @foreach($columns[$x]->distincts as $key => $value)
                                    <option value="{{$key}}" {{\Request::input('filter.col'.$x) == $key ? 'selected' : ''}}>{{$value}}</option> 
                                    @endforeach
                                    </optgroup>
                                    @endif
                                </select>
                                @endif
                              </th>
                              <?php
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
                                        @if(!empty($columns[$x]->validation['in']))
                                        <select class="sheet_cell" id="content_{{$y}}_{{$x}}" data-row-id="{{$y}}" data-col-id="{{$x}}" value="{{ $content ? $content['col'.$x] : ''}}" name="content[{{$y}}][col{{$x}}]">
                                            <option value=""></option> 
                                            @foreach(explode(',',$columns[$x]->validation['in']) as $option)
                                            <option value="{{trim($option)}}" {{ $content && $content['col'.$x] == trim($option) ? 'selected' : ''}}>{{trim($option)}}</option>
                                            @endforeach
                                        </select>
                                        @else
                                        <input class="sheet_cell" type="text" id="content_{{$y}}_{{$x}}" data-row-id="{{$y}}" data-col-id="{{$x}}" value="{{ $content ? $content['col'.$x] : ''}}" name="content[{{$y}}][col{{$x}}]">
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
                        @endfor
                   </tbody>
                    <tfoot>
                        <tr class="bg-warning">
                            <td></td>
                            @for($x=1; $x<=$max; $x++)
                              <td>
                                @foreach($counts[$x] as $key=>$value)
                                ({{$value}}) {{$key}}<br/>
                                @endforeach
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
@endsection

@section('styles')
<style>
#spreadsheet{background:#FFF;}
#spreadsheet tbody td{}
</style>
@append

@section('scripts')
<script type="text/javascript">
    var sheetupdated = false;
    $(document).ready(function(){
        $('.sheet_cell').on('change',function(){
            var cell = this;
            var row = $(cell).data('row-id');
            var col = $(cell).data('col-id');
            $('#th'+row).removeClass('bg-info').addClass('bg-danger');
            $('#action_bar').removeClass('bg-warning').addClass('bg-success');
            $('#savebutton').removeClass('hidden');
            $('#exportbutton').addClass('hidden');
            sheetupdated=true;
        });
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