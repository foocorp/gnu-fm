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

require_once('../database.php');
require_once('radio-utils.php');

if (!isset($_GET['session']) || !isset($_GET['url'])) {
	die("FAILED\n");
}

$session = $_GET['session'];
$url = $_GET['url'];

$res = $adodb->GetOne('SELECT username FROM Radio_Sessions WHERE session = ' . $adodb->qstr($session));

if (!$res) {
	die("BADSESSION\n");
}

if (preg_match('@^(globaltags|artist|user)/[^:]+$@', $url, $regs)) {
	$url = 'librefm://' . $url; // compensate for shell-fm sending incomplete urls
}

$stationname = radio_title_from_url($url);
if ($stationname == 'FAILED') {
	die("FAILED Unavailable station\n");
}

$adodb->Execute('UPDATE Radio_Sessions SET url = ' . $adodb->qstr($url) . ' WHERE session = ' . $adodb->qstr($session));

echo "response=OK\n";
echo "url=http://libre.fm\n"; // Need to parse the station request and give a real URL
echo 'stationname=' . $stationname ."\n";
