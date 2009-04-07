/* Libre.fm -- a free network service for sharing your music listening habits

   Copyright (C) 2009 Libre.fm Project

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

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
