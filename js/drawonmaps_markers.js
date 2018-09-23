// initialize status
$(document).ready(function(){

	// load popover window with markers
    $('[data-toggle="popover"]').popover({
        html : true, 
        content: function() {
          return $('#markers_list_content').html();
        },
    });
    /*
	$('body').on('click', function (e) {
		$('[data-toggle="popover"]').each(function () {
			if (!$(this).is(e.target) && $(this).has(e.target).length === 0 && $('.popover').has(e.target).length === 0) {
				$(this).popover('hide');
			}
		});
	});*/    
    
    $('#add_current_loc_btn').on('click', function() { 
		add_current_location();
	});
	
});

// detect current location if button clicked
function add_current_location() {
	//alert('staka');
	 if (navigator.geolocation) {
		var timeoutVal = 10 * 1000 * 1000;
		navigator.geolocation.getCurrentPosition(
			displayPosition, 
			displayError,
			{ enableHighAccuracy: true, timeout: timeoutVal, maximumAge: 0 }
		);
	}
	else {
		alert("Geolocation is not supported by this browser");
	}	
}

// add marker with current location if geolocation was successful
function displayPosition(position) {
	var s = document.querySelector('#current_loc_status');

	if (s.className == 'success') {
		// not sure why we're hitting this twice in FF, I think it's to do with a cached result coming back    
		return;
	}

	createMarker(position.coords.latitude, position.coords.longitude);
	map.setCenter(position.coords.latitude, position.coords.longitude);
}

// display error if geolocation failed
function displayError(error) {
  var errors = { 
    1: 'Permission denied',
    2: 'Position unavailable',
    3: 'Request timeout'
  };
  alert("Error: " + errors[error.code]);
}
