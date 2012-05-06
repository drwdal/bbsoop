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

global $controller, $action, $ID, $fragment, $extra, $page_title;

// all actions require login
ensure_login ( 0, 'account' );

switch ( $action ) {

	case 'index':
		global $private_messages;
		$page_title = 'Private messages';
		$today = date ( MYSQL_DATETIME_FORMAT );
		$private_messages = PrivateMessage::find ( array ( 'all', 'conditions' => array ( "to_user_ID = '%u' AND (expires_at IS NULL OR expires_at > '%s')", $_SESSION['logged_in_user']['ID'], $today ), 'order' => 'created_at DESC' ) );
		check_unread_private_messages_count ( );
	break;

	case 'new':
		global $private_message;
		$private_message = new PrivateMessage ( array ( 'body' => $_POST['private_message']['body'], 'anonymous' => $_POST['private_message']['anonymous'], 'from_user_ID' => $_SESSION['logged_in_user']['ID'], 'to_user_ID' => $_POST['private_message']['to_user_ID'] ) );
		if ( $_SESSION['logged_in_user']['type'] >= MODERATOR_TYPE ) {
			// MOD or greater can do special things with PMs
			if ( intval ( $_POST['private_message']['reply_allowed'] ) == 0 ) {
				$private_message->reply_allowed = 0;
			} else {
				$private_message->reply_allowed = 1;
			}
			if ( isset ( $_POST['private_message']['expires_at'] ) && ! empty ( $_POST['private_message']['expires_at'] ) ) {
				$expires_at = strtotime ( $_POST['private_message']['expires_at'] . ' 00:00:00' );
				$private_message->expires_at = date ( MYSQL_DATETIME_FORMAT, $expires_at );
			}
		} else {
			$private_message->reply_allowed = 1;
			$private_message->anonymous = 0;
		}
		$valid_reply_to = 1;
		if ( isset ( $ID ) && intval ( $ID ) > 0 ) {
			global $reply_to_private_message;
			// ID appears legit; check it out…
			$reply_to_private_message = PrivateMessage::find ( ( int ) $ID );
			if ( ! isset ( $reply_to_private_message ) ) {
				// invalid ID?
				$valid_reply_to = 0;
				$_SESSION['notice'] = 'Cannot find private message #' . intval ( $ID ) . '.';
			}
			if ( $reply_to_private_message->to_user_ID != $_SESSION['logged_in_user']['ID'] || $reply_to_private_message->reply_allowed != 1 ) {
				// replying to a message not “for you” or one that is expired
				$valid_reply_to = 0;
				$_SESSION['notice'] = 'Cannot reply to private message #' . intval ( $ID ) . '.';
			}
			if ( isset ( $reply_to_private_message->expires_at ) && time ( ) > intval ( $reply_to_private_message->expires_at ( 'U' ) ) ) {
				// old message
				$valid_reply_to = 0;
				$_SESSION['notice'] = 'Cannot reply to an expired private message.';
			}
			if ( $valid_reply_to == 1 ) {
				// everything checks out, so inherit the conversation_ID and user_ID and keep this chain going
				if ( isset ( $reply_to_private_message->conversation_ID ) && intval ( $reply_to_private_message->conversation_ID ) > 0 ) {
					// already part of a conversation thread
					$private_message->conversation_ID = $reply_to_private_message->conversation_ID;
				} else {
					// new conversation thread
					$private_message->conversation_ID = $reply_to_private_message->ID ( );
				}
				$private_message->to_user_ID = $reply_to_private_message->from_user_ID;
			}
		}
		// if no $ID then assume this is a new PM to admin(s)
		// TODO: assign new PMs to admin(s) to_user_ID of 0? Allow admins and/or mods the ability to check this “pool” of messages?
		if ( ! isset ( $private_message->to_user_ID ) ) {
			$private_message->to_user_ID = 0;
		}
		// this could be where some minimum account age is required to PM the admin(s)
		if ( $valid_reply_to != 1 ) {
			redirect_to ( 'account/info/' );
		}
		$user_account = UserAccount::find ( ( int ) $private_message->to_user_ID );
		if ( isset ( $fragment ) && $fragment == 'quote' && $_SERVER['REQUEST_METHOD'] == 'GET' && isset ( $reply_to_private_message ) ) {
			// quoting reply
			$output = preg_replace ( "/(^|\r\n)/", "\\1> ", $reply_to_private_message->body ) . "\n";
			$_POST['private_message']['body'] = $output . $private_message->body;
		}
		if ( valid_post ( ) && isset ( $user_account ) ) {
			// create new message
			global $errors;
			$errors_count = count ( $errors );
			if ( $private_message->validates ( ) ) {
				$user_account->update_counter ( 'unread_private_messages_count', 1 );
			}
			if ( $errors_count == count ( $errors ) ) {
				if ( $new_ID = $private_message->create ( ) ) {
					if ( intval ( $_SESSION['logged_in_user']['type'] ) >= MODERATOR_TYPE ) {
						$_SESSION['notice'] = 'Private message sent to <a href="' . BASE_URI . 'admin/user/' . $private_message->to_user_ID . '">User ' . $private_message->to_user_ID . '</a>.';
					} else {
						$_SESSION['notice'] = 'Private message sent.';
					}
					if ( LOG_ACTIONS == 1 ) {
						$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $new_ID, 'record_table' => PrivateMessage::table, 'record_class' => 'PrivateMessage', 'description' => 'sent a new private message', 'action' => 'insert' ) );
						$action_log->create ( );
					}
					redirect_to ( '', 303 );
				}
			}
		}
		// display form to create a message
		$page_title = 'New private message';
	break;

	case 'view':
		global $private_message, $logged_in_user;
		$page_title = 'Private message #' . intval ( $ID );
		$private_message = PrivateMessage::find ( array ( 'first', 'conditions' => array ( "ID = '%u' AND to_user_ID = '%u'", $ID, $_SESSION['logged_in_user']['ID'] ) ) );
		if ( ! isset ( $private_message ) ) {
			$_SESSION['notice'] = 'Could not find private message #' . intval ( $ID ) . '.';
			redirect_to ( '' );
		}
		if ( isset ( $private_message->expires_at ) ) {
			$expires_at = ( int ) $private_message->expires_at ( 'U' );
			if ( $expires_at < time ( ) ) {
				$_SESSION['notice'] = 'Private message #' . intval ( $ID ) . ' has expired and cannot be read.';
				redirect_to ( '' );
			}
		}
		if ( $private_message->is_read == 0 ) {
			$private_message->is_read = 1;
			$private_message->update ( 'is_read' );
			if ( LOG_ACTIONS == 1 ) {
				$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $private_message->ID ( ), 'record_table' => PrivateMessage::table, 'record_class' => 'PrivateMessage', 'description' => 'read a new private message', 'action' => 'insert' ) );
				$action_log->create ( );
			}
			$logged_in_user->update_counter ( 'unread_private_messages_count', -1 );
		}
	break;

	default:
		set_error ( 404 );
	break;
}

?>