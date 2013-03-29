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
require_once('../../config.php');
require_once($install_path . '/database.php');
require_once($install_path . '/templating.php');
require_once($install_path . '/data/Server.php');
require_once($install_path . '/data/clientcodes.php');

function displayError($error_msg) {
	global $smarty;
	$smarty->assign('error_msg', $error_msg);
	$smarty->display('api_auth.tpl');
	exit();
}

$smarty->assign('site_name', $site_name);
if ($logged_in) {
	$smarty->assign('username', $this_user->name);
}

// We always need the api_key parameter and parameter cb or token
if (!isset($_REQUEST['api_key']) || !(isset($_REQUEST['cb']) || isset($_REQUEST['token']))) {
	displayError('Must submit a combination of parameters api_key and cb or api_key and token to proceed.');

// Web app auth stage 1
} elseif (isset($_GET['api_key']) && isset($_GET['cb']) && !isset($_REQUEST['token'])) {
	$token = Server::getAuthToken();
	$smarty->assign('stage', 'webapp1');
	$smarty->assign('token', $token);
	$smarty->assign('cb', $_GET['cb']);
	$smarty->assign('api_key', $_GET['api_key']);

// Desktop app auth stage 1
} elseif (isset($_GET['api_key']) && isset($_GET['token']) && !isset($_GET['cb']) && !isset($_POST['token'])) {

	// Ensures the token exists and is not already bound to a user
	$query = 'SELECT * FROM Auth WHERE token = ? AND username IS NULL';
	$params = array($_GET['token']);
	try {
		$result = $adodb->GetRow($query, $params);
	} catch (Exception $e) {
		reportError($e->getMessage(), $e->getTraceAsString());
		displayError('Database error');
	}

	if (!$result) {
		displayError('Invalid token');
	}
	$smarty->assign('stage', 'deskapp1');
	$smarty->assign('api_key', $_GET['api_key']);
	$smarty->assign('token', $_GET['token']);

// Web/Desktop app auth stage 2.1
} elseif (isset($_POST['api_key'], $_POST['token'])) {
	if(!$logged_in) {
		// Authenticate the user using the submitted password
		$query = 'SELECT username FROM Users WHERE lower(username) = lower(?) AND password = ?';
		$params = array($_POST['username'], md5($_POST['password']));
		try {
			$username = $adodb->GetOne($query, $params);
		} catch (Exception $e) {
			reportError($e->getMessage(), $e->getTraceAsString());
			displayError('Database error');
		}
		if (!$username) {
			displayError('Authentication failed');
		}
	}

	// Bind the user to the token and cancel the expiration rule
	$query = 'UPDATE Auth SET username = ?, expires = 0 WHERE token = ?';
	$params = array($username, $_POST['token']);
	try {
		$adodb->Execute($query, $params);
	} catch (Exception $e) {
		reportError($e->getMessage(), $e->getTraceAsString());
		displayError('Database error');
	}

	// Web app auth step 2.2
	if(isset($_POST['cb'])) {
		$callback_url = $_POST['cb'];
		if (preg_match("/\?/", $callback_url)) {
			$redirect_url = $callback_url . '&token=' . $_POST['token'];
		} else {
			$redirect_url = $callback_url . '?token=' . $_POST['token'];
		}

		header('Location:' . $redirect_url);

	// Desktop app auth step 2.2
	} else {
		$smarty->assign('stage', 'deskapp2.2');
		$smarty->assign('username', $username);
	}
}

$client = getClientData(null, $_REQUEST['api_key']);
$smarty->assign('clientname', $client['name']);
$smarty->assign('clienturl', $client['url']);
$smarty->display('api_auth.tpl');
