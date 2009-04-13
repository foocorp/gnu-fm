<?php

/* Libre.fm -- a free network service for sharing your music listening habits

   Copyright (C) 2009 Libre.fm Project

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

require_once("HTTP/Request.php");

if($argc != 5) {
	die("Usage: 'php5 import.php <username> <password> <server> <scrobble dump>\n");
}

$username = $argv[1];
$password = $argv[2];
$server = $argv[3];
$data = file($argv[4]);

$timestamp = time();
$token = md5(md5($password) . $timestamp);
$response = file($server . "/?hs=true&p=1.2&u=$username&t=$timestamp&a=$token&c=import");
if(trim($response[0]) != "OK") {
	die("Couldn't login\n");
}

$session_id = trim($response[1]);
$submissions_server = trim($response[3]);

$r = new HTTP_Request($submissions_server);
$r->setMethod(HTTP_REQUEST_METHOD_POST);

for($i = 0; $i < count($data); $i++) {
	$row = explode("\t", $data[$i]);

	$track = $row[1];
	$artist = $row[0];
	$time = strtotime($row[2]);
	if(!$time) {
		$time = time();
	}

	$r->addPostData('s', $session_id);
	$r->addPostData('a['.$i.']', $artist);
	$r->addPostData('t['.$i.']', $track);
	$r->addPostData('i['.$i.']', $time);

// This is highly broken.  It should consolidate each set of 50 submissions
// into each batch, then reset $i to 0 (and $i should be decoupled from the
// current row number.
	$r->sendRequest();

// It should also do error checking.

// even though this is broken and useless, people are hammering the server with multiple
// requests per second, so throttle the uselessness a bit
	sleep(2);

	echo $i . "/" . count($data) ."   sending ". $artist . " playing " . $track .".. ";
	echo $r->getResponseBody();
}


?>
