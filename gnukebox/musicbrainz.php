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

    @apache_setenv('no-gzip', 1);
    @ini_set('zlib.output_compression', 0);
    @ini_set('implicit_flush', 1);


header('Content-type: text/html; charset=utf-8');
require_once('database.php');
require_once('utils/human-time.php');

$res = $mdb2->query("SELECT artist, track from scrobbles where Album is null LIMIT 20;");

     echo "<ul>";

			if(PEAR::isError($res)) {
				die($res->getMessage());
			}
			while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {


			echo "<li>" . $row['artist'] . "&mdash;" . $row['track'] . "</li>";

			echo "Finding album...";

			echo doABunchOfShit($row['artist'], $row['track']);

			   for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
    ob_implicit_flush(1)  ;


			}
			

function doABunchOfShit($artist, $track){

	 $album = ScrobbleLookup($artist, $track);

	 if ($album){
	 
	 return $album;

	 } else {

	 $album = BrainzLookup ($artist, $track);

	 return $album;} 
	 	 
}

function ScrobbleLookup($artist, $track){

	 	     global $mdb2;

			$sql = "SELECT album from scrobbles where artist = " . $mdb2->quote($artist) . " and track = " . $mdb2->quote($track) . " LIMIT 1;";

			$resAlbum = $mdb2->query($sql);

			if(PEAR::isError($resAlbum)) {
			     die($resAlbum->getMessage());
                        }

			$albumData = $resAlbum->fetchRow(MDB2_FETCHMODE_ASSOC);

			return $albumData['album'];

}

function BrainzLookup($artist, $track){

	 	     global $mdb2;

			$sql = "select a.name as artist,l.name as album, t.name as track,t.gid as mbid from brainz.track t left join brainz.artist a on t.artist=a.id left join brainz.albumjoin j on j.track=t.id left join brainz.album l on l.id=j.album  where lower(t.name)=" . $mdb2->quote(strtolower($track)) . " and lower(a.name)=" . $mdb2->quote(strtolower($artist)) . " LIMIT 1;";
			
			$resBrainz = $mdb2->query($sql);

			if(PEAR::isError($resBrainz)) {
			     die($resBrainz->getMessage());
                        }

			$albumData = $resBrainz->fetchRow(MDB2_FETCHMODE_ASSOC);

			return $albumData['album'];

}

		?>
		</ul>