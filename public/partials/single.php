<?php
/* @var SPC_Community_Calendar_Event $spccevent */

global $spccevent;

if (!isset($spccevent)) {
    echo('Invalid evnet.');

    return;
}

$e = $spccevent;

// Setup address
$address = array();
if (!empty($e->get_venue())) {
    array_push($address, $e->get_venue());
}
if (!empty($e->get_address())) {
    array_push($address, $e->get_address());
}
if (!empty($e->get_address2())) {
    array_push($address, $e->get_address2());
}
if (!empty($e->get_city())) {
    array_push($address, $e->get_city());
}
if (!empty($e->get_state())) {
    array_push($address, $e->get_state());
}
if (!empty($e->get_postal_code())) {
    array_push($address, $e->get_postal_code());
}

// Prepare location
$lat = $e->get_lat();
$lng = $e->get_lng();
$has_location = !empty($lat) && !empty($lng);
$has_address = !empty($address);

// Prepare tags
$tags_array = $e->get_categories();
$tags_list = array();
foreach ($tags_array as $tag) {
    array_push($tags_list, '<li><a href="#">' . $tag . '</a></li>');
}

// Prepare other data
$event_socials = $e->get_social_urls();
$event_cost = $e->get_cost();
$event_website = $e->get_website();
$event_phone = $e->get_phone();
$event_email = $e->get_email();
$event_parking = $e->get_parking();
$event_organizer = $e->get_organizer();
$calendar_url = $e->get_google_calendar_url();

// Format phone
$event_phone = spcc_format_phone($event_phone);
$event_website = !empty($event_website) ? spcc_add_scheme($event_website) : '';

$calendar_links = $e->get_calendar_links();

?>

