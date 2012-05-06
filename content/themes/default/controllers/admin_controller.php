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

global $controller, $action, $ID, $fragment, $extra, $page_title, $original_URI, $needs_UI;

$ensure_login_except = array ( );
if ( ! in_array ( $action, $ensure_login_except ) ) { ensure_login ( MODERATOR_TYPE, 'account' ); }

switch ( $action ) {
	case 'index':
		$page_title = 'Admin';
		if ( isset ( $_SESSION['logged_in_user'] ) ) {
			redirect_to ( 'admin/intro' );
		}
	break;

	case 'intro':
		$page_title = 'Admin';
	break;

	case 'app_settings':
		global $settings;
		$page_title = 'Admin: settings';
		if ( ! defined ( 'SETTINGS_SORTABLE' ) ) {
			$SETTINGS_SORTABLE = Setting::find ( array ( 'first', 'conditions' => "name = 'SETTINGS_SORTABLE'" ) );
			define ( 'SETTINGS_SORTABLE', $SETTINGS_SORTABLE->value );
		}
		if ( valid_post ( ) && verify_permissions ( ADMIN_TYPE ) ) {
			$success = true;
			if ( $_POST['mode'] == 'add' ) {
				$setting = new Setting ( $_POST['setting'] );
				if ( $setting->save ( ) ) {
					$_SESSION['notice'] = "Setting added.";
				} else {
					$success = false;
				}
			} else if ( $_POST['mode'] == 'update' ) {
				// default message // TODO: fix
				$_SESSION['notice'] = "No settings changed.";
				foreach ( $_POST['settings'] as $k => $v ) {
					$setting = new Setting ( array ( 'ID' => $k, 'value' => $v['value'] ) );
					if ( SETTINGS_SORTABLE == 1 ) {
						$setting->order_by = $v['order_by'];
					}
					if ( $setting->save ( ) ) {
						$_SESSION['notice'] = "Settings updated.";
					} else {
						$success = false;
					}
				}
			}
			if ( $success == true ) {
				if ( LOG_ACTIONS == 1 ) {
					if ( $_POST['mode'] == 'add' ) {
						$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $setting->ID ( ), 'record_table' => Setting::table, 'record_class' => 'Setting', 'description' => 'added a new application setting', 'action' => 'insert' ) );
					} else {
						$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => 0, 'record_table' => Setting::table, 'record_class' => 'Setting', 'description' => 'changed application settings', 'action' => 'update' ) );
					}
					$action_log->create ( );
				}
				redirect_to ( 'admin/app_settings' );
			}
			if ( LOG_ACTIONS == 1 ) {
				if ( $_POST['mode'] == 'add' ) {
					$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => 0, 'record_table' => Setting::table, 'record_class' => 'Setting', 'description' => 'failed to add a new application setting', 'action' => 'insert' ) );
				} else {
					$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => 0, 'record_table' => Setting::table, 'record_class' => 'Setting', 'description' => 'failed to change application settings', 'action' => 'update' ) );
				}
				$action_log->create ( );
			}
		}
		$settings = Setting::find( array ( 'order' => 'category ASC, order_by ASC, name ASC' ) );
	break;

	case 'bulletins':
		global $bulletins, $DB, $queries, $TRACKER, $bulletins_count, $page, $page_count, $records_per_page;
		$page_title = 'Bulletins';
		Setting::load ( array ( 'conditions' => "category = 'BULLETINS'", 'select' => 'category, ' . MIN_SETTINGS_FIELDS ) );
		$records_per_page = ( int ) 40;
		$page = 1;
		if ( isset ( $ID ) && intval ( $ID ) > 1 ) {
			$page = ( int ) $ID;
		}
		$offset = ( $page - 1 ) * $records_per_page;
		if ( isset ( $fragment ) && $fragment == 'user' && isset ( $extra ) && intval ( $extra ) > 0 ) {
			$bulletins = Bulletin::find ( array ( 'conditions' => array ( "user_ID = '%u'", $extra ), 'order' => 'ID desc', 'limit' => "$offset, $records_per_page" ) );
			$queries[] = sprintf ( "SELECT count(ID) as count FROM `bulletins` WHERE `user_ID` = '%u'", $extra );
			$page_title = sprintf ( "Bulletins from user #%u", $extra );
		} else {
			$bulletins = Bulletin::find ( array ( 'order' => 'ID desc', 'limit' => "$offset, $records_per_page" ) );
			$queries[] = "SELECT count(ID) as count FROM `bulletins`";
		}
		if ( $result =  $DB->query ( end ( $queries ) ) ) {
			$TRACKER['queries_select']++;
			$media_count = $result->fetch_object ( );
			$result->free_result ( );
		}
		$page_count = ceil ( $media_count->count / $records_per_page );
	break;

	case 'categories':
		global $categories;
		$page_title = 'Categories';
		$categories = Category::find ( array ( 'order' => 'ID asc' ) );
	break;

	case 'media':
		global $media, $DB, $queries, $TRACKER, $media_count, $page, $page_count, $records_per_page, $results;
		$page_title = 'Media';
		Setting::load ( array ( 'conditions' => "category = 'POSTING' OR category = 'MEDIA' OR category = 'MODERATION'", 'select' => 'category, ' . MIN_SETTINGS_FIELDS ) );
		$records_per_page = ( int ) 40;
		$page = 1;
		if ( isset ( $ID ) && intval ( $ID ) > 1 ) {
			$page = ( int ) $ID;
		}
		$offset = ( $page - 1 ) * $records_per_page;
