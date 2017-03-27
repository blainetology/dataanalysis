<?php $content = ${$report->template->file}; ?>
<div class="container">
<br/>
<h3>Appointments (set &amp; Kept)</h3>

<table class="table table-striped table-bordered">
	<thead>
		<tr><th>All Leads</th><th>Num Set</th><th>Num Kept</th><th>% of All that Set</th><th>% of Set that Kept</th></tr>
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

<div style="width:100%; background: #F00; font-size: 10px;">
	<div style="padding:2px 5px;">{{ $content['all'] }} Leads</div>
	<div style="width:{{ round($content['set']/$content['all']*100) }}%; background: #FF0;">
		<div style="padding:2px 5px;">{{ $content['set'] }} Set</div>
		<div style="width:{{ round( ($content['kept'])/$content['set']*100) }}%; background: #0F0;">
			<div style="padding:2px 5px;">{{ $content['kept'] }} Kept</div>&nbsp;
		</div>
	</div>
</div>

</div>