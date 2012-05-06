/* INIT */
var mod_env_domain;
var mod_env_base_uri;
var mod_env_controller;
var mod_env_action;
var mod_env_ID;
var mod_env_fragment;
var mod_env_extra;
var nonce;

jQuery(document).ready(function(){
	mod_env_domain = jQuery('#mod_env_domain').val();
	mod_env_base_uri = jQuery('#mod_env_base_uri').val();
	mod_env_controller = jQuery('#mod_env_controller').val();
	mod_env_action = jQuery('#mod_env_action').val();
	mod_env_ID = jQuery('#mod_env_ID').val();
	mod_env_fragment = jQuery('#mod_env_fragment').val();
	mod_env_extra = jQuery('#mod_env_extra').val();
	nonce = jQuery('input[name=nonce]').val();
	jQuery.ajaxSetup({
		type: "POST"
	});
});

function mod_action ( record_class, record_ID, record_action, related_record_ID, related_record_class ) {
	record_class = record_class;
	jQuery.ajax({
		url: mod_env_base_uri + 'admin/mod_action',
		data: { record_class: record_class, record_ID: record_ID, record_action: record_action, related_record_ID: related_record_ID, related_record_class: related_record_class, nonce: nonce }, 
		dataType: 'json', 
		success: function ( data, textStatus, XMLHttpRequest ) {
			if ( data.action_result == '1' ) {
				// successful
				if ( data.record_action == 'deleted' ) {
					if ( record_class == 'Topic' ) {
						// return to topic index
						alert ( 'success' );
					} else if ( record_class == 'Media' ) {
						jQuery('#media-' + record_ID).slideUp('fast');
						jQuery('#moderation-media-' + related_record_ID).slideUp('fast');
					} else {
						if ( mod_env_controller == 'admin' ) {
							jQuery('#reply-' + record_ID).addClass('deleted');
						} else {
							jQuery('#reply-' + record_ID).slideUp('fast');
						}
					}
				}
			} else {
				alert ( 'failed to delete ' + record_class + ' ' + record_ID );
			}
		}, 
		error: mod_action_error
	});
}

function mod_remote_address ( record_ID, record_action ) {
	jQuery.ajax({
		url: mod_env_base_uri + 'admin/mod_remote_address',
		data: { record_ID: record_ID, record_action: record_action, nonce: nonce }, 
		dataType: 'json', 
		success: function ( data, textStatus, XMLHttpRequest ) {
			if ( data.action_result == '1' ) {
				// successful
				if ( data.new_status == '1' ) {
					$new_text = 'Ban';
					if ( record_action == 'view' ) {
						jQuery('#remote_address-' + record_ID + '-banned').html('');
					}
				} else {
					$new_text = 'Un-ban';
					if ( record_action == 'view' ) {
						jQuery('#remote_address-' + record_ID + '-banned').html('Banned');
					}
				}
				jQuery('#remote_address-' + record_ID + '-' + record_action).html($new_text);
			} else {
				alert ( 'failed to update remote address' );
			}
		}, 
		error: mod_action_error
	});
}

function mod_action_sanitize_result ( data_in, type ) {
	data_out = new Array();
	if ( type == 'json' ) {
		alert ( data_in );
		jQuery.each(data_in, function(key, value) {
			data_out[key] = value;
		});
	} else {
		return false;
	}
	return data_out;
}

function mod_action_error ( XMLHttpRequest, textStatus, errorThrown ) {
	alert ( XMLHttpRequest + ' ' + textStatus + ' ' + errorThrown );
}

function mod_confirm_action ( message, form_ID ) {
	var c=confirm(message);
	if (c == true){
		jQuery('#' + form_ID).submit();
		return true;
	} else {
		return false;
	}
}

function wordfilter_test ( test ) {
	if ( test == 'benchmark' ) {
		var wordfilter_test_mode = 'benchmark';
	} else {
		var wordfilter_test_mode = 'default';
	}
	wordfilter_case_sensitive = 0;
	if ( jQuery('#wordfilter_case_sensitive').attr('checked') ) {
		wordfilter_case_sensitive = 1;
	}
	jQuery.ajax({
		url: mod_env_base_uri + 'admin/wordfilter_test',
		data: {
			wordfilter_test_mode: wordfilter_test_mode, 
			wordfilter_case_sensitive: wordfilter_case_sensitive, 
			wordfilter_text: jQuery('#wordfilter_text').val(), 
			wordfilter_pattern: jQuery('#wordfilter_pattern').val(), 
			wordfilter_replacement: jQuery('#wordfilter_replacement').val(), 
			wordfilter_message: jQuery('#wordfilter_message').val(), 
			wordfilter_mode: jQuery('#wordfilter_mode').val(), 
			wordfilter_action: jQuery('#wordfilter_action').val()
		}, 
		dataType: 'json', 
		success: function ( data, textStatus, XMLHttpRequest ) {
			if ( data.action_result == '1' ) {
				alert ( data.action );
			} else {
				alert ( 'The text did not activate the filter.' );
			}
			if ( data.benchmark != undefined ) {
				jQuery('#wordfilter_benchmark').val(jQuery('#wordfilter_benchmark').val()+data.benchmark+"\n");
			}
			jQuery('#wordfilter_result').val(data.text);
			jQuery('#wordfilter_applied_pattern').val(data.applied_pattern);
		}, 
		error: mod_action_error
	});
}



