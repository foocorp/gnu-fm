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

function displayError($error_msg) {
	global $smarty;
	$smarty->assign('error_msg', $error_msg);
	$smarty->display('api_auth.tpl');
	exit();
}

/**
 * if !api_key
 *		error
 *
 * if api_key && cb && !token
 *		web app step 1
 *
 * if api_key && token && !cb
 *		desktop app step 1
 *
 * if username && api_key && token && password
 *		web/desktop app step 2.1
 *		
 *		if cb
 *			web app step 2.2
 *				redirect to callback_url with token as param
 *				client needs to do a auth.getsession within 60 minutes
 *		else
 *			desktop app step 2.2
 *				print success message
 *				client needs to do a auth.getsession within 60 minutes
 *		
 *
 */


// We always need the api_key parameter
if (!isset($_REQUEST['api_key'])) {
	displayError('Must submit a combination of parameters api_key and cb or api_key and token to proceed.');

// Web app auth stage 1
} elseif (isset($_GET['api_key']) && isset($_GET['cb']) && !isset($_REQUEST['token'])) {
	$token = Server::getAuthToken();
	$smarty->assign('stage', 'webapp1');
	$smarty->assign('token', $token);
	$smarty->assign('cb', $_GET['cb']);
	$smarty->assign('api_key', $_GET['api_key']);

// Desktop app auth stage 1
} elseif (isset($_GET['api_key']) && isset($_GET['token']) && !isset($_GET['cb']) && !isset($_POST['username'])) {

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
} elseif (isset($_POST['username'], $_POST['api_key'], $_POST['token'])) {
	// Authenticate the user using the submitted password
	$query = 'SELECT username FROM Users WHERE lower(username) = lower(?) AND password = ?';
	$params = array($_POST['username'], md5($_POST['password']));
	try {
		$result = $adodb->GetOne($query, $params);
	} catch (Exception $e) {
		reportError($e->getMessage(), $e->getTraceAsString());
		displayError('Database error');
	}
	if (!$result) {
		displayError('Authentication failed');
	}

	// Bind the user to the token and cancel the expiration rule
	$query = 'UPDATE Auth SET username = ?, expires = 0 WHERE token = ?';
	$params = array($_POST['username'], $_POST['token']);
	try {
		$adodb->Execute($query, $params);
	} catch (Exception $e) {
		reportError($e->getMessage(), $e->getTraceAsString());
		displayError('Database error');
	}

	// Web app auth step 2.2
	if(isset($_POST['cb'])) {
		$redirect_url = $_POST['cb'];
		header('Location:' . $redirect_url . '&token=' . $_POST['token']);

	// Desktop app auth step 2.2
	} else {
		$smarty->assign('stage', 'deskapp2.2');
		$smarty->assign('username', $_POST['username']);
	}
}

$smarty->display('api_auth.tpl');
