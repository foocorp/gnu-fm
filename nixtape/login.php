<?php

/* Libre.fm -- a free network service for sharing your music listening habits

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
require_once($install_path . '/data/User.php');

if(isset($_COOKIE['session_id']) && $_GET['action'] == 'logout') {
	setcookie('session_id', '', time() - 3600);
	header('Location: index.php');
}

if(isset($_POST['login'])) {

	$errors = '';
	$username = $_POST['username'];
	$password = $_POST['password'];
	$remember = $_POST['remember'];

	if(empty($username)) {
		$errors .= 'You must enter a username.<br />';
	}
	if(empty($password)) {
		$errors .= 'You must enter a password.<br />';
	}

	if(empty($errors)) {
		$res = $mdb2->query('SELECT username FROM Users WHERE '
			. ' lower(username) = ' . $mdb2->quote(strtolower($username), 'text')
			. ' AND password = ' . $mdb2->quote(md5($password), 'text') . ' AND active = 1');
		if(PEAR::isError($res)) {
			$errors .= 'A database error happened.';
		} elseif(!$res->numRows()) {
			$errors .= 'Invalid username or password.';
			$smarty->assign('invalid', true);
		} else {
			// Give the user a session id, like any other client
			$session_id = md5(md5($password) . time());
			if(isset($remember)){
				$session_time = time() + 31536000; // 1 year
			} else {
				$session_time = time() + 86400; // 1 day
			}
			$mdb2->query('INSERT INTO Scrobble_Sessions (username, sessionid, expires) VALUES ('
				. $mdb2->quote($username, 'text') . ', '
				. $mdb2->quote($session_id, 'text') . ', '
				. $mdb2->quote($session_time, 'integer') . ')');

			setcookie('session_id', $session_id, $session_time);
			$logged_in = true;
		}
	}
}

if(isset($logged_in) && $logged_in) {
	// Check that return URI is on this server. Prevents possible phishing uses.
	if ( substr($_POST['return'], 0, 1) == '/' ) {
		header(sprintf('Location: http://%s%s', $_SERVER['SERVER_NAME'], $_POST['return']));
	} else {
		header('Location: ' . $base_url); 
	}

} else {
	if ( substr($_REQUEST['return'], 0, 1) == '/' ) {
		$smarty->assign('return', $_REQUEST['return']);
	} else {
		$smarty->assign('return', ''); 
	}

	$smarty->assign('username', $username);
	$smarty->assign('errors', $errors);
	$smarty->display('login.tpl');
}
