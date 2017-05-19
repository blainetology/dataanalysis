<?php $content = ${$report->template->file}; ?>
<hr/>
<h3>Appointments (Set &amp; Kept)</h3>

<div class="row">
	<div class="col-md-9">
		<table class="table table-striped table-bordered">
			<tbody>
				<tr>
					<th>All Marketing Leads</th><td>{{ $content['all'] }}</td>
				</tr>
				<tr>
					<th>Number of Set Appointments</th><td>{{ $content['set'] }}</td>
				</tr>
				<tr>
					<th>Number of Kept Appointments</th><td>{{ $content['kept'] }}</td>
				</tr>
				<tr>
					<th>% of All Leads that Set Appt</th><td>
					@if($content['all']>0)
					{{ round($content['set']/$content['all']*100) }}%
					@else
					---
					@endif
					</td>
				</tr>
				<tr>
					<th>% of Set Appts that Kept Appt</th>
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
	</div>
	<div class="col-sm-3">
		<div class="well well-sm">
			<canvas id="allChart" width="300" height="300"></canvas>
		</div>
	</div>
</div>

@if(!empty($content['advisors']))
	<hr/>
	<hr/>
	<div class="row">
		<div class="col-lg-12">
			<h3>Appointments (Set &amp; Kept), By Advisor</h3>
			<table class="table table-striped table-bordered table-condensed">
				<thead>
					<tr><th>Advisor</th><th width="14%">All Leads</th><th width="14%">Number Set</th><th width="14%">Number Kept</th><th width="14%"><span class="hidden-sm hidden-xs">% of </span>All that Set</th><th width="14%" class="nostretch"><span class="hidden-sm hidden-xs">% of </span>Set that Kept</th></tr>
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
		</div>
		@foreach($content['advisors'] as $name=>$row)
		<div class="col-sm-3">
			<div class="well well-sm">
			<strong>{{ $name }}</strong>
			<canvas id="{{ str_slug($name,'_') }}Chart" width="300" height="300" class="chartCanvas"></canvas>
			</div>
		</div>
		@endforeach
	</div>
@endif

@if(!empty($content['sources']))
	<hr/>
	<hr/>
	<div class="row">
		<div class="col-lg-12">
			<h3>Appointments (Set &amp; Kept), By Source</h3>
			<table class="table table-striped table-bordered table-condensed">
				<thead>
					<tr><th>Source</th><th width="14%">All Leads</th><th width="14%">Number Set</th><th width="14%">Number Kept</th><th width="14%"><span class="hidden-sm hidden-xs">% of </span>All that Set</th><th width="14%" class="nostretch"><span class="hidden-sm hidden-xs">% of </span>Set that Kept</th></tr>
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
		</div>
		@foreach($content['sources'] as $name=>$row)
		<div class="col-sm-3">
			<div class="well well-sm">
			<strong>{{ ucwords($name) }}</strong>
			<canvas id="{{ str_slug($name,'_') }}Chart" width="300" height="300" class="chartCanvas"></canvas>
			</div>
		</div>
		@endforeach
	</div>
@endif

@if(!empty($content['seminars']))
	<hr/>
	<hr/>
	<div class="row">
		<div class="col-lg-12">
			<h3>Appointments (Set &amp; Kept), By Seminar</h3>
			<table class="table table-striped table-bordered table-condensed">
				<thead>
					<tr><th>Seminar</th><th width="14%">All Leads</th><th width="14%">Number Set</th><th width="14%">Number Kept</th><th width="14%"><span class="hidden-sm hidden-xs">% of </span>All that Set</th><th width="14%" class="nostretch"><span class="hidden-sm hidden-xs">% of </span>Set that Kept</th></tr>
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
		</div>
		@foreach($content['seminars'] as $name=>$row)
		<div class="col-sm-3">
			<div class="well well-sm">
			<strong>{{ $row['type'] }} - {{ $row['date'] }}</strong>
			<canvas id="{{ str_slug($name,'_') }}Chart" width="300" height="300" class="chartCanvas"></canvas>
			</div>
		</div>
		@endforeach
	</div>
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
<script>
$('.well-sm').each(function(){
	//var that = $(this);
	//$(this).css('width',that.parent('div').width()+'px');
	//$(this).css('height',(that.parent('div').width()+20)+'px');
})
var colors = ["#F00","#FF0","#0C0"];
var labels = ["Never set appointment","Set appt. but didn't keep","Kept appointment"];
var data = {labels: labels,datasets:[{data: [{{$content['all']-$content['set']}}, {{$content['set']-$content['kept']}}, {{$content['kept']}}],backgroundColor: colors,hoverBackgroundColor: colors}]};
var ctx = document.getElementById("allChart");
var allPieChart = new Chart(ctx,{type: 'pie',data: data,options: {}});

@foreach($content['advisors'] as $name=>$row)
var data{{ str_slug($name,'_') }} = {labels: labels,datasets:[{data: [{{$row['all']-$row['set']}}, {{$row['set']-$row['kept']}}, {{$row['kept']}}],backgroundColor: colors,hoverBackgroundColor: colors}]};
var ctx = document.getElementById("{{str_slug($name,'_')}}Chart");
var {{ str_slug($name,'_') }}PieChart = new Chart(ctx,{type: 'pie',data: data{{ str_slug($name,'_') }},options: {}});
@endforeach

@foreach($content['sources'] as $name=>$row)
var data{{ str_slug($name,'_') }} = {labels: labels,datasets:[{data: [{{$row['all']-$row['set']}}, {{$row['set']-$row['kept']}}, {{$row['kept']}}],backgroundColor: colors,hoverBackgroundColor: colors}]};
var ctx = document.getElementById("{{str_slug($name,'_')}}Chart");
var {{ str_slug($name,'_') }}PieChart = new Chart(ctx,{type: 'pie',data: data{{ str_slug($name,'_') }},options: {}});
@endforeach

@foreach($content['seminars'] as $name=>$row)
var data{{ str_slug($name,'_') }} = {labels: labels,datasets:[{data: [{{$row['all']-$row['set']}}, {{$row['set']-$row['kept']}}, {{$row['kept']}}],backgroundColor: colors,hoverBackgroundColor: colors}]};
var ctx = document.getElementById("{{str_slug($name,'_')}}Chart");
var {{ str_slug($name,'_') }}PieChart = new Chart(ctx,{type: 'pie',data: data{{ str_slug($name,'_') }},options: {}});
@endforeach
</script>