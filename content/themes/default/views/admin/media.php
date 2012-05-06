<?php
global $DB;
?>
<p class="float-right">Displaying images <?php echo ( ( ( $GLOBALS['page'] - 1 ) * $GLOBALS['media_per_page'] ) + 1 ); ?>–<?php echo ( ( ( $GLOBALS['page'] * $GLOBALS['media_per_page'] ) > $GLOBALS['media_count']->count ) ? $GLOBALS['media_count']->count : $GLOBALS['page'] * $GLOBALS['media_per_page'] ); ?> of <?php echo $GLOBALS['media_count']->count; ?></p>
<?php /* SEARCH ========== */ ?>
<h3><a href="javascript:void(0);" onclick="jQuery('#media-search').slideToggle('fast');">Search…</a></h3>
<div id="media-search" style="display: none;">
	<form action="" method="post" class="wrapper">
		<p>ID</p>
		<p>File name</p>
		<p>Status</p>
		<p>Upload to find</p>
	</form>
</div>
<?php /* PAGINATE ========== */ ?>
<p class="pagination c">
<?php if ( isset ( $GLOBALS['page'] ) && $GLOBALS['page'] > 1 ) { ?>
	<a href="<?php echo BASE_URI; ?>admin/media_moderation/<?php echo $GLOBALS['page'] - 1; ?>" class="previous">« Previous page</a>
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
	 | <a href="<?php echo BASE_URI; ?>admin/media_moderation/<?php echo $i; ?>"<?php if ( $i == $GLOBALS['page'] ) { echo ' class="current"'; }?>><?php echo $i; ?></a>
<?php $i++; } ?> |
<?php if ( $GLOBALS['media_count']->count > $GLOBALS['media_per_page'] && $GLOBALS['page'] < $GLOBALS['page_count'] ) { ?>
	<a href="<?php echo BASE_URI; ?>admin/media_moderation/<?php echo $GLOBALS['page'] + 1; ?>" class="next">Next page »</a>
<?php } else { echo "Next page »"; } ?>
<?php if ( ALLOW_ADULT_CONTENT != 1 ) { ?>
	<span class="float-right">Adult content is <strong>not allowed</strong></span>
<?php } ?>
</p>
<?php /* MEDIA ========== */ ?>
<?php
if ( isset ( $GLOBALS['media'] ) && count ( $GLOBALS['media'] ) > 0 ) {
	foreach ( $GLOBALS['media'] as $media ) {
?>
<div class="image_group wrapper c">
<?php if ( $media->status != 'published' ) { ?>
	<p class="float-right"><span class="highlight"><?php echo $media->status; ?></span></p>
<?php } ?>
	<p id="media-<?php echo $media->ID ( ); ?>" class="image"><a href="<?php echo htmlspecialchars ( $media->URL ( ) ); ?>" title="<?php echo htmlspecialchars ( $media->original_file_name ( ) ); ?>"><img src="<?php echo htmlspecialchars ( $media->thumbnail_URI ( ) ); ?>" height="<?php echo $media->t_height; ?>" width="<?php echo $media->t_width; ?>" alt="<?php echo htmlspecialchars ( $media->original_file_name ( ) ); ?>" /></a><span class="media-meta"><?php echo $media->width . 'w × ' . $media->height . 'h, ' . humanize_bytes ( $media->file_size ); ?></span></p>
	<div class="media_info wrapper c">
		<p><a href="<?php echo htmlspecialchars ( $media->URL ( ) ); ?>" title="<?php echo htmlspecialchars ( $media->original_file_name ( ) ); ?>"><?php echo htmlspecialchars ( $media->original_file_name ( ) ); ?></a></p>
		<p>From <a href="<?php echo BASE_URI; ?>admin/user/<?php echo $media->user_ID ( ); ?>">user <?php echo $media->user_ID ( ); ?><!-- put user type here? --></a></p>
		<p>
			Topics: <?php $result = $DB->query ( "SELECT count(ID) as count, status, media_ID FROM `topics` WHERE `status` = 'published' AND `media_ID` = '" . $media->ID ( ) . "'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?> <span class="help">TODO: link to view these</span><br />
			Replies: <?php $result = $DB->query ( "SELECT count(ID) as count, status, media_ID FROM `replies` WHERE `status` = 'published' AND `media_ID` = '" . $media->ID ( ) . "'" ); $row = $result->fetch_assoc ( ); echo $row['count']; $result->close(); ?> <span class="help">TODO: link to view these</span>
		</p>
	</div>
<?php /* MODERATE ========== */ ?>
	<div class="moderation">
		<p>
			<a href="javascript:void(0);" onclick="mod_action('media','<?php echo $media->ID ( ); ?>','rule violation');">Rule violation</a><br /><br />
<?php if ( ALLOW_ADULT_CONTENT != 1 ) { ?>
			<a href="javascript:void(0);" onclick="mod_action('media','<?php echo $media->ID ( ); ?>','adult content');">Adult content</a><br /><br />
<?php } ?>
			<a href="javascript:void(0);" onclick="mod_action('media','<?php echo $media->ID ( ); ?>','illegal content');">Illegal content</a>
		</p>
	</div>
</div>
<?php
	} /* END FOREACH */
} else {
?>
	<p>No media.</p>
<?php } ?>
<?php /* PAGINATE ========== */ ?>
<p class="pagination c">
<?php if ( isset ( $GLOBALS['page'] ) && $GLOBALS['page'] > 1 ) { ?>
	<a href="<?php echo BASE_URI; ?>admin/media_moderation/<?php echo $GLOBALS['page'] - 1; ?>" class="previous">« Previous page</a>
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
	 | <a href="<?php echo BASE_URI; ?>admin/media_moderation/<?php echo $i; ?>"<?php if ( $i == $GLOBALS['page'] ) { echo ' class="current"'; }?>><?php echo $i; ?></a>
<?php $i++; } ?> |
<?php if ( $GLOBALS['page'] == 1 || ( $GLOBALS['media_count']->count > $GLOBALS['media_per_page'] && $GLOBALS['page'] < $GLOBALS['page_count'] ) ) { ?>
	<a href="<?php echo BASE_URI; ?>admin/media/<?php echo $GLOBALS['page'] + 1; ?>" class="next">Next page »</a>
<?php } else { echo "Next page »"; } ?>
<?php if ( ALLOW_ADULT_CONTENT != 1 ) { ?>
	<span class="float-right">Adult content is <strong>not allowed</strong></span>
<?php } ?>
</p>
