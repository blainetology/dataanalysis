<?php $content = ${$report->template->file}; ?>
<br/><hr/>


<h3>Total Amount Written</h3>
<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">&nbsp;</h3>
	</div>
	<table class="table table-striped table-bordered table-condensed">
		<thead>
			<tr bgcolor="#FFF">
			<th class="small">Month</th>
			@foreach($content['columns'] as $column)
			<th class="small">{{$column}}</th>
			@endforeach
			<th class="small">Total</th>
			</tr>
		</thead>
		<tbody>
			@foreach($content['months'] as $month=>$total)
			<?php $totals=0; ?> 
			<tr>
				<td>{{ $month }}</td>
				@foreach($total as $row)
				<?php $totals+=$row; ?> 
				<td>${{ number_format($row,2) }}</td>
				@endforeach
				<td>${{ number_format($totals,2) }}</td>
			</tr>
			@endforeach
		</tbody>
		<tfoot>
			<tr bgcolor="#FFF">
				<th>Total</th>
				<?php $totals=0; ?> 
				@foreach($content['all'] as $row)
				<?php $totals+=$row; ?> 
				<th>${{ number_format($row,2) }}</th>
				@endforeach
				<th>${{ number_format($totals,2) }}</th>
			</tr>
		</tfoot>
	</table>
</div>

@foreach($content['sections'] as $sections)
	<br/><hr/>
	<h3>Total Amount, By {{$sections['label']}}</h3>
	@foreach($sections['data'] as $name=>$section)
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">{{ $name }}</h3>
		</div>
		<table class="table table-striped table-bordered table-condensed">

			<thead>
				<tr bgcolor="#FFF">
				<th class="small">Month</th>
				@foreach($content['columns'] as $column)
				<th class="small">{{$column}}</th>
				@endforeach
				<th class="small">Total</th>
				</tr>
			</thead>
			<tbody>
				@foreach($section['months'] as $month=>$total)
				<?php $totals=0; ?> 
				<tr>
					<td>{{ $month }}</td>
					@foreach($total as $row)
					<?php $totals+=$row; ?> 
					<td>${{ number_format($row,2) }}</td>
					@endforeach
					<td>${{ number_format($totals,2) }}</td>
				</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr bgcolor="#FFF">
					<th>Total</th>
					<?php $totals=0; ?> 
					@foreach($section['all'] as $row)
					<?php $totals+=$row; ?> 
					<th>${{ number_format($row,2) }}</th>
					@endforeach
					<th>${{ number_format($totals,2) }}</th>
				</tr>
			</tfoot>

		</table>
	</div>
	@endforeach
@endforeach

@if(!empty($content['weeks']))
	<br/><hr/>
	<h3>Total Amount, By Week</h3>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h3 class="panel-title">&nbsp;</h3>
		</div>
		<table class="table table-striped table-bordered table-condensed">
			<thead>
				<tr bgcolor="#FFF">
				<th class="small">Week</th>
				@foreach($content['columns'] as $column)
				<th class="small">{{$column}}</th>
				@endforeach
				<th class="small">Total</th>
				</tr>
			</thead>
			<tbody>
				@foreach($content['weeks'] as $week)
					<?php $totals=0; ?> 
					<tr>
						<td>{{ $week['start'] }}-{{ $week['end'] }}</td>
						@foreach($week['cols'] as $row)
						<?php $totals+=$row; ?> 
						<td>${{ number_format($row,2) }}</td>
						@endforeach
						<td>${{ number_format($totals,2) }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
@endif
