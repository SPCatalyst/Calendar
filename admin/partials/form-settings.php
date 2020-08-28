<?php

// Data Repository
$repo = new SPC_Community_Calendar_Data_Repository();

// Settings Repository
$settings = new SPC_Community_Calendar_Settings();

// Filters & Cats
$categories         = $repo->get_categories();
$categories_choices = spcc_array_key_value( $categories->get_items(), 'id', 'name' );
$filters            = $repo->get_filters();
$filters_choices    = spcc_array_key_value( $filters->get_items(), 'id', 'name' );
$views_choices      = array(
	'grid' => 'Grid',
	'list' => 'List',
	'map'  => 'Map',
);

$account = $repo->get_account();

if ( ! $settings->has_settings() ) {
	$settings->import();
}


// Current Values
$categories_selected = $settings->get( 'preferred_categories', array() );
if ( ! is_array( $categories_selected ) ) {
	$categories_selected = array();
}
$filters_selected = $settings->get( 'preferred_filters', array() );
if ( ! is_array( $filters_selected ) ) {
	$filters_selected = array();
}
$view_selected     = $settings->get( 'preferred_view', 'list' );
$color_schemes     = $settings->get( 'color_schemes' );
$events_type       = $settings->get( 'type' );
$events_visibility = $settings->get( 'visibility' );
$google_maps_key   = $settings->get( 'google_maps_key' );
$logo              = $settings->get( 'logo' );
$maps_provider     = $settings->get( 'maps_provider', 'leaflet' );
$status            = $account->get_item_param( 'status' );
$color_primary     = isset( $color_schemes[0] ) ? $color_schemes[0] : null;
$color_secondary   = isset( $color_schemes[1] ) ? $color_schemes[2] : null;
$color_alt         = isset( $color_schemes[3] ) ? $color_schemes[3] : null;
$events_page       = $settings->get( 'events_page' );

$pages = get_posts( array(
	'post_type'      => 'page',
	'posts_per_page' => - 1,
	'post_status'    => 'publish',
) );


$permission = $account->get_item_param( 'permission' );

$permission_human = '';
if ( $permission === 'trusted' ) {
	$permission_human = 'Trusted Member (Can post network and local events without approval)';
} else if ( $permission === 'approved' ) {
	$permission_human = 'Approved Member (Can post network and local events. Requires approval for the network events)';
} else if ( $permission === 'limited' ) {
	$permission_human = 'Limited Member (Can post local events only)';
} else {
	$permission_human = 'Error: Access not assigned.';
}


$prev_access_request = get_option('spcc_access_request');

?>

