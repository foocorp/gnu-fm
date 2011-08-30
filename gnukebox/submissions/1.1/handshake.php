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

// Implements the submissions handshake protocol 1.1 as detailed at: http://www.audioscrobbler.net/wiki/Protocol1.1.merged
//
// By sending the timestamp as the md5 challenge then creating the session key from md5(md5($password) . $timestamp) we can
// force a 1.1 client to give us a session key that can be used by the 1.2 protocol handler, so we only handle handshakes for
// 1.1 then pass all submissions off to the 1.2 handler.

require_once('auth-utils.php');
require_once('config.php');
require_once('temp-utils.php');

$supported_protocols = array('1.1');

if (!isset($_GET['p']) || !isset($_GET['u']) || !isset($_GET['c'])) {
	die("FAILED\n");
}

$protocol = $_GET['p']; $username = $_GET['u']; $client = $_GET['c'];

if (!in_array($protocol, $supported_protocols)) {
	die("FAILED Unsupported protocol version\n");
}

$timestamp = time();

$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
try {
	$row = $adodb->GetRow('SELECT uniqueid,password FROM Users WHERE lower(username) = lower(' . $adodb->qstr($username) . ')');
} catch (Exception $e) {
	die('FAILED ' . $e->getMessage() . "\n");
}
if (!$row) {
	die("BADUSER\n");
}
$password = $row['password'];
$uniqueid = $row['uniqueid'];
$session_id = md5($password . $timestamp);
try {
$res = $adodb->Execute('INSERT INTO Scrobble_Sessions(userid, sessionid, client, expires) VALUES ('
	. ($uniqueid) . ','
	. $adodb->qstr($session_id, 'text') . ','
	. $adodb->qstr($client, 'text') . ','
	. $adodb->qstr(time() + 86400) . ')');
} catch (Exception $e) {
	die('FAILED ' . $e->getMessage() . "\n");
}

echo "UPTODATE\n";
echo $timestamp . "\n";
echo $submissions_server . "/submissions/1.2/\n";
echo "INTERVAL 1\n";
