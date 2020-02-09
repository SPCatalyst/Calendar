<?php
if ( ! is_array( $events_list ) ) {
	return;
}

$posts = array_chunk( $events_list, 3 );
?>

<?php foreach ( $posts as $group ): ?>
    <div class="cc-g-row">
		<?php foreach ( $group as $_post ): $e = new SPC_Community_Calendar_Event( $_post ); ?>
            <div class="cc-g-event">
                <div class="cc-g-event-image">
                    <a target="_self" href="<?php echo $e->get_link(); ?>">
                        <img src="<?php echo $e->get_thumbnail( 'spgc-small-thumb' ); ?>" alt="<?php echo $e->get_title(); ?>">
                    </a>
                </div>
                <div class="cc-g-event-title">
                    <h3><a target="_self" href="<?php echo $e->get_link(); ?>"><?php echo $e->get_title(); ?></a></h3>
                </div>
                <div class="cc-g-event-meta">
					<?php echo $e->get_formatted_datetime(); ?>
                </div>
            </div>
		<?php endforeach; ?>
    </div>
<?php endforeach; ?>
