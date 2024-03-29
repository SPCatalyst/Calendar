/**
 * The single event map functionality
 */
(function ($) {

	var $el = $('#spcc-event-map');
	if (!$el.length) {
		return;
	}

	var lat = $el.data('lat');
	var lng = $el.data('lng');

	var center = new google.maps.LatLng(lat, lng);

	window.evnetMap = new google.maps.Map($el.get(0), {
		zoom: 9,
		center: center,
		mapTypeControl: false,
		scaleControl: false,
		streetViewControl: false,
	});
	marker = new google.maps.Marker({
		position: center,
		map: window.evnetMap
	});

})(jQuery);

/**
 * The events archive map functionality
 */
(function ($) {

	var validate_coordinate = function(coord) {
		return ("" !== coord);
	};

	window.init_spcc_map = function () {
		var $el = $('#spcc-events-map');

		if (!$el.length) {
			return;
		}

		window.map = new google.maps.Map($el.get(0), {
			zoom: 15,
		});
		var events = window.spccevents ? window.spccevents : [];
		if (typeof events === 'string' || events instanceof String) {
			events = JSON.parse(window.spccevents);
		}
		var infowindow = new google.maps.InfoWindow();
		var bounds = new google.maps.LatLngBounds();

		for (i = 0; i < events.length; i++) {

			var lat = events[i].event_lat;
			var lng = events[i].event_lng;

			if(!validate_coordinate(lat) || !validate_coordinate(lng)) {
				continue;
			}

			window.currentEvent = events[i];
			marker = new google.maps.Marker({
				position: new google.maps.LatLng(lat, lng),
				map: window.map,
				event: window.currentEvent,
			});
			bounds.extend(marker.position);
			google.maps.event.addListener(marker, 'click', (function (marker, i) {
				return function () {

					var mapEvent = marker.event;

					var venue = mapEvent.hasOwnProperty('event_venue') ? mapEvent.event_venue : undefined;

					var values = [
						{name: 'Venue', value: venue},
						{name: 'Address', value: mapEvent.event_address_formatted},
						{name: 'Start Date', value: mapEvent.event_start_date_formatted},
						{name: 'End Date', value: mapEvent.event_end_date_formatted},
					];

					var content_meta = '';
					for (var i = 0; i < values.length; i++) {
						if (values[i].value) {
							content_meta += '<div class="sifw-event-meta-entry"><div class="sifw-event-meta-name">' + values[i].name + '</div><div class="sifw-event-meta-val">' + values[i].value + '</div></div>';
						}
					}

					var content = '<div class="sifw-event">\n' +
						'\t<div class="sifw-event-title">\n' +
						'\t\t<h4>' + mapEvent.event_title + '</h4>\n' +
						'\t</div>\n' +
						'\t<div class="sifw-event-meta">\n' + content_meta +
						'</div>';

					infowindow.setContent(content);
					infowindow.open(window.map, marker);
				}
			})(marker, i));
		}

		window.map.fitBounds(bounds);
		var listener = google.maps.event.addListener(map, "idle", function () {
			window.map.setZoom(9);
			google.maps.event.removeListener(listener);
		});
	};

	google.maps.event.addDomListener(window, 'load', window.init_spcc_map);
})(jQuery);