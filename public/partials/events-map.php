<?php
global $post;

$events = array();

$included = array(
    'event_address_formatted',
    'event_start_date_formatted',
    'event_end_date_formatted',
    'event_venue',
    'event_title',
    'event_lat',
    'event_lng',
);

foreach ($events_list as $event) {
    $event_details = (new SPC_Community_Calendar_Event($event))->to_array();
    if (empty($event_details['event_lat']) || empty($event_details['event_lng'])) {
        continue;
    }
    $event_short = array();
    foreach ($included as $key) {
        if (!empty($event_details[$key])) {
            $event_short[$key] = $event_details[$key];
        } else {
            $event_short[$key] = null;
        }
    }
    array_push($events, $event_short);
}
?>

<div class="spcc-map">
    <script>window.spccevents = '<?php echo json_encode($events); ?>';</script>
    <div id="spcc-events-map"></div>
</div>