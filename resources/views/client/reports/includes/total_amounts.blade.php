<?php $content = ${$report->template->file}; ?>
<?php $tdwidth = round(100/(count($content['all']['all']['cols'])+4)); ?>

@if(!empty($content['sections']))
	<span class="pull-right hidden-sm hidden-xs">
	Jump To 
	@foreach($content['sections'] as $sections)
		| <a href="#section_{{ str_slug($sections['label'],'_') }}">By {{ $sections['label'] }}</a>
	@endforeach
	</span>
@endif

<h3 style="font-size:1.6em;">All Together</h3>
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">&nbsp;</h3>
	</div>
	<table class="table table-striped table-bordered table-condensed report-table small">
		<tbody>
			@if(!empty($content['all']['months']))
				<tr>
					<th class="bg-info">Month</th>
					@foreach($content['columns'] as $column)
						<th class="bg-info">{{$column['label']}}</th>
					@endforeach
				</tr>
				@foreach($content['all']['months'] as $month=>$total)
					<tr>
						<td>{{ $month }}</td>
						@foreach($total['cols'] as $colindex=>$row)
							<td align="right">
								@if($content['columns'][$colindex]['type'] == 'percent')
								{{ $row ? round($row*100,1).'%' : '---' }}
								@elseif($content['columns'][$colindex]['type'] == 'dollar')
								${{ number_format((float)$row,2) }}
								@elseif($content['columns'][$colindex]['type'] == 'integer')
								{{ (int)$row }}
								@elseif($content['columns'][$colindex]['type'] == 'numeric')
								{{ number_format((float)$row,2) }}
								@else
								{{ $row ? $row : '---' }}
								@endif
							</td>
						@endforeach
					</tr>
				@endforeach
			@endif
			@if(!empty($content['all']['weeks']))
				<tr>
					<th class="bg-info">Week</th>
					@foreach($content['columns'] as $column)
						<th class="bg-info">{{$column['label']}}</th>
					@endforeach
				</tr>
				@foreach($content['all']['weeks'] as $week)
					<tr>
						<td>{{ $week['start'] }}-{{ $week['end'] }}</td>
						@foreach($week['cols'] as $colindex=>$row)
							<td align="right">
								@if($content['columns'][$colindex]['type'] == 'percent')
								{{ $row ? round($row*100,1).'%' : '---' }}
								@elseif($content['columns'][$colindex]['type'] == 'dollar')
								${{ number_format((float)$row,2) }}
								@elseif($content['columns'][$colindex]['type'] == 'integer')
								{{ (int)$row }}
								@elseif($content['columns'][$colindex]['type'] == 'numeric')
								{{ number_format((float)$row,2) }}
								@else
								{{ $row ? $row : '---' }}
								@endif
							</td>
						@endforeach
					</tr>
				@endforeach
			@endif
			<tr>
				@if(!empty($content['all']['weeks']) || !empty($content['all']['months']))
					<th class="bg-info"></th>
				@endif
				@foreach($content['columns'] as $column)
					<th class="bg-info">{{$column['label']}}</th>
				@endforeach
			</tr>
			<tr>
				@if(!empty($content['all']['weeks']) || !empty($content['all']['months']))
					<td width="{{ round($tdwidth*1.25) }}%" class="bg-success"><strong>Totals</strong></td>
				@endif
				@foreach($content['all']['all']['cols'] as $colindex=>$row)
					<td align="right" width="{{ $tdwidth }}%" class="bg-success">
					<strong>
						@if($content['columns'][$colindex]['type'] == 'percent')
						{{ $row ? round($row*100,1).'%' : '---' }}
						@elseif($content['columns'][$colindex]['type'] == 'dollar')
						${{ number_format((float)$row,2) }}
						@elseif($content['columns'][$colindex]['type'] == 'integer')
						{{ (int)$row }}
						@elseif($content['columns'][$colindex]['type'] == 'numeric')
						{{ number_format((float)$row,2) }}
						@else
						{{ $row ? $row : '---' }}
						@endif
					</strong>
					</td>
				@endforeach
			</tr>
		</tbody>
	</table>
