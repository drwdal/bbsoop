/* VARS ========== */
var server_time;
var env_base_uri;
var session_ID;
var topic_title_characters_maximum;

jQuery(document).ready(function(){
	jQuery('.collapse').css('display', 'none'); // hide; fallback for clients without JS
	jQuery('.js_required').css('display', 'block'); // show; fallback for clients without JS
	/* ENV VARS ========== */
	env_base_uri = jQuery('#env_base_uri').val();
	session_ID = jQuery('#session_ID').val();

	/* REPLIES TIME AGO ========== */
	// get server time
	server_time = jQuery('#server_time').val();
	// adjust time
	jQuery('span.time').each(time_ago);
	// fix grammar
	jQuery('span.at-label').each(function(){jQuery(this).detach();});

	/* HIGHLIGHT ========== */
	// format highlight
	if ( location.hash != '' ) {
		if ( location.hash == '#topic' ) {
			jQuery('#topic').addClass('reply-highlight');
		} else if ( location.hash.substring(1,6) == 'reply' ) {
			jQuery(location.hash).addClass('reply-highlight');
		}
	}

	/* FORM FOCUS ========== */
	give_forms_focus ( );

	/* ADD UI FEATURES ========== */
	format_you_replies ( );
	if ( document.getElementById ( 'seconds_left_to_edit' ) ) {
		edit_countdown ( );
	}
	jQuery('input.has_calendar').each(function(){jQuery(this).datepicker({dateFormat:'yy-mm-dd'});});
	// characters remaining
	if ( document.getElementById ( 'topic_title_characters_maximum' ) ) {
		topic_title_chars_init ( );
	}
	jQuery('#notice').delay(3000).slideUp('fast');
	jQuery('#notice').click(function(){jQuery(this).dequeue();});
});



/* FUNCTIONS ========== */

function time_ago ( index, element ){
	my_seconds = jQuery(element).attr('title');
	jQuery(element).attr('title', jQuery(element).html() );
	my_time_diff = server_time - my_seconds;
	my_time_string = '';
	if ( my_time_diff >= 31536000 ) {
		my_time_int = Math.floor ( my_time_diff / 31536000 );
		my_time_string = my_time_int + ' year';
	} else if ( my_time_diff >= 2592000 ) {
		my_time_int = Math.floor ( my_time_diff / 2592000 );
		my_time_string = my_time_int + ' month';
	} else if ( my_time_diff >= 604800 ) {
		my_time_int = Math.floor ( my_time_diff / 604800 );
		my_time_string = my_time_int + ' week';
	} else if ( my_time_diff >= 86400 ) {
		my_time_int = Math.floor ( my_time_diff / 86400 );
		my_time_string = my_time_int + ' day';
	} else if ( my_time_diff >= 3600 ) {
		my_time_int = Math.floor ( my_time_diff / 3600 );
		my_time_string = my_time_int + ' hour';
	} else if ( my_time_diff >= 60 ) {
		my_time_int = Math.floor ( my_time_diff / 60 );
		my_time_string = my_time_int + ' minute';
	} else {
		my_time_int = my_time_diff;
		my_time_string = my_time_int + ' second';
	}
	if ( my_time_int != 1 ) {
		my_time_string += 's';
	}
	my_time_string += ' ago';
	jQuery(element).html(my_time_string);
}

function topic_title_chars_init ( ) {
	topic_title_characters_maximum = jQuery('#topic_title_characters_maximum').val();
	jQuery('#record_title').keypress(function(){
		my_title = jQuery(this).val();
		jQuery('#title-character-count').html(topic_title_characters_maximum - my_title.length + ' characters remaining');
	});
}

function give_forms_focus ( ) {
	if ( element = document.getElementById('record_title') || document.getElementById('record_body') || document.getElementById('username') || document.getElementById('private_message_body') ) {
		element.focus();
		return true;
	}
	switch ( location.hash ) {
		case '#change_password':
			element = jQuery('#password_new');
			break;
	}
	if ( element ) {
		element.focus();
	}
}

