<?php
global $DB;
$hour_group1 = date ( MYSQL_DATETIME_FORMAT, time ( ) - ( 60 * 60 * 2 ) );
$hour_group2 = date ( MYSQL_DATETIME_FORMAT, time ( ) - ( 60 * 60 * 12 ) );
$hour_group3 = date ( MYSQL_DATETIME_FORMAT, time ( ) - ( 60 * 60 * 36 ) );
?>
<table border="0" cellpadding="0" cellspacing="0" class="statistics">
	<tr>
		<th></th>
		<th class="align-right">2 hours</th>
		<th class="align-right">12 hours</th>
		<th class="align-right">36 hours</th>
		<th class="align-right">total</th>
		<th>notes</th>
	</tr>
	<tr class="first">
		<td>Topics…</td>
		<td colspan="5"></td>
	</tr>
	<tr>
		<th>Total</th>
		<td colspan="3"></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count FROM `topics`" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td class="align-left"></td>
	</tr>
	<tr>
		<th>Published:</th>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `topics` WHERE `created_at` > '$hour_group1' AND `status` = 'published'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `topics` WHERE `created_at` > '$hour_group2' AND `status` = 'published'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `topics` WHERE `created_at` > '$hour_group3' AND `status` = 'published'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `topics` WHERE `status` = 'published'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td class="align-left"></td>
	</tr>
	<tr>
		<th>Draft:</th>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `topics` WHERE `created_at` > '$hour_group1' AND `status` = 'draft'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `topics` WHERE `created_at` > '$hour_group2' AND `status` = 'draft'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `topics` WHERE `created_at` > '$hour_group3' AND `status` = 'draft'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `topics` WHERE `status` = 'draft'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td class="align-left">failed topics that were abadoned</td>
	</tr>
	<tr>
		<th>Deleted:</th>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `topics` WHERE `created_at` > '$hour_group1' AND `status` = 'deleted'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `topics` WHERE `created_at` > '$hour_group2' AND `status` = 'deleted'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `topics` WHERE `created_at` > '$hour_group3' AND `status` = 'deleted'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `topics` WHERE `status` = 'deleted'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td class="align-left"></td>
	</tr>
	<tr class="first">
		<td>Replies…</td>
		<td colspan="5"></td>
	</tr>
	<tr>
		<th>Total:</th>
		<td colspan="3"></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count FROM `replies`" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td class="align-left"></td>
	</tr>
	<tr>
		<th>Published:</th>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count FROM `replies` WHERE replies.created_at > '$hour_group1' AND replies.status = 'published'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, created_at FROM `replies` WHERE replies.created_at > '$hour_group2' AND replies.status = 'published'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, created_at FROM `replies` WHERE replies.created_at > '$hour_group3' AND replies.status = 'published'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, replies.created_at FROM `replies` WHERE replies.status = 'published'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td class="align-left"></td>
	</tr>
	<tr>
		<th>Hidden:</th>
		<td colspan="3"></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE replies.ID, replies.topic_ID, count(replies.ID) as count, replies.status, replies.created_at, topics.status FROM `replies` LEFT JOIN `topics` ON replies.topic_ID = topics.ID WHERE replies.status = 'published' AND topics.status = 'deleted'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td class="align-left">these are published, but their topics were deleted</td>
	</tr>
	<tr>
		<th>Draft:</th>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `replies` WHERE `created_at` > '$hour_group1' AND `status` = 'draft'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `replies` WHERE `created_at` > '$hour_group2' AND `status` = 'draft'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `replies` WHERE `created_at` > '$hour_group3' AND `status` = 'draft'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `replies` WHERE `status` = 'draft'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td class="align-left">failed replies that were abadoned</td>
	</tr>
	<tr>
		<th>Deleted:</th>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `replies` WHERE `status` = 'deleted' AND `created_at` > '$hour_group1'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `replies` WHERE `status` = 'deleted' AND `created_at` > '$hour_group2'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `replies` WHERE `status` = 'deleted' AND `created_at` > '$hour_group3'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `replies` WHERE `status` = 'deleted'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td class="align-left"></td>
	</tr>
	<tr class="first">
		<td>Media…</td>
		<td colspan="5"><span class="help">todo: make this more accurate</span></td>
	</tr>
	<tr>
		<th>Total:</th>
		<td colspan="3"></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE topics.media_ID, replies.media_ID, count( DISTINCT topics.ID ) AS count_topics, count( DISTINCT replies.ID ) AS count_replies FROM topics, replies WHERE topics.media_ID IS NOT NULL AND replies.media_ID IS NOT NULL" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $total_media = $row['count_topics'] + $row['count_replies']; $result->close(); ?></td>
		<td class="align-left"><?php if ( DUPLICATE_IMAGES == 1 ) { echo "using “Smart” duplicate detection"; } else if ( DUPLICATE_IMAGES == 2 ) { echo "rejecting duplicates"; }  ?></td>
	</tr>
	<tr>
		<th>Published:</th>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `media` WHERE `status` = 'published' AND `created_at` > '$hour_group1'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `media` WHERE `status` = 'published' AND `created_at` > '$hour_group2'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `media` WHERE `status` = 'published' AND `created_at` > '$hour_group3'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count FROM `media` WHERE `status` = 'published'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td class="align-left">some are “orphaned” by deleted replies and topics</td>
	</tr>
	<tr>
		<th>Rule violations:</th>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `media` WHERE `status` = 'rule violation' AND `created_at` > '$hour_group1'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `media` WHERE `status` = 'rule violation' AND `created_at` > '$hour_group2'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `media` WHERE `status` = 'rule violation' AND `created_at` > '$hour_group3'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count FROM `media` WHERE `status` = 'rule violation'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td class="align-left"></td>
	</tr>
