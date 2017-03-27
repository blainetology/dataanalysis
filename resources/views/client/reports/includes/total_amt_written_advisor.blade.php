<?php $content = ${$report->template->file}; ?>
<div class="container">
<br/>
<h3>Total Amount Written by Advisor</h3>
@foreach($content['advisors'] as $name=>$advisor)
<br/><h4>{{ $name }}</h4>
<table class="table table-striped table-bordered">
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
@endforeach

</div>