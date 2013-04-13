<?php

/* GNUkebox -- a free software server for recording your listening habits

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
require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require_once($install_path . 'database.php');

/**
 * Validate authentication using a web services token.
 *
 * @param string $username User name.
 * @param string $api_key 32 character API key.
 * @param string $sk Web services token.
 * @return bool
 */
function check_web_auth($username, $api_key, $sk) {
	global $adodb;

	// Using the valid_api_key function from nixtape/2.0/index.php would be appropriate here
	if (strlen($api_key) != 32) {
		return false;
	}

	$query = 'SELECT username FROM Auth WHERE sk = ?';
	$params = array($sk);
	$result = $adodb->GetOne($query, $params);
	if (!$result) {
		// TODO: Log failures somewhere
		return false;
	}

	return $result == $username;
}

/**
 * Validates authentication using a standard authentication token.
 *
 * @param string $username User name.
 * @param string $token Token.
 * @param int $timestamp Timestamp in seconds since Epoch.
 * @return bool
 */
function check_standard_auth($username, $token, $timestamp) {
	// Validates authentication using a standard authentication token
	global $adodb;

	$query = 'SELECT password FROM Users WHERE lower(username) = lower(?)';
	$params = array($username);
	$pass = $adodb->GetOne($query, $params);
	if (!$pass) {
		// TODO: Log failures somewhere
		return false;
	}

	$check_token = md5($pass . $timestamp);

	return $check_token == $token;
}

/**
 * Checks if the session is still valid.
 *
 * @param $sessionid Scrobble session id.
 * @return bool True if session exists and is still valid.
 */
function check_session($sessionid) {
	global $adodb;

	$query = 'SELECT expires FROM Scrobble_Sessions WHERE sessionid = ? AND expires >= ?';
	$params = array($sessionid, time());
	$session = $adodb->GetOne($query, $params);

	return (bool) $session;
}