<?php if ( ALLOW_ADULT_CONTENT != 1 ) { ?>
	<tr>
		<th>Adult content:</th>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `media` WHERE `status` = 'adult content' AND `created_at` > '$hour_group1'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `media` WHERE `status` = 'adult content' AND `created_at` > '$hour_group2'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `media` WHERE `status` = 'adult content' AND `created_at` > '$hour_group3'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count FROM `media` WHERE `status` = 'adult content'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td class="align-left"></td>
	</tr>
<?php } ?>
	<tr>
		<th>Illegal content:</th>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `media` WHERE `status` = 'illegal content' AND `created_at` > '$hour_group1'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `media` WHERE `status` = 'illegal content' AND `created_at` > '$hour_group2'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `media` WHERE `status` = 'illegal content' AND `created_at` > '$hour_group3'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count FROM `media` WHERE `status` = 'illegal content'" ); echo $DB->error; $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td class="align-left"></td>
	</tr>
	<tr>
		<th>File size:</th>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE SUM(file_size) as `total_file_size`, status, created_at FROM `media` WHERE `created_at` > '$hour_group1'" ); $row = $result->fetch_assoc ( ); echo humanize_bytes ( $row['total_file_size'] ); $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE SUM(file_size) as `total_file_size`, status, created_at FROM `media` WHERE `created_at` > '$hour_group2'" ); $row = $result->fetch_assoc ( ); echo humanize_bytes ( $row['total_file_size'] ); $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE SUM(file_size) as `total_file_size`, status, created_at FROM `media` WHERE `created_at` > '$hour_group3'" ); $row = $result->fetch_assoc ( ); echo humanize_bytes ( $row['total_file_size'] ); $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE SUM(file_size) as `total_file_size`, status, created_at FROM `media`" ); $row = $result->fetch_assoc ( ); echo humanize_bytes ( $row['total_file_size'] ); $result->close(); ?></td>
		<td class="align-left"></td>
	</tr>
	<tr class="first">
		<td>User accounts…</td>
		<td colspan="5"></td>
	</tr>
	<tr>
		<th>Total:</th>
		<td colspan="3"></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count FROM `user_accounts`" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td class="align-left"></td>
	</tr>
	<tr>
		<th>New:</th>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `user_accounts` WHERE `created_at` > '$hour_group1'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `user_accounts` WHERE `created_at` > '$hour_group2'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT SQL_CACHE count(ID) as count, status, created_at FROM `user_accounts` WHERE `created_at` > '$hour_group3'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td>&nbsp;</td>
		<td class="align-left"></td>
	</tr>
	<tr>
		<th>Banned:</th>
		<td><?php $result = $DB->query ( "SELECT count(ID) as count, ban_expires, updated_at FROM `user_accounts` WHERE `ban_expires` >= UTC_TIMESTAMP() AND `updated_at` > '$hour_group1'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT count(ID) as count, ban_expires, updated_at FROM `user_accounts` WHERE `ban_expires` >= UTC_TIMESTAMP() AND `updated_at` > '$hour_group2'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT count(ID) as count, ban_expires, updated_at FROM `user_accounts` WHERE `ban_expires` >= UTC_TIMESTAMP() AND `updated_at` > '$hour_group3'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT count(ID) as count, ban_expires, updated_at FROM `user_accounts` WHERE `ban_expires` >= UTC_TIMESTAMP()" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td class="align-left"></td>
	</tr>
	<tr class="first">
		<td>Remote addresses…</td>
		<td colspan="5"></td>
	</tr>
	<tr>
		<th>Total:</th>
		<td colspan="3"></td>
		<td><?php $result = $DB->query ( "SELECT count(ID) as count FROM `remote_addresses`" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td class="align-left"></td>
	</tr>
	<tr>
		<th>New:</th>
		<td><?php $result = $DB->query ( "SELECT count(ID) as count, first_seen FROM `remote_addresses` WHERE `first_seen` > '$hour_group1'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT count(ID) as count, first_seen FROM `remote_addresses` WHERE `first_seen` > '$hour_group2'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT count(ID) as count, first_seen FROM `remote_addresses` WHERE `first_seen` > '$hour_group3'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td>&nbsp;</td>
		<td class="align-left"></td>
	</tr>
	<tr>
		<th>Active:</th>
		<td><?php $result = $DB->query ( "SELECT count(ID) as count, last_seen FROM `remote_addresses` WHERE `last_seen` > '$hour_group1'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT count(ID) as count, last_seen FROM `remote_addresses` WHERE `last_seen` > '$hour_group2'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT count(ID) as count, last_seen FROM `remote_addresses` WHERE `last_seen` > '$hour_group3'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td><?php $result = $DB->query ( "SELECT count(ID) as count, last_seen FROM `remote_addresses` WHERE `last_seen` IS NOT NULL" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?></td>
		<td class="align-left">rejects addresses that have only loaded one page</td>
	</tr>
</table>