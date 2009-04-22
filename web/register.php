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
require_once("utils/EmailAddressValidator.php");

// Moving to open alpha
/*$authcode = $_GET["authcode"];

$res = $mdb2->query("SELECT inviter FROM Invitations WHERE code = " . $mdb2->quote($authcode, "text"));
if(!$res->numRows()) {
	$invalid_authcode = true;
} else {
	$invalid_authcode = false;
}*/
function sendEmail($text, $email) {
        $headers = 'From: Libre.fm Account Activation <account@libre.fm>';
	$subject = 'Libre.fm Account Activation - Action needed!';
	mail($email, $subject, $text, $headers);
}
if(isset($_GET['auth'])) {
	$authcode = $_GET['auth'];
	$res = $mdb2->query("SELECT * FROM AccountActivation WHERE authcode = " . $mdb2->quote($authcode, 'text'));
	if (PEAR::isError($res) || !$res->numRows()) {
		$errors = "Unknown activationcode.";
		$smarty->assign('errors', $errors);
		$smarty->display('error.tpl');
		die();
	}

	$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

	$sql = "UPDATE Users SET active = 1 WHERE username = " . $row['username'];
	$res = $mdb2->exec($sql);
	if (PEAR::isError($res)) {
	    $errors = $res->getUserInfo();
	    $smarty->assign('errors', $errors);
	    $smarty->display('error.tpl');
	    die();
	}
	$smarty->assign('activated', true);
	$smarty->display('register.tpl');
	die();
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
	if(!preg_match("/^[a-zA-Z0-9_]{3,16}$/", $username)) {
		$errors .= "Your username must be atleast 3 characters in length (max 16) and only consist of <i>a-z, A-Z, 0-9</i> and _ (underscore).<br />";
	}
	if(empty($password)) {
		$errors .= "You must enter a password.<br />";
	}
	if($password != $passwordrepeat) {
		$errors .= "Your passwords do not match.<br />";
	}
	if(empty($email)) {
		$errors .= "You must enter an e-mail address.<br />";
	} else {
	    $validator = new EmailAddressValidator();
	    if (!$validator->check_email_address($email)) {
		$errors .= "You must provide a valid email address!<br />";
	    }
	}

	//Check this username is available
	$res = $mdb2->query("SELECT username FROM Users WHERE lower(username) = " . $mdb2->quote(strtolower($username)));
	if($res->numRows()) {
		$errors .= "Sorry, that username is already registered.<br />";
	}

	if(empty($errors)) {
		// Create the user
		$sql = "INSERT INTO Users (username, password, email, fullname, bio, location, created, active) VALUES ("
			. $mdb2->quote($username, "text") . ", "
			. $mdb2->quote(md5($password), "text") . ", "
			. $mdb2->quote($email, "text") . ", "
			. $mdb2->quote($fullname, "text") . ", "
			. $mdb2->quote($bio, "text") . ", "
			. $mdb2->quote($location, "text") . ", "
			. time() . ", 0)";
		$insert = $mdb2->exec($sql);
		if (PEAR::isError($insert)) {
		    reportError("Create user, insert, register.php", $res->getUserInfo());
		    $errors .= "An error occurred.";
		    $smarty->assign('errors', $errors);
		    $smarty->display('error.tpl');
		    die();
		}

		$code = md5($username . time());
		$sql = "INSERT INTO AccountActivation (username, authcode) VALUES("
			. $mdb2->quote($username, 'text') . ", "
			. $mdb2->quote($code, 'text') . ")";
		$res = $mdb2->exec($sql);

		if (PEAR::isError($res)) {
		    reportError("AccountActivation, insert, register.php", $res->getUserInfo());
		    $errors .= "An error occurred.";
		    $smarty->assign('errors', $errors);
		    $smarty->display('error.tpl');
		    die();
		}

		$url = $base_url . "/register.php?auth=" . $code;
		$content = "Hi!\n\nSomeone from the IP-address " . $_SERVER['REMOTE_ADDR'] . " registered an account "
		    . "@ http://alpha.libre.fm. If this was you, please visit the webpage specified below to activate "
		    . "your account. If not, please disregard this email.\n\n" . $url . "\n\n- The Libre.fm Team";
		sendEmail($content, $email);

		// Remove auth code and set their username as the invitee
		//$mdb2->query("UPDATE Invitations SET code = NULL, invitee = " . $mdb2->quote($username, "text") . " WHERE code = " . $mdb2->quote($authcode, "text"));
		//$removesql = "DELETE FROM Invitation_Request WHERE email=" . $mdb2->quote($email, 'text');
		//$mdb2->exec($removesql);
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
//$smarty->assign("invalid_authcode", $invalid_authcode);
//$smarty->assign("authcode", $authcode);

$smarty->display("register.tpl");
?>
