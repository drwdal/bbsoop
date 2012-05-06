<p class="pagination">
<span class="float-right"><?php echo number_format ( $GLOBALS['records_count']->count ); ?> replies</span>
<?php if ( isset ( $GLOBALS['page'] ) && $GLOBALS['page'] > 1 ) { ?>
	<a href="<?php echo BASE_URI; ?>admin/replies/<?php echo $GLOBALS['page'] - 1; ?>/<?php if ( ! empty ( $GLOBALS['fragment'] ) ) { echo htmlspecialchars ( $GLOBALS['fragment'] ) . '/'; if ( ! empty ( $GLOBALS['extra'] ) ) { echo htmlspecialchars ( $GLOBALS['extra'] ) . '/'; } } ?>" class="previous">« Previous page</a>
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
	 | <a href="<?php echo BASE_URI; ?>admin/replies/<?php echo $i; ?>/<?php if ( ! empty ( $GLOBALS['fragment'] ) ) { echo htmlspecialchars ( $GLOBALS['fragment'] ) . '/'; if ( ! empty ( $GLOBALS['extra'] ) ) { echo htmlspecialchars ( $GLOBALS['extra'] ) . '/'; } } ?>"<?php if ( $i == $GLOBALS['page'] ) { echo ' class="current"'; }?>><?php echo $i; ?></a>
<?php $i++; } ?> |
<?php if ( $GLOBALS['records_count']->count > $GLOBALS['records_per_page'] && $GLOBALS['page'] < $GLOBALS['page_count'] ) { ?>
	<a href="<?php echo BASE_URI; ?>admin/replies/<?php echo $GLOBALS['page'] + 1; ?>/<?php if ( ! empty ( $GLOBALS['fragment'] ) ) { echo htmlspecialchars ( $GLOBALS['fragment'] ) . '/'; if ( ! empty ( $GLOBALS['extra'] ) ) { echo htmlspecialchars ( $GLOBALS['extra'] ) . '/'; } } ?>" class="next">Next page »</a>
<?php } else { echo "Next page »"; } ?>
</p>
<?php
if ( isset ( $GLOBALS['replies'] ) ) {
	global $reply, $prev_ID;
	$count = count ( $GLOBALS['replies'] );
	$prev_ID = 0;
	foreach ( $GLOBALS['replies'] as $reply ) {
?>
<hr class="a" />
<div id="reply-<?php echo $reply->ID ( ); ?>" class="wrapper reply<?php if ( isset ( $_SESSION['logged_in_user'] ) && $reply->user_ID ( ) == $_SESSION['logged_in_user']['ID'] ) { echo ' you'; } ?> <?php echo htmlspecialchars ( $reply->status ); ?> c">
	<?php generate_post ( $reply ); ?>
		<?php IMGBOARD_render_view ( 'topics/_mod_action' ); ?>
		<div class="actions">
			<p class="reply-to-reply">
				<a href="<?php echo $reply->URI ( ); ?>" title="<?php echo htmlspecialchars ( $reply->topic ( )->title ); ?>">View in context</a>
			</p>
			<p class="post-user-actions">
<?php if ( defined ( 'FAVORITES_ON' ) && FAVORITES_ON == 1 ) { ?>
				[<span class="favorites-count" id="favorites-reply-<?php echo $reply->ID ( ); ?>-label"><?php if ( $reply->favorites_count > 0 ) { echo ' ' . $reply->favorites_count . ' favorite' . ( ( 1 == $reply->favorites_count ) ? '' : 's' ); } ?></span><a href="javascript:void(0);" onclick="favorite_reply(<?php echo $reply->ID ( ); ?>)" title="Mark this reply as a favorite" class="favorite" id="favorites-reply-<?php echo $reply->ID ( ); ?>"> <?php if ( $favorites_count > 0 && in_array ( $reply->ID ( ), $favorites ) ) { echo '-'; } else { echo '+'; } ?> </a>]
<?php } ?>
<?php if ( defined ( 'REPORTING_ON' ) && REPORTING_ON == 1 ) { ?>
				[<a href="javascript:void(0);" onclick="report_reply(<?php echo $reply->ID ( ); ?>)" title="Report this reply" class="report" id="report-reply-<?php echo $reply->ID ( ); ?>"> ! </a>]
<?php } ?>
			</p>
		</div>
	</div>
</div>
<?php
	$prev_ID = $reply->ID ( );
	} /* foreach $replies */
} /* isset $replies */
?>
<p class="pagination">
<span class="float-right"><?php echo number_format ( $GLOBALS['records_count']->count ); ?> replies</span>
<?php if ( isset ( $GLOBALS['page'] ) && $GLOBALS['page'] > 1 ) { ?>
	<a href="<?php echo BASE_URI; ?>admin/replies/<?php echo $GLOBALS['page'] - 1; ?>/<?php if ( ! empty ( $GLOBALS['fragment'] ) ) { echo htmlspecialchars ( $GLOBALS['fragment'] ) . '/'; if ( ! empty ( $GLOBALS['extra'] ) ) { echo htmlspecialchars ( $GLOBALS['extra'] ) . '/'; } } ?>" class="previous">« Previous page</a>
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
	 | <a href="<?php echo BASE_URI; ?>admin/replies/<?php echo $i; ?>/<?php if ( ! empty ( $GLOBALS['fragment'] ) ) { echo htmlspecialchars ( $GLOBALS['fragment'] ) . '/'; if ( ! empty ( $GLOBALS['extra'] ) ) { echo htmlspecialchars ( $GLOBALS['extra'] ) . '/'; } } ?>"<?php if ( $i == $GLOBALS['page'] ) { echo ' class="current"'; }?>><?php echo $i; ?></a>
<?php $i++; } ?> |
<?php if ( $GLOBALS['records_count']->count > $GLOBALS['records_per_page'] && $GLOBALS['page'] < $GLOBALS['page_count'] ) { ?>
	<a href="<?php echo BASE_URI; ?>admin/replies/<?php echo $GLOBALS['page'] + 1; ?>/<?php if ( ! empty ( $GLOBALS['fragment'] ) ) { echo htmlspecialchars ( $GLOBALS['fragment'] ) . '/'; if ( ! empty ( $GLOBALS['extra'] ) ) { echo htmlspecialchars ( $GLOBALS['extra'] ) . '/'; } } ?>" class="next">Next page »</a>
<?php } else { echo "Next page »"; } ?>
</p>
