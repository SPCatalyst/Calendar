<?php
global $post;
$events = array();
foreach ( $events_list as $event ) {
	array_push( $events, ( new SPC_Community_Calendar_Event( $event ) )->to_array() );
}
?>

<div class="spcc-map">
    <script>window.spccevents = '<?php echo json_encode( $events ); ?>';</script>
    <div id="spcc-events-map"></div>
</div>