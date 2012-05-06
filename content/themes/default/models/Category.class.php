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

class Category extends _IMGBOARD {
	/* CONSTANTS
	================================================== */
	const table = 'categories';
	// TODO: register this class with the parent so there is a convenient list of available classes; this will be useful to build relations in a flexible and DRY manner
	// TODO: use the class list to improve the utility of Setting by building something like :record_select in Rails’ ActiveScaffold
	//IMGBOARD_class_list ( get_class ( $this ) );
	
	/* PROPERTIES
	================================================== */
	public		$name;
	public		$description;
	public		$visible_to;
	public		$replies_count;
	public		$topics_count;
	public		$media_count;

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
	public function update_counter ( $field, $value ) {
		return parent::update_counter ( self::table, $field, $value );
	}
	
	/* PROPERTY-LEVEL FUNCTIONS
	================================================== */
	public function table ( ) {
		return self::table;
	}
	public function name_URI ( ) {
		return urlencode ( preg_replace ( "/[\.,]/", '', str_replace ( ' ', '_',  strtolower ( $this->name ) ) ) );
	}
	public function URI ( ) {
		return BASE_URI . 'board/' . $this->name_URI ( ) . '/';
	}

	/* RELATED RECORDS
	================================================== */
	public function topics ( ) {
		return Topic::find ( array ( 'conditions' => array ( "`category_ID` = '%u' AND `status` = 'published'", $this->ID ( ) ), 'order' => 'ID ASC' ) );
	}

	/* PRIVATE
	================================================== */
	private function apply_class_validations ( ) {
		// validate the content of each property
		$this->ID =		$this->validate_integer ( $this->ID, TRUE, TRUE );
		$this->created_at =	$this->validate_datetime ( $this->created_at, TRUE );
		$this->updated_at =	$this->validate_datetime ( $this->updated_at, TRUE );
		$this->name =		$this->validate_string ( $this->name, TRUE, 0, TRUE );
		$this->description =	$this->validate_string ( $this->description, TRUE, 0, TRUE );
		$this->visible_to =	$this->validate_integer ( $this->status, TRUE );
		$this->topics_count =	$this->validate_integer ( $this->topics_count, TRUE, TRUE );
		$this->replies_count =	$this->validate_integer ( $this->replies_count, TRUE, TRUE );
		$this->media_count =	$this->validate_integer ( $this->media_count, TRUE, TRUE );
		return true;
		// TODO: return human-readable errors?
	}
	private function apply_special_validations ( ) {
		global $errors;
		$valid = 1;
		if ( $valid == 1 ) {
			return true;
		}
		return false;
	}


}

?>