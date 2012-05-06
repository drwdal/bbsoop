<?php
/* ==================================================

IMGBOARD © 2010 Authorized Clone LLC

================================================== */
if ( ! defined ( 'IMGBOARD_INIT' ) ) { header ( 'FAIL', TRUE, 403 ); die( ); }

global $DB, $controller, $action, $ID, $fragment, $extra, $page_title, $original_URI;

switch ( $action ) {
	case 'index':
		$page_title = 'Documentation';
	break;

	case 'to-do':
		$page_title = 'To-do';
	break;

	default:
		set_error ( 404 );
	break;
}

?>