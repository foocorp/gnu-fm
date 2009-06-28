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

header('Content-type: text/html; charset=utf-8');
require_once('database.php');
require_once('utils/human-time.php');
require_once('temp-utils.php');

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
 "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
   <title>GNUkebox version <?php echo $version; ?></title>
   <meta name="author" content="FooCorp catalogue number FOO200 and contributors" />
</head>
<body>

<h1>GNUkebox <a href="stats.php">statistics</a></h1>

       <?php include('info.html'); ?>

       <h2>Last 100 tracks received</h2>

     <?php

	$adodb->SetFetchMode(ADODB_FETCH_ASSOC);

	try {

	$res = $adodb->CacheGetAll(60, "SELECT userid, artist, track, time FROM Scrobbles ORDER BY time DESC LIMIT 100");

	}

	catch (exception $e)
	{
		die($e->getMessage());
	}

	echo "<ul>\n";

			foreach($res as &$row){

			echo "<li>" . $row['userid'] . " listened to
			" . $row['artist'] . "&mdash;" . $row['track']
			. " (" . human_timestamp($row['time']) . ")</li>\n";

			} 
			?>

	</ul>

	<hr>

	<address>Powered by <a href="http://gnukebox.org">GNUkebox</a> &mdash; <a href="http://bzr.savannah.gnu.org/lh/librefm/">Source code</a></address>

</body>
</html>
