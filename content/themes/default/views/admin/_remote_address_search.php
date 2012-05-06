<h3><a href="javascript:void(0);" onclick="jQuery('#remote_address-search').slideToggle('fast');">Search…</a></h3>
<form action="" method="post" id="remote_address-search" style="display: none;" class="search wrapper c">
	<fieldset class="wrapper c">
		<p><label for="search_remote_address">Address</label> <input type="text" name="search[remote address]" id="search_remote_address" class="large" value="<?php if ( isset ( $_SESSION['search'] ) && ! empty ( $_SESSION['search']['remote address'] ) ) { echo htmlspecialchars ( $_SESSION['search']['remote address'] ); } ?>" /> <span class="help">partial addresses work (i.e. 192.168)</span></p>
	</fieldset>
	<fieldset class="wrapper c">
		<legend>Sort by…</legend>
		<p class="less-margin"><input type="radio" name="search[order]" id="search_order_abuse" value="abuse"<?php if ( $GLOBALS['fragment'] == 'abuse' ) { echo ' checked="checked"'; } ?> /><label for="search_order_abuse">abusive actions</label></p>
		<p class="less-margin"><input type="radio" name="search[order]" id="search_order_date" value="date"<?php if ( empty ( $GLOBALS['fragment'] ) || $GLOBALS['fragment'] == 'date' ) { echo ' checked="checked"'; } ?> /><label for="search_order_date">date seen (default)</label></p>
		<p class="less-margin"><input type="radio" name="search[order]" id="search_order_remote_address" value="remote address"<?php if ( $GLOBALS['fragment'] == 'remote_address' ) { echo ' checked="checked"'; } ?> /><label for="search_order_remote_address">remote address</label></p>
	</fieldset>
	<fieldset class="wrapper c">
		<legend>Results</legend>
		<p class="less-margin"><label for="search_results_per_page">Per page</label> <select name="search[per page]"><option>50</option><option>75</option><option>100</option><option>150</option><option>200</option></select></p>
	</fieldset>
	<p><input type="submit" value="search" /></p>
</form>