<?php
// This message should be plain text; no HTML is sent.
?>
A password reset request was recieved for your account from the IP address <?php echo $_SERVER['REMOTE_ADDR'] ?>. If you did not initiate this request, please disregard it.

Username: <?php echo $GLOBALS['user_account']->username; ?>

Temporary password: <?php echo $GLOBALS['user_account']->temp_password; ?>

Go to this URL and enter your temporary password to reset your account password:
http://authorizedclone.com/bbsoop/account/password_reset/login

=== this email was automatically generated ===