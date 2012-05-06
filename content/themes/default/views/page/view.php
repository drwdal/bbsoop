<?php if ( isset ( $_SESSION['logged_in_user'] ) && $_SESSION['logged_in_user']['type'] >= ADMIN_TYPE ) { ?>
<p id="top-button" class="float-right buttonized"><a href="<?php echo BASE_URI ?>admin/page_edit/<?php echo $GLOBALS['page']->ID ( ); ?>">Edit</a></p>
<?php } ?>
<?php echo get_cached_body ( $GLOBALS['page'] ); ?>
</div>