<?php
// this code donated by somebody on TinyChan
header ( 'Status: 404', TRUE );
die();

/* CONFIG ========== */
ini_set ( 'date.timezone', 'GMT' );
header ( 'Content-Type: text/plain' );

$now = time ( );
$since = $now - 1800; // 5 minutes
$htaccess = 'htaccess.txt';
file_put_contents ( $htaccess, '' ); // clear this out
$apachelogfile ='ip_log.txt';
$handle = @fopen ( $apachelogfile, "r" );
$exess_number_of_cnx = 50; // how many hits an ip has to have made in the last 5 min before they are banned
$date_regex = '/\t(\d+)\b/'; // change this to how dates are formatted in your log file

/* SETUP ========== */
echo "Serious anteating, lolz…\n";
echo "Now:\t\t" . $now . "\n";
echo "Since:\t\t" . $since . "\n";
echo "\n";

function ban_ip ( $ip ) {
	global $banned, $htaccess;
	$banned[] = $ip;
	file_put_contents ( $htaccess, "deny from $ip\n", FILE_APPEND );
}

$ips = array ( );
$banned = array ( );


if ( $handle ) {
	while ( !feof ( $handle ) ) {
		$buffer = fgets ( $handle, 4096 ); // Read a line.
		if ( !isset ( $time ) && preg_match ( $date_regex, $buffer, $m ) ) {
			$time = $m[0];
		} elseif ( isset ( $time ) ) {
			if ( ( $now - $time ) > 1800 ) { // we only want to look through hits in the last five mintues
				echo "wrong time\n";
				break;
			} elseif ( preg_match ( '/(\b(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\b)/', $buffer , $m ) ) {
				$ip = $m[0];
				echo ".";
				if ( ! in_array ( $ip, $banned ) && $ips[$ip] > $exess_number_of_cnx ) { 
					echo "\nBAN!\t$ip\n";
					ban_ip ( $ip );
				} else {
					$ips[$ip]++;
				}
			}
		} 
	}
	fclose ( $handle ); // Close the file.
}
?>

