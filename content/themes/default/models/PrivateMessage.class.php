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

class PrivateMessage extends _IMGBOARD {
	/* CONSTANTS
	================================================== */
	const table = 'private_messages';
	
	/* PROPERTIES
	================================================== */
	public		$expires_at;
	public		$from_user_ID;
	public		$to_user_ID;
	public		$body;
	public		$conversation_ID;
	public		$is_read;
	public		$anonymous;
	public		$reply_allowed;

	/* CONSTRUCTOR
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
	public function reply_URI ( ) {
		return BASE_URI . 'private_messages/new/' . $this->ID ( );
	}
	public function quote_URI ( ) {
		return BASE_URI . 'private_messages/new/' . $this->ID ( ) . '/quote/';
	}
	
	/* PROPERTY-LEVEL FUNCTIONS
	================================================== */
	public function table ( ) {
		return self::table;
	}

	/* RELATED RECORDS
	================================================== */
	public function conversation_thread ( ) {
		if ( ! isset ( $this->conversation_ID ) || $this->conversation_ID == 0 ) {
			return PrivateMessage::find ( array ( 'conditions' => "`conversation_ID` = ID", 'order' => 'created_at ASC' ) );
		}
	}

	/* PRIVATE
	================================================== */
	private function apply_class_validations ( ) {
		// validate the content of each property
		$this->ID =		$this->validate_integer ( $this->ID, TRUE, TRUE );
		$this->created_at =	$this->validate_datetime ( $this->created_at, TRUE );
		$this->expires_at =	$this->validate_datetime ( $this->expires_at, TRUE );
		$this->from_user_ID =	$this->validate_integer ( $this->from_user_ID, TRUE, TRUE );
		$this->to_user_ID =	$this->validate_integer ( $this->to_user_ID, TRUE, TRUE );
		$this->conversation_ID =$this->validate_integer ( $this->conversation_ID, TRUE, TRUE );
		$this->body =		$this->validate_string ( $this->body, TRUE );
		$this->is_read =	$this->validate_boolean ( $this->is_read );
		$this->anonymous =	$this->validate_boolean ( $this->anonymous );
		$this->reply_allowed =	$this->validate_boolean ( $this->reply_allowed );
		return true;
		// TODO: return human-readable errors?
	}
	private function apply_special_validations ( ) {
		global $errors;
		$valid = 1;
		// validations go hurr
		if ( $valid == 1 ) {
			return true;
		}
		return false;
	}


}

?>