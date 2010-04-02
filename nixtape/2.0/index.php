<?php

/* GNU FM -- a free network service for sharing your music listening habits

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

require_once('../database.php');
require_once('../api/ArtistXML.php');
require_once('../api/UserXML.php');
require_once('../api/TrackXML.php');

# Error constants
define('LFM_INVALID_SERVICE',	2);
define('LFM_INVALID_METHOD',	3);
define('LFM_INVALID_TOKEN',	4);
define('LFM_INVALID_FORMAT',	5);
define('LFM_INVALID_PARAMS',	6);
define('LFM_INVALID_RESOURCE',	7);
define('LFM_TOKEN_ERROR',	8);
define('LFM_INVALID_SESSION',	9);
define('LFM_INVALID_APIKEY',	10);
define('LFM_SERVICE_OFFLINE',	11);
define('LFM_SUBSCRIPTION_ERROR',12);
define('LFM_INVALID_SIGNATURE',	13);
define('LFM_SUBSCRIPTION_REQD',	18);
define('LFM_NOT_ENOUGH_CONTENT',	20);
define('LFM_NOT_ENOUGH_MEMBERS',	21);
define('LFM_NOT_ENOUGH_FANS',	22);
define('LFM_NOT_ENOUGH_NEIGHBORS',	23);

# Error descriptions as per API documentation
$error_text = array(
	LFM_INVALID_SERVICE		=> 'Invalid service - This service does not exist',
	LFM_INVALID_METHOD		=> 'Invalid Method - No method with that name in this package',
	LFM_INVALID_TOKEN		=> 'Invalid authentication token supplied',
	LFM_INVALID_FORMAT		=> 'Invalid format - This service doesn\'t exist in that format',
	LFM_INVALID_PARAMS		=> 'Invalid parameters - Your request is missing a required parameter',
	LFM_INVALID_RESOURCE		=> 'Invalid resource specified',
	LFM_TOKEN_ERROR			=> 'There was an error granting the request token. Please try again later',
	LFM_INVALID_SESSION		=> 'Invalid session key - Please re-authenticate',
	LFM_INVALID_APIKEY		=> 'Invalid API key - You must be granted a valid key by last.fm',
	LFM_SERVICE_OFFLINE		=> 'Service Offline - This service is temporarily offline. Try again later.',
	LFM_SUBSCRIPTION_ERROR		=> 'Subscription Error - The user needs to be subscribed in order to do that',
	LFM_INVALID_SIGNATURE		=> 'Invalid method signature supplied',
	LFM_SUBSCRIPTION_REQD		=> 'This user has no free radio plays left. Subscription required.',
	LFM_NOT_ENOUGH_CONTENT		=> 'There is not enough content to play this station',
	LFM_NOT_ENOUGH_MEMBERS		=> 'This group does not have enough members for radio',
	LFM_NOT_ENOUGH_FANS		=> 'This artist does not have enough fans for radio',
	LFM_NOT_ENOUGH_NEIGHBORS	=> 'Thare are not enough neighbors for radio'
);

# Resolves method= parameters to handler functions
$method_map = array(
	'auth.gettoken'			=> method_auth_gettoken,
	'auth.getsession'		=> method_auth_getsession,
	'auth.getmobilesession'		=> method_auth_getmobilesession,
	'artist.getinfo'		=> method_artist_getinfo,
	'artist.gettoptracks'		=> method_artist_gettoptracks,
	'user.getinfo'			=> method_user_getinfo,
	'user.gettoptracks'		=> method_user_gettoptracks,
	'user.getrecenttracks'		=> method_user_getrecenttracks,
	'user.gettoptags'		=> method_user_gettoptags,
	'radio.tune'			=> method_radio_tune,
	'radio.getplaylist'		=> method_radio_getPlaylist,
	'track.gettoptags'		=> method_track_getTopTags,
	'track.gettags'			=> method_track_getTags,
	'track.ban'			=> method_track_ban,
);

function method_user_getrecenttracks() {
	if (!isset($_GET['user'])) {
		report_failure(LFM_INVALID_SIGNATURE);
	}

	header('Content-Type: text/xml');
	print(XML::prettyXML(UserXML::getRecentTracks($_GET['user'], $_GET['limit'])));
}

function method_user_gettoptags() {
	if (!isset($_GET['user'])) {
		report_failure(LFM_INVALID_SIGNATURE);
	}

        header('Content-Type: text/xml');
        print(XML::prettyXML(UserXML::getTopTags($_GET['user'])));
}


function method_user_gettoptracks() {
	if (!isset($_GET['user'])) {
		report_failure(LFM_INVALID_SIGNATURE);
	}

	header('Content-Type: text/xml');
	print(XML::prettyXML(UserXML::getTopTracks($_GET['user'], $_GET['period'])));
}

function method_user_getinfo() {
	if (!isset($_GET['user'])) {
		report_failure(LFM_INVALID_SIGNATURE);
	}
	header('Content-Type: text/xml');
	print(XML::prettyXML(UserXML::getInfo($_GET['user'])));
}

function method_artist_getinfo() {
	if (!isset($_GET['artist'])) {
		report_failure(LFM_INVALID_SIGNATURE);
	}
	header('Content-Type: text/xml');
	print(XML::prettyXML(ArtistXML::getInfo($_GET['artist'])));
}

function method_artist_gettoptracks() {
	if (!isset($_GET['artist'])) {
	report_failure(LFM_INVALID_SIGNATURE);
	}
	header('Content-Type: text/xml');
	print(XML::prettyXML(ArtistXML::getTopTracks($_GET['artist'])));

}

function method_auth_gettoken() {
	global $adodb;

	if (!isset($_GET['api_sig']) || !valid_api_sig($_GET['api_sig']))
		report_failure(LFM_INVALID_SIGNATURE);

	$key = md5(time() . rand());

	try {
	$result = $adodb->Execute('INSERT INTO Auth (token, expires) VALUES ('
		. $adodb->qstr($key) . ", "
		. (int)(time() + 3600)
		. ")");
	}
	catch (exception $e) {
		report_failure(LFM_SERVICE_OFFLINE);
	}

	print("<lfm status=\"ok\">\n");
	print("	<token>{$key}</token></lfm>");
}

function method_auth_getmobilesession() {
	global $adodb;

	if (!isset($_GET['api_sig']) || !valid_api_sig($_GET['api_sig']))
		report_failure(LFM_INVALID_SIGNATURE);

	if (!isset($_GET['authToken']))
		report_failure(LFM_INVALID_TOKEN);

	// Check for a token that is bound to a user
	try {
	$result = $adodb->GetRow('SELECT username, password FROM Users WHERE '
		. 'username = ' . $adodb->qstr($_GET['username']));
	}
	catch (exception $e) {
		report_failure(LFM_SERVICE_OFFLINE);
	}
	if (is_null($result)) {
		report_failure(LFM_INVALID_TOKEN);
	}

	list($username, $password) = $result;
	if (md5($username . $password) != $_GET['authToken']) {
		report_failure(LFM_INVALID_TOKEN);
	}

	$key = md5(time() . rand());
	$session = md5(time() . rand());

	// Update the Auth record with the new session key
	try {
	$result = $adodb->Execute('INSERT INTO Auth (token, sk, expires, username) '
		. 'VALUES ('
		. $adodb->qstr($key) . ', '
		. $adodb->qstr($session) . ', '
		. (int)(time() + 3600) . ', '
		. $adodb->qstr($username)
		. ')');
	}
	catch (exception $e) {
		report_failure(LFM_SERVICE_OFFLINE);
	}

	print("<lfm status=\"ok\">\n");
	print("	<session>\n");
	print("		<name>{$username}</name>\n");
	print("		<key>{$session}</key>\n");
	print("		<subscriber>0</subscriber>\n");
	print("	</session>\n");
	print("</lfm>");
}

function method_auth_getsession() {
	global $adodb;

	if (!isset($_GET['api_sig']) || !valid_api_sig($_GET['api_sig']))
		report_failure(LFM_INVALID_SIGNATURE);

	if (!isset($_GET['token']))
		report_failure(LFM_INVALID_TOKEN);

	// Check for a token that (1) is bound to a user, and (2) is not bound to a session
	try {
	$username = $adodb->GetOne('SELECT username FROM Auth WHERE '
		. 'token = ' . $adodb->qstr($_GET['token']) . ' AND '
		. 'username IS NOT NULL AND sk IS NULL');
	}
	catch (exception $e) {
		report_failure(LFM_SERVICE_OFFLINE);
	}
	if (!$username) {
		report_failure(LFM_INVALID_TOKEN);
	}

	$session = md5(time() . rand());

	// Update the Auth record with the new session key
	try {
	$result = $adodb->Execute('UPDATE Auth SET '
		. 'sk = ' . $adodb->qstr($session) . ' WHERE '
		. 'token = ' . $adodb->qstr($_GET['token']));
	}
	catch (exception $e) {
		report_failure(LFM_SERVICE_OFFLINE);
	}

	print("<lfm status=\"ok\">\n");
	print("	<session>\n");
	print("		<name>{$username}</name>\n");
	print("		<key>{$session}</key>\n");
	print("		<subscriber>0</subscriber>\n");
	print("	</session>\n");
	print("</lfm>");
}

function method_radio_tune() {
	global $adodb;

	if (!isset($_GET['api_sig']) || !valid_api_sig($_GET['api_sig']))
		report_failure(LFM_INVALID_SIGNATURE);

	if (!isset($_GET['station']))
		report_failure(LFM_INVALID_PARAMS);

	if (!isset($_GET['api_key']))
		report_failure(LFM_INVALID_PARAMS);

	if (!isset($_GET['sk']))
		report_failure(LFM_INVALID_PARAMS);

	try {
	$username = $adodb->GetOne('SELECT username FROM Auth WHERE '
		. 'token = ' . $adodb->qstr($_GET['token']) . ' AND '
		. 'username IS NOT NULL AND sk = '.$adodb->qstr($_GET['sk']));
	}
	catch (exception $e) {
		report_failure(LFM_SERVICE_OFFLINE);
	}
	if (!$username) {
		report_failure(LFM_INVALID_TOKEN);
	}

/*
 * Here we should tune the station.  The immediate problem is that
 * without radio handshake, the user will not necessarily have a
 * session in Radio_Sessions.
 *
 * After that's solved, we should either set $stationtype,
 * $stationname, $stationurl, or report_failure.
 */
	report_failure(LFM_SUBSCRIPTION_REQD);

	print("<lfm status=\"ok\">\n");
	print("	<station>\n");
	print("		<type>{$stationtype}</type>\n");
	print("		<name>{$stationname}</name>\n");
	print("		<url>{$stationurl}</url>\n");
	print("		<supportsdiscovery>0</supportsdiscovery>\n");
	print("	</station>\n");
	print("</lfm>");
}

