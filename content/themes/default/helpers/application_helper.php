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

function IMGBOARD_footer ( ) {
	require ( BASE_PATH . 'include/footer.php' );
}

function generate_nav ( $context = 'header', $interaction = 'javascript' ) {
	global $controller, $action;
	if ( $controller == 'admin' && verify_permissions ( ADMIN_TYPE ) ) {
		include ( BASE_PATH . THEME_PATH . 'views/admin/_nav.php' );
	} else {
		include ( BASE_PATH . THEME_PATH . 'views/common/_nav.php' );
	}
}

function generate_subnav ( ) {
	global $controller, $action;
	if ( file_exists ( BASE_PATH . THEME_PATH . "views/{$controller}/_{$controller}_subnav.php" ) ) {
		require ( BASE_PATH . THEME_PATH . "views/{$controller}/_{$controller}_subnav.php" );
	}
}

function site_title (  ) {
	// controllers MUST sanitize this
	return $GLOBALS['site_title'];
}
function page_title (  ) {
	// controllers MUST sanitize this
	return $GLOBALS['page_title'];
}

function IMGBOARD_title ( $output_mode = 'echo' ) {
	global $controller, $action, $ID;
	$output = '';
	$separator = ' — ';
	if ( isset ( $GLOBALS['topic'] ) ) {
		$output = page_title ( ) . $separator . str_replace ( '_', ' ', titleize ( $controller ) );
	} else if ( isset ( $ID ) && ! empty ( $ID ) ) {
		$output = str_replace ( '_', ' ', $ID ) . $separator . str_replace ( '_', ' ', titleize ( $action ) ) . $separator . str_replace ( '_', ' ', titleize ( $controller ) );
	} else if ( $action != 'index' ) {
		$output = str_replace ( '_', ' ', titleize ( $action ) ) . $separator . str_replace ( '_', ' ', titleize ( $controller ) );
	} else {
		$output = str_replace ( '_', ' ', titleize ( $controller ) );
	}
	$output = strip_tags ( $output . $separator . $GLOBALS['site_title'] );
	if ( $output_mode == 'echo' ) {
		echo $output;
	} else if ( $output_mode == 'return' ) {
		return $output;
	}
}

function IMGBOARD_meta_description ( $output_mode = 'echo' ) {
	global $controller, $action, $ID;
	$output = '';
	if ( isset ( $ID ) && ! empty ( $ID ) ) {
		$output = "$ID | $action | $controller";
	} else {
		$output = "$action | $controller";
	}
	if ( $output_mode == 'echo' ) {
		echo $output;
	} else if ( $output_mode == 'return' ) {
		return $output;
	}
}

function generate_notice ( ) {
	if ( ( isset ( $_SESSION['notice'] ) && ! empty ( $_SESSION['notice'] ) ) || ( isset ( $_SESSION['notice_now'] ) && ! empty ( $_SESSION['notice_now'] ) ) ) {
		IMGBOARD_render_view ( 'common/_notice' );
		unset ( $_SESSION['notice_now'] );
	}
}

function generate_errors ( ) {
	if ( count ( $GLOBALS['errors'] ) > 0 ) {
		IMGBOARD_render_view ( 'common/_validation_errors' );
	}
}

function generate_post ( $record ) {
	global $show_mod, $controller;
	// get the users’ name or anonymous ID
	$output = anonymous_user_mapping ( $record );
	// prepend label, if anonymous
	if ( $record->name ( ) == '' ) {
		$output = 'Anonymous ' . $output;
	}
	// give user links to and for priviledged users
	$my_link = '';
	if ( 1 === $show_mod || $controller == 'admin' ) {
		$my_link =  BASE_URI . 'admin/user/' . $record->user_ID ( );
	} else if ( $record->user_account ( ) && $record->user_account ( )->type ( ) >= MODERATOR_TYPE ) {
		//$my_link = user_type_link ( $record->user_account ( ) );
		$my_link = "javascript:alert('TODO: link this to a page provided through settings.');";
	}
	if ( ! empty ( $my_link) ) {
		$output = '<a href="' . $my_link . '">' . $output . '</a>';
	}
	// give the name an HTML wrapper
	$output = '<span class="user-account">' . $output . '</span>';
	$output .= '<span class="meta">';
	// TODO: collect user info during the original queries
	// check cache
	if ( CACHE_LEVEL > 0 ) {
		$output .= get_cached_body ( $record, TRUE );
	} else {
		$output .= generate_post_body ( $record, TRUE );
	}
	echo '<h3 class="name c">' . $output;
}

