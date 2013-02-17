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
//TODO move html to template
require_once('../../database.php');
require_once('../../templating.php');

function displayError($error_msg) {
	global $smarty;
	$smarty->assign('error_msg', $error_msg);
	$smarty->display('api_auth.tpl');
	exit();
}

// Desktop app auth
if (isset($_POST['username'], $_POST['api_key'], $_POST['token'])) {

	// Authenticate the user using the submitted password
	try {
		$result = $adodb->GetOne('SELECT username FROM Users WHERE '
				. 'lower(username) = ' . $adodb->qstr(strtolower($_POST['username'])) . ' AND '
				. 'password = ' . $adodb->qstr(md5($_POST['password'])));
	} catch (Exception $e) {
		displayError('Database error');
	}
	if (!$result) {
		displayError('Authentication failed');
	}

	// Bind the user to the token and cancel the expiration rule
	try {
		$result = $adodb->Execute('UPDATE Auth SET '
				. 'username = ' . $adodb->qstr($_POST['username']) . ', '
				. 'expires = 0 '
				. 'WHERE '
				. 'token = ' . $adodb->qstr($_POST['token']));
	} catch (Exception $e) {
		displayError('Database error');
	}
	$smarty->assign('username', $_POST['username']);

} else if (!isset($_GET['api_key'], $_GET['token'])) {

	displayError('Must submit an api_key and token to proceed.');

} else {

	// Ensures the token exists and is not already bound to a user
	try {
		$result = $adodb->GetRow('SELECT * FROM Auth WHERE '
				. 'token = ' . $adodb->qstr($_GET['token']) . ' AND '
				. 'username IS NULL');
	} catch (Exception $e) {
		displayError('Database error');
	}

	if (!$result) {
		displayError('Invalid token');
	}

	$smarty->assign('api_key', $_GET['api_key']);
	$smarty->assign('token', $_GET['token']);
}

$smarty->display('api_auth.tpl');
