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

	global $adodb; 	       // include the Database connector

	// Delete any expired session ids
	$adodb->Execute('DELETE FROM Scrobble_Sessions WHERE expires < ' . time());

	try {
		$res = $adodb->GetOne('SELECT username FROM Scrobble_Sessions WHERE sessionid = ' . $adodb->qstr($session_id)); // get the username from the table
	}
	catch (exception $e) {
		die('FAILED ufs ' . $e->getMessage() . '\n');
		// die is there is an error, printing the error
	}

	if(!$res) {
		die('BADSESSION\n');

		// the user has no session
	}

	return $res;

	       // return the first user
}

function createArtistIfNew($artist) {
	global $adodb;

	$artist = NoSpamTracks($artist);

	try {
		$res = $adodb->GetOne('SELECT name FROM Artist WHERE lower(name) = lower(' . ($artist) . ')');
	}
	catch (exception $e) {
		die('FAILED art ' . $e->getMessage() . '\n');
	}

	if(!$res) {
		// Artist doesn't exist, so we create them
		try {
			$res = $adodb->Execute('INSERT INTO Artist (name) VALUES (' . ($artist) . ')');
		}
		catch (exception $e) {
			die('FAILED artc ' . $e->getMessage() . '\n');
		}
	}
}

function createAlbumIfNew($artist, $album) {
	global $adodb;

	try {
		$name = $adodb->GetOne('SELECT name FROM Album WHERE lower(name) = lower(' . ($album) . ') AND lower(artist_name) = lower(' . ($artist) . ')');
	}
	catch (exception $e) {
		die('FAILED alb ' . $e->getMessage() . '\n');
	}

	if(!$name) {
		// Album doesn't exist, so create it

		$art = $adodb->qstr(getAlbumArt($artist, $album));

		if ($art !="") {
			$license = $adodb->qstr('amazon');

			$sql = 'INSERT INTO Album (name, artist_name, image, artwork_license) VALUES (' . ($album) . ', ' . ($artist) . ', ' . ($art) . ', ' . ($license) .')';

		} else {
			$sql = 'INSERT INTO Album (name, artist_name) VALUES (' . ($album) . ', ' . ($artist) . ')';
		}
		try {
			$adodb->Execute($sql);
		}
		catch (exception $e) {
			die('FAILED albc ' . $e->getMessage() . '\n');
		}
	}
}

function getTrackCreateIfNew($artist, $album, $track, $mbid) {
	global $adodb;

	$track = NoSpamTracks($track);
	$artist = NoSpamTracks($artist);

	try {
	if($album != 'NULL') {
	$res = $adodb->GetOne('SELECT id FROM Track WHERE lower(name) = lower(' . ($track) . ') AND lower(artist_name) = lower(' . ($artist) . ') AND lower(album_name) = lower(' . ($album) . ')');
	} else {
	$res = $adodb->GetOne('SELECT id FROM Track WHERE lower(name) = lower(' . ($track) . ') AND lower(artist_name) = lower(' . ($artist) . ') AND album_name IS NULL');
	}
	}
	catch (exception $e) {
		die('FAILED trk ' . $e->getMessage() . '\n');
	}

	if(!$res) {
		// Create new track
		try {
		$res = $adodb->Execute('INSERT INTO Track (name, artist_name, album_name, mbid) VALUES ('
			. ($track) . ', '
			. ($artist) . ', '
			. ($album) . ', '
			. ($mbid) . ')');
		}
		catch (exception $e) {
			die('FAILED trkc ' . $e->getMessage() . '\n');
		}
		return getTrackCreateIfNew($artist, $album, $track, $mbid);
	} else {
		return $res;
	}
}

function getScrobbleTrackCreateIfNew($artist, $album, $track, $mbid, $tid) {
	global $adodb;

	try {
	$res = $adodb->GetOne('SELECT id FROM Scrobble_Track WHERE name = lower('
		. ($track) . ') AND artist = lower(' . ($artist) . ') AND album '
		. (($album == 'NULL') ? 'IS NULL' : ('= lower(' . ($album) . ')')) . ' AND mbid '
		. (($mbid == 'NULL') ? 'IS NULL' : ('= lower(' . ($mbid) . ')')));
	}
	catch (exception $e) {
		die('FAILED st ' . $e->getMessage() . '\n');
	}

	if(!$res) {
		$sql = 'INSERT INTO Scrobble_Track (name, artist, album, mbid, track) VALUES ('
			. 'lower(' . ($track) . '), '
			. 'lower(' . ($artist) . '), '
			. (($album == 'NULL') ? 'NULL' : 'lower(' . ($album) . ')') . ', '
			. (($mbid == 'NULL') ? 'NULL' : 'lower(' . ($mbid) . ')') . ', '
			. ($tid) . ')';
		try {
			$res = $adodb->Execute($sql);
		}
		catch (exception $e) {
			$msg = $e->getMessage() . ' - ' . $e->getUserInfo();
			reportError($msg, $sql);

			die('FAILED stc ' . $res->getMessage() . '\n');
		}
		return getScrobbleTrackCreateIfNew($artist, $album, $track, $mbid, $tid);
	} else {
		return $res;
	}
}

function scrobbleExists($username, $artist, $track, $time) {
	global $adodb;

	try {
	$res = $adodb->GetOne('SELECT time FROM Scrobbles WHERE username = ' . ($username) . ' AND artist = ' . ($artist) . ' AND track = ' . ($track) . ' AND time = ' . ($time));
	}
	catch (exception $e) {
		die('FAILED se ' . $e->getMessage() . '\n');
	}

	if(!$res) {
		return false;
	} else {
		return true;
	}
}
function NoSpamTracks ($track) {

  // This function exists to remove things like '(PREVIEW: buy it at www.magnatune.com)' from track names.

  $track = str_replace(' (PREVIEW: buy it at www.magnatune.com)', "", $track);

  return $track;
  
}

function getAlbumArt($artist, $album) {

  $Access_Key_ID = '1EST86JB355JBS3DFE82'; // this is mattl's personal key :)

        $SearchIndex='Music';
$Keywords=urlencode($artist.' '.$album);
        $Operation = 'ItemSearch';
$Version = '2007-07-16';
        $ResponseGroup = 'ItemAttributes,Images';
$request=
        'http://ecs.amazonaws.com/onca/xml'
                . '?Service=AWSECommerceService'
. '&AssociateTag=' . $Associate_tag
. '&AWSAccessKeyId=' . $Access_Key_ID
. '&Operation=' . $Operation
. '&Version=' . $Version
. '&SearchIndex=' . $SearchIndex
. '&Keywords=' . $Keywords
. '&ResponseGroup=' . $ResponseGroup;

$aws_xml = simplexml_load_file($request) or die('xml response not loading\n');

$image = $aws_xml->Items->Item->MediumImage->URL;
        $URI = $aws_xml->Items->Item->DetailPageURL;
        return $image;
}

function validateMBID ($input) {
if(isset($input)) {
	$input = strtolower(rtrim($input));
	if(preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/', $input)) {
		return $input;
	} else {
		return null;
	}
} else {
	return null;
}

}
?>
