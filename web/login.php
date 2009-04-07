<?php

require_once("database.php");
require_once("templating.php");

if(isset($_POST['login'])) {

	$errors = "";
	$username = $_POST['username'];
	$password = $_POST['password'];

	if(empty($username)) {
		$errors .= "You must enter a username.<br />";
	}
	if(empty($password)) {
		$errors .= "You must enter a password.<br />";
	}

	if(empty($errors)) {

		$res = $mdb2->query("SELECT username FROM Users WHERE " 
 			. " username = " . $mdb2->quote($username, "text")
			. " AND password = " . $mdb2->quote(md5($password), "text"));
		if(!$res->numRows()) {
			$errors .= "Invalid username or password.";
		} else {
			// Give the user a session id, like any other client
			$session_id = md5(md5($password) . time());
			$mdb2->query("INSERT INTO Scrobble_Sessions (username, sessionid, expires) VALUES ("
				. $mdb2->quote($username, "text") . ", "
				. $mdb2->quote($session_id, "text") . ", "
				. $mdb2->quote(time() + 604800) . ")"); // Web sessions last a week
			$_SESSION['session_id'] = $session_id;
			$logged_in = true;
			$smarty->assign("logged_in", true);
		}
	}
}

if($logged_in) {
	// Send the user to the welcome page when they've logged in
	$smarty->display("welcome.tpl");
} else {
	$smarty->display("login.tpl");
}
?>
