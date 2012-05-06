<input type="hidden" value="<?php echo time(); ?>" id="server_time" />
<input type="hidden" id="env_base_uri" value="<?php echo BASE_URI; ?>" />
<input type="hidden" id="session_ID" value="<?php echo SESSION_ID; ?>" />
<?php if ( $GLOBALS['controller'] == 'topics' && $GLOBALS['action'] == 'new' ) { ?>
<input type="hidden" id="topic_title_characters_maximum" value="<?php echo TOPIC_TITLE_CHARACTERS_MAXIMUM; ?>" />
<?php } ?>
