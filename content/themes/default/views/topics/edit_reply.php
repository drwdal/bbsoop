<?php if ( isset ( $_POST['preview'] ) ) { ?>
<div id="preview" class="wrapper c">
	<h2>Preview:</h2>
	<?php echo parse_post_body ( $_POST['record']['body'] ); ?>
</div>
<hr class="a" />
<?php } ?>
<form action="" method="post" class="wrapper edit-record">
	<?php IMGBOARD_render_view ( 'topics/_edit_record' ); ?>
	<p class="clear"><input type="submit" value="Submit" tabindex="4" /><input type="hidden" value="add" name="mode" /><?php nonce_for_form ( ); ?> <input type="submit" value="Preview" tabindex="5" name="preview" class="float-right" /></p>
</form>
<?php IMGBOARD_render_view ( 'common/_formatting_instructions' ); ?>
