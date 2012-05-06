<div id="mod-action-topic" class="wrapper c" style="display: none;">
	<h3>Topic options</h3>
	<form action="<?php echo $GLOBALS['topic']->edit_URI ( ); ?>" method="post" class="wrapper c">
		<p><label for="record_locked" class="medium">Locked</label> <input type="hidden" value="0" name="record[locked]" /><input type="checkbox" value="1" name="record[locked]" id="record_locked"<?php if ( $GLOBALS['topic']->locked == 1 ) { echo ' checked="checked"'; } ?> /></p>
		<p><label for="record_sticky" class="medium">Sticky</label> <input type="hidden" value="0" name="record[sticky]" /><input type="checkbox" value="1" name="record[sticky]" id="record_sticky"<?php if ( $GLOBALS['topic']->sticky == 1 ) { echo ' checked="checked"'; } ?> /></p>
		<p><label for="record_safe_for_work" class="medium">safe for work</label> <input type="hidden" value="0" name="record[safe_for_work]" /><input type="checkbox" value="1" name="record[safe_for_work]" id="record_safe_for_work"<?php if ( $GLOBALS['topic']->safe_for_work == 1 ) { echo ' checked="checked"'; } ?> /></p>
		<p class="clear"><label class="medium">&nbsp;</label> <input type="submit" value="Update topic" name="mod_topic" /><?php nonce_for_form ( ); ?></p>
	</form>
</div>
