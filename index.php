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

// This is the base file through which everything else loads and runs.

// checking for this constant is a simple way to ensure that all other files are called from here, but it’s certainly not foolproof
define ( 'IMGBOARD_INIT', 1 );

// get configurations specific to this install
require ( 'config/config.php' );
// this file initializes the framework and database
require ( 'include/core.php' );
global $DB;
if ( $DB->connect_error ) {
	// TODO: detect the difference between a connection error and an uninitialized application
	header ( 'Location: http://' . DOMAIN . BASE_URI . 'install.php', TRUE, 303 );
} else {
	// Ready to go! Start up the application-level code
	IMGBOARD_init_controller ( );
	IMGBOARD_get_helper ( );
	IMGBOARD_init_view ( );
	IMGBOARD_footer ( );
}
?>
