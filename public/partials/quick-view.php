<?php
/* @var WP_Post $event */

$e = new SPC_Community_Calendar_Event( $event );

$address        = $e->get_formatted_address();
$venue          = $e->get_venue();
$start_date     = $e->get_start_date();
$end_date       = $e->get_end_date();
$link           = $e->get_link();
$mapsLink       = 'https://www.google.com/maps/search/'.urlencode($address);

?>

<div class="spc-quickview">
    <div class="spc-quickview-photo">
		<img src="<?php echo $e->get_thumbnail('large'); ?>" alt="<?php echo $e->get_title(); ?>">
    </div>
    <div class="spc-quickview-title">
        <h3><?php echo $event->post_title; ?></h3>
    </div>
    <div class="spc-quickview-details">
        <div class="spcc-table-responsive">
            <table class="spcc-table spcc-text-left">
				<?php if ( ! empty( $venue ) ): ?>
                    <tr>
                        <th><?php _e( 'Venue' ); ?></th>
                        <td>
							<?php echo $venue; ?>
                        </td>
                    </tr>
				<?php endif; ?>
				<?php if ( ! empty( $address ) ): ?>
                    <tr>
                        <th><?php _e( 'Address' ); ?></th>
                        <td>
							<?php echo $address; ?> <a href="<?php echo $mapsLink; ?>" target="_blank">(See Map)</a>
                        </td>
                    </tr>
				<?php endif; ?>
				<?php if ( ! empty( $start_date ) ): ?>
                    <tr>
                        <th><?php _e( 'Start Date' ); ?></th>
                        <td>
							<?php echo $start_date; ?>
                        </td>
                    </tr>
				<?php endif; ?>
				<?php if ( ! empty( $end_date ) && $start_date != $end_date ): ?>
                    <tr>
                        <th><?php _e( 'End Date' ); ?></th>
                        <td>
							<?php echo $end_date; ?>
                        </td>
                    </tr>
				<?php endif; ?>
            </table>
        </div>
    </div>
    <div class="spc-quickview-foot">
        <p><a href="<?php echo $link; ?>"><?php _e( 'More Details' ); ?></a></p>
    </div>
</div>