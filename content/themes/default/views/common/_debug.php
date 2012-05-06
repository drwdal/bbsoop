<?php
/* ==================================================

IMGBOARD © 2010 Authorized Clone LLC

================================================== */
if ( ! defined ( 'IMGBOARD_INIT' ) ) { header ( 'FAIL', TRUE, 403 ); die( ); }
?>
<div id="debug">
<h3>Debug</h3>
<pre>
<?php
global $board_name, $controller, $action, $ID, $fragment, $extra, $TRACKER, $DB, $queries, $RUNTIME_OPTIONS;
if ( DEBUG_LEVEL >= 1 ) {
	printf ( "Board: %s\n", $board_name );
	printf ( "Controller: %s\n", $controller );
	printf ( "Action: %s\n", $action );
	printf ( "ID: %s\n", $ID );
	printf ( "Fragment: %s\n", $fragment );
	printf ( "Extra: %s\n", $extra );
	if ( isset ( $_POST ) && count ( $_POST ) > 0 ) {
		echo "POST variables…\n";
		foreach ( $_POST as $key => $value ) {
			if ( is_array ( $value ) ) {
				echo "  [$key] =>\n";
				foreach ( $value as $k => $v ) {
					if ( is_array ( $v ) ) {
						echo "    [$k]\n";
						foreach ( $v as $l => $w ) {
							echo "       [$l] => $w\n";
						}
					} else {
						echo "    $k: $v\n";
					}
				}
			} else {
				echo "  [$key] => $value\n";
			}
		}
	}
}
if ( DEBUG_LEVEL >= 2 ) {
	echo "----------\n";
	printf ( "IMGBOARD version %s\n", IMGBOARD_VERSION );
	$process_time = microtime ( true ) - $TRACKER['start_time'];
	$process_time_view = $TRACKER['end_time_view'] - $TRACKER['start_time_view'];
	$latency = microtime ( true ) - $_SERVER['REQUEST_TIME'] - $process_time;
	printf ( "Approximate latency:	%f seconds\n", $latency );
	printf ( "Processed in:		%f seconds\n", $process_time );
	printf ( "View processed in:	%f seconds\n", $process_time_view );
	printf ( "SELECT queries: %d\n", $TRACKER['queries_select'] );
	printf ( "UPDATE queries: %d\n", $TRACKER['queries_update'] );
	printf ( "INSERT queries: %d\n", $TRACKER['queries_insert'] );
	printf ( "DELETE queries: %d\n", $TRACKER['queries_delete'] );
//	echo "Queries…\n";
//	foreach ( $queries as $key => $value ) {
//		echo '  ' . htmlspecialchars ( $value ) . "\n";
//	}
	if ( isset ( $TRACKER['nonce_valid'] ) ) {
		printf ( "nonce valid: %s\n", ( $TRACKER['nonce_valid'] ) );
	}
	printf ( "PHP memory usage: %s\n", humanize_bytes ( memory_get_peak_usage ( ) ) );
	printf ( "PHP memory limit: %s\n", ini_get ( 'memory_limit' ) );
	$pid = getmypid ( ); printf ( "Webserver memory usage: %s\n", humanize_bytes ( 1024 * ( int ) `ps --pid $pid --no-headers -o rss` ) );
}
if ( DEBUG_LEVEL >= 3 ) {
	echo "----------\n";
	printf ( "Session name: %s\n", session_name ( ) );
	printf ( "Session ID: %s\n", SESSION_ID );
	if ( isset ( $_SESSION ) && count ( $_SESSION ) > 0 ) {
		echo "Session variables…\n";
		foreach ( $_SESSION as $key => $value ) {
			if ( is_array ( $value ) ) {
				echo "  [$key] =>\n";
				foreach ( $value as $k => $v ) {
					echo "    [$k] => $v\n";
				}
			} else {
				echo "  $key: $value\n";
			}
		}
	}
	echo "Runtime options…\n";
	foreach ( $RUNTIME_OPTIONS as $key => $value ) {
		echo "  [$key] => $value\n";
	}
}
if ( DEBUG_LEVEL >= 4 ) {
	echo "----------\n";
	printf ( "Domain: %s\n", DOMAIN );
	printf ( "Base path: %s\n", BASE_PATH );
	printf ( "Theme path: %s\n", THEME_PATH );
	printf ( "Base URI: %s\n", BASE_URI );
	printf ( "Base asset URI: %s\n", BASE_ASSET_URI );
	echo "Included files:\n";
	$included_files = get_included_files();
	foreach ( $included_files as $filename ) {
		echo "  $filename\n";
	}
}
if ( DEBUG_LEVEL >= 5 ) {
	echo "----------\n";
	printf ( "PHP version: %s\n", phpversion ( ) );
	printf ( "Database server: %s\n", $DB->host_info );
	printf ( "Database server version: %d\n", $DB->server_version );
	printf ( "Database protocol version: %d\n", $DB->protocol_version );
	printf ( "Database character set: %s\n", $DB->character_set_name ( ) );
}
?>
</pre>
</div>
