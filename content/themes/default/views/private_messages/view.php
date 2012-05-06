<h3>From <?php if ( isset ( $_SESSION['logged_in_user'] ) && $_SESSION['logged_in_user']['type'] >= MODERATOR_TYPE ) { echo '<a href="' . BASE_URI . 'admin/user/' . $GLOBALS['private_message']->from_user_ID . '/" class="inline">'; } ?><?php echo htmlspecialchars ( ( $GLOBALS['private_message']->anonymous == 0 ) ? 'user ' . $GLOBALS['private_message']->from_user_ID : 'Anonymous user' ); ?><?php if ( isset ( $_SESSION['logged_in_user'] ) && $_SESSION['logged_in_user']['type'] >= MODERATOR_TYPE ) { echo '</a>'; } ?></h3>
<div id="private_message" class="wrapper c">
	<? echo parse_post_body ( $GLOBALS['private_message']->body ); ?>
<?php if ( $GLOBALS['private_message']->reply_allowed == 1 ) { ?>
	<p class="reply-to-message">
		<a href="<?php echo $GLOBALS['private_message']->quote_URI ( ); ?>" title="Quote this message while replying to it">Quote</a>
		<a href="<?php echo $GLOBALS['private_message']->reply_URI ( ); ?>" title="Reply to this message">Reply</a>
	</p>
<?php } ?>
</div>