</div>

@foreach($content['sections'] as $sections)
	<a name="section_{{ str_slug($sections['label'],'_') }}"></a>
	<hr/>
	<h3 style="font-size:1.6em;">By {{$sections['label']}}</h3>
	@foreach($sections['data'] as $name=>$section)
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title"><strong>{{ ucwords($name) }}</strong></h3>
		</div>
		<table class="table table-striped table-bordered table-condensed report-table small">
			<tbody>
				@if(!empty($section['months']))
					<tr>
						<th class="bg-info">Month</th>
						@foreach($content['columns'] as $column)
							<th class="bg-info">{{$column['label']}}</th>
						@endforeach
					</tr>
					@foreach($section['months'] as $month=>$total)
						<tr>
							<td>{{ $month }}</td>
							@foreach($total['cols'] as $colindex=>$row)
								<td align="right">
									@if($content['columns'][$colindex]['type'] == 'percent')
									{{ $row ? round($row*100,1).'%' : '---' }}
									@elseif($content['columns'][$colindex]['type'] == 'dollar')
									${{ number_format((float)$row,2) }}
									@elseif($content['columns'][$colindex]['type'] == 'integer')
									{{ (int)$row }}
									@elseif($content['columns'][$colindex]['type'] == 'numeric')
									{{ number_format((float)$row,2) }}
									@else
									{{ $row ? $row : '---' }}
									@endif
								</td>
							@endforeach
						</tr>
					@endforeach
				@endif
				@if(!empty($section['weeks']))
					<tr>
						<th class="bg-info">Week</th>
						@foreach($content['columns'] as $column)
							<th class="bg-info">{{$column['label']}}</th>
						@endforeach
					</tr>
					@foreach($section['weeks'] as $week)
						<tr>
							<td>{{ $week['start'] }}-{{ $week['end'] }}</td>
							@foreach($week['cols'] as $colindex=>$row)
								<td align="right">
									@if($content['columns'][$colindex]['type'] == 'percent')
									{{ $row ? round($row*100,1).'%' : '---' }}
									@elseif($content['columns'][$colindex]['type'] == 'dollar')
									${{ number_format((float)$row,2) }}
									@elseif($content['columns'][$colindex]['type'] == 'integer')
									{{ (int)$row }}
									@elseif($content['columns'][$colindex]['type'] == 'numeric')
									{{ number_format((float)$row,2) }}
									@else
									{{ $row ? $row : '---' }}
									@endif
								</td>
							@endforeach
						</tr>
					@endforeach
				@endif
				<tr>
					@if(!empty($section['weeks']) || !empty($section['months']))
						<th class="bg-info"></th>
					@endif
					@foreach($content['columns'] as $column)
						<th class="bg-info">{{$column['label']}}</th>
					@endforeach
				</tr>
				<tr>
					@if(!empty($section['weeks']) || !empty($section['months']))
						<td width="{{ round($tdwidth*1.25) }}%" class="bg-success"><strong>Totals</strong></td>
					@endif
					@foreach($section['all']['cols'] as $colindex=>$row)
						<td align="right" width="{{ $tdwidth }}%" class="bg-success">
							<strong>
								@if($content['columns'][$colindex]['type'] == 'percent')
								{{ $row ? round($row*100,1).'%' : '---' }}
								@elseif($content['columns'][$colindex]['type'] == 'dollar')
								${{ number_format((float)$row,2) }}
								@elseif($content['columns'][$colindex]['type'] == 'integer')
								{{ (int)$row }}
								@elseif($content['columns'][$colindex]['type'] == 'numeric')
								{{ number_format((float)$row,2) }}
								@else
								{{ $row ? $row : '---' }}
								@endif
							</strong>
						</td>
					@endforeach
				</tr>
			</tbody>
		</table>
	</div>
	@endforeach
@endforeach

@section('styles')
<style>
    #map{height: 400px;}
    .report-table th{text-align: center !important;}
</style>
@append

@section('scripts')

@append