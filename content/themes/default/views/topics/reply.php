<h3 class="float-right js_required" style="display: none;"><a href="<?php echo $GLOBALS['topic']->URI ( ); ?>" onclick="jQuery('#topic_body_wrapper').slideToggle('fast'); if ( this.innerHTML == 'View topic' ) { this.innerHTML = 'Hide topic'; } else { this.innerHTML = 'View topic'; } return false;">View topic</a></h3>
<div id="topic_body_wrapper" class="wrapper topic clear collapse">
	<?php global $user_accounts; $user_accounts = array ( ); generate_post ( $GLOBALS['topic'] ); ?>
	</div>
</div>
<hr class="a" />
<?php if ( isset ( $GLOBALS['reply_to_this'] ) && get_class ( $GLOBALS['reply_to_this'] ) == 'Reply' ) { ?>
<h3 class="clear float-right js_required" style="display: none;"><a href="<?php echo $GLOBALS['reply_to_this']->URI ( ); ?>" onclick="jQuery('#reply_body_wrapper').slideToggle('fast'); if ( this.innerHTML == 'View reply' ) { this.innerHTML = 'Hide reply'; } else { this.innerHTML = 'View reply'; } return false;">View reply</a></h3>
<div id="reply_body_wrapper" class="wrapper reply collapse clear">
	<?php generate_post ( $GLOBALS['reply_to_this'] ); ?>
	</div>
</div>
<hr class="a" />
<?php } ?>
<?php if ( isset ( $_POST['preview'] ) ) { ?>
<div id="preview" class="wrapper c">
	<h2>Preview:</h2>
	<?php echo parse_post_body ( $_POST['record']['body'] ); ?>
</div>
<hr class="a" />
<?php } ?>
<form action=""<?php if ( defined ( 'UPLOADS_ALLOWED' ) && UPLOADS_ALLOWED == 1 && $GLOBALS['topic']->media_count < TOPIC_MEDIA_COUNT_LIMIT ) { echo ' enctype="multipart/form-data"'; } ?> method="post" class="wrapper new-record clear c">
<?php if ( REPLIES_GO_LIVE_WHEN_POSTED != 1 ) { ?>
	<p class="help"><strong>Note:</strong> Your reply will be held in moderation before going live.</p>
<?php } ?>
	<?php IMGBOARD_render_view ( 'topics/_new_record' ); ?>
<?php
if ( defined ( 'UPLOADS_ALLOWED' ) && UPLOADS_ALLOWED == 1 ) {
	if ( $GLOBALS['topic']->media_count < TOPIC_MEDIA_COUNT_LIMIT ) {
		IMGBOARD_render_view ( 'topics/_file_upload' );
	} else { ?>
	<p><span class="help">This topic has reached its media limit.</span></p>
<?	}
} ?>
	<p class="clear"><input type="submit" value="Submit" tabindex="4" name="submit" /><input type="hidden" value="add" name="mode" /><?php nonce_for_form ( ); ?> <input type="submit" value="Preview" tabindex="5" name="preview" class="float-right" /></p>
</form>
<?php IMGBOARD_render_view ( 'common/_formatting_instructions' ); ?>