function generate_post_body ( $record, $wrapper = TRUE ) {
	$output = '';
	$record_class = get_class ( $record );
	if ( $record_class == 'Topic' ) {
		$output .= ' started this topic ';
	} else if ( $record_class == 'Reply' ) {
		$output .= ' replied ';
	}
	if ( $record_class == 'Topic' || $record_class == 'Reply' ) {
		$output .= ' <span class="at-label">at </span><span class="time" title="' . $record->created_at ( 'U' ) . '">' . $record->created_at ( ) . ' UTC</span>';
		if ( $record_class == 'Reply' && isset ( $GLOBALS['topic'] ) && $GLOBALS['topic']->ID ( ) > 0 ) {
			$output .= ', ' . seconds_to_time ( ( $record->created_at ( 'U' ) - $GLOBALS['topic']->created_at ( 'U' ) ) ) . ' after the original post';
		}
		$output .= '</span>'; // closing the meta span
		if ( $record_class == 'Reply' ) {
			$output .= ' <span class="float-right reply-ID" title="reply #' . number_format ( $record->ID ( ) ) . '"><a href="' . $record->URI ( ) . '">#' . number_format ( $record->ID ( ) ) . "</a></span>";
		} else if ( get_class ( $record ) == 'Topic' ) {
			$output .= ' <span class="float-right topic-ID" title="topic #' . number_format ( $record->ID ( ) ) . '">#' . number_format ( $record->ID ( ) ) . "</span>";
		}
		$output .= "</h3>\n";
	}
	if ( $wrapper ) {
		$output .= "<div class=\"wrapper c\">\n" . generate_post_media ( $record ) . parse_post_body ( $record->body, get_class ( $record ), $record->ID ( ) ) . "\n";
		if ( isset ( $record->updated_at ) && intval ( $record->updated_at ( 'U' ) ) > intval ( $record->created_at ( 'U' ) ) ) {
			$output .= "\n<p class=\"meta\">(Edited " . seconds_to_time ( intval ( $record->updated_at ( 'U' ) ) - intval ( $record->created_at ( 'U' ) ) ) . " later)</p>\n";
		}
	} else {
		$output .= generate_post_media ( $record ) . parse_post_body ( $record->body, get_class ( $record ), $record->ID ( ) );
		if ( isset ( $record->updated_at ) && intval ( $record->updated_at ( 'U' ) ) > intval ( $record->created_at ( 'U' ) ) ) {
			$output .= "\n<p class=\"meta\">(Edited " . seconds_to_time ( intval ( $record->updated_at ( 'U' ) ) - intval ( $record->created_at ( 'U' ) ) ) . " later)</p>\n";
		}
	}
	return $output;
}

function generate_post_media ( $record ) {
	$output = '';
	$obj_class = new ReflectionClass ( get_class ( $record ) );
	if ( $obj_class->hasProperty ( 'media_ID' ) ) {
		if ( $media = $record->media ( ) ) {
			if ( $media->type ( ) == 'image' && ( $media->status == 'published' || ( $media->status == 'adult_content' && defined ( 'ALLOW_ADULT_CONTENT' ) && ALLOW_ADULT_CONTENT == 1 ) ) ) {
				$output = '<p class="image" id="' . strtolower ( get_class ( $record ) ) . '-' . $record->ID ( ) . '-media-' . $media->ID ( ) . '"><a href="' . htmlspecialchars ( $media->URL ( ) ) . '" title="' . htmlspecialchars ( $media->original_file_name ( ) ) . '"><img src="' . htmlspecialchars ( $media->thumbnail_URI ( ) ) . '" alt="' . htmlspecialchars ( $media->original_file_name ( ) ) . '" height="' . $media->t_height . '" width="' . $media->t_width . '" /></a><span class="media-meta">' . $media->width . 'w × ' . $media->height . 'h, ' . humanize_bytes ( $media->file_size ) . '</span></p>';
				return $output;
			}
		}
	}
	return false;
}

