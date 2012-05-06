<?php
if ( isset ( $GLOBALS['settings'] ) && count ( $GLOBALS['settings'] ) > 0 ) {
	foreach ( $GLOBALS['settings'] as $setting ) {
?>
	<p><?php echo htmlspecialchars ( strtolower ( str_replace ( '_', ' ',  $setting->name ) ) ) ?></p>
<?php
	}
}
?>