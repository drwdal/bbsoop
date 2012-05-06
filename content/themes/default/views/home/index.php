<div class="two-cols col-group c">
	<div class="column wrapper c">
		<h1>Welcome to BBSOOP</h1>
		<?php echo get_cached_body ( $GLOBALS['page'] ); ?>
		<p><a href="http://anontalk.com" title="AnonTalk.com">AnonTalk.com is a bulletin board that supports legal, free speech</a></p>
	</div>
	<div class="column padded tan last c">
		<h2>Boards</h2>
		<div id="boards_list">
<?php
$i=1;
foreach ( $GLOBALS['boards'] as $board ) {
?>
			<div class="board wrapper<?php echo ( ( $i % 2 == 0 ) ? ' last' : '' ) ?> c" id="board-<?php echo $board->ID ( ); ?>">
				<h3 class="rule"><a href="<?php echo $board->URI ( ); ?>"><?php echo htmlspecialchars ( $board->name ); ?></a></h3>
				<p><?php echo htmlspecialchars ( $board->description ); ?></p>
				<ul>
					<li>Topics: <?php echo number_format ( $board->topics_count ); ?></li>
					<li>Replies: <?php echo number_format ( $board->replies_count ); ?></li>
					<li>Media: <?php echo number_format ( $board->media_count ); ?></li>
				</ul>
			</div>
<?php
	$i++;
}
?>
		</div>
	</div>
</div>
