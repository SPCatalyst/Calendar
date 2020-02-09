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
    <div id="event-<?php echo $e->get_id(); ?>" class="cc-event cc-event-row">
        <div class="cc-event-left">
            <a target="_self" href="<?php echo $e->get_link(); ?>">
				<img src="<?php echo $e->get_thumbnail( 'spgc-small-thumb' ); ?>" alt="<?php echo $e->get_title(); ?>">
            </a>
        </div>
        <div class="cc-event-right">
			<?php if ( ! empty( $date ) ): ?>
                <h4 class="cc-post-date"><?php echo $e->get_formatted_datetime(); ?></h4>
			<?php endif; ?>
            <h2 class="cc-post-title"><a target="_self"
                                         href="<?php echo $e->get_link(); ?>"><?php echo $e->get_title(); ?></a></h2>
            <div class="cc-post-excerpt">
				<?php the_excerpt(); ?>
            </div>
            <div class="cc-post-actions">
                <p>
                    <a href="<?php echo $url_add_to_calendar; ?>"><i class="fa fa fa-list"></i> Add to Calendar</a>
                    <a href="#" data-id="<?php echo $e->get_id(); ?>" class="spcc-action-qw"><i
                                class="fa fa fa-eye"></i> Quick View</a>
                </p>
            </div>
        </div>
    </div>
<?php endforeach; ?>
