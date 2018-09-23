var map;
var map_line;
var map_polygon;	
var map_rectangle;	
var map_circle;	
var map_tmp_index;
var map_markers = [];
var map_polylines = [];
var map_polygons = [];
var map_rectangles = [];
var map_circles = [];
var map_routes = [];
var map_overlays = [];

// Update center
$(document).on('click', '.pan-to-marker', function(e) {
    e.preventDefault();

    var lat, lng;
    var $index = $(this).data('marker-index');
    var $lat = $(this).data('marker-lat');
    var $lng = $(this).data('marker-lng');

    if ($index != undefined) {
        // using indices
        var position = map.markers[$index].getPosition();
        lat = position.lat();
        lng = position.lng();
    }
    else {
        // using coordinates
        lat = $lat;
        lng = $lng;
    }

    map.setCenter(lat, lng);
});

// initialize status
$(document).ready(function(){
    prettyPrint();
    var path_line = [];
    var path_poly = [];
    var path_rect = [];
    var path_circle = [];

    map = new GMaps({
        div: '#map',
        lat: map_default_lat,
        lng: map_default_lng,
        mapTypeControlOptions: {
          mapTypeIds : ["roadmap", "satellite", "osm"]
        },
        zoom_changed: function(e) {
            $('#map_zoom').val(this.getZoom());
        },
        maptypeid_changed: function(e) {
            $('#map_typeid').val(this.getMapTypeId());
        },
        center_changed: function(e) {
            $('#map_center_lat').val(this.getCenter().lat());
            $('#map_center_lng').val(this.getCenter().lng());
        },        
    });
    $('#map_center_lat').val(map_default_lat);
    $('#map_center_lng').val(map_default_lng);
    
    map.addMapType("osm", {
      getTileUrl: function(coord, zoom) {
        return "http://tile.openstreetmap.org/" + zoom + "/" + coord.x + "/" + coord.y + ".png";
      },
      tileSize: new google.maps.Size(256, 256),
      name: "OpenStreetMap",
      maxZoom: 18
    });   
   
    // set zoom of map
    map.setZoom(map_default_zoom);
    // set type of map
    map.setMapTypeId(map_default_typeid);
  
    // initialize status of buttons
    initButtonsStatus();

    // search for an area and center map there
    $('#geocoding_form').submit(function(e){
        e.preventDefault();
        GMaps.geocode({
            address: $('#address').val().trim(),
            callback: function(results, status){
                if(status=='OK'){
                    var latlng = results[0].geometry.location;
                    map.setCenter(latlng.lat(), latlng.lng());
                    
                    if(document.getElementById('add_marker_on_loc_found').checked) {
                        if ( $('input:radio[name=drawwhat]:checked').val() == 'markers') {
                            createMarker(latlng.lat(), latlng.lng());
                        }
                        else if ( $('input:radio[name=drawwhat]:checked').val() == 'route') {
                            createRoute(latlng.lat(), latlng.lng());
                        }                        
                        
                    }
                }
            }
        });
    });   

    // on cancel button click remove polyline or polygon and initialize buttons
    $('#cancel_editing').on("click",function(){
        if ( $('input:radio[name=drawwhat]:checked').val() == 'line')	{	// add a new line 
            if (!$("#save_btn").is(":disabled")) {
                if ($('#line_'+map_tmp_index).length)	{	// line edited exists
                    var tmp_coords = [];
                    tmp_coords = getPathFromCoordsArray($('#line_coords_'+map_tmp_index).val());

                    map_polylines[map_tmp_index].setPath(tmp_coords);
                    map_polylines[map_tmp_index].setEditable(false);
                }
                else {	// line edited is new
                    deletePolyline(map_tmp_index);
                }

                initButtonsStatus();			
            }
        }
        else if ( $('input:radio[name=drawwhat]:checked').val() == 'polygon')	{
            if (!$("#save_btn").is(":disabled")) {
                if ($('#poly_'+map_tmp_index).length)	{	// polygon edited exists
                        var tmp_coords = [];
                        tmp_coords = getPathFromCoordsArray($('#poly_coords_'+map_tmp_index).val());

                        map_polygons[map_tmp_index].setPath(tmp_coords);
                        map_polygons[map_tmp_index].setEditable(false);
                }
                else {	// polygon edited is new
                        deletePolygon(map_tmp_index);
                }

                initButtonsStatus();
            }		
        }	
        else if ( $('input:radio[name=drawwhat]:checked').val() == 'rectangle')	{
            if (!$("#save_btn").is(":disabled")) {
                if ($('#rect_'+map_tmp_index).length)	{	// polygon edited exists
                    var tmp_coords = [];
                    tmp_coords = getDataFromArray($('#rect_coords_'+map_tmp_index).val());
                    var p1 = new google.maps.LatLng(tmp_coords[0],tmp_coords[1]);
                    var p2 = new google.maps.LatLng(tmp_coords[2],tmp_coords[3]);

                    map_rectangles[map_tmp_index].setBounds(new google.maps.LatLngBounds(p1,p2));
                    map_rectangles[map_tmp_index].setEditable(false);
                }
                else {	// polygon edited is new
                    deleteRectangle(map_tmp_index);
                }

                initButtonsStatus();
            }
        }
        else if ( $('input:radio[name=drawwhat]:checked').val() == 'circle')	{
            if (!$("#save_btn").is(":disabled")) {
                if ($('#circle_'+map_tmp_index).length)	{	// circle edited exists
                    var tmp_coords = [];
                    tmp_coords = getDataFromArray($('#circle_coords_'+map_tmp_index).val());

                    map_circles[map_tmp_index].setCenter(new google.maps.LatLng(tmp_coords[0],tmp_coords[1]));
                    map_circles[map_tmp_index].setRadius(parseInt(tmp_coords[2]));
                    map_circles[map_tmp_index].setEditable(false);
                }
                else {	// polygon edited is new
                    deleteCircle(map_tmp_index);
                }

                initButtonsStatus();
            }	

            initButtonsStatus();
        }

        $("#cancel_editing").attr("disabled", true);
        $('#basic_options :input').removeAttr('disabled');
        $("#allmarks *").removeAttr('disabled');	
        // enable submit form button
        $('#addmap_btn').removeAttr('disabled');
    });

    // add a new object when button is clicked, depending on radio selection
    $('#add_btn').on("click",function(){
        // get center of map 
        var center = map.getCenter();            
        var mzoom = map.getZoom();
        if (mzoom==0)
			mzoom = 1;
        var init_point = 10000/(mzoom*mzoom*mzoom*mzoom*mzoom);
        if ( $('input:radio[name=drawwhat]:checked').val() == 'line')	{	// add a new line 
            path_line = [];
            path_line.push(addCoordsToArray(center.lat()-init_point, center.lng()-init_point));
            path_line.push(addCoordsToArray(center.lat()+init_point, center.lng()+init_point));

            drawMapLine(path_line);
            enableEditingMode(true);			
        }
        else if ( $('input:radio[name=drawwhat]:checked').val() == 'polygon')	{
            path_poly = [];
            path_poly.push(addCoordsToArray(center.lat()-init_point, center.lng()-init_point));
            path_poly.push(addCoordsToArray(center.lat()-init_point, center.lng()+init_point));
            path_poly.push(addCoordsToArray(center.lat()+init_point, center.lng()+init_point));

            drawMapPoly(path_poly);
            enableEditingMode(true);	
        }	
        else if ( $('input:radio[name=drawwhat]:checked').val() == 'rectangle')	{
            path_rect = [];
            path_rect.push(addCoordsToArray(center.lat()-init_point, center.lng()-init_point));
            path_rect.push(addCoordsToArray(center.lat()+init_point, center.lng()+init_point));

            drawMapRect(path_rect);
            enableEditingMode(true);
        }
        else if ( $('input:radio[name=drawwhat]:checked').val() == 'circle')	{
            path_circle = [];
            var init_radius = 60000000/(mzoom*mzoom*mzoom*mzoom);
            drawMapCircle(center.lat(), center.lng(), init_radius);
            enableEditingMode(true);				
        }
        $("#allmarks *").attr("disabled", "disabled").off('click');
    });

    // add a new object when button is clicked, depending on radio selection
    $('#save_btn').on("click",function(){
        if ( $('input:radio[name=drawwhat]:checked').val() == 'line')	{	// add a new line 
            path_line = map_line.getPath();

            if (path_line.length > 0)	{
                //var pathstr = path_line.getArray();
                if ($('#line_'+map_polylines.indexOf(map_line)).length)	{
                    $('#line_coords_'+map_polylines.indexOf(map_line)).val(path_line.getArray().toString());
                }
                else  {
                    $('#allmarks').append('<div id="line_'+map_polylines.indexOf(map_line)+'_div"><input name="line_title[]" type="text" id="line_'+map_polylines.indexOf(map_line)+'" value="Line #'+map_polylines.indexOf(map_line)+'"><input name="line_coords[]" id="line_coords_'+map_polylines.indexOf(map_line)+'" type="hidden" value="' + path_line.getArray().toString() + '">&nbsp;&nbsp;<button type="button" class="btn btn-primary btn-xs" id="line_'+map_polylines.indexOf(map_line)+'_edit" onClick="editPolyline('+map_polylines.indexOf(map_line)+');" >Edit</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs" id="line_'+map_polylines.indexOf(map_line)+'_delete" onClick="deletePolyline('+map_polylines.indexOf(map_line)+');" >Remove</button></div>');	
                }
                setPolylineEditable(map_polylines.indexOf(map_line), false);
            }

            initButtonsStatus();
            $('#basic_options :input').removeAttr('disabled');
            $('#drawwhat_clearall').removeAttr('disabled');		
            $("#allmarks *").removeAttr('disabled');	
        }
        else if ( $('input:radio[name=drawwhat]:checked').val() == 'polygon')	{
            path_poly = map_polygon.getPath(); ;

            if (path_poly.length > 0)	{
                //var pathstr = path_poly.getArray();
                if ($('#poly_'+map_polygons.indexOf(map_polygon)).length)	{
                    $('#poly_coords_'+map_polygons.indexOf(map_polygon)).val(path_poly.getArray().toString());
                }
                else  {
                    $('#allmarks').append('<div id="poly_'+map_polygons.indexOf(map_polygon)+'_div"><input name="poly_title[]" type="text" id="poly_'+map_polygons.indexOf(map_polygon)+'" value="Polygon #'+map_polygons.indexOf(map_polygon)+'"><input name="poly_coords[]" id="poly_coords_'+map_polygons.indexOf(map_polygon)+'" type="hidden" value="' + path_poly.getArray().toString() + '">&nbsp;&nbsp;<button type="button" class="btn btn-primary btn-xs" id="poly_'+map_polygons.indexOf(map_polygon)+'_edit"  onClick="editPolygon('+map_polygons.indexOf(map_polygon)+');" >Edit</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs" id="poly_'+map_polygons.indexOf(map_polygon)+'_delete" onClick="deletePolygon('+map_polygons.indexOf(map_polygon)+');" >Remove</button></div>');	
                }
                setPolygonEditable(map_polygons.indexOf(map_polygon), false);
            }

            initButtonsStatus();
            $('#basic_options :input').removeAttr('disabled');
            $('#drawwhat_clearall').removeAttr('disabled');	
            $("#allmarks *").removeAttr('disabled');	
        }	
        else if ( $('input:radio[name=drawwhat]:checked').val() == 'rectangle')	{
            path_rect = map_rectangle.getBounds(); 

            if (path_rect.toString().length > 0)	{
                path_rect = removeParenthesis(path_rect.toString());
                //var pathstr = path_rect;
                if ($('#rect_'+map_rectangles.indexOf(map_rectangle)).length)	{
                    $('#rect_coords_'+map_rectangles.indexOf(map_rectangle)).val(path_rect.toString());
                }
                else  {
                        //alert(path_rect);
                    $('#allmarks').append('<div id="rect_'+map_rectangles.indexOf(map_rectangle)+'_div"><input name="rect_title[]" type="text" id="rect_'+map_rectangles.indexOf(map_rectangle)+'" value="Rectangle #'+map_rectangles.indexOf(map_rectangle)+'"><input name="rect_coords[]" id="rect_coords_'+map_rectangles.indexOf(map_rectangle)+'" type="hidden" value="' + path_rect.toString() + '">&nbsp;&nbsp;<button type="button" class="btn btn-primary btn-xs" id="rect_'+map_rectangles.indexOf(map_rectangle)+'_edit"  onClick="editRectangle('+map_rectangles.indexOf(map_rectangle)+');" >Edit</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs" id="rect_'+map_rectangles.indexOf(map_rectangle)+'_delete" onClick="deleteRectangle('+map_rectangles.indexOf(map_rectangle)+');" >Remove</button></div>');	
                }
                setRectangleEditable(map_rectangles.indexOf(map_rectangle), false);
            }

            initButtonsStatus();
            $('#basic_options :input').removeAttr('disabled');
            $('#drawwhat_clearall').removeAttr('disabled');	
            $("#allmarks *").removeAttr('disabled');	
        }
        else if ( $('input:radio[name=drawwhat]:checked').val() == 'circle')	{
            path_circle = map_circle.getCenter()+',('+map_circle.getRadius()+')';
            if (path_circle.length > 0)	{
                if ($('#circle_'+map_circles.indexOf(map_circle)).length)	{
                    $('#circle_coords_'+map_circles.indexOf(map_circle)).val(path_circle);
                }
                else  {
                    $('#allmarks').append('<div id="circle_'+map_circles.indexOf(map_circle)+'_div"><input name="circle_title[]" type="text" id="circle_'+map_circles.indexOf(map_circle)+'" value="Circle #'+map_circles.indexOf(map_circle)+'"><input name="circle_coords[]" id="circle_coords_'+map_circles.indexOf(map_circle)+'" type="hidden" value="' + path_circle + '">&nbsp;&nbsp;<button type="button" class="btn btn-primary btn-xs" id="circle_'+map_circles.indexOf(map_circle)+'_edit"  onClick="editCircle('+map_circles.indexOf(map_circle)+');" >Edit</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs" id="circle_'+map_circles.indexOf(map_circle)+'_delete" onClick="deleteCircle('+map_circles.indexOf(map_circle)+');" >Remove</button></div>');	
                }
                setCircleEditable(map_circles.indexOf(map_circle), false);
            }				
            initButtonsStatus();
            $('#basic_options :input').removeAttr('disabled');
            $('#drawwhat_clearall').removeAttr('disabled');	
            $("#allmarks *").removeAttr('disabled');					
        }

        // enable submit form button
        $('#addmap_btn').removeAttr('disabled');
    });		

    // Disable all line and polygon buttons when markers option is enabled
    $('#drawwhat_markers').on("click",function(){
        $("#add_btn").attr("disabled", true);
        $("#save_btn").attr("disabled", true);
        writeOnHelpbox('drawwhat_markers');
        showHideButtons(false);
    });

    // enable Add line button when if lines option  enabled
    $('#drawwhat_line').on("click",function(){
        $("#add_btn").removeAttr("disabled");
        $("#add_btn").text('Add line');	
        $("#save_btn").text('Save line');	
        writeOnHelpbox('drawwhat_line');
        showHideButtons(true);
    });		

    $('#drawwhat_polygon').on("click",function(){
        $("#add_btn").removeAttr("disabled");
        $("#add_btn").text('Add polygon');	
        $("#save_btn").text('Save polygon');	
        writeOnHelpbox('drawwhat_polygon');
        showHideButtons(true);
    });

    $('#drawwhat_rectangle').on("click",function(){
        $("#add_btn").removeAttr("disabled");
        $("#add_btn").text('Add rectangle');	
        $("#save_btn").text('Save rectangle');			
        writeOnHelpbox('drawwhat_rectangle');
        showHideButtons(true);
    });		

    $('#drawwhat_circle').on("click",function(){
        $("#add_btn").removeAttr("disabled");
        $("#add_btn").text('Add circle');	
        $("#save_btn").text('Save circle');				
        writeOnHelpbox('drawwhat_circle');
        showHideButtons(true);
    });	
    
    $('#drawwhat_route').on("click",function(){
        $("#add_btn").attr("disabled", true);
        $("#save_btn").attr("disabled", true);
        writeOnHelpbox('drawwhat_route');
        showHideButtons(false);
    });	    

    // Clear All button - delete all shapes on map and their input boxes
    $('#drawwhat_clearall').on("click",function(){
        var r = confirm("You are going to remove all shapes from map.\nAre you sure ?");
        if (r == true) {
            removeShapes(true);
        } 
    });		

    // add input boxes and options for every marker addition
    GMaps.on('marker_added', map, function(marker) {
        // do nothing
    });

    // add a marker on map if markers option is enabled
    GMaps.on('click', map.map, function(event) {
        if ( $('input:radio[name=drawwhat]:checked').val() == 'markers') {
            var lat = event.latLng.lat();
            var lng = event.latLng.lng();
            createMarker(lat, lng);
        }
        else if ( $('input:radio[name=drawwhat]:checked').val() == 'route') {
            var lat = event.latLng.lat();
            var lng = event.latLng.lng();
            createRoute(lat, lng);
        }
    });

});


