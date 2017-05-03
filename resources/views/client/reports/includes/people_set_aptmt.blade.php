<?php $content = ${$report->template->file}; ?>
<hr/>
<h3>Appointments (Set &amp; Kept)</h3>

<table class="table table-striped table-bordered table-condensed">
	<thead>
		<tr bgcolor="#FFF"><th>All Leads</th><th>Num Set</th><th>Num Kept</th><th><span class="hidden-sm hidden-xs">% of </span>All that Set</th><th><span class="hidden-sm hidden-xs">% of </span>Set that Kept</th></tr>
	</thead>
	<tbody>
		<tr>
			<td>{{ $content['all'] }}</td>
			<td>{{ $content['set'] }}</td>
			<td>{{ $content['kept'] }}</td>
			<td>
			@if($content['all']>0)
			{{ round($content['set']/$content['all']*100) }}%
			@else
			---
			@endif
			</td>
			<td>
			@if($content['set']>0)
			{{ round($content['kept']/$content['set']*100) }}%
			@else
			---
			@endif
			</td>
		</tr>
	</tbody>
</table>

@if(!empty($content['advisors']))
	<hr/>
	<h3>Appointments (Set &amp; Kept), By Advisor</h3>
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr bgcolor="#FFF"><th>Advisor</th><th>All Leads</th><th>Num Set</th><th>Num Kept</th><th><span class="hidden-sm hidden-xs">% of </span>All that Set</th><th class="nostretch"<span class="hidden-sm hidden-xs">% of </span>Set that Kept</th></tr>
		</thead>
		<tbody>
			@foreach($content['advisors'] as $name=>$row)
			<tr>
				<td>{{ $name }}</td>
				<td>{{ $row['all'] }}</td>
				<td>{{ $row['set'] }}</td>
				<td>{{ $row['kept'] }}</td>
				<td>
				@if($row['all']>0)
				{{ round($row['set']/$row['all']*100) }}%
				@else
				---
				@endif
				</td>
				<td>
				@if($row['set']>0)
				{{ round($row['kept']/$row['set']*100) }}%
				@else
				---
				@endif
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
@endif

@if(!empty($content['sources']))
	<hr/>
	<h3>Appointments (Set &amp; Kept), By Source</h3>
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr bgcolor="#FFF"><th>Source</th><th>All Leads</th><th>Num Set</th><th>Num Kept</th><th><span class="hidden-sm hidden-xs">% of </span>All that Set</th><th><span class="hidden-sm hidden-xs">% of </span>Set that Kept</th></tr>
		</thead>
		<tbody>
			@foreach($content['sources'] as $name=>$row)
			<tr>
				<td>{{ $name }}</td>
				<td>{{ $row['all'] }}</td>
				<td>{{ $row['set'] }}</td>
				<td>{{ $row['kept'] }}</td>
				<td>
				@if($row['all']>0)
				{{ round($row['set']/$row['all']*100) }}%
				@else
				---
				@endif
				</td>
				<td>
				@if($row['set']>0)
				{{ round($row['kept']/$row['set']*100) }}%
				@else
				---
				@endif
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
@endif

@if(!empty($content['seminars']))
	<hr/>
	<h3>Appointments (Set &amp; Kept), By Seminar</h3>
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr bgcolor="#FFF"><th>Seminar</th><th>All Leads</th><th>Num Set</th><th>Num Kept</th><th><span class="hidden-sm hidden-xs">% of </span>All that Set</th><th><span class="hidden-sm hidden-xs">% of </span>Set that Kept</th></tr>
		</thead>
		<tbody>
			@foreach($content['seminars'] as $name=>$row)
			<tr>
				<td>{{ $row['type'] }} - {{ $row['date'] }}</td>
				<td>{{ $row['all'] }}</td>
				<td>{{ $row['set'] }}</td>
				<td>{{ $row['kept'] }}</td>
				<td>
				@if($row['all']>0)
				{{ round($row['set']/$row['all']*100) }}%
				@else
				---
				@endif
				</td>
				<td>
				@if($row['set']>0)
				{{ round($row['kept']/$row['set']*100) }}%
				@else
				---
				@endif
				</td>
			</tr>
			@endforeach
		</tbody>
	</table>
@endif

<div style="width:100%; background: #F00; font-size: 10px; display:none;">
	<div style="padding:2px 5px;">{{ $content['all'] }} Leads</div>
	<div style="width:{{ !empty($content['all']) ? round($content['set']/$content['all']*100) : 0 }}%; background: #FF0;">
		<div style="padding:2px 5px;">{{ $content['set'] }} Set</div>
		<div style="width:{{ !empty($content['set']) ? round( ($content['kept'])/$content['set']*100) : 0 }}%; background: #0F0;">
			<div style="padding:2px 5px;">{{ $content['kept'] }} Kept</div>&nbsp;
		</div>
	</div>
</div>