<div class="spcc-event-wrap bootstrap-wrapper">
    <div class="row">
        <div class="col-sm-8">
            <div class="spcc-event-wrap-main">
                <div class="spcc-event-thumbnail">
                    <?php $e->the_thumbnail('large'); ?>
                </div>
                <div class="spcc-event-text">
                    <?php echo wpautop($e->get_event_content()); ?>
                    <div class="spcc-share-icons">
                        <?php if (function_exists('social_warfare')): ?>
                            <?php social_warfare(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-4">
            <div class="spcc-event-card">
                <div class="spcc-event-date-feat">
                    <span><?php echo $e->get_start_date('M d Y'); ?></span>
                </div>
                <div class="spcc-event-titles">
                    <h1 class="spcc-event-title"><?php echo $e->get_title(); ?></h1>
                    <?php if ($event_organizer): ?>
                        <p class="spcc-event-organizer"><?php echo sprintf('Organized by %s', $event_organizer); ?></p>
                    <?php endif; ?>
                </div>
            </div>
            <?php if (!empty($tags_list)): ?>
                <div class="spcc-event-tags">
                    <div class="row">
                        <div class="col-sm-4">
                            <span class="spcc-event-tags-label"> <?php _e('Event Tags:'); ?></span>
                        </div>
                        <div class="col-sm-8">
                            <ul class="spcc-event-tags-list">
                                <?php echo implode("\n", $tags_list); ?>
                            </ul>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="spcc-event-details">
                <h3 class="spcc-event-details-title"><?php _e('Event Details'); ?></h3>
                <div class="spcc-event-detail">
                    <div class="spcc-event-detail--label">
                        <?php _e('Event Starts'); ?>
                    </div>
                    <div class="spcc-event-detail--value">
                        <?php echo $e->get_start_date('M d Y h:i A'); ?>
                    </div>
                </div>
                <div class="spcc-event-detail">
                    <div class="spcc-event-detail--label">
                        <?php _e('Event Ends'); ?>
                    </div>
                    <div class="spcc-event-detail--value">
                        <?php echo $e->get_end_date('M d Y h:i A'); ?>
                    </div>
                </div>

                <?php if (!empty($event_website) || !empty($event_socials)): ?>
                <div class="spcc-event-detail">
                        <div class="row">
                            <?php if (!empty($event_website)): ?>
                                <div class="col-sm-6">
                                    <div class="spcc-event-detail--label">
                                        <?php _e('Event Website'); ?>
                                    </div>
                                    <div class="spcc-event-detail--value">
                                        <a href="<?php echo $event_website; ?>" target="_blank"><?php _e('View'); ?></a>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <?php if (!empty($event_socials)): ?>
                                <div class="col-sm-6">
                                    <div class="spcc-event-detail--label">
                                        <?php _e('Social Media'); ?>
                                    </div>
                                    <div class="spcc-event-detail--value">
                                        <ul class="spcc-event-socials">
                                            <?php foreach ($event_socials as $key => $url): ?>
                                                <a class="spcc-<?php echo $key; ?>" href="<?php echo $url; ?>"><i
                                                            class="spcc-icon spcc-icon-<?php echo $key; ?>"></i></a>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                </div>
                <?php endif; ?>


                <?php if (!empty($event_phone)): ?>
                    <div class="spcc-event-detail">
                        <div class="spcc-event-detail--label">
                            <?php _e('Event Phone'); ?>
                        </div>
                        <div class="spcc-event-detail--value">
                            <?php echo $event_phone; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (!empty($event_email)): ?>
                    <div class="spcc-event-detail">
                        <div class="spcc-event-detail--label">
                            <?php _e('Event Email'); ?>
                        </div>
                        <div class="spcc-event-detail--value">
                            <?php echo $event_email; ?>
                        </div>
                    </div>
                <?php endif; ?>
                <div class="spcc-event-detail">
                    <div class="spcc-event-detail--label">
                        <?php _e('Event Cost'); ?>
                    </div>
                    <div class="spcc-event-detail--value">
                        <?php if ($event_cost == 0): ?>
                            <?php _e('FREE'); ?>
                        <?php else: ?>
                            <?php echo '$' . $event_cost; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <?php if (!empty($event_parking) && !$e->is_virtual()): ?>
                    <div class="spcc-event-detail">
                        <div class="spcc-event-detail--label">
                            <?php _e('Event Parking'); ?>
                        </div>
                        <div class="spcc-event-detail--value">
                            <?php echo $event_parking; ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="spcc-event-calendar">
                <a href="#" class="spcc-event-calendar--button"
                   target="_blank"><?php _e('Add to Calendar'); ?></a>
            </div>

            <?php if (!$e->is_virtual()): ?>

                <div class="spcc-event-venue-wrap">
                    <?php if ($has_location): ?>
                        <div class="spcc-event-map">
                            <div id="spcc-event-map" data-lat="<?php echo $lat; ?>"
                                 data-lng="<?php echo $lng; ?>"></div>
                        </div>
                    <?php endif; ?>
                    <?php if ($has_address): ?>
                        <div class="spcc-event-venue">
                            <div class="spcc-event-address">
                                <?php echo implode(', ', $address); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

            <?php elseif ($e->is_virtual()): ?>
                <div class="spcc-event-virtual-wrap">
                    <a href="<?php echo $event_website; ?>"><img
                                src="<?php echo SPCC_ROOT_URL; ?>public/img/virtual.png" alt="Virtual Event"/></a>
                </div>
            <?php endif; ?>

        </div>
    </div>

</div>


<div class="remodal" data-remodal-id="calendar" id="calendarModal">
    <button data-remodal-action="close" class="remodal-close"></button>
    <h3 class="spcc-modal-title">Calendar</h3>
    <p class="spcc-modal-paragraph">
        Click on the calendar you prefer to save this event to:
    </p>
    <ul class="spcc-modal-links">
        <?php foreach ( $calendar_links as $name => $calendar_link ): ?>
            <li><a href="<?php echo $calendar_link; ?>"><?php echo $name; ?></a></li>
        <?php endforeach; ?>
    </ul>
    <br>
    <button data-remodal-action="cancel" class="remodal-cancel">Cancel</button>
    <button data-remodal-action="confirm" class="remodal-confirm">OK</button>
</div>