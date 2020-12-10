<?php
/* @var array $event */
$repo                = new SPC_Community_Calendar_Data_Repository();
$categories          = $repo->get_categories();
$categories_choices  = spcc_array_key_value( $categories->get_items(), 'id', 'name' );

$states = array(
        'FL' => 'Florida',
);
if(!isset($event['post_title']) || !isset($event['ID'])) {
    die('Event not found.');
}
$attendance = isset($event['meta']['event_attendance']) ? $event['meta']['event_attendance'] : 'physical';
$is_virtual = $attendance === 'virtual';

$account = $repo->get_account();
$permission = $account->get_item_param('permission');

?>
<form id="editEventForm" class="event-form-wrap" method="POST" action="">
	<div class="form-row status-wrapper-row">
		<div class="status-wrapper"></div>
	</div>
	<div class="form-row">
		<label for="title"><?php _e('Title'); ?> <span class="required">*</span></label>
		<input type="text" id="title" name="title" required value="<?php echo $event['post_title']; ?>" class="form-control" placeholder="<?php _e('Enter Title'); ?>">
	</div>
	<div class="form-row">
		<label for="description"><?php _e('Description'); ?> <span class="required">*</span></label>
		<textarea type="text" id="description" name="description" required class="form-control" placeholder="<?php _e('Enter Description'); ?>"><?php echo $event['post_content']; ?></textarea>
	</div>
	<div class="form-row">
		<div class="form-col-6">
			<label for="start"><?php _e('Start Date'); ?> <span class="required">*</span></label>
			<input type="text" id="start" name="start" required value="<?php echo isset($event['meta']['event_start']) ? $event['meta']['event_start'] : ''; ?>" class="form-control datetimepicker" data-timepicker="true" data-date-format="yyyy-mm-dd" data-time-format="hh:ii:00" placeholder="<?php _e('Enter start date'); ?>">
		</div>
		<div class="form-col-6">
			<label for="end"><?php _e('End Date'); ?> <span class="required">*</span></label>
			<input type="text" id="end" name="end" required value="<?php echo isset($event['meta']['event_end']) ? $event['meta']['event_end'] : ''; ?>" class="form-control datetimepicker" data-timepicker="true" data-date-format="yyyy-mm-dd" data-time-format="hh:ii:00" placeholder="<?php _e('Enter end date'); ?>">
		</div>
	</div>
	<div class="form-row">
        <?php if(!$is_virtual): ?>
            <label for="venue"><?php _e('Venue'); ?> <span class="required">*</span></label>
            <input type="text" id="venue" name="venue" required value="<?php echo isset($event['meta']['event_venue']) ? $event['meta']['event_venue'] : ''; ?>" class="form-control" placeholder="<?php _e('Enter venue'); ?>">
        <?php else: ?>
            <div class="form-col-6">
                <label for="venue"><?php _e('Venue'); ?> <span class="required">*</span></label>
                <input type="text" id="venue" name="venue" required value="<?php echo isset($event['meta']['event_venue']) ? $event['meta']['event_venue'] : ''; ?>" class="form-control" placeholder="<?php _e('Enter venue'); ?>">
            </div>
            <div class="form-col-6">
                <label for="parking"><?php _e('Parking'); ?> <span class="required">*</span></label>
                <input type="text" id="parking" name="parking" required value="<?php echo isset($event['meta']['event_parking']) ? $event['meta']['event_parking'] : ''; ?>" class="form-control" placeholder="<?php _e('Enter parking'); ?>">
            </div>
        <?php endif; ?>

	</div>


    <?php if(!$is_virtual): ?>
    <div class="form-row">
        <div class="form-col-6">
            <label for="address"><?php _e( 'Address Line 1' ); ?><span class="required">*</span></label>
            <input type="text" id="address" name="address" required class="form-control"
                   placeholder="<?php _e( 'Enter address' ); ?>" value="<?php echo isset($event['meta']['event_address']) ? $event['meta']['event_address'] : ''; ?>">
        </div>
        <div class="form-col-6">
            <label for="address2"><?php _e( 'Address Line 2' ); ?></label>
            <input type="text" id="address2" name="address2" class="form-control"
                   placeholder="<?php _e( 'Enter address' ); ?>" value="<?php echo isset($event['meta']['event_address2']) ? $event['meta']['event_address2'] : ''; ?>">
        </div>
    </div>

	<div class="form-row">
		<div class="form-col-4">
			<label for="city"><?php _e('City'); ?> <span class="spcc-required">*</span></label>
			<input type="text" id="city" name="city" required value="<?php echo isset($event['meta']['event_city']) ? $event['meta']['event_city'] : ''; ?>" class="form-control" placeholder="<?php _e('Enter city'); ?>">
		</div>
		<div class="form-col-4">
			<label for="state"><?php _e('State'); ?> <span class="spcc-required">*</span></label>
			<select id="state" name="state" required  class="form-control">
				<?php foreach($states as $key => $name): ?>
                <option value="<?php echo $key; ?>" <?php echo isset($event['meta']['event_state']) && $event['meta']['event_state'] === $key ? 'selected' : ''; ?>><?php echo $name; ?></option>
                <?php endforeach; ?>
			</select>
		</div>
		<div class="form-col-4">
			<label for="postal_code"><?php _e('Postal Code'); ?> <span class="spcc-required">*</span></label>
			<input type="text" id="postal_code" name="postal_code" required value="<?php echo isset($event['meta']['event_postal_code']) ? $event['meta']['event_postal_code'] : ''; ?>" class="form-control" placeholder="<?php _e('Enter postal code'); ?>">
		</div>
	</div>

    <?php endif; ?>

	<div class="form-row">
		<label for="image"><?php _e('Image'); ?></label>
		<input type="file" id="image" name="image" placeholder="<?php _e('Enter image'); ?>">
	</div>
	<div class="form-row">
        <div class="form-col-6">
            <label for="category"><?php _e('Category'); ?> <span class="spcc-required">*</span></label>
            <select id="category" name="category" <?php echo !empty($categories_choices) ? 'required' : ''; ?> class="form-control">
		        <?php foreach($categories_choices as $value => $name): ?>
                    <option value="<?php echo $value; ?>" <?php echo isset($event['categories'][$value]) ? 'selected' : ''; ?>><?php echo $name; ?></option>
		        <?php endforeach; ?>
            </select>
        </div>
        <div class="form-col-6">
	        <?php $is_disabled = isset($event['meta']['event_type']) && in_array($event['meta']['event_type'], array('private', 'public')); ?>
            <label for="type"><?php _e('Type'); ?> <span class="spcc-required">*</span></label>
            <select id="type" name="type" required class="form-control" <?php echo $is_disabled ? 'readonly' : ''; ?>>
                <option value="private" <?php echo isset($event['meta']['event_type']) && $event['meta']['event_type'] === 'private' ? 'selected' : ''; ?>><?php _e('Private / Local'); ?></option>
                <option <?php disabled('limited', $permission); ?> value="public" <?php echo isset($event['meta']['event_type']) && $event['meta']['event_type'] === 'public' ? 'selected' : ''; ?>><?php _e('Public / Network'); ?></option>
            </select>
        </div>
	</div>

    <div class="form-row">
        <div class="form-col-6">
            <label for="host"><?php _e( 'Host' ); ?></label>
            <input type="text" value="<?php echo isset($event['meta']['event_host']) ? $event['meta']['event_host'] : ''; ?>" id="host" name="host" required class="form-control">
        </div>
        <div class="form-col-6">
            <label for="cost"><?php _e( 'Cost' ); ?></label>
            <input type="text" value="<?php echo isset($event['meta']['event_cost']) ? $event['meta']['event_cost'] : ''; ?>" id="cost" name="cost" class="form-control" >
        </div>
    </div>

    <div class="form-row">
        <div class="form-col-6">
            <label for="facebook"><?php _e( 'Facebook' ); ?></label>
            <input type="text" value="<?php echo isset($event['meta']['event_facebook']) ? $event['meta']['event_facebook'] : ''; ?>" id="facebook" name="facebook" required class="form-control">
        </div>
        <div class="form-col-6">
            <label for="twitter"><?php _e( 'Twitter' ); ?></label>
            <input type="text" value="<?php echo isset($event['meta']['event_twitter']) ? $event['meta']['event_twitter'] : ''; ?>" id="twitter" name="twitter" class="form-control">
        </div>
    </div>

    <div class="form-row">
        <div class="form-col-6">
            <label for="instagram"><?php _e( 'Instagram' ); ?></label>
            <input type="text" value="<?php echo isset($event['meta']['event_instagram']) ? $event['meta']['event_instagram'] : ''; ?>" id="instagram" name="instagram" required class="form-control">
        </div>
        <div class="form-col-6">
            <label for="pinterest"><?php _e( 'Pinterest' ); ?></label>
            <input type="text" value="<?php echo isset($event['meta']['event_pinterest']) ? $event['meta']['event_pinterest'] : ''; ?>" id="pinterest" name="pinterest" class="form-control">
        </div>
    </div>

    <div class="form-row">
        <div class="form-col-6">
        <?php if($is_virtual): ?>
            <label for="website"><?php _e( 'Conference URL'); ?><span class="spcc-required">*</span></label>
        <?php else: ?>
            <label for="website"><?php _e( 'Website'); ?></label>
        <?php endif; ?>
        <input type="text" value="<?php echo isset($event['meta']['event_website']) ? $event['meta']['event_website'] : ''; ?>" id="website" name="website" required class="form-control"">
        </div>
        <div class="form-col-6">
            <label for="tickets_url"><?php _e( 'Tickets URL' ); ?></label>
            <input type="text" value="<?php echo isset($event['meta']['event_tickets_url']) ? $event['meta']['event_tickets_url'] : ''; ?>" id="tickets_url" name="tickets_url" class="form-control">
        </div>
    </div>

	<div class="form-row form-row-footer">
        <input type="hidden" name="event_id" value="<?php echo $event['ID']; ?>">
		<input type="hidden" name="country" value="US">
        <input type="hidden" name="attendance" value="<?php echo $attendance; ?>">
		<button type="submit" class="button-primary"><?php _e('Update'); ?></button>
	</div>
</form>