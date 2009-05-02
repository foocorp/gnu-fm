<?php

/* Libre.fm -- a free network service for sharing your music listening habits

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

require_once('database.php');	// include the database connection string

function usernameFromSID($session_id) 
{

//derive the username from a session ID

	global $mdb2; 	       // include the Database connector

	// Delete any expired session ids
	$mdb2->query("DELETE FROM Scrobble_Sessions WHERE expires < " . time());

	$res = $mdb2->query("SELECT username FROM Scrobble_Sessions WHERE sessionid = " . $mdb2->quote($session_id, "text")); // get the username from the table

	if(PEAR::isError($res)) {   
		die("FAILED " . $res->getMessage() . "\n");
		// die is there is an error, printing the error
	}

	if(!$res->numRows()) {
		die("BADSESSION\n");

		// the user has no session
	}

	return $res->fetchOne(0);

	       // return the first user
}

function createArtistIfNew($artist) {
	global $mdb2;

	$artist = NoSpamTracks($artist);

	$res = $mdb2->query("SELECT name FROM Artist WHERE name = " . ($artist));
	if(PEAR::isError($res)) {
		die("FAILED " . $res->getMessage() . "\n");
	}

	if(!$res->numRows()) {
		// Artist doesn't exist, so we create them
		$res = $mdb2->query("INSERT INTO Artist (name) VALUES (" . ($artist) . ")");
		if(PEAR::isError($res)) {
			die("FAILED " . $res->getMessage() . "\n");
		}
	}
}

function createAlbumIfNew($artist, $album) {
	global $mdb2;

	$res = $mdb2->query("SELECT name FROM Album WHERE name = " . ($album) . " AND artist_name = " . ($artist));
	if(PEAR::isError($res)) {
		die("FAILED " . $res->getMessage() . "\n");
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
			die("FAILED " . $res->getMessage() . "\n");
		}
	}
}

function getTrackCreateIfNew($artist, $album, $track, $mbid) {
	global $mdb2;

	$track = NoSpamTracks($track);
	$artist = NoSpamTracks($artist);

	if($album != 'NULL') {
	$res = $mdb2->query("SELECT id FROM Track WHERE lower(name) = " . (strtolower($track)) . " AND lower(artist) = " . (strtolower($artist)) . " AND lower(album) = lower(" . ($album) . ")");
	} else {
	$res = $mdb2->query("SELECT id FROM Track WHERE lower(name) = " . (strtolower($track)) . " AND lower(artist) = " . (strtolower($artist)) . " AND album IS NULL");
	}
	if(PEAR::isError($res)) {
		die("FAILED " . $res->getMessage() . "\n");
	}

	if(!$res->numRows()) {
		// Create new track
		$res = $mdb2->query("INSERT INTO Track (name, artist, album, mbid) VALUES ("
			. ($track) . ", "
			. ($artist) . ", "
			. ($album) . ", "
			. ($mbid) . ")");
		if(PEAR::isError($res)) {
			die("FAILED " . $res->getMessage() . "\n");
		}
		return getTrackCreateIfNew($artist, $album, $track, $mbid);
	} else {
		return $res->fetchOne(0);
	}
}

function getScrobbleTrackCreateIfNew($artist, $album, $track, $mbid, $tid) {
	global $mdb2;

	$res = $mdb2->query("SELECT id FROM Scrobble_Track WHERE name = " . (strtolower($track)) . " AND artist = " . (strtolower($artist)) . " AND album " . (($album == 'NULL') ? "IS NULL" : ("= " . (strtolower($album)))) . " AND mbid " . (($mbid == 'NULL') ? "IS NULL" : ("= " . (strtolower($mbid)))));
	if(PEAR::isError($res)) {
		die("FAILED " . $res->getMessage() . "\n");
	}

	if(!$res->numRows()) {
		// Create new track
		$res = $mdb2->exec("INSERT INTO Scrobble_Track (name, artist, album, mbid, track) VALUES ("
			. strtolower($track) . ", "
			. strtolower($artist) . ", "
			. strtolower($album) . ", "
			. strtolower($mbid) . ","
			. strtolower($tid) . ")");
		if(PEAR::isError($res)) {
			die("FAILED " . $res->getMessage() . "\n");
		}
		return getScrobbleTrackCreateIfNew($artist, $album, $track, $mbid, $tid);
	} else {
		return $res->fetchOne(0);
	}
}

function scrobbleExists($username, $artist, $track, $time) {
	global $mdb2;

	$res = $mdb2->query("SELECT time FROM Scrobbles WHERE username = " . ($username) . " AND artist = " . ($artist) . " AND track = " . ($track) . " AND time = " . ($time));
	if(PEAR::isError($res)) {
		die("FAILED " . $res->getMessage() . "\n");
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

$aws_xml = simplexml_load_file($request) or die("xml response not loading\n");

$image = $aws_xml->Items->Item->MediumImage->URL;
        $URI = $aws_xml->Items->Item->DetailPageURL;
        return $image;
}

?>
