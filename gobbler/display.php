<?php
require_once('database.php');
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
 "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

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
		<title>Libre.fm Scrobble Server</title>
	</head>

	<body>
		<h1>Libre.fm Scrobble Server</h1>
		
		<p>This site handles <em>scrobble</em> and <em>now playing</em> submissions from client applications and offers access to our web services API. If you just want to use <a href="http://libre.fm">libre.fm</a> then you probably want to visit the <a href="http://libre.fm">main site</a>, but if you're a developer then here are some useful stats to help you see what's happening.</p>

		<h2>Last 10 Scrobbles</h2>

		<table>
			<tr><th>User</th><th>Artist</th><th>Track</th><th>Time</th></tr>
		<?php
			$res = $mdb2->query("SELECT username, artist, track, time FROM Scrobbles ORDER BY time DESC LIMIT 10");
			if(PEAR::isError($res)) {
				die($res->getMessage());
			}
			while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
				echo "<tr>";
				foreach($row as $field => $value) {
					if($field == "time") {
						$value = strftime("%c", $value);
					}
					echo "<td>". stripslashes($value) . "</td>";
				}
				echo "</tr>";
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

 <p>
    <a href="http://validator.w3.org/check?uri=referer"><img
        src="http://www.w3.org/Icons/valid-html401"
        alt="Valid HTML 4.01 Strict" height="31" width="88"></a>
  </p>


	</body>
</html>	