/*
		$results = array ( );
// this is almost correct, but it returns all replies with duplicate media_ID
		$queries[] = "SELECT replies.media_ID, media.* FROM `replies` LEFT JOIN media ON replies.media_ID = media.ID WHERE replies.media_ID IS NOT NULL ORDER BY media.ID DESC";
// get user info?
//		$queries[] = "SELECT media.*, user_accounts.type as user_type FROM `media` LEFT JOIN user_accounts ON media.user_ID = user_accounts.ID ORDER BY media.ID DESC";
		$result = $DB->query ( end ( $queries ) );
		while ( $row = $result->fetch_object ( ) ) {
			$results[] = $row;
		}
		$result->free_result ( );
*/
		if ( isset ( $fragment ) && $fragment == 'user' && isset ( $extra ) && intval ( $extra ) > 0 ) {
			$media = Media::find ( array ( 'conditions' => array ( "user_ID = '%u'", $extra ), 'order' => 'ID desc', 'limit' => "$offset, $records_per_page" ) );
			$queries[] = sprintf ( "SELECT count(ID) as count FROM `media` WHERE `user_ID` = '%u'", $extra );
			$page_title = sprintf ( "Media from user #%u", $extra );
		} else {
			$media = Media::find ( array ( 'order' => 'ID desc', 'limit' => "$offset, $records_per_page" ) );
			$queries[] = "SELECT count(ID) as count FROM `media`";
		}
		if ( $result =  $DB->query ( end ( $queries ) ) ) {
			$TRACKER['queries_select']++;
			$media_count = $result->fetch_object ( );
			$result->free_result ( );
		}
		$page_count = ceil ( $media_count->count / $records_per_page );
	break;

	case 'moderation':
		global $DB, $queries, $TRACKER, $counts;
		$page_title = 'Moderation';
		$thirty_minutes = gmdate ( MYSQL_DATETIME_FORMAT, time() - 1800 );
		$ninety_minutes = gmdate ( MYSQL_DATETIME_FORMAT, time() - 5400 );
		$my_tables = array ( 'replies', 'media', 'topics', 'bulletins' );
		foreach ( $my_tables as $table ) {
			// 30
			$queries[] = "SELECT count(ID) as count FROM `" . $DB->real_escape_string ( $table ) . "` WHERE `created_at` > '" . $DB->real_escape_string ( $thirty_minutes ) . "' AND ( `status` = 'published' OR `status` = 'pending approval' )";
			$result = $DB->query ( end ( $queries ) );
			$TRACKER['queries_select']++;
			$my_count = $result->fetch_object ( );
			$counts["$table"]['30']['count'] = ( int ) $my_count->count;
			$result->free_result ( );
			// 240
			$queries[] = "SELECT count(ID) as count FROM `" . $DB->real_escape_string ( $table ) . "` WHERE `created_at` > '" . $DB->real_escape_string ( $ninety_minutes ) . "' AND ( `status` = 'published' OR `status` = 'pending approval' )";
			$result = $DB->query ( end ( $queries ) );
			$TRACKER['queries_select']++;
			$my_count = $result->fetch_object ( );
			$counts["$table"]['90']['count'] = ( int ) $my_count->count;
			$result->free_result ( );
		}
		// count reports
		$queries[] = "SELECT count(*) as count FROM `replies_reports` WHERE `created_at` > '" . $DB->real_escape_string ( $thirty_minutes ) . "' AND `status` = 'new'";
		$result = $DB->query ( end ( $queries ) );
		$TRACKER['queries_select']++;
		$my_count = $result->fetch_object ( );
		$counts['replies']['30']['reports'] = ( int ) $my_count->count;
		$queries[] = "SELECT count(*) as count FROM `replies_reports` WHERE `created_at` > '" . $DB->real_escape_string ( $ninety_minutes ) . "' AND `status` = 'new'";
		$result = $DB->query ( end ( $queries ) );
		$TRACKER['queries_select']++;
		$my_count = $result->fetch_object ( );
		$counts['replies']['90']['reports'] = ( int ) $my_count->count;
		$queries[] = "SELECT count(*) as count FROM `topics_reports` WHERE `created_at` > '" . $DB->real_escape_string ( $thirty_minutes ) . "' AND `status` = 'new'";
		$result = $DB->query ( end ( $queries ) );
		$TRACKER['queries_select']++;
		$my_count = $result->fetch_object ( );
		$counts['topics']['30']['reports'] = ( int ) $my_count->count;
		$queries[] = "SELECT count(*) as count FROM `topics_reports` WHERE `created_at` > '" . $DB->real_escape_string ( $ninety_minutes ) . "' AND `status` = 'new'";
		$result = $DB->query ( end ( $queries ) );
		$TRACKER['queries_select']++;
		$my_count = $result->fetch_object ( );
		$counts['topics']['90']['reports'] = ( int ) $my_count->count;
	break;

	case 'mod_action':
		global $layout, $DB, $TRACKER, $queries;
		$layout = 0;
		if ( valid_post ( ) ) {
			$result = array ( );
			if ( $_POST['record_class'] == 'Topic' ) {
				$record = Topic::find( ( int ) $_POST['record_ID'] );
			} else if ( $_POST['record_class'] == 'Reply' ) {
				$record = Reply::find( ( int ) $_POST['record_ID'] );
			} else if ( $_POST['record_class'] == 'Media' ) {
				$record = Media::find( ( int ) $_POST['record_ID'] );
			}
			$result['record_table'] = $record->table ( );
			if ( isset ( $record ) && $record->ID ( ) > 0 ) {
				$original_status = $record->status;
				if ( $_POST['record_action'] == 'delete' && $_POST['record_class'] != 'media' ) {
					$result['record_action'] = 'deleted';
					$record->status = 'deleted';
				} else if ( ( $_POST['record_action'] == 'illegal content' || $_POST['record_action'] == 'adult content' || $_POST['record_action'] == 'rule violation' ) && $_POST['record_class'] == 'media' ) {
					$result['record_action'] = 'deleted';
					$record->status = $_POST['record_action'];
				}
				$user_account = UserAccount::find( ( int ) $record->user_ID ( ) );
				if ( ( $user_account->type < $_SESSION['logged_in_user']['type'] || $user_account->ID ( ) == $_SESSION['logged_in_user']['ID'] ) && ( $record->status != $original_status && $record->update ( 'status' ) ) ) {
					$result['action_result'] = 1;
					if ( get_class ( $record ) == 'Reply' ) {
						if ( $_POST['record_action'] == 'delete' ) {
							$topic = Topic::find( ( int ) $record->topic_ID ( ) );
							$topic->update_counter ( 'replies_count', -1 );
							$user_account->update_counter ( 'replies_count', -1 );
							if ( isset ( $record->media_ID ) && $record->media_ID ( ) > 0 ) {
								$topic->update_counter ( 'media_count', -1 );
								$user_account->update_counter ( 'media_count', -1 );
							}
						}
					} else if ( get_class ( $record ) == 'Topic' ) {
						if ( $_POST['record_action'] == 'delete' ) {
							// topics counter
							$user_account->update_counter ( 'topics_count', -1 );
							// topics favorited counter
							$queries[] = "SELECT COUNT(ID) as count FROM `topics_favorites` WHERE `topic_ID` = '" . $DB->real_escape_string ( $record->ID ( ) ) . "' AND `user_ID` = '" . $DB->real_escape_string ( $record->user_ID ( ) ) . "'";
							$results = $DB->query ( end ( $queries ) );
							$TRACKER['queries_select']++;
							if ( $row = $results->fetch_object ( ) ) {
								if ( $row->count > 0 ) {
									$this_user->update_counter ( 'topics_favorited_count', ( $row->count * -1 ) );
								}
								$results->close ( );
							}
							// cascade delete replies
							$replies = Reply::find ( array ( 'conditions' => array ( "`topic_ID` = '%u' AND `status` = 'published'", $record->ID ( ) ), 'select' => 'ID, status, user_ID' ) );
							if ( isset ( $replies ) && count ( $replies ) > 0 ) {
								foreach ( $replies as $reply ) {
									$this_user = UserAccount::find ( ( int ) $reply->user_ID ( ) );
									$this_user->update_counter ( 'replies_count', -1 );
									$reply->status = 'topic deleted';
									$reply->update ( 'status' );
									$queries[] = "SELECT COUNT(ID) as count FROM `replies_favorites` WHERE `reply_ID` = '" . $DB->real_escape_string ( $reply->ID ( ) ) . "' AND `user_ID` = '" . $DB->real_escape_string ( $reply->user_ID ( ) ) . "'";
									$results = $DB->query ( end ( $queries ) );
									$TRACKER['queries_select']++;
									if ( $row = $results->fetch_object ( ) ) {
										if ( $row->count > 0 ) {
											$this_user->update_counter ( 'replies_favorited_count', ( $row->count * -1 ) );
										}
										$results->close ( );
									}
								}
							}
							unset ( $replies );
						}
					} else if ( get_class ( $record ) == 'Media' ) {
						// alter related records
						if ( $record->status == 'rule violation' ) {
							if ( isset ( $_POST['related_record_class'] ) ) { 
								if ( $_POST['related_record_class'] == 'Topic' ) { 
									$related = Topic::find ( ( int ) $_POST['related_record_ID'] );
								} else if ( $_POST['related_record_class'] == 'Reply' ) {
									$related = Reply::find ( ( int ) $_POST['related_record_ID'] );
								}
							} else {
								$related = Topic::find ( array ( 'first', 'conditions' => array ( "media_ID = '%u'", $record->ID ( ) ), 'order' => 'ID DESC' ) );
								if ( ! isset ( $related ) ) {
									$related = Reply::find ( array ( 'first', 'conditions' => array ( "media_ID = '%u'", $record->ID ( ) ), 'order' => 'ID DESC' ) );
								}
								if ( ! isset ( $related ) ) {
									$result['action_result'] = 0;
								}
							}
							if ( isset ( $related ) ) {
								$this_user = UserAccount::find ( ( int ) $related->userID ( ) );
								if ( ! isset ( $reply->body ) || empty ( $reply->body ) ) {
									// if image-only post, set to delete
									if ( get_class ( $related ) == 'Topic' ) {
										$this_user->update_counter ( 'topics_count', -1 );
									} else if ( get_class ( $related ) == 'Reply' ) {
										$this_user->update_counter ( 'replies_count', -1 );
									}
									$related->status = 'deleted';
									$related->update ( 'status' );
								} else {
									// if post has text, delete only image
									$related->media_ID = 'NULL';
									$related->update ( 'media_ID' );
									$this_user->update_counter ( 'media_count', -1 );
									$related->partial_cache = '';
									$related->update ( 'partial_cache' );
								}
								$user_account = UserAccount::find ( ( int ) $related->user_ID ( ) );
								$user_account->update_counter ( 'media_count', -1 );
							}
						} else {
							if ( $_POST['related_record_class'] == 'Topic' ) {
								// if a topic
								$topics = Topic::find( array ( 'conditions' => array ( "`media_ID` = '%u'", $record->ID ( ) ), 'select' => 'ID, media_ID, status' ) );
								if ( isset ( $topics ) && count ( $topics ) > 0 ) {
									foreach ( $topics as $topic ) {
										$this_user = UserAccount::find ( ( int ) $reply->user_ID );
										if ( ! isset ( $reply->body ) || empty ( $reply->body ) ) {
											// if image-only post, set topic to delete
											$this_user->update_counter ( 'topics_count', -1 );
											$topic->status = 'deleted';
											$topic->update ( 'status' );
										} else {
											// if post has text, delete only image
											$topic->media_ID = 'NULL';
											$topic->update ( 'media_ID' );
											$topic->partial_cache = '';
											$topic->update ( 'partial_cache' );
										}
										$this_user->update_counter ( 'media_count', -1 );
										$topic->update_counter ( 'media_count', -1 );
									}
								}
							} else if ( $_POST['related_record_class'] == 'Reply' ) {
								// if a reply
								$replies = Reply::find( array ( 'conditions' => array ( "`media_ID` = '%u'", $record->ID ( ) ), 'select' => 'ID, media_ID, status, topic_ID' ) );
								if ( isset ( $replies ) && count ( $replies ) > 0 ) {
									foreach ( $replies as $reply ) {
										$this_user = UserAccount::find ( ( int ) $reply->user_ID );
										$topic = Topic::find( ( int ) $reply->topic_ID ( ) );
										if ( ! isset ( $reply->body ) || empty ( $reply->body ) ) {
											// if image-only post, set reply to delete
											$reply->status = 'deleted';
											$this_user->update_counter ( 'replies_count', -1 );
											$reply->update ( 'status' );
											$topic->update_counter ( 'replies_count', -1 );
										} else {
											// if post has text, delete only image
											$reply->media_ID = 'NULL';
											$reply->update ( 'media_ID' );
											$reply->partial_cache = '';
											$reply->update ( 'partial_cache' );
										}
										$this_user->update_counter ( 'media_count', -1 );
										$topic->update_counter ( 'media_count', -1 );
									}
								}
							}
							// delete the media file(s)
							$record->delete_my_files ( );
						}
					}
				} else {
					$result['action_result'] = 0;
				}
			}
			if ( LOG_ACTIONS == 1 ) {
				if ( $result['action_result'] == 0 ) {
					$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $_POST['record_ID'], 'record_table' => $result['record_table'], 'record_class' => $_POST['record_class'], 'description' => 'failed to ' . $_POST['record_action'] . ' a ' . strtolower ( $_POST['record_class'] ), 'action' => 'update' ) );
				} else {
					$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $_POST['record_ID'], 'record_table' => $result['record_table'], 'record_class' => $_POST['record_class'], 'description' => $result['record_action'] . ' a ' . strtolower ( $_POST['record_class'] ), 'action' => 'update' ) );
				}
				$action_log->create ( );
			}
			header ( "Content-Type: application/json" );
			echo json_encode ( $result );
		}
	break;

	case 'mod_remote_address':
		// AJAX
		global $layout, $DB, $TRACKER, $queries;
		$layout = 0;
		// only admins can do this, for now
		if ( valid_post ( ) && verify_permissions ( ADMIN_TYPE ) ) {
			$result = array ( );
			$result['action_result'] = 0;
			if ( $remote_address = RemoteAddress::find( ( int ) $_POST['record_ID'] ) ) {
				if ( $_POST['record_action'] == 'view' ) {
					$remote_address->permission_to_view = ( ( $remote_address->permission_to_view == 1 ) ? 0 : 1 );
					$remote_address->update ( 'permission_to_view' );
					$result['new_status'] = $remote_address->permission_to_view;
				} else if ( $_POST['record_action'] == 'register' ) {
					$remote_address->permission_to_register = ( ( $remote_address->permission_to_register == 1 ) ? 0 : 1 );
					$remote_address->update ( 'permission_to_register' );
					$result['new_status'] = $remote_address->permission_to_register;
				} else if ( $_POST['record_action'] == 'post' ) {
					$remote_address->permission_to_post = ( ( $remote_address->permission_to_post == 1 ) ? 0 : 1 );
					$remote_address->update ( 'permission_to_post' );
					$result['new_status'] = $remote_address->permission_to_post;
				} else if ( $_POST['record_action'] == 'search' ) {
					$remote_address->permission_to_search = ( ( $remote_address->permission_to_search == 1 ) ? 0 : 1 );
					$remote_address->update ( 'permission_to_search' );
					$result['new_status'] = $remote_address->permission_to_search;
				}
				$result['action_result'] = 1;
			}
			if ( LOG_ACTIONS == 1 ) {
				if ( $result['new_status'] == 1 ) {
					// unbanned
					$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $remote_address->ID ( ), 'record_table' => RemoteAddress::table, 'record_class' => 'RemoteAddress', 'description' => 'unbanned a remote address', 'action' => 'update' ) );
				} else if ( $result['new_status'] == 0 ) {
					$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $remote_address->ID ( ), 'record_table' => RemoteAddress::table, 'record_class' => 'RemoteAddress', 'description' => 'banned a remote address', 'action' => 'update' ) );
				}
				$action_log->create ( );
			}
			header ( "Content-Type: application/json" );
			echo json_encode ( $result );
		}
	break;

	case 'page_edit':
		global $page;
		$page_title = 'User info';
		$needs_ui = 1;
		if ( isset ( $ID ) ) {
			$page = Page::find ( ( int ) $ID );
		}
		if ( valid_post ( ) && verify_permissions ( ADMIN_TYPE ) ) {
			if ( isset ( $page ) ) {
				// TODO: get update working on arrays
				$page->title = $_POST['record']['title'];
				$page->update ( 'title' );
				$page->body = $_POST['record']['body'];
				$page->update ( 'body' );
				$page->status = $_POST['record']['status'];
				$page->update ( 'status' );
				$page->partial_cache = '';
				$page->update ( 'partial_cache' );
				if ( isset ( $_POST['record']['URI'] ) && ! empty ( $_POST['record']['URI'] ) ) {
					$page->URI = $_POST['record']['URI'];
				} else {
					$page->URI = 'NULL';
				}
				$page->update ( 'URI' );
				$_SESSION['notice'] = '<a href="' . $page->URI ( ) . '">Page updated.</a>';
				if ( LOG_ACTIONS == 1 ) {
					if ( $page->status == 'published' ) {
						$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $page->ID ( ), 'record_table' => Page::table, 'record_class' => 'Page', 'description' => 'edited a page' . ( ( $page->URI ( ) ) ? ( ': “<a href="' . $page->URI ( ) . '" title="' . htmlspecialchars ( $page->title ) . '">' . htmlspecialchars ( $page->title ) . '</a>”' ) : ( '' ) ), 'action' => 'update' ) );
					} else {
						$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $page->ID ( ), 'record_table' => Page::table, 'record_class' => 'Page', 'description' => 'edited a page', 'action' => 'update' ) );
					}
					$action_log->create ( );
				}
				redirect_to ( 'admin/pages' );
			} else {
				// creating new
				$page = new Page ( array ( 'title' => $_POST['record']['title'], 'body' => $_POST['record']['body'], 'status' => $_POST['record']['status'] ) );
				if ( isset ( $_POST['record']['URI'] ) && ! empty ( $_POST['record']['URI'] ) ) {
					$page->URI = $_POST['record']['URI'];
				}
				if ( $new_ID = $page->create ( ) ) {
					$page->set_ID ( $new_ID );
					if ( LOG_ACTIONS == 1 ) {
						if ( $page->status == 'published' ) {
							$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $new_ID, 'record_table' => Page::table, 'record_class' => 'Page', 'description' => 'created a new page' . ( ( $page->URI ( ) ) ? ( ': “<a href="' . $page->URI ( ) . '" title="' . htmlspecialchars ( $page->title ) . '">' . htmlspecialchars ( $page->title ) . '</a>”' ) : ( '' ) ), 'action' => 'insert' ) );
						} else {
							$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $new_ID, 'record_table' => Page::table, 'record_class' => 'Page', 'description' => 'created a new page', 'action' => 'insert' ) );
						}
						$action_log->create ( );
					}
					$_SESSION['notice'] = "Page created.";
					if ( $page->status == 'published' ) {
						redirect_to ( 'page/view/' . $new_ID . '/' . $page->title_URI ( ) );
					} else {
						redirect_to ( 'admin/pages' );
					}
				}
				if ( LOG_ACTIONS == 1 ) {
					$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => 0, 'record_table' => Page::table, 'record_class' => 'Page', 'description' => 'failed to create a new page', 'action' => 'insert' ) );
					$action_log->create ( );
				}
			}
		}
		if ( isset ( $page ) ) {
			// editing existing
			$page_title = 'Editing “' . htmlspecialchars ( $page->title ) . '”';
		} else {
			// creating new
			$page_title = 'New page';
		}
	break;

	case 'pages':
		global $pages, $DB, $queries, $TRACKER, $page, $page_count, $pages_count, $records_per_page;
		// lolz, these names are recursive/redundant?
		$page_title = 'Pages';
		$page = 1;
		$offset = 0;
		$records_per_page = ( int ) 50;
		if ( isset ( $ID ) && intval ( $ID ) > 1 ) {
			$page = ( int ) $ID;
		}
		$offset = ( $page - 1 ) * $records_per_page;
		$queries[] = "SELECT count(ID) as count FROM `pages`";
		if ( $result =  $DB->query ( end ( $queries ) ) ) {
			$TRACKER['queries_select']++;
			$pages_count = $result->fetch_object ( );
			$result->free_result ( );
		}
		$page_count = ceil ( $pages_count->count / $records_per_page );
		$pages = Page::find ( array ( 'order' => 'ID DESC', 'limit' => "$offset, $records_per_page" ) );
	break;

	case 'remote_addresses':
		global $remote_addresses, $DB, $queries, $TRACKER, $page, $page_count, $records_count, $records_per_page;
		$page_title = 'Remote addresses';
		$page = 1;
		$offset = 0;
		$records_per_page = ( int ) 100;
		if ( isset ( $ID ) && intval ( $ID ) > 1 ) {
			$page = ( int ) $ID;
		}
		$offset = ( $page - 1 ) * $records_per_page;
		if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
			// validate request
			// redirect to GET, put data in URL
			if ( isset ( $_POST['search']['remote address'] ) && ! empty ( $_POST['search']['remote address'] ) ) {
				if ( ! isset ( $_SESSION['search'] ) ) {
					$_SESSION['search'] = array ( );
				}
				$_SESSION['search']['remote address'] = $_POST['search']['remote address'];
			}
			switch ( $_POST['search']['order'] ) {
				case 'abuse':
					redirect_to ( 'admin/remote_addresses/' . $page . '/abuse' );
				break;
				case 'remote address':
					redirect_to ( 'admin/remote_addresses/' . $page . '/remote_address' );
				break;
				default:
					redirect_to ( 'admin/remote_addresses/' . $page );
				break;
			}
		}
		$queries[] = "SELECT count(ID) as count FROM `remote_addresses`";
		if ( $result =  $DB->query ( end ( $queries ) ) ) {
			$TRACKER['queries_select']++;
			$records_count = $result->fetch_object ( );
			$result->free_result ( );
		}
		$page_count = ceil ( $records_count->count / $records_per_page );
		if ( isset ( $_SESSION['search'] ) && ! empty ( $_SESSION['search']['remote address'] ) ) {
			$my_remote_address_search = $_SESSION['search']['remote address'];
			if ( substr_count ( $_POST['search']['remote address'], '.' ) < 3 ) {
				// search for partial IPv4
				$my_remote_address_search .= '%';
			}
		}
		switch ( $fragment ) {
			case 'abuse':
				if ( isset ( $my_remote_address_search ) && ! empty ( $my_remote_address_search ) ) {
					$remote_addresses = RemoteAddress::find ( array ( 'conditions' => array ( "`permission_to_view` = '1' AND `permission_to_register` = '1' AND `remote_address` LIKE '%s'", $my_remote_address_search ), 'select' => '*, ((`users_count` - `replies_count` - `topics_count` - `media_count`) + (DATEDIFF(UTC_TIMESTAMP(), `first_seen`)/60)) as `activity_count`', 'order' => 'activity_count DESC', 'limit' => "$offset, $records_per_page" ) );
				} else {
					$remote_addresses = RemoteAddress::find ( array ( 'conditions' => "`permission_to_view` = '1' AND `permission_to_register` = '1'", 'select' => '*, ((`users_count` - `replies_count` - `topics_count` - `media_count`) + (DATEDIFF(UTC_TIMESTAMP(), `first_seen`)/60)) as `activity_count`', 'order' => 'activity_count DESC', 'limit' => "$offset, $records_per_page" ) );
				}
			break;
			case 'remote_address':
				if ( isset ( $my_remote_address_search ) && ! empty ( $my_remote_address_search ) ) {
					$remote_addresses = RemoteAddress::find ( array ( 'conditions' => array ( "`remote_address` LIKE '%s'", $my_remote_address_search ), 'order' => 'CAST(SUBSTRING(remote_address,1,3) AS UNSIGNED) DESC, CAST(SUBSTRING(remote_address,4,3) AS UNSIGNED) DESC', 'limit' => "$offset, $records_per_page" ) );
				} else {
					$remote_addresses = RemoteAddress::find ( array ( 'order' => 'CAST(SUBSTRING(remote_address,1,3) AS UNSIGNED) DESC, CAST(SUBSTRING(remote_address,4,3) AS UNSIGNED) DESC', 'limit' => "$offset, $records_per_page" ) );
				}
			break;
			default:
				// unset session
				unset ( $_SESSION['search'] );
				$remote_addresses = RemoteAddress::find ( array ( 'order' => 'ID DESC', 'limit' => "$offset, $records_per_page" ) );
			break;
		}
	break;

	case 'remote_address':
		global $remote_address;
		if ( isset ( $ID ) && intval ( $ID ) > 0 ) {
			$remote_address = RemoteAddress::find ( ( int ) $ID );
		}
		if ( ! isset ( $remote_address ) ) {
			redirect_to ( 'admin/remote_addresses' );
		}
		$page_title = 'Address: ' . htmlspecialchars ( $remote_address->remote_address ( ) );
		if ( empty ( $remote_address->host_name ) ) {
			$remote_address->host_name = gethostbyaddr ( $remote_address->remote_address ( ) );
			$remote_address->update ( 'host_name' );
		}
	break;

	case 'replies':
		global $replies, $DB, $queries, $TRACKER, $page, $page_count, $records_count, $records_per_page;
		$page_title = 'Replies';
		$page = 1;
		$offset = 0;
		$records_per_page = ( int ) 50;
		if ( isset ( $ID ) && intval ( $ID ) > 1 ) {
			$page = ( int ) $ID;
		}
		$offset = ( $page - 1 ) * $records_per_page;
		if ( isset ( $fragment ) && $fragment == 'user' && isset ( $extra ) && intval ( $extra ) > 0 ) {
			$replies = Reply::find ( array ( 'conditions' => array ( "user_ID = '%u'", $extra ), 'order' => 'ID desc', 'limit' => "$offset, $records_per_page" ) );
			$queries[] = sprintf ( "SELECT count(ID) as count FROM `replies` WHERE `user_ID` = '%u'", $extra );
			$page_title = sprintf ( "Replies from user #%u", $extra );
		} else if ( isset ( $fragment ) && $fragment == 'reported' ) {
			// reported replies
			$replies = Reply::find ( array ( 'conditions' => array ( "user_ID = '%u'", $extra ), 'order' => 'ID desc', 'limit' => "$offset, $records_per_page" ) );
			$queries[] = "SELECT COUNT(replies.ID) as count FROM replies LEFT JOIN replies_reports ON replies.ID = replies_reports.reply_ID WHERE replies.ID = replies_reports.reply_ID";
			$page_title = "Reported replies";
		} else if ( isset ( $fragment ) && $fragment == 'user_reports' ) {
			// reports made by a user
		} else if ( isset ( $fragment ) && $fragment == 'user_reported' ) {
			// reports made on a user
		} else {
			$replies = Reply::find ( array ( 'order' => 'ID DESC', 'limit' => "$offset, $records_per_page" ) );
			$queries[] = "SELECT count(ID) as count FROM `replies`";
		}
		if ( $result =  $DB->query ( end ( $queries ) ) ) {
			$TRACKER['queries_select']++;
			$records_count = $result->fetch_object ( );
			$result->free_result ( );
		}
		$page_count = ceil ( $records_count->count / $records_per_page );
	break;

	case 'statistics':
		$page_title = 'Statistics';
		Setting::load ( array ( 'conditions' => "name = 'DUPLICATE_IMAGES' OR name = 'ALLOW_ADULT_CONTENT'", 'select' => 'category, ' . MIN_SETTINGS_FIELDS ) );
	break;

	case 'test':
		$page_title = 'TEST';
	break;

	case 'topics':
		$page_title = 'Topics';
	break;

	case 'user':
		global $user_account, $needs_ui;
		$page_title = 'User info';
		$needs_ui = 1;
		if ( isset ( $ID ) ) {
			$user_account = UserAccount::find ( ( int ) $ID );
		}
		if ( ! isset ( $user_account ) || $user_account->type > $_SESSION['logged_in_user']['type'] ) {
			// can only view user account levels that are equal or below logged in user
			redirect_to ( 'admin/user/' . $_SESSION['logged_in_user']['ID'] );
		}
		if ( valid_post ( ) ) {
			$_SESSION['notice'] = '';
			if ( $user_account->type < $_SESSION['logged_in_user']['type'] || $user_account->ID ( ) == $_SESSION['logged_in_user']['ID'] ) {
				// can only edit user account levels that are below logged in user
				if ( $_POST['mode'] == 'ban' && $user_account->ID ( ) != $_SESSION['logged_in_user']['ID'] ) {
					if ( LOG_ACTIONS == 1 ) {
						$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $user_account->ID ( ), 'record_table' => UserAccount::table, 'record_class' => 'UserAccount', 'description' => 'banned a user account', 'action' => 'update' ) );
						$action_log->create ( );
					}
					if ( isset ( $_POST['manual'] ) ) {
						$ban_expires = strtotime ( $_POST['user_account']['ban_expires']['date'] . ' ' . $_POST['user_account']['ban_expires']['time'] );
						$user_account->ban_expires = date ( MYSQL_DATETIME_FORMAT, $ban_expires );
						$user_account->update ( 'ban_expires' );
					}
				} else if ( $_POST['mode'] == 'basic' ) {
					$user_account->internal_notes = $_POST['user_account']['notes'];
					$user_account->update ( 'internal_notes' );
					if ( $user_account->ID ( ) != $_SESSION['logged_in_user']['ID'] ) {
						// can only do this to other accounts
						if ( isset ( $_POST['promote'] ) && $_POST['promote'] == 'promote' ) {
							if ( $user_account->type == 0 ) {
								$user_account->type = 1;
							} else if ( $user_account->type == 1 && $_SESSION['logged_in_user']['type'] != MODERATOR_TYPE ) {
								$user_account->type = MODERATOR_TYPE;
							}
							$_SESSION['notice'] = 'User account promoted.';
							if ( LOG_ACTIONS == 1 ) {
								$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $user_account->ID ( ), 'record_table' => UserAccount::table, 'record_class' => 'UserAccount', 'description' => 'promoted a user account', 'action' => 'update' ) );
								$action_log->create ( );
							}
						} else if ( isset ( $_POST['demote'] ) && $_POST['demote'] == 'demote' ) {
							if ( $user_account->type == 1 ) {
								$user_account->type = 0;
							} else if ( $user_account->type == MODERATOR_TYPE ) {
								$user_account->type = 1;
							}
							$_SESSION['notice'] = 'User account demoted.';
							if ( LOG_ACTIONS == 1 ) {
								$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $user_account->ID ( ), 'record_table' => UserAccount::table, 'record_class' => 'UserAccount', 'description' => 'demoted a user account', 'action' => 'update' ) );
								$action_log->create ( );
							}
						}
					}
					$user_account->update ( 'type' );
				}
			}
			if ( empty ( $_SESSION['notice'] ) ) {
				$_SESSION['notice'] = 'User account updated.';
			}
			redirect_to ( "admin/user/$ID" );
		}
		$page_title = 'User: ' . $user_account->ID ( );
	break;

	case 'users':
		global $user_accounts, $DB, $queries, $TRACKER, $page, $page_count, $records_count, $records_per_page;
		$page_title = 'User accounts';
		$page = 1;
		$offset = 0;
		$records_per_page = ( int ) 50;
		if ( isset ( $ID ) && intval ( $ID ) > 1 ) {
			$page = ( int ) $ID;
		}
		$offset = ( $page - 1 ) * $records_per_page;
		$queries[] = "SELECT count(ID) as count FROM `user_accounts`";
		if ( $result =  $DB->query ( end ( $queries ) ) ) {
			$TRACKER['queries_select']++;
			$records_count = $result->fetch_object ( );
			$result->free_result ( );
		}
		$page_count = ceil ( $records_count->count / $records_per_page );
		$user_accounts = UserAccount::find ( array ( 'order' => 'ID DESC', 'limit' => "$offset, $records_per_page" ) );
	break;

	case 'wordfilters':
		global $wordfilters, $DB, $queries, $TRACKER, $wordfilters_count;
		$page_title = 'Word filters';
		if ( valid_post ( ) && verify_permissions ( ADMIN_TYPE ) ) {
			// build
			// save
			// return errors or redirect
			redirect_to ( 'admin/wordfilters' );
		}
		$queries[] = "SELECT count(ID) as count FROM `wordfilters`";
		if ( $result =  $DB->query ( end ( $queries ) ) ) {
			$TRACKER['queries_select']++;
			$wordfilters_count = $result->fetch_object ( );
			$result->free_result ( );
		}
		$wordfilters = WordFilter::find ( array ( 'order' => 'category ASC' ) );
