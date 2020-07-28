<form id="createEventForm" class="event-form-wrap" method="POST" action="">
    <div class="form-row status-wrapper-row">
        <div class="status-wrapper"></div>
    </div>
    <div class="form-row">
        <label for="title"><?php _e( 'Title' ); ?> <span class="spcc-required">*</span></label>
        <input type="text" id="title" name="title" required class="form-control"
               placeholder="<?php _e( 'Enter Title' ); ?>">
    </div>
    <div class="form-row">
        <label for="description"><?php _e( 'Description' ); ?> <span class="spcc-required">*</span></label>
        <textarea type="text" id="description" name="description" required class="form-control"
                  placeholder="<?php _e( 'Enter Description' ); ?>"></textarea>
    </div>
    <div class="form-row">
        <label for="attendance"><?php _e( 'Attendance' ); ?> <span class="spcc-required">*</span></label>
        <select class="form-control" name="attendance" id="attendance">
            <option>---</option>
            <option value="physical"><?php _e('Physical'); ?></option>
            <option value="virtual"><?php _e('Virtual'); ?></option>
        </select>
    </div>


    <div class="form-row">
        <div class="form-col-6">
            <label for="start"><?php _e( 'Start Date' ); ?> <span class="spcc-required">*</span></label>
            <input type="text" id="start" name="start" autocomplete="off" required class="form-control datetimepicker"
                   data-timepicker="true" data-date-format="yyyy-mm-dd" data-time-format="hh:ii:00"
                   placeholder="<?php _e( 'Enter start date' ); ?>">
        </div>
        <div class="form-col-6">
            <label for="end"><?php _e( 'End Date' ); ?> <span class="spcc-required">*</span></label>
            <input type="text" id="end" name="end" autocomplete="off" required class="form-control datetimepicker" data-timepicker="true"
                   data-date-format="yyyy-mm-dd" data-time-format="hh:ii:00"
                   placeholder="<?php _e( 'Enter end date' ); ?>">
        </div>
    </div>
    <div class="form-row">
        <label for="venue"><?php _e( 'Venue' ); ?> <span class="spcc-required">*</span></label>
        <input type="text" id="venue" name="venue" required class="form-control"
               placeholder="<?php _e( 'Enter venue' ); ?>">
    </div>


    <div class="form-attendance-dependant"></div>


    <div class="form-row form-row-footer">
        <button type="submit" class="button-primary"><?php _e( 'Submit' ); ?></button>
    </div>
</form>