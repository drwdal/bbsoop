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

class RemoteAddress extends _IMGBOARD {
	/* CONSTANTS
	================================================== */
	const table = 'remote_addresses';
	
	/* PROPERTIES
	================================================== */
	protected	$remote_address;
	public		$type;
	public		$first_seen;
	public		$last_seen;
	public		$host_name;
	public		$TOR;
	public		$proxy;
	public		$permission_to_view;
	public		$permission_to_register;
	public		$permission_to_post;
	public		$permission_to_search;
	public		$users_count;
	public		$replies_count;
	public		$topics_count;
	public		$media_count;
	public		$bulletins_count;
	public		$search_count;

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
	public function table ( ) {
		return self::table;
	}
	public function remote_address ( ) {
		return $this->remote_address;
	}
	public function URI ( ) {
		return BASE_URI . 'admin/remote_address/' . $this->ID ( );
	}

	/* PRIVATE
	================================================== */
	private function apply_class_validations ( ) {
		// validate the content of each property
		$this->ID =			$this->validate_integer ( $this->ID, TRUE, TRUE );
		$this->first_seen =		$this->validate_datetime ( $this->first_seen, TRUE );
		$this->last_seen =		$this->validate_datetime ( $this->last_seen, TRUE );
		$this->host_name =		$this->validate_string ( $this->host_name, TRUE, 255 );
		$this->TOR =			$this->validate_integer ( $this->TOR, FALSE, TRUE );
		$this->proxy =			$this->validate_integer ( $this->proxy, FALSE, TRUE );
		$this->permission_to_view =	$this->validate_integer ( $this->permission_to_view, FALSE, TRUE );
		$this->permission_to_register =	$this->validate_integer ( $this->permission_to_register, FALSE, TRUE );
		$this->permission_to_post =	$this->validate_integer ( $this->permission_to_post, FALSE, TRUE );
		$this->permission_to_search =	$this->validate_integer ( $this->permission_to_search, FALSE, TRUE );
		$this->users_count =		$this->validate_integer ( $this->users_count, FALSE, TRUE );
		$this->replies_count =		$this->validate_integer ( $this->replies_count, FALSE, TRUE );
		$this->topics_count =		$this->validate_integer ( $this->topics_count, FALSE, TRUE );
		$this->media_count =		$this->validate_integer ( $this->media_count, FALSE, TRUE );
		$this->bulletins_count =	$this->validate_integer ( $this->bulletins_count, FALSE, TRUE );
		$this->search_count =		$this->validate_integer ( $this->search_count, FALSE, TRUE );
		return true;
		// TODO: return human-readable errors?
	}
	private function apply_special_validations ( ) {
		// validate the content of each property against the APP settings
		return true;
	}


}

?>