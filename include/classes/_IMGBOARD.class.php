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

class _IMGBOARD {
	/* PROPERTIES
	================================================== */
	protected	$ID;		// int(10) not NULL
	protected	$created_at;	// datetime not NULL
	public		$updated_at;	// datetime allow NULL default NULL
	private		$_hash;	// stores hash of already-validated object
	
	/* CONSTUCTOR
	================================================== */
	function __construct ( $values = NULL ) {
		// set properties from input array
		if ( is_array ( $values ) && count ( $values ) > 0 ) {
			$class_vars = get_class_vars ( get_class ( $this ) );
			foreach ( $values as $key => $value ) {
				if ( array_key_exists ( $key, $class_vars ) ) {
					$this->{$key} = $value;
				}
			}
			// validate properties
			// TODO: skip validation on load?
			// or add an "after construct" function that can optionally validate
			// $this->validate ( );
		}
	}

	/* RECORD-LEVEL FUNCTIONS
	================================================== */
	protected static function find ( $table, $arguments = NULL ) {
		if ( isset ( $table ) ) {
			global $DB, $queries, $TRACKER;
			// build query
			$return_array = 1;
			if ( is_int ( $arguments ) || array_key_exists ( 0, $arguments ) && is_int ( $arguments[0] ) ) {
				$my_query = "SELECT SQL_CACHE * FROM `" . $DB->real_escape_string ( $table ) . "`";
				if ( is_array ( $arguments ) ) {
					// find by IDs
					foreach ( $arguments as $key => $value ) {
						$arguments[$key] = ( int ) $DB->real_escape_string ( $value );
					}
					$my_query .= " WHERE `ID` = '" . implode ( "' OR `ID` = '", $arguments ) . "' LIMIT " . count ( $arguments );
				} else {
					// find by ID
					$my_query .= " WHERE `ID` = '" . $DB->real_escape_string ( ( int ) $arguments ) . "' LIMIT 1";
					$return_array = 0;
				}
			} else if ( is_string ( $arguments ) ) {
				switch ( $arguments ) {
					case 'all':
						$my_query = "SELECT SQL_CACHE * FROM `" . $DB->real_escape_string ( $table ) . "`";
						break;
					case 'first':
						$my_query = "SELECT SQL_CACHE * FROM `" . $DB->real_escape_string ( $table ) . "` LIMIT 0, 1";
						$return_array = 0;
						break;
				}
			} else {
				$my_query = 'SELECT SQL_CACHE ';
				// SELECT X
				if ( isset ( $arguments['select'] ) && ! empty ( $arguments['select'] ) ) {
					$my_query .= $arguments['select'];
				} else {
					$my_query .= '*';
				}
				// FROM table
				$my_query .= " FROM `" . $DB->real_escape_string ( $table ) . "`";
				// WHERE X
				if ( isset ( $arguments['conditions'] ) ) {
					if ( is_array ( $arguments['conditions'] ) ) {
						$my_query .= ' WHERE ' . $arguments['conditions'][0];
					} else {
						$my_query .= ' WHERE ' . $arguments['conditions'];
					}
				}
				// GROUP BY
				if ( isset ( $arguments['group'] ) && ! empty ( $arguments['group'] ) ) {
					// build GROUP BY
					$my_query .= ' GROUP BY ' . $arguments['group'];
				}
				// ORDER BY
				if ( isset ( $arguments['order'] ) && ! empty ( $arguments['order'] ) ) {
					// build ORDER BY
					$my_query .= ' ORDER BY ' . $arguments['order'];
				} else if ( isset ( $arguments['group'] ) && ! empty ( $arguments['group'] ) ) {
					// reduces CPU overhead
					$my_query .= ' ORDER BY NULL';
				}
				// LIMIT
				if ( array_key_exists ( 0, $arguments ) && $arguments[0] == 'first' ) {
					// limit 0, 1
					$my_query .= ' LIMIT 0, 1';
					$return_array = 0;
					// TODO: how do I do 'last' !?
				} else {
					if ( isset ( $arguments['limit'] ) && ! empty ( $arguments['limit'] ) ) {
						if ( isset ( $arguments['offset'] ) && ! empty ( $arguments['offset'] ) ) {
							$my_query .= ' LIMIT ' . $arguments['offset'] . ', ' . $arguments['limit'];
						} else {
							$my_query .= ' LIMIT ' . $arguments['limit'];
						}
					}
				}
			}
			// prepare query
			$conditions_count = count ( $arguments['conditions'] );
			if ( is_array ( $arguments['conditions'] ) && $conditions_count > 1 ) {
				// prepare for replacement
				$my_query = array ( $my_query );
				// sanitize each
				for ( $i = 1; $i < $conditions_count; $i++ ) {
					$my_query[] = $DB->real_escape_string ( $arguments['conditions'][$i] );
				}
				// replace each
				$my_query = call_user_func_array ( 'sprintf', $my_query );
			}
			// send query
			$queries[] = $my_query;
			$TRACKER['queries_select']++;
			if ( $result = $DB->query ( $my_query ) ) {
				if ( $result->num_rows > 0 ) {
					// get results
					$results = array ( );
					while ( $rowobj = $result->fetch_array ( MYSQLI_ASSOC ) ) {
						$results[] = $rowobj;
					}
					$result->close ( );
					if ( $return_array == 1 ) {
						return $results;
					} else {
						return $results[0];
					}
				} else {
					return false;
				}
			} else if ( DEBUG_LEVEL >= 1 ) {
				header ( 'Status: 500', TRUE );
				echo "<pre>" . print_r ( $queries, true ) . "<br /><br />\n";
				die ( sprintf ( "ERROR: %s", $DB->error ) );
			} else {
				return false;
			}
		}
	}
	public function validates ( ) {
		// validate the content of each property
		// skip validation if already validated
		if ( $this->_hash == var_to_hash ( $this ) || $this->validate ( ) ) {
			return true;
		}
		// TODO: return human-readable errors
		return false;
	}
	public function create ( $table ) {
		if ( $this->validates ( ) ) {
			global $DB, $queries, $TRACKER, $errors;
			// insert
			// get collection of vars
			$obj_vars = get_object_vars ( $this );
			// build reflector to compare with
			$obj_class = new ReflectionClass ( get_class ( $this ) );
			// collect vars to save
			$vars_to_save = array ( );
			foreach ( $obj_vars as $k => $v ) {
				if ( $obj_class->hasProperty ( $k ) && isset ( $this->{$k} ) && $k != 'bumped_at' ) {
					// exists and is accessible, add to query
					$vars_to_save[$k] = $v;
				}
			}
			if ( $obj_class->hasProperty ( 'bumped_at' ) ) {
				// Topic created at bump
				$my_query = sprintf ( "INSERT INTO `%s` (`created_at`, `bumped_at`, ", $DB->real_escape_string ( $table ) );
			} else if ( $obj_class->hasProperty ( 'first_seen' ) ) {
				// RemoteAddress
				$my_query = sprintf ( "INSERT INTO `%s` (`first_seen`, ", $DB->real_escape_string ( $table ) );
			} else if ( $obj_class->hasProperty ( 'last_seen' ) ) {
				// TopicView
				$my_query = sprintf ( "INSERT INTO `%s` (`last_seen`, ", $DB->real_escape_string ( $table ) );
			} else {
				$my_query = sprintf ( "INSERT INTO `%s` (`created_at`, ", $DB->real_escape_string ( $table ) );
			}
			foreach ( array_keys ( $vars_to_save ) as $k ) {
				$my_query .= sprintf ( "`%s`, ", $DB->real_escape_string ( $k ) );
			}
			if ( $obj_class->hasProperty ( 'bumped_at' ) ) {
				$my_query = rtrim ( $my_query, ", " ) . ") VALUES (UTC_TIMESTAMP(), UTC_TIMESTAMP(), ";
			} else {
				$my_query = rtrim ( $my_query, ", " ) . ") VALUES (UTC_TIMESTAMP(), ";
			}
			foreach ( $vars_to_save as $k => $v ) {
				if ( $k == 'password' ) {
					$my_query .= sprintf ( "'%s', ", $DB->real_escape_string ( _APPCONFIG::salted_sha1 ( $v ) ) );
				} else {
					if ( $v instanceof DateTime ) {
						$my_query .= "'" . $this->datetime_to_string ( $v ) . "', ";
					} else {
						$my_query .= sprintf ( "'%s', ", $DB->real_escape_string ( $v ) );
					}
				}
			}
			$my_query = rtrim ( $my_query, ", " ) . ")";
			$queries[] = $my_query;
			$TRACKER['queries_insert']++;
			if ( $result = $DB->query ( $my_query ) ) {
				if ( $DB->affected_rows == 1 ) {
					if ( $DB->insert_id ) {
						return intval ( $DB->insert_id );
					}
				} else {
					return false;
				}
				return true;
			} else {
				$errors[] = $DB->error;
			}
		}
		return false;
	}
	public function update ( $table, $fields ) {
		if ( $this->validates ( ) ) {
			global $DB, $queries, $TRACKER, $errors;
			// update
			// get collection of vars
			$obj_vars = get_object_vars ( $this );
			// build reflector to compare with
			$obj_class = new ReflectionClass ( get_class ( $this ) );
			// collect vars to save
			$vars_to_save = array ( );
			if ( is_string ( $fields ) ) {
				if ( $obj_class->hasProperty ( $fields ) ) {
					if ( isset ( $this->{$fields} ) ) {
						if ( $fields == 'ban_expires' ) {
							$vars_to_save[$fields] = $this->ban_expires ( );
						} else if ( $fields == 'last_seen' ) {
							$vars_to_save[$fields] = time ( );
						} else {
							$vars_to_save[$fields] = $this->{$fields};
						}
					} else {
						$vars_to_save[$fields] = 'NULL';
					}
				}
			} else if ( is_array ( $fields ) ) {
				foreach ( $fields as $v ) {
					if ( $obj_class->hasProperty ( $v ) ) {
						if ( isset ( $this->{$v} ) ) {
							// exists and is accessible, add to query
							if ( $v == 'ban_expires' ) {
								$vars_to_save[$v] = $this->{$v} ( );
							} else if ( $v == 'last_seen' ) {
								$vars_to_save[$v] = time ( );
							} else {
								$vars_to_save[$v] = $this->{$v};
							}
						} else {
							$vars_to_save[$v] = 'NULL';
						}
					}
				}
			}
			$my_query = sprintf ( "UPDATE `%s` SET ", $DB->real_escape_string ( $table ) );
			foreach ( $vars_to_save as $k => $v ) {
				if ( $k == 'password' ) {
					$my_query .= sprintf ( "`$k` = '%s' AND ", $DB->real_escape_string ( _APPCONFIG::salted_sha1 ( $v ) ) );
				} else {
					if ( $k == 'updated_at' || $k == 'bumped_at' || $k == 'last_seen' ) {
						$my_query .= "`$k` = UTC_TIMESTAMP() AND ";
					} else if ( $v == 'NULL' || $k == 'ban_expires' && empty ( $v ) ) {
						$my_query .= "`$k` = NULL AND ";
					} else {
						$my_query .= sprintf ( "`$k` = '%s' AND ", $DB->real_escape_string ( $v ) );
					}
				}
			}
			$my_query = rtrim ( $my_query, " AND " ) . sprintf ( " WHERE `ID` = '%s' LIMIT 1", $this->ID );
			$queries[] = $my_query;
			$TRACKER['queries_update']++;
			if ( $result = $DB->query ( $my_query ) ) {
				if ( $DB->affected_rows == 1 ) {
					return true;
				}
				return false;
			} else {
				$errors[] = $DB->error;
			}
		}
		return false;
	}

