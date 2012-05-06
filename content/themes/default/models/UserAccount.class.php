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

class UserAccount extends _IMGBOARD {
	/* CONSTANTS
	================================================== */
	const table = 'user_accounts';
	
	/* PROPERTIES
	================================================== */
	public		$username;
	public		$password;
	public		$temp_password;
	public		$remote_address;
	public		$email_address;
	public		$type;
	public		$status;
	public		$session_ID;
	public		$internal_notes;
	public		$replies_count;
	public		$topics_count;
	public		$search_count;
	public		$bulletins_count;
	public		$replies_favorites_count;
	public		$replies_favorited_count;
	public		$topics_favorites_count;
	public		$topics_favorited_count;
	public		$media_count;
	public		$reports_count;
	public		$unread_private_messages_count;
	public		$ban_expires;
	public		$ban_reason;

	/* CONSTUCTOR
	================================================== */
	// inherited

	/* RECORD-LEVEL FUNCTIONS
	================================================== */
	public static function find ( $arguments = NULL ) {
		if ( $results = parent::find ( self::table, $arguments ) ) {
			if ( is_int ( key ( $results ) ) ) {
				$count = count ( $results );
				for ( $i = 0; $i < $count; $i++ ) {
					$results[$i] = new self ( $results[$i] );
				}
				return $results;
			} else {
				return new self ( $results );
			}
		}
	}
	public function validate ( ) {
		if ( $this->apply_class_validations ( ) && $this->apply_special_validations ( ) ) {
			$this->_hash = var_to_hash ( $this );
			return true;
		} else {
			// TODO: return human-readable errors?
			return false;
		}
	}
	public function create ( ) {
		return parent::create ( self::table );
	}
	public function update ( $fields ) {
		return parent::update ( self::table, $fields );
	}
	public function update_counter ( $field, $value ) {
		return parent::update_counter ( self::table, $field, $value );
	}
	
	/* PROPERTY-LEVEL FUNCTIONS
	================================================== */
	// these are getters
	public function table ( ) {
		return self::table;
	}
	public function type ( ) {
		return ( int ) $this->type;
	}
	public function status ( ) {
		return ( string ) $this->status;
	}
	public function username ( ) {
		return ( string ) $this->username;
	}
	public function ban_expires ( $format = MYSQL_DATETIME_FORMAT ) {
		return parent::datetime_to_string ( $this->ban_expires, $format );
	}
	public function remote_address ( ) {
		if ( isset ( $this->remote_address ) && ! empty ( $this->remote_address ) ) {
			return RemoteAddress::find ( array ( 'first', 'conditions' => array ( "remote_address = '%s'", $this->remote_address ) ) );
		}
		return false;
	}
	public function URI ( ) {
		return BASE_URI . 'admin/user/' . $this->user_ID ( );
	}

	/* PRIVATE
	================================================== */
	private function apply_class_validations ( ) {
		// validate the content of each property
		$this->ID =			$this->validate_integer ( $this->ID, TRUE, TRUE );
		$this->created_at =		$this->validate_datetime ( $this->created_at, TRUE );
		$this->updated_at =		$this->validate_datetime ( $this->updated_at, TRUE );
		$this->username =		$this->validate_string ( $this->username, FALSE, 60, TRUE );
		$this->password =		$this->validate_string ( $this->password, FALSE, 60, TRUE );
		$this->temp_password =		$this->validate_string ( $this->temp_password, TRUE, 60, TRUE );
		$this->remote_address =		$this->validate_string ( $this->remote_address, TRUE, 255, TRUE );
		$this->email_address =		$this->validate_string ( $this->email_address, TRUE, 125, TRUE );
		$this->type =			$this->validate_string ( $this->type, TRUE, TRUE );
//		$this->status =			$this->validate_string ( $this->status, TRUE, 30, TRUE );
//		$this->internal_notes =		$this->validate_string ( $this->internal_notes, TRUE );
		$this->session_ID =		$this->validate_string ( $this->session_ID, TRUE, 60, TRUE );
		$this->replies_count =		$this->validate_integer ( $this->replies_count, FALSE );
		$this->topics_count =		$this->validate_integer ( $this->topics_count, FALSE );
		$this->search_count =		$this->validate_integer ( $this->search_count, FALSE );
		$this->media_count =		$this->validate_integer ( $this->media_count, FALSE );
		$this->reports_count =		$this->validate_integer ( $this->reports_count, FALSE );
		$this->replies_favorites_count =$this->validate_integer ( $this->replies_favorites_count, FALSE );
		$this->replies_favorited_count =$this->validate_integer ( $this->replies_favorited_count, FALSE );
		$this->topics_favorites_count =	$this->validate_integer ( $this->topics_favorites_count, FALSE );
		$this->topics_favorited_count =	$this->validate_integer ( $this->topics_favorited_count, FALSE );
		$this->bulletins_count =	$this->validate_integer ( $this->bulletins_count, FALSE, TRUE );
		$this->unread_private_messages_count =	$this->validate_integer ( $this->unread_private_messages_count, FALSE, TRUE );
		$this->ban_expires =		$this->validate_datetime ( $this->ban_expires, TRUE );
//		$this->ban_reason =		$this->validate_string ( $this->ban_reason, TRUE );
		return true;
		// TODO: return human-readable errors?
	}
	private function apply_special_validations ( ) {
		// validate the content of each property against the APP settings
		return true;
	}


}

?>