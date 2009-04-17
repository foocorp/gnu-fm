<?
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

require_once('../../database.php');
require_once('../../scrobble-utils.php');

if(!isset($_GET['c'])) {
	die("Failed Required GET parameters are not set\n");
}

$count = $_GET['c'];
$time = time();

$rowvalues = "";

for($i = 0; $i < $count; $i++) {
	// Scrobble!
	$rowvalues .= "("
		. $mdb2->quote("testuser", "text") . ", "
		. $mdb2->quote("Metallica", "text") . ", "
		. $mdb2->quote("Ride the Lightning", "text") . ", "
		. $mdb2->quote("Ride the Lightning", "text") . ", "
		. $mdb2->quote($time + $i, "integer") . ", "
		. $mdb2->quote("19bc7363-eb0a-450d-ab1d-1f3082072e0a", "text") . ", "
		. $mdb2->quote("P", "text") . ", "
		. $mdb2->quote("P", "text") . ", "
		. $mdb2->quote(393, integer) . ")";

	if($i+1 == $count) {

	// Scrobble!
		$sql = "INSERT INTO Scrobbles (username, artist, album, track, time, mbid, source, rating, length) VALUES" . $rowvalues;
		echo "$sql\n";
		$res =& $mdb2->query($sql);
		if(PEAR::isError($res)) {
		    $msg = $res->getMessage();
		    reportError($msg, $sql);
                die("FAILED " . $msg . "\nError has been reported to site administrators.");
        	}

	$rowvalues = "";

	} else {
	$rowvalues .= ",";
	}


}

die("OK\n");

?>
