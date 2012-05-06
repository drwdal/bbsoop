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

/* PATHS AND URIS
================================================== */
define ( 'THEME_PATH', 'content/themes/' . THEME . '/' );
define ( 'BASE_URL', 'http' . ( ( array_key_exists ( 'HTTPS', $_SERVER ) ) ? 's' : '' ) . '://' . DOMAIN . BASE_URI );
define ( 'BASE_ASSET_URI', BASE_URI . 'content/themes/' . THEME . '/assets/');

/* PATTERNS
================================================== */
// most of these work by matching characters that are excluded from the pattern’s set
define ( 'ALPHA_PATTERN', '/[^a-zA-Z]/' );
define ( 'ASCII_PATTERN', '/[^\x20-\x7E]/' );
// excludes all but a few common languages, punctuation sets, and includes the drawing chars and symbols
define ( 'UNICODE_RESTRICT_EXCLUSIVE', '/[^\x20-\x7E\xA0-\xFF\x{100}-\x{2AF}\x{0400}-\x{0482}\x{48A}-\x{4FF}}\x{0E00}-\x{0E7F}\x{0F00}-\x{0FFF}\x{1E00}-\x{1EFF}\x{1F00}-\x{1FFF}\x{2010}-\x{2027}\x{2030}-\x{205E}\x{2100}-\x{23FF}\x{2500}-\x{25FF}\x{2600}-\x{27FF}\x{2900}-\x{2BFF}\x{2E80}-\x{9FFF}]/u' );
// matches ZALGO-like stuff and zero-width chars
define ( 'UNICODE_RESTRICT_SPECIFIC', '/[\x{488}\x{489}\x{2B0}-\x{3BF}]/u' );
// table names in the database
define ( 'TABLE_STRING_PATTERN', '/[^a-zA-Z0-9_]/' );
define ( 'SIMPLE_ASCII_STRING_PATTERN', '/[^a-zA-Z0-9_\-\.\/]/' );
define ( 'SIMPLE_HEXIDECIMAL_PATTERN', '/[^a-f0-9]/' );
define ( 'INTEGER_PATTERN', '/[^0-9]/' );
define ( 'IP_PATTERN', '/[^a-f0-9:\.]/' );
define ( 'IPV6_PATTERN', '/[^a-f0-9:]/' );
define ( 'IP4_PATTERN', '/[^0-9\.]/' );
define ( 'MYSQL_DATETIME_FORMAT', "Y-m-d H:i:s" );

/* SETTINGS
================================================== */
// the minimum fields necessary to load and use a Setting
define ( 'MIN_SETTINGS_FIELDS', 'name, value, type' );
function SETTINGS_TYPES ( ) {
	// TODO: get this working so SETTINGS_TYPES() will return a list of the pseudo-types plus classes that relations can be built for (i.e. define Setting X as a link to Page Y)
	return array_merge ( $GLOBALS['IMGBOARD_class_list'], SETTINGS_PSEUDO_TYPES ( ) );
}
function SETTINGS_PSEUDO_TYPES ( ) {
	return array (
		'text', 
		'boolean', 
		'integer', 
		'float', 
		'datetime', 
		'date'
	);
}


?>