<?php $content = ${$report->template->file}; ?>

<br/>
<h3>Total Amount Issued</h3>
<table class="table table-striped table-bordered table-condensed">
	<thead>
		<tr bgcolor="#FFF"><th>FIA Amount Issued</th><th>AUM Amount Invested</th><th>Total</th></tr>
	</thead>
	<tbody>
		<tr>
			<td>${{ number_format($content['issued']['fia'],2) }}</td>
			<td>${{ number_format($content['issued']['aum'],2) }}</td>
			<td>${{ number_format($content['issued']['total'],2) }}</td>
		</tr>
	</tbody>
</table>


@if($content['sources'])
<br/>
<h3>Total Amount Issued By Source</h3>
<table class="table table-striped table-bordered table-condensed">
	<thead>
		<tr bgcolor="#FFF"><th>Source</th><th>FIA Amount Issued</th><th>AUM Amount Invested</th><th>Total</th></tr>
	</thead>
	<tbody>
		@foreach($content['sources'] as $source=>$amount)
		<tr>
			<td>{{ $source }}</td>
			<td>${{ number_format($amount['fia'],2) }}</td>
			<td>${{ number_format($amount['aum'],2) }}</td>
			<td>${{ number_format($amount['total'],2) }}</td>
		</tr>
		@endforeach
	</tbody>
</table>
@endif

@if(!empty($content['weeks']))
	<br/>
	<h3>Total Amount Issued By Week</h3>
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr bgcolor="#FFF"><th>Week</th><th>FIA Amount Issued</th><th>AUM Amount Invested</th><th>Total</th></tr>
		</thead>
		<tbody>
			@foreach($content['weeks'] as $name=>$week)
			<tr>
				<td>{{ $week['start'] }} - {{ $week['end'] }}</td>
				<td>${{ number_format($week['fia'],2) }}</td>
				<td>${{ number_format($week['aum'],2) }}</td>
				<td>${{ number_format($week['fia']+$week['aum'],2) }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
@endif