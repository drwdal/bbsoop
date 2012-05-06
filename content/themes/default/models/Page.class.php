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

class Page extends _IMGBOARD {
	/* CONSTANTS
	================================================== */
	const table = 'pages';
	
	/* PROPERTIES
	================================================== */
	public		$status;
	public		$title;
	public		$URI;
	public		$body;
	public		$parent_ID;
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
	public function title_URI ( ) {
		$my_title_URI = urlencode ( preg_replace ( "/[\.,]/", '', str_replace ( ' ', '_', strtolower ( $this->title ) ) ) );
		if ( ! empty ( $my_title_URI ) ) {
			return $my_title_URI;
		}
		return false;
	}
	public function URI ( ) {
		if ( isset ( $this->URI ) && ! empty ( $this->URI ) && $this->URI != 'NULL' ) {
			$my_URI = BASE_URI . $this->URI;
		} else {
			$my_URI = BASE_URI . 'page/view/' . $this->ID . '/' . $this->title_URI ( );
		}
		if ( ! empty ( $my_URI ) ) {
			return $my_URI;
		}
		return false;
	}

	/* RELATED RECORDS
	================================================== */
	public function parent_page ( ) {
		if ( ! isset ( $this->parent_ID ) ) {
			return false;
		}
		return Page::find ( ( int ) $this->parent_ID );
	}

	/* PRIVATE
	================================================== */
	private function apply_class_validations ( ) {
		// validate the content of each property
		$this->ID =		$this->validate_integer ( $this->ID, TRUE, TRUE );
		$this->created_at =	$this->validate_datetime ( $this->created_at, TRUE );
		$this->updated_at =	$this->validate_datetime ( $this->updated_at, TRUE );
		$this->parent_ID =	$this->validate_integer ( $this->parent_ID, TRUE, TRUE );
		$this->URI =		$this->validate_string ( $this->URI, TRUE, 0, TRUE );
		$this->title =		$this->validate_string ( $this->title, FALSE, 0, TRUE );
		$this->body =		$this->validate_string ( $this->body, FALSE );
		$this->status =		$this->validate_string ( $this->status, FALSE, 0, TRUE );
		return true;
		// TODO: return human-readable errors?
	}
	private function apply_special_validations ( ) {
		// validate the content of each property against the APP settings
		if ( isset ( $this->URI ) && ! empty ( $this->URI ) ) {
			$this->URI = str_replace ( '%2F', '/', urlencode ( preg_replace ( "/[\.,]/", '', str_replace ( ' ', '-', $this->URI) ) ) );
		}
		return true;
	}


}

?>