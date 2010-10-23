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
require_once('../api/JSONEncoder.php');
require_once('../api/TrackXML.php');
require_once('../api/AlbumXML.php');
require_once('../data/Server.php');
require_once('../radio/radio-utils.php');

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
define('LFM_TOKEN_UNAUTHORISED', 14);
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
	LFM_TOKEN_UNAUTHORISED		=> 'This token has not yet been authorised',
	LFM_SUBSCRIPTION_REQD		=> 'This user has no free radio plays left. Subscription required.',
	LFM_NOT_ENOUGH_CONTENT		=> 'There is not enough content to play this station',
	LFM_NOT_ENOUGH_MEMBERS		=> 'This group does not have enough members for radio',
	LFM_NOT_ENOUGH_FANS		=> 'This artist does not have enough fans for radio',
	LFM_NOT_ENOUGH_NEIGHBORS	=> 'Thare are not enough neighbors for radio'
);

# Resolves method= parameters to handler functions
$method_map = array(
	'auth.gettoken'			=> method_auth_getToken,
	'auth.getsession'		=> method_auth_getSession,
	'auth.getmobilesession'		=> method_auth_getMobileSession,
	'artist.addtags'		=> method_artist_addTags,
	'artist.getinfo'		=> method_artist_getInfo,
	'artist.gettoptracks'		=> method_artist_getTopTracks,
	'artist.gettoptags'		=> method_artist_getTopTags,
	'album.addtags'			=> method_album_addTags,
	'album.gettoptags'		=> method_album_getTopTags,
	'user.getinfo'			=> method_user_getInfo,
	'user.gettoptracks'		=> method_user_getTopTracks,
	'user.getrecenttracks'		=> method_user_getRecentTracks,
	'user.gettoptags'		=> method_user_getTopTags,
	'user.getlovedtracks'		=> method_user_getLovedTracks,
	'radio.tune'			=> method_radio_tune,
	'radio.getplaylist'		=> method_radio_getPlaylist,
	'track.addtags'			=> method_track_addTags,
	'track.gettoptags'		=> method_track_getTopTags,
	'track.gettags'			=> method_track_getTags,
	'track.ban'			=> method_track_ban,
	'track.love'			=> method_track_love,
);


/**
 * User methods
 */

