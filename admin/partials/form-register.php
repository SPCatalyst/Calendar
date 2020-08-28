<form class="section-form" id="form-register" method="POST" action="">
    <div class="settings-row">
        <label for="name"><?php _e( 'Name', 'spcc' ); ?></label>
        <input type="text" name="name" id="name" class="required">
    </div>
    <div class="settings-row">
        <label for="email"><?php _e( 'Email', 'spcc' ); ?><span class="required">*</span></label>
        <input type="email" name="email" id="email" class="required">
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
        <p>Already have account? <a href="#" class="spcc-form-switch" data-nextform="login">Log in</a></p>
    </div>
    <div class="settings-row">
        <input type="hidden" name="req_type" value="register">
        <span class="spcc-spinner"></span>
        <button type="submit" class="button-primary"><?php _e( 'Register', 'spcc' ); ?></button>
    </div>
</form>