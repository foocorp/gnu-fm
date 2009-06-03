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

require_once('database.php');

function check_web_auth($username, $token, $timestamp, $api_key, $sk) {
	// Validates authentication using a web services token
	global $adodb;

	// Using the valid_api_key function from nixtape/2.0/index.php would be appropriate here
	if (strlen($api_key) != 32) {
		return false;
	}

	$adodb->SetFetchMode(ADODB_FETCH_ASSOC); // this query should get the uniqueid and then return it on success
	$result = $adodb->GetOne('SELECT username FROM Auth WHERE '
		//. 'expires > ' . time() . ' AND '   // session keys have an infinite lifetime
		. 'sk = ' . $adodb->qstr($sk)
		);
	if (!$result) {
		// TODO: Log failures somewhere
		return false;
	}

	return $result == $username;
}

function check_standard_auth($username, $token, $timestamp) {
	// Validates authentication using a standard authentication token
	global $adodb;

	$adodb->SetFetchMode(ADODB_FETCH_ASSOC); // this query should get the uniqueid and then return it on success
	$pass = $adodb->GetOne('SELECT password FROM Users WHERE username =' . $adodb->qstr($username));
	if (!$pass) {
		// TODO: Log failures somewhere
		return false;
	}

	$check_token = md5($pass . $timestamp);

	return $check_token == $token;
}

/**
 * Checks if the session is still valid. Assumes $sessionID is already quoted.
 */
function check_session($sessionID) {
	global $adodb;

	$session = $adodb->GetOne('SELECT expires from Scrobble_Sessions WHERE sessionid = ' . $sessionID);
	if (!$session) {
		return(false);
	}

	return($session >= time());
}
