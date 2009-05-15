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
require_once('auth.php');
require_once('database2.php');
require_once('templating.php');
require_once('config.php');

global $u_user;
$username = $u_user->name;
$userlevel = $u_user->userlevel;

function sendEmail($email) {
	global $adodb;
	global $base_url;
	global $u_user;
	$username = $u_user->name;
	$code = md5(md5($username) . time());

	// Insert the invitation into the table
	$sql = 'INSERT INTO Invitations (inviter, code) VALUES ('
			. $adodb->qstr($username) . ', '
			. $adodb->qstr($code) . ')';

	try {
		$adodb->Execute($sql);
	}
	catch (exception $e) {
		die($e->getMessage());
	}

	$url = $base_url . '/register.php?authcode=' . $code;
	$headers = 'From: Libre.fm Invitations <invitations@libre.fm>';
	$subject = 'Libre.fm Invitation';
	$body = 'Hi!' . "\n\n" . 'You requested an invite to libre.fm, and here it is! Just click the link and fill in your details.';
	$body .= "\n\n" . $url;
	$body .= "\n\n - The Libre.fm Team";
	mail($email, $subject, $body, $headers);
	unset($url, $subject, $body, $headers);
}

if ($userlevel < 2) {
	$smarty->assign('error', 'Error!');
	$smarty->assign('details', 'Invalid privileges.');
	$smarty->display('error.tpl');
	die();
} else {
	$action = $_GET['action'];
	if (isset($action)) {
		if ($action == 'invite') {
			if (!isset($_GET['email'])) {
				$smarty->assign('error', 'Error!');
				$smarty->assign('details', 'Missing email.');
				$smarty->display('error.tpl');
				die();
			} else {
				// Send the email
				sendEmail($_GET['email']);
				$smarty->assign('sent', true);
				$sql = 'UPDATE Invitation_Request SET status=1 WHERE email=' . $adodb->qstr($_GET['email']);
				$adodb->Execute($sql);
			}
		} else {
			$smarty->assign('error', 'Error!');
			$smarty->assign('error', 'Missing argument!');
			$smarty->display('error.tpl');
			die();
		}
	}

}

$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
$data = $adodb->GetAll('SELECT email,status FROM Invitation_Request ORDER BY time ASC');
$smarty->assign('emails', $data);
$smarty->display('admin.tpl');
?>