	public function update_counter ( $table, $field, $amount ) {
		if ( $this->validates ( ) ) {
			global $DB, $queries, $TRACKER, $errors;
			// update
			// build reflector to compare with
			$obj_class = new ReflectionClass ( get_class ( $this ) );
			if ( ! ( $obj_class->hasProperty ( $field ) && isset ( $this->{$field} ) ) ) {
				return false;
			}
			$my_query = sprintf ( "UPDATE `%s` SET ", $DB->real_escape_string ( $table ) );
			$amount = ( int ) $amount;
			if ( $amount > 0 ) {
				$my_query .= sprintf ( "$field=$field+%d ", $DB->real_escape_string ( $amount ) );
			} else if ( $amount < 0 ) {
				// IF statement prevents this from going negative
				$my_query .= sprintf ( "$field=IF(ABS(%d)<=$field,$field%d,0) ", $DB->real_escape_string ( $amount ), $DB->real_escape_string ( $amount ) );
			} else {
				return false;
			}
			$my_query .= sprintf ( " WHERE `ID` = '%u' LIMIT 1", $this->ID ( ) );
			$queries[] = $my_query;
			$TRACKER['queries_update']++;
			if ( $result = $DB->query ( $my_query ) ) {
				if ( $DB->affected_rows == 1 ) {
					$this->{$field} = $this->{$field} + $amount;
					return true;
				}
				return false;
			} else {
				$errors[] = $DB->error;
			}
		}
		return false;
	}

