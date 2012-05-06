<form action="" method="post" class="wrapper c" id="reset">
	<p><label for="recovery_string" class="large">username or email address</label> <input type="text" value="" name="recovery_string" id="recovery_string" class="large" tabindex="4" /> <span class="help">this reset is only available if you have previously associated an email address with your account</span></p>
	<p><label class="large">&nbsp;</label><input type="submit" name="reset" value="Reset password" tabindex="5" /><?php nonce_for_form ( ); ?></p>
</form>
<h2>Log in…</h2>
<div id="account_login" class="wrapper c">
	<form action="" method="post" class="wrapper c">
		<?php IMGBOARD_render_view ( 'common/_login' ); ?>
	</form>
</div>