<form class="section-form" id="form-settings" method="POST" action="">
    <div class="settings-row">
        <label><?php _e( 'Service Status', 'spcc' ); ?></label>
		<?php if ( ! $account->is_error() ): ?>
            <p class="service-success"><?php _e( 'ONLINE' ); ?></p>
		<?php else: ?>
            <p class="service-error"><?php _e( 'OFFLINE' ); ?></p>
		<?php endif; ?>
    </div>

    <div class="settings-row">
        <label><?php _e( 'Access Type', 'spcc' ); ?></label>
        <p><?php echo $permission_human; ?></p>
		<?php if ( $permission === 'limited' && !$prev_access_request ): ?>
            <p class="spcc-italic spcc-request-access-info">If you want to be able to post to the network. You need to send us request. <a
                        href="#requestAccess" rel="modal:open" class="button-small spcc-request-access button">Request
                    access</a></p>
		<?php endif; ?>
        <?php if($permission === 'limited' && $prev_access_request): ?>
            <p class="spcc-italic spcc-request-access-info">Access requested on <?php echo $prev_access_request; ?></p>
        <?php endif; ?>
    </div>

    <div class="settings-row">
		<?php
		$key                = 'logo';
		$placeholder        = 'https://placehold.it/150x150?text=IMG';
		$logo_current_value = $logo;
		?>
        <label for='<?php echo $key; ?>'><?php _e( 'Logo', 'spcc' ); ?></label>
		<?php spcc_custom_upload_field( $key, $logo_current_value, $placeholder ); ?>
    </div>

    <div class="settings-row">
        <div class="color-schemes">
            <div class="color-scheme">
                <label for="color_schemes"><?php _e( 'Primary color', 'spcc' ); ?></label>
                <input type="hidden" class="spcc-colorpicker" name="color_schemes[0]" id="color_schemes"
                       value="<?php echo $color_primary; ?>">
            </div>
            <div class="color-scheme">
                <label for="color_schemes"><?php _e( 'Secondary color', 'spcc' ); ?></label>
                <input type="hidden" class="spcc-colorpicker" name="color_schemes[1]" id="color_schemes"
                       value="<?php echo $color_secondary; ?>">
            </div>
            <div class="color-scheme">
                <label for="color_schemes"><?php _e( 'Alt color', 'spcc' ); ?></label>
                <input type="hidden" class="spcc-colorpicker" name="color_schemes[2]" id="color_schemes"
                       value="<?php echo $color_alt; ?>">
            </div>
        </div>
    </div>
    <div class="settings-row">
        <label for="maps_provider"><?php _e( 'Maps Provider', 'spcc' ); ?></label>
        <select name="maps_provider" id="maps_provider" class="spcc-select spcc-conditional"
                data-target="#google_maps_key_wrap" data-target-hideifvalue="leaflet">
            <option value="leaflet" <?php selected( $maps_provider, 'leaflet' ); ?>><?php _e( 'Leaflet/OSM', 'spcc' ); ?></option>
            <option value="google" <?php selected( $maps_provider, 'google' ); ?>><?php _e( 'Google', 'spcc' ); ?></option>
        </select>
    </div>
    <div class="settings-row" id="google_maps_key_wrap"
         style="<?php echo $maps_provider === 'leaflet' ? 'display:none;' : ''; ?>">
        <label for="google_maps_key"><?php _e( 'Google Maps Key', 'spcc' ); ?></label>
        <input type="text" name="google_maps_key" id="google_maps_key"
               value="<?php echo $google_maps_key; ?>">
    </div>
    <div class="settings-row">
        <label for="type" class="spcc-radio-group">
            <span><?php _e( 'Events Type', 'spcc' ); ?></span>
            <input type="radio" name="type" <?php checked( $events_type, 'internal', true ); ?>
                   value="internal"> <?php _e( 'Show only internal events', 'spcc' ); ?> <br/>
            <input type="radio" name="type" <?php checked( $events_type, 'any', true ); ?>
                   value="any"> <?php _e( 'Show my events and events from the Catalyst Master Calendar', 'spcc' ); ?>
        </label>
    </div>
    <!--<div class="settings-row">
                <label for="visibility" class="spcc-radio-group">
                    <span><?php _e( 'Events Visibility', 'spcc' ); ?></span>
                    <input type="radio" name="visibility" <?php checked( $events_visibility, 'public', true ); ?>
                           value="public"> <?php _e( 'All events are accessible to everybody', 'spcc' ); ?> <br/>
                    <input type="radio" name="visibility" <?php checked( $events_visibility, 'private', true ); ?>
                           value="private"> <?php _e( 'Only people with the Calendar\'s URL can access events', 'spcc' ); ?>
                </label>
            </div>-->
    <div class="settings-row">
        <label for="preferred_categories"><?php _e( 'Preferred Categories', 'spcc' ); ?></label>
        <select name="preferred_categories[]" id="preferred_categories" multiple class="spcc-select">
			<?php foreach ( $categories_choices as $value => $name ): ?>
                <option value="<?php echo $value; ?>" <?php echo in_array( $value, $categories_selected ) ? 'selected' : ''; ?>><?php echo $name; ?></option>
			<?php endforeach; ?>
        </select>
    </div>
    <div class="settings-row">
        <label for="preferred_filters"><?php _e( 'Preferred Filters', 'spcc' ); ?></label>
        <select name="preferred_filters[]" id="preferred_filters" multiple class="spcc-select">
			<?php foreach ( $filters_choices as $value => $name ): ?>
                <option value="<?php echo $value; ?>" <?php echo in_array( $value, $filters_selected ) ? 'selected' : ''; ?>><?php echo $name; ?></option>
			<?php endforeach; ?>
        </select>
    </div>
    <div class="settings-row">
        <label for="preferred_view"><?php _e( 'Preferred View', 'spcc' ); ?></label>
        <select name="preferred_view" id="preferred_view" class="spcc-select">
			<?php foreach ( $views_choices as $value => $name ): ?>
                <option value="<?php echo $value; ?>" <?php echo $value == $view_selected ? 'selected' : ''; ?>><?php echo $name; ?></option>
			<?php endforeach; ?>
        </select>
    </div>
    <div class="settings-row">
        <label for="events_page"><?php _e( 'Events Page', 'spcc' ); ?></label>
        <select name="events_page" id="events_page" class="spcc-select">
			<?php foreach ( $pages as $page ): ?>
                <option value="<?php echo $page->ID; ?>" <?php echo $page->ID == $events_page ? 'selected' : ''; ?>><?php echo $page->post_title; ?></option>
			<?php endforeach; ?>
        </select>
    </div>
    <div class="settings-row">
        <input type="hidden" name="req_type" value="save_settings">
        <span class="spcc-spinner"></span>
        <button type="submit" class="button-primary"><?php _e( 'Save', 'spcc' ); ?></button>
        &nbsp;&nbsp;
        <a href="#"
           class="spcc-disconnect">Disconnect</a>
    </div>
</form>