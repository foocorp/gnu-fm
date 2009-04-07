<?php

require_once('database.php');

session_start();

if(isset($_SESSION['session_id'])) {
	$res = $mdb2->query("SELECT username FROM Scrobble_Sessions WHERE "
		. "sessionid = " . $mdb2->quote($_SESSION['session_id'], "text")
	       	. " AND expires > " . time());
	if(!$res->numRows()) {
		// Session is invalid
		unset($_SESSION['session_id']);
	} else {
		$logged_in = true;
		$username = $res->fetchOne(0);
	}
}
?>
