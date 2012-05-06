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

global $board_name, $controller, $action, $ID, $fragment, $extra, $site_title, $original_URI;
$site_title = SITE_TITLE;

// look for a URI override
if ( $page = Page::find ( array ( 'first', 'conditions' => array ( "`URI` = '%s'", $original_URI ) ) ) ) {
	// override these vars
	$controller = 'page';
	$action = 'view';
	$ID = $page->ID ( );
} else if ( isset ( $board_name ) && ! empty ( $board_name ) ) {
	global $current_board;
	$current_board = Category::find ( array ( 'first', 'conditions' => array ( "`name` LIKE '%s'", uridecode ( $board_name ) ) ) );
	if ( ! isset ( $current_board ) ) {
		$current_board = Category::find ( 1 );
	}
	if ( ! isset ( $current_board ) ) {
		redirect_to ( 'install.php' );
	}
}

/* FUNCTIONS
================================================== */
function set_error ( $error_code ) {
	global $page_title, $error;
	switch ( $error_code ) {
		case 404:
			$page_title = 'Not found';
			header ( 'Status: 404', TRUE, 404 );
			$error = 404;
			break;
	}
}
function render_error ( ) {
	global $error;
	switch ( $error ) {
		case 404:
			IMGBOARD_render_view ( 'error/404' );
			return true;
			break;
		default:
			return false;
			break;
	}
}
function ensure_login ( $user_type = ADMIN_TYPE, $redirect_to = '' ) {
	global $logged_in_user;
	validate_session ( );
	$valid_login = 0;
	if ( isset ( $_SESSION['logged_in_user'] ) && isset ( $_SESSION['logged_in_user']['ID'] ) ) {
		// this is globally available as a convience method
		// but its properties are limited for security (i.e. password) and performance (counters, etc)
		$logged_in_user = UserAccount::find ( array ( 'first', 'conditions' => array ( "`ID` = '%u'", $_SESSION['logged_in_user']['ID'] ), 'select' => 'ID, created_at, username, email_address, type, status, ban_expires, ban_reason, replies_count, topics_count, media_count, search_count, unread_private_messages_count' ) );
		if ( isset ( $logged_in_user ) ) {
			if ( $logged_in_user->status == 'new' ) {
				// this is when an automatic account can actually be considered real
				$logged_in_user->status = 'active';
				$logged_in_user->update ( 'status' );
				$_SESSION['logged_in_user']['status'] = 'active';
				if ( LOG_ACTIONS == 1 ) {
					$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $logged_in_user->ID ( ), 'record_ID' => $logged_in_user->ID ( ), 'record_table' => UserAccount::table, 'record_class' => get_class ( $logged_in_user ), 'description' => 'created a new account', 'action' => 'insert' ) );
					$action_log->create ( );
				}
			}
			if ( $logged_in_user->type ( ) < $user_type ) {
				$_SESSION['notice'] = 'You do not have permission to access this page.';
				$valid_login = 0;
			} else if ( $logged_in_user->status ( ) != 'active' ) {
				$_SESSION['notice'] = 'Your account has been deactivated.';
				unset ( $_SESSION['logged_in_user'] );
				$valid_login = 0;
			} else if ( isset ( $logged_in_user->ban_expires ) ) {
				if ( time ( ) < $logged_in_user->ban_expires ( 'U' ) ) {
					$_SESSION['notice'] = 'Your account is banned.<br />The ban expires ' . $logged_in_user->ban_expires ( ) . ' UTC.';
					if ( isset ( $logged_in_user->ban_reason ) && ! empty ( $logged_in_user->ban_reason ) ) {
						$_SESSION['notice'] .= '<br />Ban reason: ' . htmlspecialchars ( $logged_in_user->ban_reason );
					}
					unset ( $_SESSION['logged_in_user'] );
					$valid_login = 0;
				} else {
					$logged_in_user->ban_expires = 'NULL';
					$logged_in_user->update ( 'ban_expires' );
					$_SESSION['notice'] = 'Your ban has expired.';
					if ( LOG_ACTIONS == 1 ) {
						$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $logged_in_user->ID ( ), 'record_table' => UserAccount::table, 'record_class' => 'UserAccount', 'description' => 'returned after a ban', 'action' => 'update' ) );
						$action_log->create ( );
					}
					if ( isset ( $logged_in_user->ban_reason ) && ! empty ( $logged_in_user->ban_reason ) ) {
						$_SESSION['notice'] .= '<br />Ban reason: ' . htmlspecialchars ( $logged_in_user->ban_reason );
						$logged_in_user->ban_reason = 'NULL';
						$logged_in_user->update ( 'ban_reason' );
					}
					$valid_login = 1;
				}
			} else {
				$valid_login = 1;
			}
		} else {
			$_SESSION['notice'] = 'You must log in to access this page.';
			$valid_login = 0;
			unset ( $_SESSION['logged_in_user'] );
		}
	} else {
		$_SESSION['notice'] = 'You must log in to access this page.';
	}
	if ( $valid_login != 1 ) {
		$_SESSION['redirect_destination'] = $_SERVER['REQUEST_URI'];
		redirect_to ( $redirect_to );
	}
	if ( $GLOBALS['controller'] != 'private_messages' ) {
		check_private_messages ( );
	}
	return true;
}
function verify_permissions ( $minimum_user_type ) {
	if ( isset ( $_SESSION['logged_in_user'] ) && isset ( $_SESSION['logged_in_user']['type'] ) && $_SESSION['logged_in_user']['type'] >= $minimum_user_type ) {
		return true;
	}
	return false;
}
function check_private_messages ( ) {
	global $logged_in_user;
	if ( $logged_in_user->unread_private_messages_count > 0 ) {
		// account thinks it has PMs; confirm in DB
		$today = date ( MYSQL_DATETIME_FORMAT );
		$private_message = PrivateMessage::find ( array ( 'first', 'conditions' => array ( "`to_user_ID` = '%u' AND `is_read` = '0' AND (expires_at IS NULL OR expires_at > '%s')", $_SESSION['logged_in_user']['ID'], $today ), 'order' => 'created_at ASC', 'select' => 'ID, created_at, to_user_ID, is_read' ) );
		if ( ! isset ( $private_message ) ) {
			$logged_in_user->unread_private_messages_count = 0;
			$logged_in_user->update ( 'unread_private_messages_count' );
			return false;
		}
		if ( $logged_in_user->unread_private_messages_count == 1 ) {
			$today = date ( MYSQL_DATETIME_FORMAT );
			$_SESSION['notice_now'] = 'You have <a href="' . BASE_URI . 'private_messages/view/' . $private_message->ID ( ) . '">1 new private message</a>.';
		} else if ( $user_pms->unread_private_messages_count > 1 ) {
			$_SESSION['notice_now'] = 'You have <a href="' . BASE_URI . 'private_messages/">' . $user_pms->unread_private_messages_count . ' new private messages</a>.';
		}
	}
}
function check_unread_private_messages_count ( ) {
	global $DB, $queries, $TRACKER;
	$today = date ( MYSQL_DATETIME_FORMAT );
	$queries[] = sprintf ( "SELECT count(ID) as count FROM `private_messages` WHERE `to_user_ID` = '%u' AND `is_read` = '0' AND (`expires_at` IS NULL OR `expires_at` > '%s')", $_SESSION['logged_in_user']['ID'], $today );
	$TRACKER['queries_select']++;
	if ( $result = $DB->query ( end ( $queries ) ) ) {
		// if there was a result, set the $unread_private_messages_count
		$unread_private_messages_count = $result->fetch_object ( );
		$result->free_result ( );
	}
	// if unread_private_messages_count not equal to $unread_private_messages_count->count
	if ( ! isset ( $_SESSION['logged_in_user']['unread_private_messages_count'] ) || $_SESSION['logged_in_user']['unread_private_messages_count'] != $unread_private_messages_count->count ) {
		$user_account = UserAccount::find ( array ( 'first', 'conditions' => array ( "ID = '%u'", $_SESSION['logged_in_user']['ID'] ), 'select' => 'ID, unread_private_messages_count' ) );
		$_SESSION['logged_in_user']['unread_private_messages_count'] = $unread_private_messages_count->count;
		$user_account->unread_private_messages_count = $unread_private_messages_count->count;
		$user_account->update ( 'unread_private_messages_count' );
	}
}
function ini_set_post_settings ( ) {
	if ( defined ( 'UPLOADS_ALLOWED' ) && UPLOADS_ALLOWED == 1 ) {
		if ( defined ( 'UPLOAD_MAXIMUM_FILE_SIZE' ) ) {
			ini_set ( 'upload_max_filesize', ( int ) UPLOAD_MAXIMUM_FILE_SIZE );
		}
		// this is useful if GD will be used
		ini_set ( 'memory_limit', '128M' );
	}
}
function respond_to_json ( $output ) {
	global $board_name, $controller, $action, $ID, $fragment, $layout;
	if ( isset ( $fragment ) && $fragment == 'json' ) {
		$layout = 0;
		header ( "Content-Type: application/json", TRUE );
		header ( "Content-Disposition: inline; filename=\"" . implode ( '-', array ( $board_name, $controller, $action ) ) . ".json\"", TRUE );
		echo json_encode ( $output );
	}
}

?>