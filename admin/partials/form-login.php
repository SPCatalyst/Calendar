<form class="section-form" id="form-login" method="POST" action="">
    <h3 class="section-title"><?php _e('Login'); ?></h3>
    <div class="settings-row">
        <label for="email"><?php _e( 'Email', 'spcc' ); ?><span class="required">*</span></label>
        <input type="email" name="email" id="email" class="required"
               value="<?php echo isset( $_POST['email'] ) ? $_POST['email'] : null; ?>">
    </div>
    <div class="settings-row">
        <label for="password"><?php _e( 'Password', 'spcc' ); ?></label>
        <input type="password" name="password" id="password">
    </div>
    <div class="settings-row">
        <p>Don't have an account? <a href="#" class="spcc-form-switch" data-nextform="register">Sign up</a></p>
    </div>
    <div class="settings-row">
        <input type="hidden" name="req_type" value="login">
        <span class="spcc-spinner"></span>
        <button type="submit" class="button-primary spcc-submit"><?php _e( 'Login', 'spcc' ); ?></button>
    </div>
</form>