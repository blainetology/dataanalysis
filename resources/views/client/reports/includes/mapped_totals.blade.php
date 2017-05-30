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
			<div class="panel-heading" id="columnsFilter"></div>
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
    var xsec = null;
    var xkey = null;
    var xcol = null;
    var data = {!! json_encode($content) !!};
    console.log(data);
    
    function initMap(){

        $.each(data.sections, function(col,section){
            $('#map-links').append('<br/><br/><a href="javascript:populate(\''+escape(col)+'\',null,xcol)">'+section.label.toUpperCase() + "</a>");
            $.each(section.data, function(key,block){
                $('#map-links').append('<br/> &nbsp; <a href="javascript:populate(\''+escape(col)+'\',\''+escape(key)+'\',xcol)">&bull; ' + key + "</a>");

            });
        });

        $('#columnsFilter').append('<a href="javascript:populate(xsec,xkey,\'total\')" style="color:#FFF;">Totals</a> | <a href="javascript:populate(xsec,xkey,\'count\')" style="color:#FFF;">Entries</a>');
        $.each(data.columns, function(id,label){
            $('#columnsFilter').append(' | <a href="javascript:populate(xsec,xkey,\'col'+id+'\')" style="color:#FFF;">' + label + '</a>');
        });


        $('#map').height($(window).height()-185);
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 10,
          center: new google.maps.LatLng(33,-112),
        });
        populate(null,null,'total');

    }
    function populate(sec,key,col){
        xsec = sec;
        xkey = key;
        xcol = col;

        for (var i = 0; i < circles.length; i++) {
            if(typeof circles[i] !== "undefined")
                circles[i].setMap(null);
        }
        circles = [];

        var index=0;
        var max=null;
        var min=null;
        if(xsec && xkey){

            var section = data.sections[xsec];
            var block = section.data[xkey];

            $.each(block, function(address,data){
                if(address != 'color'){
                    var radius = 0;
                    if(xcol=='total')
                        radius = data.all;
                    else if(xcol=='count')
                        radius = data.count;
                    else
                        radius = data.cols[xcol];

                    if(!min)
                        min=radius;
                    if(!max)
                        max=radius;
                    if(radius<min)
                        min=radius;
                    if(radius>max)
                        max=radius;
                }
            });
            console.log(min,max);
            $.each(block, function(address,data){
            	if(address != 'color'){
                    var radius = 0;
                    if(xcol=='total')
                        radius = data.all;
                    else if(xcol=='count')
                        radius = data.count;
                    else
                        radius = data.cols[xcol];
	                circles[index] = new google.maps.Circle({
	                    title: address,
	                    strokeColor: colors[block.color],
	                    strokeOpacity: 0.8,
	                    strokeWeight: 2,
	                    fillColor: colors[block.color],
	                    fillOpacity: 0.35,
	                    map: map,
	                    center: new google.maps.LatLng(parseFloat(data['geocode']['latitude']),parseFloat(data['geocode']['longitude'])),
	                    radius: Math.round(radius)
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
        else if(xsec && !xkey){
            var x=1;
            var y=1;

            section = data.sections[xsec];
            $.each(section.data, function(key,block){
                $.each(block, function(address,data){
                    if(address != 'color'){
                        var radius = 0;
                        if(xcol=='total')
                            radius = data.all;
                        else if(xcol=='count')
                            radius = data.count;
                        else
                            radius = data.cols[xcol];

                        if(!min)
                            min=radius;
                        if(!max)
                            max=radius;
                        if(radius<min)
                            min=radius;
                        if(radius>max)
                            max=radius;
                    }
                });
            });
            console.log(min,max);
            $.each(section.data, function(key,block){
                $.each(block, function(address,data){
	            	if(address != 'color'){
                        var radius = 0;
                        if(xcol=='total')
                            radius = data.all;
                        else if(xcol=='count')
                            radius = data.count;
                        else
                            radius = data.cols[xcol];
	                    circles[index] = new google.maps.Circle({
	                        title: address,
	                        strokeColor: colors[block.color],
	                        strokeOpacity: 0.8,
	                        strokeWeight: 2,
	                        fillColor: colors[block.color],
	                        fillOpacity: 0.35,
	                        map: map,
	                        center: new google.maps.LatLng(parseFloat(data['geocode']['latitude']),parseFloat(data['geocode']['longitude'])),
	                        radius: Math.round(radius)
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
        }
        else{
            $.each(data.all, function(address,data){
                var radius = 0;
                if(xcol=='total')
                    radius = data.all;
                else if(xcol=='count')
                    radius = data.count;
                else
                    radius = data.cols[xcol];

                if(!min)
                    min=radius;
                if(!max)
                    max=radius;
                if(radius<min)
                    min=radius;
                if(radius>max)
                    max=radius;
            });
            console.log(min,max);
            $.each(data.all, function(address,data){
                var radius = 0;
                if(xcol=='total')
                    radius = data.all;
                else if(xcol=='count')
                    radius = data.count;
                else
                    radius = data.cols[xcol];
                circles[index] = new google.maps.Circle({
                    title: address,
                    strokeColor: '#FF0000',
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: '#FF0000',
                    fillOpacity: 0.35,
                    map: map,
                    center: new google.maps.LatLng(parseFloat(data['geocode']['latitude']),parseFloat(data['geocode']['longitude'])),
                    radius: Math.round(radius)
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
        //map.setZoom(zoom-1);

    }
    google.maps.event.addDomListener(window, "load", initMap);
</script>

@append