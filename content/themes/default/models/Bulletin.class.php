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

class Bulletin extends _IMGBOARD {
	/* CONSTANTS
	================================================== */
	const table = 'bulletins';
	
	/* PROPERTIES
	================================================== */
	public		$status;
	public		$user_ID;
	public		$body;
	public		$partial_cache;

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
	
	/* PROPERTY-LEVEL FUNCTIONS
	================================================== */
	public function table ( ) {
		return self::table;
	}

	/* RELATED RECORDS
	================================================== */
	public function user_account ( ) {
		return UserAccount::find ( ( int ) $this->user_ID );
	}

	/* PRIVATE
	================================================== */
	private function apply_class_validations ( ) {
		// validate the content of each property
		$this->ID =		$this->validate_integer ( $this->ID, TRUE, TRUE );
		$this->created_at =	$this->validate_datetime ( $this->created_at, TRUE );
		$this->updated_at =	$this->validate_datetime ( $this->updated_at, TRUE );
		$this->user_ID =	$this->validate_integer ( $this->user_ID, FALSE );
		$this->body =		$this->validate_string ( $this->body, FALSE );
		$this->partial_cache =	$this->validate_string ( $this->partial_cache, FALSE );
		$this->status =		$this->validate_string ( $this->status, FALSE );
		return true;
		// TODO: return human-readable errors?
	}
	private function apply_special_validations ( ) {
		global $errors;
		// validate the content of each property against the APP settings
		$valid = 1;
		if ( defined ( 'BULLETIN_BODY_CHARACTERS_MINIMUM' ) ) {
			$my_length = strlen ( $this->body );
			if ( $my_length < BULLETIN_BODY_CHARACTERS_MINIMUM ) {
				$errors[] = 'Your bulletin is too short. It must contain at least ' . number_format ( BULLETIN_BODY_CHARACTERS_MINIMUM ) . ' character' . ( ( BULLETIN_BODY_CHARACTERS_MINIMUM != 1 ) ? 's' : '') . '.';
				$valid = 0;
			}
		}
		if ( defined ( 'BULLETIN_BODY_CHARACTERS_MAXIMUM' ) ) {
			$my_length = strlen ( $this->body );
			if ( $my_length > BULLETIN_BODY_CHARACTERS_MAXIMUM ) {
				$too_long = $my_length - BULLETIN_BODY_CHARACTERS_MAXIMUM;
				$errors[] = 'Your bulletin is ' . number_format ( $too_long ) . ' character' . ( ( $too_long != 1 ) ? 's' : '' ) . " too long. The limit is " . number_format ( BULLETIN_BODY_CHARACTERS_MAXIMUM ) . '.';
				$valid = 0;
			}
		}
		if ( $valid == 1 ) {
			return true;
		}
		return false;
	}


}

?>