// create a marker from given coords
function createRoute(lat, lng) {
    var point = $('#route_point').val();
    var newmarker = map.addMarker({
        lat: lat,
        lng: lng,
        title: point,
        draggable: true,
        icon: $('#current_marker_path').text()
    });
    
    if (point == 'start') {
        $("#helpbox").text("Click on map to place the end point of route");	
        $("#route_point").val("end");	
    }
    else if (point == 'end') {
        //paintRoute(map_tmp_route_start_lat, map_tmp_route_start_lng, lat, lng);
        paintRoute(map.markers.indexOf(newmarker));
        $("#helpbox").text("Click on map to place the start point of route");	
        $("#route_point").val("start");
    }
}

function paintRoute(index) {
    var start = map.markers[index].getPosition();
    var end = map.markers[index-1].getPosition();
    var end_icon = map.markers[index].getIcon();
    var start_icon = map.markers[index-1].getIcon();
    
    var newroute = map.drawRoute({
        origin: [start.lat(), start.lng()],
        destination: [end.lat(), end.lng()],
        travelMode: 'driving',
        strokeColor: '#131540',
        strokeOpacity: 0.6,
        strokeWeight: 6
    });

    map_routes.push(newroute);
    var r_index = map_routes.length;
    //$('#allmarks').append('<div id="route_'+r_index+'_div"><input name="route_title[]" type="text" id="route_'+index+'" value="Route #'+r_index+'"><input name="route_start[]" class="route_start" id="route_start_'+index+'" type="hidden" value="'+start.lat()+','+start.lng()+'">&nbsp;&nbsp;<input name="route_end[]" class="route_end" id="route_end_'+index+'" type="hidden" value="'+end.lat()+','+end.lng()+'">&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs" id="circle_'+index+'_delete" onClick="deleteRoute('+index+', '+r_index+');" >Remove</button></div>');
    $('#allmarks').append('<div id="route_'+r_index+'_div"><input name="route_title[]" type="text" id="route_'+index+'" value="Route #'+r_index+'"><input name="route_start[]" class="route_start" id="route_start_'+index+'" type="hidden" value="'+start.lat()+','+start.lng()+'">&nbsp;&nbsp;<input name="route_end[]" class="route_end" id="route_end_'+index+'" type="hidden" value="'+end.lat()+','+end.lng()+'"><input name="route_marker_icon[]" id="route_marker_icon_'+index+'" type="hidden" value="' + start_icon+','+end_icon + '">&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs" id="circle_'+index+'_delete" onClick="deleteRoute('+index+', '+r_index+');" >Remove</button></div>');
} 

