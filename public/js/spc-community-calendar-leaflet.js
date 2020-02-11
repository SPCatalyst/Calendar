/**
 * The single event map functionality
 */
(function ($) {

    var id = 'spcc-event-map';

    var $el = $('#' + id);
    if (!$el.length) {
        return;
    }

    var lat = $el.data('lat');
    var lng = $el.data('lng');

    var eventsMap = L.map(id).setView([lat, lng], 13);
    L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '',
    }).addTo(eventsMap);

    var marker = L.marker([lat, lng]).addTo(eventsMap);


})(jQuery);

/**
 * The events archive map functionality
 */
(function ($) {

    var validate_coordinate = function(coord) {
        return ("" !== coord);
    };

    var create_popup = function (event) {
        var venue = event.hasOwnProperty('event_venue') ? event.event_venue : undefined;
        var values = [
            {name: 'Venue', value: venue},
            {name: 'Address', value: event.event_address_formatted},
            {name: 'Start Date', value: event.event_start_date_formatted},
            {name: 'End Date', value: event.event_end_date_formatted},
        ];
        var content_meta = '';
        for (var i = 0; i < values.length; i++) {
            if (values[i].value) {
                content_meta += '<div class="spcc-ifw-event-meta-entry"><div class="spcc-ifw-event-meta-name">' + values[i].name + '</div><div class="spcc-ifw-event-meta-val">' + values[i].value + '</div></div>';
            }
        }
        return '<div class="sifw-event">\n' +
            '\t<div class="sifw-event-title">\n' +
            '\t\t<h4>' + event.event_title + '</h4>\n' +
            '\t</div>\n' +
            '\t<div class="sifw-event-meta">\n' + content_meta +
            '</div>';
    };

    window.init_spcc_map = function () {

        var id = 'spcc-events-map';
        var $el = $('#' + id);

        if (!$el.length) {
            return;
        }

        var eventsMap = L.map('spcc-events-map').setView([27.773056, -82.639999], 13);
        L.tileLayer('http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '',
        }).addTo(eventsMap);

        var bounds = [];
        var events = window.spccevents ? JSON.parse(window.spccevents) : [];
        for (i = 0; i < events.length; i++) {
            var lat = events[i].event_lat;
            var lng = events[i].event_lng;
            if( !validate_coordinate(lat) || !validate_coordinate(lng)) {
                continue;
            }
            var marker = L.marker([lat, lng]).addTo(eventsMap);
            marker.bindPopup(create_popup(events[i]));
            bounds.push([lat, lng]);
        }
        if(bounds.length > 0) {
            eventsMap.fitBounds(bounds);
        }
    };

    window.init_spcc_map();

})(jQuery);