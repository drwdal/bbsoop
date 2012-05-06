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

class Setting extends _IMGBOARD {
	/* CONSTANTS
	================================================== */
	const table = 'settings';
	
	/* PROPERTIES
	================================================== */
	public		$type;		// varchar(30) not NULL default 'text'
	public		$name;		// varchar(60) not NULL
	public		$category;	// varchar(60) allow NULL default NULL
	public		$value;		// varchar(255) not NULL
	public		$default;	// varchar(255) allow NULL default NULL
	public		$description;	// varchar(255) allow NULL default NULL
	public		$option_values;	// text allow NULL default NULL
	public		$option_labels;	// text allow NULL default NULL
	public		$order_by;	// int(11) not NULL default 0
	public		$load_at_startup;// int(11) not NULL default 0
	public		$editable;	// smallint(1) not NULL default 1
	
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
	public static function load ( $arguments = NULL ) {
		global $RUNTIME_OPTIONS;
		$settings = self::find ( $arguments );
		if ( is_array ( $settings ) && count ( $settings ) > 0 ) {
			foreach ( $settings as $setting ) {
				if ( ! defined ( $setting->name ) ) {
					define ( $setting->name, $setting->value );
				}
				$RUNTIME_OPTIONS[$setting->name] = $setting->value;
			}
		} else if ( is_object ( $settings ) ) {
			define ( $settings->name, $settings->value );
			$RUNTIME_OPTIONS[$settings->name] = $settings->value;
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
	public function save ( ) {
		if ( $this->readonly == 0 && $this->validates ( ) ) {
			global $queries, $DB, $TRACKER;
			// attempt to find self
			$queries[] = sprintf ( "SELECT ID, value, order_by FROM %s WHERE ID = '$this->ID' LIMIT 1", self::table );
			$result = $DB->query ( end ( $queries ) );
			$TRACKER['queries_select']++;
			// if self found then update
			if ( $result->num_rows > 0 ) {
				$my_obj_previous = $result->fetch_object ( );
				$result->close ( );
				if ( $this->editable == 1 ) {
					// full update
					$queries[] = sprintf ( "UPDATE %s SET"
						. "`updated_at` = NOW() AND "
						. "`type` = '" . $DB->real_escape_string ( $this->type ) . "', "
						. "`name` = '" . $DB->real_escape_string ($this->name ) . "', "
						. "`category` = '" . $DB->real_escape_string ($this->category ) . "', "
						. "`value` = '" . $DB->real_escape_string ( $this->value ) . "', "
						. "`default` = '" . $DB->real_escape_string ( $this->default ) . "', "
						. "`description` = '" . $DB->real_escape_string ( $this->description ) . "', "
						. "`option_values` = '" . $DB->real_escape_string ( $this->option_values ) . "', "
						. "`option_labels` = '" . $DB->real_escape_string ( $this->option_labels ) . "', "
						. "`order_by` = '" . $DB->real_escape_string ( $this->order_by ) . "', "
						. "`load_at_startup` = '" . $DB->real_escape_string ( $this->load_at_startup ) . "', "
						. "WHERE `ID` = '" . $DB->real_escape_string ( $this->ID ) . "' "
						. "LIMIT 1", self::table );
					if ( $DB->query ( end ( $queries ) ) ) {
						$TRACKER['queries_update']++;
						return true;
					}
				} else {
					// limited update; only run if value or order_by have changed
					if ( $this->value != $my_obj_previous->value || ( isset ( $this->order_by ) && $this->order_by != $my_obj_previous->order_by ) ) {
						$queries[] = sprintf ( "UPDATE %s SET "
							. "`updated_at` = NOW(), "
							. "`value` = '" . $DB->real_escape_string ( $this->value ) . "', "
							. "`order_by` = '" . $DB->real_escape_string ( $this->order_by ) . "' "
							. "WHERE `ID` = " . $DB->real_escape_string ( $this->ID ) . " "
							. "LIMIT 1", self::table );
						if ( $DB->query ( end ( $queries ) ) ) {
							$TRACKER['queries_update']++;
						}
					}
					return true;
				}
			} else {
				// did not find self in DB, make INSERT query
				$my_query = sprintf ( "INSERT INTO %s "
					. "( `created_at`, `type`, `name`, `category`, `value`, `default`, `description`, `option_values`, `option_labels`, `order_by`, `load_at_startup` ) "
					. "VALUES ( NOW(), ", self::table );
				// handle nulls
				if ( ! isset ( $this->type ) ) {
					$my_query .= "NULL, ";
				} else {
					$my_query .= "'" . $DB->real_escape_string ( $this->type ) . "', ";
				}
				if ( ! isset ( $this->name ) ) {
					$my_query .= "NULL, ";
				} else {
					$my_query .= "'" . $DB->real_escape_string ( $this->name ) . "', ";
				}
				if ( ! isset ( $this->category ) ) {
					$my_query .= "NULL, ";
				} else {
					$my_query .= "'" . $DB->real_escape_string ( $this->category ) . "', ";
				}
				if ( ! isset ( $this->value ) ) {
					$my_query .= "NULL, ";
				} else {
					$my_query .= "'" . $DB->real_escape_string ( $this->value ) . "', ";
				}
				if ( ! isset ( $this->default ) ) {
					$my_query .= "NULL, ";
				} else {
					$my_query .= "'" . $DB->real_escape_string ( $this->default ) . "', ";
				}
				if ( ! isset ( $this->description ) ) {
					$my_query .= "NULL, ";
				} else {
					$my_query .= "'" . $DB->real_escape_string ( $this->description ) . "', ";
				}
				if ( ! isset ( $this->option_values ) ) {
					$my_query .= "NULL, ";
				} else {
					$my_query .= "'" . $DB->real_escape_string ( $this->option_values ) . "', ";
				}
				if ( ! isset ( $this->option_labels ) ) {
					$my_query .= "NULL, ";
				} else {
					$my_query .= "'" . $DB->real_escape_string ( $this->option_labels ) . "', ";
				}
				if ( ! isset ( $this->order_by ) ) {
					$my_query .= "NULL, ";
				} else {
					$my_query .= "'" . $DB->real_escape_string ( $this->order_by ) . "', ";
				}
				if ( ! isset ( $this->load_at_startup ) ) {
					$my_query .= "NULL )";
				} else {
					$my_query .= "'" . $DB->real_escape_string ( $this->load_at_startup ) . "' )";
				}
				$queries[] = $my_query;
				if ( $DB->query ( end ( $queries ) ) ) {
					$TRACKER['queries_insert']++;
					return true;
				} else { echo $DB->error; }
			}
			// TODO: more elegant database error recovery?
			header ( 'Status: 400', TRUE );
			header ( "Content-Type: text/plain" );
			die ( end ( $queries ) . "\nERROR: $DB->error" );
			return false;
		}
		// did not validate
		return false;
	}

	/* PROPERTY-LEVEL FUNCTIONS
	================================================== */
	public function table ( ) {
		return self::table;
	}

	/* PRIVATE
	================================================== */
	private function apply_class_validations ( ) {
		// validate the content of each property
		$this->ID =		$this->validate_integer ( $this->ID, TRUE, TRUE );
		$this->created_at =	$this->validate_datetime ( $this->created_at, TRUE );
		$this->updated_at =	$this->validate_datetime ( $this->updated_at, TRUE );
//		$this->name =		$this->validate_string ( $this->name, FALSE, 60, TRUE );
//		$this->category =	$this->validate_string ( $this->category, TRUE, 60, TRUE );
		switch ( $this->type ) {
			case 'boolean':
				$this->value =		$this->validate_boolean ( $this->value, TRUE );
				$this->default =	$this->validate_boolean ( $this->default, TRUE );
				break;
			case 'integer':
				$this->value =		$this->validate_integer ( $this->value, TRUE, TRUE );
				$this->default =	$this->validate_integer ( $this->default, TRUE, TRUE );
				break;
			case 'float':
				$this->value =		$this->validate_float ( $this->value, TRUE, TRUE );
				$this->default =	$this->validate_float ( $this->default, TRUE, TRUE );
				break;
			case 'datetime':
				$this->value =		$this->validate_datetime ( $this->value, TRUE );
				$this->default =	$this->validate_datetime ( $this->default, TRUE );
				break;
			case 'date':
				$this->value =		$this->validate_datetime ( $this->value, TRUE );
				$this->default =	$this->validate_datetime ( $this->default, TRUE );
				break;
			default:
				$this->value =		$this->validate_string ( $this->value, TRUE, 255, TRUE );
				$this->default =	$this->validate_string ( $this->default, TRUE, 255, TRUE );
				break;
		}
//		$this->description =	$this->validate_string ( $this->description, TRUE, 255, TRUE );
//		$this->option_values =	$this->validate_string ( $this->option_values, TRUE );
//		$this->option_labels =	$this->validate_string ( $this->option_labels, TRUE );
		$this->editable =	$this->validate_boolean ( $this->editable, TRUE );
		$this->order_by =	$this->validate_integer ( $this->order_by, TRUE );
		$this->load_at_startup =$this->validate_integer ( $this->load_at_startup, TRUE );
		return true;
		// TODO: return human-readable errors?
	}
	private function apply_special_validations ( ) {
		// validate the content of each property against the APP settings
		$this->name =		strtoupper ( preg_replace ( TABLE_STRING_PATTERN, '', str_replace ( ' ', '_', $this->name ) ) );
		$this->category =	strtoupper ( preg_replace ( TABLE_STRING_PATTERN, '', str_replace ( ' ', '_', $this->category ) ) );
		return true;
	}
}

?>