function deleteRoute(index, r_index) {
    $('#route_'+r_index+'_div').remove();
    map.markers[index].setMap(null);
    map.markers[index-1].setMap(null);
    
    // clean all routes
    map.cleanRoute();
    // draw routes left
    for (var i=0; i<=map_routes.length; i++)	{ 
        //console.log(i+' - '+map_routes.length);
        if ( $( '#route_'+i+'_div' ).length ) {
            var start = $('#route_'+i+'_div .route_start').val();
            var end = $('#route_'+i+'_div .route_end').val();
            var start_arr = start.split(',');
            var end_arr = end.split(',');
            
            var newroute = map.drawRoute({
                origin: [start_arr[0],start_arr[1]],
                destination: [end_arr[0],end_arr[1]],
                travelMode: 'driving',
                strokeColor: '#131540',
                strokeOpacity: 0.6,
                strokeWeight: 6
            });    
            map_routes.push(newroute);
        }
    }
}

/**
 * Paint/draw the route between 2 points
 * 
 * @param {type} index
 * @returns {undefined}
 *
function paintRoute(s_lat, s_lng, e_lat, e_lng) {
    var newroute = map.drawRoute({
        origin: [s_lat, s_lng],
        destination: [e_lat, e_lng],
        travelMode: 'driving',
        strokeColor: '#131540',
        strokeOpacity: 0.6,
        strokeWeight: 6
    });

    map_routes.push(newroute);
    var index = map_routes.length;
    $('#allmarks').append('<div id="route_'+index+'_div">');
    $('#allmarks').append('<input name="route_title[]" type="text" id="route_'+index+'" value="Route #'+index+'">');
    $('#allmarks').append('<input name="route_start[]" id="route_start_'+index+'" type="text" value="'+s_lat+','+s_lng+'">&nbsp;&nbsp;');
    $('#allmarks').append('<input name="route_end[]" id="route_end_'+index+'" type="text" value="'+e_lat+','+e_lng+'">&nbsp;&nbsp;');
    $('#allmarks').append('<button type="button" class="btn btn-danger btn-xs" id="circle_'+index+'_delete" onClick="deleteRoute('+index+');" >Remove</button>');
    $('#allmarks').append('</div>');	
} 
*/

