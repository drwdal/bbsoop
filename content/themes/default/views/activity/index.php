<h2><?php echo number_format ( $GLOBALS['online_count']->count ); ?> user<?php if ( $GLOBALS['online_count']->count != 1 ) { echo 's'; } ?> online <span class="help">in the past 15 minutes</span></h2>
<?php

if ( isset ( $GLOBALS['actions'] ) ) {
	// $action is a reserved var
	$utc_now = time ( );
	foreach ( $GLOBALS['actions'] as $act ) {
?>
	<p class="c action"><?php if ( isset ( $_SESSION['logged_in_user'] ) && $act->user_ID == $_SESSION['logged_in_user']['ID'] ) { echo 'You'; } else if ( isset ( $_SESSION['logged_in_user'] ) && $_SESSION['logged_in_user']['type'] >= MODERATOR_TYPE ) { echo '<a href="' . BASE_URI . 'admin/user/' . $act->user_ID . '">user ' . $act->user_ID . '</a>'; } else { echo '?'; } ?> <?php echo $act->description; ?> <span class="age"><?php echo seconds_to_time ( $utc_now - intval ( $act->created_at ( 'U' ) ) ); ?></span></p>
<?php
	}
}
?>
