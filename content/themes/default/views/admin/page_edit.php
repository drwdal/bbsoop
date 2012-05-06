<form action="" method="post" class="wrapper c">
	<p><label for="record_title" class="block">Title</label> <input type="text" value="<?php echo htmlentities ( $GLOBALS['page']->title, ENT_NOQUOTES, APP_CHARSET ); ?>" name="record[title]" id="record_title" class="full_width large-text" tabindex="1" /></p>
	<p><textarea id="record_body" name="record[body]" class="full_width" cols="60" rows="16" tabindex="2"><?php echo htmlentities ( $GLOBALS['page']->body, ENT_NOQUOTES, APP_CHARSET ); ?></textarea></p>
	<p><label for="record_URI" class="xsmall">URI</label> <?php echo BASE_URI; ?><input type="text" value="<?php echo htmlentities ( $GLOBALS['page']->URI, ENT_NOQUOTES, APP_CHARSET ); ?>" name="record[URI]" id="record_URI" class="large" tabindex="3" /> <span class="help">custom URI for this page; periods not allowed; leave blank for none</span></p>
	<p><label for="record_status" class="xsmall">Status</label> <select name="record[status]" id="record_status" class="mlarge" tabindex="4"><?php foreach ( $GLOBALS['post_status'] as $status ) { ?><option<?php if ( $GLOBALS['page']->status == $status ) { echo ' selected="selected"'; } ?>><?php echo $status; ?></option><?php } ?></select></p>
	<p><input type="submit" value="Save" tabindex="5" /><?php nonce_for_form ( ); ?></p>
</form>
<?php IMGBOARD_render_view ( 'common/_formatting_instructions' ); ?>
