<?php $content = ${$report->template->file}; ?>
@if(!empty($content['sections']))
	<div align="right">
	Jump To 
	@foreach($content['sections'] as $sections)
		| <a href="#section_{{ str_slug($sections['label'],'_') }}">By {{ $sections['label'] }}</a>
	@endforeach
	</div>
@endif

<hr/>
<h3>All Together</h3>
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title">&nbsp;</h3>
	</div>
	<table class="table table-striped table-bordered table-condensed">
		<tbody>
			@if(!empty($content['months']))
				<tr>
					<th class="small bg-info">Month</th>
					@foreach($content['columns'] as $column)
					<th class="small bg-info">{{$column}}</th>
					@endforeach
					<th class="small bg-info">Total</th>
				</tr>
				@foreach($content['months'] as $month=>$total)
					<?php $totals=0; ?> 
					<tr>
						<td width="25%">{{ $month }}</td>
						@foreach($total as $row)
						<?php $totals+=$row; ?> 
						<td width="{{ round((100-40)/count($total)) }}%">${{ number_format($row,2) }}</td>
						@endforeach
						<td width="15%">${{ number_format($totals,2) }}</td>
					</tr>
				@endforeach
			@endif
			@if(!empty($content['weeks']))
				<tr>
					<th class="small bg-info">Week</th>
					@foreach($content['columns'] as $column)
					<th class="small bg-info">{{$column}}</th>
					@endforeach
					<th class="small bg-info">Total</th>
				</tr>
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
			@endif
			<tr>
				@if(!empty($content['weeks']) || !empty($content['months']))
					<th class="small bg-info"></th>
				@endif
				@foreach($content['columns'] as $column)
				<th class="small bg-info">{{$column}}</th>
				@endforeach
				<th class="small bg-info">Total</th>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				@if(!empty($content['weeks']) || !empty($content['months']))
					<th class="bg-success">Totals</th>
				@endif
				<?php $totals=0; ?> 
				@foreach($content['all'] as $row)
				<?php $totals+=$row; ?> 
				<th width="{{ round((100-40)/count($content['all'])) }}%" class="bg-success">${{ number_format($row,2) }}</th>
				@endforeach
				<th class="bg-success">${{ number_format($totals,2) }}</th>
			</tr>
		</tfoot>
	</table>
</div>

@foreach($content['sections'] as $sections)
	<a name="section_{{ str_slug($sections['label'],'_') }}"></a>
	<hr/>
	<h3>By {{$sections['label']}}</h3>
	@foreach($sections['data'] as $name=>$section)
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title"><strong>{{ ucwords($name) }}</strong></h3>
		</div>
		<table class="table table-striped table-bordered table-condensed">
			<tbody>
				@if(!empty($section['months']))
					<tr>
						<th class="small bg-info">Month</th>
						@foreach($content['columns'] as $column)
						<th class="small bg-info">{{$column}}</th>
						@endforeach
						<th class="small bg-info">Total</th>
					</tr>
					@foreach($section['months'] as $month=>$total)
						<?php $totals=0; ?> 
						<tr>
							<td width="25%">{{ $month }}</td>
							@foreach($total as $row)
							<?php $totals+=$row; ?> 
							<td width="{{ round((100-40)/count($total)) }}%">${{ number_format($row,2) }}</td>
							@endforeach
							<td width="15%">${{ number_format($totals,2) }}</td>
						</tr>
					@endforeach
				@endif
				@if(!empty($section['weeks']))
					<tr>
						<th class="small bg-info">Week</th>
						@foreach($content['columns'] as $column)
						<th class="small bg-info">{{$column}}</th>
						@endforeach
						<th class="small bg-info">Total</th>
					</tr>
					@foreach($section['weeks'] as $week)
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
				@endif
				<tr>
					@if(!empty($section['weeks']) || !empty($section['months']))
						<th class="small bg-info"></th>
					@endif
					@foreach($content['columns'] as $column)
					<th class="small bg-info">{{$column}}</th>
					@endforeach
					<th class="small bg-info">Total</th>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					@if(!empty($section['weeks']) || !empty($section['months']))
						<th class="bg-success">Totals</th>
					@endif
					<?php $totals=0; ?> 
					@foreach($section['all'] as $row)
					<?php $totals+=$row; ?> 
					<th width="{{ round((100-40)/count($section['all'])) }}%" class="bg-success">${{ number_format($row,2) }}</th>
					@endforeach
					<th class="bg-success">${{ number_format($totals,2) }}</th>
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
</style>
@append

@section('scripts')

<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyADHSrojKFkUvVCmQrh1yfkPNhC25xLIzE&callback=initMap"></script>
<script type="text/javascript">
	console.log('appended');
	var map;
    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 2,
          center: new google.maps.LatLng(2.8,-187.3),
        });
    }
</script>

@append