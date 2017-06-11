<?php $content = ${$report->template->file}; ?>
<?php $tdwidth = round(100/(count($content['all']['all']['cols'])+4)); ?>
<html>
<h1 colspan="{{count($content['columns'])+1}}">Total Amounts Report</h1>
<table>
			<tr>
			<td colspan="{{count($content['columns'])+1}}"><h1>All Together</h1></td>
			</tr>
			<tr>
			<td class="bg-primary" style="background-color:#3399cc;" colspan="{{count($content['columns'])+1}}"></td>
			</tr>

			@if(!empty($content['all']['months']))
				<tr>
					<th valign="middle" style="background-color:#d9edf7; height:20;" class="bg-info">Month</th>
					@foreach($content['columns'] as $column)
						<th valign="middle" style="background-color:#d9edf7; height:20;" class="bg-info">{{$column['label']}}</th>
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
					<th valign="middle" style="background-color:#d9edf7; height:20;" class="bg-info">Week</th>
					@foreach($content['columns'] as $column)
						<th valign="middle" style="background-color:#d9edf7; height:20;" class="bg-info">{{$column['label']}}</th>
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
					<th valign="middle" style="background-color:#d9edf7; height:20;" class="bg-info" style="width:25;"></th>
				@endif
				@foreach($content['columns'] as $column)
					<th valign="middle" style="background-color:#d9edf7; height:20;" class="bg-info" style="width:15;">{{$column['label']}}</th>
				@endforeach
			</tr>
			<tr>
				@if(!empty($content['all']['weeks']) || !empty($content['all']['months']))
					<td valign="middle" style="background-color:#dff0d8; height:20;" class="bg-success"><strong>Totals</strong></td>
				@endif
				@foreach($content['all']['all']['cols'] as $colindex=>$row)
					<td valign="middle" style="background-color:#dff0d8; height:20;" align="right" class="bg-success">
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

@foreach($content['sections'] as $sections)
	<tr><th></th></tr>
	<tr><td colspan="{{count($content['columns'])+1}}"><h1>By {{$sections['label']}}</h1></td></tr>
	@foreach($sections['data'] as $name=>$section)
		<tr><th></th></tr>
		<tr><td style="background-color:#3399cc;" class="bg-primary" colspan="{{count($content['columns'])+1}}"><h2>{{ ucwords($name) }}</h2></td></tr>
		@if(!empty($section['months']))
			<tr>
				<th valign="middle" style="background-color:#d9edf7; height:20;" class="bg-info">Month</th>
				@foreach($content['columns'] as $column)
					<th valign="middle" style="background-color:#d9edf7; height:20;" class="bg-info">{{$column['label']}}</th>
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
				<th valign="middle" style="background-color:#d9edf7; height:20;" class="bg-info">Week</th>
				@foreach($content['columns'] as $column)
					<th valign="middle" style="background-color:#d9edf7; height:20;" class="bg-info">{{$column['label']}}</th>
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
				<th valign="middle" style="background-color:#d9edf7; height:20;" class="bg-info"></th>
			@endif
			@foreach($content['columns'] as $column)
				<th valign="middle" style="background-color:#d9edf7; height:20;" class="bg-info">{{$column['label']}}</th>
			@endforeach
		</tr>
		<tr>
			@if(!empty($section['weeks']) || !empty($section['months']))
				<td valign="middle" style="background-color:#dff0d8; height:20;" class="bg-success"><strong>Totals</strong></td>
			@endif
			@foreach($section['all']['cols'] as $colindex=>$row)
				<td valign="middle" style="background-color:#dff0d8; height:20;" align="right" class="bg-success">
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
	@endforeach
@endforeach
</table>
</html>