// if true, edit on enable mode so enable/disable all involved options and buttons
function enableEditingMode(what) {
    if (what)	{
        $("#add_btn").attr("disabled", true);
        $("#save_btn").removeAttr("disabled");
        $("#cancel_editing").removeAttr("disabled");
        $('#basic_options :input').attr('disabled', true);
        $("#drawwhat_clearall").attr("disabled", true);	
        $("#addmap_btn").attr("disabled", true);	
    }
}

// initialize status of buttons on page load and after editing an element
function initButtonsStatus() {
    // set initial status of buttons
    if ( $('input:radio[name=drawwhat]:checked').val() == 'markers')	{
        $("#add_btn").attr("disabled", true);
        $("#save_btn").attr("disabled", true);
        $("#cancel_editing").attr("disabled", true);
        $("#drawwhat_clearall").removeAttr("disabled");
        writeOnHelpbox('drawwhat_markers');
        showHideButtons(false);
    }	
    else if ( $('input:radio[name=drawwhat]:checked').val() == 'line')	{
        $("#add_btn").removeAttr("disabled");
        $("#save_btn").attr("disabled", true);
        $("#add_btn").text('Add line');	
        $("#save_btn").text('Save line');	
        $("#cancel_editing").attr("disabled", true);
        $("#drawwhat_clearall").removeAttr("disabled");
        writeOnHelpbox('drawwhat_line');
        showHideButtons(true);
    }
    else if ( $('input:radio[name=drawwhat]:checked').val() == 'polygon')	{
        $("#add_btn").removeAttr("disabled");
        $("#save_btn").attr("disabled", true);
        $("#add_btn").text('Add polygon');	
        $("#save_btn").text('Save polygon');	
        $("#cancel_editing").attr("disabled", true);	
        $("#drawwhat_clearall").removeAttr("disabled");
        writeOnHelpbox('drawwhat_polygon');
        showHideButtons(true);
    }	
    else if ( $('input:radio[name=drawwhat]:checked').val() == 'rectangle')	{
        $("#add_btn").removeAttr("disabled");
        $("#save_btn").attr("disabled", true);
        $("#add_btn").text('Add rectangle');	
        $("#save_btn").text('Save rectangle');	
        $("#cancel_editing").attr("disabled", true);	
        $("#drawwhat_clearall").removeAttr("disabled");
        writeOnHelpbox('drawwhat_rectangle');		
        showHideButtons(true);	
    }
    else if ( $('input:radio[name=drawwhat]:checked').val() == 'circle')	{
        $("#add_btn").removeAttr("disabled");
        $("#save_btn").attr("disabled", true);
        $("#add_btn").text('Add circle');	
        $("#save_btn").text('Save circle');			
        $("#cancel_editing").attr("disabled", true);	
        $("#drawwhat_clearall").removeAttr("disabled");	
        writeOnHelpbox('drawwhat_circle');
        showHideButtons(true);
    }
}	

// show or hide add/delete/cancel button
function showHideButtons(what) {
    if (what)	{
        $("#add_btn").css("display", "inline");
        $("#save_btn").css("display", "inline");
        $("#cancel_editing").css("display", "inline");
        $("#markers_cpanel").css("display", "none");
    }
    else {
        $("#add_btn").css("display", "none");
        $("#save_btn").css("display", "none");
        $("#cancel_editing").css("display", "none");
        $("#markers_cpanel").css("display", "inline");
    }
}

// get path from a a given array of coords, it works for polylines and polygons
function getPathFromCoordsArray(tmp_str) {
    var tmp_array = [];
    tmp_array = tmp_str.split("),(");	
    var tmp_coords = [];

    for (var i=0; i<tmp_array.length; i++)	{
        var tmp_array_2 = tmp_array[i];
        var tmp_arr_value = tmp_array_2.trim();
        var tmp_str_2;

        if ((tmp_arr_value.indexOf("(")) === 0)	{
            tmp_str_2 = tmp_arr_value.slice(1)
        }
        else if (tmp_arr_value.indexOf(")") >=0)	{
            tmp_str_2 = tmp_arr_value.substring(0, tmp_arr_value.length-1)		
        }
        else {
            tmp_str_2 = tmp_arr_value;
        }            

        var tmp_array_3 = [];
        tmp_array_3 = tmp_str_2.split(",");

        tmp_coords.push(new google.maps.LatLng(tmp_array_3[0], tmp_array_3[1]));
    }	

    return tmp_coords;
}

