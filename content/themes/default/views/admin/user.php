<form class="wrapper c" action="" method="post">
<?php if ( ! empty ( $GLOBALS['user_account']->ban_expires ) && time ( ) < $GLOBALS['user_account']->ban_expires ( 'U' ) ) { ?>
	<h3 class="rule">Banned</h3>
	<p class="less-margin"><label class="medium">expires in</label> <?php echo seconds_to_time ( $GLOBALS['user_account']->ban_expires ( 'U' ) - time ( ) ); ?></p>
	<p class="less-margin"><label class="medium">Reason</label> <?php echo ( ( ! empty ( $GLOBALS['user_account']->ban_reason ) ? htmlspecialchars ( $GLOBALS['user_account']->ban_reason ) : '(none)' ) ); ?></p>
<?php } ?>
	<h3 class="rule"><?php echo $GLOBALS['user_type_strings'][( int ) $GLOBALS['user_account']->type ( )]; ?> account</h3>
	<p class="float-right"> <input type="submit" value="demote" name="demote" /> <input type="submit" value="promote" name="promote" /> <input type="hidden" value="basic" name="mode" /></p>
	<p class="less-margin"><label class="medium">account age</label> <?php echo seconds_to_time ( time() - $GLOBALS['user_account']->created_at ( 'U' ) ); ?></p>
	<p class="less-margin"><label class="medium">IP address</label> <?php $remote_address = $GLOBALS['user_account']->remote_address ( ); echo ( ( ! empty ( $remote_address ) ) ? '<a href="' . $remote_address->URI ( ) . '">' . $remote_address->remote_address ( ) . '</a>' : '(unknown)' ); ?></p>
	<p class="less-margin"><label class="medium">replies count</label> <a href="<?php echo BASE_URI . 'admin/replies/1/user/' . $GLOBALS['user_account']->ID ( ); ?>"><?php echo number_format ( $GLOBALS['user_account']->replies_count ); ?></a></p>
	<p class="less-margin"><label class="medium">topics count</label> <a href="<?php echo BASE_URI . 'admin/topics/1/user/' . $GLOBALS['user_account']->ID ( ); ?>"><?php echo number_format ( $GLOBALS['user_account']->topics_count ); ?></a></p>
	<p class="less-margin"><label class="medium">media count</label> <a href="<?php echo BASE_URI . 'admin/media/1/user/' . $GLOBALS['user_account']->ID ( ); ?>"><?php echo number_format ( $GLOBALS['user_account']->media_count ); ?></a></p>
	<p class="less-margin"><label class="medium">reports count</label> <a href="<?php echo BASE_URI . 'admin/reports/1/user/' . $GLOBALS['user_account']->ID ( ); ?>"><?php echo number_format ( $GLOBALS['user_account']->reports_count ); ?></a></p>
	<p class="less-margin"><label class="medium">bulletins count</label> <a href="<?php echo BASE_URI . 'admin/bulletins/1/user/' . $GLOBALS['user_account']->ID ( ); ?>"><?php echo number_format ( $GLOBALS['user_account']->bulletins_count ); ?></a></p>
	<p class="less-margin"><label class="medium">notes</label> <textarea name="user_account[notes]" id="user_account_notes" class="xlarge" rows="5" cols="65"><?php echo htmlspecialchars ( $GLOBALS['user_account']->internal_notes ); ?></textarea></p>
	<p><label class="medium">&nbsp;</label>Notes are intended to be kept confidential amongst the administration and moderators. Users cannot see the contents of their notes.</p>
	<p><label class="medium">&nbsp;</label> <input type="submit" value="update notes" /><?php nonce_for_form ( ); ?></p>
</form>
<?php if ( $GLOBALS['user_account']->ID ( ) != $_SESSION['logged_in_user']['ID'] ) { ?>
<h2 class="rule"><a href="javascript:void(0);" onclick="jQuery('#message-form').slideToggle('fast');">Private Message…</a></h2>
<form action="<?php echo BASE_URI . 'private_messages/new'; ?>" method="post" class="wrapper c" id="message-form" style="display: none;">
<?php IMGBOARD_render_view ( 'private_messages/_new' ); ?>
</form>
<?php } ?>
<?php if ( $GLOBALS['user_account']->ID ( ) != $_SESSION['logged_in_user']['ID'] && $GLOBALS['user_account']->type < $_SESSION['logged_in_user']['type'] ) { ?>
<h2 class="rule"><a href="javascript:void(0);" onclick="jQuery('#ban-form').slideToggle('fast');">Ban…</a></h2>
<form action="" method="post" class="wrapper c" id="ban-form" style="display: none;">
	<h4 class="rule"><a href="javascript:void(0);" onclick="jQuery('#ban-presets').slideToggle('fast');jQuery('#ban-manually').slideToggle('fast');">Presets…</a></h4>
	<div id="ban-presets" class="wrapper c" style="display: none;">
		<p><input type="submit" value="Trolling" class="ban" name="trolling" onclick="mod_confirm_action('Please confirm the “trolling” ban.', 'ban-form'); return false;" /> <span class="help">bans the user ID for three days and notifies the user; warn first</span></p>
<?php if ( ALLOW_ADULT_CONTENT != 1 ) { ?>
		<p><input type="submit" value="Adult content" class="ban" name="adult content" onclick="mod_confirm_action('Please confirm the “adult content” ban.', 'ban-form'); return false;" /> <span class="help">bans the user ID for one week; warn first</span></p>
<?php } ?>
		<p><input type="submit" value="Illegal content" class="ban" name="illegal content" onclick="mod_confirm_action('Please confirm the “illegal content” ban.', 'ban-form'); return false;" /> <span class="help">deletes all content, bans the user ID forever, and bans the IP address for one week; does not require a warning</span></p>
	</div>
	<h4 class="rule"><a href="javascript:void(0);" onclick="jQuery('#ban-presets').slideToggle('fast');jQuery('#ban-manually').slideToggle('fast');">Manual ban…</a></h4>
	<div id="ban-manually" class="wrapper c">
		<p><label>Current time</label> <?php echo date ( MYSQL_DATETIME_FORMAT ); ?> UTC</p>
		<p><label for="user_account_ban_expires_date">Expire date</label> <input type="text" class="medium has_calendar" name="user_account[ban_expires][date]" id="user_account_ban_expires_date" value="<?php echo date ( 'Y-m-d', time ( ) + 604800 ); ?>" /></p>
		<p><label for="user_account_ban_expires_time">Expire time</label> <select name="user_account[ban_expires][time]" class="small time" id="user_account_ban_expires_time"><?php for ( $i = 0; $i < 24; $i++ ) { ?><option><?php echo $i; ?>:00</option><? } ?></select></p>
		<p><label for="user_account_ban_reason">Ban reason</label> <input type="text" class="xlarge" name="user_account[ban_reason]" maxlength="255" id="user_account_ban_reason" /> <span class="help">this is shown to the user; no HTML allowed</p>
		<p><label>&nbsp;</label> <input type="submit" class="ban-large" value="DO IT!" name="manual" /><input type="hidden" value="ban" name="mode" /></p>
	</div>
	<?php nonce_for_form ( ); ?>
</form>
<?php } ?>
