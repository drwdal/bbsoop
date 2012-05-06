<?php if ( isset ( $GLOBALS['reply_to_private_message'] ) ) { ?>
<h3>Replying to private message #<?php echo $GLOBALS['reply_to_private_message']->ID ( ); ?></h3>
<?php } ?>
<p><label for="private_message_body">Body</label> <textarea id="private_message_body" name="private_message[body]" cols="65" rows="7" class="xlarge" /><?php echo htmlspecialchars ( $_POST['message']['body'] ); ?></textarea></p>
<?php if ( $_SESSION['logged_in_user']['type'] >= MODERATOR_TYPE ) { ?>
<p><label for="private_message_expires_at">Expiration date</label> <input type="text" value="" name="private_message[expires_at]" class="medium has_calendar" id="private_message_expires_at" /> <span class="help">if empty, the message will never expire</span></p>
<?php } ?>
<?php if ( $GLOBALS['user_account']->type < intval ( $_SESSION['logged_in_user']['type'] ) ) { ?>
<p><label for="private_message_reply_allowed">Reply allowed</label> <input type="hidden" value="0" name="private_message[reply_allowed]" /><input type="checkbox" name="private_message[reply_allowed]" value="1" id="private_message_reply_allowed" checked="checked" /> <span class="help">if unchecked, the recipient will not be allowed to reply to your message</span></p>
<p><label for="private_message_anonymous">Anonymous</label> <input type="hidden" value="0" name="private_message[anonymous]" /><input type="checkbox" name="private_message[anonymous]" value="1" id="private_message_anonymous" /> <span class="help">if unchecked, the recipient will see your internal user ID along with the message</span></p>
<?php } ?>
<p><label>&nbsp;</label> <input type="submit" value="Send" /><?php nonce_for_form ( ); ?><input type="hidden" value="<?php echo htmlspecialchars ( $GLOBALS['ID'] ); ?>" name="private_message[to_user_ID]" /></p>
