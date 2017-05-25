<?php $content = ${$report->template->file}; ?>

<pre style="display:none;">
{{ print_r($content,true) }}
</pre>
<hr/>
<div class="row">
	<div class="col-md-2 bg-success">
		<br/>
		<div class="col-lg-12">
			<div id="map-links">
				<a href="javascript:populate(null,null)">OVERALL TOTALS</a>
			</div>
			<br/><br/>
		</div>
	</div>
	<div class="col-md-10">
		<div class="panel panel-primary">
			<div class="panel-heading">
			</div>
			<div id="map"></div>
		</div>
	</div>
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
    var colors = ['#00CC00','#0000CC','#CC00CC','#FF9900','#333333','#FF9999',''];
    var bounds = new google.maps.LatLngBounds();
    var circles = [];

    var data = {!! json_encode($content) !!};
    console.log(data);
    
    function initMap(){

        $.each(data.sections, function(col,section){
            $('#map-links').append('<br/><br/><a href="javascript:populate(\''+escape(col)+'\',null)">'+section.label.toUpperCase() + "</a>");
            $.each(section.data, function(key,block){
                $('#map-links').append('<br/> &nbsp; <a href="javascript:populate(\''+escape(col)+'\',\''+escape(key)+'\')">&bull; ' + key + "</a>");

            });
        });


        $('#map').height($(window).height()-185);
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

            var section = data.sections[col];
            var block = section.data[key];

            $.each(block, function(address,data){
            	if(address != 'color'){
	                circles[index] = new google.maps.Circle({
	                    title: address,
	                    strokeColor: colors[block.color],
	                    strokeOpacity: 0.8,
	                    strokeWeight: 3,
	                    fillColor: colors[block.color],
	                    fillOpacity: 0.35,
	                    map: map,
	                    center: new google.maps.LatLng(parseFloat(data['geocode']['latitude']),parseFloat(data['geocode']['longitude'])),
	                    radius: Math.round(data.all/3)
	                });
                    var infowindow = new google.maps.InfoWindow({
			            content: "<h4>"+key+"</h4>"+JSON.stringify(data)
        			});  
			        google.maps.event.addListener(circles[index], 'click', function(ev) {
			            // alert(infowindow.content);
			            infowindow.setPosition(this.getCenter());
			            infowindow.open(map);
			        });

	                index++;
	                bounds.extend(new google.maps.LatLng(parseFloat(data['geocode']['latitude']),parseFloat(data['geocode']['longitude'])));
	            }
            });

        }
        else if(col && !key){
            var x=1;
            var y=1;

            section = data.sections[col];
            $.each(section.data, function(key,block){
                $.each(block, function(address,data){
	            	if(address != 'color'){
	                    circles[index] = new google.maps.Circle({
	                        title: address,
	                        strokeColor: colors[block.color],
	                        strokeOpacity: 0.8,
	                        strokeWeight: 3,
	                        fillColor: colors[block.color],
	                        fillOpacity: 0.35,
	                        map: map,
	                        center: new google.maps.LatLng(parseFloat(data['geocode']['latitude']),parseFloat(data['geocode']['longitude'])),
	                        radius: Math.round(data.all/3)
	                    });
	                    var infowindow = new google.maps.InfoWindow({
				            content: "<h4>"+key+"</h4>"+JSON.stringify(data)
	        			});  
				        google.maps.event.addListener(circles[index], 'click', function(ev) {
				            // alert(infowindow.content);
				            infowindow.setPosition(this.getCenter());
				            infowindow.open(map);
				        });
	                    index++;
	                    bounds.extend(new google.maps.LatLng(parseFloat(data['geocode']['latitude']),parseFloat(data['geocode']['longitude'])));
	                }
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
                    strokeColor: '#FF0000',
                    strokeOpacity: 0.8,
                    strokeWeight: 3,
                    fillColor: '#FF0000',
                    fillOpacity: 0.35,
                    map: map,
                    center: new google.maps.LatLng(parseFloat(data['geocode']['latitude']),parseFloat(data['geocode']['longitude'])),
                    radius: Math.round(data['all']/2)
                });
                var infowindow = new google.maps.InfoWindow({
		            content: "<h4>"+address+"</h4>"+JSON.stringify(data)
    			});  
		        google.maps.event.addListener(circles[index], 'click', function(ev) {
		            // alert(infowindow.content);
		            infowindow.setPosition(this.getCenter());
		            infowindow.open(map);
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