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

switch ( $action ) {
	case 'index':
		global $boards, $page;
		$page = Page::find ( array ( 'first', 'conditions' => array ( "`title` = 'Home page'" ) ) );
		$user_type = 0;
		if ( isset ( $_SESSION['logged_in_user'] ) ) {
			$user_type = ( int ) $_SESSION['logged_in_user']['type'];
		}
		$boards = Category::find ( array ( 'conditions' => array ( "`visible_to` <= '%u'", $user_type ), 'order' => 'name ASC' ) );
	break;

	default:
		set_error ( 404 );
	break;
}

?>