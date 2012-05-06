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

class Topic extends _IMGBOARD {
	/* CONSTANTS
	================================================== */
	const table = 'topics';
	
	/* PROPERTIES
	================================================== */
	protected	$user_ID;
	protected	$name;
	protected	$tripcode;
	public		$media_ID;
	public		$category_ID;
	public		$bumped_at;
	public		$title;
	public		$body;
	public		$status;
	public		$sticky;
	public		$locked;
	public		$safe_for_work;
	public		$mod_edited;
	public		$replies_count;
	public		$views_count;
	public		$favorites_count;
	public		$replies_favorites_count;
	public		$media_count;
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
	public function media_ID ( ) {
		return ( int ) $this->media_ID;
	}
	public function name ( ) {
		return $this->name;
	}
	public function tripcode ( ) {
		return $this->tripcode;
	}
	public function URI ( ) {
		if ( isset ( $GLOBALS['current_board'] ) && ! empty ( $GLOBALS['current_board'] ) ) {
			return $GLOBALS['current_board']->URI ( ) . 'topics/' . $this->ID ( ) . '/';
		} else {
			return BASE_URI . 'topics/view/' . $this->ID ( ) . '/';
		}
	}
	public function redirect_URI ( ) {
		if ( isset ( $GLOBALS['current_board'] ) && ! empty ( $GLOBALS['current_board'] ) ) {
			return 'board/' . $GLOBALS['current_board']->name_URI ( ) . '/topics/' . $this->ID ( ) . '/';
		} else {
			return 'topics/view/' . $this->ID ( ) . '/';
		}
	}
	public function edit_URI ( ) {
		if ( isset ( $GLOBALS['current_board'] ) && ! empty ( $GLOBALS['current_board'] ) ) {
			return $GLOBALS['current_board']->URI ( ) . 'edit/' . $this->ID ( ) . '/';
		} else {
			return BASE_URI . 'topics/edit/' . $this->ID ( ) . '/';
		}
	}
	public function reply_URI ( ) {
		if ( isset ( $GLOBALS['current_board'] ) && ! empty ( $GLOBALS['current_board'] ) ) {
			return $GLOBALS['current_board']->URI ( ) . 'reply/' . $this->ID ( ) . '/';
		} else {
			return BASE_URI . 'topics/reply/' . $this->ID ( ) . '/';
		}
	}
	public function cite_URI ( ) {
		if ( isset ( $GLOBALS['current_board'] ) && ! empty ( $GLOBALS['current_board'] ) ) {
			return $GLOBALS['current_board']->URI ( ) . 'reply/' . $this->ID ( ) . '/cite/OP/';
		} else {
			return BASE_URI . 'topics/reply/' . $this->ID ( ) . '/cite/OP/';
		}
	}
	public function quote_URI ( ) {
		if ( isset ( $GLOBALS['current_board'] ) && ! empty ( $GLOBALS['current_board'] ) ) {
			return $GLOBALS['current_board']->URI ( ) . 'reply/' . $this->ID ( ) . '/quote/OP/';
		} else {
			return BASE_URI . 'topics/reply/' . $this->ID ( ) . '/quote/OP/';
		}
	}

	/* RELATED RECORDS
	================================================== */
	public function replies ( ) {
		return Reply::find ( array ( 'conditions' => array ( "`topic_ID` = '%u' AND `status` = 'published'", $this->ID ( ) ), 'order' => 'ID ASC' ) );
	}
	public function user_account ( ) {
		return UserAccount::find ( array ( 'first', 'conditions' => array ( "`ID` = '%u'", $this->user_ID ) ) );
	}
	public function media ( ) {
		if ( is_int ( $this->media_ID ( ) ) && $this->media_ID ( ) > 0 ) {
			return Media::find ( array ( 'first', 'conditions' => array ( "`ID` = '%u' AND `status` = 'published'", $this->media_ID ( ) ) ) );
		}
	}