// get circle data from a given array of coords
function getDataFromArray(tmp_str) {
    var tmp_array = [];
    tmp_array = tmp_str.split(",");	
    var tmp_coords = [];

    for (var i=0; i<tmp_array.length; i++)	{
        var tmp_str_2 = tmp_array[i];
        var tmp_arr_value = tmp_str_2.trim();
        if ((tmp_arr_value.indexOf("(")) === 0)	{
            tmp_arr_value = tmp_arr_value.slice(1)
        }
        if (tmp_arr_value.indexOf(")") >=0)	{
            tmp_arr_value = tmp_arr_value.substring(0, tmp_arr_value.length-1)		
        }
        tmp_coords[i] = tmp_arr_value;
    }	

    return tmp_coords;
}	

// remove parenthesis of string
function removeParenthesis(initial_str) {
    var tmp_str;

    if ((initial_str.indexOf("(")) === 0)	{
        tmp_str = initial_str.slice(1)
    }
    if (tmp_str.indexOf(")") >=0)	{
        tmp_str = tmp_str.substring(0, tmp_str.length-1)		
    }

    return tmp_str;
}		

// show message on helpbox
function writeOnHelpbox(objectclicked) {
    if (objectclicked == 'drawwhat_markers')	{
        $("#helpbox").text('Click on map to place a marker');	
    }
    else if (objectclicked == 'drawwhat_line')	{
        $("#helpbox").text("Click on 'Add line' button to draw a polyline");	
    }
    else if (objectclicked == 'drawwhat_polygon')	{
        $("#helpbox").text("Click on 'Add polygon' button to draw a polygon");	
    }
    else if (objectclicked == 'drawwhat_rectangle')	{
        $("#helpbox").text("Click on 'Add rectangle' button to draw a rectangle");	
    }
    else if (objectclicked == 'drawwhat_circle')	{
        $("#helpbox").text("Click on 'Add circle' button to draw a circle");	
    }
    else if (objectclicked == 'drawwhat_route')	{
        $("#helpbox").text("Click on map to place the start point of route");	
        $("#route_point").val("start");	
    }    
    else {
        $("#helpbox").text("");	
    }
}

// add given coords to an array and return this array
function addCoordsToArray(lat, lng) {
    var shapeCoords = new Array();

    shapeCoords.push(lat);    
    shapeCoords.push(lng); 
    return shapeCoords;
}

// create a marker from given coords
function createMarker(lat, lng) {
    var index = map.markers.length;
    var newmarker = map.addMarker({
        lat: lat,
        lng: lng,
        title: 'Marker #' + index,
        draggable: true,
        icon: $('#current_marker_path').text(),
        dragend: function(e) {
            $('#marker_coords_'+index).val('('+this.getPosition().lat()+','+this.getPosition().lng()+')');
            $('#marker_icon_'+index).val(this.getIcon());
        }
    });		
    
    $('#allmarks').append('<div id="marker_'+map.markers.indexOf(newmarker)+'_div"><input name="marker_title[]" type="text" id="marker_'+map.markers.indexOf(newmarker)+'" value="'+newmarker.getTitle()+'"><input class="marker_coords" name="marker_coords[]" id="marker_coords_'+map.markers.indexOf(newmarker)+'" type="text" value="(' + newmarker.getPosition().lat() + ',' + newmarker.getPosition().lng() + ')"><input name="marker_icon[]" id="marker_icon_'+map.markers.indexOf(newmarker)+'" type="hidden" value="' + newmarker.getIcon() + '">&nbsp;&nbsp;<button type="button" class="btn btn-info btn-xs pan-to-marker" id="marker_'+map.markers.indexOf(newmarker)+'_cnr" data-marker-lat="' + newmarker.getPosition().lat() + '" data-marker-lng="' + newmarker.getPosition().lng() + '">Show marker</button>&nbsp;&nbsp;<button id="marker_'+map.markers.indexOf(newmarker)+'_chm" type="button" class="btn btn-xs btn-primary" data-toggle="popover" title="Select Icon" data-content="" data-placement="top">Change icon</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs" id="marker_'+map.markers.indexOf(newmarker)+'_rmv" href="#" onClick="deleteMarker('+map.markers.indexOf(newmarker)+');">Remove</button></div>');
    
    // make reverse geolocation for assigning address to marker
    geocoder = new google.maps.Geocoder();
    var latlng = new google.maps.LatLng(newmarker.getPosition().lat(),newmarker.getPosition().lng());
    geocoder.geocode({'latLng': latlng}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            //Check result 0
            var result = results[0];

            if (result.formatted_address.length > 0) {
                $('#marker_'+map.markers.indexOf(newmarker)).val(result.formatted_address);
            }
        } 
    });

    $('#marker_'+map.markers.indexOf(newmarker)+'_chm').on('show.bs.popover', function () {
      $('#what_todo').html(map.markers.indexOf(newmarker));
    })	
    $('#marker_'+map.markers.indexOf(newmarker)+'_chm').on('hidden.bs.popover', function () {
      $('#what_todo').html('');
    })
    $('#marker_'+map.markers.indexOf(newmarker)+'_chm').popover({
        html : true, 
        content: function() {
            return $('#markers_list_content').html();
        },
    });    
}

// delete a marker and its input boxes
function deleteMarker(index) {
    map.markers[index].setMap(null);
    deleteObjectsInputs('#marker_'+index);
    deleteObjectsInputs('#marker_coords_'+index);
    deleteObjectsInputs('#marker_'+index+'_cnr');
    deleteObjectsInputs('#marker_'+index+'_rmv');
    deleteObjectsInputs('#marker_'+index+'_div');
}  

// delete a css object from a given id
function deleteObjectsInputs(index) {
    $(index).remove();
} 

// remove objects on map and their input boxes
function removeShapes(rm_markers) {
    // find all polylines and remove them and their input boxes
    for (var i=0; i<map_polylines.length; i++)	{
        deletePolyline(i);
    }		
    path_line = [];
    map_polylines = [];

    // find all polygons and remove them and their input boxes
    for (var i=0; i<map_polygons.length; i++)	{
        deletePolygon(i);
    }
    path_poly = [];
    map_polygons = [];

    // find all rectangles and remove them and their input boxes
    for (var i=0; i<map_rectangles.length; i++)	{
        deleteRectangle(i);
    }		
    path_rect = [];
    map_rectangles = [];		

    // find all circle and remove them and their input boxes
    for (var i=0; i<map_circles.length; i++)	{
        deleteCircle(i);
    }		
    path_circle = [];
    map_circles = [];

    // find all markers and remove them and their input boxes
    if (rm_markers) {	// if enabled
        var tmp_array = map.markers.length;
        for (var i=0; i<tmp_array; i++)	{
                deleteMarker(i);
        }		
    }
    
    // get routes no
    var routes_no = map_routes.length;
    // remove routes, if any
    map.cleanRoute();
    
    // draw routes left
    for (var i=0; i<=routes_no; i++)	{ 
        //console.log(i+' - '+map_routes.length);
        if ( $( '#route_'+i+'_div' ).length ) {
            $('#route_'+i+'_div').remove();
        }
    }    
}

