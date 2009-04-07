<?php

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

require_once("database.php");
require_once("templating.php");

$authcode = $_GET["authcode"];

$res = $mdb2->query("SELECT inviter FROM Invitations WHERE code = " . $mdb2->quote($authcode, "text"));
if(!$res->numRows()) {
	$invalid_authcode = true;
} else {
	$invalid_authcode = false;
}

if(isset($_POST['register'])) {

	$errors = "";
	$username = $_POST['username'];
	$password = $_POST['password'];
	$passwordrepeat = $_POST['password-repeat'];
	$fullname = $_POST['fullname'];
	$email = $_POST['email'];
	$location = $_POST['location'];
	$bio = $_POST['bio'];


	//Basic validation
	if(empty($username)) {
		$errors .= "You must enter a username.<br />";
	}
	if(empty($password)) {
		$errors .= "You must enter a password.<br />";
	}
	if($password != $passwordrepeat) {
		$errors .= "Your passwords do not match.<br />";
	}
	if(empty($email)) {
		$errors .= "You must enter an e-mail address.<br />";
	}

	//Check this username is available
	$res = $mdb2->query("SELECT username FROM Users WHERE username = " . $mdb2->quote($username));
	if($res->numRows()) {
		$errors .= "Sorry, that username is already registered.<br />";
	}

	if(empty($errors) && !$invalid_authcode) {
		// Create the user
		$mdb2->query("INSERT INTO Users (username, password, email, fullname, bio, location, created) VALUES ("
			. $mdb2->quote($username, "text") . ", "
			. $mdb2->quote(md5($password), "text") . ", "
			. $mdb2->quote($email, "text") . ", "
			. $mdb2->quote($fullname, "text") . ", "
			. $mdb2->quote($bio, "text") . ", "
			. $mdb2->quote($location, "text") . ", "
			. time() . ")");
		// Remove auth code and set their username as the invitee
		$mdb2->query("UPDATE Invitations SET code = NULL, invitee = " . $mdb2->quote($username, "text") . " WHERE code = " . $mdb2->quote($authcode, "text"));
		$smarty->assign("registered", true);
	} else {
		$smarty->assign("username", $username);
		$smarty->assign("fullname", $fullname);
		$smarty->assign("email", $email);
		$smarty->assign("location", $location);
		$smarty->assign("bio", $bio);
		$smarty->assign("errors", $errors);
	}
}
$smarty->assign("invalid_authcode", $invalid_authcode);
$smarty->assign("authcode", $authcode);

$smarty->display("register.tpl");
?>
