<?php if ( isset ( $_POST['preview'] ) ) { ?>
<div id="preview" class="wrapper c">
	<h2>Preview:</h2>
	<?php echo parse_post_body ( $_POST['record']['body'] ); ?>
</div>
<hr class="a" />
<?php } ?>
<form action="" method="post" class="wrapper new-record">
<?php if ( BULLETINS_GO_LIVE_WHEN_POSTED != 1 && $_SESSION['logged_in_user']['type'] < USER_LEVEL_EXEMPT_FROM_BULLETIN_POST_TIMING_LIMITS ) { ?>
	<p class="help"><strong>Note:</strong> Your bulletin will be held in moderation before going live.</p>
<?php } ?>
<?php if ( $_SESSION['logged_in_user']['type'] >= USER_LEVEL_EXEMPT_FROM_BULLETIN_POST_TIMING_LIMITS ) { ?>
	<p class="help"><strong>Note:</strong> Your bulletin will be posted immediately, without moderation.</p>
<?php } ?>
	<p><textarea id="record_body" name="record[body]" class="full_width" cols="60" rows="16" tabindex="2"><?php echo htmlentities ( $_POST['record']['body'], ENT_NOQUOTES, APP_CHARSET ); ?></textarea><input type="hidden" value="<?php echo htmlentities ( $_POST['record']['preview_ID'], ENT_NOQUOTES, APP_CHARSET ); ?>" name="record[preview_ID]" /></p>
	<p class="clear"><input type="submit" value="Submit" tabindex="4" /><input type="hidden" value="add" name="mode" /><?php nonce_for_form ( ); ?> <input type="submit" value="Preview" tabindex="5" name="preview" class="float-right" /></p>
</form>
<?php IMGBOARD_render_view ( 'common/_formatting_instructions' ); ?>
