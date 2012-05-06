<p class="float-right buttonized"><a href="javascript:void(0);" onclick="jQuery('#register_account').slideDown('fast');jQuery('#account_login').slideUp('fast',function(){if(jQuery('#account_login').css('display')=='none'){jQuery('#username_new').focus();}});">Create new account</a></p>
<h2><a href="javascript:void(0);" onclick="jQuery('#register_account').slideToggle('fast');jQuery('#account_login').slideToggle('fast',function(){if(jQuery('#account_login').css('display')=='none'){jQuery('#username_new').focus();}else{jQuery('#username').focus();}});">Please log in.</a></h2>
<div id="account_login">
	<form action="<?php echo BASE_URI ?>account/login" method="post" class="wrapper">
		<?php IMGBOARD_render_view ( 'common/_login' ); ?>
	</form>
</div>
<h2><a href="javascript:void(0);" onclick="jQuery('#register_account').slideToggle('fast');jQuery('#account_login').slideToggle('fast',function(){if(jQuery('#account_login').css('display')=='none'){jQuery('#username_new').focus();}else{jQuery('#username').focus();}});">New? Please register.</a></h2>
<div id="register_account" class="wrapper collapse c">
	<p>You are not required to divulge any personal information. Your username will never be visible to anybody—including the site administration. Your IP address will be (briefly) logged.</p>
	<form action="<?php echo BASE_URI ?>account/new" method="post" class="wrapper" id="user_account_register">
		<p><label for="username" class="mlarge">Username</label> <input type="text" value="" name="username" id="username_new" class="large" tabindex="1" /> <span class="help" id="username_help">must be unique; some are already taken</span></p>
		<div style="display: none;" class="wrapper c js_required">
			<p><label class="mlarge">&nbsp;</label><input type="button" value="Check for availability" tabindex="2" onclick="username_check();" id="username_check_button" /> <img src="<?php echo BASE_URI; ?>content/themes/default/assets/images/loader.gif" height="16" width="16" alt="loading" id="username_loading" style="display: none;" /></p>
		</div>
		<p><label for="password_new" class="mlarge">Password</label> <input type="password" value="" name="password_new" id="password_new" class="medium" tabindex="3" /></p>
		<p><label for="password_verify" class="mlarge">Verify password</label> <input type="password" value="" name="password_verify" id="password_verify" class="medium" tabindex="4" /> <span class="help">please type your password again to confirm</span></p>
		<p><label class="mlarge">&nbsp;</label><input type="submit" value="Create account" tabindex="5" /><?php nonce_for_form ( ); ?></p>
	</form>
</div>
<h3><a href="<?php echo BASE_URI; ?>account/password_reset/">Password reset</a></h3>