function get_cached_body ( $record, $wrapper = 1 ) {
	// get the cached body for this record, and generate it if necessary
	if ( empty ( $record->partial_cache ) ) {
		$record->partial_cache = generate_post_body ( $record, $wrapper );
		$record->update ( array ( 'partial_cache' ) );
	}
	return $record->partial_cache;
}

function parse_post_body ( $body, $record_class = NULL, $record_ID = NULL ) {
	// escape dangerous characters
	$body = "<p>" . htmlentities ( $body, ENT_NOQUOTES, APP_CHARSET ) . "</p>";
	// auto-link
	$body = preg_replace ( "/([a-z]+:\/\/([^<>() \t\r\n\v\f]*)(\/)?[^<>(), \t\r\n\v\f]*)/", "<a href=\"\\1\" rel=\"nofollow\">\\1</a>", $body );
	if ( strpos ( $body, 'youtube.com' ) !== FALSE ) {
		// auto link special: YouTube
		$body = preg_replace ( "/(<a href=\"http:\/\/(www\.)?youtube\.com\/watch\?v=([^,\.& \t\r\n\v\f]+)([^,\. \t\r\n\v\f]+)?\"([^<]*)*<\/a>)/", "\\1 [<a href=\"javascript:void(0);\" onclick=\"play_video('youtube','\\3', this, '$record_class', '$record_ID');\" class=\"video youtube\">play</a>]", $body );
	}
	if ( strpos ( $body, 'vimeo.com' ) !== FALSE ) {
		// auto link special: Vimeo
		$body = preg_replace ( "/(<a href=\"http:\/\/(www\.)?vimeo\.com\/([0-9]+)([^,\. \t\r\n\v\f]+)?\"([^<]*)*<\/a>)/", "\\1 [<a href=\"javascript:void(0);\" onclick=\"play_video('vimeo','\\3', this, '$record_class', '$record_ID');\" class=\"video vimeo\">play</a>]", $body );
	}
	if ( strpos ( $body, 'vids.myspace.com' ) !== FALSE ) {
		// auto link special: MySpace (ugh)
		$body = preg_replace ( "/(<a href=\"http:\/\/vids\.myspace\.com\/index\.cfm\?fuseaction=vids\.individual&amp;videoid=([0-9]+)([^,\. \t\r\n\v\f]+)?\"([^<]*)*<\/a>)/", "\\1 [<a href=\"javascript:void(0);\" onclick=\"play_video('myspace','\\2', this, '$record_class', '$record_ID');\" class=\"video myspace\">play</a>]", $body );
	}
	// double line-breaks to paragraphs
	// this has to go before blockquote parsing
	$body = str_replace ( "\r\n\r\n", "</p>\n<p>", $body );
	// single line-breaks to HTML line break
	$body = str_replace ( "\r\n", "<br />\n", $body );
	// [[blockquote]]
	// separating the start and end allows the blockquote to contain multiple elements
	$body = str_replace ( '<p>[[', '<blockquote class="wrapper"><p>', $body );
	$body = str_replace ( ']]</p>', '</p></blockquote>', $body );
	// @reply
	// commas make this pretty, but need to be removed
	// TODO: previous
	$body = preg_replace_callback ( "/(@([0-9,]+)([^a-zA-Z]))/", "reply_linkage", $body );
	// @OP
	$body = preg_replace ( "/@OP([^a-zA-Z])/", "<a href=\"#topic\" class=\"at_reply\" title=\"OP\" onclick=\"handle_at_reply(this);\">@OP</a>\\1", $body );
	// auto-quote "> quote"
	// <a> links break this
	$body = preg_replace ( "/(\n|<p>)(&gt;([^<]*))(<br \/>|<\/p>)/", "\\1<span class=\"quote\">\\2</span>\\4", $body );
	// TODO: combine the headers into one, singular regexp?
	// ====header 4====
	$body = preg_replace ( "/<p>====([^=]+)====<br \/>\n/", "<h4>\\1</h4>\n<p>", $body );
	// ====header 4====
	$body = preg_replace ( "/<p>====([^=]+)====<\/p>/", "<h4>\\1</h4>", $body );
	// ===header 3===
	$body = preg_replace ( "/<p>===([^=]+)===<br \/>\n/", "<h3>\\1</h3>\n<p>", $body );
	// ===header 3===
	$body = preg_replace ( "/<p>===([^=]+)===<\/p>/", "<h3>\\1</h3>", $body );
	// ==header 2==
	$body = preg_replace ( "/<p>==([^=]+)==<br \/>\n/", "<h2>\\1</h2>\n<p>", $body );
	// ==header 2==
	$body = preg_replace ( "/<p>==([^=]+)==<\/p>/", "<h2>\\1</h2>", $body );
	// **spoilers**
	$body = preg_replace ( "/\*\*(.*?)\*\*/", "<span class=\"spoiler\">\\1</span>", $body );
	// ++strikeout++
	$body = preg_replace ( "/\+\+(.*?)\+\+/", "<del>\\1</del>", $body );
	// %%highlight%%
	$body = preg_replace ( "/%%(.*?)%%/", "<span class=\"highlight\">\\1</span>", $body );
	// '''bold'''
	$body = preg_replace ( "/'''(.*?)'''/", "<strong>\\1</strong>", $body );
	// ''italic''
	$body = preg_replace ( "/''(.*?)''/", "<em>\\1</em>", $body );
	// __underline__ (treat as emphasis)
	$body = preg_replace ( "/__(.*?)__/", "<em>\\1</em>", $body );
	// em dash
	$body = str_replace ( '--', '—', $body );
	// ellipsis
	$body = str_replace ( '...', '…', $body );
	return "$body\n";
}
function reply_linkage ( $matches ) {
	global $prev_ID;
	// take each @reply and link it to the proper post
	$reply_ID = $matches[2];
	$reply_ID_as_int = ( int ) preg_replace ( INTEGER_PATTERN, "", $reply_ID );
	if ( isset ( $prev_ID ) && $reply_ID_as_int == $prev_ID ) {
		// the linked reply is the previous post
		$reply_ID = "previous";
	}
	return "<a href=\"#reply-" . preg_replace ( "/[^0-9]/", "", $matches[2] ) . "\" class=\"at_reply\" title=\"" . preg_replace ( "/[^0-9]/", "", $matches[2] ) . "\" onclick=\"handle_at_reply(this);\">@$reply_ID</a>$matches[3]";
	// TODO: detect if the linked post was deleted or does not exist within this thread?
}

