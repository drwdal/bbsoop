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

/* PHP CONFIG AND INITIALIZERS
================================================== */
// domain used to validate the script environment and redirect locations
define ( 'DOMAIN', 'authorizedclone.com' );

// site title
define ( 'SITE_TITLE', 'BBSOOP' );

// application character set
define ( 'APP_CHARSET', 'UTF-8' );

// which application theme to use
define ( 'THEME', 'default' );

// session name
// multiple installations can be on the same domain if these are unique
ini_set ( 'session.name', 'SID' );

// time (in seconds) to store session data on the server
// default is 1440 (24 minutes); shorter times improve user privacy; longer times increase moderation control
ini_set ( 'session.gc_maxlifetime', 28800 );

// memory limit applied to PHP
ini_set ( 'memory_limit', '4M' );

// limits extra HTTP methods
ini_set ( 'http.request.methods.allowed', "GET,POST" );


/* DO NOT CHANGE THESE
================================================== */
// figures out the script install location relative to the server filesystem root
// do not change
define ( 'BASE_PATH', preg_replace ( "/[^\/]*$/", '', $_SERVER["SCRIPT_FILENAME"] ) );

// figures out he script install location relative to the webserver document root
// do not change
define ( 'BASE_URI', preg_replace ( "/[^\/]*$/", '', $_SERVER["SCRIPT_NAME"] ) );

// standardizes time across various hosts
// do not change
ini_set ( 'date.timezone', 'GMT' );

// reduce threat of XSS through JavaScript
ini_set ( 'session.cookie_httponly', 1 );

// turn off silly/useless feature
ini_set ( 'magic_quotes_gpc', 0 );
ini_set ( 'magic_quotes_runtime', 0 );


?>