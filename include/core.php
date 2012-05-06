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

/* INITIALIZE
================================================== */
// simple functions and strings that help to load and run other code
require ( 'initializers.php' );
// yo dawg, requiring this fill will require other files so you can require files while you require this file
require ( 'include/class_loader.php' );
// initiate the DB connection
_APPCONFIG::start_app ( );
// set the scope of the DB
global $DB;
if ( $DB->connect_error ) {
	// don’t go any further in this file
	return false;
}
// basic vars for passing strings to other components
global $error, $errors, $queries;
$errors = $RUNTIME_OPTIONS = $queries = array ( );
// the
require ( 'helpers_core.php' );
require ( 'routes.php' );
if ( $_SERVER['HTTP_HOST'] != DOMAIN ) {
	redirect_to ( '' );
}

/* TRACKER
================================================== */
// counters useful for performance analysis
$TRACKER = array (
	'start_time' => microtime ( true ), 
	'queries_select' => 0, 
	'queries_update' => 0, 
	'queries_insert' => 0, 
	'queries_delete' => 0 
);

/* LOAD SETTINGS
================================================== */
Setting::load ( array ( 'first', 'conditions' => "name = 'LOAD_ALL_SETTINGS_AT_STARTUP'", 'select' => MIN_SETTINGS_FIELDS ) );
if ( LOAD_ALL_SETTINGS_AT_STARTUP == 1 ) {
	// load everything
	Setting::load ( array ( 'select' => MIN_SETTINGS_FIELDS ) );
} else {
	// or load just what is necessary
	Setting::load ( array ( 'conditions' => "load_at_startup = 1 AND category != 'USER_SETTINGS'", 'select' => MIN_SETTINGS_FIELDS ) );
}
// Apache Benchmark should be toggled on the system itself, but this is one simple, accessible way to limit potential resource strain
if ( defined ( 'ALLOW_APACHE_BENCHMARK' ) && ALLOW_APACHE_BENCHMARK != 1 ) {
	if ( strpos ( $_SERVER['HTTP_USER_AGENT'], 'ApacheBench' ) !== FALSE ) {
		header ( 'Status: 403', TRUE, 403 );
		die ( );
	}
}

/* SESSION
================================================== */
session_set_cookie_params ( ( int ) SESSION_LIFETIME, SESSION_PATH, DOMAIN );
// alternate method to get the session, if cookies can’t be used
// TODO: is this necessary? is this problematic?
if ( isset ( $_POST['session_ID'] ) ) {
	session_id ( $_POST['session_ID'] );
}
session_start ( );
define ( 'SESSION_ID', session_id ( ) );
// validating the session is important, but not always desirable if flexibility 
if ( isset ( $_SESSION['logged_in_user'] ) && $_SESSION['logged_in_user']['type'] >= USER_LEVEL_VALIDATE_SESSION ) {
	validate_session ( );
}

/* MANUAL-FIREWALL STUFF
================================================== */
// TODO: log rotation
//file_put_contents ( 'ip_log.txt', $_SERVER['REMOTE_ADDR'] . "\t" . time ( ) . "\t" . $_SERVER['REQUEST_URI'] . "\t" . $_SERVER['HTTP_X_FORWARDED_FOR'] . "\t" . $_SERVER['HTTP_CLIENT_IP'] . "\t" . $_SERVER['HTTP_PC_REMOTE_ADDR'] . "\t" . $_SERVER['VIA'] . "\t" . $_SERVER['FORWARDED'] . "\n", FILE_APPEND );
//file_put_contents ( 'ip_log.txt', $_SERVER['REMOTE_ADDR'] . "\t" . time ( ) . "\t" . $_SERVER['REQUEST_URI'] . "\n", FILE_APPEND );

/* MAINTENANCE MODE
================================================== */
if ( MAINTENANCE_MODE == 1 ) {
	// while maintenance mode is on, disable the site, except for privileged users and/or the account actions
	if ( $controller != 'account' && ( ! isset ( $_SESSION['logged_in_user'] ) || intval ( $_SESSION['logged_in_user']['type'] ) < MODERATOR_TYPE ) ) {
		header ( 'Status: 503', TRUE, 503 );
		header ( 'Content-Type: text/plain', TRUE );
		Setting::load ( array ( 'first', 'conditions' => "`name` = 'MAINTENANCE_MESSAGE'", 'select' => 'name, value' ) );
		exit ( MAINTENANCE_MESSAGE );
	} else {
		// notify users
		$errors[] = 'Maintenance mode is on.';
	}
}
// TODO: unit, functional, and integration tests; provide a secure method to trigger these from here

/* LOG REMOTE ADDRESSES
================================================== */
// logging IP addresses can be controversial, but it’s one of the primary ways to reduce abuse
if ( TRACK_REMOTE_ADDRESSES == 1 ) {
	global $user_remote_address;
	if ( $user_remote_address = RemoteAddress::find ( array ( 'first', 'conditions' => array ( "remote_address = '%s'", $_SERVER['REMOTE_ADDR'] ), 'select' => 'ID, remote_address, last_seen, permission_to_view, permission_to_register, permission_to_post, permission_to_search, users_count' ) ) ) {
		// the remote address was found
		$user_remote_address->last_seen = time ( );
		$user_remote_address->update ( 'last_seen' );
	} else {
		// otherwise, insert it; “permission_to_view” = 0 will ban all new IPs; be careful :-)
		$user_remote_address = new RemoteAddress ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'permission_to_view' => 1, 'permission_to_register' => 1, 'permission_to_post' => 1, 'permission_to_search' => 1, 'host_name' => gethostbyaddr ( $_SERVER['REMOTE_ADDR'] ) ) );
		$user_remote_address->create ( );
	}
	// BANNED
	// TODO: toggle this through a site setting
	if ( $user_remote_address->permission_to_view == 0 ) {
		header ( 'Content-Type: text/plain' );
		die ( 'Your IP address is banned for spam or abusive activity.' );
	}
