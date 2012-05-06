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
define ( 'IMGBOARD_INIT', 1 );
//die('Installation is turned off.');

require ( 'config/config.php' );
require ( 'include/initializers.php' );
require ( 'include/class_loader.php' );
_APPCONFIG::start_app ( ); // initiates DB connection
require ( 'include/helpers_core.php' );

global $DB;
if ( $DB->connect_error ) {
	die ( "<h1>BBSOOP cannot install</h1>\n<p>No database was found; edit <code>config/_APPCONFIG.class.php</code> to reflect your database configuration.</p>\n<p><a href=\"http://authorizedclone.com/bbsoop/\">BBSOOP</a>.</p>\n" );
}
header( 'Content-Type: text/html; charset=utf-8' );
$errors = array ( );
$warnings = array ( );

/* check if installed; die if yes
================================================== */
$my_result = $DB->query ( "SHOW TABLE STATUS LIKE '%'" );
if ( $my_result->num_rows > 0 ) {
	// possibly installed, check versions
	$invalid_tables = 0;
	while ( $row = $my_result->fetch_object ( ) ) {
		preg_match ( "/^.*?;|.*/", $row->Comment, $m );
		$my_version = preg_replace ( "/[^0-9\.]/", '', $m );
		if ( $my_version[0] != IMGBOARD_VERSION ) {
			echo 'Table ' . $row->Name . ' is out of date.';
		}
	}
	if ( $invalid_tables > 0 ) {
		die ( 'Update needed (but not available).' );
	} else {
		die ( "<h1>Success.</h1>\n<p>BBSOOP is installed and up-to-date.</p>" );
	}
}


/* check for version matching; die if fail
================================================== */
$php_version    = round ( phpversion ( ), 1 );
$mysql_version  = ( int ) preg_replace ( "/[^0-9]/", '', $DB->server_version );
if ( $php_version < 5.2 ) {
	$errors[] = 'PHP version 5.2 or greater is required. This computer is running version ' . phpversion ( ) . '.';
}
if ( $php_version >= 5.3 ) {
	$warnings[] = 'PHP version 5.3 (or greater) may work, but is not officially tested. This computer is running version ' . phpversion ( ) . '.';
}
if ( $mysql_version < 40000 ) {
	$errors[] = 'MySQL version 4 or greater is required. This computer is running version ' . $DB->server_version . '.';
}
if ( $mysql_version > 40000 ) {
	$warnings[] = 'MySQL version 4 may work, but is not officially tested. This computer is running version ' . $DB->server_version . '.';
}
if ( $mysql_version < 50000 ) {
	$warnings[] = 'MySQL version 5 or greater is recommended. This computer is running version ' . $DB->server_version . '.';
}

/* check for config matching; die if fail
================================================== */
// functions that are either disabled or not included
$required_functions = array (
	'sha1', 
	'json_encode', 
	'mysqli_connect', 
	'session_start'
);
foreach ( $required_functions as $required_function ) {
	if ( ! function_exists ( $required_function ) ) {
		$errors[] = 'The PHP function “' . $required_function . '” is required, but not currently available.';
	}
}
$required_ini_bools = array (
	'register_globals' => 0, 
	'disable_classes' => 0, 
	'disable_functions' => 0, 
	'safe_mode' => 0
);
foreach ( $required_ini_bools as $key => $value ) {
	$my_ini = ini_get ( $key );
	if ( $my_ini != $value ) {
		$errors[] = 'The PHP ini “' . $key . '” is currently set as “' . ( ( $my_ini == 1 ) ? 'On' : 'Off' ) . ',” but must be set as “' . ( ( $value == 1 ) ? 'On' : 'Off' ) . '.”';
	}
}
$recommended_ini = array (
	'display_errors' => array ( 0, 'is recommended for a production website' ), 
	'file_uploads' => array ( 1, 'is necessary for image uploads' ), 
	'register_long_arrays' => array ( 0, 'can increase performance' )
);
foreach ( $recommended_ini as $key => $value ) {
	$my_ini = ini_get ( $key );
	if ( $my_ini != $value[0] ) {
		$warnings[] = 'The PHP ini “' . $key . '” is currently set as “' . ( ( $my_ini == 1 ) ? 'On' : 'Off' ) . '.” Setting it to “' . ( ( $value[0] == 1 ) ? 'On' : 'Off' ) . '” ' . $value[1] . '.';
	}
}
$recommended_classes = array (
	'Imagick'
);
foreach ( $recommended_classes as $recommended_class ) {
	if ( ! class_exists ( $recommended_class ) ) {
		$warnings[] = 'The PHP class “' . $recommended_class . '” is recommended, but not currently available.';
	}
}
$my_query = "SHOW ENGINES";
if ( $result = $DB->query ( $my_query ) ) {
	$innoDB = 0;
	$MyISAM = 0;
	while ( $row = $result->fetch_object ( ) ) {
		if ( strtolower ( $row->Engine ) == 'innodb' && ( strtolower ( $row->Support ) == 'yes' || strtolower ( $row->Support ) == 'default' ) ) {
			$innoDB = 1;
		}
		if ( strtolower ( $row->Engine ) == 'myisam' && ( strtolower ( $row->Support ) == 'yes' || strtolower ( $row->Support ) == 'default' ) ) {
			$MyISAM = 1;
		}
	}
	$result->free_result ( );
}
if ( $MyISAM == 0 && $innoDB == 0 ) {
	$errors[] = 'The InnoDB and MyISAM database engines could not be found.';
}
if ( _APPCONFIG::default_keys ( ) > 0 ) {
	$errors[] = 'The config/_APPCONFIG.class.php file still has default application keys listed; use these values…';
	$count = _APPCONFIG::default_keys ( );
	for ( $i=0; $i < $count; $i++ ) {
		// 128-char keys
		$errors[] = random_password ( 16 ) . random_password ( 16 ) . random_password ( 16 ) . random_password ( 16 ) . random_password ( 16 ) . random_password ( 16 ) . random_password ( 16 ) . random_password ( 16 );
	}
}
//print_r ( $engines );
//print_r ( $errors );
//print_r ( $warnings );