function play_video ( provider, media_ID, element, record_class, record_ID ) {
	my_ID = record_class + '-' + record_ID + '-media-' + media_ID;
	if ( jQuery(element).html() == 'play' ) {
		jQuery(element).html('close');
	} else {
		jQuery(element).html('play');
		jQuery('#' + my_ID).slideUp();
		return false;
	}
	var video_player_html = '';
	// reference for dimensions: http://www.adobe.com/devnet/flash/apps/flv_bitrate_calculator/video_sizes.html
	if ( provider == 'youtube' ) {
		video_player_html = '<div id="' + my_ID + '" style="display: none;" class="video wrapper c"><object width="853" height="505"><param name="movie" value="http://www.youtube-nocookie.com/v/' + media_ID + '&amp;hl=en_US&amp;fs=1&amp;border=1&amp;autoplay=1"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube-nocookie.com/v/' + media_ID + '&amp;hl=en_US&amp;fs=1&amp;border=1&amp;autoplay=1" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="853" height="505"></embed></object><a href="http://www.youtube.com/watch?v=' + media_ID + '" class="youtube_alternate"><img src="http://img.youtube.com/vi/' + media_ID + '/0.jpg" width="480" height="360" alt="Video" /></a></div>';
	} else if ( provider == 'vimeo' ) {
		video_player_html = '<div id="' + my_ID + '" style="display: none;" class="video wrapper c"><object width="769" height="442"><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=' + media_ID + '&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=1&amp;fullscreen=1&amp;autoplay=1" /><embed src="http://vimeo.com/moogaloop.swf?clip_id=' + media_ID + '&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;fullscreen=1&amp;autoplay=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="769" height="442"></embed></object></div>';
	} else if ( provider == 'myspace' ) {
		video_player_html = '<div id="' + my_ID + '" style="display: none;" class="video wrapper c"><object width="425" height="360"><param name="allowFullScreen" value="true"/><param name="wmode" value="transparent" /><param name="movie" value="http://mediaservices.myspace.com/services/media/embed.aspx/m=' + media_ID + ',t=1,mt=video"/><embed src="http://mediaservices.myspace.com/services/media/embed.aspx/m=' + media_ID + ',t=1,mt=video" width="425" height="360" allowFullScreen="true" type="application/x-shockwave-flash" wmode="transparent"></embed></object></div>';
	}
	jQuery(element).parent().after(video_player_html + "\n");
	jQuery('#' + my_ID).slideDown();
}

function handle_at_reply ( element ) {
	record_ID = jQuery(element).attr('title').replace(/[^0-9]/g,'');
	jQuery('.reply-highlight').each(function(){
		jQuery(this).removeClass('reply-highlight');
	});
	if ( record_ID == 'OP' ) {
		jQuery('#topic').addClass('reply-highlight');
	} else {
		jQuery('#reply-' + record_ID).addClass('reply-highlight');
	}
}

function format_you_replies ( ) {
	jQuery('div.you').each(function(){
		my_ID = jQuery(this).attr('id').replace('reply-','');
		jQuery('a[title=' + my_ID + ']').each(function(){
			jQuery(this).after(' <span class="you">(you)</span>');
		});
	});
}

function favorite_reply ( reply_ID ) {
	previous_value = jQuery('#favorites-reply-' + reply_ID).html();
	jQuery.ajax({
		url: env_base_uri + 'topics/favorite_reply',
		data: { 
			reply_ID: reply_ID, 
			session_ID: session_ID
		}, 
		dataType: 'json', 
		type: 'POST', 
		beforeSend: function () {
			jQuery('#favorites-reply-' + reply_ID + '-label').html(' sending…');
		}, 
		success: function ( data, textStatus, XMLHttpRequest ) {
			if ( data.result == '1' ) {
				if ( data.reply_favorites == 0 ) {
					jQuery('#favorites-reply-' + reply_ID + '-label').html( '' );
				} else if ( data.reply_favorites == 1 ) {
					jQuery('#favorites-reply-' + reply_ID + '-label').html( ' 1 favorite ' );
				} else {
					jQuery('#favorites-reply-' + reply_ID + '-label').html( ' ' + data.reply_favorites + ' favorites ' );
				}
				if ( data.action == 'added' ) {
					jQuery('#favorites-reply-' + reply_ID).html(' - ');
				} else {
					jQuery('#favorites-reply-' + reply_ID).html(' + ');
				}
			} else {
				jQuery('#favorites-reply-' + reply_ID).html( previous_value );
				alert ( 'Error: cannot favorite this reply.' );
			}
		}, 
		error: action_error, 
		complete: function () {
		}
	});
}

