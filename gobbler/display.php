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

header('Content-type: text/html; charset=utf-8');
require_once('database.php');
require_once('utils/human-time.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
 "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
   <title>Libre.fm &mdash; discover new music</title>
   <link rel="stylesheet" href="/reset-fonts-grids.css" type="text/css">
   <link rel="stylesheet" href="/base.css" type="text/css">
   <link rel="stylesheet" href="/librefm.css" type="text/css">
</head>
<body>
<div id="doc2" class="yui-t7">
   <div id="hd" role="navigation"><h1><a href="/">Header</a></h1>

     <ul>
       <li><a href="https://savannah.nongnu.org/svn/?group=librefm">Code</a></li>
       <li><a href="http://blog.libre.fm/">Blog</a></li>
       <li><a href="http://ideas.libre.fm/">Wiki</a></li>
       <li><a href="http://lists.autonomo.us/mailman/listinfo/libre-fm">List</a></li>
       <li><a href="https://savannah.nongnu.org/bugs/?group=librefm">Bugs</a></li>
       <li id="login"><a href="http://alpha.libre.fm/login.php">Log in</a></li>
     </ul>
     
   </div>
   <div id="bd" role="main">
   <div id="coolio">
	<div class="yui-g" id="banner">
	  <a href="http://alpha.libre.fm/"><img src="http://libre.fm/i/topblock.png" alt="" /></a>
	</div>
<div class="yui-gc">
    <div class="yui-u first" id="content">
    <div style="padding: 10px;">
		<?php

  $req_user = $_GET["u"];
$req_artist = $_GET["a"];
$req_track = $_GET["t"];
  

if ($req_user) {

			$res = $mdb2->query("SELECT username, artist, track, time FROM Scrobbles WHERE username = '" . $req_user . "' ORDER BY time DESC LIMIT 100");

			echo "<h2>" . $req_user . "'s most recent listening data</h2>";

} elseif ($req_artist) {

			$res = $mdb2->query("SELECT username, artist, track, time FROM Scrobbles WHERE artist = '" . $req_artist ."' ORDER BY time DESC LIMIT 100");

			echo "<h2>Last 100 Tracks by " . $req_artist . "</h2>";

} elseif ($req_track) {

			$res = $mdb2->query("SELECT username, artist, track, time FROM Scrobbles WHERE track = '" . $req_track . "' ORDER BY time DESC LIMIT 100");

			echo "<h2>Last 100 plays of " . $req_track . "</h2>";
			
} elseif (!$res) {

			$res = $mdb2->query("SELECT username, artist, track, time FROM Scrobbles ORDER BY time DESC LIMIT 25");

			echo "<h2>Last 25 tracks received</h2>";

}

?>

		<table>
			<tr><th>User</th><th>Artist</th><th>Track</th><th>Time</th></tr>
			
<?php

			if(PEAR::isError($res)) {
				die($res->getMessage());
			}
			$i = 0;
			while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
			$i++;
				echo ($i % 2 == 0) ? "<tr class=\"even\">" : "<tr class=\"odd\">";
				foreach($row as $field => $value) {
					if($field == "username"){
					$value = "<a href=\"/user/" . $value . "\">$value</a>";}
					if($field == "time") {
						$value = '<abbr title=\'' . strftime('%c', $value) . '\'>' . human_timestamp($value) . '</abbr>';
					}
					echo "<td>". strip_tags(stripslashes($value)) . "</td>";
				}
				echo "</tr>\n";
			}

		?>
		</table>
		
    </div></div>
    <div class="yui-u" id="sidebar">
    <div style="padding: 10px;">

		<h2>Statistics</h2>

		<?php
			$res = $mdb2->query("SELECT COUNT(*) as total from Scrobbles");
			if(PEAR::isError($res)) {
				die($res->getMessage());
			}
			$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			echo "<p>" . stripslashes($row["total"]) . " gobbles.</p>";

			$res = $mdb2->query("SELECT COUNT(*) as total from Track");
			if(PEAR::isError($res)) {
				die($res->getMessage());
			}
			$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			echo "<p>" . stripslashes($row["total"]) . " tracks.</p>";

			$res = $mdb2->query("SELECT COUNT(*) as total from Users");
			if(PEAR::isError($res)) {
				die($res->getMessage());
			}
			$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
			echo "<p>" . stripslashes($row["total"]) . " users.</p>";

		?>

		<h2>Now Playing?</h2>

		<?php
			$res = $mdb2->query("SELECT username, artist, track, client, ClientCodes.name, ClientCodes.url from Now_Playing LEFT OUTER JOIN Scrobble_Sessions ON Now_Playing.sessionid=Scrobble_Sessions.sessionid LEFT OUTER JOIN ClientCodes ON Scrobble_Sessions.client=ClientCodes.code ORDER BY Now_Playing.expires DESC LIMIT 10");
			if(PEAR::isError($res)) {
				die($res->getMessage());
			}
			while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
				if($row["name"] == "") {
				  $client = strip_tags(stripslashes($row["client"])) . "(unknown, please tell us what this is)";
				} else {
				  $client = "<a href=\"" . strip_tags(stripslashes($row["url"])) . "\">" . strip_tags(stripslashes($row["name"])) . "</a>";
				}
				echo "<p>" . strip_tags(stripslashes($row["username"])) . " is listening to " . strip_tags(stripslashes($row["track"])) . " by " . strip_tags(stripslashes($row["artist"])) . " with " . $client . "</p>";
			}
		?>


      
    </div></div>
</div></div>
<div class="yui-g" id="artists">

  <strong><a href="http://libre.fm/contact/">Talk to us</a></strong> if you're in a band, represent a
  label or music service, we'd like to talk ideas and
  possibilities. While our intention is eventually provide download
  and streaming services for freely-licensed music, we are also
  interested in linking all bands to respectable DRM-free music
  services.
  
</div>
<div class="yui-g">
    <div class="yui-u first" id="links">
This site handles <em>gobble</em> and <em>now playing</em>
submissions from client applications and offers access to our web
services API. If you just want to use <a
href="http://libre.fm">libre.fm</a> then you probably want to
visit the <a href="http://libre.fm">main site</a>, but if you're
a developer then here are some useful stats to help you see
what's happening.
</div>
    <div class="yui-u" id="moarlinks">
<a href="http://www.gnu.org/licenses/agpl.html">GNU Affero General Public License v3 or later</a>. Source: <a href="http://svn.savannah.gnu.org/viewvc/trunk/gobbler/?root=librefm">http://svn.savannah.gnu.org/viewvc/trunk/gobbler/?root=librefm</a>
</div>
</div>

	</div>
   <div id="ft" role="navigation">

     <ul>
       <li class="copy">&copy; 2009 Libre.fm Project</li>
       <li><a href="http://libre.fm/contributors/">Contributors</a></li>
       <li><a href="http://libre.fm/licensing/">Licensing information</a></li>
       <li><a href="http://libre.fm/developer/">Developers</a></li>
       <li><a href="http://libre.fm/api/">API</a></li>
       <li><a href="http://libre.fm/download/">Download</a></li>
     </ul>

     <p><img src="http://libre.fm/i/cc-by-sa.png" alt="Attribution-ShareAlike 3.0" /></p>

<p><a href="/data">Data dumps are here</a</p>

   </div>
</div>
</body>
</html>

