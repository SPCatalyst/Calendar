<?php

// Settings Repository
$settings = new SPC_Community_Calendar_Settings();

// Data Repository
$repo = new SPC_Community_Calendar_Data_Repository();

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

$errors  = array();
$message = '';
$account = $repo->get_account();

if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	$is_registration = isset( $_POST['req_type'] ) && $_POST['req_type'] === 'register';
	if ( $is_registration ) { // If Registration
		$validator  = new \Rakit\Validation\Validator();
		$validation = $validator->validate( $_POST + $_FILES, [
			'name'     => 'required',
			'email'    => 'required|email',
			'website'  => 'required|url',
			'password' => 'required|min:6',
		] );
		if ( $validation->fails() ) {
			$message = __( 'Please correct the following errors' );
			$errors  = $validation->errors()->all();
		} else {
			$data     = array(
				'name'     => $_POST['name'],
				'email'    => $_POST['email'],
				'website'  => $_POST['website'],
				'password' => $_POST['password'],
			);
			$api      = new SPC_Community_Calendar_API();
			$response = $api->register_account( $data );
			if ( $response->is_error() ) {
				$message = __( 'Please correct the following errors:' );
				$errors  = $response->get_errors();
			} else {
				$message = __( 'Your registration was successful. Please wait until your account is approved.' );
				$item    = $response->get_item();
				update_option( 'spcc_token', $item['token'] );
				update_option( 'spcc_website_id', $item['website_id'] );
				$account = $repo->get_account();
			}
		}
	} else { // If Settings Save
		$settings->saveRequest();
		$message = __( 'Settings updated!', 'spcc' );
	}
}

if ( ! $settings->has_settings() ) {
	$settings->import();
}

// Current Values
$categories_selected = $settings->get( 'preferred_categories', array() );
$filters_selected    = $settings->get( 'preferred_filters', array() );
$view_selected       = $settings->get( 'preferred_view', 'list' );
$color_schemes       = $settings->get( 'color_schemes' );
$events_type         = $settings->get( 'type' );
$events_visibility   = $settings->get( 'visibility' );
$google_maps_key     = $settings->get( 'google_maps_key' );
$logo                = $settings->get( 'logo' );
$maps_provider       = $settings->get( 'maps_provider', 'leaflet' );

$status = $account->get_item_param( 'status' );

$color_primary   = isset( $color_schemes[0] ) ? $color_schemes[0] : null;
$color_secondary = isset( $color_schemes[1] ) ? $color_schemes[2] : null;
$color_alt       = isset( $color_schemes[3] ) ? $color_schemes[3] : null;

?>

