<?php foreach ( $GLOBALS['counts'] as $key => $value ) { ?>
<p class="float-right"><a href="<?php echo BASE_URI; ?>admin/<?php echo htmlspecialchars ( $key ); ?>/1/reported">Reports</a></p>
<h2 class="rule"><a href="<?php echo BASE_URI; ?>admin/<?php echo htmlspecialchars ( $key ); ?>"><?php echo htmlspecialchars ( titleize ( $key ) ); ?></a></h2>
<?php foreach ( $value as $k => $v ) { ?>
<h3><?php echo htmlspecialchars ( $k ); ?> minutes</h3>
<ul>
<?php if ( isset ( $v['count'] ) && $v['count'] > 0 ) { ?>
	<li><a href="<?php echo BASE_URI; ?>admin/<?php echo htmlspecialchars ( $key ); ?>"><?php echo htmlspecialchars ( $v['count'] ); ?> new</a></li>
<?php } ?>
<?php if ( isset ( $v['reports'] ) && $v['reports'] > 0 ) { ?>
	<li><a href="<?php echo BASE_URI; ?>admin/<?php echo htmlspecialchars ( $key ); ?>/1/reported"><?php echo htmlspecialchars ( $v['reports'] ); ?> report<?php if ( $v['reports'] != 1 ) { echo 's'; } ?></a></li>
<?php } ?>
<?php if ( ( ! isset ( $v['count'] ) || $v['count'] == 0 ) && ( ! isset ( $v['reports'] ) || $v['reports'] == 0 ) ) { ?>
	<li class="help">None</li>
<?php } ?>
</ul>
<?php
	}
}
?>
