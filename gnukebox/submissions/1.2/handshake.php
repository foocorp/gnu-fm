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

// Implements the submissions handshake protocol as detailed at: http://www.last.fm/api/submissions

require_once('auth-utils.php');
require_once('config.php');

$supported_protocols = array("1.2", "1.2.1");


if(!isset($_GET['p']) || !isset($_GET['u']) || !isset($_GET['t']) || !isset($_GET['a']) || !isset($_GET['c'])) {
	die("BADAUTH\r\n");
}

$protocol = $_GET['p']; $username = $_GET['u']; $timestamp = $_GET['t']; $auth_token = $_GET['a']; $client = $_GET['c'];

if($client == "import") {
	die("FAILED Import scripts are broken\r\n"); // this should be removed or changed to check the version once import.php is fixed
}

if(!in_array($protocol, $supported_protocols))  {
	die("FAILED Unsupported protocol version\r\n");
}

if(abs($timestamp - time()) > 300) {
	die("BADTIME\r\n"); // let's try a 5-minute tolerance
}

if(isset($_GET['api_key']) && isset($_GET['sk'])) {
	$authed = check_web_auth($username, $auth_token, $timestamp, $_GET['api_key'], $_GET['sk']);
} else {
	$authed = check_standard_auth($username, $auth_token, $timestamp);
}

if(!$authed) {
	die("BADAUTH\r\n");
}

$session_id = md5($auth_token . time());
$sql = "INSERT INTO Scrobble_Sessions(username, sessionid, client, expires) VALUES ("
	. $adodb->qstr($username) . ","
	. $adodb->qstr($session_id) . ","
	. $adodb->qstr($client) . ","
	. (time() + 86400) . ")";

$res = $adodb->Execute($sql);

if(PEAR::isError($res)) {
	$msg = $res->getMessage();
	reportError($msg, $sql);
	die("FAILED " . $msg . "\r\n");
}

echo "OK\r\n";
echo $session_id . "\r\n";
echo $submissions_server . "/nowplaying/1.2/\r\n";
echo $submissions_server . "/submissions/1.2/\r\n";

?>