	public function destroy ( $table ) {
		global $DB, $queries, $TRACKER, $errors;
		if ( intval ( $this->ID ( ) ) > 0 ) {
			$queries[] = "DELETE FROM `" . $DB->real_escape_string ( $table ) . "` WHERE `ID` = '" . $DB->real_escape_string ( $this->ID ( ) ) . "' LIMIT 1";
			$result = $DB->query ( end ( $queries ) );
			if ( $DB->affected_rows == 1 ) {
				$TRACKER['queries_delete']++;
				return true;
			}
		}
		return false;
	}

	// original source: http://www.hurrchan.net/wiki/Tripcodes#PHP_Tripcode_Algoritm
	// this is heavily modified
	static function generate_tripcode ( $name, $length = 10 ) {
		if ( ! defined ( 'LIMIT_NAME_UNICODE' ) ) {
			// get the setting that controls cleanup of unicode in the name field
			Setting::load ( array ( 'first', 'conditions' => "name = 'LIMIT_NAME_UNICODE'", 'select' => 'name, type, value' ) );
		}
		// parse the input
		$name = stripslashes ( $name );
		$t = explode('#', $name);
		$nameo = $t[0];
		if ( isset ( $t[1] ) || isset ( $t[2] ) ) {
			// take the first valid set of key data ([in]secure trips are split differently)
			$trip = ( ( strlen ( $t[1] ) > 0 ) ? $t[1] : $t[2] );
			if ( ( function_exists ( 'mb_convert_encoding' ) ) ) {
				// multi-byte encoding is necessary to produce standard tripcode output, but only use it if available
				mb_substitute_character('none');
				// $trip is now properly encoded
				$trip = mb_convert_encoding ( $trip, 'Shift_JIS', 'UTF-8' );
			}
			if ( isset ( $t[2] ) ) {
				// this tripkey is secure, use the application-level salt
				$trip = '!!' . substr ( crypt ( $trip, _APPCONFIG::$auth_key ), ( -1 * $length ) );
			} else {
				// this tripkey is standard, use the globally-known salt
				$salt = substr ( $trip.'H.', 1, 2 );
				$salt = preg_replace ( '/[^\.-z]/', '.', $salt );
				$salt = strtr ( $salt, ':;<=>?@[\]^_`', 'ABCDEFGabcdef' );
				$trip = '!' . substr ( crypt ( $trip, $salt ), ( -1 * $length ) );
			}
		}
		if ( defined ( 'LIMIT_NAME_UNICODE' ) && LIMIT_NAME_UNICODE == 1 ) {
			// clean the name field to remove unicode that is messy or misleading (fake underlines, ZALGO text, etc)
			$nameo = preg_replace ( UNICODE_RESTRICT_SPECIFIC, '', $nameo );
		}
		if ( isset ( $trip ) ) {
			return array ( $nameo, $trip );
		}
		return array ( $nameo );
	}
	

