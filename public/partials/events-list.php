<?php
if ( ! is_array( $events_list ) ) {
	return;
}

?>

<?php foreach ( $events_list as $event ): ?>
	<?php
	$e                   = new SPC_Community_Calendar_Event( $event );
	$url_add_to_calendar = $e->get_google_calendar_url();
	$url_tickets = $e->get_tickets_url();

	$is_featured = $e->is_featured();

	$organizer = $e->get_organizer();
	?>
    <div id="event-<?php echo $e->get_id(); ?>" class="spcc-event spcc-event-row <?php echo ($is_featured) ? 'spc-border-featured' : ''; ?>">
        <div class="spcc-event-left">
            <a target="_self" href="<?php echo $e->get_link(); ?>">
				<img src="<?php echo $e->get_thumbnail( 'spgc-small-thumb' ); ?>" alt="<?php echo $e->get_title(); ?>">
            </a>
        </div>
        <div class="spcc-event-right">
            <h4 class="spcc-post-date"><?php echo $e->get_formatted_datetime(); ?></h4>
            <h2 class="spcc-post-title"><a target="_self" href="<?php echo $e->get_link(); ?>"><?php echo $e->get_title(); ?></a></h2>
	        <?php if(!empty($organizer)): ?>
                <h4  class="spcc-post-organizer"><?php echo sprintf( 'Organized by %s', $organizer ); ?></h4>
	        <?php endif; ?>
            <div class="spcc-post-excerpt">
				<p>
					<?php echo $e->get_excerpt(); ?>
                </p>
            </div>
            <div class="spcc-post-actions">
                <p>
                    <a target="_blank" href="<?php echo $url_add_to_calendar; ?>"><i class="spcc-icon spcc-icon-list"></i> Add to Calendar</a>
	                <?php if(!empty($url_tickets)): ?>
                        <a target="_blank" href="<?php echo $url_tickets; ?>"><i class="spcc-icon spcc-icon-ticket"></i> Get Tickets</a>
	                <?php endif; ?>
                    <a href="<?php echo $e->get_link(); ?>"><i class="spcc-icon spcc-icon-eye"></i> More Details</a>
                </p>
            </div>
        </div>
    </div>
<?php endforeach; ?>
