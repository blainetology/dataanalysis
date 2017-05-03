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