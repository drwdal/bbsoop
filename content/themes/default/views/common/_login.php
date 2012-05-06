	<p><label for="username" class="xsmall">Username</label> <input type="text" value="" name="username" id="username" class="large" tabindex="1" /></p>
	<p><label for="password" class="xsmall">Password</label> <input type="password" value="" name="password" id="password" class="medium" tabindex="2" /> <!--a href="<?php echo BASE_URI; ?>account/password_reset" title="Reset your password through email.">forgot your password?</a--></p>
	<p><label class="xsmall">&nbsp;</label><input type="submit" value="Log in" tabindex="3" /><?php nonce_for_form ( ); ?></p>
