<?php $content = ${$report->template->file}; ?>
<hr/>

<h3>Total Amount Pending</h3>
<table class="table table-striped table-bordered">
	<tbody>
		<tr>
			<th>Total</th>
			<td>${{ number_format($content['total'],2) }}</td>
		</tr>
	</tbody>
</table>

@if(!empty($content['advisors']))
	<hr/>
	<h3>Total Amount Pending, By Advisor</h3>
	<table class="table table-striped table-bordered">
		<thead>
			<tr><th>Advisor</th><th>Total</th></tr>
		</thead>
		<tbody>
			@foreach($content['advisors'] as $name=>$total)
			<tr>
				<td>{{ $name }}</td>
				<td>${{ number_format($total,2) }}</td>
			</tr>
			@endforeach
		</tbody>
	</table>
@endif