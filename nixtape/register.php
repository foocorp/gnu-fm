<?php

/* GNU FM -- a free network service for sharing your music listening habits

   Copyright (C) 2009 Free Software Foundation, Inc

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

require_once('database.php');
require_once('templating.php');
require_once('utils/EmailAddressValidator.php');

if ($logged_in == true) {
	header('Location: index.php');
	exit();
}

function sendEmail($text, $email) {
	$headers = 'From: Libre.fm Account Activation <account@libre.fm>';
	$subject = 'Libre.fm Account Activation - Action needed!';
	mail($email, $subject, $text, $headers);
}

if (isset($_GET['auth'])) {
	$authcode = $_GET['auth'];
	$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
	try {
		$row = $adodb->GetRow('SELECT * FROM AccountActivation WHERE authcode = ' . $adodb->qstr($authcode));
	} catch (Exception $e) {
		$errors = 'Unknown activationcode.';
		$smarty->assign('errors', $errors);
		$smarty->display('error.tpl');
		die();
	}

	$sql_update = 'UPDATE Users SET active = 1 WHERE username = ' . $adodb->qstr($row['username']);
	$sql_delete = 'DELETE FROM AccountActivation WHERE authcode = ' . $adodb->qstr($authcode);
	try {
		$res = $adodb->Execute($sql_update);
		$res = $adodb->Execute($sql_delete);
	} catch (Exception $e) {
		$errors = 'An error occurred.';
		$details = $e->getMessage();
		$smarty->assign('pageheading', $errors);
		$smarty->assign('details', $details);
		$smarty->display('error.tpl');
		die();
	}
	$smarty->assign('activated', true);
}

if (isset($_POST['register'])) {

	$errors = '';
	$username = $_POST['username'];
	$password = $_POST['password'];
	$passwordrepeat = $_POST['password-repeat'];
	$fullname = $_POST['fullname'];
	$email = $_POST['email'];
	$location = $_POST['location'];
	$bio = $_POST['bio'];


	//Basic validation
	if (!preg_match('/^[a-zA-Z0-9][a-zA-Z0-9_-]{1,14}[a-zA-Z0-9]$/', $username)) {
		$errors .= 'Your username must be at least 3 characters in length (max 16) and only consist of <i>a-z, A-Z, 0-9</i> and _ (underscore), and may not begin or end with an underscore.<br />';
	}
	if (empty($password)) {
		$errors .= 'You must enter a password.<br />';
	}
	if ($password != $passwordrepeat) {
		$errors .= 'Your passwords do not match.<br />';
	}
	if (empty($email)) {
		$errors .= 'You must enter an e-mail address.<br />';
	} else {
		$validator = new EmailAddressValidator();
		if (!$validator->check_email_address($email)) {
			$errors .= 'You must provide a valid email address!<br />';
		}
	}

	//Check this username is available
	try {
		$res = $adodb->GetOne('SELECT username FROM Users WHERE lower(username) = ' . $adodb->qstr(strtolower($username)));
	} catch (Exception $e) {
		$errors .= 'Database error.<br />';
	}
	if ($res) {
		$errors .= 'Sorry, that username is already registered.<br />';
	}

	if (empty($errors)) {
		// Create the user
		$sql = 'INSERT INTO Users (username, password, email, fullname, bio, location, created, active) VALUES ('
			. $adodb->qstr($username) . ', '
			. $adodb->qstr(md5($password)) . ', '
			. $adodb->qstr($email) . ', '
			. $adodb->qstr($fullname) . ', '
			. $adodb->qstr($bio) . ', '
			. $adodb->qstr($location) . ', '
			. time() . ', 0)';
		try {
			$insert = $adodb->Execute($sql);
		} catch (Exception $e) {
			reportError('Create user, insert, register.php', $e->getMessage());
			$errors .= 'An error occurred.';
			$details = $e->getMessage();
			$smarty->assign('pageheading', $errors);
			$smarty->assign('details', $details);
			$smarty->display('error.tpl');
			die();
		}

		$code = md5($username . time());
		$sql = 'INSERT INTO AccountActivation (username, authcode, expires) VALUES('
			. $adodb->qstr($username) . ', '
			. $adodb->qstr($code) . ', '
			. (time()+(86400*2)) . ')';
		try {
			$res = $adodb->Execute($sql);
		} catch (Exception $e) {
			reportError('AccountActivation, insert, register.php', $e->getMessage());
			$errors .= 'An error occurred.';
			$details = $e->getMessage();
			$smarty->assign('pageheading', $errors);
			$smarty->assign('details', $details);
			$smarty->display('error.tpl');
			die();
		}

		$url = $base_url . '/register.php?auth=' . $code;
		$content = "Hi!\n\nSomeone registered an account "
			. "at http://alpha.libre.fm. If this was you, please visit the webpage specified below to activate "
			. "your account within 48 hours, after which time all information provided by you and "
			. "your activation code will be permanently deleted from our database. If you do not want to activate your account, "
			. "please disregard this email.\n\n" . $url . "\n\n- The Libre.fm Team";
		sendEmail($content, $email);

		$smarty->assign('registered', true);
	} else {
		$smarty->assign('username', $username);
		$smarty->assign('fullname', $fullname);
		$smarty->assign('email', $email);
		$smarty->assign('location', $location);
		$smarty->assign('bio', $bio);
		$smarty->assign('errors', $errors);
	}
}

$smarty->display('register.tpl');
