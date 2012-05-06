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
	<div class="clear wrapper bulletin <?php echo ( ( $i % 2 == 1 ) ? 'even' : 'odd' ) ?>">
		<p class="bulletin float-right">
			<span class="topic-meta">
				<span class="created-at counter" title="<?php echo $bulletin->created_at ( ); ?> UTC"><span class="a">, created at: </span><?php echo seconds_to_time ( $utc_now - intval ( $bulletin->created_at ( 'U' ) ) ); ?><span class="a"> ago,</span></span>
				<span class="poster-name meta"><?php if ( isset ( $_SESSION['logged_in_user'] ) && $_SESSION['logged_in_user']['type'] >= MODERATOR_TYPE ) { ?><a href="<?php echo BASE_URI . 'admin/user/' . $bulletin->user_ID; ?>"><?php } ?><span class="a">poster: </span><?php if ( isset ( $_SESSION['logged_in_user'] ) && $bulletin->user_ID == $_SESSION['logged_in_user']['ID'] ) { echo '(you) '; } echo $bulletin->user_ID; ?><?php if ( isset ( $_SESSION['logged_in_user'] ) && $_SESSION['logged_in_user']['type'] >= MODERATOR_TYPE ) { ?></a><?php } ?></span>
			</span>
		</p>
			<?php echo get_cached_body ( $bulletin ); ?>
		</div><?php /* cached_body leaves a wrapper open */ ?>
	</div>
<?php $i++; } ?>
<?php } else { ?>
	<p>No bulletins.</p>
<?php } ?>
<p id="bottom-new-bulletin" class="clear float-right buttonized"><a href="<?php echo BASE_URI ?>bulletins/new">New bulletin</a></p>