function method_radio_getPlaylist() {
	global $adodb;

	if (!isset($_GET['api_sig']) || !valid_api_sig($_GET['api_sig']))
		report_failure(LFM_INVALID_SIGNATURE);

	if (!isset($_GET['api_key']))
		report_failure(LFM_INVALID_PARAMS);

	if (!isset($_GET['sk']))
		report_failure(LFM_INVALID_PARAMS);

/*
 * Here we should get the station based on the session key.  If
 * no station is tuned for that key, we should default to something
 * reasonable.
 *
 * Then we should return a playlist in a format not quite identical
 * to the one spit out
 * by xspf.php.
 */

        $res = $adodb->Execute('SELECT Track.name, Track.artist_name, Track.album_name FROM Track INNER JOIN Tags ON Track.name=Tags.track 
AND Track.artist_name=Tags.artist AND Track.album_name=Tags.album WHERE streamurl<>\'\' AND streamable=1 AND lower(tag) = \'folk\'');

$avail = $res->RecordCount();

$tr[0] = rand(0,$avail-1);
$tr[1] = rand(0,$avail-1);
$tr[2] = rand(0,$avail-1);
$tr[3] = rand(0,$avail-1);
$tr[4] = rand(0,$avail-1);
$tr = array_unique($tr);
// we should probably shuffle these here

$radiotracks = array();
$adodb->SetFetchMode(ADODB_FETCH_ASSOC);

        for($i=0; $i<count($tr); $i++) {

        $res->Move($tr[$i]);
        $row = $res->FetchRow();

        $track = new Track($row['name'], $row['artist_name']);
        $album = new Album($row['album_name'], $row['artist_name']);
        $artist = new Artist($row['artist_name']);

        if($track->duration == 0) {
                $duration = 180000;
        } else {
                $duration = $track->duration * 1000;
        }

        $radiotracks[$i]['location'] = $track->streamurl;
        $radiotracks[$i]['title'] = $track->name;
        $radiotracks[$i]['id'] = "0000";
        $radiotracks[$i]['album'] = $album->name;
        $radiotracks[$i]['creator'] = $artist->name;
        $radiotracks[$i]['duration'] = $duration;
        $radiotracks[$i]['image'] = $album->image;
        $radiotracks[$i]['artisturl'] = $artist->getURL();
        $radiotracks[$i]['albumurl'] = $album->getURL();
        $radiotracks[$i]['trackurl'] = $track->getURL();
        $radiotracks[$i]['downloadurl'] = $track->getURL();

        }

	print("<lfm status=\"ok\">\n");
	print("<playlist version=\"1\" xmlns=\"http://xspf.org/ns/0/\">\n");
	print("<title>Fake Playlist</title>\n");
	print("<creator>libre.fm</creator>\n");
	print("<link rel=\"http://libre.fm/expiry\">9999</link>\n");
	print("<trackList>\n");

        for($i=0; $i<count($tr); $i++) {
        print("<track>\n");
        print("<location>".urlencode($radiotracks[$i]['location'])."</location>\n");
        print("<title>".urlencode($radiotracks[$i]['title'])."</title>\n");
        print("<id>".urlencode($radiotracks[$i]['id'])."</id>\n");
        print("<album>".urlencode($radiotracks[$i]['album'])."</album>\n");
        print("<creator>".urlencode($radiotracks[$i]['creator'])."</creator>\n");
        print("<duration>".urlencode($radiotracks[$i]['duration'])."</duration>\n");
        print("<image>".urlencode($radiotracks[$i]['image'])."</image>\n");
        print("</track>\n");
	}

	print("</trackList>\n");
	print("</playlist>\n");
	print("</lfm>\n");

}

