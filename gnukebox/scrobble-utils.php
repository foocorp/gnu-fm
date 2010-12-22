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

require_once('database.php');	// include the database connection string
require_once('temp-utils.php');

function useridFromSID($session_id)
{
	//derive the username from a session ID
	global $adodb; 	   // include the Database connector

	// Delete any expired session ids
	$adodb->Execute('DELETE FROM Scrobble_Sessions WHERE expires < ' . time());

	try {
		$res = $adodb->GetOne('SELECT userid FROM Scrobble_Sessions WHERE sessionid = ' . $adodb->qstr($session_id)); // get the username from the table
	}
	catch (exception $e) {
		die('FAILED ufs ' . $e->getMessage() . '\n');
		// die is there is an error, printing the error
	}

	if(!$res) {
		die("BADSESSION\n");
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
			$artist = substr($artist,0,255);
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
		$artist = substr($artist,0,255);
		$album = substr($album,0,255);
		$name = $adodb->GetOne('SELECT name FROM Album WHERE lower(name) = lower(' . ($album) . ') AND lower(artist_name) = lower(' . ($artist) . ')');
	}
	catch (exception $e) {
		die('FAILED alb ' . $e->getMessage() . '\n');
	}

	if(!$name) {
		// Album doesn't exist, so create it

		// Disable to fix scrobble breakage
		//$art = $adodb->qstr(getAlbumArt($artist, $album));
		$art = "";

		if ($art !="") {
			$license = $adodb->qstr('amazon');
			$sql = 'INSERT INTO Album (name, artist_name, image, artwork_license) VALUES (' . ($album) . ', ' . ($artist) . ', ' . ($art) . ', ' . ($license) .')';
		} else {
			$sql = 'INSERT INTO Album (name, artist_name) VALUES (' . ($album) . ', ' . ($artist) . ')';
		}
		try {
			$adodb->Execute($sql);
		} catch (exception $e) {
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
			$track = substr($track,0,255);
			$artist = substr($artist,0,255);
			$album = substr($album,0,255);
			$res = $adodb->Execute('INSERT INTO Track (name, artist_name, album_name, mbid) VALUES ('
				. ($track) . ', '
				. ($artist) . ', '
				. ($album) . ', '
				. ($mbid) . ')');
		} catch (exception $e) {
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
	} catch (exception $e) {
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
			$msg = $e->getMessage();
			reportError($msg, $sql);
			die('FAILED stc ' . $res->getMessage() . '\n');
		}
		return getScrobbleTrackCreateIfNew($artist, $album, $track, $mbid, $tid);
	} else {
		return $res;
	}
}

function scrobbleExists($userid, $artist, $track, $time) {
	global $adodb;

	try {
		$res = $adodb->GetOne('SELECT time FROM Scrobbles WHERE userid = ' . ($userid) . ' AND artist = ' . ($artist) . ' AND track = ' . ($track) . ' AND time = ' . ($time));
	} catch (exception $e) {
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
	$request='http://ecs.amazonaws.com/onca/xml'
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

/**
 * Sends a scrobble on to any other services the user has connected to their account
 */
function forwardScrobble($userid, $artist, $album, $track, $time, $mbid, $source, $rating, $length) {
	global $adodb, $lastfm_key, $lastfm_secret;

	// Strip database quoting from details and urlencode
	$artist = urlencode(substr($artist, 1, strlen($artist) - 2));
	$track = urlencode(substr($track, 1, strlen($track) - 2));
	$album == 'NULL' ? $album = false : $album = urlencode(substr($album, 1, strlen($album) - 2));
	$mbid == 'NULL' ? $mbid = false : $mbid = urlencode(substr($mbid, 1, strlen($mbid) - 2));
	$source == 'NULL' ? $source = false : $source = urlencode(substr($source, 1, strlen($source) - 2));
	$rating == '\'0\'' ? $rating = false : 0;
	$length == 'NULL' ? $length = false : 0;

	$res = $adodb->CacheGetAll(600, 'SELECT * FROM Service_Connections WHERE userid = ' . $userid . ' AND forward = 1');
	foreach($res as &$row) {
		$remote_key = $row['remote_key'];
		$ws_url = $row['webservice_url'];
		$curl_session = curl_init($ws_url);

		$post_vars = '';
		if($album) {
			$post_vars .= 'album[0]=' . $album . '&';
		}
		$post_vars .= 'api_key=' . $lastfm_key . '&artist[0]=' . $artist;
		if($length) {
			$post_vars .= '&length[0]=' . $length;
		}
		if($mbid) {
			$post_vars .= '&mbid[0]=' . $mbid;
		}
		$post_vars .= '&method=track.scrobble';
		if($rating) {
			$post_vars .= '&rating[0]=' . $rating;
		}
		$post_vars .= '&sk=' . $remote_key;
		if($source) {
			$post_vars .= '&source[0]='. $source;
		}
		$post_vars .= '&timestamp[0]=' . $time . '&track[0]=' . $track;

		$sig = str_replace('&', '', urldecode($post_vars));
		$sig = str_replace('=', '', $sig);
		
		$sig = md5($sig . $lastfm_secret);

		$post_vars .= '&api_sig=' . $sig;
		curl_setopt ($curl_session, CURLOPT_POST, true);
		curl_setopt ($curl_session, CURLOPT_POSTFIELDS, $post_vars);
		$response = curl_exec($curl_session);

		curl_close($curl_session);
	}
}

?>
