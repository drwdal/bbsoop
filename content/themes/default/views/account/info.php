<h2><?php echo htmlspecialchars ( $_SESSION['logged_in_user']['username'] ); ?></h2>
<p>
	Account type: <?php echo $GLOBALS['user_type_strings'][( int ) $_SESSION['logged_in_user']['type']]; ?><br />
	Joined: <?php echo $GLOBALS['user_account']->created_at ( DATE_FORMAT ); ?> (<?php echo seconds_to_time ( time() - $GLOBALS['user_account']->created_at ( 'U' ) ); ?> ago)<br />
</p>
<h3 class="rule">Replies</h3>
<p>
	Published: <?php echo number_format ( $GLOBALS['user_account']->replies_count ); ?><br />
	Favorited by others: <?php echo number_format ( $GLOBALS['user_account']->replies_favorited_count ); ?><br />
	Favorited by you: <?php echo number_format ( $GLOBALS['user_account']->replies_favorites_count ); ?>
</p>
<h3 class="rule">Topics</h3>
<p>
	Published: <?php echo number_format ( $GLOBALS['user_account']->topics_count ); ?><br />
	Favorited by others: <?php echo number_format ( $GLOBALS['user_account']->topics_favorited_count ); ?><br />
	Favorited by you: <?php echo number_format ( $GLOBALS['user_account']->topics_favorites_count ); ?>
</p>
<h3 class="rule">Media</h3>
<p>
	Uploaded: <?php echo number_format ( $GLOBALS['user_account']->media_count ); ?><br />
</p>
<h3 class="rule">Bulletins</h3>
<p>
	Submitted: <?php echo number_format ( $GLOBALS['user_account']->bulletins_count ); ?><br />
</p>
