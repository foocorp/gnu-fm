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

// Implements the submissions handshake protocol as detailed at: http://www.last.fm/api/submissions

require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require_once($install_path . 'data/Server.php');
require_once($install_path . '1.x/auth-utils.php');
require_once($install_path . 'temp-utils.php');

header('Content-Type: text/plain');

$supported_protocols = array('1.2', '1.2.1');

if (!isset($_REQUEST['p']) || !isset($_REQUEST['u']) || !isset($_REQUEST['t']) || !isset($_REQUEST['a']) || !isset($_REQUEST['c'])) {
	die("BADAUTH\n");
}

$protocol = $_REQUEST['p'];
$username = $_REQUEST['u'];
$timestamp = $_REQUEST['t'];
$auth_token = $_REQUEST['a'];
$client = $_REQUEST['c'];

if (!in_array($protocol, $supported_protocols)) {
	die("FAILED Unsupported protocol version\n");
}

if (abs($timestamp - time()) > 300) {
	die("BADTIME\n"); // let's try a 5-minute tolerance
}

if (isset($_REQUEST['api_key']) && isset($_REQUEST['sk'])) {
	$authed = check_web_auth($username, $_REQUEST['api_key'], $_REQUEST['sk']);
} else {
	$authed = check_standard_auth($username, $auth_token, $timestamp);
}

if (!$authed) {
	die("BADAUTH\n");
}

$userid = username_to_uniqueid($username);
$session_id = Server::getScrobbleSession($userid, $client);

if ($session_id) {
	echo "OK\n";
	echo $session_id . "\n";
	echo $base_url . "/1.x/nowplaying/1.2/\n";
	echo $base_url . "/1.x/submissions/1.2/\n";
} else {
	echo "FAILED\n";
}
