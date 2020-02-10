<?php
/* @var SPC_Community_Calendar_Event $spccevent */

global $spccevent;

if ( ! isset( $spccevent ) ) {
	echo( 'Invalid evnet.' );

	return;
}

$e = $spccevent;

$address = array();

if ( ! empty( $e->get_venue() ) ) {
	array_push( $address, $e->get_venue() );
}

if ( ! empty( $e->get_address() ) ) {
	array_push( $address, $e->get_address() );
}

if ( ! empty( $e->get_address2() ) ) {
	array_push( $address, $e->get_address2() );
}

if ( ! empty( $e->get_city() ) ) {
	array_push( $address, $e->get_city() );
}

if ( ! empty( $e->get_state() ) ) {
	array_push( $address, $e->get_state() );
}

if ( ! empty( $e->get_postal_code() ) ) {
	array_push( $address, $e->get_postal_code() );
}


$socials = $e->get_social_urls();


$event_cost    = $e->get_cost();
$event_website = $e->get_website();


?>

<div class="spcc-event-wrap">
    <!-- Disabled on stpetecatalyst.com main
    <div class="row spcc-event-row spcc-event-row-intro">
        <div class="spcc-event-col-6">&nbsp;</div>
        <div class="spcc-event-col-6 spcc-text-right">
            <a href="https://stpetecatalyst.com"><img src="<?php echo ''; ?>assets/img/poweredby.jpg" width="300" alt="st pete catalyst"></a>
        </div>
    </div> -->
    <div class="spcc-event-row spcc-event-row-main">
        <div class="spcc-event-col-6">
            <div class="spcc-event-date">
				<?php echo $e->get_formatted_date(); ?>
            </div>
            <div class="spcc-event-title">
                <h1><?php echo $e->get_title(); ?></h1>
            </div>
            <div class="spcc-event-text">
				<?php echo wpautop( $e->get_event_content() ); ?>
            </div>
			<?php do_action( 'spcc_event_after_content' ); ?>
        </div>
        <div class="spcc-event-col-6">
            <div class="spcc-event-thumbnail">
                <img src="<?php echo $e->get_thumbnail( 'mvp-mid-thumb' ); ?>" alt="<?php echo $e->get_title(); ?>">
            </div>
        </div>
    </div>
    <div class="spcc-event-row">
        <div class="spcc-event-col-6">
            <div class="spcc-event-venue-wrap">
                <div class="spcc-event-map">
                    <div id="spcc-event-map" data-lat="<?php echo $e->get_lat(); ?>"
                         data-lng="<?php echo $e->get_lng(); ?>"></div>
                </div>
                <div class="spcc-event-venue">
                    <h3 class="spcc-event-page-title">Venue</h3>
                    <div class="spcc-event-address">
						<?php echo implode( '<br/>', $address ); ?>
                    </div>
                </div>
            </div>

        </div>
        <div class="spcc-event-col-6">
            <div class="spcc-event-section">
                <h3 class="spcc-event-page-title">Event Details</h3>
                <div class="spcc-event-details">
                    <div class="spcc-event-details-row">
                        <div class="spcc-event-details-name">Start</div>
                        <div class="spcc-event-details-val"><?php echo $e->get_start_date( "H:i" ); ?></div>
                    </div>
                    <div class="spcc-event-details-row">
                        <div class="spcc-event-details-name">End</div>
                        <div class="spcc-event-details-val"><?php echo $e->get_end_date( "H:i" ); ?></div>
                    </div>
					<?php if ( ! empty( $event_cost ) ): ?>
                        <div class="spcc-event-details-row">
                            <div class="spcc-event-details-name">Cost</div>
                            <div class="spcc-event-details-val"><?php echo $event_cost; ?></div>
                        </div>
					<?php endif; ?>
					<?php if ( ! empty( $event_website ) ): ?>
                        <div class="spcc-event-details-row">
                            <div class="spcc-event-details-name">visit website</div>
                            <div class="spcc-event-details-val"><a href="<?php echo $event_website; ?>"><?php echo $event_website; ?></a></div>
                        </div>
					<?php endif; ?>
                </div>
            </div>
			<?php if ( ! empty( $socials ) ): ?>
                <div class="spcc-event-section">
                    <h3 class="spcc-event-page-title">Event Social Media</h3>
                    <div class="spcc-event-details">
                        <ul class="spcc-event-socials">
							<?php foreach ( $socials as $key => $url ): ?>
                                <li><a class="spcc-<?php echo $key; ?>" href="<?php echo $url; ?>"><i
                                                class="spcc-icon spcc-icon-<?php echo $key; ?>"></i></a></li>
							<?php endforeach; ?>
                        </ul>
                    </div>
                </div>
			<?php endif; ?>
        </div>
    </div>
</div>