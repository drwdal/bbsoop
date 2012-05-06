<?php
if ( isset ( $GLOBALS['topics'] ) ) { ?>
	<p id="topics_heading">Title <span class="topic-meta"><span class="bumped-at counter">Last bump ▼</span> <span class="view-count counter">Views</span> <span class="reply-count counter">Replies</span> <span class="media-count counter">Media</span></span></p>
<?php
	$utc_now = time();
	$i=0;
	foreach ( $GLOBALS['topics'] as $topic ) {
?>
	<hr class="a" />
	<p class="clear topic <?php echo ( ( $i % 2 == 1 ) ? 'even' : 'odd' ) ?>">
		<span class="count"><?php echo $i + 1; ?>.</span> <a href="<?php echo $topic->URI ( ); ?>" title="Read topic: <?php echo htmlspecialchars ( $topic->title ); ?>" class="topic"><?php echo htmlspecialchars ( $topic->title ); ?></a>
		<span class="topic-meta">
			<span class="bumped-at counter" title="<?php echo $topic->bumped_at ( ); ?> UTC"><span class="a">, last bump: </span><?php echo seconds_to_time ( $utc_now - intval ( $topic->bumped_at ( 'U' ) ) ); ?><span class="a"> ago,</span></span>
			<span class="view-count counter"><span class="a">views: </span><?php echo ( ( $topic->views_count == 0 ) ? '-' : $topic->views_count ); ?><span class="a">,</span></span>
			<span class="reply-count counter"><span class="a">replies: </span><span class="new-reply-count"><?php $my_count = $GLOBALS['topics_new_replies'][( string ) $topic->ID ( )]; if ( $my_count != 0 ) { echo "($my_count new) "; } ?></span><?php echo ( ( $topic->replies_count == 0 ) ? '-' : $topic->replies_count ); ?><span class="a">,</span></span>
			<span class="media-count counter"><span class="a">media: </span><?php echo ( ( $topic->media_count == 0 ) ? '-' : $topic->media_count ); ?></span>
		</span>
	</p>
<?php $i++; } ?>
<?php } else { ?>
	<p>No topics.</p>
<?php } ?>
