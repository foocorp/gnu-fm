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

// TODO: Check if the request has expired before changing.

require_once('database.php');
require_once('templating.php');
require_once('utils/EmailAddressValidator.php');

global $adodb;
$errors = '';

function sendEmail($text, $email) {
	$headers = 'From: Libre.fm Reset <recovery@libre.fm>';
	$subject = 'Libre.fm Password Reset';
	mail($email, $subject, $text, $headers);
}

if (isset($_GET['code'])) {
	$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
	$row = $adodb->GetRow('SELECT * FROM Recovery_Request WHERE code=' . $adodb->qstr($_GET['code']));
	if (!$row) {
		$errors .= "Invalid reset token.\n";
		$smarty->assign('errors', $errors);
		$smarty->display('error.tpl');
		die();
	}

	$password = '';
	$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';

	for ($i = 0; $i < 8; $i++) {
		$password .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
	}

	$email = $row['email'];

	$sql = 'UPDATE Users SET password=' . $adodb->qstr(md5($password)) . ' WHERE email='
		. $adodb->qstr($email);

	$adodb->Execute($sql);

	$content = "Hi!\n\nYour password has been set to " . $password . "\n\n - The Libre.fm Team";
	sendEmail($content, $email);
	$sql = 'DELETE FROM Recovery_Request WHERE code=' . $adodb->qstr($email);
	$adodb->Execute($sql);
	$smarty->assign('changed', true);
}

else if (isset($_POST['user'])) {
	$username = $_POST['user'];

	$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
	$err = 0;

	try {
		$row = $adodb->GetRow('SELECT * FROM Users WHERE username='
				. $adodb->qstr($username));
	}
	catch (exception $e) {
		$err = 1;
	}

	if ($err || !$row) {
		$errors .= "User not found.\n";
		$smarty->assign('errors', $errors);
		$smarty->display('error.tpl');
		die();
	}
	$code = md5($username . $row['email'] . time());
	
	// If a recovery_request already exists, delete it from the database
	$sql = 'SELECT COUNT(*) as c FROM Recovery_Request WHERE username =' . 
		$adodb->qstr($username);
	try {
		$res = $adodb->GetRow($sql);
		if ($res['c'] != 0) {
			$sql = 'DELETE FROM Recovery_Request WHERE username =' .
				$adodb->qstr($username);
			$adodb->Execute($sql);
		}
	} catch (exception $e) {
		$errors .= 'Error on: ' . $sql;
		$smarty->assign('errors', $errors);
		$smarty->display('error.tpl');
		die();
	}

	$sql = 'INSERT INTO Recovery_Request (username, email, code, expires) VALUES('
		. $adodb->qstr($username) . ', '
		. $adodb->qstr($row['email']) . ', '
		. $adodb->qstr($code) . ', '
		. $adodb->qstr(time() + 86400) . ')';

	try {
		$res = $adodb->Execute($sql);
	}
	catch (exception $e) {
		$errors .= 'Error on: ' . $sql;
		$smarty->assign('errors', $errors);
		$smarty->display('error.tpl');
		die();
	}

	$url = $base_url . '/reset.php?code=' . $code;
	$content = "Hi!\n\nSomeone from the IP-address " . $_SERVER['REMOTE_ADDR'] . " entered your username "
		. "in the password reset form at libre.fm. To change you password, please visit\n\n"
		. $url . "\n\n- The Libre.fm Team";
	sendEmail($content, $row['email']);
	$smarty->assign('sent', true);
}

$smarty->display('reset.tpl');
?>