/* TODO: these can be used to detect transparent proxies, which are one major source of abuse, but many legitimate, well-configured services report these headers, too (mobile phones and certain ISPs, in particular)
	$_SESSION['remote_port'] = $_SERVER['REMOTE_PORT'];
	$_SESSION['forwarded_for'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
	$_SESSION['forwarded_server'] = $_SERVER['HTTP_X_FORWARDED_SERVER'];
	$_SESSION['forwarded_host'] = $_SERVER['HTTP_X_FORWARDED_HOST'];
	$_SESSION['HTTP_via'] = $_SERVER['HTTP_VIA'];
	$_SESSION['HTTP_client_IP'] = $_SERVER['HTTP_CLIENT_IP'];
*/
}

/* AUTO-CREATE ACCOUNTS
================================================== */
// possibly leads to a runaway use of server resources if constantly creating accounts
// TODO: throttle it to a maximum number of X new accounts per interval Y?
if ( defined ( 'AUTOMATICALLY_REGISTER_ACCOUNTS' ) && AUTOMATICALLY_REGISTER_ACCOUNTS == 1 ) {
	if ( ! isset ( $_SESSION['logged_in_user'] ) && ( ! array_key_exists ( 'notice', $_SESSION ) || $_SESSION['notice'] != 'You are logged out.' ) && $controller != 'account' ) {
		// dont trigger this for bots and crawlers
		if ( ! check_user_agent ( 'bot' ) ) {
			// dont trigger this for the hosts of bots and crawlers (i.e. they check cloaking with these)
			if ( ! ( isset ( $user_remote_address->host_name ) && ! check_host_name ( $user_remote_address->host_name, 'bot' ) ) ) {
				if ( ! isset ( $user_remote_address ) ) {
					$user_remote_address = RemoteAddress::find ( array ( 'first', 'conditions' => array ( "remote_address = '%s'", $_SERVER['REMOTE_ADDR'] ), 'select' => 'ID, remote_address, last_seen, permission_to_view, permission_to_register, users_count' ) );
				}
				if ( ! isset ( $user_remote_address ) ) {
					$user_remote_address = new RemoteAddress ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'permission_to_view' => 1, 'permission_to_register' => 1, 'permission_to_post' => 1, 'permission_to_search' => 1, 'host_name' => gethostbyaddr ( $_SERVER['REMOTE_ADDR'] ) ) );
					$user_remote_address->create ( );
				}
				if ( empty ( $user_remote_address->host_name ) ) {
					$user_remote_address->host_name = gethostbyaddr ( $_SERVER['REMOTE_ADDR'] );
					$user_remote_address->update ( 'host_name' );
				}
				if ( $user_remote_address->permission_to_register == 1 ) {
					// create a new account!
					$my_pass = random_password ( 16 );
					$user_account = new UserAccount ( array ( 'password' => $my_pass, 'status' => 'new', 'username' => random_password ( 20 ) . random_password ( 20 ), 'internal_notes' => $_SERVER['REMOTE_ADDR'] . ' (' . $user_remote_address->host_name . ')' . "\r\n\r\n" . $_SERVER['HTTP_USER_AGENT'] ) );
					if ( $new_ID = $user_account->create ( ) ) {
						// TODO: do some of these belong inside the UserAccount class?
						$user_account = UserAccount::find ( $new_ID );
						if ( $new_ID == 1 ) {
							// hackish method to make the first user account an admin
							$user_account->type = ADMIN_TYPE;
							$user_account->update ( 'type' );
						}
						$user_remote_address->update_counter ( 'users_count', 1 );
						$user_account->temp_password = $my_pass;
						$user_account->update ( 'temp_password' );
						$_SESSION['notice_now'] = 'Welcome to ' . DOMAIN . '! An account has automatically been created for you.';
						$_SESSION['logged_in_user'] = array ( );
						$_SESSION['logged_in_user']['ID'] = $user_account->ID ( );
						$_SESSION['logged_in_user']['username'] = $user_account->username ( );
						$_SESSION['logged_in_user']['remote_address'] = $_SERVER['REMOTE_ADDR'];
						$_SESSION['logged_in_user']['type'] = $user_account->type ( );
						$_SESSION['temp_password'] = $my_pass;
					}
				}
			}
		}
	}
}

/* MOBILE
================================================== */
global $mobile_mode;
$mobile_mode = 0;
if ( defined ( 'DETECT_MOBILE_DEVICES' ) && DETECT_MOBILE_DEVICES == 1 ) {
	if ( check_user_agent ( 'mobile' ) ) {
		$mobile_mode = 1;
	}
}


/* CONTINUE
================================================== */
require ( 'include/helpers_middleware.php' );
?>
