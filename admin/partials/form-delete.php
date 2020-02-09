<?php
/* @var array $event */
if ( ! isset( $event['post_title'] ) || ! isset( $event['ID'] ) ) {
	die( 'Event not found.' );
}
?>
<form id="deleteEventForm" class="event-form-wrap" method="POST" action="">
    <div class="form-row status-wrapper-row">
        <div class="status-wrapper"></div>
    </div>
    <div class="form-row">
        <h2><?php _e( 'Delete event' ); ?></h2>
        <p><?php _e( 'Are you sure you want to delete this event? This action can not be undone.' ); ?></p>
    </div>
    <div class="form-row form-row-footer">
        <input type="hidden" name="event_id" value="<?php echo $event['ID']; ?>">
        <button type="submit" class="button-primary"><?php _e( 'Delete' ); ?></button>
    </div>
</form>