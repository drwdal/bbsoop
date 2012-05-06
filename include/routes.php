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

// DEFAULTS
global $controller, $action, $ID, $fragment, $extra, $board_name, $original_URI;
$controller = preg_replace ( SIMPLE_ASCII_STRING_PATTERN, '', ( string ) $_GET['controller'] );
$action = preg_replace ( SIMPLE_ASCII_STRING_PATTERN, '', ( string ) ( ( array_key_exists ( 'action', $_GET ) ) ? $_GET['action'] : '' ) );
$ID = preg_replace ( SIMPLE_ASCII_STRING_PATTERN, '', ( string ) ( ( array_key_exists ( 'ID', $_GET ) ) ? $_GET['ID'] : '' ) );
$fragment = preg_replace ( SIMPLE_ASCII_STRING_PATTERN, '', ( string ) ( ( array_key_exists ( 'fragment', $_GET ) ) ? $_GET['fragment'] : '' ) );
$extra = preg_replace ( SIMPLE_ASCII_STRING_PATTERN, '', ( string ) ( ( array_key_exists ( 'extra', $_GET ) ) ? $_GET['extra'] : '' ) );
$original_URI = preg_replace ( "/^" . str_replace ( '/', '\/', BASE_URI ) . "/", '', $_SERVER['REQUEST_URI'] );

// BOARDS
if ( isset ( $_GET['board_name'] ) && ! empty ( $_GET['board_name'] ) ) {
	$board_name = preg_replace ( SIMPLE_ASCII_STRING_PATTERN, '', ( string ) $_GET['board_name'] );
}
?>