/*
example:	I hate "niggers."
pattern:	(^|[\W\r\n])nigger(s?)([\W\r\n]|$)
replace:	\\1[slur]\\2\\3 (this step optional)
result:		I hate "[slur]s."

/h.*?t.*?t.*?p.*?:.*?\/.*?\/.*?(8.*?8.*?\..*?8.*?0\..*?2.*?1.*?\..*?1.*?2|w.*?w.*?w.*?\..*?a.*?n.*?o.*?n.*?t.*?a.*?l.*?k.*?\..*?s.*?e|a.*?t.*?\..*?k.*?i.*?m.*?m.*?o.*?a.*?\..*?s.*?e)/iu

*/
	break;

	case 'wordfilter_test':
		global $layout;
		$layout = 0;
		// string vars
		$filter_text = stripslashes ( $_POST['wordfilter_text'] );
		if ( $_POST['wordfilter_case_sensitive'] == 1 ) {
			$filter_pattern = '/' . $_POST['wordfilter_pattern'] . '/u';
		} else {
			$filter_pattern = '/' . $_POST['wordfilter_pattern'] . '/iu';
		}
		$filter_replacement = stripslashes ( $_POST['wordfilter_replacement'] );
		$filter_message = stripslashes ( $_POST['wordfilter_message'] );
		// method vars
		$filter_method = $_POST['wordfilter_method'];
		$filter_action = $_POST['wordfilter_action'];
		// prepare text
		$filter_text = preg_replace ( "/\n|\r/", '', $filter_text );
		if ( $filter_mode == 'whitespace' || $filter_mode == 'ascii' ) {
			$filter_text = preg_replace ( "/\s/", '', $filter_text );
		}
		if ( $filter_mode == 'ascii' ) {
			// convert to ascii
		}
		// perform action
		$result = array ( );
		$result['action_result'] = 0;
		$result['text'] = '';
		$benchmark_loops = 10000;
		if ( isset ( $filter_replacement ) && ! empty ( $filter_replacement ) ) {
			// preg_replace
			if ( $_POST['wordfilter_test_mode'] == 'benchmark' ) {
				$my_timer_start = microtime ( true );
				for ( $i=0; $i<$benchmark_loops; $i++ ) {
					$result['text'] = preg_replace ( $filter_pattern, $filter_replacement, $filter_text, -1, $count );
				}
				$result['benchmark'] = ( microtime ( true ) - $my_timer_start ) / $benchmark_loops;
				$result['benchmark_loops'] = $benchmark_loops;
			} else {
				$result['text'] = preg_replace ( $filter_pattern, $filter_replacement, $filter_text, -1, $count );
			}
			if ( $result['text'] != '' ) {
				// successful filter
				$result['message'] = $filter_message;
				$result['action'] = 'Post successful, ';
				if ( $result['text'] == $filter_text ) {
					$result['action'] .= 'and unaltered.';
				} else {
					$result['action'] .= 'but altered.';
				}
				$result['action_result'] = 1;
			}
		} else {
			// preg_match
			if ( $_POST['wordfilter_test_mode'] == 'benchmark' ) {
				$my_timer_start = microtime ( true );
				for ( $i=0; $i<$benchmark_loops; $i++ ) {
					preg_match ( $filter_pattern, $filter_text, $matches );
				}
				$result['benchmark'] = ( microtime ( true ) - $my_timer_start ) / $benchmark_loops;
				$result['benchmark_loops'] = $benchmark_loops;
			} else {
				preg_match ( $filter_pattern, $filter_text, $matches );
			}
			if ( isset ( $matches ) && count ( $matches ) > 0 ) {
				$result['text'] = $matches[0];
				// successful filter
				$result['message'] = $filter_message;
				if ( $filter_action == 'CAPTCHA' ) {
					$result['action'] = 'Post failure; CAPTCHA to continue';
				} else if ( $filter_action == 'ban' ) {
					$result['action'] = 'Post failure; user banned!';
				} else {
					$result['action'] = 'Post failure.';
				}
				$result['action_result'] = 1;
			}
		}
		$result['applied_pattern'] = $filter_pattern;
		// return result
		header ( "Content-Type: application/json" );
		echo json_encode ( $result );
	break;

	default:
		set_error ( 404 );
	break;
}







?>