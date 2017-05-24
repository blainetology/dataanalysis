<?php $content = ${$report->template->file}; ?>

<pre>
{{ print_r($content,true) }}
</pre>

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
        height: 500px;
      }
      .report-table th{text-align: center !important;}
</style>
@append

@section('scripts')

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyADHSrojKFkUvVCmQrh1yfkPNhC25xLIzE" type="text/javascript"></script>
<script type="text/javascript">
	var map;
    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 9,
          center: new google.maps.LatLng(33,-112),
        });

        @foreach($content['all'] as $address=>$data)
        var cityCircle = new google.maps.Circle({
        	title: '{{addslashes($address)}}',
            strokeColor: '#FF0000',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            fillColor: '#FF0000',
            fillOpacity: 0.35,
            map: map,
            center: new google.maps.LatLng({{$data['geocode']['latitude']}},{{$data['geocode']['longitude']}}),
            radius: {{round($data['all']/2)}}
        });
        @endforeach

        @foreach($content['sections'] as $col=>$section)
	        @foreach($section['data'] as $address=>$data)
	        var cityCircle = new google.maps.Circle({
	        	title: '{{addslashes($address)}}',
	            strokeColor: '#FF0000',
	            strokeOpacity: 0.8,
	            strokeWeight: 2,
	            fillColor: '#FF0000',
	            fillOpacity: 0.35,
	            map: map,
	            center: new google.maps.LatLng({{$data['geocode']['latitude']}},{{$data['geocode']['longitude']}}),
	            radius: {{round($data['all']/2)}}
	        });
	        @endforeach
        @endforeach

    }
    google.maps.event.addDomListener(window, "load", initMap);
</script>

@append