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
		global $pages;
		$page_title = 'Pages';
		$pages = Page::find ( array ( 'conditions' => "`status` = 'published' AND `parent_ID` IS NULL", 'select' => 'ID, URI, title, status, parent_ID', 'order' => 'title ASC' ) );
	break;

	case 'view':
		global $page;
		// find the page
		if ( isset ( $ID ) && intval ( $ID ) > 0 ) {
			$page = Page::find ( array ( 'first', 'conditions' => array ( "`ID` = '%u' AND `status` = 'published'", $ID ) ) );
		}
		if ( ! isset ( $page ) ) {
			redirect_to ( 'page' );
		}
		$page_title = htmlspecialchars ( $page->title );
	break;

	default:
		set_error ( 404 );
	break;
}

?>