// draw a polyline from a given path
function drawMapLine(path) {
    var lindex = map_polylines.length;
    map_tmp_index = lindex; // assign line index for the case of canceling
    map_line = map.drawPolyline({
        path: path,
        index: lindex,
        editable: true,
        draggable: true,
        strokeColor: '#131540',
        strokeOpacity: 0.7,
        strokeWeight: 6,
    });
    map_polylines[map_polylines.length] = map_line;
}

// draw a polygon from a given path
function drawMapPoly(path) {
    var pindex = map_polygons.length;
    map_tmp_index = pindex; // assign line polygon for the case of canceling
    map_polygon = map.drawPolygon({
        paths: path, // pre-defined polygon shape
        index: pindex,
        strokeColor: '#f4183d',
        editable: true,
        draggable: true,
        strokeOpacity: 1,
        strokeWeight: 3,
        fillColor: '#f4183d',
        fillOpacity: 0.7
    });	
    map_polygons[map_polygons.length] = map_polygon;
}	

// draw a rectable from a given path
function drawMapRect(path) {
    var rindex = map_rectangles.length;
    map_tmp_index = rindex; // assign rectable index for the case of canceling
    map_rectangle = map.drawRectangle({
        bounds: path, // pre-defined rectangle shape
        index: rindex,
        strokeColor: '#f4183d',
        editable: true,
        draggable: true,
        strokeOpacity: 1,
        strokeWeight: 3,
        fillColor: '#f4183d',
        fillOpacity: 0.7
    });	
    map_rectangles[map_rectangles.length] = map_rectangle;
}

// draw a circle from a given path
function drawMapCircle(lat, lng, radius) {
    var cindex = map_circles.length;
    map_tmp_index = cindex; // assign circle
    map_circle = map.drawCircle({
        lat: lat,
        lng: lng,
        radius: radius,
        index: cindex,
        strokeColor: '#f4183d',
        editable: true,
        draggable: true,
        strokeOpacity: 1,
        strokeWeight: 3,
        fillColor: '#f4183d',
        fillOpacity: 0.7
    });	
    map_circles[map_circles.length] = map_circle;
}		

// set a polyline editable option as true or false
function setPolylineEditable(index, what) {
    map_line = map_polylines[index];
    map_line.setDraggable(what);
    map_line.setEditable(what);
} 

// set a polyline editable option as true and alter related buttons
function editPolyline(index) {
    $('input:radio[name=drawwhat][value=line]').click();
    map_tmp_index = index; // assign line index for the case of canceling
    setPolylineEditable(index, true);
    $("#add_btn").attr("disabled", true);
    $("#save_btn").removeAttr("disabled");	
    $("#cancel_editing").removeAttr("disabled");
    $('#basic_options :input').attr('disabled', true);	
    $('#drawwhat_clearall').attr('disabled', true);	
    $("#allmarks *").attr("disabled", "disabled").off('click');
    $("#addmap_btn").attr("disabled", true);
} 	

// delete a polyline and its input boxes
function deletePolyline(index) {
    map_polylines[index].setMap(null);
    deleteObjectsInputs('#line_'+index);
    deleteObjectsInputs('#line_coords_'+index);	
    deleteObjectsInputs('#line_'+index+'_edit');		
    deleteObjectsInputs('#line_'+index+'_delete');
    deleteObjectsInputs('#line_'+index+'_div');		
} 

// set a polygon editable option as true or false
function setPolygonEditable(index, what) {
    map_polygon = map_polygons[index];
    map_polygon.setDraggable(what);
    map_polygon.setEditable(what);
} 

// set a polygon editable option as true and alter related buttons
function editPolygon(index) {
    $('input:radio[name=drawwhat][value=polygon]').click();
    map_tmp_index = index; // assign line index for the case of canceling
    setPolygonEditable(index, true);
    $("#add_btn").attr("disabled", true);
    $("#save_btn").removeAttr("disabled");			
    $("#cancel_editing").removeAttr("disabled");
    $('#basic_options :input').attr('disabled', true);
    $('#drawwhat_clearall').attr('disabled', true);		
    $("#allmarks *").attr("disabled", "disabled").off('click');		
    $("#addmap_btn").attr("disabled", true);
} 	

// delete a polygon and its input boxes
function deletePolygon(index) {
    map_polygons[index].setMap(null);
    deleteObjectsInputs('#poly_'+index);
    deleteObjectsInputs('#poly_coords_'+index);	
    deleteObjectsInputs('#poly_'+index+'_edit');		
    deleteObjectsInputs('#poly_'+index+'_delete');
    deleteObjectsInputs('#poly_'+index+'_div');		
} 

// set a rectangle editable option as true or false
function setRectangleEditable(index, what) {
    map_rectangle = map_rectangles[index];
    map_rectangle.setDraggable(what);
    map_rectangle.setEditable(what);
}	

// set a rectangle editable option as true and alter related buttons
function editRectangle(index) {
    $('input:radio[name=drawwhat][value=rectangle]').click();
    map_tmp_index = index; // assign line index for the case of canceling
    setRectangleEditable(index, true);
    $("#add_btn").attr("disabled", true);
    $("#save_btn").removeAttr("disabled");			
    $("#cancel_editing").removeAttr("disabled");
    $('#basic_options :input').attr('disabled', true);
    $('#drawwhat_clearall').attr('disabled', true);	
    $("#allmarks *").attr("disabled", "disabled").off('click');	
    $("#addmap_btn").attr("disabled", true);		
} 	

// delete a rectangle and its input boxes
function deleteRectangle(index) {
    map_rectangles[index].setMap(null);
    deleteObjectsInputs('#rect_'+index);
    deleteObjectsInputs('#rect_coords_'+index);	
    deleteObjectsInputs('#rect_'+index+'_edit');		
    deleteObjectsInputs('#rect_'+index+'_delete');
    deleteObjectsInputs('#rect_'+index+'_div');		
} 	

// set a circle editable option as true or false
function setCircleEditable(index, what) {
    map_circle = map_circles[index];
    map_circle.setDraggable(what);
    map_circle.setEditable(what);
} 	

// set a circle editable option as true and alter related buttons
function editCircle(index) {
    $('input:radio[name=drawwhat][value=circle]').click();
    map_tmp_index = index; // assign circle index for the case of canceling
    setCircleEditable(index, true);
    $("#add_btn").attr("disabled", true);
    $("#save_btn").removeAttr("disabled");			
    $("#cancel_editing").removeAttr("disabled");
    $('#basic_options :input').attr('disabled', true);
    $('#drawwhat_clearall').attr('disabled', true);	
    $("#allmarks *").attr("disabled", "disabled").off('click');		
    $("#addmap_btn").attr("disabled", true);	
} 	

