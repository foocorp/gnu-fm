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

require_once('database.php');

function usernameFromSID($session_id) {
	global $mdb2;

	// Delete any expired session ids
	$mdb2->query("DELETE FROM Scrobble_Sessions WHERE expires < " . time());

	$res = $mdb2->query("SELECT username FROM Scrobble_Sessions WHERE sessionid = " . $mdb2->quote($session_id, "text"));
	if(PEAR::isError($res)) {
		die("FAILED " . $res->getMessage());
	}

	if(!$res->numRows()) {
		die("BADSESSION");
	}

	return $res->fetchOne(0);
}

function createArtistIfNew($artist) {
	global $mdb2;

	$artist = NoSpamTracks($artist);

	$res = $mdb2->query("SELECT name FROM Artist WHERE name = " . ($artist));
	if(PEAR::isError($res)) {
		die("FAILED " . $res->getMessage());
	}

	if(!$res->numRows()) {
		// Artist doesn't exist, so we create them
		$res = $mdb2->query("INSERT INTO Artist (name) VALUES (" . ($artist) . ")");
		if(PEAR::isError($res)) {
			die("FAILED " . $res->getMessage());
		}
	}
}

function createAlbumIfNew($artist, $album) {
	global $mdb2;

	$res = $mdb2->query("SELECT name FROM Album WHERE name = " . ($album));
	if(PEAR::isError($res)) {
		die("FAILED " . $res->getMessage());
	}

	if(!$res->numRows()) {
		// Album doesn't exist, so create it
	        
	  $art = $mdb2->quote(getAlbumArt($artist, $album));
	  if ($art !="") {
	    $license = $mdb2->quote("amazon");

	    $res = $mdb2->query("INSERT INTO Album (name, artist_name, image, artwork_license) VALUES (" . ($album) . ", " . ($artist) . ", " . ($art) . ", " . ($license) .")");

	  } else {

		$res = $mdb2->query("INSERT INTO Album (name, artist_name) VALUES (" . ($album) . ", " . ($artist) . ")");

	  }

		if(PEAR::isError($res)) {
			die("FAILED " . $res->getMessage());
		}
	}
}

function createTrackIfNew($artist, $album, $track, $mbid) {
	global $mdb2;

	$track = NoSpamTracks($track);
	$artist = NoSpamTracks($artist);

	$res = $mdb2->query("SELECT name FROM Track WHERE lower(name) = " . (strtolower($track)) . " AND lower(artist) = " . (strtolower($artist)));
	if(PEAR::isError($res)) {
		die("FAILED " . $res->getMessage());
	}

	if(!$res->numRows()) {
		// Create new track
		$res = $mdb2->query("INSERT INTO Track (name, artist, album, mbid) VALUES ("
			. ($track) . ", "
			. ($artist) . ", "
			. ($album) . ", "
			. ($mbid) . ")");
		if(PEAR::isError($res)) {
			die("FAILED " . $res->getMessage());
		}
	}
}

function scrobbleExists($username, $artist, $track, $time) {
	global $mdb2;

	$res = $mdb2->query("SELECT time FROM Scrobbles WHERE username = " . ($username) . " AND artist = " . ($artist) . " AND track = " . ($track) . " AND time = " . ($time));
	if(PEAR::isError($res)) {
		die("FAILED " . $res->getMessage());
	}

	if(!$res->numRows()) {
		return false;
	} else {
		return true;
	}
}
function NoSpamTracks ($track) {

  // This function exists to remove things like '(PREVIEW: buy it at www.magnatune.com)' from track names.

  $track = str_replace("(PREVIEW: buy it at www.magnatune.com)", "", $track);

  return $track;
  
}

function getAlbumArt($artist, $album) {

  $Access_Key_ID = "1EST86JB355JBS3DFE82"; // this is mattl's personal key :)

        $SearchIndex='Music';
$Keywords=urlencode($artist.' '.$album);
        $Operation = "ItemSearch";
$Version = "2007-07-16";
        $ResponseGroup = "ItemAttributes,Images";
$request=
        "http://ecs.amazonaws.com/onca/xml"
                . "?Service=AWSECommerceService"
. "&AssociateTag=" . $Associate_tag
. "&AWSAccessKeyId=" . $Access_Key_ID
. "&Operation=" . $Operation
. "&Version=" . $Version
. "&SearchIndex=" . $SearchIndex
. "&Keywords=" . $Keywords
. "&ResponseGroup=" . $ResponseGroup;

$aws_xml = simplexml_load_file($request) or die("xml response not loading");

$image = $aws_xml->Items->Item->MediumImage->URL;
        $URI = $aws_xml->Items->Item->DetailPageURL;
        return $image;
}

?>