function favorite_topic ( topic_ID ) {
	previous_value = jQuery('#favorites-topic-' + topic_ID).html();
	jQuery.ajax({
		url: env_base_uri + 'topics/favorite_topic',
		data: { 
			topic_ID: topic_ID, 
			session_ID: session_ID
		}, 
		dataType: 'json', 
		type: 'POST', 
		beforeSend: function () {
			jQuery('#favorites-topic-' + topic_ID + '-label').html(' sending…');
		}, 
		success: function ( data, textStatus, XMLHttpRequest ) {
			if ( data.result == '1' ) {
				if ( data.topic_favorites == 0 ) {
					jQuery('#favorites-topic-' + topic_ID + '-label').html( '');
				} else if ( data.topic_favorites == 1 ) {
					jQuery('#favorites-topic-' + topic_ID + '-label').html( ' 1 favorite ');
				} else {
					jQuery('#favorites-topic-' + topic_ID + '-label').html( ' ' + data.topic_favorites + ' favorites ');
				}
				if ( data.action == 'added' ) {
					jQuery('#favorites-topic-' + topic_ID).html(' - ');
				} else {
					jQuery('#favorites-topic-' + topic_ID).html(' + ');
				}
			} else {
				jQuery('#favorites-reply-' + topic_ID + '-label').html( previous_value );
				alert ( 'Error: cannot favorite this reply.' );
			}
		}, 
		error: action_error, 
		complete: function () {
		}
	});
}

function report_reply ( reply_ID ) {
	if ( jQuery('#report-reply-' + reply_ID).html() == ' reported ' ) {
		alert ( 'Error: you have already reported this reply.' );
		return false;
	}
	jQuery.ajax({
		url: env_base_uri + 'topics/report_reply',
		data: { 
			reply_ID: reply_ID, 
			reason: 'rule violation', 
			session_ID: session_ID
		}, 
		dataType: 'json', 
		type: 'POST', 
		beforeSend: function () {
			jQuery('#report-reply-' + reply_ID).html(' sending…');
		}, 
		success: function ( data, textStatus, XMLHttpRequest ) {
			if ( data.result == '1' ) {
				if ( data.action != 'added' ) {
					alert ( 'You have already reported this reply.' );
				}
				jQuery('#report-reply-' + reply_ID).html(' reported ');
			} else {
				alert ( 'Error: cannot report this reply.' );
			}
		}, 
		error: action_error, 
		complete: function () {
		}
	});
}

function report_topic ( topic_ID ) {
	if ( jQuery('#report-topic-' + topic_ID).html() == ' reported ' ) {
		alert ( 'Error: you have already reported this topic.' );
		return false;
	}
	jQuery.ajax({
		url: env_base_uri + 'topics/report_topic',
		data: { 
			topic_ID: topic_ID, 
			reason: 'rule violation', 
			session_ID: session_ID
		}, 
		dataType: 'json', 
		type: 'POST', 
		beforeSend: function () {
			jQuery('#report-topic-' + topic_ID).html(' sending…');
		}, 
		success: function ( data, textStatus, XMLHttpRequest ) {
			if ( data.result == '1' ) {
				if ( data.action != 'added' ) {
					alert ( 'You have already reported this topic.' );
				}
				jQuery('#report-topic-' + topic_ID).html(' reported ');
			} else {
				alert ( 'Error: cannot report this topic.' );
			}
		}, 
		error: action_error, 
		complete: function () {
		}
	});
}

function username_check ( ) {
	jQuery.ajax({
		url: env_base_uri + 'account/username_check',
		data: { 
			username: jQuery('#username_new').val()
		}, 
		dataType: 'json', 
		type: 'POST', 
		beforeSend: function () {
			jQuery('#username_loading').css('display', 'inline');
		}, 
		success: function ( data, textStatus, XMLHttpRequest ) {
			if ( data.result == '1' ) {
				jQuery('#username_help').html('<strong>username available!</strong>');
				jQuery('#username_help').addClass('highlight');
			} else {
				jQuery('#username_help').html('<strong>username already taken</strong>');
			}
		}, 
		error: action_error, 
		complete: function () {
			jQuery('#username_loading').css('display', 'none');
		}
	});
}

function edit_countdown ( ) {
	var d = new Date();
	var edit_expires = parseInt(jQuery('#seconds_left_to_edit').attr('title'));
	var time_left = edit_expires - ( Math.round(d.getTime() / 1000) );
	if ( time_left > 0 ) {
		jQuery('#seconds_left_to_edit').html(time_left);
		var t=setTimeout("edit_countdown()",1000);
	} else {
		jQuery('#seconds_left_to_edit').html('0');
	}
}

function action_error ( XMLHttpRequest, textStatus, errorThrown ) {
	alert ( textStatus + ' ' + errorThrown );
}