// delete a circle and its input boxes
function deleteCircle(index) {
    map_circles[index].setMap(null);
    deleteObjectsInputs('#circle_'+index);
    deleteObjectsInputs('#circle_coords_'+index);	
    deleteObjectsInputs('#circle_'+index+'_edit');		
    deleteObjectsInputs('#circle_'+index+'_delete');
    deleteObjectsInputs('#circle_'+index+'_div');		
} 	

///////////////// load map related functions //////////////////
// load a marker from db
function loadMarker(lat, lng, id, title, marker_icon, draggable_state) {
    var index = map.markers.length;
    var newmarker = map.addMarker({
        lat: lat,
        lng: lng,
        title: title,
        icon: marker_icon,
        draggable: draggable_state,
        dragend: function(e) {
            $('#marker_coords_'+index).val('('+this.getPosition().lat()+','+this.getPosition().lng()+')');
            $('#marker_icon_'+index).val(this.getIcon());
        },			
        infoWindow: {
          content: title
        }
    });	
    map_markers.push(newmarker);
    
    if (draggable_state) {
        $('#allmarks').append('<div id="marker_'+map.markers.indexOf(newmarker)+'_div"><input name="marker_id[]" type="hidden" value="'+id+'"><input name="marker_title[]" type="text" id="marker_'+map.markers.indexOf(newmarker)+'" value="'+newmarker.getTitle()+'"><input class="marker_coords" name="marker_coords[]" id="marker_coords_'+map.markers.indexOf(newmarker)+'" type="text" value="(' + newmarker.getPosition().lat() + ',' + newmarker.getPosition().lng() + ')"><input name="marker_icon[]" id="marker_icon_'+map.markers.indexOf(newmarker)+'" type="hidden" value="' + newmarker.getIcon() + '">&nbsp;&nbsp;<button type="button" class="btn btn-info btn-xs pan-to-marker" id="marker_'+map.markers.indexOf(newmarker)+'_cnr" data-marker-lat="' + newmarker.getPosition().lat() + '" data-marker-lng="' + newmarker.getPosition().lng() + '">Show marker</button>&nbsp;&nbsp;<button id="marker_'+map.markers.indexOf(newmarker)+'_chm" type="button" class="btn btn-xs btn-primary" data-toggle="popover" title="Select Icon" data-content="" data-placement="top">Change icon</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs" id="marker_'+map.markers.indexOf(newmarker)+'_rmv" href="#" onClick="deleteMarker('+map.markers.indexOf(newmarker)+');">Remove</button></div>');

        $('#marker_'+map.markers.indexOf(newmarker)+'_chm').on('show.bs.popover', function () {
          $('#what_todo').html(map.markers.indexOf(newmarker));
        })	
        $('#marker_'+map.markers.indexOf(newmarker)+'_chm').on('hidden.bs.popover', function () {
          $('#what_todo').html('');
        })
        $('#marker_'+map.markers.indexOf(newmarker)+'_chm').popover({
            html : true, 
            content: function() {
                return $('#markers_list_content').html();
            },
        });        
    }
    //alert(marker_icon);
    return newmarker;
}		

// draw a polyline from a given path
function loadPolyline(path, title, id) {
    var lindex = map_polylines.length;
    map_line = map.drawPolyline({
        path: path,
        editable: false,
        index: lindex,
        draggable: false,
        strokeColor: '#131540',
        strokeOpacity: 0.6,
        strokeWeight: 6,
        click: function(e) {
            createOverlay(e.latLng.lat(),e.latLng.lng(),title, 0);
        }
    });
    map_polylines[map_polylines.length] = map_line;

    path_line = map_line.getPath();
    $('#allmarks').append('<div id="line_'+map_polylines.indexOf(map_line)+'_div"><input name="line_id[]" type="hidden" value="'+id+'"><input name="line_title[]" type="text" id="line_'+map_polylines.indexOf(map_line)+'" value="'+title+'"><input name="line_coords[]" id="line_coords_'+map_polylines.indexOf(map_line)+'" type="hidden" value="' + path + '">&nbsp;&nbsp;<button type="button" class="btn btn-primary btn-xs" id="line_'+map_polylines.indexOf(map_line)+'_edit" onClick="editPolyline('+map_polylines.indexOf(map_line)+');" >Edit</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs" id="line_'+map_polylines.indexOf(map_line)+'_delete" onClick="deletePolyline('+map_polylines.indexOf(map_line)+');" >Remove</button></div>');	


    var bounds = new google.maps.LatLngBounds();
    var path = map_line.getPath();
    for (var i = 0; i < path.getLength(); i++) {
        bounds.extend(path.getAt(i));
    }
    return bounds;
}	

// draw a polygon from a given path
function loadPolygon(path, title, id) {
    var pindex = map_polygons.length;
    map_polygon = map.drawPolygon({
        paths: path, // pre-defined polygon shape
        index: pindex,
        strokeColor: '#f4183d',
        editable: false,
        draggable: false,
        strokeOpacity: 1,
        strokeWeight: 3,
        fillColor: '#f4183d',
        fillOpacity: 0.6,
        click: function(e) {
            createOverlay(e.latLng.lat(),e.latLng.lng(),title, calculatePolygonArea(map_polygon));
        }
    });
    map_polygons[map_polygons.length] = map_polygon;

    path_poly = map_polygon.getPath();
    $('#allmarks').append('<div id="poly_'+map_polygons.indexOf(map_polygon)+'_div"><input name="poly_id[]" type="hidden" value="'+id+'"><input name="poly_title[]" type="text" id="poly_'+map_polygons.indexOf(map_polygon)+'" value="'+title+'"><input name="poly_coords[]" id="poly_coords_'+map_polygons.indexOf(map_polygon)+'" type="hidden" value="' + path + '">&nbsp;&nbsp;<button type="button" class="btn btn-primary btn-xs" id="poly_'+map_polygons.indexOf(map_polygon)+'_edit"  onClick="editPolygon('+map_polygons.indexOf(map_polygon)+');" >Edit</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs" id="poly_'+map_polygons.indexOf(map_polygon)+'_delete" onClick="deletePolygon('+map_polygons.indexOf(map_polygon)+');" >Remove</button></div>');	

    var bounds = new google.maps.LatLngBounds();
    var path = map_polygon.getPath();
    for (var i = 0; i < path.getLength(); i++) {
        bounds.extend(path.getAt(i));
    }
    return bounds;		
}	

// calculate area for a given polygon
function calculatePolygonArea(polygon) {
    var z = google.maps.geometry.spherical.computeArea(polygon.getPath().getArray());

    return z.toFixed(2);
}    