/* process setup if POST
================================================== */
if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
	include ( 'include/schema.php' );
	session_start ( );
	$logged_in_user = UserAccount::find ( 1 );
	$_SESSION['logged_in_user'] = array ( );
	$_SESSION['logged_in_user']['ID'] = $logged_in_user->ID ( );
	$_SESSION['logged_in_user']['username'] = $logged_in_user->username ( );
	$_SESSION['logged_in_user']['type'] = $logged_in_user->type ( );
	$_SESSION['logged_in_user']['status'] = $logged_in_user->status;
	$_SESSION['notice'] = 'Welcome to BBSOOP!';
	if ( LOG_ACTIONS == 1 ) {
		$action_log = new Action ( array ( 'remote_address' => $_SERVER['REMOTE_ADDR'], 'user_ID' => $logged_in_user->ID ( ), 'record_ID' => $logged_in_user->ID ( ), 'record_table' => UserAccount::table, 'record_class' => 'UserAccount', 'description' => 'logged in', 'action' => 'select' ) );
		$action_log->create ( );
	}
	header ( 'Location: http://' . DOMAIN . BASE_URI, TRUE, 303 );
	exit ( );
}

/* display form for installation
================================================== */

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-US">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>Install — <?php echo SITE_TITLE; ?></title>
		<meta name="description" content="index | board" />
		<meta http-equiv="content-language" content="en" />
		<link rel="stylesheet" href="/bbsoop/content/themes/default/assets/stylesheets/screen.css" type="text/css" media="screen" />
		<!--link rel="stylesheet" href="/bbsoop/content/themes/default/assets/stylesheets/bbs.css" type="text/css" media="screen" /-->
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
		<script type="text/javascript" src="/bbsoop/content/themes/default/assets/javascripts/application.js"></script>
		<script type="text/javascript">
			function validate_installation ( ) {
				errors = 0;
				if ( jQuery('#user_account_username').val().length == 0 ) {
					errors++;
					jQuery('label[for=user_account_username]').addClass('required');
				}
				if ( jQuery('#user_account_password').val().length == 0 ) {
					errors++;
					jQuery('label[for=user_account_password]').addClass('required');
				}
				if ( jQuery('#user_account_password_confirm').val().length == 0 ) {
					errors++;
					jQuery('label[for=user_account_password_confirm]').addClass('required');
				}
				if ( jQuery('#user_account_password').val() != jQuery('#user_account_password_confirm').val() ) {
					errors++;
					jQuery('label[for=user_account_password_confirm]').addClass('required');
					jQuery('label[for=user_account_username]').addClass('required');
					alert ( "The passwords you entered do not match." );
				}
				if ( jQuery('#user_account_email_address').val().length == 0 ) {
					errors++;
					jQuery('label[for=user_account_email_address]').addClass('required');
				}
				if ( errors == 0 ) {
					jQuery('#install-form').submit();
					return true;
				}
				alert ( "Please complete all form fields." );
				return false;
			}
		</script>
		<link rel="shortcut icon" href="/bbsoop/favicon.png" />
	</head>
	<body id="index" class="default_board board moderator logged-in">
		<div id="page" class="c">
			<div id="header" class="c">
				<h2>BBSOOP installation</h2>
				<p class="meta"><a href="http://authorizedclone.com/bbsoop/documentation/">Documentation</a></p>
			</div>
			<div id="content" class="c">
				<h1>Hello.</h1>
