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

require_once('config.php');

if ($_GET['method'] == 'scrobble') {
	$url = $submissions_server . '/submissions/1.2/';
} else if ($_GET['method'] == 'nowplaying') {
	$url = $submissions_server . '/nowplaying/1.2/';
} else {
	die("Invalid proxy method\n");
}

$session = curl_init($url);

$post_vars = '';
foreach ($_POST as $key => $element) {
	if (is_array($element)) {
		$i = 0;
		foreach ($element as $e) {
			$post_vars .= $key . '[' . $i . ']=' . $e . '&';
			$i++;
		}
	} else {
		$post_vars .= $key . '=' . $element . '&';
	}
}
curl_setopt($session, CURLOPT_POST, true);
curl_setopt($session, CURLOPT_POSTFIELDS, $post_vars);

$response = curl_exec($session);
echo $response;

curl_close($session);
