<?php
if ( ! is_array( $events_list ) ) {
	return;
}

$posts = array_chunk( $events_list, 4 );
?>

<div class="spcc-events-container">
    <div class="spcc-events-main">
        <div class="spcc-events-main--list">
			<?php foreach ( $posts as $group ): ?>
                <div class="spcc-g-row">
					<?php foreach ( $group as $_post ): $e = new SPC_Community_Calendar_Event( $_post ); ?>
                        <div class="<?php echo $config['per_page'] == 4 ? 'spcc-g-event-4' : 'spcc-g-event'; ?>">
                            <div class="spcc-g-event-entry">
                                <div class="spcc-g-event-image">
                                    <a target="_self" href="<?php echo $e->get_link(); ?>">
                                        <img src="<?php echo $e->get_thumbnail( 'spgc-small-thumb' ); ?>"
                                             alt="<?php echo $e->get_title(); ?>">
                                    </a>
                                </div>
                                <div class="spcc-g-event-title">
                                    <h3><a target="_self"
                                           href="<?php echo $e->get_link(); ?>"><?php echo $e->get_title(); ?></a></h3>
                                </div>
                                <div class="spcc-g-event-meta">
		                            <?php echo $e->get_start_date('M d - h:i A'); ?>
                                </div>
                            </div>
                        </div>
					<?php endforeach; ?>
                </div>
			<?php endforeach; ?>
        </div>
    </div>
</div>
