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

// handles DB connections
require ( "classes/_DBCONNECT.class.php" );
if ( file_exists ( BASE_PATH . "config/_APPCONFIG.class.php" ) ) {
	// holds DB info protected from app; sends to DB connector
	require ( BASE_PATH . "config/_APPCONFIG.class.php" );
} else {
	// This is one of the core conditions that pushes BBSOOP past the initial install
	header ( 'Status: 500', TRUE, 500 );
	die ( "<h1>BBSOOP cannot start</h1>\n<p>Rename <code>config/_APPCONFIG.class.example.php</code> to <code>config/_APPCONFIG.class.php</code> and edit its contents to reflect your database configuration.</p>\n<p>On the command line: <br /><code>mv config/_APPCONFIG.class.example.php config/_APPCONFIG.class.php</code><br /><code>vim config/_APPCONFIG.class.php</code></p>\n<p><a href=\"http://authorizedclone.com/bbsoop/\">BBSOOP</a>.</p>\n" );
}
// handles DB operations/transactions, base class for database tables
require ( "classes/_IMGBOARD.class.php" );

function __autoload ( $class_name ) {
	$class_name = preg_replace ( TABLE_STRING_PATTERN, '', $class_name );
	if ( file_exists ( BASE_PATH . THEME_PATH . "models/${class_name}.class.php" ) ) {
		// if the class exists in the theme, load it up
		require ( BASE_PATH . THEME_PATH . "models/${class_name}.class.php" );
	} else {
		return false;
	}
}

/* ALWAYS ON version
// sub classes
$models = array (
	'category', 
	'media', 
	'remote_address', 
	'reply', 
	'setting', 
	'topic', 
	'user_account' 
);
foreach ( $models as $model ) {
	require ( BASE_PATH . THEME_PATH . "models/$model.class.php" );
}
*/
?>