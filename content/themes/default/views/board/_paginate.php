<?php if ( isset ( $GLOBALS['page'] ) && $GLOBALS['page'] > 1 ) { ?>
	<a href="<?php echo $GLOBALS['current_board']->URI ( ) . ( $GLOBALS['page'] - 1 ); ?>" class="previous">« Previous page</a>
<?php } else { echo "« Previous page"; } ?>
<?php
if ( $GLOBALS['page'] < 6 ) {
	$i = 1;
} else {
	$i = $GLOBALS['page'] - 5;
}
$max = $GLOBALS['page'] + 5;
if ( $max > $GLOBALS['page_count'] ) {
	$max = $GLOBALS['page_count'];
}
while ( $i < $max ) {
?>
	 <span class="spacer">|</span> <a href="<?php echo $GLOBALS['current_board']->URI ( ) . $i; ?>"<?php if ( $i == $GLOBALS['page'] ) { echo ' class="current"'; }?>><?php echo $i; ?></a>
<?php $i++; } ?> <span class="spacer">|</span>
<?php if ( $GLOBALS['page'] == 1 || ( $GLOBALS['topics_count']->count > $GLOBALS['topics_per_page'] && $GLOBALS['page'] < $GLOBALS['page_count'] ) ) { ?>
	<a href="<?php echo $GLOBALS['current_board']->URI ( ) . ( $GLOBALS['page'] + 1 ); ?>" class="next">Next page »</a>
<?php } else { echo "Next page »"; } ?>
