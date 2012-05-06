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

class Mailer extends _IMGBOARD {
	/* CONSTANTS
	================================================== */
	// this is a pseudo-class that does not have a database table
	const table = NULL;
	
	/* PROPERTIES
	================================================== */
	public		$headers = array ( );
	public		$header_string;	// headers array as string
	public		$recipients;	// does not limit the number of recipients; this is exploitable by design
	public		$to;		// more strict validation to limit the recipient to one person; reduces spam risk
	public		$BCC;		// blind carbon copy
	public		$from;
	public		$content_type;
	public		$subject;
	public		$body;		// TODO: implement multipart?

	/* CONSTUCTOR
	================================================== */
	// inherited

	/* RECORD-LEVEL FUNCTIONS
	================================================== */
	public function send ( ) {
		global $errors;
		// TODO: implement SMTP methods?
		if ( SEND_EMAILS != 1 ) {
			$errors[] = 'Emails are currently disabled. Your password cannot be reset through email.';
			return false;
		}
		if ( $this->validate ( ) ) {
			$error_count = count ( $errors );
			// format headers array into a header string
			$this->header_string = 'From: ' . $this->from;
			$this->header_string .= "\n" . 'X-Mailer: IMGBOARD/' . IMGBOARD_VERSION;
			$this->header_string .= "\n" . 'Content-type: text/plain; charset=UTF-8';
			foreach ( $this->headers as $key => $value ) {
				$this->header_string .= "\n" . $key . ': ' . $value;
			}
			// format body into wordwrap
			$this->body = wordwrap ( $this->body, 65, "\n", TRUE );
			// format subject as UTF-8 with base64 encoding
			$this->subject = '=?UTF-8?B?' . base64_encode ( $this->subject ) . '?=';
			$sent = 0;
			if ( isset ( $this->to ) && ! empty ( $this->to ) ) {
				mail ( $this->to, $this->subject, $this->body, $this->header_string );
			} else if ( isset ( $this->recipients ) && ! empty ( $this->recipients ) ) {
				mail ( $this->to, $this->subject, $this->body, $this->header_string );
			} else {
				$errors[] = 'Cannot send email; no recipient specified.';
				return false;
			}
		}
		return true;
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

	// disable these
	public static function find ( $arguments = NULL ) {
		// remove the function from the base class
		return false;
	}
	public function create ( ) {
		// remove the function from the base class
		return false;
	}
	public function update ( ) {
		// remove the function from the base class
		return false;
	}
	public function update_counter ( ) {
		// remove the function from the base class
		return false;
	}
	public function destroy ( ) {
		// remove the function from the base class
		return false;
	}

	/* PRIVATE
	================================================== */
	private function apply_class_validations ( ) {
		// validate the content of each property
		if ( isset ( $this->from ) && ! empty ( $this->from ) && substr_count ( strtolower ( $this->from ), '@' ) == 1 && substr_count ( strtolower ( $this->from ), ',' ) == 0 && substr_count ( strtolower ( $this->from ), ';' ) == 0 ) {
			// it looks like from is formated correctly!
		} else {
			return false;
		}
		if ( isset ( $this->to ) && ! empty ( $this->to ) && substr_count ( strtolower ( $this->to ), '@' ) == 1 && substr_count ( strtolower ( $this->to ), ',' ) == 0 && substr_count ( strtolower ( $this->to ), ';' ) == 0 ) {
			// it looks like to is formated correctly!
		} else {
			return false;
		}
		return true;
		// TODO: return human-readable errors?
	}
	private function apply_special_validations ( ) {
		// validate the content of each property against the APP settings
		return true;
	}


}

?>