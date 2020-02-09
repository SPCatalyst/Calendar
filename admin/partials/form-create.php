<?php
$repo               = new SPC_Community_Calendar_Data_Repository();
$categories         = $repo->get_categories();
$categories_choices = spcc_array_key_value( $categories->get_items(), 'id', 'name' );
$states             = array(
	'FL' => 'Florida',
);
?>

<form id="createEventForm" class="event-form-wrap" method="POST" action="">
    <div class="form-row status-wrapper-row">
        <div class="status-wrapper"></div>
    </div>
    <div class="form-row">
        <label for="title"><?php _e( 'Title' ); ?></label>
        <input type="text" id="title" name="title" required class="form-control"
               placeholder="<?php _e( 'Enter Title' ); ?>">
    </div>
    <div class="form-row">
        <label for="description"><?php _e( 'Description' ); ?></label>
        <textarea type="text" id="description" name="description" required class="form-control"
                  placeholder="<?php _e( 'Enter Description' ); ?>"></textarea>
    </div>
    <div class="form-row">
        <div class="form-col-6">
            <label for="start"><?php _e( 'Start Date' ); ?></label>
            <input type="text" id="start" name="start" required class="form-control datetimepicker"
                   data-timepicker="true" data-date-format="yyyy-mm-dd" data-time-format="hh:ii:00"
                   placeholder="<?php _e( 'Enter start date' ); ?>">
        </div>
        <div class="form-col-6">
            <label for="end"><?php _e( 'End Date' ); ?></label>
            <input type="text" id="end" name="end" required class="form-control datetimepicker" data-timepicker="true"
                   data-date-format="yyyy-mm-dd" data-time-format="hh:ii:00"
                   placeholder="<?php _e( 'Enter end date' ); ?>">
        </div>
    </div>
    <div class="form-row">
        <label for="venue"><?php _e( 'Venue' ); ?></label>
        <input type="text" id="venue" name="venue" required class="form-control"
               placeholder="<?php _e( 'Enter venue' ); ?>">
    </div>
    <div class="form-row">
        <div class="form-col-6">
            <label for="address"><?php _e( 'Address Line 1' ); ?></label>
            <input type="text" id="address" name="address" required class="form-control" placeholder="<?php _e( 'Enter address' ); ?>">
        </div>
        <div class="form-col-6">
            <label for="address2"><?php _e( 'Address Line 2' ); ?></label>
            <input type="text" id="address2" name="address2" class="form-control" placeholder="<?php _e( 'Enter address' ); ?>">
        </div>
    </div>
    <div class="form-row">
        <div class="form-col-4">
            <label for="city"><?php _e( 'City' ); ?></label>
            <input type="text" id="city" name="city" required class="form-control"
                   placeholder="<?php _e( 'Enter city' ); ?>">
        </div>
        <div class="form-col-4">
            <label for="state"><?php _e( 'State' ); ?></label>
            <select id="state" name="state" required class="form-control">
				<?php foreach ( $states as $key => $name ): ?>
                    <option value="<?php echo $key; ?>"><?php echo $name; ?></option>
				<?php endforeach; ?>
            </select>
        </div>
        <div class="form-col-4">
            <label for="postal_code"><?php _e( 'Postal Code' ); ?></label>
            <input type="text" id="postal_code" required name="postal_code" class="form-control"
                   placeholder="<?php _e( 'Enter postal code' ); ?>">
        </div>
    </div>
    <div class="form-row">
        <label for="image"><?php _e( 'Image' ); ?></label>
        <input type="file" id="image" name="image" required placeholder="<?php _e( 'Enter image' ); ?>">
    </div>
    <div class="form-row">
        <div class="form-col-6">
            <label for="category"><?php _e( 'Category' ); ?></label>
            <select id="category" name="category" <?php echo ! empty( $categories_choices ) ? 'required' : ''; ?>
                    class="form-control">
				<?php foreach ( $categories_choices as $value => $name ): ?>
                    <option value="<?php echo $value; ?>"><?php echo $name; ?></option>
				<?php endforeach; ?>
            </select>
        </div>
        <div class="form-col-6">
            <label for="type"><?php _e( 'Type' ); ?></label>
            <select id="type" name="type" required class="form-control">
                <option value="private" selected><?php _e( 'Private' ); ?></option>
                <option value="public"><?php _e( 'Public' ); ?></option>
            </select>
        </div>
    </div>

    <div class="form-row">
        <div class="form-col-6">
            <label for="host"><?php _e( 'Host' ); ?></label>
            <input type="text" id="host" name="host" required class="form-control">
        </div>
        <div class="form-col-6">
            <label for="cost"><?php _e( 'Cost' ); ?></label>
            <input type="text" id="cost" name="cost" class="form-control">
        </div>
    </div>

    <div class="form-row">
        <div class="form-col-6">
            <label for="facebook"><?php _e( 'Facebook' ); ?></label>
            <input type="text" id="facebook" name="facebook" required class="form-control">
        </div>
        <div class="form-col-6">
            <label for="twitter"><?php _e( 'Twitter' ); ?></label>
            <input type="text" id="twitter" name="twitter" class="form-control">
        </div>
    </div>

    <div class="form-row">
        <div class="form-col-6">
            <label for="instagram"><?php _e( 'Instagram' ); ?></label>
            <input type="text" id="instagram" name="instagram" required class="form-control">
        </div>
        <div class="form-col-6">
            <label for="pinterest"><?php _e( 'Pinterest' ); ?></label>
            <input type="text" id="pinterest" name="pinterest" class="form-control">
        </div>
    </div>

    <div class="form-row">
        <label for="website"><?php _e( 'Website' ); ?></label>
        <input type="text" id="website" name="website" required class="form-control">
    </div>

    <div class="form-row form-row-footer">
        <input type="hidden" name="country" value="US">
        <button type="submit" class="button-primary"><?php _e( 'Submit' ); ?></button>
    </div>
</form>