function method_user_getRecentTracks() {
	if (!isset($_GET['user'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	if (isset($_GET['page'])) {
		$page = $_GET['page'];
	} else {
		$page = 1;
	}

	$xml = UserXML::getRecentTracks($_GET['user'], $_GET['limit'], $page);
	respond($xml);
}

function method_user_getTopTags() {
	if (!isset($_GET['user'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$xml = UserXML::getTopTags($_GET['user']);
	respond($xml);
}


function method_user_getTopTracks() {
	if (!isset($_GET['user'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$xml = UserXML::getTopTracks($_GET['user'], $_GET['period']);
	respond($xml);
}

function method_user_getInfo() {
	if (!isset($_GET['user'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$xml = UserXML::getInfo($_GET['user']);
	respond($xml);
}

function method_user_getLovedTracks() {
	if (!isset($_GET['user'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$user = $_GET['user'];
	if (isset($_GET['limit'])) {
		$limit = $_GET['limit'];
	} else {
		$limit = 50;
	}

	$xml = UserXML::getLovedTracks($user, $limit);
	respond($xml);
}



/**
 * Artist methods
 */

function method_artist_addTags() {
	if (!isset($_POST['artist']) || !isset($_POST['tags'])) {
		report_failure(LFM_INVALID_PARAMS);
	}
	
	$userid = get_userid();
	$xml = TrackXML::addTags($userid, $_POST['artist'], '', '', $_POST['tags']);
	respond($xml);
}

function method_artist_getInfo() {
	if (!isset($_GET['artist'])) {
		report_failure(LFM_INVALID_PARAMS);
	}
	
	$xml = ArtistXML::getInfo($_GET['artist']);
	respond($xml);
}

function method_artist_getTopTracks() {
	if (!isset($_GET['artist'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$xml = ArtistXML::getTopTracks($_GET['artist']);
	respond($xml);
}

function method_artist_getTopTags() {
	if (!isset($_GET['artist'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$xml = ArtistXML::getTopTags($_GET['artist']);
	respond($xml);
}


/**
 * Album methods
 */

function method_album_addTags() {
	if (!isset($_POST['artist']) || !isset($_POST['album']) || !isset($_POST['tags'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$userid = get_userid();
	$xml = TrackXML::addTags($userid, $_POST['artist'], $_POST['album'], '', $_POST['tags']);
	respond($xml);
}

function method_album_getTopTags() {
	if (!isset($_GET['artist']) || !isset($_GET['album'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$xml = AlbumXML::getTopTags($_GET['artist'], $_GET['album']);
	respond($xml);
}


/**
 * Authentication methods
 */

function method_auth_getToken() {
	global $adodb;

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

	$xml = '<lfm status="ok"><token>' . $key . '</token></lfm>';
	respond($xml);
}

function method_auth_getMobileSession() {
	global $adodb;

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

	if ($_GET['format'] == 'json') {
		$json_data = array('session' => array('name' => $username, 'key' => $session, 'subscriber' => 0));
		json_response(json_encode($json_data));
	} else {
		print("<lfm status=\"ok\">\n");
		print("	<session>\n");
		print("		<name>{$username}</name>\n");
		print("		<key>{$session}</key>\n");
		print("		<subscriber>0</subscriber>\n");
		print("	</session>\n");
		print("</lfm>");
	}
}

function method_auth_getSession() {
	global $adodb;

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
		report_failure(LFM_TOKEN_UNAUTHORISED);
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

	if ($_GET['format'] == 'json') {
		$json_data = array('session' => array('name' => $username, 'key' => $session, 'subscriber' => 0));
		json_response(json_encode($json_data));
	} else {
		print("<lfm status=\"ok\">\n");
		print("	<session>\n");
		print("		<name>{$username}</name>\n");
		print("		<key>{$session}</key>\n");
		print("		<subscriber>0</subscriber>\n");
		print("	</session>\n");
		print("</lfm>");
	}
}

function method_radio_tune() {
	global $adodb;

	if (!isset($_POST['station']))
		report_failure(LFM_INVALID_PARAMS);

	if (!isset($_POST['sk']))
		report_failure(LFM_INVALID_PARAMS);

	try {
	$username = $adodb->GetOne('SELECT username FROM Auth WHERE '
		. 'sk = ' . $adodb->qstr($_POST['sk']) . ' AND '
		. 'username IS NOT NULL');
	}
	catch (exception $e) {
		report_failure(LFM_SERVICE_OFFLINE);
	}
	if (!$username) {
		report_failure(LFM_INVALID_TOKEN);
	}

	Server::getRadioSession($_POST['station'], $username, $_POST['sk']);
	$stationtype = 'globaltag';
	$stationname = radio_title_from_url($_POST['station']);
	$stationurl = 'http://libre.fm';

	if ($_GET['format'] == 'json') {
		header('Content-Type: text/javascript');
		$json_data = array('station' => array('type' => $stationtype, 'name' => $stationname, 'url' => $stationurl, 'supportsdiscovery' => 1));
		print(json_encode($json_data));
	} else {
		header('Content-Type: text/xml');
		print("<lfm status=\"ok\">\n");
		print("	<station>\n");
		print("		<type>" . $stationtype . "</type>\n");
		print("		<name>" .$stationname . "</name>\n");
		print("		<url>" . $stationurl . "</url>\n");
		print("		<supportsdiscovery>1</supportsdiscovery>\n");
		print("	</station>\n");
		print("</lfm>");
	}
}

function method_radio_getPlaylist() {
	global $adodb;

	if (!isset($_REQUEST['sk']))
		report_failure(LFM_INVALID_PARAMS);

	make_playlist($_REQUEST['sk']);
}

/**
 * Track methods
 */

function method_track_addTags() {
	if (!isset($_POST['artist']) || !isset($_POST['track']) || !isset($_POST['tags'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$userid = get_userid();
	$xml = TrackXML::addTags($userid, $_POST['artist'], $_POST['album'], $_POST['track'], $_POST['tags']);
	respond($xml);
}

function method_track_getTopTags() {
	if (!isset($_GET['artist']) || !isset($_GET['track'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$xml = TrackXML::getTopTags($_GET['artist'], $_GET['track']);
	respond($xml);
}

function method_track_getTags() {
	global $adodb;

	if (!isset($_GET['artist']) || !isset($_GET['track'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$userid = get_userid();
	$xml = TrackXML::getTags($_GET['artist'], $_GET['track'], $userid);
	respond($xml);
}

function method_track_ban() {
	if (!isset($_POST['artist']) || !isset($_POST['track'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$userid = get_userid();
	$xml = TrackXML::ban($_POST['artist'], $_POST['track'], $userid);
	respond($xml);
}

function method_track_love() {
	if (!isset($_POST['artist']) || !isset($_POST['track'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$userid = get_userid();
	$xml = TrackXML::love($_POST['artist'], $_POST['track'], $userid);
	respond($xml);
}

function get_userid() {
	global $adodb;

	if (!isset($_REQUEST['sk'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$username = $adodb->GetOne('SELECT username FROM Auth WHERE '
		. 'sk = ' . $adodb->qstr($_REQUEST['sk']) . ' AND '
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

	if($_REQUEST['format'] == 'json') {
		$json_data = array('error' => $code, 'message' => $error_text[$code]);
		json_response(json_encode($json_data));
	} else {
		print("<lfm status=\"failed\">\n");
		print("	<error code=\"{$code}\">".$error_text[$code]."</error></lfm>");
	}
	die();
}

function respond($xml) {
	if ($_REQUEST['format'] == 'json') {
		json_response(JSONEncoder::encodeXML($xml));
	} else {
		xml_response($xml);
	}
}

function xml_response($xml) {
	header('Content-Type: text/xml');
	print(XML::prettyXML($xml));
}

function json_response($data) {
	header('Content-Type: text/javascript');
	if($_REQUEST['callback']) {
		print($_REQUEST['callback'] . '(' . $data . ');');
	} else {
		print($data);
	}
}

$_REQUEST['method'] = strtolower($_REQUEST['method']);
if (!isset($_REQUEST['method']) || !isset($method_map[$_REQUEST['method']]))
	report_failure(LFM_INVALID_METHOD);

$method = $method_map[$_REQUEST['method']];
$method();
