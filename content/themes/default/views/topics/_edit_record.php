<?php
if ( $_SESSION['logged_in_user']['type'] < MODERATOR_TYPE ) {
	if ( get_class ( $GLOBALS['record'] ) == 'Topic' ) {
		$time_to_edit = TIME_TO_EDIT_TOPICS;
	} else if ( get_class ( $GLOBALS['record'] ) == 'Reply' ) {
		$time_to_edit = TIME_TO_EDIT_REPLIES;
	}
?>
	<p>You have <span id="seconds_left_to_edit" title="<?php echo ( $GLOBALS['record']->created_at ( 'U' ) + $time_to_edit ); ?>"><?php echo ( ( $GLOBALS['record']->created_at ( 'U' ) + $time_to_edit ) - time ( ) ); ?></span> seconds to finish editing.</p>
<?php } ?>
<?php if ( $GLOBALS['action'] == 'edit' ) { ?>
	<p><label for="record_title" class="block">Title <span class="float-right help" id="title-character-count"><?php echo TOPIC_TITLE_CHARACTERS_MAXIMUM; ?> characters allowed</span></label> <input type="text" maxlength="<?php echo TOPIC_TITLE_CHARACTERS_MAXIMUM; ?>" id="record_title" name="record[title]" class="full_width large-text" value="<?php echo htmlentities ( $GLOBALS['record']->title, ENT_COMPAT, APP_CHARSET ); ?>" tabindex="1" /></p>
<?php } ?>
	<p><textarea id="record_body" name="record[body]" class="full_width" cols="60" rows="16" tabindex="2"><?php echo htmlentities ( ( ( isset ( $_POST['record']['body'] ) && ! empty ( $_POST['record']['body'] ) ) ? ( $_POST['record']['body'] ) : ( $GLOBALS['record']->body ) ), ENT_NOQUOTES, APP_CHARSET ); ?></textarea></p>