<?php if ( count ( $errors ) == 0 ) { ?>
				<p>You’re just a few minutes away from a working BBSOOP application.</p>
<?php } else if ( count ( $errors ) > 0 ) { ?>
				<h2 class="rule">Errors</h2>
				<div id="errors">
<?php foreach ( $errors as $error ) { ?>
					<p><?php echo $error; ?></p>
<?php } ?>
				</div>
				<p>These errors must be corrected before you can install BBSOOP.</p>
<?php } ?>
<?php if ( count ( $warnings ) > 0 ) { ?>
				<h2 class="rule">Warnings</h2>
				<div id="notice">
<?php foreach ( $warnings as $warning ) { ?>
					<p><?php echo $warning; ?></p>
<?php } ?>
				</div>
				<p>These warnings will not prevent installation, but are worth addressing if you have adequate access to the computer.</p>
<?php } ?>
				<form action="" id="install-form" method="post" class="wrapper c">
					<h3 class="rule">Database settings</h3>
					<p><span class="help">Edit config/_APPCONFIG.class.php to adjust these values.</span></p>
					<p><label class="mlarge">Database name</label> <input type="text" class="large" value="<?php echo htmlspecialchars ( _APPCONFIG::$db_name ); ?>" readonly="readonly" /></p>
					<p><label class="mlarge">Table prefix</label> <input type="text" class="large" value="<?php echo htmlspecialchars ( ( ( empty ( _APPCONFIG::$table_prefix ) ) ? '(none)' : _APPCONFIG::$table_prefix ) ); ?>" readonly="readonly" /> <span class="help">allows multiple applications to be installed in one database</span></p>
					<h3 class="rule">Configuration</h3>
					<p><span class="help">Edit config/config.php to adjust these values.</span></p>
					<p><label class="mlarge">BASE_PATH</label> <input type="text" class="xlarge" value="<?php echo htmlspecialchars ( BASE_PATH ); ?>" readonly="readonly" /></p>
					<p><label class="mlarge">DOMAIN</label> <input type="text" class="xlarge" value="<?php echo htmlspecialchars ( DOMAIN ); ?>" readonly="readonly" /></p>
					<p><label class="mlarge">BASE_URI</label> <input type="text" class="xlarge" value="<?php echo htmlspecialchars ( BASE_URI ); ?>" readonly="readonly" /></p>
					<p><label class="mlarge">session_name</label> <input type="text" class="mlarge" value="<?php echo htmlspecialchars ( session_name ( ) ); ?>" readonly="readonly" /> <span class="help">must be unique on this domain</span></p>
<?php if ( count ( $errors ) == 0 ) { ?>
					<h3 class="rule">Create your adminstrative account</h3>
					<p><label for="user_account_username" class="mlarge">Username</label> <input type="text" class="large required_field" value="" maxlength="60" name="user_account[username]" id="user_account_username" /> <span class="help">1–60 characters; no limits on character type</span></p>
					<p><label for="user_account_password" class="mlarge">Password</label> <input type="password" class="mlarge required_field" value="" maxlength="60" name="user_account[password]" id="user_account_password" /> <span class="help">1–60 characters; no limits on character type</span></p>
					<p><label for="user_account_password_confirm" class="mlarge">Confirm password</label> <input type="password" class="mlarge required_field" value="" maxlength="60" name="user_account[password_confirm]" id="user_account_password_confirm" /> <span class="help">type it again to confirm</span></p>
					<p><label for="user_account_email_address" class="mlarge">Email address</label> <input type="text" class="large required_field" value="" maxlength="125" name="user_account[email_address]" id="user_account_email_address" /> <span class="help">allows password resetting; type “none” to disable this feature (improves security)</span></p>
					<p><label class="mlarge">&nbsp;</label> <input type="submit" value="Install BBSOOP" onclick="validate_installation(); return false;" /></p>
<?php } ?>
				</form>
				<h3 class="rule">Support</h3>
				<p>Official BBSOOP application support is available through Authorized Clone LLC for $65 an hour. Custom development and modifications can be arranged for approximately $80 an hour.</p>
				<p><a href="http://authorizedclone.com/">authorizedclone.com</a><br /><script type="text/javascript">eval(decodeURIComponent('%64%6f%63%75%6d%65%6e%74%2e%77%72%69%74%65%28%27%3c%61%20%68%72%65%66%3d%22%6d%61%69%6c%74%6f%3a%61%75%74%68%6f%72%69%7a%65%64%63%6c%6f%6e%65%40%67%6d%61%69%6c%2e%63%6f%6d%22%3e%61%75%74%68%6f%72%69%7a%65%64%63%6c%6f%6e%65%40%67%6d%61%69%6c%2e%63%6f%6d%3c%2f%61%3e%27%29%3b'))</script></p>
			</div>
			<div id="footer" class="c">
				<p>© 2008–2010 Authorized Clone LLC</p>
				<noscript>
					<p><span class="required">Note:</span> your browser’s JavaScript is disabled; some site features may not fully function.</p>
				</noscript>
			</div>
		</div>
	</body>
</html>

