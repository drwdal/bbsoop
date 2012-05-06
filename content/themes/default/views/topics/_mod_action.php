<?php
if ( isset ( $GLOBALS['reply'] ) ) { 
	$record = $GLOBALS['reply'];
} else {
	$record = $GLOBALS['topic'];
}
$record_class = get_class ( $record );
$record_ID = $record->ID ( );
?>
<div class="moderation" id="moderation-<?php echo $record_ID ?>">
	<p>
		<a href="javascript:void(0);" onclick="mod_action(<?php echo "'" . $record_class . "'," . $record_ID . ",'delete');" ?>" title="Immediately delete this <?php echo get_class ( $record ); ?>">Delete <?php echo get_class ( $record ); ?></a><br /><br />
		<a href="<?php echo $record->edit_URI ( ); ?>" title="Edit this <?php echo get_class ( $record ); ?>">Mod Edit</a>
<?php if ( $record_class == 'Topic' ) { ?>
		<br /><br /><a href="javascript:void(0);" onclick="jQuery('#mod-action-topic').slideToggle('fast');">Topic options…</a>
<?php } ?>
	</p>
<?php if ( isset ( $record->media_ID ) ) { ?>
	<div class="moderation_media" id="moderation-media-<?php echo $record_ID; ?>">
		<h4>Image:</h4>
		<p>
			<a href="javascript:void(0);" onclick="mod_action('media','<?php echo $record->media_ID ( ); ?>','rule violation',<?php echo $record_ID . ",'" . $record_class . "'"; ?>);">Rule violation</a><br />
<?php if ( 1 != ALLOW_ADULT_CONTENT ) { ?>
			<a href="javascript:void(0);" onclick="mod_action('media','<?php echo $record->media_ID ( ); ?>','adult content',<?php echo $record_ID . ",'" . $record_class . "'"; ?>);">Adult content</a><br />
<?php } ?>
			<a href="javascript:void(0);" onclick="mod_action('media','<?php echo $record->media_ID ( ); ?>','illegal content',<?php echo $record_ID . ",'" . $record_class . "'"; ?>);">Illegal content</a>
		</p>
	</div>
<?php } ?>
</div>
