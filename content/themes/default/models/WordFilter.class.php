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

class WordFilter extends _IMGBOARD {
	/* CONSTANTS
	================================================== */
	const table = 'wordfilters';
	
	/* PROPERTIES
	================================================== */
	public		$mode;
	public		$method;
	public		$pattern;
	public		$category;
	public		$description;
	public		$active;
	public		$action;
	public		$replacement;
	public		$user_message;
	public		$user_level_exempt;

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
	// none?

	/* RELATED RECORDS
	================================================== */
	// none

	/* PRIVATE
	================================================== */
	private function apply_class_validations ( ) {
		// validate the content of each property
		$this->ID =			$this->validate_integer ( $this->ID, TRUE, TRUE );
		$this->created_at =		$this->validate_datetime ( $this->created_at, TRUE );
		$this->updated_at =		$this->validate_datetime ( $this->updated_at, TRUE );
		$this->mode =			$this->validate_string ( $this->mode, TRUE );
		$this->method =			$this->validate_string ( $this->method, TRUE );
		$this->pattern =		$this->validate_integer ( $this->pattern, TRUE, TRUE );
		$this->category =		$this->validate_string ( $this->category, TRUE );
		$this->description =		$this->validate_string ( $this->description, FALSE );
		$this->action =			$this->validate_string ( $this->action, FALSE );
		$this->replacement =		$this->validate_string ( $this->replacement, FALSE );
		$this->user_message =		$this->validate_string ( $this->user_message, FALSE );
		$this->user_level_exempt =	$this->validate_integer ( $this->user_level_exempt, FALSE );
		return true;
		// TODO: return human-readable errors?
	}
	private function apply_special_validations ( ) {
		// validate the content of each property against the APP settings
		return true;
	}


}

?>