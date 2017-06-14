<?php $content = ${$report->template->file}; ?>
<?php $tdwidth = round(100/(count($content['all']['all']['cols'])+4)); ?>
<html>
	<h1 height="32" colspan="{{count($content['columns'])+1}}">Total Amounts Report</h1>
	<h2 height="24" colspan="{{count($content['columns'])+1}}">All Together</h2>

	<table>
		<tr>
			<td class="bg-primary" style="background-color:#3399cc;" colspan="{{count($content['columns'])+1}}"></td>
		</tr>

		@if(!empty($content['all']['months']))
			<tr>
				<td valign="middle" style="background-color:#d9edf7; height:16; font-weight:900;" class="bg-info">Month</td>
				@foreach($content['columns'] as $column)
					<td valign="middle" style="background-color:#d9edf7; height:16; font-weight:900;" class="bg-info">{{$column['label']}}</td>
				@endforeach
			</tr>
			@foreach($content['all']['months'] as $month=>$total)
				<tr>
					<td valign="middle" height="12">{{ $month }}</td>
					@foreach($total['cols'] as $colindex=>$row)
						<td valign="middle" align="right">
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
				<td valign="middle" style="background-color:#d9edf7; height:16; font-weight:900;" class="bg-info">Week</td>
				@foreach($content['columns'] as $column)
					<td valign="middle" style="background-color:#d9edf7; height:16; font-weight:900;" class="bg-info">{{$column['label']}}</td>
				@endforeach
			</tr>
			@foreach($content['all']['weeks'] as $week)
				<tr>
					<td valign="middle" height="12">{{ $week['start'] }}-{{ $week['end'] }}</td>
					@foreach($week['cols'] as $colindex=>$row)
						<td valign="middle" align="right">
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
				<td valign="middle" style="background-color:#d9edf7; height:16; width:25; font-weight:900;"></td>
			@endif
			@foreach($content['columns'] as $column)
				<td valign="middle" style="background-color:#d9edf7; height:16; width:15; font-weight:900;">{{$column['label']}}</td>
			@endforeach
		</tr>
		<tr>
			@if(!empty($content['all']['weeks']) || !empty($content['all']['months']))
				<td valign="middle" style="background-color:#dff0d8; height:16; font-weight:900;" class="bg-success"><strong>Totals</strong></td>
			@endif
			@foreach($content['all']['all']['cols'] as $colindex=>$row)
				<td valign="middle" style="background-color:#dff0d8; height:16; font-weight:900;" align="right" class="bg-success">
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
	</table>
@foreach($content['sections'] as $sections)
	<h2 height="24" colspan="{{count($content['columns'])+1}}">By {{$sections['label']}}</h2>
	@foreach($sections['data'] as $name=>$section)
		<table border="thin">
			<tr><td valign="middle" style="background-color:#3399cc; height:22; font-size:16px; font-weight:900;" colspan="{{count($content['columns'])+1}}">{{ ucwords($name) }}</td></tr>
			@if(!empty($section['months']))
				<tr>
					<td valign="middle" style="background-color:#d9edf7; height:16; font-weight:900;" class="bg-info">Month</td>
					@foreach($content['columns'] as $column)
						<td valign="middle" style="background-color:#d9edf7; height:16; font-weight:900;" class="bg-info">{{$column['label']}}</td>
					@endforeach
				</tr>
				@foreach($section['months'] as $month=>$total)
					<tr>
						<td valign="middle" height="12">{{ $month }}</td>
						@foreach($total['cols'] as $colindex=>$row)
							<td valign="middle" align="right">
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
					<td valign="middle" style="background-color:#d9edf7; height:16; font-weight:900;" class="bg-info">Week</td>
					@foreach($content['columns'] as $column)
						<td valign="middle" style="background-color:#d9edf7; height:16; font-weight:900;" class="bg-info">{{$column['label']}}</td>
					@endforeach
				</tr>
				@foreach($section['weeks'] as $week)
					<tr>
						<td valign="middle" height="12">{{ $week['start'] }}-{{ $week['end'] }}</td>
						@foreach($week['cols'] as $colindex=>$row)
							<td valign="middle" align="right">
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
					<td valign="middle" style="background-color:#d9edf7; height:16; font-weight:900;" class="bg-info"></td>
				@endif
				@foreach($content['columns'] as $column)
					<td valign="middle" style="background-color:#d9edf7; height:16; font-weight:900;" class="bg-info">{{$column['label']}}</td>
				@endforeach
			</tr>
			<tr>
				@if(!empty($section['weeks']) || !empty($section['months']))
					<td valign="middle" style="background-color:#dff0d8; height:16; font-weight:900;" class="bg-success"><strong>Totals</strong></td>
				@endif
				@foreach($section['all']['cols'] as $colindex=>$row)
					<td valign="middle" style="background-color:#dff0d8; height:16; font-weight:900;" align="right" class="bg-success">
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
		</table>
	@endforeach
@endforeach

</html>
