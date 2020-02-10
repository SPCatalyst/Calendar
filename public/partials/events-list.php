<?php
if ( ! is_array( $events_list ) ) {
	return;
}
$posts = array_chunk( $events_list, 3 );
?>

<?php foreach ( $events_list as $event ): ?>
	<?php
	$e                   = new SPC_Community_Calendar_Event( $event );
	$url_add_to_calendar = $e->get_google_calendar_url();

	?>
    <div id="event-<?php echo $e->get_id(); ?>" class="spcc-event spcc-event-row">
        <div class="spcc-event-left">
            <a target="_self" href="<?php echo $e->get_link(); ?>">
				<img src="<?php echo $e->get_thumbnail( 'spgc-small-thumb' ); ?>" alt="<?php echo $e->get_title(); ?>">
            </a>
        </div>
        <div class="spcc-event-right">
            <h4 class="spcc-post-date"><?php echo $e->get_formatted_datetime(); ?></h4>
            <h2 class="spcc-post-title"><a target="_self" href="<?php echo $e->get_link(); ?>"><?php echo $e->get_title(); ?></a></h2>
            <div class="spcc-post-excerpt">
				<?php echo $e->get_excerpt(); ?>
            </div>
            <div class="spcc-post-actions">
                <p>
                    <a target="_blank" href="<?php echo $url_add_to_calendar; ?>"><i class="spcc-icon spcc-icon-list"></i> Add to Calendar</a>
                    <a href="#" data-id="<?php echo $e->get_id(); ?>" class="spcc-action-qw"><i
                                class="spcc-icon spcc-icon-eye"></i> Quick View</a>
                </p>
            </div>
        </div>
    </div>
<?php endforeach; ?>
