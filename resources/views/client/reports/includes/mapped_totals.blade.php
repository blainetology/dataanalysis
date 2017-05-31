<?php $content = ${$report->template->file}; ?>

<pre style="display:none;">
{{ print_r($content,true) }}
</pre>
<hr/>
<div class="row">
	<div class="col-md-2 bg-success" id="linkscontainer" style="overflow:auto;">
		<br/>
		<div class="col-lg-12">
			<div id="map-links"></div>
			<br/><br/>
		</div>
	</div>
	<div class="col-md-10" id="mapcontainer">
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
      .map-link.active, .field-link.active{text-decoration: underline;}
</style>
@append

@section('scripts')

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyADHSrojKFkUvVCmQrh1yfkPNhC25xLIzE" type="text/javascript"></script>
<script type="text/javascript">
	var map;
    var colors = ['#cc0000','#009900','#0000cc','#660099','#ff6600','#0099ff','#cccc00','#ff66ff','#999999','#99cc66'];
    var bounds = new google.maps.LatLngBounds();
    var circles = [];
    var xsec = null;
    var xkey = null;
    var xcol = null;
    var firstload = true;
    var previnfowindow=null;

    var index=0;
    var radius = 0;
    var max=null;
    var min=null;
    var adjust=null;
    var offset=null;

    var alldata = {!! json_encode($content) !!};
    console.log(alldata);
    
    function initMap(){

        $('#map-links').append('<a href="javascript:populate(null,null,xcol)" class="map-link active">OVERALL TOTALS</a>');
        $.each(alldata.sections, function(col,section){
            $('#map-links').append('<br/><br/><a href="javascript:populate(\''+escape(col)+'\',null,xcol)" class="map-link">'+section.label.toUpperCase() + "</a>");
            $.each(section.data, function(key,block){
                $('#map-links').append('<br/> &nbsp; <a href="javascript:populate(\''+escape(col)+'\',\''+escape(key)+'\',xcol)" class="map-link">&bull; ' + key + "</a>");

            });
        });
        $('.map-link').on('click',function(){
            $('.map-link').removeClass('active');
            $(this).addClass('active');
        });

        $('#columnsFilter').append('<a href="javascript:populate(xsec,xkey,\'total\')" style="color:#FFF;" class="field-link active">Totals</a> | <a href="javascript:populate(xsec,xkey,\'count\')" style="color:#FFF;" class="field-link">Entries</a>');
        $.each(alldata.columns, function(id,label){
            $('#columnsFilter').append(' | <a href="javascript:populate(xsec,xkey,\'col'+id+'\')" style="color:#FFF;" class="field-link">' + label + '</a>');
        });
        $('.field-link').on('click',function(){
            $('.field-link').removeClass('active');
            $(this).addClass('active');
        });


        $('#map').height($(window).height()-210);
        $('#linkscontainer').height($('#mapcontainer').height()-20);
        map = new google.maps.Map(document.getElementById('map'), {
          zoom: 6,
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

        if(xsec && xkey){
            var section = alldata.sections[xsec];
            var block = section.data[xkey];
            min=null;
            max=null;
            $.each(section.data, function(key,block){
                getMaxMin(block);
            });
            offset = min/2;
            adjust = max-offset;
            console.log(min,max,offset,adjust);
            plotCircles(key,block);
        }
        else if(xsec && !xkey){
            var section = alldata.sections[xsec];
            min=null;
            max=null;
            $.each(section.data, function(key,block){
                getMaxMin(block);
            });
            offset = min/2;
            adjust = max-offset;
            console.log(min,max,offset,adjust);
            $.each(section.data, function(key,block){
                plotCircles(key,block);
            });
        }
        else{
            var block = alldata.all;
            min=null;
            max=null;
            getMaxMin(block);
            offset = min/2;
            adjust = max-offset;
            console.log(min,max,offset,adjust);
            plotCircles(key,block);
        }

        if(firstload){
            resizeBounds();
            firstload=false;
        }

    }
    function getMaxMin(block){
        $.each(block, function(address,data){
            if(address != 'color'){
                radius = 0;
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
    }
    function plotCircles(key,block){
        $.each(block, function(address,data){
            if(address != 'color'){
                radius = 0;
                if(xcol=='total')
                    radius = data.all;
                else if(xcol=='count')
                    radius = data.count;
                else
                    radius = data.cols[xcol];
                circles[index] = new google.maps.Circle({
                    title: address,
                    strokeColor: colors[block.color],
                    strokeOpacity: 0.9,
                    strokeWeight: 4,
                    fillColor: colors[block.color],
                    fillOpacity: 0.45,
                    map: map,
                    center: new google.maps.LatLng(parseFloat(data['geocode']['latitude']),parseFloat(data['geocode']['longitude'])),
                    radius: Math.round((radius-offset)/adjust*2500)
                });
                var infocontent = "<h4><small>"+address+"</small><br/><span class=\"text-danger\">"+(key ? key : 'All Data').toUpperCase()+"</span></h4>";
                infocontent += '<div style="font-size:1.2em; margin-bottom:10px;"><strong>Total:</strong> '+data.all.toLocaleString('en-US',{ style: 'currency', currency: 'USD'})+"<br/>"+'<strong>Records:</strong> '+data.count.toLocaleString()+"</div>";
                $.each(data.cols,function(index,col){
                    infocontent += '<strong>'+alldata.columns[index.replace('col','')]+':</strong> '+col.toLocaleString('en-US',{ style: 'currency', currency: 'USD'})+"<br/>";
                });

                var infowindow = new google.maps.InfoWindow({
                    content: infocontent
                });  
                google.maps.event.addListener(circles[index], 'click', function(ev) {
                    // alert(infowindow.content);
                    if( previnfowindow ) {
                        previnfowindow.close();
                    }
                    previnfowindow = infowindow;
                    infowindow.setPosition(this.getCenter());
                    infowindow.open(map);
                });

                index++;
                bounds.extend(new google.maps.LatLng(parseFloat(data['geocode']['latitude']),parseFloat(data['geocode']['longitude'])));
            }
        });

    }
    function resizeBounds(){
        console.log('bounds',bounds);
        map.fitBounds(bounds);
        //var zoom = map.getZoom();
        //map.setZoom(zoom-1);
        console.log('resizeBounds',map.getZoom());
    }
    google.maps.event.addDomListener(window, "load", initMap);
</script>

@append