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

/* APP
================================================== */
function IMGBOARD_init_controller ( ) {
	global $controller;
	require ( BASE_PATH . THEME_PATH . "controllers/application_controller.php" );
	if ( file_exists ( BASE_PATH . THEME_PATH . "controllers/${controller}_controller.php" ) ) {
		require ( BASE_PATH . THEME_PATH . "controllers/{$controller}_controller.php" );
	} else {
		set_error ( 404 );
	}
}
function IMGBOARD_get_helper ( ) {
	global $controller, $action;
	if ( file_exists ( BASE_PATH . THEME_PATH . "helpers/{$controller}_helper.php" ) ) {
		require ( BASE_PATH . THEME_PATH . "helpers/{$controller}_helper.php" );
	} else {
		require ( BASE_PATH . THEME_PATH . "helpers/application_helper.php" );
	}
}
function IMGBOARD_init_view ( ) {
	global $controller, $action, $layout;
	if ( isset ( $layout ) && $layout === 0 ) {
		// do nothing?
	} else {
		if ( file_exists ( BASE_PATH . THEME_PATH . "views/layout/$controller.php" ) ) {
			require ( BASE_PATH . THEME_PATH . "views/layout/$controller.php" );
		} else {
			require ( BASE_PATH . THEME_PATH . 'views/layout/application.php' );
		}
	}
}
function IMGBOARD_view ( ) {
	global $controller, $action, $error, $TRACKER;
	$TRACKER['start_time_view'] = microtime ( TRUE );
	if ( ! ( ! empty ( $error ) && render_error ( $error ) ) ) {
		if ( file_exists ( BASE_PATH . THEME_PATH . "views/$controller/$action.php" ) ) {
			require ( BASE_PATH . THEME_PATH . "views/$controller/$action.php" );
		}
	}
	$TRACKER['end_time_view'] = microtime ( TRUE );
}
function IMGBOARD_render_view ( $path ) {
	// TODO: validation to prevent this from being dangerous
	if ( file_exists ( BASE_PATH . THEME_PATH . "views/$path.php" ) ) {
		include ( BASE_PATH . THEME_PATH . "views/$path.php" );
	} else {
		trigger_error ( $path . 'does not exist in IMGBOARD_render_view ( );' );
	}
}
function IMGBOARD_class_list ( $action='return', $insert_class=NULL ) {
	if ( $action == 'insert' && ! in_array ( $insert_class ) ) {
		$GLOBALS['IMGBOARD_class_list'][] = $insert_class;
	}
	// always return, but only if set
	if ( isset ( $GLOBALS['IMGBOARD_class_list'] ) && ! empty ( $GLOBALS['IMGBOARD_class_list'] ) ) {
		return $GLOBALS['IMGBOARD_class_list'];
	}
}

/* NONCE
================================================== */
function nonce_for_form ( ) {
	echo '<input type="hidden" name="nonce" value="' . _APPCONFIG::nonce_generate_hash ( ) . '" />';
}
function nonce_is_valid ( $nonce ) {
	// validates a nonce
	global $TRACKER, $errors;
	// not necessary // $nonce = preg_replace ( SIMPLE_HEXIDECIMAL_PATTERN, '', $nonce );
	if ( _APPCONFIG::nonce_generate_hash ( ) == $nonce ) {
		$TRACKER['nonce_valid'] = TRUE;
		return true;
	}
	$TRACKER['nonce_valid'] = FALSE;
	$errors[] = "Invalid or expired authorization token; please try again. (This is a known bug)";
	return false;
}
function valid_post ( ) {
	global $errors;
	// valid if POST and nonce validates
	if ( $_SERVER['REQUEST_METHOD'] == 'POST' && nonce_is_valid ( $_POST['nonce'] ) ) {
		return true;
	}
	// silent failure
	return false;
}

function uridecode ( $uri ) {
	return str_replace ( '_', ' ', urldecode ( $uri ) );
}

/* UPLOADS
================================================== */
function valid_upload ( $file_name ) {
	if ( is_uploaded_file ( $file_name ) ) {
		return true;
	}
	return false;
}



?>