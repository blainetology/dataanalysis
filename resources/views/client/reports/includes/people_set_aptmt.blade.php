<?php $content = ${$report->template->file}; ?>
<br/>
<h3>Appointments (Set &amp; Kept)</h3>

<table class="table table-striped table-bordered table-condensed">
	<thead>
		<tr bgcolor="#FFF"><th>All Leads</th><th>Num Set</th><th>Num Kept</th><th>% of All that Set</th><th>% of Set that Kept</th></tr>
	</thead>
	<tbody>
		<tr>
			<td>{{ $content['all'] }}</td>
			<td>{{ $content['set'] }}</td>
			<td>{{ $content['kept'] }}</td>
			<td>{{ round($content['set']/$content['all']*100) }}%</td>
			<td>{{ round($content['kept']/$content['set']*100) }}%</td>
		</tr>
	</tbody>
</table>

@if(!empty($content['advisors']))
	<br/>
	<h3>Appointments (Set &amp; Kept) By Advisor</h3>
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr bgcolor="#FFF"><th>Advisor</th><th>All Leads</th><th>Num Set</th><th>Num Kept</th><th>% of All that Set</th><th>% of Set that Kept</th></tr>
		</thead>
		<tbody>
			@foreach($content['advisors'] as $name=>$row)
			<tr>
				<td>{{ $name }}</td>
				<td>{{ $row['all'] }}</td>
				<td>{{ $row['set'] }}</td>
				<td>{{ $row['kept'] }}</td>
				<td>{{ round($row['set']/$row['all']*100) }}%</td>
				<td>{{ round($row['kept']/$row['set']*100) }}%</td>
			</tr>
			@endforeach
		</tbody>
	</table>
@endif

<div style="width:100%; background: #F00; font-size: 10px; display:none;">
	<div style="padding:2px 5px;">{{ $content['all'] }} Leads</div>
	<div style="width:{{ round($content['set']/$content['all']*100) }}%; background: #FF0;">
		<div style="padding:2px 5px;">{{ $content['set'] }} Set</div>
		<div style="width:{{ round( ($content['kept'])/$content['set']*100) }}%; background: #0F0;">
			<div style="padding:2px 5px;">{{ $content['kept'] }} Kept</div>&nbsp;
		</div>
	</div>
</div>