	/* PROPERTY-LEVEL FUNCTIONS
	================================================== */
	// standard getters and formatting for all records that extend this class
	public function ID ( ) {
		return ( int ) $this->ID;
	}
	public function set_ID ( $ID ) {
		$this->ID = $ID;
	}
	public function set_nametrip ( ) {
		// shared method to assign name and tripcode to the necessary columns; this wouldn’t need to live here if Topic and Reply classes used STI.
		$class_vars = get_class_vars ( get_class ( $this ) );
		// only assign it if the columns exist
		if ( array_key_exists ( 'tripcode', $class_vars ) && array_key_exists ( 'name', $class_vars ) ) {
			// only process this 
			if ( ! empty ( $this->name ) ) {
				$name_auth = self::generate_tripcode ( $this->name );
				if ( isset ( $name_auth[0] ) ) { $this->name = $name_auth[0]; }
				if ( isset ( $name_auth[1] ) ) { $this->tripcode = $name_auth[1]; }
				$this->update ( 'name' );
				$this->update ( 'tripcode' );
			}
		}
		return true;
	}
	public function created_at ( $format = MYSQL_DATETIME_FORMAT ) {
		return $this->datetime_to_string ( $this->created_at, $format );
	}
	public function updated_at ( $format = MYSQL_DATETIME_FORMAT ) {
		return $this->datetime_to_string ( $this->updated_at, $format );
	}
	public function last_seen ( $format = MYSQL_DATETIME_FORMAT ) {
		return $this->datetime_to_string ( $this->last_seen, $format );
	}
	public function first_seen ( $format = MYSQL_DATETIME_FORMAT ) {
		return $this->datetime_to_string ( $this->first_seen, $format );
	}
	public function bumped_at ( $format = MYSQL_DATETIME_FORMAT ) {
		return $this->datetime_to_string ( $this->bumped_at, $format );
	}
	public function datetime_to_string ( $datetime, $format = MYSQL_DATETIME_FORMAT ) {
		// DateTime requires PHP >= 5.2.0
		if ( $datetime instanceof DateTime ) {
			return $datetime->format ( $format );
		}
		$datetime = new DateTime( $datetime );
		return $datetime->format( $format );
	}
	##################################################
	########## VALIDATION FUNCTIONS
	##################################################
	// these typecast input to normalize data and (hopefully) increase the application’s fault-tolerance
	// validations include NULL status, max/min lengths, max/min numeric values, and convenience/cosmetic methods like string trimming
	// some data types can be mapped from valid PHP assignments to SQL-compatible values (this is not a DB abstraction, but a basic sanity check of types and values)
	public function validate_string ( $string = NULL, $allow_NULL = TRUE, $length = 0, $trim = TRUE ) {
		if ( ! isset ( $string ) && $allow_NULL == TRUE ) {
			return NULL;
		}
		$string = ( string ) $string;
		if ( $length != 0 ) {
			$string = substr ( $string, 0, $length );
		}
		// trim() strips space, \t, \n, \r, \0 and \x0B from the beginning and end; the set can be expanded with a second (string) argument
		if ( $trim == TRUE ) {
			$string = trim ( $string );
		}
		return $string;
	}
	public function validate_datetime ( $datetime = NULL, $allow_NULL = TRUE ) {
		// DateTime requires PHP >= 5.2.0
		if ( ( ! isset ( $datetime ) || $datetime == 'NULL' ) && $allow_NULL == TRUE ) {
			return NULL;
		}
		
		if ( ! is_object ( $datetime ) ) {
			try {
				$datetime = date_create ( $datetime );
			} catch ( Exception $e ) {
				// date_create can throw exceptions on failure to parse; this shouldn’t kill the execution, but should return a useful message, instead
				$errors[] = '“' . $datetime . '” is not a valid DateTime';
			}
		}
		return $datetime;
	}
	// TODO: precision? (Probably not necessary; the views and DB can each handle that easily enough. Plus, precision might be more useful in a pseudo-type for currency.)
	public function validate_float ( $float = NULL, $allow_NULL = TRUE, $unsigned = FALSE, $max = NULL, $min = NULL ) {
		if ( ! isset ( $float ) && $allow_NULL == TRUE ) {
			return NULL;
		}
		$float = ( float ) $float;
		return $float;
	}
	public function validate_integer ( $integer = NULL, $allow_NULL = TRUE, $unsigned = FALSE, $max = NULL, $min = NULL ) {
		if ( ! isset ( $integer ) && $allow_NULL == TRUE ) {
			return NULL;
		}
		$integer = ( int ) $integer;
		// zero is very valid and normal, but not a useful default, so return null when invalid; methods called on validate_integer should handle this as necessary
		if ( $unsigned == TRUE && $integer < 0 ) {
			// was less than zero
			$integer = NULL;
		}
		if ( $max != NULL && $integer > $max ) {
			// too large
			$integer = NULL;
		}
		if ( $min != NULL && $integer < $min ) {
			// too small
			$integer = NULL;
		}
		// TODO: would “return NULL” work?
		return $integer;
	}
	// This application stores PHP-assigned booleans as TINYINT in the DB; BOOLEAN is less flexible, in SQL
	public function validate_boolean ( $boolean = NULL, $allow_NULL = TRUE ) {
		if ( ! isset ( $boolean ) && $allow_NULL == TRUE ) {
			return NULL;
		}
		$boolean = ( int ) $boolean;
		// enforce boolean as (0|1)
		if ( $boolean != 0 && $boolean != 1 ) {
			$boolean = NULL;
		}
		return $boolean;
	}
	// TODO: does anything use this? Is it valid?
	public function validate_pseudo_type ( $type = NULL, $allow_NULL = TRUE ) {
		if ( ! isset ( $type ) && $allow_NULL == TRUE ) {
			return NULL;
		}
		$type = trim ( ( string ) $type );
		if ( ! in_array ( $type, SETTINGS_PSEUDO_TYPES ( ) ) ) {
			$type = NULL;
		}
		return $type;
	}
	// these are just placeholders that subclasses should override
	private function apply_class_validations ( ) {
		return true;
	}
	private function apply_special_validations ( ) {
		return true;
	}
	
	/* PRIVATE FUNCTIONS
	================================================== */
}

?>
