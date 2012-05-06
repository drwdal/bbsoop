<?php IMGBOARD_render_view ( 'admin/_user_search' ); ?>
<p class="pagination">
	<span class="float-right"><?php echo number_format ( $GLOBALS['records_count']->count ); ?> user accounts</span>
<?php if ( isset ( $GLOBALS['page'] ) && $GLOBALS['page'] > 1 ) { ?>
	<a href="<?php echo BASE_URI; ?>admin/users/<?php echo $GLOBALS['page'] - 1; ?>" class="previous">« Previous page</a>
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
	 | <a href="<?php echo BASE_URI; ?>admin/users/<?php echo $i; ?>"<?php if ( $i == $GLOBALS['page'] ) { echo ' class="current"'; }?>><?php echo $i; ?></a>
<?php $i++; } ?> |
<?php if ( $GLOBALS['records_count']->count > $GLOBALS['user_accounts_per_page'] && $GLOBALS['page'] < $GLOBALS['page_count'] ) { ?>
	<a href="<?php echo BASE_URI; ?>admin/users/<?php echo $GLOBALS['page'] + 1; ?>" class="next">Next page »</a>
<?php } else { echo "Next page »"; } ?>
</p>
<?php 
if ( count ( $GLOBALS['user_accounts'] ) > 0 ) {
	foreach ( $GLOBALS['user_accounts'] as $user_account ) {
?>
<div class="user wrapper c" id="user_account_<?php echo $user_account->ID ( ); ?>">
	<h2 class="rule">
<?php if ( $user_account->type ( ) <= $_SESSION['logged_in_user']['type'] ) { ?>
		<a href="<?php echo BASE_URI . 'admin/user/' . $user_account->ID ( ); ?>" title="view more details for user <?php echo $user_account->ID ( ); ?>">
<?php } ?>
		<?php echo $user_account->ID ( ); ?><?php if ( $user_account->type ( ) > 0 ) { echo ': ' . $GLOBALS['user_type_strings'][( int ) $user_account->type ( )]; } ?>
<?php if ( $user_account->type ( ) <= $_SESSION['logged_in_user']['type'] ) { ?>
		</a>
<?php } ?>
	</h2>
<?php if ( ! empty ( $GLOBALS['user_account']->ban_expires ) && time ( ) < $GLOBALS['user_account']->ban_expires ( 'U' ) ) { ?>
	<p>Status: <span class="required">banned</span> until <?php echo $GLOBALS['user_account']->ban_expires ( ) . ' UTC'; ?> Reason: <?php echo ( ( ! empty ( $GLOBALS['user_account']->ban_reason ) ? htmlspecialchars ( $GLOBALS['user_account']->ban_reason ) : 'none' ) ); ?></p>
<?php } ?>
	<p class="user-meta small_text">
		<label class="xsmall">Created</label> <?php echo seconds_to_time ( time ( ) - $user_account->created_at ( 'U' ) ) . ' ago'; ?><br />
		<label class="xsmall">Replies</label> <?php echo number_format ( $user_account->replies_count ); ?><br />
		<label class="xsmall">Topics</label> <?php echo number_format ( $user_account->topics_count ); ?><br />
		<label class="xsmall">Media</label> <?php echo number_format ( $user_account->media_count ); ?><br />
		<label class="xsmall">Reports</label> <?php echo number_format ( $user_account->reports_count ); ?>
	</p>
	<div class="internal-notes wrapper c">
		<?php echo parse_post_body ( $user_account->internal_notes ); ?>
	</div>
</div>
<?php
	}
}
?>
<p class="pagination">
	<span class="float-right"><?php echo number_format ( $GLOBALS['records_count']->count ); ?> user accounts</span>
<?php if ( isset ( $GLOBALS['page'] ) && $GLOBALS['page'] > 1 ) { ?>
	<a href="<?php echo BASE_URI; ?>admin/users/<?php echo $GLOBALS['page'] - 1; ?>" class="previous">« Previous page</a>
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
	 | <a href="<?php echo BASE_URI; ?>admin/users/<?php echo $i; ?>"<?php if ( $i == $GLOBALS['page'] ) { echo ' class="current"'; }?>><?php echo $i; ?></a>
<?php $i++; } ?> |
<?php if ( $GLOBALS['records_count']->count > $GLOBALS['user_accounts_per_page'] && $GLOBALS['page'] < $GLOBALS['page_count'] ) { ?>
	<a href="<?php echo BASE_URI; ?>admin/users/<?php echo $GLOBALS['page'] + 1; ?>" class="next">Next page »</a>
<?php } else { echo "Next page »"; } ?>
</p>
