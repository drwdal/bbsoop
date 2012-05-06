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


class _APPCONFIG extends _DBCONNECT {
	// this class stores the critical application configurations
	// it holds sensitive information like usernames, paswords, and keys
	// but it is not foolproof!
	// if somebody has access to the filesystem and permissions to read this file
	// they can view all of the information
	// PHP can also be tricked into showing these values
	// so use good coding practices and sanitize user input

	/* START EDITING HERE
	================================================== */
	// database server; typically localhost
	public static $db_host		= 'localhost';
	// username to connect to database server with
	private static $db_user		= 'root';
	// password for username
	private static $db_password	= '';
	// name of database on server
	// utf8_general_ci is the recommended collation
	public static $db_name		= 'bbsoop';
	// character set for connections; utf8 is best
	public static $db_charset	= 'utf8';
	
	// DO NOT LOSE THIS! BACK IT UP!
	// SET IT ONLY WHEN INITIALLY BUILDING THIS APP
	// CHANGING IT ON AN ESTABLISHED SITE WILL DISABLE ACCESS TO IMPORTANT FIELDS
	// it is the key to verify encrypted database fields (i.e. passwords)
	private static $secure_auth_key	= 'secure auth key';
	public static $auth_key = 'auth key';
	
	// changing this (should) invalidate all logged in sessions // TODO: implement and test
	private static $nonce_key	= 'nonce key';
	
	// prefix for table names; multiple IMGBOARD apps can exist in the same database if they have different table prefixes
	public static $table_prefix	= '';
	
	/* STOP EDITING HERE
	================================================== */
	
	// TODO: fix this error — Notice: Only variable references should be returned by reference in /var/www/bbsoop/config/_APPCONFIG.class.php on line 69 
	public function start_app ( ) {
		self::connect ( array ( 'db_host' => self::$db_host, 'db_user' => self::$db_user, 'db_password' => self::$db_password, 'db_name' => self::$db_name, 'db_charset' => self::$db_charset ) );
	}
	public function nonce_generate_hash ( ) {
		if ( defined ( 'NONCE_LIFETIME' ) && NONCE_LIFETIME > 0 ) {
			$time = ceil ( time ( ) / NONCE_LIFETIME ) * NONCE_LIFETIME;
			if ( defined ( SESSION_ID ) && SESSION_ID != '' ) {
				return sha1 ( date ( 'Y-m-d H:i', $time ) . ':' . $_SERVER['REMOTE_ADDR'] . SESSION_ID . ':' . self::$nonce_key );
			} else {
				return sha1 ( date ( 'Y-m-d H:i', $time ) . ':' . $_SERVER['REMOTE_ADDR'] . ':' . self::$nonce_key );
			}
		}
	}
	public function salted_sha1 ( $input ) {
		return ( string ) sha1 ( self::$secure_auth_key . $input );
	}
	public function default_keys ( ) {
		$error_count = 0;
		if ( self::$secure_auth_key == 'secure auth key' ) {
			$error_count++;
		}
		if ( self::$auth_key == 'auth key' ) {
			$error_count++;
		}
		if ( self::$nonce_key == 'nonce key' ) {
			$error_count++;
		}
		return $error_count;
	}
}

?>