function anonymous_user_mapping ( $record ) {
	global $user_accounts, $controller;
	$output = '<strong>';
	$meta = array ( );
	// only bother with this if we’re outside the admin area
	if ( $controller != 'admin' ) {
		// gets position of user ID in this topic
		$ID_map = array_search ( $record->user_ID ( ), $user_accounts );
	}
	if ( strlen ( $record->name ( ) ) > 0 ) {
		// clean up the name output
		$name = htmlspecialchars ( $record->name ( ) );
	}
	$joined_in = 0;
	if ( $ID_map !== FALSE ) {
		// this user has already joined the topic
		if ( isset ( $name ) ) {
			$output .= $name;
		} else {
			$output .= num_to_alpha_counter ( $ID_map + 1 );
		}
		if ( 0 === $ID_map ) {
			$meta[] = 'OP';
		}
	} else {
		// this user has joined in
		$user_accounts[] = $record->user_ID ( );
		if ( isset ( $name ) ) {
			// name
			$output .= $name;
		} else {
			// anonymous
			$output .= num_to_alpha_counter ( count ( $user_accounts ) );
		}
		$joined_in = 1;
	}
	$output .=  '</strong>';
	if ( strlen ( $record->tripcode ( ) ) > 0 ) {
		// add the tripcode
		$output .= ' ' . $record->tripcode ( );
	}
	if ( $controller == 'admin' ) {
		// display additional information in the CMS
		$output .= ' [User #' . number_format ( $record->user_ID ( ) ) . ']';
	}
	if ( isset ( $_SESSION['logged_in_user'] ) && $record->user_ID ( ) == $_SESSION['logged_in_user']['ID'] ) {
		// this post is yours
		$meta[] = 'you';
	}
	if ( count ( $meta ) > 0 ) {
		// combine the strings
		$output .= ' (' . implode ( ', ', $meta ) . ')';
	}
	if ( get_class ( $record ) == 'Reply' && $joined_in == 1 ) {
		// first post by this user in this thread
		$output .= ' joined in and';
	}
	return $output;
}

