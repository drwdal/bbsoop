<?php
########## SETUP
global $user_accounts, $show_mod, $replies, $favorites, $topic_favorite, $new_reply_ID;
$user_accounts = array ( );
$show_mod = 0;
if ( isset ( $_SESSION['logged_in_user'] ) && $_SESSION['logged_in_user']['type'] >= MODERATOR_TYPE ) {
	$show_mod = 1;
}
$favorites_count = count ( $favorites );
########## MAIN TOPIC
?>
<div class="wrapper topic<?php if ( isset ( $_SESSION['logged_in_user'] ) && $GLOBALS['topic']->user_ID ( ) == $_SESSION['logged_in_user']['ID'] ) { echo ' you'; } ?> c" id="topic">
	<?php if ( 1 === $show_mod ) { IMGBOARD_render_view ( 'topics/_mod_action_topic' ); } ?>
	<?php generate_post ( $GLOBALS['topic'] ); ?>
		<div class="actions">
<?php if ( 1 != $GLOBALS['topic']->locked ) { ?>
			<p class="reply-to-topic">
<?php if ( $_SESSION['logged_in_user'] && ( ( $GLOBALS['topic']->user_ID ( ) == $_SESSION['logged_in_user']['ID'] && ( TIME_TO_EDIT_REPLIES > ( time() - ( int ) $GLOBALS['topic']->created_at ( 'U' ) ) || USER_LEVEL_EXEMPT_FROM_EDIT_TIMING_LIMITS <= $_SESSION['logged_in_user']['type'] ) ) ) ) { ?>
				<a href="<?php echo $GLOBALS['topic']->edit_URI ( ); ?>" title="Edit this topic">Edit</a> |
<?php } ?>
				<a href="<?php echo $GLOBALS['topic']->quote_URI ( ); ?>" title="Quote this reply while replying to it">Quote</a>
				<a href="<?php echo $GLOBALS['topic']->cite_URI ( ); ?>" title="Reply to this reply">Reply</a>
			</p>
<?php } ?>
<?php if ( isset ( $_SESSION['logged_in_user'] ) ) { ?>
			<p class="post-user-actions">
				[<span class="favorites-count" id="favorites-topic-<?php echo $GLOBALS['topic']->ID ( ); ?>-label"><?php if ( $GLOBALS['topic']->favorites_count > 0 ) { echo ' ' . $GLOBALS['topic']->favorites_count . ' favorite' . ( ( 1 == $GLOBALS['topic']->favorites_count ) ? '' : 's' ); } ?></span><a href="javascript:void(0);" onclick="favorite_topic(<?php echo $GLOBALS['topic']->ID ( ); ?>)" title="Mark this topic as a favorite" class="favorite" id="favorites-topic-<?php echo $GLOBALS['topic']->ID ( ); ?>"> <?php if ( isset ( $GLOBALS['topic_favorite'] ) && $GLOBALS['topic_favorite']->user_ID == $_SESSION['logged_in_user']['ID'] ) { echo '-'; } else { echo '+'; } ?> </a>]
				[<a href="javascript:void(0);" onclick="report_topic(<?php echo $GLOBALS['topic']->ID ( ); ?>)" title="Report this topic" class="report" id="report-topic-<?php echo $GLOBALS['topic']->ID ( ); ?>"> ! </a>]
			</p>
<?php } ?>
		</div>
		<?php if ( 1 === $show_mod ) { IMGBOARD_render_view ( 'topics/_mod_action' ); } ?>
	</div><!-- close of wrapper div -->
</div>
<?php
########## REPLIES
if ( isset ( $replies ) ) {
	global $reply, $prev_ID;
	$count = count ( $replies );
	$prev_ID = 0;
	foreach ( $replies as $reply ) {
########## EACH REPLY
?>
<hr class="a" />
<?php if ( isset ( $new_reply_ID ) && $new_reply_ID > 0 && $new_reply_ID == $reply->ID ( ) ) { ?>
<a id="new" class="a">&nbsp;</a>
<?php } ?>
<div id="reply-<?php echo $reply->ID ( ); ?>" class="wrapper reply<?php if ( isset ( $_SESSION['logged_in_user'] ) && $reply->user_ID ( ) == $_SESSION['logged_in_user']['ID'] ) { echo ' you'; } ?> c">
	<?php generate_post ( $reply ); ?>
		<?php if ( 1 === $show_mod ) { IMGBOARD_render_view ( 'topics/_mod_action' ); } ?>
		<div class="actions">
<?php if ( 1 != $GLOBALS['topic']->locked ) { ?>
			<p class="reply-to-reply">
<?php if ( $_SESSION['logged_in_user'] && ( ( $reply->user_ID ( ) == $_SESSION['logged_in_user']['ID'] && ( TIME_TO_EDIT_REPLIES > ( time() - ( int ) $reply->created_at ( 'U' ) ) || USER_LEVEL_EXEMPT_FROM_EDIT_TIMING_LIMITS <= $_SESSION['logged_in_user']['type'] ) ) ) ) { ?>
				<a href="<?php echo $reply->edit_URI ( ); ?>" title="Edit this reply">Edit</a> |
<?php } ?>
				<a href="<?php echo $reply->quote_URI ( ); ?>" title="Quote this reply while replying to it">Quote</a>
				<a href="<?php echo $reply->cite_URI ( ); ?>" title="Reply to this reply">Reply</a>
			</p>
<?php } ?>
<?php if ( isset ( $_SESSION['logged_in_user'] ) ) { ?>
			<p class="post-user-actions">
<?php if ( defined ( 'FAVORITES_ON' ) && FAVORITES_ON == 1 ) { ?>
				[<span class="favorites-count" id="favorites-reply-<?php echo $reply->ID ( ); ?>-label"><?php if ( $reply->favorites_count > 0 ) { echo ' ' . $reply->favorites_count . ' favorite' . ( ( 1 == $reply->favorites_count ) ? '' : 's' ); } ?></span><a href="javascript:void(0);" onclick="favorite_reply(<?php echo $reply->ID ( ); ?>)" title="Mark this reply as a favorite" class="favorite" id="favorites-reply-<?php echo $reply->ID ( ); ?>"> <?php if ( $favorites_count > 0 && in_array ( $reply->ID ( ), $favorites ) ) { echo '-'; } else { echo '+'; } ?> </a>]
<?php } ?>
<?php if ( defined ( 'REPORTING_ON' ) && REPORTING_ON == 1 ) { ?>
				[<a href="javascript:void(0);" onclick="report_reply(<?php echo $reply->ID ( ); ?>)" title="Report this reply" class="report" id="report-reply-<?php echo $reply->ID ( ); ?>"> ! </a>]
<?php } ?>
			</p>
<?php } ?>
		</div>
	</div><!-- close of wrapper div -->
</div>
<?php
	$prev_ID = $reply->ID ( );
	} /* foreach $replies */
} /* isset $replies */
?>
<?php if ( 0 == $GLOBALS['topic']->locked ) { ?>
<p id="new-reply" class="buttonized float-left clear"><a href="<?php echo $GLOBALS['topic']->reply_URI ( ); ?>">New reply</a></p>
<?php } else { ?>
<p class="buttonized float-left clear"><a href="javascript:void(0);">Topic locked</a></p>
<?php if ( $_SESSION['logged_in_user']['type'] >= MODERATOR_TYPE ) { ?>
<p id="new-reply" class="buttonized float-left clear"><a href="<?php echo $GLOBALS['topic']->reply_URI ( ); ?>">New reply  (moderators or greater)</a></p>
<?php } ?>
<?php } ?>