/* Google Maps */
(function($) {
	$(document).ready(function() {
		// Init google maps
		googleMapsInit();
	});
})(jQuery);


var bounds = new google.maps.LatLngBounds(),
	locations = [];

for (var i = 0; i < php_vars.listing_count; i++) {
	locations[i] = new google.maps.LatLng(php_vars.latitudes[i], php_vars.longitudes[i]);
	bounds.extend (locations[i]);
}

function googleMapsInit() {
	var centerCoords= new google.maps.LatLng(php_vars.center_lat_radius, php_vars.center_lon_radius);
	var mapOptions = {
		center: centerCoords,
		zoom: 8
	};
	
	var map = new google.maps.Map(document.getElementById("gd-map"), mapOptions);
	google.maps.event.addDomListener(window, "resize", function() {
		var center = map.getCenter();
		google.maps.event.trigger(map, "resize");
		map.setCenter(center); 
	});

	//setting pin variables
	var markers = [],
		mapPreviews = [],
		infoWindows = [];
	
	for (var i = 0; i < php_vars.listing_count; i++) {
		markers[i] = new google.maps.Marker({
			position: locations[i],
			map: map,
			draggable: false,
			animation: google.maps.Animation.DROP
		});

		infoWindows[i] = new google.maps.InfoWindow({
			content: php_vars.map_previews[i]
		});

		addInfoWindow(markers[i], map, infoWindows[i]);
	}
	
	map.fitBounds(bounds);
}

var myInfoWindow;
function addInfoWindow(marker, map, infoWindow) {
	google.maps.event.addListener(marker, 'click', function() {
		if (myInfoWindow) {
			myInfoWindow.close();
		}

		myInfoWindow = infoWindow;
		infoWindow.open(map, marker);
	});
}