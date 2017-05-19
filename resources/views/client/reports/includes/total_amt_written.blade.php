<?php $content = ${$report->template->file}; ?>
<hr/>


<h3>Total Amount Written</h3>
<table class="table table-striped table-bordered table-condensed">
	<thead>
		<tr bgcolor="#FFF"><th>Month</th><th>FIA</th><th>AUM</th><th>Life</th><th>Total</th></tr>
	</thead>
	<tbody>
		@foreach($content['months'] as $month=>$total)
		<tr>
			<td>{{ $month }}</td>
			<td>${{ number_format($total['fia'],2) }}</td>
			<td>${{ number_format($total['aum'],2) }}</td>
			<td>${{ number_format($total['life'],2) }}</td>
			<td>${{ number_format($total['fia']+$total['aum']+$total['life'],2) }}</td>
		</tr>
		@endforeach
	</tbody>
	<tfoot>
		<tr bgcolor="#FFF">
			<th>Total</th>
			<th>${{ number_format($content['all']['fia'],2) }}</th>
			<th>${{ number_format($content['all']['aum'],2) }}</th>
			<th>${{ number_format($content['all']['life'],2) }}</th>
			<th>${{ number_format($content['all']['fia']+$content['all']['aum']+$content['all']['life'],2) }}</th>
		</tr>
	</tfoot>
</table>
		<div class="well well-sm">
			<canvas id="allChart" width="300" height="300"></canvas>
		</div>

@if(!empty($content['advisors']))
	<hr/>
	<h3>Total Amount Written, By Advisor</h3>
	@foreach($content['advisors'] as $name=>$advisor)
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">{{ $name }}</h3>
		</div>
		<table class="table table-striped table-bordered table-condensed">
			<thead>
				<tr bgcolor="#FFF"><th>Month</th><th>FIA</th><th>AUM</th><th>Life</th><th>Total</th></tr>
			</thead>
			<tbody>
				@foreach($advisor['months'] as $month=>$total)
				<tr>
					<td>{{ $month }}</td>
					<td>${{ number_format($total['fia'],2) }}</td>
					<td>${{ number_format($total['aum'],2) }}</td>
					<td>${{ number_format($total['life'],2) }}</td>
					<td>${{ number_format($total['fia']+$total['aum']+$total['life'],2) }}</td>
				</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr bgcolor="#FFF">
					<th>Total</th>
					<th>${{ number_format($advisor['all']['fia'],2) }}</th>
					<th>${{ number_format($advisor['all']['aum'],2) }}</th>
					<th>${{ number_format($advisor['all']['life'],2) }}</th>
					<th>${{ number_format($advisor['all']['fia']+$advisor['all']['aum']+$advisor['all']['life'],2) }}</th>
				</tr>
			</tfoot>
		</table>
	</div>
	@endforeach
@endif

@if(!empty($content['weeks']))
	<hr/>
	<h3>Total Amount Written, By Week</h3>
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr bgcolor="#FFF"><th>Week</th><th>FIA</th><th>AUM</th><th>Life</th><th>Total</th></tr>
		</thead>
		<tbody>
			@foreach($content['weeks'] as $name=>$week)
			<tr>
				<td>{{ $week['start'] }} - {{ $week['end'] }}</td>
				<td>${{ number_format($week['fia'],2) }}</td>
				<td>${{ number_format($week['aum'],2) }}</td>
				<td>${{ number_format($week['life'],2) }}</td>
				<td>${{ number_format($week['fia']+$week['aum']+$week['life'],2) }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
@endif
<script>
var colors = ["#F00","#FF0","#0C0"];
var labels = ["FIA","AUM","Life"];
var data = {labels: labels,datasets:[{data: [{{$content['all']['fia']}}, {{$content['all']['aum']}}, {{$content['all']['life']}}],backgroundColor: colors,hoverBackgroundColor: colors},{data: [{{$content['all']['fia']}}, {{$content['all']['aum']}}, {{$content['all']['life']}}],backgroundColor: colors,hoverBackgroundColor: colors}],'stacked':true};
var ctx = document.getElementById("allChart");
var allPieChart = new Chart(ctx,{type: 'bar',data: data,options: {}});

@foreach($content['advisors'] as $name=>$row)
var data{{ str_slug($name,'_') }} = {labels: labels,datasets:[{data: [{{$row['all']['fia']}}, {{$row['all']['aum']}}, {{$row['all']['life']}}],backgroundColor: colors,hoverBackgroundColor: colors}]};
var ctx = document.getElementById("{{str_slug($name,'_')}}Chart");
var {{ str_slug($name,'_') }}PieChart = new Chart(ctx,{type: 'bar',data: data{{ str_slug($name,'_') }},options: {}});
@endforeach
</script>