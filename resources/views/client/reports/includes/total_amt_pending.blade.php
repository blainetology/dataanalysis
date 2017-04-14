<?php $content = ${$report->template->file}; ?>
<br/>
<h3>Total Amount Pending</h3>
<table class="table table-striped table-bordered">
	<tbody>
		<tr>
			<th>Total</th>
			<td>${{ number_format($content['total'],2) }}</td>
		</tr>
	</tbody>
</table>