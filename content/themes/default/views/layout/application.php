<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo htmlspecialchars ( APP_CHARSET ); ?>" />
		<title><?php IMGBOARD_title ( ); ?></title>
		<meta name="description" content="<?php IMGBOARD_meta_description ( ); ?>" />
		<meta http-equiv="content-language" content="en" />
		<link rel="stylesheet" href="<?php echo BASE_ASSET_URI; ?>stylesheets/screen.css" type="text/css" media="screen" />
		<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Droid+Sans" />
<?php if ( isset ( $GLOBALS['mobile_mode'] ) && $GLOBALS['mobile_mode'] == 1 ) { ?>
		<link rel="stylesheet" href="<?php echo BASE_ASSET_URI; ?>stylesheets/mobile.css" type="text/css" media="screen" />
		<meta name="viewport" content="width=device-width">
		<meta name="HandheldFriendly" content="true" />
<?php } ?>
		<!--link rel="stylesheet" href="<?php echo BASE_ASSET_URI; ?>stylesheets/bbs.css" type="text/css" media="screen" /-->
<?php if ( array_key_exists ( 'needs_ui', $GLOBALS ) && $GLOBALS['needs_ui'] == 1 ) { ?>
		<link rel="stylesheet" href="<?php echo BASE_ASSET_URI; ?>stylesheets/jquery-ui-1.8.1.custom.css" type="text/css" media="screen" />
<?php } ?>
		<script type="text/javascript" src="http<?php echo ( ( array_key_exists ( 'HTTPS', $_SERVER ) ) ? 's' : '' ); ?>://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
<?php if ( array_key_exists ( 'needs_ui', $GLOBALS ) && $GLOBALS['needs_ui'] == 1 ) { ?>
		<script type="text/javascript" src="<?php echo BASE_ASSET_URI; ?>javascripts/jquery-ui-1.8.1.custom.min.js"></script>
<?php } ?>
		<script type="text/javascript" src="<?php echo BASE_ASSET_URI; ?>javascripts/application.js"></script>
<?php if ( isset ( $_SESSION['logged_in_user'] ) && $_SESSION['logged_in_user']['type'] >= MODERATOR_TYPE ) { ?>
		<script type="text/javascript" src="<?php echo BASE_ASSET_URI; ?>javascripts/moderation.js"></script>
<?php } ?>
		<link rel="shortcut icon" href="<?php echo BASE_URI; ?>favicon.png" />
	</head>
	<body id="<?php echo htmlspecialchars ( $action ); ?>" class="<?php echo htmlspecialchars ( $GLOBALS['board_name'] . '_board ' ); echo htmlspecialchars ( $controller ); if ( isset ( $_SESSION['logged_in_user'] ) ) { if ( $_SESSION['logged_in_user']['type'] >= MODERATOR_TYPE ) { echo ' moderator'; } echo ' logged-in'; } ?><?php if ( isset ( $GLOBALS['mobile_mode'] ) && $GLOBALS['mobile_mode'] == 1 ) { echo ' mobile'; } ?>">
		<a class="a" href="#content" title="skip navigation">Skip navigation</a>
<?php generate_notice ( ); generate_errors ( ); ?>
		<div id="page" class="c">
			<div id="header" class="c">
				<h2><a href="<?php echo BASE_URI; ?>" title="home"><?php echo site_title ( ); ?></a></h2>
				<p class="meta"><a href="<?php echo BASE_URI; ?>about/privacy/">Privacy</a> | <a href="<?php echo BASE_URI; ?>about/legal/">Legal</a></p>
<?php generate_nav ( ); ?>
<?php generate_subnav ( ); ?>
			</div>
			<div id="content" class="c">
<?php if ( isset ( $GLOBALS['page_title'] ) && ! empty ( $GLOBALS['page_title'] ) ) { ?>
				<h1><?php echo page_title ( ); ?></h1>
<?php } ?>
<?php IMGBOARD_view ( ); ?>
			</div>
			<div id="footer" class="c">
				<p class="float-right"><a href="<?php echo BASE_URI; ?>about/privacy/">Privacy</a> | <a href="<?php echo BASE_URI; ?>about/legal/">Legal</a></p>
				<p>All content is user-created and not necessarily endorsed by the operators of this communication service.</p>
<?php if ( DEBUG_LEVEL >= 1 ) { IMGBOARD_render_view ( 'common/_debug' ); } ?>
				<div id="env" style="display: none;">
<?php
if ( isset ( $_SESSION['logged_in_user'] ) && $_SESSION['logged_in_user']['type'] >= MODERATOR_TYPE ) {
	IMGBOARD_render_view ( 'topics/_mod_env' );
}
IMGBOARD_render_view ( 'common/_env' );
IMGBOARD_footer ( );
?>
				</div>
				<noscript>
					<p><span class="required">Note:</span> your browser’s JavaScript is disabled; some site features may not fully function.</p>
				</noscript>
			</div>
		</div>
	</body>
</html>
