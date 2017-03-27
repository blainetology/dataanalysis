<?php $content = ${$report->template->file}; ?>
<div class="container">
<br/>
<h3>Total Amount Issued</h3>

<table class="table table-striped table-bordered">
	<thead>
		<tr bgcolor="#FFF"><th>Source</th><th>Amount Issued</th></tr>
	</thead>
	<tbody>
		@foreach($content['sources'] as $source=>$amount)
		<tr>
			<td>{{ $source }}</td>
			<td>${{ number_format($amount,2) }}
		</tr>
		@endforeach
	</tbody>
</table>

</div>