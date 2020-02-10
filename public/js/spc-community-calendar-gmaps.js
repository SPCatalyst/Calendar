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
		zoom: 12,
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

	window.init_spcc_map = function () {
		var $el = $('#spcc-events-map');

		if (!$el.length) {
			return;
		}

		window.map = new google.maps.Map($el.get(0), {
			zoom: 15,
		});
		var events = window.spccevents ? JSON.parse(window.spccevents) : [];
		var infowindow = new google.maps.InfoWindow();
		var bounds = new google.maps.LatLngBounds();

		for (i = 0; i < events.length; i++) {
			window.currentEvent = events[i];
			marker = new google.maps.Marker({
				position: new google.maps.LatLng(events[i].event_lat, events[i].event_lng),
				map: window.map
			});
			bounds.extend(marker.position);
			google.maps.event.addListener(marker, 'click', (function (marker, i) {
				return function () {

					var venue = window.currentEvent.hasOwnProperty('event_venue') ? window.currentEvent.event_venue : undefined;

					var values = [
						{name: 'Venue', value: venue},
						{name: 'Address', value: window.currentEvent.event_address_formatted},
						{name: 'Start Date', value: window.currentEvent.event_start_date_formatted},
						{name: 'End Date', value: window.currentEvent.event_end_date_formatted},
					];

					var content_meta = '';
					for (var i = 0; i < values.length; i++) {
						if (values[i].value) {
							content_meta += '<div class="sifw-event-meta-entry"><div class="sifw-event-meta-name">' + values[i].name + '</div><div class="sifw-event-meta-val">' + values[i].value + '</div></div>';
						}
					}

					var content = '<div class="sifw-event">\n' +
						'\t<div class="sifw-event-title">\n' +
						'\t\t<h4>' + window.currentEvent.event_title + '</h4>\n' +
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
			window.map.setZoom(11);
			google.maps.event.removeListener(listener);
		});
	};

	google.maps.event.addDomListener(window, 'load', window.init_spcc_map);
})(jQuery);