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

$ensure_login = array ( 'new' );
if ( in_array ( $action, $ensure_login ) ) { ensure_login ( 0, 'account' ); }

switch ( $action ) {

	case 'index':
		global $bulletins;
		$page_title = 'Bulletins';
		$bulletins = Bulletin::find ( array ( 'conditions' => "`status` = 'published'", 'order' => 'created_at DESC' ) );
	break;

	case 'new':
		global $bulletin, $errors, $logged_in_user;
		Setting::load ( array ( 'conditions' => "category = 'BULLETINS' OR category = 'MODERATION'", 'select' => 'category, ' . MIN_SETTINGS_FIELDS ) );
		ini_set_post_settings ( );
		$page_title = 'New bulletin';
		if ( NEW_BULLETINS_ALLOWED == 1 ) {
			if ( defined ( 'MINIMUM_ACCOUNT_AGE_NEW_BULLETIN' ) && ( ( time ( ) - intval ( $logged_in_user->created_at ( 'U' ) ) ) < MINIMUM_ACCOUNT_AGE_NEW_BULLETIN ) ) {
				$_SESSION['notice'] = 'You cannot yet post new bulletins. Your account must be at least ' . MINIMUM_ACCOUNT_AGE_NEW_BULLETIN . ' second' . ( ( MINIMUM_ACCOUNT_AGE_NEW_BULLETIN != 1 ) ? 's' : '' ) . ' old.';
				redirect_to ( '' );
			}
			$bulletin = new Bulletin ( array ( 'body' => $_POST['record']['body'], 'user_ID' => $_SESSION['logged_in_user']['ID'] ) );
			if ( valid_post ( ) ) {
				if ( isset ( $_POST['preview'] ) ) {
					return false;
				}
				// update the (temporary) user account info with their IP address; assists moderation without compromising privacy too much
				$user_account = UserAccount::find ( ( int ) $_SESSION['logged_in_user']['ID'] );
				$_SESSION['logged_in_user']['last_remote_address'] = $_SERVER['REMOTE_ADDR'];
				// check for timing limits
				if ( USER_LEVEL_EXEMPT_FROM_BULLETIN_POST_TIMING_LIMITS == -1 || ( intval ( USER_LEVEL_EXEMPT_FROM_BULLETIN_POST_TIMING_LIMITS ) > intval ( $_SESSION['logged_in_user']['type'] ) ) ) {
					// post limiting is on and applies to this user
					if ( time() - $_SESSION['logged_in_user']['last_bulletin'] < BULLETIN_TIME_BETWEEN_EACH ) {
						$errors[] = BULLETIN_TIME_BETWEEN_EACH . ' second' . ( ( BULLETIN_TIME_BETWEEN_EACH != 1 ) ? 's' : '' ) . ' are required between each new topic.';
						header ( 'Status: 409', TRUE, 409 );
						return false;
					}
				}
				// publish
				if ( $bulletin->validates ( ) ) {
					if ( BULLETINS_GO_LIVE_WHEN_POSTED == 1 || $_SESSION['logged_in_user']['type'] >= MODERATOR_TYPE ) {
						$bulletin->status = 'published';
					} else if ( BULLETINS_GO_LIVE_WHEN_POSTED == 0 ) {
						$bulletin->status = 'pending approval';
					}
				}
				// save
				if ( $new_ID = $bulletin->create ( ) ) {
					$remote_address = RemoteAddress::find ( array ( 'first', 'conditions' => array ( "`remote_address` = '%s'", $_SERVER['REMOTE_ADDR'] ), 'select' => 'ID, remote_address, topics_count, media_count, permission_to_post' ) );
					if ( count ( $errors ) == 0 ) {
						if ( LOG_ACTIONS == 1 ) {
							$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $new_ID, 'record_table' => Bulletin::table, 'record_class' => 'Bulletin', 'description' => 'created a bulletin', 'action' => 'insert' ) );
							$action_log->create ( );
						}
						$_SESSION['logged_in_user']['last_bulletin'] = time();
						$_SESSION['logged_in_user']['last_remote_address'] = $_SERVER['REMOTE_ADDR'];
						$remote_address->update_counter ( 'bulletins_count', 1 );
						$user_account->update_counter ( 'bulletins_count', 1 );
						redirect_to ( "bulletins", 303 );
					}
				}
				header ( 'Status: 400', TRUE, 400 );
			}
		} else {
			$_SESSION['notice'] = "Bulletin posting is currently disabled.";
			redirect_to ( 'bulletins' );
		}
	break;

	default:
		set_error ( 404 );
	break;
}

?>