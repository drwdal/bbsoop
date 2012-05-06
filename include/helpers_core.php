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

/* REDIRECT
================================================== */
function redirect_to ( $uri, $status = 303 ) {
	$status = preg_replace ( INTEGER_PATTERN, '', $status );
	// status must send first
	header ( 'Status: ' . $status, TRUE, $status );
	header ( 'Location: ' . BASE_URL . $uri, TRUE );
	IMGBOARD_footer ( );
	exit ( );
}

/* OBJECTS
================================================== */
function var_to_hash ( $var ) {
	return ( sha1 ( var_export ( $var, TRUE ) ) );
}


function validate_session ( ) {
	$valid_session = 1;
	if ( defined ( 'VALIDATE_REMOTE_ADDRESS' ) && intval ( VALIDATE_REMOTE_ADDRESS ) == 1 ) {
		if ( isset ( $_SESSION['REMOTE_ADDR'] ) && ! empty ( $_SESSION['REMOTE_ADDR'] ) ) {
			// validate current
			if ( $_SESSION['REMOTE_ADDR'] != $_SERVER['REMOTE_ADDR'] ) {
				$valid_session = 0;
			}
		} else {
			// set new
			$_SESSION['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
		}
	}
	if ( defined ( 'VALIDATE_USER_AGENT' ) && intval ( VALIDATE_USER_AGENT ) == 1 ) {
		if ( isset ( $_SESSION['HTTP_USER_AGENT'] ) && ! empty ( $_SESSION['HTTP_USER_AGENT'] ) ) {
			// validate current
			if ( $_SESSION['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT'] ) {
				$valid_session = 0;
			}
		} else {
			// set new
			$_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
		}
	}
	if ( $valid_session != 1 ) {
		// clear session data, send cookie that deletes, and destroy the session
		$_SESSION = array();
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
		}
		session_destroy();
		$_SESSION['notice'] = 'Your browser or connection location changed; you have been logged out.';
	}
}

/* USER-AGENTS
================================================== */
function check_user_agent ( $type = NULL ) {
	$user_agent = strtolower ( $_SERVER['HTTP_USER_AGENT'] );
	if ( $type == 'bot' ) {
		// matches popular bots
		if ( preg_match ( "/googlebot|adsbot|yahooseeker|yahoobot|msnbot|yandexbot|watchmouse|pingdom\.com|feedfetcher-google|twiceler/", $user_agent ) ) {
			return true;
			// watchmouse|pingdom\.com are "uptime services"
		}
	} else if ( $type == 'browser' ) {
		// matches core browser types
		if ( preg_match ( "/mozilla\/|opera\//", $user_agent ) ) {
			return true;
		}
	} else if ( $type == 'mobile' ) {
		// matches popular mobile devices that have small screens and/or touch inputs
		// mobile devices have regional trends; some of these will have varying popularity in Europe, Asia, and America
		// detailed demographics are unknown, and South America, the Pacific Islands, and Africa trends might not be represented, here
		if ( preg_match ( "/phone|iphone|itouch|ipod|symbian|android|htc_|htc-|palmos|blackberry|opera mini|iemobile|windows ce|nokia|fennec|hiptop|kindle|mot |mot-|webos\/|blazer\/|samsung|sonyericsson|^sie-|nintendo|playstation/", $user_agent ) ) {
			// these are the most common
			return true;
		} else if ( preg_match ( "/mobile|pda;|avantgo|eudoraweb|minimo|netfront|brew|teleca|lg;|lge |wap;| wap /", $user_agent ) ) {
			// these are less common, and might not be worth checking
			return true;
		}
	}
	return false;
}

function check_host_name ( $host_name, $type ) {
	$host_name = strtolower ( $host_name );
	if ( $type == 'bot' ) {
		// useful to prevent certain automated actions from firing with bots or search engines (i.e. automated registration)
		// might possibly cause search engine "cloaking" issues, though (bot with regular user-agent sees different result than bot with proper user-agent)
		if ( preg_match ( "/googlebot|adsbot|yahooseeker|yahoobot|msnbot|watchmouse|pingdom\.com|feedfetcher-google/", $host_name ) ) {
			return true;
			// watchmouse|pingdom\.com are "uptime services"
		}
	}
}

function random_password ( $length = 8 ) {
	$length = ( int ) $length;
	if ( $length == 0 ) {
		$length = 8;
	}
	$password_source = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%?*&^-=+_';
	return substr ( str_shuffle ( $password_source ), 0, $length );
}



global $user_type_strings, $post_status, $media_status, $category_status;

/* USER TYPES
================================================== */
define ( 'ADMIN_TYPE', 5 );
define ( 'MODERATOR_TYPE', 4 );
define ( 'REGULAR_TYPE', 1 );
define ( 'PUBLIC_TYPE', 0 );
$user_type_strings = array ( 'public', 'regular', '', '', 'moderator', 'admin' );

/* RECORD STATUS
================================================== */
$post_status = array ( 'draft', 'pending approval', 'published', 'deleted' );
$media_status = array ( 'draft', 'pending approval', 'published', 'rejected', 'deleted', 'rule violation', 'adult content', 'illegal content' );
$category_status = array ( 'public', 'hidden' );
$report_reasons = array ( 'spam', 'rule violation', 'illegal content' );


/**
 * Iterate through an entire array and its children and strip the slashes from
 * each string.
 *
 * @param &$array The array, passed by reference.
 * @return void
 */
function stripslashes_from_array(&$array) {
	foreach ($array as $key => $value) {
		if (is_array($value)) {
			stripslashes_from_array($array[$key]);
		} else {
			$array[$key] = stripslashes($value);
		}
	}
}


if ( get_magic_quotes_gpc ( ) ) {
	stripslashes_from_array($_GET);
	stripslashes_from_array($_POST);
}
if ( get_magic_quotes_runtime ( ) ) { set_magic_quotes_runtime ( 0 ); }


?>
