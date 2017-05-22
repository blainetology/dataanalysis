<?php $content = ${$report->template->file}; ?>
@if(!empty($content['sections']))
	<span class="pull-right">
	Jump To 
	@foreach($content['sections'] as $sections)
		| <a href="#section_{{ str_slug($sections['label'],'_') }}">By {{ $sections['label'] }}</a>
	@endforeach
	</span>
@endif

<h3 style="font-size:1.6em;">All Together</h3>
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">&nbsp;</h3>
	</div>
	<table class="table table-striped table-bordered table-condensed report-table">
		<tbody>
			@if(!empty($content['months']))
				<tr>
					<th class="bg-info">Month</th>
					@foreach($content['columns'] as $column)
						<th class="bg-info">{{$column}}</th>
					@endforeach
					<th class="bg-info">Records</th>
					<th class="bg-info">Total</th>
				</tr>
				@foreach($content['months'] as $month=>$total)
					<?php $totals=0; ?> 
					<tr>
						<td width="20%">{{ $month }}</td>
						@foreach($total['cols'] as $row)
							<?php $totals+=$row; ?> 
							<td align="right" width="{{ round((100-40)/count($total['cols'])) }}%">${{ number_format($row,2) }}</td>
						@endforeach
						<td align="right" width="8%">{{ number_format($total['count']) }}</td>
						<td align="right" width="12%">${{ number_format($totals,2) }}</td>
					</tr>
				@endforeach
			@endif
			@if(!empty($content['weeks']))
				<tr>
					<th class="bg-info">Week</th>
					@foreach($content['columns'] as $column)
						<th class="bg-info">{{$column}}</th>
					@endforeach
					<th class="bg-info">Records</th>
					<th class="bg-info">Total</th>
				</tr>
				@foreach($content['weeks'] as $week)
					<?php $totals=0; ?> 
					<tr>
						<td width="20%">{{ $week['start'] }}-{{ $week['end'] }}</td>
						@foreach($week['cols'] as $row)
							<?php $totals+=$row; ?> 
							<td align="right" width="{{ round((100-40)/count($week['cols'])) }}%">${{ number_format($row,2) }}</td>
						@endforeach
						<td align="right" width="8%">{{ number_format($week['count']) }}</td>
						<td align="right" width="12%">${{ number_format($totals,2) }}</td>
					</tr>
				@endforeach
			@endif
			<tr>
				@if(!empty($content['weeks']) || !empty($content['months']))
					<th class="bg-info"></th>
				@endif
				@foreach($content['columns'] as $column)
					<th class="bg-info">{{$column}}</th>
				@endforeach
				<th class="bg-info">Records</th>
				<th class="bg-info">Total</th>
			</tr>
		</tbody>
		<tfoot>
			<tr style="font-weight: 900;">
				@if(!empty($content['weeks']) || !empty($content['months']))
					<td class="bg-success">Totals</td>
				@endif
				<?php $totals=0; ?> 
				@foreach($content['all']['cols'] as $row)
					<?php $totals+=$row; ?> 
					<td align="right" width="{{ round((100-40)/count($content['all']['cols'])) }}%" class="bg-success">${{ number_format($row,2) }}</td>
				@endforeach
				<td align="right" class="bg-success">{{ number_format($content['all']['count']) }} </td>
				<td align="right" class="bg-success">${{ number_format($totals,2) }}</td>
			</tr>
		</tfoot>
	</table>
</div>

@foreach($content['sections'] as $sections)
	<a name="section_{{ str_slug($sections['label'],'_') }}"></a>
	<hr/>
	<h3 style="font-size:1.6em;">By {{$sections['label']}}</h3>
	@foreach($sections['data'] as $name=>$section)
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title"><strong>{{ ucwords($name) }}</strong></h3>
		</div>
		<table class="table table-striped table-bordered table-condensed report-table">
			<tbody>
				@if(!empty($section['months']))
					<tr>
						<th class="bg-info">Month</th>
						@foreach($content['columns'] as $column)
							<th class="bg-info">{{$column}}</th>
						@endforeach
						<th class="bg-info">Records</th>
						<th class="bg-info">Total</th>
					</tr>
					@foreach($section['months'] as $month=>$total)
						<?php $totals=0; ?> 
						<tr>
							<td width="20%">{{ $month }}</td>
							@foreach($total['cols'] as $row)
								<?php $totals+=$row; ?> 
								<td align="right" width="{{ round((100-40)/count($total['cols'])) }}%">${{ number_format($row,2) }}</td>
							@endforeach
							<td align="right" width="8%">{{ number_format($total['count']) }}</td>
							<td align="right" width="12%">${{ number_format($totals,2) }}</td>
						</tr>
					@endforeach
				@endif
				@if(!empty($section['weeks']))
					<tr>
						<th class="bg-info">Week</th>
						@foreach($content['columns'] as $column)
							<th class="bg-info">{{$column}}</th>
						@endforeach
						<th class="bg-info">Records</th>
						<th class="bg-info">Total</th>
					</tr>
					@foreach($section['weeks'] as $week)
						<?php $totals=0; ?> 
						<tr>
							<td width="20%">{{ $week['start'] }}-{{ $week['end'] }}</td>
							@foreach($week['cols'] as $row)
								<?php $totals+=$row; ?> 
								<td align="right" width="{{ round((100-40)/count($week['cols'])) }}%">${{ number_format($row,2) }}</td>
							@endforeach
							<td align="right" width="8%">{{ number_format($week['count']) }}</td>
							<td align="right" width="12%">${{ number_format($totals,2) }}</td>
						</tr>
					@endforeach
				@endif
				<tr>
					@if(!empty($section['weeks']) || !empty($section['months']))
						<th class="bg-info"></th>
					@endif
					@foreach($content['columns'] as $column)
						<th class="bg-info">{{$column}}</th>
					@endforeach
					<th class="bg-info">Records</th>
					<th class="bg-info">Total</th>
				</tr>
			</tbody>
			<tfoot>
				<tr style="font-weight: 900;">
					@if(!empty($section['weeks']) || !empty($section['months']))
						<td class="bg-success">Totals</td>
					@endif
					<?php $totals=0; ?> 
					@foreach($section['all']['cols'] as $row)
						<?php $totals+=$row; ?> 
						<td align="right" width="{{ round((100-40)/count($section['all']['cols'])) }}%" class="bg-success">${{ number_format($row,2) }}</td>
					@endforeach
					<td align="right" class="bg-success">{{ number_format($section['all']['count']) }}</td>
					<td align="right" class="bg-success">${{ number_format($totals,2) }}</td>
				</tr>
			</tfoot>

		</table>
	</div>
	@endforeach
@endforeach

<hr/>
<h3>By Location</h3>
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">&nbsp;</h3>
	</div>
	<div id="map"></div>
</div>

@section('styles')
<style>
      #map {
        height: 400px;
      }
      .report-table th{text-align: center !important;}
</style>
@append

@section('scripts')

<script type="text/javascript">
	var map;
    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 2,
          center: new google.maps.LatLng(2.8,-187.3),
        });
    }
//    google.maps.event.addDomListener(window, "load", initMap);
</script>

@append