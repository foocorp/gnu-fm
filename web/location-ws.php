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

# This file should probably be moved out of the web root. /js/edit_profile.js will
# need updating if you do.

// The place being searched for.
$query = $_GET['q'];

if (!$query)
{
	header("Content-Type: text/plain");
	die("Must supply a query argument.\r\n");
}

// The number of results requested
$num = $_GET['n'];
if (! $num)
	$num = 10;

// When translated versions become available, should be able to calculate
// the language code from the subdomain.
$lang = 'en';

$uri =  sprintf('http://ws.geonames.org/searchJSON?q=%s&maxRows=%d&lang=%s&style=full',
	urlencode($query),
	$num,
	urlencode($lang));

# We'll try to use cURL if the extension is installed on this server.
if (function_exists('curl_init'))
{
	header("Content-Type: application/json");
	$ch = curl_init($uri);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_USERAGENT, 'libre.fm');
	$output = curl_exec($ch);
	curl_close($ch);
	echo $output;
}

# Otherwise, we'll fall back to direct socket calls. Ugly.
elseif (function_exists('parse_url'))
{
	$_uri = parse_url($uri);
	if (! $_uri['port'])
		$_uri['port'] = 80;
		
	if (! ($nh = fsockopen($_uri['host'], $_uri['port'], $errno, $errstr, 20)) )
	{
		header("Content-Type: text/plain");
		die("Could not open network connection! ($errno - $errstr)\r\n");
	}
	
	fwrite($nh, "GET {$_uri[path]}?{$_uri[query]} HTTP/1.0\r\n"
		. "Host: {$_uri['host']}\r\n"
		. "User-Agent: libre.fm\r\n"
		. "Connection: close\r\n\r\n"
		);
	header("Content-Type: application/json");
	while (!feof($nh))
	{
		$output .= fgets($nh, 128);
	}
	fclose($nh);
	
	// Remove HTTP header.
	echo substr(strstr($output, "\r\n\r\n"), 4);
}

