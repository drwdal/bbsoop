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

global $board_name, $controller, $action, $ID, $fragment, $extra, $page_title, $original_URI;

//$ensure_login_except = array ( 'index','new','login','logout' );
//if ( ! in_array ( $action, $ensure_login_except ) ) { ensure_login ( 0 ); }

switch ( $action ) {

	case 'index':
		global $current_board, $topics, $DB, $queries, $TRACKER, $page, $page_count, $topics_count, $topics_per_page, $topics_new_replies;
		if ( ! isset ( $current_board ) ) {
			redirect_to ( '' );
		}
//		$page_title = htmlspecialchars ( $current_board->name );
		$page_title = 'Topics';
		$page = 1;
		$offset = 0;
		$topics_per_page = ( int ) 100;
		if ( isset ( $ID ) && intval ( $ID ) > 1 ) {
			$page = ( int ) $ID;
		}
		$offset = ( $page - 1 ) * $topics_per_page;
		$queries[] = "SELECT status, COUNT(ID) as count FROM `topics` WHERE `status` = 'published' AND `category_ID` = '" . $DB->real_escape_string ( $current_board->ID ( ) ) . "'";
		if ( $result =  $DB->query ( end ( $queries ) ) ) {
			$TRACKER['queries_select']++;
			$topics_count = $result->fetch_object ( );
		}
		$page_count = ceil ( $topics_count->count / $topics_per_page );
		$topics = Topic::find ( array ( 'conditions' => array ( "`status` = 'published' AND `category_ID` = '%u'", $current_board->ID ( ) ), 'select' => 'ID, title, created_at, status, replies_count, media_count, bumped_at, views_count, sticky, category_ID, safe_for_work', 'order' => 'sticky DESC, bumped_at DESC', 'limit' => "$offset, $topics_per_page" ) );
		if ( isset ( $_SESSION['logged_in_user'] ) && array_key_exists ( 'status', $_SESSION['logged_in_user'] ) && $_SESSION['logged_in_user']['status'] == 'active' ) {
			// this is a fairly inefficient method
			$topics_new_replies = array ( );
			if ( isset ( $topics ) && count ( $topics ) > 0 ) {
				foreach ( $topics as $topic ) {
					$my_count = 0;
					if ( $topic_view = TopicView::find ( array ( 'first', 'conditions' => array ( "`user_ID` = '%u' AND `topic_ID` = '%u'", $_SESSION['logged_in_user']['ID'], $topic->ID ( ) ) ) ) ) {
						// already viewed, find new count
						if ( intval ( $topic->bumped_at ( 'U' ) ) > intval ( $topic_view->last_seen ( 'U' ) ) ) {
							$queries[] = "SELECT COUNT(ID) as count FROM `replies` WHERE `topic_ID` = '" . $DB->real_escape_string ( $topic->ID ( ) ) . "' AND `created_at` > '" . $DB->real_escape_string ( $topic_view->last_seen ( ) ) . "'";
							$result = $DB->query ( end ( $queries ) );
							$TRACKER['queries_select']++;
							if ( $row = $result->fetch_object ( ) ) {
								$my_count = $row->count;
							}
						}
					}
					$topics_new_replies[( string ) $topic->ID ( )] = $my_count;
				}
			}
		}
		respond_to_json ( array ( 'current_board' => $current_board, 'topics' => $topics ) );
	break;

	default:
		set_error ( 404 );
	break;
}
?>
