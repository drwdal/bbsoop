<p id="top-button" class="float-right buttonized"><a href="<?php echo BASE_URI ?>bulletins/new">New bulletin</a></p>
<?php
if ( isset ( $GLOBALS['bulletins'] ) ) { ?>
	<p id="bulletins_heading">Message <span class="bulletin-meta"><span class="created-at counter">Age ▼</span> <span class="poster-name meta">Poster</span></span></p>
<?php
	$utc_now = time();
	$i=0;
	foreach ( $GLOBALS['bulletins'] as $bulletin ) {
?>
	<hr class="a" />
	<div class="clear wrapper bulletin <?php echo ( ( $i % 2 == 1 ) ? 'even' : 'odd' ) ?>" id="bulletin-<?php echo $bulletin->ID ( ); ?>">
		<p class="bulletin float-right">
			<span class="topic-meta">
				<span class="created-at counter" title="<?php echo $bulletin->created_at ( ); ?> UTC"><span class="a">, created at: </span><?php echo seconds_to_time ( $utc_now - intval ( $bulletin->created_at ( 'U' ) ) ); ?><span class="a"> ago,</span></span>
				<span class="poster-name meta"><?php if ( isset ( $_SESSION['logged_in_user'] ) && $_SESSION['logged_in_user']['type'] >= MODERATOR_TYPE ) { ?><a href="<?php echo BASE_URI . 'admin/user/' . $bulletin->user_ID; ?>"><?php } ?><span class="a">poster: </span><?php if ( isset ( $_SESSION['logged_in_user'] ) && $bulletin->user_ID == $_SESSION['logged_in_user']['ID'] ) { echo '(you) '; } echo $bulletin->user_ID; ?><?php if ( isset ( $_SESSION['logged_in_user'] ) && $_SESSION['logged_in_user']['type'] >= MODERATOR_TYPE ) { ?></a><?php } ?></span>
			</span>
		</p>
		<?php echo get_cached_body ( $bulletin, 1 ); ?>
	</div>
<?php $i++; } ?>
<?php } else { ?>
	<p>No bulletins.</p>
<?php } ?>
<p id="bottom-new-bulletin" class="clear float-right buttonized"><a href="<?php echo BASE_URI ?>bulletins/new">New bulletin</a></p>
<?php /* PAGINATE ========== */ ?>
<p class="pagination c">
<?php if ( isset ( $GLOBALS['page'] ) && $GLOBALS['page'] > 1 ) { ?>
	<a href="<?php echo BASE_URI; ?>admin/bulletins/<?php echo $GLOBALS['page'] - 1; ?>" class="previous">« Previous page</a>
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
	 | <a href="<?php echo BASE_URI; ?>admin/bulletins/<?php echo $i; ?>"<?php if ( $i == $GLOBALS['page'] ) { echo ' class="current"'; }?>><?php echo $i; ?></a>
<?php $i++; } ?> |
<?php if ( $GLOBALS['bulletins_count']->count > $GLOBALS['bulletins_per_page'] && $GLOBALS['page'] < $GLOBALS['page_count'] ) { ?>
	<a href="<?php echo BASE_URI; ?>admin/bulletins/<?php echo $GLOBALS['page'] + 1; ?>" class="next">Next page »</a>
<?php } else { echo "Next page »"; } ?>
</p>
