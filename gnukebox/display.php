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
<!DOCTYPE html>
<html>
<head>
   <title>Libre.fm server version <?php echo $version; ?></title>
   <meta name="author" content="FooCorp catalogue number FOO200 and contributors" />
   <link rel="stylesheet" href="http://s.libre.fm/librefm/css/r.css" type="text/css" />
   <link rel="stylesheet" href="http://s.libre.fm/librefm/css/b.css" type="text/css" />
<meta http-equiv="refresh" content="20" />
</head>
<body>
<div id="doc2" class="yui-t6">
   <div id="hd" role="banner">
   <h1><a href="http://libre.fm/"><img src="http://s.libre.fm/librefm/img/lfm-top-black.png" alt="Libre.fm" /></a></h1></div>
   <div id="bd" role="main">
	<div id="yui-main">
	<div class="yui-b"><div class="yui-g">

       <h2>Last 10 tracks received</h2>

     <?php

	$adodb->SetFetchMode(ADODB_FETCH_ASSOC);

	try {

	$res = $adodb->CacheGetAll(60, "SELECT artist, track, time FROM Scrobbles ORDER BY time DESC LIMIT 10");

	}

	catch (exception $e)
	{
		die($e->getMessage());
	}

	echo "<ul>\n";

			foreach($res as &$row){

			echo "<li>" . $row['artist'] . "&mdash;" . $row['track'] . "</li>\n";

			} 
			?>

	</ul>
</div>
</div>
	</div>
	<div class="yui-b">

        </div>

</div>
  <div id="ft" role="contentinfo"><p>Libre.fm is powered by <a href="http://bzr.savannah.gnu.org/lh/librefm/">GNU FM</a></p></div>
</body>
</html>
