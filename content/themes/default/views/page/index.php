<?php 
if ( count ( $GLOBALS['pages'] ) > 0 ) {
	foreach ( $GLOBALS['pages'] as $page ) {
?>
<h2><a href="<?php echo $page->URI ( ); ?>"><?php echo htmlspecialchars ( $page->title ); ?></a></h2>
<?php
	}
} else {
?>
<p>No pages.</p>
<?php } ?>