<?php
global $remote_address;
?>
<div class="remote_address wrapper c" id="remote_address-<?php echo $remote_address->ID ( ); ?>">
<p class="float-right"><a href="http://www.maxmind.com/app/locate_demo_ip?ips=<?php echo $remote_address->remote_address ( ); ?>">GeoIP</a> | <a href="http://whois.domaintools.com/<?php echo $remote_address->remote_address ( ); ?>">WHOIS</a></p>
	<h2 class="rule">
		<a href="<?php echo BASE_URI . 'admin/remote_address/' . $remote_address->ID ( ); ?>" title="view more details for remote address <?php echo $remote_address->remote_address ( ); ?>"><?php echo $remote_address->remote_address ( ); ?></a>
<?php if ( $remote_address->permission_to_view == 0 ) { ?>
		<span class="banned" id="remote_address-<?php echo $remote_address->ID ( ); ?>-banned">Banned</span>
<?php } ?>
	</h2>
	<div class="moderation wrapper c">
		<p><a href="javascript:void(0);" onclick="mod_remote_address(<?php echo $remote_address->ID ( ); ?>, 'view');"><span id="remote_address-<?php echo $remote_address->ID ( ); ?>-view"><?php echo ( ( $remote_address->permission_to_view == 1 ) ? 'Ban' : 'Un-ban' ); ?></span> viewing</a></a></p>
		<hr />
		<p>
			<a href="javascript:void(0);" onclick="mod_remote_address(<?php echo $remote_address->ID ( ); ?>, 'register');"><span id="remote_address-<?php echo $remote_address->ID ( ); ?>-register"><?php echo ( ( $remote_address->permission_to_register == 1 ) ? 'Ban' : 'Un-ban' ); ?></span> registration</a><br />
			<a href="javascript:void(0);" onclick="mod_remote_address(<?php echo $remote_address->ID ( ); ?>, 'post');"><span id="remote_address-<?php echo $remote_address->ID ( ); ?>-post"><?php echo ( ( $remote_address->permission_to_post == 1 ) ? 'Ban' : 'Un-ban' ); ?></span> posting</a><br />
			<a href="javascript:void(0);" onclick="mod_remote_address(<?php echo $remote_address->ID ( ); ?>, 'search');"><span id="remote_address-<?php echo $remote_address->ID ( ); ?>-search"><?php echo ( ( $remote_address->permission_to_search == 1 ) ? 'Ban' : 'Un-ban' ); ?></span> searching</a></a><br />
		</p>
	</div>
	<p class="remote_address-meta">
		<label class="medium">First seen</label> <?php echo seconds_to_time ( time ( ) - $remote_address->first_seen ( 'U' ) ); ?> ago<br />
		<label class="medium">Host name</label> <?php echo $remote_address->host_name; ?><br />
		<label class="medium">Users</label> <?php echo number_format ( $remote_address->users_count ); ?><br />
		<label class="medium">Replies</label> <?php echo number_format ( $remote_address->replies_count ); ?><br />
		<label class="medium">Topics</label> <?php echo number_format ( $remote_address->topics_count ); ?><br />
		<label class="medium">Media</label> <?php echo number_format ( $remote_address->media_count ); ?><br />
		<label class="medium">Searches</label> <?php echo number_format ( $remote_address->search_count ); ?>
	</p>
	<div class="internal-notes wrapper c">
		<?php echo parse_post_body ( $remote_address->internal_notes ); ?>
	</div>
</div>
