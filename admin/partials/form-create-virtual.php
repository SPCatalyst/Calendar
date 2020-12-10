<?php

$repo               = new SPC_Community_Calendar_Data_Repository();
$categories         = $repo->get_categories();
$categories_choices = spcc_array_key_value( $categories->get_items(), 'id', 'name' );

$account = $repo->get_account();
$permission = $account->get_item_param('permission');
?>

<div class="form-row">
    <label for="venue"><?php _e( 'Venue' ); ?> <span class="spcc-required">*</span></label>
    <input type="text" id="venue" name="venue" required class="form-control"
           placeholder="<?php _e( 'Enter venue' ); ?>">
</div>
<div class="form-row">
    <label for="image"><?php _e( 'Image' ); ?> <span class="spcc-required">*</span></label>
    <input type="file" id="image" name="image" required placeholder="<?php _e( 'Enter image' ); ?>">
</div>
<div class="form-row">
    <div class="form-col-6">
        <label for="category"><?php _e( 'Category' ); ?> <span class="spcc-required">*</span></label>
        <select id="category" name="category" <?php echo ! empty( $categories_choices ) ? 'required' : ''; ?>
                class="form-control">
			<?php foreach ( $categories_choices as $value => $name ): ?>
                <option value="<?php echo $value; ?>"><?php echo $name; ?></option>
			<?php endforeach; ?>
        </select>
    </div>
    <div class="form-col-6">
        <label for="type"><?php _e( 'Type' ); ?> <span class="spcc-required">*</span></label>
        <select id="type" name="type" required class="form-control">
            <option value="private" selected><?php _e( 'Private / Local' ); ?></option>
            <option <?php disabled('limited', $permission); ?> value="public"><?php _e( 'Public / Network' ); ?></option>
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-col-6">
        <label for="host"><?php _e( 'Host' ); ?></label>
        <input type="text" id="host" name="host" class="form-control">
    </div>
    <div class="form-col-6">
        <label for="cost"><?php _e( 'Cost' ); ?></label>
        <input type="text" id="cost" name="cost" class="form-control">
    </div>
</div>

<div class="form-row">
    <div class="form-col-6">
        <label for="facebook"><?php _e( 'Facebook' ); ?></label>
        <input type="text" id="facebook" name="facebook" class="form-control">
    </div>
    <div class="form-col-6">
        <label for="twitter"><?php _e( 'Twitter' ); ?></label>
        <input type="text" id="twitter" name="twitter" class="form-control">
    </div>
</div>

<div class="form-row">
    <div class="form-col-6">
        <label for="instagram"><?php _e( 'Instagram' ); ?></label>
        <input type="text" id="instagram" name="instagram" class="form-control">
    </div>
    <div class="form-col-6">
        <label for="pinterest"><?php _e( 'Pinterest' ); ?></label>
        <input type="text" id="pinterest" name="pinterest" class="form-control">
    </div>
</div>

<div class="form-row">
    <div class="form-col-6">
        <label for="website"><?php _e( 'Conference URL' ); ?> <span class="spcc-required">*</span></label>
        <input type="text" id="website" name="website" class="form-control">
    </div>
    <div class="form-col-6">
        <label for="tickets_url"><?php _e( 'Tickets URL' ); ?></label>
        <input type="text" id="tickets_url" name="tickets_url" class="form-control">
    </div>
</div>