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

<?php
header('Content-type: text/html; charset=utf-8');
require_once('database.php');
require_once('utils/human-time.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
 "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
		<title>Libre.fm Gobble Server</title>
   <link rel="stylesheet" href="reset-fonts-grids.css" type="text/css">
   <link rel="stylesheet" href="base.css" type="text/css">
   <link rel="stylesheet" href="librefm.css" type="text/css">
<!--
<rdf:RDF xmlns="http://web.resource.org/cc/"
         xmlns:dc="http://purl.org/dc/elements/1.1/"
         xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
<license rdf:resource="http://creativecommons.org/licenses/by-sa/3.0/us/" />
</Work>
<License rdf:about="http://creativecommons.org/licenses/by-sa/3.0/us/">
</License>
</rdf:RDF>
-->
</head>
<body>
<div id="doc3" class="yui-t7">
   <div id="hd" style="banner"><h1><a href="http://libre.fm/">Libre.fm Gobble Server</a></h1></div>
   <div id="bd" style="main">
	<div id="yui-main">
	<div class="yui-b"><div class="yui-g">

  <p>This is a demo site for the libre.fm 'gobbler' server. That name might change. If you'd like an account, come to our IRC channel and ask mattl, Elleo or Clint nicely. You'll need to supply us with a username, password and email address.</p>

<p>You'll also need to be comfortable editing your 'hosts' file and understand the implications of doing that.</p>

		<?php

  $req_user = $_GET["u"];
$req_artist = $_GET["a"];
$req_track = $_GET["t"];
  

if ($req_user) {

			$res = $mdb2->query("SELECT username, artist, track, time FROM Scrobbles WHERE username = '" . $req_user . "' ORDER BY time DESC LIMIT 100");

			echo "<h2>last 100 Gobbles by " . $req_user . "</h2>";

} elseif ($req_artist) {

			$res = $mdb2->query("SELECT username, artist, track, time FROM Scrobbles WHERE artist = '" . $req_artist ."' ORDER BY time DESC LIMIT 100");

			echo "<h2>last 100 Gobbles of " . $req_artist . "</h2>";

} elseif ($req_track) {

			$res = $mdb2->query("SELECT username, artist, track, time FROM Scrobbles WHERE track = '" . $req_track . "' ORDER BY time DESC LIMIT 100");

			echo "<h2>last 100 Gobbles of " . $req_track . "</h2>";
			
} elseif (!$res) {

			$res = $mdb2->query("SELECT username, artist, track, time FROM Scrobbles ORDER BY time DESC LIMIT 10");

			echo "<h2>last 10 Gobbles</h2>";

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
					$value = "<a href=\"/?u=" . $value . "\">$value</a>";}
					if($field == "time") {
						$value = '<abbr title=\'' . strftime('%c', $value) . '\'>' . human_timestamp($value) . '</abbr>';
					}
					echo "<td>". stripslashes($value) . "</td>";
				}
				echo "</tr>\n";
			}

		?>
		</table>
		
		<h2>Now Playing?</h2>

		<?php
			$res = $mdb2->query("SELECT username, artist, track FROM Now_Playing ORDER BY expires DESC LIMIT 10");
			if(PEAR::isError($res)) {
				die($res->getMessage());
			}
			while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
				echo "<p>" . stripslashes($row["username"]) . " is listening to " . stripslashes($row["track"]) . " by " . stripslashes($row["artist"]) . "</p>";
			}
		?>

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
 <p>
    <a href="http://validator.w3.org/check?uri=referer"><img
        src="http://www.w3.org/Icons/valid-html401"
        alt="Valid HTML 4.01 Strict" height="31" width="88"></a>
  </p>


</div>
</div>
	</div>
	
	</div>
   <div id="ft" style="contentinfo">

<p>This site handles <em>gobble</em> and <em>now playing</em>
submissions from client applications and offers access to our web
services API. If you just want to use <a
href="http://libre.fm">libre.fm</a> then you probably want to
visit the <a href="http://libre.fm">main site</a>, but if you're
a developer then here are some useful stats to help you see
what's happening.</p>

<p><a href="http://svn.savannah.gnu.org/viewvc/trunk/gobbler/?root=librefm">http://svn.savannah.gnu.org/viewvc/trunk/gobbler/?root=librefm</a></p></div>
</div>
</body>
</html>
