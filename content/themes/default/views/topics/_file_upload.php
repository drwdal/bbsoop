<?php if ( MEDIA_GOES_LIVE_WHEN_POSTED != 1 ) { ?>
	<p class="help"><label class="xsmall"><strong>Note:</strong></label> Your upload will be held in moderation before going live.</p>
<?php } ?>
<p class="buttonized float-left-with_margin"><a href="javascript:void(0);" onclick="jQuery('#upload_instructions').slideToggle('fast'); if ( this.innerHTML == 'View upload restrictions' ) { this.innerHTML = 'Hide upload restrictions'; } else { this.innerHTML = 'View upload restrictions'; }">View upload restrictions</a></p>
<p><input type="hidden" name="MAX_FILE_SIZE" value="<?php echo UPLOAD_MAXIMUM_FILE_SIZE; ?>" /><input type="file" accept="<?php echo ACCEPT_MIME_TYPES; ?>" name="file_upload" id="file_upload" class="medium" tabindex="3" /></p>
<div id="upload_instructions" class="wrapper clear" style="display: none;">
	<p>
<?php if ( ALLOW_ADULT_CONTENT != 1 ) { ?>
		<label class="large">Adult content:</label> <span class="required">NOT ALLOWED</span><br />
<?php } ?>
		<label class="large">Maximum file size:</label> <?php echo humanize_bytes ( UPLOAD_MAXIMUM_FILE_SIZE ); ?><br />
		<label class="large">Allowed file types:</label> <?php echo ACCEPT_FILE_EXTENSIONS; ?><br />
		<label class="large">Thumbnail size:</label> <?php echo THUMBNAIL_WIDTH . 'w × ' . THUMBNAIL_HEIGHT . 'h'; ?><br />
		<label class="large">Maximum dimensions:</label> <?php echo UPLOAD_MAXIMUM_WIDTH . 'w × ' . UPLOAD_MAXIMUM_HEIGHT . 'h'; ?><br />
		<label class="large"><a href="http://en.wikipedia.org/wiki/Exchangeable_image_file_format" target="_blank">EXIF data</a> is:</label> <?php echo ( ( REMOVE_EXIF_AND_PROFILE == 0 ) ? 'preserved' : 'removed' ); ?>
	</p>
</div>
