<form action="" method="post" class="wrapper c">
	<fieldset class="wrapper c">
		<h2 id="change_username">Change your username</h2>
		<p><label class="mlarge">Current username</label> <input type="text" value="<?php echo htmlspecialchars ( $GLOBALS['logged_in_user']->username ); ?>" readonly="readonly" class="large" /></p>
		<p><label for="username_new" class="mlarge">New username</label> <input type="text" value="" id="username_new" name="username_new" class="large" maxlength="60" /> <span class="help" id="username_help">must be unique; some are already taken</span></p>
		<div style="display: none;" class="wrapper c js_required">
			<p><label class="mlarge">&nbsp;</label><input type="button" value="Check for availability" tabindex="2" onclick="username_check();" id="username_check_button" /> <img src="<?php echo BASE_URI; ?>content/themes/default/assets/images/loader.gif" height="16" width="16" alt="loading" id="username_loading" style="display: none;" /></p>
			<p><label for="username_password_confirm" class="mlarge">Password</label> <input type="password" value="" id="username_password_confirm" name="username_password_confirm" class="medium" maxlength="60" /> <span class="help">enter your current password to confirm this change</span></p>
		</div>
		<p><label class="mlarge">&nbsp;</label> <input type="submit" value="Update account" /></p>
	</fieldset>
	<fieldset class="wrapper c">
		<h2 id="change_password">Change your password</h2>
		<p><label for="password_current" class="mlarge">Current password</label> <input type="password" value="<?php echo htmlentities ( ( ( array_key_exists ( 'temp_password', $_SESSION ) ) ? $_SESSION['temp_password'] : '' ), ENT_COMPAT, APP_CHARSET ); ?>" id="password_current" name="password_current" class="medium" maxlength="60" /> <span class="help">enter your current password to confirm this change</span></p>
		<p><label for="password_new" class="mlarge">New password</label> <input type="password" value="" id="password_new" name="password_new" class="medium" maxlength="60" /></p>
		<p><label for="password_new_confirm" class="mlarge">Confirm password</label> <input type="password" value="" id="password_new_confirm" name="password_new_confirm" class="medium" maxlength="60" /> <span class="help">type your new password again to confirm it has been entered correctly</span></p>
		<p><label class="mlarge">&nbsp;</label> <input type="submit" value="Update account" /></p>
	</fieldset>
	<fieldset class="wrapper c">
		<h2 id="change_email_address">Email address</h2>
<?php if ( defined ( 'SEND_EMAILS') && SEND_EMAILS != 1 ) { ?>
		<p>Note: emails are currently disabled. You may add an email address, but it cannot be used to reset a password.</p>
<?php } ?>
		<p class="clear"><label class="mlarge">Email address</label> <input type="text" value="<?php echo ( ( empty ( $GLOBALS['logged_in_user']->email_address ) ) ? '(none)' : htmlspecialchars ( $GLOBALS['logged_in_user']->email_address ) ); ?>" class="large" maxlength="125" readonly="readonly" /> <span class="help">this email address can be used to reset your password</span></p>
		<p class="clear"><label for="email_address_new" class="mlarge">New email address</label> <input type="text" value="" id="email_address_new" name="email_address_new" class="large" maxlength="125" /> <?php if ( ! empty ( $GLOBALS['logged_in_user']->email_address ) ) { ?><span class="help">a confirmation email will be sent to your current address before the change occurs</span><?php } ?></p>
		<p class="clear"><label for="email_address_password_confirm" class="mlarge">Password</label> <input type="password" value="" id="email_address_password_confirm" name="email_address_password_confirm" class="medium" maxlength="60" /> <span class="help">enter your current password to confirm this change</span></p>
		<p><label class="mlarge">&nbsp;</label> <input type="submit" value="Update account" /></p>
	</fieldset>
	<?php nonce_for_form ( ); ?>
</form>
