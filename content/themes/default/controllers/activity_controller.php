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

//$ensure_login_except = array ( 'index','new','login','logout' );
//if ( ! in_array ( $action, $ensure_login_except ) ) { ensure_login ( 0 ); }

switch ( $action ) {

	case 'index':
		global $actions, $online_count, $DB, $queries, $TRACKER;
		$page_title = 'Recent activity';
		if ( LOG_ACTIONS == 1 && isset ( $_SESSION['logged_in_user'] ) ) {
//			$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $_SESSION['logged_in_user']['ID'], 'record_ID' => 0, 'record_table' => Action::table, 'record_class' => 'Action', 'description' => 'viewed the activity feed', 'action' => 'select' ) );
//			$action_log->create ( );
		}
		$actions = Action::find ( array ( 'conditions' => array ( "`created_at` > '%s'", date ( MYSQL_DATETIME_FORMAT, time ( ) - 86400 ) ), 'limit' => 100, 'order' => 'ID DESC', 'select' => 'DISTINCT user_ID, ID, created_at, description' ) );
		$queries[] = "SELECT count(DISTINCT user_ID) as count FROM `actions` WHERE `created_at` > '" . date ( MYSQL_DATETIME_FORMAT, ( time ( ) - 900 ) ) . "'";
		if ( $result =  $DB->query ( end ( $queries ) ) ) {
			$TRACKER['queries_select']++;
			$online_count = $result->fetch_object ( );
			$result->free_result ( );
		}
	break;

	default:
		set_error ( 404 );
	break;
}
?>