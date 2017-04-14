<?php $content = ${$report->template->file}; ?>

<br/>
<h3>Total Amount Issued</h3>
<table class="table table-striped table-bordered">
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
<table class="table table-striped table-bordered">
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