<p id="top-button" class="float-right buttonized"><a href="<?php echo BASE_URI ?>topics/new">New topic</a></p>
<?php if ( isset ( $GLOBALS['page'] ) && $GLOBALS['page'] > 1 ) { ?>
<p id="top-pagination" class="pagination">
<?php IMGBOARD_render_view ( 'topics/_paginate' ); ?>
</p>
<?php } ?>
<?php
if ( isset ( $GLOBALS['topics'] ) ) { ?>
	<p id="topics_heading">Title <span class="topic-meta"><span class="bumped-at counter">Last bump ▼</span> <span class="view-count counter">Views</span> <span class="reply-count counter">Replies</span> <span class="media-count counter">Media</span></span></p>
<?php
	$utc_now = time ( );
	$i=1;
	foreach ( $GLOBALS['topics'] as $topic ) {
?>
	<hr class="a" />
	<p class="clear topic <?php echo ( ( $i % 2 == 0 ) ? 'even' : 'odd' ); ?>">
		<a href="<?php echo $topic->URI ( ); ?>" title="Read topic: <?php echo htmlspecialchars ( $topic->title ); ?>" class="topic"><?php echo htmlspecialchars ( $topic->title ); ?></a>
		<span class="topic-meta">
			<span class="bumped-at counter" title="<?php echo $topic->bumped_at ( ); ?> UTC"><span class="a"> last bump: </span><?php echo seconds_to_time ( $utc_now - intval ( $topic->bumped_at ( 'U' ) ) ); ?><span class="a"> ago,</span></span>
			<span class="view-count counter"><span class="a">views: </span><?php echo ( ( $topic->views_count == 0 ) ? '–' : number_format ( $topic->views_count ) ); ?><span class="a">,</span></span>
			<span class="reply-count counter"><span class="a">replies: </span><span class="new-reply-count"><?php $my_count = $GLOBALS['topics_new_replies'][( string ) $topic->ID ( )]; if ( $my_count != 0 ) { echo '<a href="' . $topic->URI ( ) . '#new">(<span class="digit">' . number_format ( $my_count ) . '</span> new)</a> '; } ?></span><?php echo ( ( $topic->replies_count == 0 ) ? '–' : number_format ( $topic->replies_count ) ); ?><span class="a">,</span></span>
			<span class="media-count counter"><span class="a">media: </span><?php echo ( ( $topic->media_count == 0 ) ? '–' : number_format ( $topic->media_count ) ); ?></span>
			<?php if ( $topic->sticky == 1 ) { ?><span class="a"> (</span><span class="sticky">sticky</span><span class="a">)</span><?php } ?>
			<?php if ( $topic->safe_for_work == 0 ) { ?><span class="a"> (</span><span class="NSFW">NSFW</span><span class="a">)</span><?php } ?>
		</span>
	</p>
<?php $i++; } ?>
<p id="bottom-new-topic" class="float-right buttonized"><a href="<?php echo BASE_URI ?>topics/new">New topic</a></p>
<?php if ( isset ( $GLOBALS['page_count'] ) && $GLOBALS['page_count'] > 1 ) { ?>
<p class="pagination">
<?php IMGBOARD_render_view ( 'topics/_paginate' ); ?>
</p>
<?php } ?>
<?php } else { ?>
	<p>No topics.</p>
<?php } ?>
