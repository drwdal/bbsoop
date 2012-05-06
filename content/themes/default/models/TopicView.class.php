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

class TopicView extends _IMGBOARD {
	/* CONSTANTS
	================================================== */
	const table = 'topics_views';
	
	/* PROPERTIES
	================================================== */
	public		$user_ID;
	public		$topic_ID;
	public		$last_seen;

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
	
	/* PROPERTY-LEVEL FUNCTIONS
	================================================== */
	public function table ( ) {
		return self::table;
	}

	/* RELATED RECORDS
	================================================== */

	/* PRIVATE
	================================================== */
	private function apply_class_validations ( ) {
		// validate the content of each property
		$this->last_seen =	$this->validate_datetime ( $this->last_seen, TRUE );
		$this->ID =		$this->validate_integer ( $this->ID, TRUE, TRUE );
		$this->user_ID =	$this->validate_integer ( $this->user_ID, TRUE, TRUE );
		$this->topic_ID =	$this->validate_integer ( $this->topic_ID, TRUE, TRUE );
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