/**
 * Track methods
 */

function method_track_getTopTags() {
	if (!isset($_GET['artist']) || !isset($_GET['track'])) {
		report_failure(LFM_INVALID_SIGNATURE);
	}

	header('Content-Type: text/xml');
	print(XML::prettyXML(TrackXML::getTopTags($_GET['artist'], $_GET['track'])));
}

function method_track_getTags() {
	global $adodb;

	if (!isset($_GET['artist']) || !isset($_GET['track'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$userid = get_userid();

	header('Content-Type: text/xml');
	print(XML::prettyXML(TrackXML::getTags($_GET['artist'], $_GET['track'], $userid)));
}

function method_track_ban() {
	if (!isset($_POST['artist']) || !isset($_POST['track'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$userid = get_userid();

	header('Content-Type: text/xml');
	print(XML::prettyXML(TrackXML::ban($_POST['artist'], $_POST['track'], $userid)));
}


function get_userid() {
	global $adodb;

	if (!isset($_REQUEST['sk'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$username = $adodb->GetOne('SELECT username FROM Auth WHERE '
		. 'sk = ' . $adodb->qstr($_GET['sk']) . ' AND '
		. 'username IS NOT NULL');

	if (!$username) {
		report_failure(LFM_INVALID_SESSION);
	}

	$userid = $adodb->GetOne('SELECT uniqueid FROM Users WHERE '
		. 'username = ' . $adodb->qstr($username));

	return $userid;
}

function valid_api_key($key) {
	return strlen($key) == 32;
}

function valid_api_sig($sig) {
	return strlen($sig) == 32;
}

function report_failure($code) {
	global $error_text;

	print("<lfm status=\"failed\">\n");
	print("	<error code=\"{$code}\">".$error_text[$code]."</error></lfm>");
	die();
}

$_REQUEST['method'] = strtolower($_REQUEST['method']);
if (!isset($_REQUEST['method']) || !isset($method_map[$_REQUEST['method']]))
	report_failure(LFM_INVALID_METHOD);

$method = $method_map[$_REQUEST['method']];
$method();
