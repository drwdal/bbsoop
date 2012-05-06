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

global $board_name, $current_board, $controller, $action, $ID, $fragment, $extra, $page_title, $original_URI;

$ensure_login = array ( 'new', 'reply', 'edit', 'edit_reply', 'report_topic', 'report_reply', 'favorite_topic', 'favorite_reply' );
if ( in_array ( $action, $ensure_login ) ) { ensure_login ( 0, 'account' ); }

switch ( $action ) {
	case 'index':
		global $topics, $DB, $queries, $TRACKER, $page, $page_count, $topics_count, $topics_per_page, $topics_new_replies;
		$page_title = 'Topics';
		$page = 1;
		$offset = 0;
		$topics_per_page = ( int ) 100;
		if ( isset ( $ID ) && intval ( $ID ) > 1 ) {
			$page = ( int ) $ID;
		}
		$offset = ( $page - 1 ) * $topics_per_page;
		$queries[] = "SELECT status, COUNT(ID) as count FROM `topics` WHERE `status` = 'published'";
		if ( $result =  $DB->query ( end ( $queries ) ) ) {
			$TRACKER['queries_select']++;
			$topics_count = $result->fetch_object ( );
		}
		$page_count = ceil ( $topics_count->count / $topics_per_page );
		$topics = Topic::find ( array ( 'conditions' => array ( "`status` = 'published'" ), 'select' => 'ID, title, created_at, status, replies_count, media_count, bumped_at, views_count, sticky, safe_for_work', 'order' => 'sticky DESC, bumped_at DESC', 'limit' => "$offset, $topics_per_page" ) );
		if ( isset ( $_SESSION['logged_in_user'] ) ) {
			// this is a very inefficient method
			$topics_new_replies = array ( );
			foreach ( $topics as $topic ) {
				$my_count = 0;
				if ( $topic_view = TopicView::find ( array ( 'first', 'conditions' => array ( "`user_ID` = '%u' AND `topic_ID` = '%u'", $_SESSION['logged_in_user']['ID'], $topic->ID ( ) ) ) ) ) {
					// already viewed, find new count
					$queries[] = "SELECT COUNT(ID) as count FROM `replies` WHERE `topic_ID` = '" . $DB->real_escape_string ( $topic->ID ( ) ) . "' AND `created_at` > '" . $DB->real_escape_string ( $topic_view->last_seen ( ) ) . "'";
					$result = $DB->query ( end ( $queries ) );
					$TRACKER['queries_select']++;
					if ( $row = $result->fetch_object ( ) ) {
						$my_count = $row->count;
					}
				}
				$topics_new_replies[( string ) $topic->ID ( )] = $my_count;
			}
		}
		respond_to_json ( $topics );
	break;

	case 'edit':
		global $logged_in_user, $record, $errors;
		if ( ! isset ( $ID ) ) {
			redirect_to ( 'topics' );
		}
		$record = Topic::find ( ( int ) $ID );
		Setting::load ( array ( 'conditions' => "category = 'POSTING' OR category = 'MODERATION'", 'select' => 'category, ' . MIN_SETTINGS_FIELDS ) );
		if ( isset ( $_POST['mod_topic'] ) && $logged_in_user->type >= MODERATOR_TYPE ) {
			$record->sticky = $_POST['record']['sticky'];
			$record->update ( 'sticky' );
			$record->locked = $_POST['record']['locked'];
			$record->update ( 'locked' );
			$record->safe_for_work = $_POST['record']['safe_for_work'];
			$record->update ( 'safe_for_work' );
			$_SESSION['notice'] = "Topic updated.";
			if ( LOG_ACTIONS == 1 ) {
				$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $record->ID ( ), 'record_table' => Topic::table, 'record_class' => 'Topic', 'description' => 'edited topic options: “<a href="' . $record->URI ( ) . '" title="' . htmlspecialchars ( $record->title ) . '">' . htmlspecialchars ( $record->title ) . '</a>”', 'action' => 'update' ) );
				$action_log->create ( );
			}
			redirect_to ( $record->redirect_URI ( ) );
		}
		if ( ! ( ( $record->locked == 1 && $logged_in_user->type < MODERATOR_TYPE ) || ( ( $logged_in_user->type >= MODERATOR_TYPE || $record->user_ID ( ) == $logged_in_user->ID ( ) ) && ( TIME_TO_EDIT_REPLIES > ( time() - ( int ) $record->created_at ( 'U' ) ) || USER_LEVEL_EXEMPT_FROM_EDIT_TIMING_LIMITS <= $logged_in_user->type ) ) ) ) {
			$_SESSION['notice'] = "You cannot edit this topic.";
			redirect_to ( $record->redirect_URI ( ) );
		}
		if ( $record->mod_edited == 1 && $logged_in_user->type < MODERATOR_TYPE ) {
			$_SESSION['notice'] = "You cannot edit a topic that has been edited by a moderator.";
			redirect_to ( $record->redirect_URI ( ) );
		}
		$user_account = UserAccount::find ( ( int ) $record->user_ID ( ) );
		if ( $logged_in_user->ID ( ) != $user_account->ID ( ) && $logged_in_user->type <= $user_account->type ) {
			$_SESSION['notice'] = "You do not have permission to edit this topic.";
			redirect_to ( $record->redirect_URI ( ) );
		}
		$page_title = 'Editing “<a href="' . BASE_URI . 'topics/view/' . $record->ID ( ) . '">' . htmlspecialchars ( $record->title ) . '</a>”';
		if ( valid_post ( ) ) {
			if ( isset ( $_POST['preview'] ) ) {
				return false;
			}
			if ( $logged_in_user->type >= MODERATOR_TYPE && $record->mod_edited != 1 ) {
				$record->mod_edited = 1;
				$record->update ( 'mod_edited' );
			}
			$record->title = $_POST['record']['title'];
			$record->body = $_POST['record']['body'];
			if ( $record->validates ( ) ) {
				// TODO: fix update method to accept array
				$record->partial_cache = '';
				$record->update ( 'partial_cache' );
				if ( $record->user_ID ( ) == $logged_in_user->ID ( ) ) {
					// mod edits don’t get the (edited X later) label
					$record->updated_at = time ( );
					$record->update ( 'updated_at' );
				}
				$record->update ( 'title' );
				$record->update ( 'body' );
			}
			if ( count ( $errors ) == 0 ) {
				if ( LOG_ACTIONS == 1 ) {
					$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $record->ID ( ), 'record_table' => Topic::table, 'record_class' => 'Topic', 'description' => 'edited a topic: “<a href="' . $record->URI ( ) . '" title="' . htmlspecialchars ( $record->title ) . '">' . htmlspecialchars ( $record->title ) . '</a>”', 'action' => 'update' ) );
					$action_log->create ( );
				}
				$_SESSION['notice'] = "Topic updated.";
				redirect_to ( $record->redirect_URI ( ) );
			}
			if ( LOG_ACTIONS == 1 ) {
				$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $record->ID ( ), 'record_table' => Topic::table, 'record_class' => 'Topic', 'description' => 'failed to edit a topic: “<a href="' . $record->URI ( ) . '" title="' . htmlspecialchars ( $record->title ) . '">' . htmlspecialchars ( $record->title ) . '</a>”', 'action' => 'update' ) );
				$action_log->create ( );
			}
		}
	break;

	case 'edit_reply':
		global $logged_in_user, $record, $errors;
		if ( ! isset ( $ID ) ) {
			redirect_to ( '' );
		}
		if ( ! $record = Reply::find ( ( int ) $ID ) ) {
			$_SESSION['notice'] = "Reply not found.";
			redirect_to ( '' );
		}
		Setting::load ( array ( 'conditions' => "category = 'POSTING' OR category = 'MODERATION'", 'select' => 'category, ' . MIN_SETTINGS_FIELDS ) );
		$topic = Topic::find ( ( int ) $record->topic_ID ( ) );
		if ( ! ( ( $logged_in_user->type >= MODERATOR_TYPE || $record->user_ID ( ) == $logged_in_user->ID ( ) ) && ( TIME_TO_EDIT_REPLIES > ( time() - ( int ) $record->created_at ( 'U' ) ) || USER_LEVEL_EXEMPT_FROM_EDIT_TIMING_LIMITS <= $logged_in_user->type ) ) ) {
			$_SESSION['notice'] = "You cannot edit this reply.";
			redirect_to ( $topic->redirect_URI ( ) );
		}
		if ( $record->mod_edited == 1 && $logged_in_user->type < MODERATOR_TYPE ) {
			$_SESSION['notice'] = "You cannot edit a reply that has been edited by a moderator.";
			redirect_to ( $topic->redirect_URI ( ) );
		}
		$user_account = UserAccount::find ( ( int ) $record->user_ID ( ) );
		if ( $logged_in_user->ID ( ) != $user_account->ID ( ) && $logged_in_user->type <= $user_account->type ) {
			$_SESSION['notice'] = "You do not have permission to edit this reply.";
			redirect_to ( $record->redirect_URI ( ) );
		}
		$page_title = 'Editing reply.';
		if ( valid_post ( ) ) {
			if ( isset ( $_POST['preview'] ) ) {
				return false;
			}
			if ( $logged_in_user->type >= MODERATOR_TYPE && $record->mod_edited != 1 ) {
				$record->mod_edited = 1;
				$record->update ( 'mod_edited' );
			}
			$record->body = $_POST['record']['body'];
			if ( $record->validates ( ) ) {
				// TODO: fix update method to accept array
				$record->partial_cache = '';
				$record->update ( 'partial_cache' );
				if ( $record->user_ID ( ) == $logged_in_user->ID ( ) ) {
					// mod edits don’t get the (edited X later) label
					$record->updated_at = time ( );
					$record->update ( 'updated_at' );
				}
				$record->update ( 'body' );
			}
			if ( count ( $errors ) == 0 ) {
				if ( LOG_ACTIONS == 1 ) {
					$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $record->ID ( ), 'record_table' => Reply::table, 'record_class' => 'Reply', 'description' => 'edited a reply in topic: “<a href="' . $record->URI ( ) . '" title="' . htmlspecialchars ( $topic->title ) . '">' . htmlspecialchars ( $topic->title ) . '</a>”', 'action' => 'update' ) );
					$action_log->create ( );
				}
				$_SESSION['notice'] = "Reply updated.";
				redirect_to ( $record->redirect_URI ( ) );
			}
			if ( LOG_ACTIONS == 1 ) {
				$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $record->ID ( ), 'record_table' => Reply::table, 'record_class' => 'Reply', 'description' => 'failed to edit a reply in topic: “<a href="' . $record->URI ( ) . '" title="' . htmlspecialchars ( $topic->title ) . '">' . htmlspecialchars ( $topic->title ) . '</a>”', 'action' => 'update' ) );
				$action_log->create ( );
			}
		}
	break;

	case 'favorite_topic':
		global $layout, $logged_in_user;
		$layout = 0;
		$result = array ( );
		$result['result'] = 0;
		if ( isset ( $_POST['topic_ID'] ) && ! empty ( $_POST['topic_ID'] ) ) {
			if ( $topic = Topic::find ( ( int ) $_POST['topic_ID'] ) ) {
				$user_account = UserAccount::find ( ( int ) $topic->user_ID ( ) );
				if ( $topic_favorite = TopicFavorite::find ( array ( 'first', 'conditions' => array ( "`topic_ID` = '%u' AND `user_ID` = '%u'", $_POST['topic_ID'], $_SESSION['logged_in_user']['ID'] ) ) ) ) {
					// already exists
					$topic_favorite->destroy ( );
					$topic->update_counter ( 'favorites_count', -1 ); // remove
					$logged_in_user->update_counter ( 'topics_favorites_count', -1 ); // remove
					if ( $logged_in_user->ID ( ) != $user_account->ID ( ) ) {
						$user_account->update_counter ( 'topics_favorited_count', -1 ); // remove
					}
					$result['action'] = 'removed';
				} else {
					$topic_favorite = new TopicFavorite ( array ( 'user_ID' => $_SESSION['logged_in_user']['ID'], 'topic_ID' => $topic->ID ( ) ) );
					if ( $new_ID = $topic_favorite->create ( ) ) {
						$topic->update_counter ( 'favorites_count', 1 ); // add
						$logged_in_user->update_counter ( 'topics_favorites_count', 1 ); // add
						if ( $logged_in_user->ID ( ) != $user_account->ID ( ) ) {
							$user_account->update_counter ( 'topics_favorited_count', 1 ); // add
						}
						$result['action'] = 'added';
					}
				}
				$result['topic_favorites'] = $topic->favorites_count;
				$result['result'] = 1;
			}
		}
		header ( "Content-Type: application/json", TRUE );
		echo json_encode ( $result );
	break;

	case 'favorite_reply':
		global $layout, $logged_in_user;
		$layout = 0;
		$result = array ( );
		$result['result'] = 0;
		if ( isset ( $_POST['reply_ID'] ) && ! empty ( $_POST['reply_ID'] ) ) {
			if ( $reply = Reply::find ( ( int ) $_POST['reply_ID'] ) ) {
				$topic = Topic::find ( ( int ) $reply->topic_ID ( ) );
				$user_account = UserAccount::find ( ( int ) $reply->user_ID ( ) );
				if ( $reply_favorite = ReplyFavorite::find ( array ( 'first', 'conditions' => array ( "`reply_ID` = '%u' AND `topic_ID` = '%u' AND `user_ID` = '%u'", $reply->ID ( ), $reply->topic_ID ( ), $_SESSION['logged_in_user']['ID'] ) ) ) ) {
					// already exists
					$reply_favorite->destroy ( );
					$reply->update_counter ( 'favorites_count', -1 ); // remove
					$logged_in_user->update_counter ( 'replies_favorites_count', -1 ); // remove
					if ( $logged_in_user->ID ( ) != $user_account->ID ( ) ) {
						$user_account->update_counter ( 'replies_favorited_count', -1 ); // remove
					}
					$topic->update_counter ( 'replies_favorites_count', -1 ); // remove
					$result['action'] = 'removed';
				} else {
					$reply_favorite = new ReplyFavorite ( array ( 'user_ID' => $_SESSION['logged_in_user']['ID'], 'reply_ID' => $_POST['reply_ID'], 'topic_ID' => $reply->topic_ID ( ) ) );
					if ( $new_ID = $reply_favorite->create ( ) ) {
						$reply->update_counter ( 'favorites_count', 1 ); // add
						$logged_in_user->update_counter ( 'replies_favorites_count', 1 ); // add
						if ( $logged_in_user->ID ( ) != $user_account->ID ( ) ) {
							$user_account->update_counter ( 'replies_favorited_count', 1 ); // add
						}
						$topic->update_counter ( 'replies_favorites_count', 1 ); // add
						$result['action'] = 'added';
					}
				}
				$result['reply_favorites'] = $reply->favorites_count;
				$result['result'] = 1;
			}
		}
		header ( "Content-Type: application/json", TRUE );
		echo json_encode ( $result );
	break;

	case 'new':
		global $topic, $replies, $errors, $logged_in_user;
		// TODO: mechanism for boards/categories
		Setting::load ( array ( 'conditions' => "category = 'POSTING' OR category = 'MEDIA' OR category = 'MODERATION'", 'select' => 'category, ' . MIN_SETTINGS_FIELDS ) );
		ini_set_post_settings ( );
		$page_title = 'New topic';
		if ( NEW_TOPICS_ALLOWED == 1 ) {
			$category_ID = 1;
			if ( isset ( $GLOBALS['current_board'] ) && intval ( $GLOBALS['current_board']->ID ( ) ) > 0 ) {
				$category_ID = $GLOBALS['current_board']->ID ( );
			}
			if ( defined ( 'MINIMUM_ACCOUNT_AGE_NEW_TOPIC' ) && ( ( time ( ) - intval ( $logged_in_user->created_at ( 'U' ) ) ) < MINIMUM_ACCOUNT_AGE_NEW_TOPIC ) ) {
				$_SESSION['notice'] = 'You cannot yet post new topics; your account must be at least ' . MINIMUM_ACCOUNT_AGE_NEW_TOPIC . ' second' . ( ( MINIMUM_ACCOUNT_AGE_NEW_TOPIC != 1 ) ? 's' : '' ) . ' old.';
				redirect_to ( '' );
			}
			if ( valid_post ( ) ) {
				$topic = new Topic ( array ( 'title' => $_POST['record']['title'], 'body' => $_POST['record']['body'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'bumped_at' => gmdate ( MYSQL_DATETIME_FORMAT ), 'name' => $_POST['record']['name'], 'category_ID' => $category_ID, 'safe_for_work' => 1 ) );
				if ( isset ( $_POST['preview'] ) ) {
					return false;
				}
				// update the user account info with their IP address; assists moderation without compromising privacy too much
				$logged_in_user->remote_address = $_SERVER['REMOTE_ADDR'];
				$logged_in_user->update ( 'remote_address' );
				// check for timing limits
				if ( USER_LEVEL_EXEMPT_FROM_POST_TIMING_LIMITS == -1 || ( intval ( USER_LEVEL_EXEMPT_FROM_POST_TIMING_LIMITS ) > intval ( $_SESSION['logged_in_user']['type'] ) ) ) {
					// post limiting is on and applies to this user
					if ( time() - $_SESSION['logged_in_user']['last_topic'] < TOPIC_TIME_BETWEEN_EACH ) {
						$errors[] = TOPIC_TIME_BETWEEN_EACH . ' second' . ( ( TOPIC_TIME_BETWEEN_EACH != 1 ) ? 's' : '' ) . ' are required between each new topic.';
						header ( 'Status: 409', TRUE, 409 );
						return false;
					}
				}
				// handle files
				if ( isset ( $_FILES ) && ! empty ( $_FILES['file_upload']['tmp_name'] ) && UPLOADS_ALLOWED == 1 ) {
					if ( defined ( 'MINIMUM_ACCOUNT_AGE_NEW_MEDIA' ) && ( ( time ( ) - intval ( $logged_in_user->created_at ( 'U' ) ) ) < MINIMUM_ACCOUNT_AGE_NEW_MEDIA ) ) {
						$_SESSION['notice'] = 'You cannot yet post new media; your account must be at least ' . MINIMUM_ACCOUNT_AGE_NEW_MEDIA . ' second' . ( ( MINIMUM_ACCOUNT_AGE_NEW_MEDIA != 1 ) ? 's' : '' ) . ' old.';
					} else {
						$media = new Media ( array ( 'original_file_name' => $_FILES['file_upload']['name'], 'user_ID' => $_SESSION['logged_in_user']['ID'] ) );
					}
				}
				$_SESSION['logged_in_user']['last_name'] = $_POST['record']['name'];
				// publish
				if ( $topic->validates ( ) ) {
					if ( TOPICS_GO_LIVE_WHEN_POSTED == 1 ) {
						$topic->status = 'published';
					} else if ( TOPICS_GO_LIVE_WHEN_POSTED == 0 ) {
						$topic->status = 'pending approval';
					}
				}
				// save
				$logged_in_user->remote_address = $_SERVER['REMOTE_ADDR'];
				$logged_in_user->update ( 'remote_address' );
				$logged_in_user->session_ID = SESSION_ID;
				$logged_in_user->update ( 'session_ID' );
				if ( $new_ID = $topic->create ( ) ) {
					$remote_address = RemoteAddress::find ( array ( 'first', 'conditions' => array ( "`remote_address` = '%s'", $_SERVER['REMOTE_ADDR'] ), 'select' => 'ID, remote_address, topics_count, media_count, permission_to_post' ) );
					$topic->set_ID ( $new_ID );
					$topic->set_nametrip ( );
					if ( isset ( $media ) ) {
						if ( $media_ID = $media->create ( ) ) {
							$topic->media_ID = $media_ID;
							$topic->update ( 'media_ID' );
							// TODO: why does this counter not update?
							$topic->update_counter ( 'media_count', 1 );
							$logged_in_user->update_counter ( 'media_count', 1 );
							$remote_address->update_counter ( 'media_count', 1 );
							if ( isset ( $GLOBALS['current_board'] ) && intval ( $GLOBALS['current_board']->ID ( ) ) > 0 ) {
								$GLOBALS['current_board']->update_counter ( 'media_count', 1 );
							}
						} else {
							$topic->status = 'draft';
							$topic->update ( array ( 'status' ) );
						}
					}
					if ( count ( $errors ) == 0 ) {
						$_SESSION['logged_in_user']['last_topic'] = time();
						$remote_address->update_counter ( 'topics_count', 1 );
						$logged_in_user->update_counter ( 'topics_count', 1 );
						if ( isset ( $GLOBALS['current_board'] ) && intval ( $GLOBALS['current_board']->ID ( ) ) > 0 ) {
							$GLOBALS['current_board']->update_counter ( 'topics_count', 1 );
						}
						if ( LOG_ACTIONS == 1 ) {
							$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $topic->ID ( ), 'record_table' => Topic::table, 'record_class' => 'Topic', 'description' => 'created a new topic: “<a href="' . $topic->URI ( ) . '" title="' . htmlspecialchars ( $topic->title ) . '">' . htmlspecialchars ( $topic->title ) . '</a>”', 'action' => 'update' ) );
							$action_log->create ( );
						}
						$_SESSION['notice'] = "Topic posted.";
						redirect_to ( $topic->redirect_URI ( ), 301 );
					}
				}
				header ( 'Status: 400', TRUE, 400 );
			}
		} else {
			$_SESSION['notice'] = "Topic posting is currently disabled.";
			redirect_to ( 'topics' );
		}
	break;

	case 'reply':
		global $topic, $replies, $errors, $logged_in_user;
		Setting::load ( array ( 'conditions' => "category = 'POSTING' OR category = 'MEDIA' OR category = 'MODERATION'", 'select' => 'category, ' . MIN_SETTINGS_FIELDS ) );
		ini_set_post_settings ( );
		if ( NEW_REPLIES_ALLOWED == 1 ) {
			$topic = Topic::find ( array ( 'first', 'conditions' => array ( "`ID` = '%u'", $ID ), 'select' => 'ID, title, body, partial_cache, media_ID, replies_count, media_count, locked' ) );
			if ( $topic->locked == 1 && $_SESSION['logged_in_user']['type'] < MODERATOR_TYPE ) {
				$_SESSION['notice'] = "Cannot reply; this topic is locked.";
				redirect_to ( $topic->redirect_URI ( ) );
			}
			if ( defined ( 'MINIMUM_ACCOUNT_AGE_NEW_REPLY' ) && ( ( time ( ) - intval ( $logged_in_user->created_at ( 'U' ) ) ) < MINIMUM_ACCOUNT_AGE_NEW_REPLY ) ) {
				$_SESSION['notice'] = 'You cannot yet post new replies; your account must be at least ' . MINIMUM_ACCOUNT_AGE_NEW_REPLY . ' second' . ( ( MINIMUM_ACCOUNT_AGE_NEW_REPLY != 1 ) ? 's' : '' ) . ' old.';
				redirect_to ( 'topics/view/' . $topic->ID ( ) );
			}
			if ( isset ( $topic ) ) {
				$page_title = 'New reply to “<a href="' . BASE_URI . 'topics/view/' . $topic->ID ( ) . '">' . htmlspecialchars ( $topic->title ) . '</a>”';
				$reply = new Reply ( array ( 'body' => $_POST['record']['body'], 'topic_ID' => $topic->ID ( ), 'user_ID' => $_SESSION['logged_in_user']['ID'], 'name' => $_POST['record']['name'] ) );
				if ( isset ( $fragment ) && $_SERVER['REQUEST_METHOD'] == 'GET' ) {
					global $reply_to_this;
					if ( $extra == 'OP' ) {
						$reply_to_this = &$topic;
					} else {
						$reply_to_this = Reply::find( array ( 'first', 'conditions' => array ( "`ID` = '%u' AND `status` = 'published'", $extra ), 'select' => 'ID, body, status, partial_cache' ) );
					}
					if ( isset ( $reply_to_this ) ) {
						if ( $extra == 'OP' ) {
							$output = '@' . $extra . "\n";
						} else {
							$output = '@' . number_format ( $extra ) . "\n";
						}
						if ( $fragment == 'quote' ) {
							$output .= preg_replace ( "/(^|\r\n)/", "\\1> ", $reply_to_this->body ) . "\n";
						}
						$_POST['record']['body'] = $output . $reply->body;
					}
				}
				if ( valid_post ( ) ) {
					if ( isset ( $_POST['preview'] ) ) {
						return false;
					}
					$_SESSION['logged_in_user']['remote_address'] = $_SERVER['REMOTE_ADDR'];
					if ( USER_LEVEL_EXEMPT_FROM_POST_TIMING_LIMITS == -1 || ( intval ( USER_LEVEL_EXEMPT_FROM_POST_TIMING_LIMITS ) > intval ( $_SESSION['logged_in_user']['type'] ) ) ) {
						// post limiting is on and applies to this user
						if ( time() - $_SESSION['logged_in_user']['last_reply'] < REPLY_TIME_BETWEEN_EACH ) {
							$errors[] = REPLY_TIME_BETWEEN_EACH . ' second' . ( ( REPLY_TIME_BETWEEN_EACH != 1 ) ? 's' : '' ) . ' are required between each new reply.';
							header ( 'STATUS', TRUE, 409 );
							return false;
						}
					}
					// handle files
					if ( isset ( $_FILES ) && ! empty ( $_FILES['file_upload']['tmp_name'] ) && UPLOADS_ALLOWED == 1 ) {
						if ( defined ( 'MINIMUM_ACCOUNT_AGE_NEW_MEDIA' ) && ( ( time ( ) - intval ( $logged_in_user->created_at ( 'U' ) ) ) < MINIMUM_ACCOUNT_AGE_NEW_MEDIA ) ) {
							$_SESSION['notice'] = 'You cannot yet post new media; your account must be at least ' . MINIMUM_ACCOUNT_AGE_NEW_MEDIA . ' second' . ( ( MINIMUM_ACCOUNT_AGE_NEW_MEDIA != 1 ) ? 's' : '' ) . ' old.';
						} else {
							if ( $topic->media_count < TOPIC_MEDIA_COUNT_LIMIT ) {
								$media = new Media ( array ( 'original_file_name' => $_FILES['file_upload']['name'], 'user_ID' => $_SESSION['logged_in_user']['ID'] ) );
							} else {
								$_SESSION['notice'] = "File not saved—this topic has reached its media limit";
							}
						}
					}
					$_SESSION['logged_in_user']['last_name'] = $_POST['record']['name'];
					if ( $reply->validates ( ) ) {
						if ( REPLIES_GO_LIVE_WHEN_POSTED == 1 ) {
						$reply->status = 'published';
						} else {
							$reply->status = 'pending approval';
						}
					}
					// save
					$_SESSION['logged_in_user']['remote_address'] = $_SERVER['REMOTE_ADDR'];
					$logged_in_user->session_ID = SESSION_ID;
					$logged_in_user->update ( 'session_ID' );
					if ( $new_ID = $reply->create ( ) ) {
						$remote_address = RemoteAddress::find ( array ( 'first', 'conditions' => array ( "`remote_address` = '%s'", $_SERVER['REMOTE_ADDR'] ), 'select' => 'ID, remote_address, replies_count, media_count, permission_to_post' ) );
						$reply->set_ID ( $new_ID );
						$reply->set_nametrip ( );
						if ( isset ( $media ) ) {
							if ( $media_ID = $media->create ( ) ) {
								$reply->media_ID = $media_ID;
								$reply->update ( array ( 'media_ID' ) );
								$topic->update_counter ( 'media_count', 1 );
								$logged_in_user->update_counter ( 'media_count', 1 );
								$remote_address->update_counter ( 'media_count', 1 );
								if ( isset ( $GLOBALS['current_board'] ) && intval ( $GLOBALS['current_board']->ID ( ) ) > 0 ) {
									$GLOBALS['current_board']->update_counter ( 'media_count', 1 );
								}
							} else {
								$reply->status = 'draft';
								$reply->update ( array ( 'status' ) );
							}
						}
						if ( count ( $errors ) == 0 ) {
							$topic->update_counter ( 'replies_count', 1 );
							$topic->bumped_at = gmdate ( MYSQL_DATETIME_FORMAT );
							$topic->update ( 'bumped_at' );
							if ( isset ( $GLOBALS['current_board'] ) && intval ( $GLOBALS['current_board']->ID ( ) ) > 0 ) {
								$GLOBALS['current_board']->update_counter ( 'replies_count', 1 );
							}
							$_SESSION['logged_in_user']['last_reply'] = time();
							$logged_in_user->update_counter ( 'replies_count', 1 );
							$remote_address->update_counter ( 'replies_count', 1 );
							if ( LOG_ACTIONS == 1 ) {
								$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $reply->ID ( ), 'record_table' => Reply::table, 'record_class' => 'Reply', 'description' => 'replied to a topic: “<a href="' . $reply->URI ( ) . '" title="' . htmlspecialchars ( $topic->title ) . '">' . htmlspecialchars ( $topic->title ) . '</a>”', 'action' => 'insert' ) );
								$action_log->create ( );
							}
							$_SESSION['notice'] = "Reply posted.";
							redirect_to ( $reply->redirect_URI ( ), 301 );
						}
					}
					header ( 'STATUS', TRUE, 400 );
				}
				if ( false && LOG_ACTIONS == 1 ) {
					$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $topic->ID ( ), 'record_table' => Topic::table, 'record_class' => 'Topic', 'description' => 'started a reply to a topic: “<a href="' . $topic->URI ( ) . '" title="' . htmlspecialchars ( $topic->title ) . '">' . htmlspecialchars ( $topic->title ) . '</a>”', 'action' => 'update' ) );
					$action_log->create ( );
				}
			} else {
				set_error ( 404 );
			}
		} else {
			$_SESSION['notice'] = "Reply posting is currently disabled.";
			redirect_to ( 'topics' );
		}
	break;

	case 'popular':
		global $topics;
		$page_title = 'Popular topics';
		// using bumped_at would allow old topics to remain in the popular listing
		// using created_at allows topics to “age out” of the popular listing
		$topics = Topic::find ( array ( 'conditions' => "`status` = 'published'", 'select' => '*, (`favorites_count` + ( `replies_favorites_count` / 2 ) + ( `views_count` / 2 ) + `replies_count` + (`media_count` * 1.5) + DATEDIFF(`created_at`, NOW())) as `activity_count`', 'order' => 'activity_count DESC', 'limit' => '50' ) );
	break;

	case 'report_reply':
		global $layout, $report_reasons;
		$layout = 0;
		$result = array ( );
		$result['result'] = 0;
		if ( isset ( $_POST['reply_ID'] ) && ! empty ( $_POST['reply_ID'] ) && in_array ( $_POST['reason'], $report_reasons ) ) {
			if ( $reply = Reply::find ( ( int ) $_POST['reply_ID'] ) ) {
				$user_account = UserAccount::find ( ( int ) $reply->user_ID ( ) );
				if ( $reply_report = ReplyReport::find ( array ( 'first', 'conditions' => array ( "`reply_ID` = '%u' AND `user_ID` = '%u'", $reply->ID ( ), $_SESSION['logged_in_user']['ID'] ) ) ) ) {
					// already exists
					$result['action'] = 'already reported';
				} else {
					$reply_report = new ReplyReport ( array ( 'user_ID' => $_SESSION['logged_in_user']['ID'], 'reply_ID' => $reply->ID ( ), 'reason' => $_POST['reason'] ) );
					if ( $new_ID = $reply_report->create ( ) ) {
						$reply->update_counter ( 'reports_count', 1 ); // add
						$user_account->update_counter ( 'reports_count', 1 ); // add
						$result['action'] = 'added';
					}
					if ( LOG_ACTIONS == 1 ) {
						$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $new_ID, 'record_table' => ReplyReport::table, 'record_class' => 'ReplyReport', 'description' => 'reported a reply', 'action' => 'insert' ) );
						$action_log->create ( );
					}
				}
				$result['result'] = 1;
			}
		}
		header ( "Content-Type: application/json", TRUE );
		echo json_encode ( $result );
	break;

	case 'report_topic':
		global $layout, $report_reasons;
		$layout = 0;
		$result = array ( );
		$result['result'] = 0;
		if ( isset ( $_POST['topic_ID'] ) && ! empty ( $_POST['topic_ID'] ) && in_array ( $_POST['reason'], $report_reasons ) ) {
			if ( $topic = Topic::find ( ( int ) $_POST['topic_ID'] ) ) {
				$user_account = UserAccount::find ( ( int ) $topic->user_ID ( ) );
				if ( $topic_report = TopicReport::find ( array ( 'first', 'conditions' => array ( "`topic_ID` = '%u' AND `user_ID` = '%u'", $topic->ID ( ), $_SESSION['logged_in_user']['ID'] ) ) ) ) {
					// already exists
					$result['action'] = 'already reported';
				} else {
					$topic_report = new TopicReport ( array ( 'user_ID' => $_SESSION['logged_in_user']['ID'], 'topic_ID' => $topic->ID ( ), 'reason' => $_POST['reason'] ) );
					if ( $new_ID = $topic_report->create ( ) ) {
						$topic->update_counter ( 'reports_count', 1 ); // add
						$user_account->update_counter ( 'reports_count', 1 ); // add
						$result['action'] = 'added';
					}
					if ( LOG_ACTIONS == 1 ) {
						$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $new_ID, 'record_table' => TopicReport::table, 'record_class' => 'TopicReport', 'description' => 'reported a topic', 'action' => 'insert' ) );
						$action_log->create ( );
					}
				}
				$result['result'] = 1;
			}
		}
		header ( "Content-Type: application/json" );
		echo json_encode ( $result );
	break;

	case 'statistics':
		global $topic, $replies;
		$topic = Topic::find ( ( int ) $ID );
		if ( isset ( $topic ) && $topic->status == 'published' ) {
			$page_title = 'Statistics for topic: “<a href="' . $topic->URI ( ) . '" title="' . htmlspecialchars ( $topic->title ) . '" class="inline">' . htmlspecialchars ( $topic->title ) . '</a>”';
		} else {
			redirect_to ( '' );
		}
	break;

	case 'view':
		global $topic, $replies, $favorites, $topic_favorite, $new_reply_ID;
		$topic = Topic::find ( ( int ) $ID );
		if ( isset ( $topic ) && $topic->status == 'published' ) {
			$page_title = htmlspecialchars ( $topic->title );
			if ( $topic->safe_for_work == 0 ) {
				$page_title .= ' <span class="small_text">(NSFW)</span>';
			}
		} else {
			set_error ( 404 );
			return false;
		}
		$replies = $topic->replies ( );
		if ( isset ( $_SESSION['logged_in_user'] ) ) {
			// new replies
			if ( $topic_view = TopicView::find ( array ( 'first', 'conditions' => array ( "`user_ID` = '%u' AND `topic_ID` = '%u'", $_SESSION['logged_in_user']['ID'], $topic->ID ( ) ) ) ) ) {
				// already viewed, find new reply
				$reply = Reply::find ( array ( 'first', 'conditions' => array ( "`topic_ID` = '%u' AND `created_at` >= '%s' AND `status` = 'published'", $topic->ID ( ), $topic_view->last_seen ( ) ), 'select' => 'ID, topic_ID, created_at, status', 'order' => 'created_at ASC' ) );
				if ( isset ( $reply ) ) {
					$new_reply_ID = ( int ) $reply->ID ( );
				}
			}
			// views
			if ( $topic_view = TopicView::find ( array ( 'first', 'conditions' => array ( "`topic_ID` = '%u' AND `user_ID` = '%u'", $topic->ID ( ), $_SESSION['logged_in_user']['ID'] ) ) ) ) {
				// already seen
				$topic_view->last_seen = time();
				$topic_view->update ( 'last_seen' );
			} else {
				// new view
				$topic_view = new TopicView ( array ( 'topic_ID' => $topic->ID ( ), 'user_ID' => $_SESSION['logged_in_user']['ID'] ) );
				$topic_view->create ( );
				$topic->update_counter ( 'views_count', 1 );
				if ( $LOG_ACTIONS == 1 ) {
					$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => $topic->ID ( ), 'record_table' => Topic::table, 'record_class' => 'Topic', 'description' => 'viewed a topic: “<a href="' . $topic->URI ( ) . '">' . htmlspecialchars ( $topic->title ) . '</a>”', 'action' => 'select' ) );
					$action_log->create ( );
				}
			}
			// favorites
			$favorite_results = ReplyFavorite::find ( array ( 'conditions' => array ( "`topic_ID` = '%u' AND `user_ID` = '%u'", $topic->ID ( ), $_SESSION['logged_in_user']['ID'] ) ) );
			$favorites = array ( );
			$count = count ( $favorite_results );
			for ( $i=0; $i < $count; $i++ ) {
				$favorites[$i] = $favorite_results[$i]->reply_ID;
			}
			// topic fav
			$topic_favorite = TopicFavorite::find ( array ( 'first', 'conditions' => array ( "`topic_ID` = '%u' AND `user_ID` = '%u'", $topic->ID ( ), $_SESSION['logged_in_user']['ID'] ) ) );
		}
		Setting::load ( array ( 'conditions' => "name = 'ALLOW_ADULT_CONTENT'", 'select' => MIN_SETTINGS_FIELDS ) );
		Setting::load ( array ( 'conditions' => "`category` = 'MODERATION'", 'select' => 'category, ' . MIN_SETTINGS_FIELDS ) );
		respond_to_json ( array ( $topic, $replies ) );
	break;

	default:
		set_error ( 404 );
	break;
}

/* FUNCTIONS
================================================== */

?>