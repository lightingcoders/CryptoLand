// initialize status
$(document).ready(function(){

/*	comment here as it's moved to the view page
	$('#chbx_markers').click(function() {
		if ($(this).is(':checked'))
			showOverlays(map_markers);
		else
			clearOverlays(map_markers);
	});	 
*/ 
    $('#chbx_polylines').click(function() {
        if ($(this).is(':checked'))
            showOverlays(map_polylines);
        else
            clearOverlays(map_polylines);
    });	
    $('#chbx_polygons').click(function() {
        if ($(this).is(':checked'))
            showOverlays(map_polygons);
        else
            clearOverlays(map_polygons);
    });	
    $('#chbx_rectangles').click(function() {
        if ($(this).is(':checked'))
            showOverlays(map_rectangles);
        else
            clearOverlays(map_rectangles);
    });	
    $('#chbx_circles').click(function() {
        if ($(this).is(':checked'))
            showOverlays(map_circles);
        else
            clearOverlays(map_circles);
    });
    	
});

function clearOverlays(arrMarkers) {
  if (arrMarkers) {
        for( var i = 0, n = arrMarkers.length; i < n; ++i ) {
          //arrMarkers[i].setMap(null);
          arrMarkers[i].setVisible(false);
        }
  }
}    

function showOverlays(arrMarkers) {
  if (arrMarkers) {
        for( var i = 0, n = arrMarkers.length; i < n; ++i ) {
                //arrMarkers[i].setMap(map);
                arrMarkers[i].setVisible(true);
        }
  }
}	
