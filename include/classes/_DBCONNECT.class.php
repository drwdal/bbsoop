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

class _DBCONNECT {
	// init
	
	public function connect ( $arguments ) {
		global $DB;
		define ( 'IMGBOARD_VERSION', '0.3.7' );
		// TODO: identify installed libraries (mysql or mysqli) and pick accordingly
		@$DB = new mysqli ( $arguments['db_host'], $arguments['db_user'], $arguments['db_password'], $arguments['db_name'] );
		if ( $DB->connect_error ) {
			// connection wholly failed
			return false;
		} else {
			// check charset
			if ( ! $DB->set_charset ( $arguments['db_charset'] ) ) {
				trigger_error ( htmlentities ( sprintf ( "Error loading character set %s: %s\n", $arguments['db_charset'], $DB->error ), ENT_QUOTES, APP_CHARSET, FALSE ), E_USER_ERROR );
			}
		}
	}
}
?>