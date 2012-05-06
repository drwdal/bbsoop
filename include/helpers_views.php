<?php
/* ==================================================

IMGBOARD Copyright 2008–2010 Authorized Clone LLC.

http://authorizedclone.com/
authorizedclone@gmail.com

This file is part of IMGBOARD.

IMGBOARD is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

IMGBOARD is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with IMGBOARD.  If not, see <http://www.gnu.org/licenses/>.

================================================== */
if ( ! defined ( 'IMGBOARD_INIT' ) ) { header ( 'Status: 403', TRUE, 403 ); die( ); }

function IMGBOARD_footer ( ) {
	require ( BASE_PATH . 'include/footer.php' );
}

function generate_nav ( $context = 'header', $interaction = 'javascript' ) {
	// is this not used?
	global $controller, $action;
	if ( $controller == 'admin' ) {
		include ( BASE_PATH . THEME_PATH . 'views/admin/_nav.php' );
	}
}

function page_title (  ) {
	echo htmlspecialchars ( $GLOBALS['page_title'] );
}

function IMGBOARD_title ( $output_mode = 'echo' ) {
	global $controller, $action, $ID;
	$output = '';
	if ( isset ( $ID ) && ! empty ( $ID ) ) {
		// viewing a specific record
		$output = str_replace ( '_', ' ', $ID ) . ' | ' . str_replace ( '_', ' ', $action ) . ' | ' . str_replace ( '_', ' ', $controller );
	} else {
		// viewing a general action
		$output = str_replace ( '_', ' ', $action ) . ' | ' . str_replace ( '_', ' ', $controller );
	}
	if ( $output_mode == 'return' ) {
		return $output;
	}
	echo $output;
}

function IMGBOARD_meta_description ( $output_mode = 'echo' ) {
	global $controller, $action, $ID;
	$output = '';
	if ( isset ( $ID ) && ! empty ( $ID ) ) {
		// viewing a specific record
		$output = "$ID | $action | $controller";
	} else {
		$output = "$action | $controller";
	}
	if ( $output_mode == 'return' ) {
		return $output;
	}
	echo $output;
}

function generate_notice ( ) {
	if ( isset ( $_SESSION['notice'] ) ) {
		include ( BASE_PATH . THEME_PATH . 'views/common/_notice.php' );
	}
}

?>