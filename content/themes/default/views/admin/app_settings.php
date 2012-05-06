<p>These settings can be used to adjust how the application interacts with the database and processes user actions.</p>
<form action="" method="post" class="wrapper existing-records">
	<table class="list" cellspacing="0" cellpadding="0">
<?php
$prevcategory = '';
$tabindex = 0;
foreach ( $GLOBALS['settings'] as $v ) {
$tabindex++;
switch ( $v->type ) {
	case 'text':
		$input_class = 'large';
		break;
	case 'integer':
		$input_class = 'small';
		break;
	case 'float':
		$input_class = 'xsmall';
		break;
	default:
		$input_class = 'medium';
		break;
} 
?>
<?php if ( $v->category != $prevcategory ) { ?>
		<tr>
			<th colspan="4" class="superheader align-left">
				<h3><?php echo htmlspecialchars ( $v->category ); ?></h3>
			</th>
		</tr>
		<tr>
			<th class="align-left">Name</th>
			<th class="align-left">Value</th>
			<th class="align-left">Description</th>
<?php if ( SETTINGS_SORTABLE == 1 ) { ?>
			<th class="align-left">Order by</th>
<?php } ?>
			<th>&nbsp;</th>
		</tr>
<?php } ?>
		<tr>
			<td><label for="settings_<?php echo htmlspecialchars ( $v->ID ( ) ); ?>_value" class="large"><?php echo htmlspecialchars ( $v->name ); ?></label></td>
			<td>
<?php if ( $v->type == 'boolean' ) { ?>
				<input type="hidden" value="0" name="settings[<?php echo $v->ID ( ); ?>][value]" /><input type="checkbox" value="1" name="settings[<?php echo $v->ID ( ); ?>][value]" id="settings_<?php echo htmlspecialchars ( $v->ID ( ) ); ?>_value"<?php if ( $v->value == 1 ) { ?> checked="checked"<?php } ?> tabindex="<?php echo $tabindex; ?>" />
<?php } else if ( $v->option_labels == '' || $v->option_labels == NULL || $v->option_values == '' || $v->option_values == NULL ) { ?>
				<input type="text" class="<?php echo $input_class; ?>" name="settings[<?php echo $v->ID ( ); ?>][value]" id="settings_<?php echo htmlspecialchars ( $v->ID ( ) ); ?>_value" value="<?php echo htmlspecialchars ( $v->value ); ?>" tabindex="<?php echo $tabindex; ?>" />
<?php } else { ?>
				<select name="settings[<?php echo $v->ID ( ); ?>][value]" id="settings_<?php echo htmlspecialchars ( $v->ID ( ) ); ?>_value" class="<?php echo $input_class; ?>" tabindex="<?php echo $tabindex; ?>">
<?php
	$my_option_labels = explode ( "\n", $v->option_labels );
	$my_option_values = explode ( "\n", $v->option_values );
	$my_options = array_combine ( $my_option_labels, $my_option_values );
	foreach ( $my_options as $key => $value ) {
		switch ( $v->type ) {
			case 'integer':
				$value = ( int ) $value;
				break;
			case 'float':
				$value = ( float ) $value;
				break;
			default:
				$value = ( string ) $value;
				break;
		}
?>
					<option value="<?php echo htmlspecialchars ( trim ( $value ) ); ?>"<?php if ( trim ( $v->value ) == trim ( $value ) ) { ?> selected="selected"<?php } ?>><?php echo htmlspecialchars ( trim ( $key ) ); ?></option>
<?php } ?>
				</select>
<?php } ?>
			</td>
			<td class="help"><?php echo htmlspecialchars ( $v->description ); ?></td>
<?php if ( SETTINGS_SORTABLE == 1 ) { ?>
			<td><input type="text" class="xsmall" name="settings[<?php echo $v->ID ( ); ?>][order_by]" id="settings_<?php echo htmlspecialchars ( $v->name ); ?>_orderby" value="<?php echo htmlspecialchars ( $v->order_by ); ?>" tabindex="<?php echo ( $tabindex + count ( $GLOBALS['settings'] ) ); ?>" />
<?php } ?>
			<td>
<?php if ( $v->editable == 1 ) { ?>
				<a href="" class="buttonized button-edit"><span>Edit</span></a> <a href="" class="buttonized button-delete"><span>Delete</span></a>
<?php } else { ?>
				&nbsp;
<?php } ?>
			</td>
		</tr>
<?php
	$prevcategory = $v->category;
}
########## NEW SETTING FORM
?>
	</table>
	<p><input type="submit" value="Update" /><input type="hidden" value="update" name="mode" /><?php nonce_for_form ( ); ?></p>
</form>

<h2><a href="javascript:void(0);" onclick="jQuery('#new_setting_wrapper').slideToggle('fast');">Add new…</a></h2>
<div id="new_setting_wrapper" class="wrapper" style="display: none;">
	<p>These settings are best suited to adjust the application’s interaction with the database. Presentation and theme settings should be treated as user preferences.</p>
	<form action="" method="post" class="new-record wrapper">
		<p><span class="required">*</span> denotes required fields</p>
		<p><label for="setting_name" class="medium">Name <span class="required">*</span></label> <input type="text" name="setting[name]" id="setting_name" value="" class="large" /> <span class="help">letters, numbers and underscores only—automatically capitalized; spaces become underscores</span></p>
		<p><label for="setting_category" class="medium">Category <span class="required">*</span></label> <input type="text" name="setting[category]" id="setting_category" value="" class="mlarge" /> <span class="help">letters, numbers and underscores only—automatically capitalized; spaces become underscores</span></p>
		<p><label for="setting_type" class="medium">Type <span class="required">*</span></label> <select name="setting[type]" id="setting_type" class="medium"><?php foreach ( SETTINGS_PSEUDO_TYPES ( ) as $option ) { echo "<option value=\"$option\">$option</option>"; } ?></select> <span class="help">boolean, date, and datetime have special validations</span></p>
		<p><label for="setting_value" class="medium">Value <span class="required">*</span></label> <input type="text" name="setting[value]" id="setting_value" value="" class="mlarge" /></p>
		<p><label for="setting_default" class="medium">Default</label> <input type="text" name="setting[default]" id="setting_default" value="" class="mlarge" /></p>
		<p><label for="setting_option_labels" class="medium">Option labels</label> <textarea name="setting[option_labels]" id="setting_option_labels" class="large" rows="4" cols="60"></textarea> <span class="help">labels for setting options, one per line; leave blank for none</span></p>
		<p><label for="setting_option_values" class="medium">Option values</label> <textarea name="setting[option_values]" id="setting_option_values" class="medium" rows="4" cols="40"></textarea> <span class="help">internal values for the options, one per line; leave blank for none</span></p>
		<p><label for="setting_description" class="medium">Description</label> <input type="text" name="setting[description]" id="setting_description" value="" class="xlarge" /></p>
		<p><label for="setting_order_by" class="medium">Order by</label> <input type="text" name="setting[order_by]" id="setting_order_by" value="0" class="xsmall" /> <span class="help">integer that affects the sort order</span></p>
		<p><label for="setting_load_at_startup" class="medium">Load at startup</label> <input type="hidden" name="setting[load_at_startup]" value="0" /><input type="checkbox" name="setting[load_at_startup]" id="setting_load_at_startup" value="1" /></p>
		<p><input type="submit" value="Add" /><input type="hidden" value="add" name="mode" /><?php nonce_for_form ( ); ?></p>
	</form>
</div>