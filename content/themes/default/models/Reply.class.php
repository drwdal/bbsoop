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

class Reply extends _IMGBOARD {
	/* CONSTANTS
	================================================== */
	const table = 'replies';
	
	/* PROPERTIES
	================================================== */
	protected	$topic_ID;
	protected	$user_ID;
	protected	$name;
	protected	$tripcode;
	public		$media_ID;
	public		$body;
	public		$status;
	public		$mod_edited;
	public		$favorites_count;
	public		$partial_cache;

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
		}
	}

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
	public function user_ID ( ) {
		return ( int ) $this->user_ID;
	}
	public function topic_ID ( ) {
		return ( int ) $this->topic_ID;
	}
	public function media_ID ( ) {
		return ( int ) $this->media_ID;
	}
	public function name ( ) {
		return ( string ) $this->name;
	}
	public function tripcode ( ) {
		return ( string ) $this->tripcode;
	}
	public function URI ( ) {
		if ( isset ( $GLOBALS['current_board'] ) && ! empty ( $GLOBALS['current_board'] ) ) {
			return $GLOBALS['current_board']->URI ( ) . 'topics/' . $this->topic_ID ( ) . '#reply-' . $this->ID ( );
		} else {
			return BASE_URI . 'topics/view/' . $this->topic_ID ( ) . '#reply-' . $this->ID ( );
		}
	}
	public function redirect_URI ( ) {
		if ( isset ( $GLOBALS['current_board'] ) && ! empty ( $GLOBALS['current_board'] ) ) {
			return 'board/' . $GLOBALS['current_board']->name_URI ( ) . '/topics/' . $this->topic_ID ( ) . '#reply-' . $this->ID ( );
		} else {
			return 'topics/view/' . $this->topic_ID ( ) . '#reply-' . $this->ID ( );
		}
	}
	public function edit_URI ( ) {
		if ( isset ( $GLOBALS['current_board'] ) && ! empty ( $GLOBALS['current_board'] ) ) {
			return $GLOBALS['current_board']->URI ( ) . 'edit_reply/' . $this->ID ( );
		} else {
			return BASE_URI . 'topics/edit_reply/' . $this->ID ( );
		}
	}
	public function cite_URI ( ) {
		if ( isset ( $GLOBALS['current_board'] ) && ! empty ( $GLOBALS['current_board'] ) ) {
			return $GLOBALS['current_board']->URI ( ) . 'topics/reply/' . $this->topic_ID ( ) . '/cite/' . $this->ID ( ) . '/';
		} else {
			return BASE_URI . 'topics/reply/' . $this->topic_ID ( ) . '/cite/' . $this->ID ( ) . '/';
		}
	}
	public function quote_URI ( ) {
		if ( isset ( $GLOBALS['current_board'] ) && ! empty ( $GLOBALS['current_board'] ) ) {
			return $GLOBALS['current_board']->URI ( ) . 'topics/reply/' . $this->topic_ID ( ) . '/quote/' . $this->ID ( ) . '/';
		} else {
			return BASE_URI . 'topics/reply/' . $this->topic_ID ( ) . '/quote/' . $this->ID ( ) . '/';
		}
	}


	/* RELATED RECORDS
	================================================== */
	public function topic ( ) {
		return Topic::find ( array ( 'first', 'conditions' => array ( "`ID` = '%u'", $this->topic_ID ( ) ), 'order' => 'ID ASC' ) );
	}
	public function user_account ( ) {
		return UserAccount::find ( array ( 'first', 'conditions' => array ( "`ID` = '%u'", $this->user_ID ( ) ) ) );
	}
	public function media ( ) {
		if ( is_int ( $this->media_ID ( ) ) && $this->media_ID ( ) > 0 ) {
			return Media::find ( array ( 'first', 'conditions' => array ( "`ID` = '%u' AND `status` = 'published'", $this->media_ID ( ) ) ) );
		}
		return false;
	}

	/* PRIVATE
	================================================== */
	private function apply_class_validations ( ) {
		// validate the content of each property
		$this->ID =		$this->validate_integer ( $this->ID, TRUE, TRUE );
		$this->topic_ID =	$this->validate_integer ( $this->topic_ID, TRUE, TRUE );
		$this->created_at =	$this->validate_datetime ( $this->created_at, TRUE );
		$this->updated_at =	$this->validate_datetime ( $this->updated_at, TRUE );
		$this->user_ID =	$this->validate_integer ( $this->user_ID, TRUE, TRUE );
		$this->media_ID =	$this->validate_integer ( $this->media_ID, TRUE, TRUE );
		$this->favorites_count =$this->validate_integer ( $this->favorites_count, TRUE, TRUE );
		$this->name =		$this->validate_string ( $this->name, TRUE, 140, TRUE );
		$this->tripcode =	$this->validate_string ( $this->tripcode, TRUE );
		$this->body =		$this->validate_string ( $this->body, TRUE, 0, TRUE );
		$this->status =		$this->validate_string ( $this->status, TRUE, 30, TRUE );
		$this->mod_edited =	$this->validate_boolean ( $this->mod_edited );
		return true;
		// TODO: return human-readable errors?
	}
	private function apply_special_validations ( ) {
		global $errors;
		$valid = 1;
		// validate the content of each property against the APP settings
		if ( defined ( 'REPLY_BODY_CHARACTERS_MINIMUM' ) ) {
			$my_length = strlen ( $this->body );
			if ( $my_length < REPLY_BODY_CHARACTERS_MINIMUM && ! ( ! empty ( $this->media_ID ) || ( UPLOAD_OVERRIDES_REPLY_CHARACTERS_MINIMUM == 1 && UPLOADS_ALLOWED == 1 && isset ( $_FILES ) && $_FILES['file_upload']['error'] == 0 ) ) ) {
				$errors[] = 'Your reply body is too short. It must contain at least ' . number_format ( REPLY_BODY_CHARACTERS_MINIMUM ) . ' character' . ( ( REPLY_BODY_CHARACTERS_MINIMUM != 1 ) ? 's' : '') . '.';
				$valid = 0;
			}
		}
		if ( defined ( 'REPLY_BODY_CHARACTERS_MAXIMUM' ) ) {
			$my_length = strlen ( $this->body );
			if ( $my_length > REPLY_BODY_CHARACTERS_MAXIMUM ) {
				$too_long = $my_length - REPLY_BODY_CHARACTERS_MAXIMUM;
				$errors[] = 'Your reply body is ' . number_format ( $too_long ) . ' character' . ( ( $too_long != 1 ) ? 's' : '' ) . ' too long. The limit is ' . number_format ( REPLY_BODY_CHARACTERS_MAXIMUM ) . ' character' . ( ( $too_long != 1 ) ? 's' : '' ) . '.';
				$valid = 0;
			}
		}
		if ( defined ( 'WORDFILTER_REPLESI' ) && WORDFILTER_REPLESI == 1 ) {
			// TODO: make a wordfilter class
		}
		if ( defined ( 'REMOVE_UNICODE' ) && REMOVE_UNICODE == 1 ) {
			if ( preg_match ( '/[^\x20-\x7E]/', $this->title ) || preg_match ( '/[^\x20-\x7E]/', $this->body ) ) {
				$errors[] = "Unicode characters are not allowed.";
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