<?php $content = ${$report->template->file}; ?>

<pre style="display:none;">
{{ print_r($content,true) }}
</pre>

<hr/>
<h3>By Location</h3>
<div id="map-links"><a href="javascript:populate(null,null)">SHOW ALL</a> | </div>
<div class="panel panel-primary">
	<div class="panel-heading">
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
    var colors = ['#FF0000','#00FF00','#0000FF','#FFFF00','#FF9900'];
    var bounds = new google.maps.LatLngBounds();
    var circles = [];

    var data = {!! json_encode($content) !!};
    console.log(data);
    
    function initMap(){

        $.each(data.sections, function(col,section){
            $('#map-links').append('<a href="javascript:populate(\''+escape(col)+'\',null)">'+section.label.toUpperCase() + "</a>, ");
            $.each(section.data, function(key,block){
                $('#map-links').append('<a href="javascript:populate(\''+escape(col)+'\',\''+escape(key)+'\')">' + key + "</a>, ");

            });
        });


        $('#map').height($(window).height()-250);
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 10,
          center: new google.maps.LatLng(33,-112),
        });
        populate();

    }
    function populate(col,key){

        for (var i = 0; i < circles.length; i++) {
            if(typeof circles[i] !== "undefined")
                circles[i].setMap(null);
        }
        circles = [];

        var index=0;
        if(col && key){

            var x=1;
            var y=1;

            var section = data.sections[col];
            var block = section.data[key];

            $.each(block, function(address,data){
                circles[index] = new google.maps.Circle({
                    title: address,
                    strokeColor: colors[y],
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: colors[x],
                    fillOpacity: 0.35,
                    map: map,
                    center: new google.maps.LatLng(parseFloat(data['geocode']['latitude']),parseFloat(data['geocode']['longitude'])),
                    radius: Math.round(data.all/3)
                });
                index++;
                bounds.extend(new google.maps.LatLng(parseFloat(data['geocode']['latitude']),parseFloat(data['geocode']['longitude'])));
            });

        }
        else if(col && !key){
            var x=1;
            var y=1;

            section = data.sections[col];
            $.each(section.data, function(key,block){
                $.each(block, function(address,data){
                    circles[index] = new google.maps.Circle({
                        title: address,
                        strokeColor: colors[y],
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        fillColor: colors[x],
                        fillOpacity: 0.35,
                        map: map,
                        center: new google.maps.LatLng(parseFloat(data['geocode']['latitude']),parseFloat(data['geocode']['longitude'])),
                        radius: Math.round(data.all/3)
                    });
                    index++;
                    bounds.extend(new google.maps.LatLng(parseFloat(data['geocode']['latitude']),parseFloat(data['geocode']['longitude'])));
                });
                y++
            });
            x++;

            map.fitBounds(bounds);
            var zoom = map.getZoom();
            map.setZoom(zoom-1);

        }
        else{
            $.each(data.all, function(address,data){
                circles[index] = new google.maps.Circle({
                    title: address,
                    strokeColor: colors[0],
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: colors[0],
                    fillOpacity: 0.35,
                    map: map,
                    center: new google.maps.LatLng(parseFloat(data['geocode']['latitude']),parseFloat(data['geocode']['longitude'])),
                    radius: Math.round(data['all']/2)
                });
                index++;
                bounds.extend(new google.maps.LatLng(parseFloat(data['geocode']['latitude']),parseFloat(data['geocode']['longitude'])));
            });
        }

        map.fitBounds(bounds);
        var zoom = map.getZoom();
        map.setZoom(zoom-1);

    }
    google.maps.event.addDomListener(window, "load", initMap);
</script>

@append