	/* PRIVATE
	================================================== */
	private function apply_class_validations ( ) {
		// validate the content of each property
		$this->ID =		$this->validate_integer ( $this->ID, TRUE, TRUE );
		$this->created_at =	$this->validate_datetime ( $this->created_at, TRUE );
		$this->updated_at =	$this->validate_datetime ( $this->updated_at, TRUE );
		$this->bumped_at =	$this->validate_datetime ( $this->bumped_at, TRUE );
		$this->user_ID =	$this->validate_integer ( $this->user_ID, TRUE, TRUE );
		$this->name =		$this->validate_string ( $this->name, TRUE, 140, TRUE );
		$this->tripcode =	$this->validate_string ( $this->tripcode, TRUE );
		$this->category_ID =	$this->validate_integer ( $this->category_ID, TRUE, TRUE );
		$this->media_ID =	$this->validate_integer ( $this->media_ID, TRUE, TRUE );
		$this->title =		$this->validate_string ( $this->title, TRUE, 0, TRUE );
		$this->body =		$this->validate_string ( $this->body, TRUE, 0, TRUE );
//		$this->status =		$this->validate_string ( $this->status, TRUE, 30, TRUE );
		$this->sticky =		$this->validate_boolean ( $this->sticky, FALSE );
		$this->locked =		$this->validate_boolean ( $this->locked, FALSE );
		$this->safe_for_work =	$this->validate_boolean ( $this->safe_for_work, FALSE );
		$this->mod_edited =	$this->validate_boolean ( $this->mod_edited );
		$this->replies_count =	$this->validate_integer ( $this->replies_count, TRUE, TRUE );
		$this->favorites_count =$this->validate_integer ( $this->favorites_count, TRUE, TRUE );
		$this->replies_favorites_count =$this->validate_integer ( $this->replies_favorites_count, TRUE, TRUE );
		$this->views_count =	$this->validate_integer ( $this->views_count, TRUE, TRUE );
		$this->media_count =	$this->validate_integer ( $this->media_count, TRUE, TRUE );
		return true;
		// TODO: return human-readable errors?
	}
	private function apply_special_validations ( ) {
		global $errors;
		$valid = 1;
		// validate the content of each property against the APP settings
		if ( defined ( 'TOPIC_TITLE_CHARACTERS_MINIMUM' ) ) {
			$my_length = strlen ( $this->title );
			if ( $my_length < TOPIC_BODY_CHARACTERS_MINIMUM ) {
				$errors[] = 'Your topic title is too short. It must contain at least ' . number_format ( TOPIC_BODY_CHARACTERS_MINIMUM ) . ' character' . ( ( TOPIC_BODY_CHARACTERS_MINIMUM != 1 ) ? 's' : '') . '.';
				$valid = 0;
			}
		}
		if ( defined ( 'TOPIC_TITLE_CHARACTERS_MAXIMUM' ) ) {
			$my_length = strlen ( $this->title );
			if ( $my_length > TOPIC_TITLE_CHARACTERS_MAXIMUM ) {
				$too_long = $my_length - TOPIC_TITLE_CHARACTERS_MAXIMUM;
				$errors[] = 'Your topic title is ' . number_format ( $too_long ) . ' character' . ( ( $too_long != 1 ) ? 's' : '' ) . " too long. The limit is " . number_format ( TOPIC_TITLE_CHARACTERS_MAXIMUM ) . '.';
				$valid = 0;
			}
		}
		if ( defined ( 'TOPIC_BODY_CHARACTERS_MINIMUM' ) ) {
			$my_length = strlen ( $this->body );
			if ( $my_length < TOPIC_BODY_CHARACTERS_MINIMUM && ! ( ! empty ( $this->media_ID ) || ( UPLOAD_OVERRIDES_REPLY_CHARACTERS_MINIMUM == 1 && UPLOADS_ALLOWED == 1 && isset ( $_FILES ) && $_FILES['file_upload']['error'] == 0 ) ) ) {
				$errors[] = 'Your topic body is too short. It must contain at least ' . number_format ( TOPIC_BODY_CHARACTERS_MINIMUM ) . ' character' . ( ( TOPIC_BODY_CHARACTERS_MINIMUM != 1 ) ? 's' : '') . '.';
				$valid = 0;
			}
		}
		if ( defined ( 'TOPIC_BODY_CHARACTERS_MAXIMUM' ) ) {
			$my_length = strlen ( $this->body );
			if ( $my_length > TOPIC_BODY_CHARACTERS_MAXIMUM ) {
				$too_long = $my_length - TOPIC_BODY_CHARACTERS_MAXIMUM;
				$errors[] = 'Your topic body is ' . number_format ( $too_long ) . ' character' . ( ( $too_long != 1 ) ? 's' : '' ) . ' too long. The limit is ' . number_format ( TOPIC_BODY_CHARACTERS_MAXIMUM ) . ' character' . ( ( $too_long != 1 ) ? 's' : '' ) . '.';
				$valid = 0;
			}
		}
		if ( defined ( 'WORDFILTER_TOPICS' ) && WORDFILTER_TOPICS == 1 ) {
			// TODO: make a wordfilter class
		}
		if ( defined ( 'REMOVE_UNICODE' ) && REMOVE_UNICODE == 1 ) {
			if ( preg_match ( ASCII_PATTERN, $this->title ) || preg_match ( ASCII_PATTERN, $this->body ) ) {
				$errors[] = "Unicode characters are not allowed.";
				$valid = 0;
			}
		}
		if ( defined ( 'LIMIT_TITLE_UNICODE' ) && LIMIT_TITLE_UNICODE == 1 ) {
			$this->title = preg_replace ( UNICODE_RESTRICT_SPECIFIC, '', $this->title );
		}
		if ( $valid == 1 ) {
			return true;
		}
		return false;
	}


}

?>