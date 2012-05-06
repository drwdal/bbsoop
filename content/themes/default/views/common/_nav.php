<ul id="nav" class="c clear">
	<li id="nav-default"<?php if ( $GLOBALS['board_name'] == 'default' ) { echo ' class="current"'; } ?>><a href="<?php echo BASE_URI; ?>" title="View the “Default” board.">Topics</a></li>
	<li id="nav-popular"<?php if ( $controller == 'topics' && $action == 'popular' ) { echo ' class="current"'; } ?>><a href="<?php echo BASE_URI; ?>topics/popular/" title="View the popular topics.">Popular</a></li>
	<li id="nav-bulletins"<?php if ( $controller == 'bulletins' ) { echo ' class="current"'; } ?>><a href="<?php echo BASE_URI; ?>bulletins/" title="View the site bulletins.">Bulletins</a></li>
	<li id="nav-activity"<?php if ( $controller == 'activity' ) { echo ' class="current"'; } ?>><a href="<?php echo BASE_URI; ?>activity/">Activity</a></li>
	<!--li id="nav-faq"<?php if ( $GLOBALS['original_URI'] == 'FAQ' ) { echo ' class="current"'; } ?>><a href="<?php echo BASE_URI; ?>FAQ/" title="View the frequently asked questions.">FAQ</a></li-->
	<li id="nav-account"<?php if ( $controller == 'account' ) { echo ' class="current"'; } ?>><a href="<?php echo BASE_URI; ?>account/">Account</a></li>
<?php if ( isset ( $_SESSION['logged_in_user'] ) && $_SESSION['logged_in_user']['type'] >= ADMIN_TYPE ) { ?>
	<li id="nav-admin" class="float-right"><a href="<?php echo BASE_URI; ?>admin">Admin</a></li>
<?php } ?>
</ul>
