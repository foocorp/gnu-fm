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

function check_web_auth($username, $token, $timestamp, $api_key, $sk) {
	// Validates authentication using a web services token
	global $mdb2;

	// Using the valid_api_key function from nixtape/2.0/index.php would be appropriate here
	if (strlen($api_key) != 32) {
		return false;
	}

	$result = $mdb2->query('SELECT username FROM Auth WHERE '
		//. 'expires > ' . time() . ' AND '   // session keys have an infinite lifetime
		. 'sk = ' . $mdb2->quote($sk, 'text')
		);
	if (PEAR::isError($result) || !$result->numRows()) {
		// TODO: Log failures somewhere
		return false;
	}

	return $result->fetchOne(0) == $username;
}

function check_standard_auth($username, $token, $timestamp) {
	// Validates authentication using a standard authentication token
	global $mdb2;

	$result = $mdb2->query("SELECT password FROM Users WHERE username =" . $mdb2->quote($username, 'text'));
	if (PEAR::isError($result) || !$result->numRows()) {
		// TODO: Log failures somewhere
		return false;
	}

	$pass = $result->fetchOne(0);
	$check_token = md5($pass . $timestamp);

	return $check_token == $token;
}
