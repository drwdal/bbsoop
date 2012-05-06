<?php if ( TRIPCODES == 1 ) { ?>
	<p><label for="record_name" class="block"><strong>Name</strong>#tripcode <span class="help">optional</span></label> <input type="text" maxlength="140" value="<?php echo htmlentities ( ( ( array_key_exists ( 'logged_in_user', $_SESSION ) && array_key_exists ( 'last_name', $_SESSION['logged_in_user'] ) ) ? $_SESSION['logged_in_user']['last_name'] : '' ), ENT_COMPAT, APP_CHARSET ); ?>" id="record_name" name="record[name]" tabindex="1" class="large" /></p>
<?php } ?>
<?php if ( $GLOBALS['action'] == 'new' ) { ?>
	<p><label for="record_title" class="block">Title <span class="float-right help" id="title-character-count"><?php echo TOPIC_TITLE_CHARACTERS_MAXIMUM; ?> characters allowed</span></label> <input type="text" maxlength="<?php echo TOPIC_TITLE_CHARACTERS_MAXIMUM; ?>" id="record_title" name="record[title]" class="full_width large-text" value="<?php echo htmlentities ( ( ( array_key_exists ( 'record', $_POST ) && array_key_exists ( 'title', $_POST['record'] ) ) ? $_POST['record']['title'] : '' ), ENT_COMPAT, APP_CHARSET ); ?>" tabindex="1" /></p>
<?php } ?>
	<p><textarea id="record_body" name="record[body]" class="full_width" cols="60" rows="16" tabindex="2"><?php echo htmlentities ( ( ( array_key_exists ( 'record', $_POST ) && array_key_exists ( 'body', $_POST['record'] ) ) ? $_POST['record']['body'] : '' ), ENT_NOQUOTES, APP_CHARSET ); ?></textarea><input type="hidden" value="<?php echo htmlentities ( ( ( array_key_exists ( 'record', $_POST ) && array_key_exists ( 'preview_ID', $_POST['record'] ) ) ? $_POST['record']['preview_ID'] : '' ), ENT_NOQUOTES, APP_CHARSET ); ?>" name="record[preview_ID]" /></p>