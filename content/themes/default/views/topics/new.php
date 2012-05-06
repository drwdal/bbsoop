<?php if ( isset ( $_POST['preview'] ) ) { ?>
<div id="preview" class="wrapper c">
	<h2>Preview:</h2>
	<?php echo parse_post_body ( $_POST['record']['body'] ); ?>
</div>
<hr class="a" />
<?php } ?>
<form action=""<?php if ( defined ( 'UPLOADS_ALLOWED' ) && UPLOADS_ALLOWED == 1 ) { echo ' enctype="multipart/form-data"'; } ?> method="post" class="wrapper new-record">
<?php if ( TOPICS_GO_LIVE_WHEN_POSTED != 1 ) { ?>
	<p class="help"><strong>Note:</strong> Your topic will be held in moderation before going live.</p>
<?php } ?>
	<?php IMGBOARD_render_view ( 'topics/_new_record' ); ?>
<?php
if ( defined ( 'UPLOADS_ALLOWED' ) && UPLOADS_ALLOWED == 1 ) {
	IMGBOARD_render_view ( 'topics/_file_upload' );
} ?>
	<p class="clear"><input type="submit" value="Submit" tabindex="4" /><input type="hidden" value="add" name="mode" /><?php nonce_for_form ( ); ?> <input type="submit" value="Preview" tabindex="5" name="preview" class="float-right" /></p>
</form>
<?php IMGBOARD_render_view ( 'common/_formatting_instructions' ); ?>
