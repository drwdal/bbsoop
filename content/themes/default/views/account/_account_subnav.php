<?php if ( isset ( $_SESSION['logged_in_user'] ) && $_SESSION['logged_in_user']['ID'] > 0 ) { ?>
<ul id="subnav" class="c clear">
	<li<?php if ( $action == 'edit' ) { echo ' class="current"'; } ?>><a href="<?php echo BASE_URI; ?>account/edit/" title="Edit account information.">Edit account</a></li>
	<li><a href="<?php echo BASE_URI; ?>private_messages/" title="View private messages.">Private messages<?php if ( isset ( $_SESSION['logged_in_user']['unread_private_messages_count'] ) ) { echo ' (' . $_SESSION['logged_in_user']['unread_private_messages_count'] . ' new)'; } ?></a></li>
	<li<?php if ( $action == 'settings' ) { echo ' class="current"'; } ?>><a href="<?php echo BASE_URI; ?>account/settings/" title="Edit account settings.">Settings</a></li>
	<li class="float-right"><a href="<?php echo BASE_URI; ?>account/logout" title="View private messages." class="logout">Log out</a></li>
</ul>
<?php } ?>
