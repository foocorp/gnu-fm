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

/** 
 * Implements the submissions handshake protocol 1.1 as detailed at: http://www.audioscrobbler.net/wiki/Protocol1.1.merged
 *
 * By sending the timestamp as the md5 challenge then creating the session key from md5(md5($password) . $timestamp) we can
 * force a 1.1 client to give us a session key that can be used by the 1.2 protocol handler, so we only handle handshakes for
 * 1.1 then pass all submissions off to the 1.2 handler.
 */
require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');
require_once($install_path . '1.x/auth-utils.php');
require_once($install_path . 'temp-utils.php');

header('Content-Type: text/plain');

$supported_protocols = array('1.1');

if (!isset($_REQUEST['p']) || !isset($_REQUEST['u']) || !isset($_REQUEST['c'])) {
	die("FAILED\n");
}

$protocol = $_REQUEST['p'];
$username = $_REQUEST['u'];
$client = $_REQUEST['c'];

if (!in_array($protocol, $supported_protocols)) {
	die("FAILED Unsupported protocol version\n");
}

$timestamp = time();

$select_query = 'SELECT uniqueid, password FROM Users WHERE lower(username) = lower(?)';
$select_params = array($username);
try {
	list($userid, $password) = $adodb->GetRow($select_query, $select_params);
} catch (Exception $e) {
	die('FAILED ' . $e->getMessage() . "\n");
}
if (!$password) {
	die("BADUSER\n");
}
$sessionid = md5($password . $timestamp);
$insert_query = 'INSERT INTO Scrobble_Sessions(userid, sessionid, client, expires) VALUES (?,?,?,?)';
$insert_params = array($userid, $sessionid, $client, time() + 86400);
try {
	$res = $adodb->Execute($insert_query, $insert_params);
} catch (Exception $e) {
	die('FAILED ' . $e->getMessage() . "\n");
}

echo "UPTODATE\n";
echo $timestamp . "\n";
echo $base_url . "/1.x/submissions/1.2/\n";
echo "INTERVAL 1\n";