<div class="wrap">
	<?php if ( $account->is_error() ): ?>
        <h2><?php _e( 'Community Calendar Account', 'spcc' ); ?></h2>
	<?php else: ?>
        <h2><?php _e( 'Community Calendar Settings', 'spcc' ); ?></h2>
	<?php endif; ?>

	<?php if ( is_array( $errors ) && count( $errors ) > 0 ): ?>
        <div class="notice notice-error is-dismissible">
			<?php if ( ! empty( $message ) ): ?>
                <p><strong><?php echo $message; ?></strong></p>
			<?php else: ?>
                <p><strong><?php _e( 'Please correct the following errors:', 'spcc' ); ?></strong></p>
			<?php endif; ?>
            <ul class="spcc-normal-list">
				<?php foreach ( $errors as $error ): ?>
                    <li><?php echo $error; ?></li>
				<?php endforeach; ?>
            </ul>
        </div>
	<?php endif; ?>

    <form class="settings-wrap" method="POST" action="">
		<?php if ( $account->is_error() ): ?>
            <div class="settings-row">
                <label for="name"><?php _e( 'Name', 'spcc' ); ?></label>
                <input type="text" name="name" id="name" class="required"
                       value="<?php echo isset( $_POST['name'] ) ? $_POST['name'] : null; ?>">
            </div>
            <div class="settings-row">
                <label for="email"><?php _e( 'Email', 'spcc' ); ?><span class="required">*</span></label>
                <input type="email" name="email" id="email" class="required"
                       value="<?php echo isset( $_POST['email'] ) ? $_POST['email'] : null; ?>">
            </div>
            <div class="settings-row">
                <label for="website"><?php _e( 'Website', 'spcc' ); ?></label>
                <input type="text" name="website" id="website" readonly value="<?php echo site_url(); ?>">
            </div>
            <div class="settings-row">
                <label for="password"><?php _e( 'Password', 'spcc' ); ?></label>
                <input type="text" name="password" id="password">
            </div>
            <div class="settings-row">
                <input type="hidden" name="req_type" value="register">
                <button type="submit" class="button-primary"><?php _e( 'Register', 'spcc' ); ?></button>
            </div>
		<?php else: ?>
            <div class="settings-row">
                <label><?php _e( 'Service Status', 'spcc' ); ?></label>
				<?php if ( $status === 'publish' ): ?>
                    <p class="service-success"><?php _e( 'ONLINE' ); ?></p>
				<?php elseif ( $status === 'draft' ): ?>
                    <p class="service-warning"><?php _e( 'WAITING FOR APPROVAL' ); ?></p>
				<?php else: ?>
                    <p class="service-error"><?php _e( 'UNKNOWN' ); ?></p>
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
                <label for="color_schemes"><?php _e( 'Primary color', 'spcc' ); ?></label>
                <input type="text" class="spcc-colorpicker" name="color_schemes[0]" id="color_schemes"
                       value="<?php echo $color_primary; ?>">
            </div>
            <div class="settings-row">
                <label for="color_schemes"><?php _e( 'Secondary color', 'spcc' ); ?></label>
                <input type="text" class="spcc-colorpicker" name="color_schemes[1]" id="color_schemes"
                       value="<?php echo $color_secondary; ?>">
            </div>
            <div class="settings-row">
                <label for="color_schemes"><?php _e( 'Alt color', 'spcc' ); ?></label>
                <input type="text" class="spcc-colorpicker" name="color_schemes[2]" id="color_schemes"
                       value="<?php echo $color_alt; ?>">
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
            <div class="settings-row">
                <label for="visibility" class="spcc-radio-group">
                    <span><?php _e( 'Events Visibility', 'spcc' ); ?>y</span>
                    <input type="radio" name="visibility" <?php checked( $events_visibility, 'public', true ); ?>
                           value="public"> <?php _e( 'All events are accessible to everybody', 'spcc' ); ?> <br/>
                    <input type="radio" name="visibility" <?php checked( $events_visibility, 'private', true ); ?>
                           value="private"> <?php _e( 'Only people with the Calendar\'s URL can access events', 'spcc' ); ?>
                </label>
            </div>
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
                <input type="hidden" name="req_type" value="save_settings">
                <button type="submit" class="button-primary"><?php _e( 'Save', 'spcc' ); ?></button>
            </div>
		<?php endif; ?>
    </form>
</div>
<p class="spcc-ver">
	<?php echo sprintf( __( 'Catalyst Community Calendar v%s' ), SPC_COMMUNITY_CALENDAR_VERSION ); ?>
</p>

<style>
    form.settings-wrap .settings-row p {
        margin-top: 0;
        margin-bottom: 0;
    }

    form.settings-wrap .settings-row {
        margin-bottom: 15px;
    }

    form.settings-wrap label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
    }

    form.settings-wrap {
        background: #fff;
        padding: 20px;
        margin-top: 10px;
        border: 1px solid #ccd0d4;
        box-shadow: 0 1px 1px rgba(0, 0, 0, .04);
    }

    .spcc-normal-list {
        list-style-type: disc;
        padding-left: 20px;
    }

    .settings-row input[type=text],
    .settings-row input[type=email],
    .settings-row select {
        min-width: 400px;
    }

    .spcc-radio-group {
        font-weight: normal !important;
    }

    .spcc-radio-group > span {
        margin-bottom: 5px !important;
        display: block;
        font-weight: bold !important;
    }

    .spcc-ver {
        font-style: italic;
        font-size: 13px;
        margin-top: 5px;
    }
</style>