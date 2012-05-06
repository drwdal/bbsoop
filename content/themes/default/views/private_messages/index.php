<?php
if ( isset ( $GLOBALS['private_messages'] ) && count ( $GLOBALS['private_messages'] ) > 0 ) {
	foreach ( $GLOBALS['private_messages'] as $private_message ) {
?>
<p><a href="<?php echo BASE_URI . 'private_messages/view/' . $private_message->ID ( ); ?>">#<?php echo $private_message->ID ( ); ?></a> — <?php echo seconds_to_time ( time ( ) - intval ( $private_message->created_at ( 'U' ) ) ); ?> old<?php if ( $private_message->is_read == 0 ) { ?> (new)<?php } ?></p>
<?php
	}
} else {
?>
<p>No messages.</p>
<?php } ?>