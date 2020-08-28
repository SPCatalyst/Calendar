<?php

// Data Repository
$repo    = new SPC_Community_Calendar_Data_Repository();
$account = $repo->get_account();

$token = get_option( 'spcc_token' );

if ( ! $account->is_error() ) {
	$settings_screen = 'settings';
} else {
	$settings_screen = 'login';
}

?>

<div class="wrap">

	<?php if ( $account->is_error() ): ?>
        <h2><?php _e( 'Community Calendar Account', 'spcc' ); ?></h2>
	<?php else: ?>
        <h2><?php _e( 'Community Calendar Settings', 'spcc' ); ?></h2>
	<?php endif; ?>

	<?php if ( $settings_screen === 'settings' ): ?>
        <div class="section-wrap">
            <div class="section-status"></div>
            <div class="section-form">
	            <?php include( 'form-settings.php' ); ?>
            </div>
        </div>

	<?php else: ?>

        <div id="login-wrap" class="section-wrap">
            <div class="section-status"></div>
			<div class="section-form">
				<?php include( 'form-login.php' ); ?>
            </div>
        </div>

        <div id="register-wrap" class="section-wrap" style="display:none;">
            <div class="section-status"></div>
	        <div class="section-form">
		        <?php include( 'form-register.php' ); ?>
            </div>
        </div>

	<?php endif; ?>
</div>

<p class="spcc-ver">
	<?php echo sprintf( __( 'Catalyst Community Calendar v%s' ), SPC_COMMUNITY_CALENDAR_VERSION ); ?>
</p>


<div id="requestAccess" class="modal">
    <H3><?php _e('Request network access'); ?></H3>
	<form method="POST" id="requestAccessForm">
        <div class="spcc-form">
            <div class="spcc-form-row">
                <label for="message">Describe the purpose for your request</label>
                <textarea class="spcc-form-control" id="message" name="message"></textarea>
            </div>
            <div class="spcc-form-row">
                <button type="submit" class="button"><?php _e('Submit'); ?></button>
            </div>
        </div>
    </form>
</div>

<style>
    form.section-form .settings-row p {
        margin-top: 0;
        margin-bottom: 0;
    }

    form.section-form .settings-row {
        margin-bottom: 15px;
    }

    form.section-form label {
        font-weight: bold;
        display: block;
        margin-bottom: 5px;
    }

    form.section-form {
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
    .settings-row {
        border-bottom: 1px solid #f0f0f0;
        margin-bottom: 20px;
        display: block;
        padding-bottom: 15px;
    }
    .settings-row:last-child {
        border-bottom: 0;
    }
    .spcc-italic {
        font-style: italic;
    }
    .color-schemes {
        max-width: 800px;
    }
    .color-scheme {
        display: inline-block;
    }
    @media(min-width: 768px) {
        .color-scheme {
            width: 20%;
        }
    }
    @media(max-width: 767px) {
        .color-scheme {
            width: 100%;
        }
    }
</style>