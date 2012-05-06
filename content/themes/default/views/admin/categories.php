<?php foreach ( $GLOBALS['categories'] as $category ) { ?>
<form action="" method="post" id="category_<?php echo $category->ID ( ); ?>" class="wrapper clear c">
	<h2 class="rule"><?php echo $category->name; ?></h2>
	<div class="stats float-right wrapper c">
		<p>
			<label>Topic count:</label><?php echo number_format ( $category->topics_count ); ?><br />
			<label>Reply count:</label><?php echo number_format ( $category->replies_count ); ?><br />
			<label>Media count:</label><?php echo number_format ( $category->media_count ); ?>
		</p>
	</div>
	<p>
		<label for="category_<?php echo $category->ID ( ); ?>_status">Visible to</label>
		<select name="category[<?php echo $category->ID ( ); ?>][status]" id="category_<?php echo $category->ID ( ); ?>_status" class="medium">
			<option value="-1"<?php if ( -1 == $category->status ) { echo ' selected="selected"'; } ?>>Nobody</option>
			<option value="5"<?php if ( 5 == $category->status ) { echo ' selected="selected"'; } ?>>Admins</option>
			<option value="4"<?php if ( 4 == $category->status ) { echo ' selected="selected"'; } ?>>Moderators</option>
			<option value="1"<?php if ( 1 == $category->status ) { echo ' selected="selected"'; } ?>>Regulars</option>
			<option value="0"<?php if ( 0 == $category->status ) { echo ' selected="selected"'; } ?>>Everyone</option>
		</select>
	</p>
	<p><label for="category_<?php echo $category->ID ( ); ?>_locked">Locked</label><input type="hidden" value="0" name="category[<?php echo $category->ID ( ); ?>][status]" /><input type="checkbox" value="1" name="category[<?php echo $category->ID ( ); ?>][status]" id="category_<?php echo $category->ID ( ); ?>_locked" /></p>
</form>
<?php } ?>
	<h3 class="clear">Settings</h3>
	<h4>Add new</h4>