function num_to_alpha_counter ( $num ) {
	// from vdklah at hotmail dot com
	// http://php.net/manual/en/function.chr.php
	// returns a numeric ID mapped to sequentially-growing ASCII values (A–Z, AA-ZZ)
	$anum = '';
	while ( $num >= 1 ) {
		$num = $num - 1;
		$anum = chr ( ( $num % 26 ) + 65 ) . $anum;
		$num = $num / 26;
	}
	return $anum;
}

function user_type_link ( $record ) {
	switch ( $record->type ( ) ) { 
		case MODERATOR_TYPE:
			// is there a delegation assignment in PHP?
			if ( ! defined ( 'MODERATOR_PAGE_LINK' ) ) {
				Setting::load ( array ( 'conditions' => "name = 'MODERATOR_PAGE_LINK'", 'select' => min_settings_fields ( ) ) );
			}
			if ( defined ( 'MODERATOR_PAGE_LINK' ) ) {
				return MODERATOR_PAGE_LINK;
			}
			return '';
		break;
		case ADMIN_TYPE:
			// is there a delegation assignment in PHP?
			if ( ! defined ( 'ADMIN_PAGE_LINK' ) ) {
				Setting::load ( array ( 'conditions' => "name = 'ADMIN_PAGE_LINK'", 'select' => min_settings_fields ( ) ) );
			}
			if ( defined ( 'ADMIN_PAGE_LINK' ) ) {
				return ADMIN_PAGE_LINK;
			}
			return '';
		break;
		default:
			return '';
		break;
	}
}

function humanize_bytes ( $bytes ) {
	$bytes = ( int ) $bytes;
	// is there a faster native procedure than division, on contemporary x86?
	switch ( $bytes ) {
		case 0:
			return '0 bytes';
			break;
		case $bytes >= ( 1073741824 ):
			return round ( ( $bytes / 1073741824 ), 2 ) . " GB";
			break;
		case $bytes >= ( 1048576 ):
			return round ( ( $bytes / 1048576 ), 2 ) . " MB";
			break;
		case $bytes >= ( 1024 ):
			return round ( ( $bytes / 1024 ), 2 ) . " KB";
			break;
		default:
			return $bytes . " bytes";
			break;
	}
}

function seconds_to_time ( $seconds, $precision = 0 ) {
	$seconds = ( int ) $seconds;
	$precision = ( int ) $precision;
	$my_time_string = '';
	$my_time_float = ( float ) 0;
	// is there a faster native procedure than division, on contemporary x86?
	// do floats and integers have significant performance differences, for division?
	if ( $seconds >= 31536000 ) {
		$my_time_float = $seconds / 31536000;
		$my_time_string = 'year';
	} else if ( $seconds >= 2592000 ) {
		$my_time_float = $seconds / 2592000;
		$my_time_string = 'month';
	} else if ( $seconds >= 604800 ) {
		$my_time_float = $seconds / 604800;
		$my_time_string = 'week';
	} else if ( $seconds >= 86400 ) {
		$my_time_float = $seconds / 86400;
		$my_time_string = 'day';
	} else if ( $seconds >= 3600 ) {
		$my_time_float = $seconds / 3600;
		$my_time_string = 'hour';
	} else if ( $seconds >= 60 ) {
		$my_time_float = $seconds / 60;
		$my_time_string = 'minute';
	} else {
		$my_time_float = $seconds;
		$my_time_string = 'second';
	}
	if ( $precision == 0 ) {
		$my_time_string = floor ( $my_time_float ) . ' ' . $my_time_string;
		if ( intval ( $my_time_float ) != 1 ) {
			$my_time_string .= 's';
		}
	} else {
		$my_time_string = round ( $my_time_float, $precision ) . ' ' . $my_time_string;
	}
	return $my_time_string;
}

function titleize ( $string ) {
	$string = strtolower ( str_replace ( '_', ' ', $string ) );
	$words = explode ( ' ', $string );
	$count = count ( $words );
	for ( $i = 0; $i < $count; $i++ ) {
		$words[$i] = strtoupper ( substr ( $words[$i], 0, 1 ) ) . substr ( $words[$i], 1 );
	}
	$string = implode ( ' ', $words );
	return $string;
}


?>