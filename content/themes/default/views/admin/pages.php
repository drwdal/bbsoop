<p id="top-button" class="float-right buttonized"><a href="<?php echo BASE_URI ?>admin/page_edit">New page</a></p>
<?php 
if ( count ( $GLOBALS['pages'] ) > 0 ) {
	foreach ( $GLOBALS['pages'] as $page ) {
?>
<div class="page wrapper c" id="page_<?php echo $page->ID ( ); ?>">
	<p class="float-right"><a href="<?php echo $page->URI ( ); ?>">View</a></p>
	<h2 class="rule"><a href="<?php echo BASE_URI . 'admin/page_edit/' . $page->ID ( ); ?>" title="edit page <?php echo $page->ID ( ); ?>"><?php echo htmlspecialchars ( $page->title ); ?></a></h2>
	<p class="page-meta">
		Created: <?php echo seconds_to_time ( time ( ) - $page->created_at ( 'U' ) ) . ' ago'; ?><br />
		Status: <?php echo htmlspecialchars ( $page->status ); ?><br />
	</p>
</div>
<?php
	} ?>
<div class="tan padded c wrapper">
	<p>Note: A large number of pages (50+) may reduce the application’s performance.</p>
</div>
<?php } else {
?>
<p>No pages.</p>
<?php } ?>
