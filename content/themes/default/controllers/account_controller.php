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

global $controller, $action, $ID, $fragment, $extra, $page_title, $original_URI;

$ensure_login_except = array ( 'index', 'new', 'login', 'logout', 'username_check', 'password_reset' );
if ( ! in_array ( $action, $ensure_login_except ) ) { ensure_login ( 0 ); }

switch ( $action ) {
	case 'index':
		$page_title = 'Account';
		if ( isset ( $_SESSION['logged_in_user'] ) ) {
			redirect_to ( 'account/info' );
		}
	break;

	case 'info':
		global $user_account;
		$page_title = 'Account';
		$user_account = UserAccount::find( ( int ) $_SESSION['logged_in_user']['ID'] );
		check_unread_private_messages_count ( );
	break;

	case 'edit':
		global $errors;
		$page_title = 'Edit account information';
		Setting::load ( array ( 'conditions' => "name = 'SEND_EMAILS'", 'select' => 'category, ' . MIN_SETTINGS_FIELDS ) );
		if ( valid_post ( ) ) {
			$errors_count = count ( $errors );
			// this action only allows you to edit the currently logged in user
			$user_account = UserAccount::find ( ( int ) $_SESSION['logged_in_user']['ID'] );
			$_SESSION['notice'] = '';
			if ( isset ( $_POST['username_new'] ) && ! empty ( $_POST['username_new'] ) ) {
				if ( isset ( $_POST['username_password_confirm'] ) && ! empty ( $_POST['username_password_confirm'] ) ) {
					// check if password matches
					if ( _APPCONFIG::salted_sha1 ( $_POST['username_password_confirm'] ) == $user_account->password ) {
						if ( ! UserAccount::find ( array ( 'first', 'conditions' => array ( "username = '%s'", $_POST['username_new'] ), 'select' => 'username, ID' ) ) ) {
							$user_account->username = $_POST['username_new'];
							if ( $user_account->validates ( ) ) {
								$user_account->update ( 'username' );
								$_SESSION['logged_in_user']['username'] = $_POST['username_new'];
								$_SESSION['notice'] .= ' Username changed.';
							}
						} else {
							$errors[] = 'Your username must be unique; “' . htmlspecialchars ( $_POST['username_new'] ) . '” is taken.';
						}
					} else {
						$errors[] = 'Username not changed; the password you entered did not match the password on file. This is a security measure to protect your account.';
					}
				} else {
					$errors[] = 'Username not changed; your current account password was not entered. This is a security measure to protect your account.';
				}
			}
			if ( isset ( $_POST['password_new'] ) && ! empty ( $_POST['password_new'] ) ) {
				if ( isset ( $_POST['password_current'] ) && ! empty ( $_POST['password_current'] ) ) {
					// check if password matches
					if ( _APPCONFIG::salted_sha1 ( $_POST['password_current'] ) == $user_account->password ) {
						if ( isset ( $_POST['password_new_confirm'] ) && ! empty ( $_POST['password_new_confirm'] ) ) {
							if ( $_POST['password_new'] == $_POST['password_new_confirm'] ) {
								$user_account->password = $_POST['password_new'];
								if ( $user_account->validates ( ) ) {
									$user_account->update ( 'password' );
									$_SESSION['notice'] .= ' Password changed.';
								}
							} else {
								$errors[] = 'Password not changed; the new password you entered did not match. Please type the new password twice to confirm.';
							}
						} else {
							$errors[] = 'Password not changed; please type the password twice to confirm. This is a security measure to protect your account.';
						}
					} else {
						$errors[] = 'Password not changed; the password you entered did not match the password on file. This is a security measure to protect your account.';
					}
				} else {
					$errors[] = 'Password not changed; your current account password was not entered. This is a security measure to protect your account.';
				}
			}
			if ( isset ( $_POST['email_address_new'] ) && ! empty ( $_POST['email_address_new'] ) ) {
				if ( _APPCONFIG::salted_sha1 ( $_POST['email_address_password_confirm'] ) == $user_account->password ) {
					// check if password matches
					if ( ! isset ( $user_account->email_address ) || empty ( $user_account->email_address ) ) {
						$user_account->email_address = $_POST['email_address_new'];
						$user_account->update ( 'email_address' );
						$_SESSION['notice'] .= ' Email address changed.';
					} else {
						$_SESSION['notice'] .= ' TODO: send email to confirm.';
					}
				} else {
					$errors[] = 'Email address not changed; your current account password was not entered. This is a security measure to protect your account.';
				}
			}
			if ( $errors_count == count ( $errors ) ) {
				if ( LOG_ACTIONS == 1 ) {
					$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $user_account->ID ( ), 'record_ID' => $user_account->ID ( ), 'record_table' => UserAccount::table, 'record_class' => get_class ( $user_account ), 'description' => 'edited account', 'action' => 'update' ) );
					$action_log->create ( );
				}
				unset ( $_SESSION['temp_password'] );
				// no new errors; redirect!
				redirect_to ( 'account/info' );
			}
			if ( LOG_ACTIONS == 1 ) {
				$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $user_account->ID ( ), 'record_ID' => $user_account->ID ( ), 'record_table' => UserAccount::table, 'record_class' => get_class ( $user_account ), 'description' => 'failed to edit account', 'action' => 'update' ) );
				$action_log->create ( );
			}
		}
		# allow email address inputting/changing, but require confirmation through old email address first
	break;

	case 'new':
		if ( valid_post ( ) ) {
			$succesful = 0;
			if ( isset ( $_POST['password_new'] ) && ! empty ( $_POST['password_new'] ) && isset ( $_POST['password_verify'] ) && ! empty ( $_POST['password_verify'] ) ) {
				if ( $_POST['password_new'] == $_POST['password_verify'] ) {
					$remote_address = RemoteAddress::find ( array ( 'first', 'conditions' => array ( "`remote_address` = '%s'", $_SERVER['REMOTE_ADDR'] ) ) );
					if ( $remote_address->permission_to_register == 1 ) {
						$user_account = new UserAccount ( array ( 'password' => $_POST['password_new'], 'status' => 'new', 'username' => strtolower ( $_POST['username'] ) ) );
						if ( $new_ID = $user_account->create ( ) ) {
							$user_account = UserAccount::find ( $new_ID );
							$user_account->status = 'active';
							$user_account->update ( 'status' );
							$remote_address->update_counter ( 'users_count', 1 );
							$_SESSION['notice'] = 'Account created!';
							$_SESSION['logged_in_user'] = array ( );
							$_SESSION['logged_in_user']['ID'] = $user_account->ID ( );
							$_SESSION['logged_in_user']['username'] = $user_account->username ( );
							$_SESSION['logged_in_user']['remote_address'] = $_SERVER['REMOTE_ADDR'];
							$_SESSION['logged_in_user']['type'] = $user_account->type ( );
							if ( LOG_ACTIONS == 1 ) {
								$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $user_account->ID ( ), 'record_ID' => $user_account->ID ( ), 'record_table' => UserAccount::table, 'record_class' => get_class ( $user_account ), 'description' => 'created a new account', 'action' => 'insert' ) );
								$action_log->create ( );
							}
							$succesful = 1;
							redirect_to ( 'account/welcome' );
						} else {
							if ( UserAccount::find ( array ( 'first', 'conditions' => array ( "username = '%s'", strtolower ( $_POST['username'] ) ), 'select' => 'username' ) ) ) {
								$_SESSION['notice'] = 'This username already exists. Your username must be unique.';
							} else {
								$_SESSION['notice'] = 'An error occured while creating your account.';
							}
						}
					} else {
						$_SESSION['notice'] = 'Your IP address is not allowed to create new accounts.';
					}
				} else {
					$_SESSION['notice'] = 'The passwords you entered did not match.';
				}
			} else {
				$_SESSION['notice'] = 'Please enter your password in both fields to confirm.';
			}
			if ( $successful == 0 ) {
				if ( LOG_ACTIONS == 1 ) {
					$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => 0, 'record_ID' => 0, 'record_table' => UserAccount::table, 'record_class' => 'UserAccount', 'description' => 'tried to create a new account', 'action' => 'insert' ) );
					$action_log->create ( );
				}
				redirect_to ( 'account' );
			}
		} else {
			redirect_to ( 'account' );
		}
	break;

	case 'login':
		if ( valid_post ( ) ) {
			// username is case-insensitive
			if ( isset ( $_POST['password'] ) && ! empty ( $_POST['password'] ) && isset ( $_POST['username'] ) && ! empty ( $_POST['username'] ) ) {
				global $logged_in_user;
				$logged_in_user = UserAccount::find ( array ( 'first', 'conditions' => array ( "`username` = '%s' AND `password` = '%s'", strtolower ( $_POST['username'] ), _APPCONFIG::salted_sha1 ( $_POST['password'] ) ), 'select' => 'ID, username, password, type, status, ban_expires, ban_reason' ) );
			}
			if ( ! isset ( $logged_in_user ) ) {
				// no match on this user/pass combo
				$_SESSION['notice'] = 'User ID or password were incorrect.';
				if ( LOG_ACTIONS == 1 ) {
					$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => 0, 'record_ID' => 0, 'record_table' => UserAccount::table, 'record_class' => 'UserAccount', 'description' => 'failed to log in', 'action' => 'select' ) );
					$action_log->create ( );
				}
				redirect_to ( 'account' );
			} else {
				// push $logged_in_user vars to the session
				set_session_user_vars ( );
				// check $logged_in_user vars in the session against various parameters
				// redirects on failure
				ensure_login ( );
				$_SESSION['notice'] = 'You are logged in.';
				if ( isset ( $_SESSION['redirect_destination'] ) ) {
					// TODO: bugfix? This seems to not work if the session cookies are expired or do not exist; but I am not sure.
					$destination = preg_replace ( "/^" . str_replace ( '/', '\/', BASE_URI ) . "/", '', $_SESSION['redirect_destination'] );
					unset ( $_SESSION['redirect_destination'] );
					redirect_to ( $destination );
				} else {
					redirect_to ( 'account/info' );
				}
			}
		}
	break;

	case 'logout':
		if ( LOG_ACTIONS == 1 ) {
			$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $_SESSION['logged_in_user']['ID'], 'record_table' => UserAccount::table, 'record_class' => 'UserAccount', 'description' => 'logged out', 'action' => 'delete' ) );
			$action_log->create ( );
		}
		unset ( $_SESSION['logged_in_user'] );
		$_SESSION['notice'] = 'You are logged out.';
		redirect_to ( '' );
	break;

	case 'password_reset':
		global $errors, $user_account;
		$page_title = 'Password reset';
		if ( valid_post ( ) ) {
			if ( isset ( $_POST['recovery_string'] ) ) {
				Setting::load ( array ( 'conditions' => "name = 'SEND_EMAILS'", 'select' => 'category, ' . MIN_SETTINGS_FIELDS ) );
				// find user by email address
				$user_account = UserAccount::find ( array ( 'first', 'conditions' => array ( "`email_address` IS NOT NULL AND `email_address` = '%s'", $_POST['recovery_string'] ) ) );
				if ( ! isset ( $user_account ) ) {
					// find user by case-insensitive username
					$user_account = UserAccount::find ( array ( 'first', 'conditions' => array ( "LOWER(`username`) = '%s'", strtolower ( $_POST['recovery_string'] ) ) ) );
				}
			} else if ( isset ( $_POST['password'] ) && $_POST['password'] != 'automatic' ) {
				global $logged_in_user;
				$reset_recommended = 0;
				$logged_in_user = UserAccount::find ( array ( 'first', 'conditions' => array ( "`username` = '%s' AND `password` = '%s'", strtolower ( $_POST['username'] ), _APPCONFIG::salted_sha1 ( $_POST['password'] ) ), 'select' => 'ID, username, password, type, status, ban_expires, ban_reason' ) );
				if ( ! $logged_in_user ) {
					if ( $logged_in_user = UserAccount::find ( array ( 'first', 'conditions' => array ( "`username` = '%s' AND `temp_password` = '%s' AND `status` = 'active' AND `email_address` IS NOT NULL AND `ban_expires` IS NULL", $_POST['username'], $_POST['password'] ), 'select' => 'ID, username, password, type, status, ban_expires, ban_reason' ) ) ) {
						$reset_recommended = 1;
						$logged_in_user->password = $_POST['password'];
						$logged_in_user->update ( 'password' );
						$logged_in_user->temp_password = 'NULL';
						$logged_in_user->update ( 'temp_password' );
					}
				}
				if ( isset ( $logged_in_user ) ) {
					set_session_user_vars ( );
					if ( $reset_recommended == 1 ) {
						$_SESSION['notice'] = 'You can now reset your password.';
						$_SESSION['temp_password'] = $_POST['password'];
						redirect_to ( 'account/edit/#change_password' );
					} else {
						$_SESSION['notice'] = 'You are now logged in.';
						redirect_to ( 'account/info' );
					}
				} else {
					$errors[] = 'Username or temporary password were incorrect.';
				}
			}
			if ( isset ( $user_account ) ) {
				$user_account->temp_password = random_password ( 20 );
				ob_start ( );
				IMGBOARD_render_view ( 'mailer/password_reset' );
				$my_body = ob_get_contents ( );
				ob_end_clean ( );
				$email = new Mailer ( array ( 'to' => $user_account->email_address, 'from' => 'noreply@' . DOMAIN, 'body' => $my_body, 'subject' => '[' . DOMAIN . '] Password reset for ' . $user_account->username ) );
				if ( $email->send ( ) && count ( $errors ) == $error_count ) {
					if ( LOG_ACTIONS == 1 ) {
						$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $_SESSION['logged_in_user']['ID'], 'record_table' => UserAccount::table, 'record_class' => 'UserAccount', 'description' => 'reset password', 'action' => 'update' ) );
						$action_log->create ( );
					}
					$user_account->update ( 'temp_password' );
					$_SESSION['notice'] = 'Email sent; password reset instructions are enclosed.';
					redirect_to ( 'account' );
				}
				if ( LOG_ACTIONS == 1 ) {
					$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $_SESSION['logged_in_user']['ID'], 'record_table' => UserAccount::table, 'record_class' => 'UserAccount', 'description' => 'failed to reset password', 'action' => 'update' ) );
					$action_log->create ( );
				}
			}
		}
	break;

	case 'settings':
		global $settings;
		$page_title = 'Settings';
		$settings = Setting::find ( array ( 'all', 'conditions' => "category = 'USER_SETTINGS'", 'order' => 'order_by ASC' ) );
		check_unread_private_messages_count ( );
	break;

	case 'username_check':
		global $layout, $DB;
		$layout = 0;
		$result = array ( );
		if ( isset ( $_POST['username'] ) && ! empty ( $_POST['username'] ) ) {
			$my_result = $DB->query ( "SELECT username FROM `user_accounts` WHERE LOWER(username) = '" . $DB->real_escape_string ( strtolower ( ( string ) $_POST['username'] ) ) . "' LIMIT 1" );
			if ( $my_result->num_rows == 1 ) {
				$result['result'] = 0; // bool false
			} else {
				$result['result'] = 1; // bool true
			}
		}
		header ( "Content-Type: application/json" );
		echo json_encode ( $result );
	break;

	case 'welcome':
		$page_title = 'Welcome.';
	break;

	default:
		set_error ( 404 );
	break;
}

function set_session_user_vars ( ) {
	global $logged_in_user;
	$_SESSION['logged_in_user'] = array ( );
	$_SESSION['logged_in_user']['ID'] = $logged_in_user->ID ( );
	$_SESSION['logged_in_user']['username'] = $logged_in_user->username ( );
	$_SESSION['logged_in_user']['type'] = $logged_in_user->type ( );
	$_SESSION['logged_in_user']['status'] = $logged_in_user->status;
	if ( LOG_ACTIONS == 1 ) {
		$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $logged_in_user->ID ( ), 'record_ID' => $logged_in_user->ID ( ), 'record_table' => UserAccount::table, 'record_class' => 'UserAccount', 'description' => 'logged in', 'action' => 'select' ) );
		$action_log->create ( );
	}
}

?>