// draw a rectangle from a given path
function loadRectangle(path, title, id) {
    var rindex = map_rectangles.length;
    map_rectangle = map.drawRectangle({
        bounds: path, // pre-defined rectangle shape
        index: rindex,
        strokeColor: '#f4183d',
        editable: false,
        draggable: false,
        strokeOpacity: 1,
        strokeWeight: 3,
        fillColor: '#f4183d',
        fillOpacity: 0.6,
        click: function(e) {
            createOverlay(e.latLng.lat(),e.latLng.lng(),title, calculateRectangleArea(map_rectangle));
        }
    });	
    map_rectangles[map_rectangles.length] = map_rectangle;

    path_rect = map_rectangle.getBounds(); 
    $('#allmarks').append('<div id="rect_'+map_rectangles.indexOf(map_rectangle)+'_div"><input name="rect_id[]" type="hidden" value="'+id+'"><input name="rect_title[]" type="text" id="rect_'+map_rectangles.indexOf(map_rectangle)+'" value="'+title+'"><input name="rect_coords[]" id="rect_coords_'+map_rectangles.indexOf(map_rectangle)+'" type="hidden" value="' + path + '">&nbsp;&nbsp;<button type="button" class="btn btn-primary btn-xs" id="rect_'+map_rectangles.indexOf(map_rectangle)+'_edit"  onClick="editRectangle('+map_rectangles.indexOf(map_rectangle)+');" >Edit</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs" id="rect_'+map_rectangles.indexOf(map_rectangle)+'_delete" onClick="deleteRectangle('+map_rectangles.indexOf(map_rectangle)+');" >Remove</button></div>');	

    return map_rectangle.getBounds();
}	

// calculate area for a given rectangle
function calculateRectangleArea(rectangle) {
    var tmp = map_rectangle.getBounds();
    var sw = tmp.getSouthWest();
    var ne = tmp.getNorthEast();
    var southWest = new google.maps.LatLng(sw.lat(), sw.lng());
    var northEast = new google.maps.LatLng(ne.lat(), ne.lng());
    var southEast = new google.maps.LatLng(sw.lat(), ne.lng());
    var northWest = new google.maps.LatLng(ne.lat(), sw.lng());
    var z = google.maps.geometry.spherical.computeArea([northEast, northWest, southWest, southEast]);

    return z.toFixed(2);
}     

// draw a circle from a given path
function loadCircle(lat, lng, radius, title, id) {
    var cindex = map_circles.length;
    map_circle = map.drawCircle({
        lat: lat,
        lng: lng,
        radius: radius,
        index: cindex,
        strokeColor: '#f4183d',
        editable: false,
        draggable: false,
        strokeOpacity: 1,
        strokeWeight: 3,
        fillColor: '#f4183d',
        fillOpacity: 0.6,
        click: function(e) {
            createOverlay(e.latLng.lat(),e.latLng.lng(),title, calculateCircleArea(map_circle));
        }
    });	
    map_circles[map_circles.length] = map_circle;

    path_circle = map_circle.getCenter()+',('+map_circle.getRadius()+')';
    $('#allmarks').append('<div id="circle_'+map_circles.indexOf(map_circle)+'_div"><input name="circle_id[]" type="hidden" value="'+id+'"><input name="circle_title[]" type="text" id="circle_'+map_circles.indexOf(map_circle)+'" value="'+title+'"><input name="circle_coords[]" id="circle_coords_'+map_circles.indexOf(map_circle)+'" type="hidden" value="' + path_circle + '">&nbsp;&nbsp;<button type="button" class="btn btn-primary btn-xs" id="circle_'+map_circles.indexOf(map_circle)+'_edit"  onClick="editCircle('+map_circles.indexOf(map_circle)+');" >Edit</button>&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs" id="circle_'+map_circles.indexOf(map_circle)+'_delete" onClick="deleteCircle('+map_circles.indexOf(map_circle)+');" >Remove</button></div>');	

    return map_circle.getBounds();
}	

// draw a roote from a start & end
function loadRoute(start_lat, start_lng, end_lat, end_lng, title, id, start_icon, end_icon) {
    var start_marker = map.addMarker({
        lat: start_lat,
        lng: start_lng,
        title: title,
        draggable: false,
        icon: start_icon
    });
    
    var end_marker = map.addMarker({
        lat: end_lat,
        lng: end_lng,
        title: title,
        draggable: false,
        icon: end_icon
    });  
  
    var newroute = map.drawRoute({
        origin: [start_lat, start_lng],
        destination: [end_lat, end_lng],
        travelMode: 'driving',
        strokeColor: '#131540',
        strokeOpacity: 0.6,
        strokeWeight: 6
    });

    map_routes.push(newroute);
    var index = map.markers.indexOf(end_marker);
    var r_index = map_routes.length;
    $('#allmarks').append('<div id="route_'+r_index+'_div"><input name="route_id[]" type="hidden" value="'+id+'"><input name="route_title[]" type="text" id="route_'+index+'" value="Route #'+r_index+'"><input name="route_start[]" class="route_start" id="route_start_'+index+'" type="hidden" value="'+start_lat+','+start_lng+'">&nbsp;&nbsp;<input name="route_end[]" class="route_end" id="route_end_'+index+'" type="hidden" value="'+end_lat+','+end_lng+'">&nbsp;&nbsp;<button type="button" class="btn btn-danger btn-xs" id="circle_'+index+'_delete" onClick="deleteRoute('+index+', '+r_index+');" >Remove</button></div>');
}

// calculate area for a given circle
function calculateCircleArea(circle) {
    var R = circle.getRadius();
    var A = Math.PI*R*R;

    return A.toFixed(2);
}    

// create an overlay for each object clicked
function createOverlay(lat, lng, title, shape_area)	{
    for (var i=0; i<map_overlays.length; i++)	{
        //removeOverlay(i);
        map_overlays[i].setMap(null);
    }		

    var shape_area_txt = '';
    if (!isNaN(parseFloat(shape_area)) && shape_area>0)
        shape_area_txt = '<br />Area: '+shape_area+' sq.m.';

        var contentString = '<div class="overlay">'+
        '<div id="siteNotice">'+
        '</div>'+
        '<div id="bodyContent">'+
        '<p><b>' +title+ '</p></b>'+
        '<p>' +shape_area_txt+ '</p>'+
        '<p><b>Price :0 CIT </b></p>'+
        '<button onclick="mySuperFunction()" type="button" class="btn btn-success btn-lg btn-block">Buy Now</button>'+

        
        '</div>'+
        '</div>';






    var overl = map.drawOverlay({
        lat: lat,
        lng: lng,
        content: contentString,

    });		

  

    
      overl.addListener('click', function() {
        infowindow.open(map, overl);   });



    map_overlays[map_overlays.length] = overl;
}	


