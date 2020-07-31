<?php
if ( ! is_array( $events_list ) ) {
	return;
}

$posts = array_chunk( $events_list, 3 );
?>

<?php foreach ( $posts as $group ): ?>
    <div class="spcc-g-row">
		<?php foreach ( $group as $_post ): $e = new SPC_Community_Calendar_Event( $_post ); ?>
            <div class="spcc-g-event">
                <div class="spcc-g-event-entry">
                    <div class="spcc-g-event-image">
                        <a target="_self" href="<?php echo $e->get_link(); ?>">
                            <img src="<?php echo $e->get_thumbnail( 'spgc-small-thumb' ); ?>"
                                 alt="<?php echo $e->get_title(); ?>">
                        </a>
                    </div>
                    <div class="spcc-g-event-title">
                        <h3><a target="_self" href="<?php echo $e->get_link(); ?>"><?php echo $e->get_title(); ?></a>
                        </h3>
                    </div>
                    <div class="spcc-g-event-meta">
						<?php echo $e->get_formatted_datetime(); ?>
                    </div>
                </div>
            </div>
		<?php endforeach; ?>
    </div>
<?php endforeach; ?>
