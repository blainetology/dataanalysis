	<h2 height="24" colspan="{{count($columns)+1}}">{{$report_name}} Report</h2>
	<h3 height="24" colspan="{{count($columns)+1}}">{{$header}}</h3>

		<table border="thin">
			<tr><td valign="middle" style="height:22; font-size:14px; font-weight:900;" colspan="{{count($columns)+1}}">{{ ucwords($subheader) }}</td></tr>
			@if(!empty($data['months']))
				<tr>
					<td valign="middle" style="background-color:#d9edf7; height:16; font-weight:900;" class="bg-info">Month</td>
					@foreach($columns as $column)
						<td valign="middle" style="background-color:#d9edf7; height:16; font-weight:900;" class="bg-info">{{$column['label']}}</td>
					@endforeach
				</tr>
				@foreach($data['months'] as $month=>$total)
					<tr>
						<td valign="middle" height="12">{{ $month }}</td>
						@foreach($total['cols'] as $colindex=>$row)
							<td valign="middle" align="right">
								@if($columns[$colindex]['type'] == 'percent')
								{{ $row ? round($row*100,1).'%' : '---' }}
								@elseif($columns[$colindex]['type'] == 'dollar')
								${{ number_format((float)$row,2) }}
								@elseif($columns[$colindex]['type'] == 'integer')
								{{ (int)$row }}
								@elseif($columns[$colindex]['type'] == 'numeric')
								{{ number_format((float)$row,2) }}
								@else
								{{ $row ? $row : '---' }}
								@endif
							</td>
						@endforeach
					</tr>
				@endforeach
			@endif
			@if(!empty($data['weeks']))
				<tr>
					<td valign="middle" style="background-color:#d9edf7; height:16; font-weight:900;" class="bg-info">Week</td>
					@foreach($columns as $column)
						<td valign="middle" style="background-color:#d9edf7; height:16; font-weight:900;" class="bg-info">{{$column['label']}}</td>
					@endforeach
				</tr>
				@foreach($data['weeks'] as $week)
					<tr>
						<td valign="middle" height="12">{{ $week['start'] }}-{{ $week['end'] }}</td>
						@foreach($week['cols'] as $colindex=>$row)
							<td valign="middle" align="right">
								@if($columns[$colindex]['type'] == 'percent')
								{{ $row ? round($row*100,1).'%' : '---' }}
								@elseif($columns[$colindex]['type'] == 'dollar')
								${{ number_format((float)$row,2) }}
								@elseif($columns[$colindex]['type'] == 'integer')
								{{ (int)$row }}
								@elseif($columns[$colindex]['type'] == 'numeric')
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
				@if(!empty($data['weeks']) || !empty($data['months']))
					<td valign="middle" style="background-color:#d9edf7; height:16; font-weight:900;" class="bg-info"></td>
				@endif
				@foreach($columns as $column)
					<td valign="middle" style="background-color:#d9edf7; height:16; font-weight:900;" class="bg-info">{{$column['label']}}</td>
				@endforeach
			</tr>
			<tr>
				@if(!empty($data['weeks']) || !empty($data['months']))
					<td valign="middle" style="background-color:#dff0d8; height:16; font-weight:900;" class="bg-success"><strong>Totals</strong></td>
				@endif
				@foreach($data['all']['cols'] as $colindex=>$row)
					<td valign="middle" style="background-color:#dff0d8; height:16; font-weight:900;" align="right" class="bg-success">
						<strong>
							@if($columns[$colindex]['type'] == 'percent')
							{{ $row ? round($row*100,1).'%' : '---' }}
							@elseif($columns[$colindex]['type'] == 'dollar')
							${{ number_format((float)$row,2) }}
							@elseif($columns[$colindex]['type'] == 'integer')
							{{ (int)$row }}
							@elseif($columns[$colindex]['type'] == 'numeric')
							{{ number_format((float)$row,2) }}
							@else
							{{ $row ? $row : '---' }}
							@endif
						</strong>
					</td>
				@endforeach
			</tr>
		</table>