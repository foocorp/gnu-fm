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

require_once('config.php');

$session = curl_init($submissions_url);

$post_vars = '';
foreach($_POST as $key => $element) {
	if (is_array($element)) {
		$i = 0;
		foreach($element as $e) {
			$post_vars .= $key . "[" . $i . "] = " . $e . "&";
			$i++;
		}
	} else {
		$post_vars .= $key.'='.$element.'&';
	}
}
curl_setopt ($session, CURLOPT_POST, true);
curl_setopt ($session, CURLOPT_POSTFIELDS, $post_vars);

$response = curl_exec($session);
echo $response;

curl_close($session);
?>
