	<h3 height="24" colspan="{{count($columns)+(!empty($data['months']) || !empty($data['weeks']) ? 1 : 0)}}">{{$report_name}} -- {{$report_dates}}</h3>

		<table border="thin">
			<tr><td valign="middle" style="height:20; font-size:14px; font-weight:900; background-color:#CCC; color:#000;" colspan="{{count($columns)+(!empty($data['months']) || !empty($data['weeks']) ? 1 : 0)}}">{{ ucwords($header) . (!empty($subheader) ? ' - '.ucwords($subheader) : '') }}</td></tr>
			@if(!empty($data['months']))
				<tr>
					<td valign="middle" style="background-color:#d9edf7; font-size:11px; font-weight:900;">Month</td>
					@foreach($columns as $column)
						<td valign="middle" style="background-color:#d9edf7; height:17; font-size:11px; font-weight:900;">{{$column['label']}}</td>
					@endforeach
				</tr>
				@foreach($data['months'] as $month=>$total)
					<tr>
						<td valign="middle" height="14">{{ $month }}</td>
						@foreach($total['cols'] as $colindex=>$row)
							<td valign="middle" align="right">{{ $row === null && $row !== '' ? '---' : $row }}</td>
						@endforeach
					</tr>
				@endforeach
			@endif
			@if(!empty($data['weeks']))
				<tr>
					<td valign="middle" style="background-color:#d9edf7; font-size:11px; font-weight:900;">Week</td>
					@foreach($columns as $column)
						<td valign="middle" style="background-color:#d9edf7; height:17; font-size:11px; font-weight:900;">{{$column['label']}}</td>
					@endforeach
				</tr>
				@foreach($data['weeks'] as $week)
					<tr>
						<td valign="middle" height="14">{{ $week['start'] }}-{{ $week['end'] }}</td>
						@foreach($week['cols'] as $colindex=>$row)
							<td valign="middle" align="right">{{ $row === null && $row !== '' ? '---' : $row }}</td>
						@endforeach
					</tr>
				@endforeach
			@endif
			<tr>
				@if(!empty($data['weeks']) || !empty($data['months']))
					<td valign="middle" style="background-color:#d9edf7; font-size:11px; font-weight:900;"></td>
				@endif
				@foreach($columns as $column)
					<td valign="middle" style="background-color:#d9edf7; height:17; font-size:11px; font-weight:900;">{{$column['label']}}</td>
				@endforeach
			</tr>
			<tr>
				@if(!empty($data['weeks']) || !empty($data['months']))
					<td valign="middle" style="background-color:#dff0d8; font-size:11px; font-weight:900;">Totals</td>
				@endif
				@foreach($data['all']['cols'] as $colindex=>$row)
					<td valign="middle" style="background-color:#dff0d8; height:17; font-weight:900;" align="right">{{ $row === null && $row !== '' ? '---' : $row }}</td>
				@endforeach
			</tr>
		</table>