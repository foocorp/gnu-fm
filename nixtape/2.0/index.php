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

/**
 * GNU FM web service methods
 *
 * @link http://bugs.foocorp.net/projects/librefm/wiki/Librefm_API
 * @package Webservice
 */
require_once('../database.php');
require_once('../api/ArtistXML.php');
require_once('../api/UserXML.php');
require_once('../api/JSONEncoder.php');
require_once('../api/TrackXML.php');
require_once('../api/AlbumXML.php');
require_once('../api/TagXML.php');
require_once('../data/Server.php');
require_once('../radio/radio-utils.php');

# Error constants
define('LFM_INVALID_SERVICE',       2);
define('LFM_INVALID_METHOD',        3);
define('LFM_INVALID_TOKEN',         4);
define('LFM_INVALID_FORMAT',        5);
define('LFM_INVALID_PARAMS',        6);
define('LFM_INVALID_RESOURCE',      7);
define('LFM_TOKEN_ERROR',           8);
define('LFM_INVALID_SESSION',       9);
define('LFM_INVALID_APIKEY',       10);
define('LFM_SERVICE_OFFLINE',      11);
define('LFM_SUBSCRIPTION_ERROR',   12);
define('LFM_INVALID_SIGNATURE',    13);
define('LFM_TOKEN_UNAUTHORISED',   14);
define('LFM_SUBSCRIPTION_REQD',    18);
define('LFM_NOT_ENOUGH_CONTENT',   20);
define('LFM_NOT_ENOUGH_MEMBERS',   21);
define('LFM_NOT_ENOUGH_FANS',      22);
define('LFM_NOT_ENOUGH_NEIGHBORS', 23);

# Error descriptions as per API documentation
$error_text = array(
	LFM_INVALID_SERVICE      => 'Invalid service - This service does not exist',
	LFM_INVALID_METHOD       => 'Invalid Method - No method with that name in this package',
	LFM_INVALID_TOKEN        => 'Invalid authentication token supplied',
	LFM_INVALID_FORMAT       => 'Invalid format - This service doesn\'t exist in that format',
	LFM_INVALID_PARAMS       => 'Invalid parameters - Your request is missing a required parameter',
	LFM_INVALID_RESOURCE     => 'Invalid resource specified',
	LFM_TOKEN_ERROR          => 'There was an error granting the request token. Please try again later',
	LFM_INVALID_SESSION      => 'Invalid session key - Please re-authenticate',
	LFM_INVALID_APIKEY       => 'Invalid API key - You must be granted a valid key by last.fm',
	LFM_SERVICE_OFFLINE      => 'Service Offline - This service is temporarily offline. Try again later.',
	LFM_SUBSCRIPTION_ERROR   => 'Subscription Error - The user needs to be subscribed in order to do that',
	LFM_INVALID_SIGNATURE    => 'Invalid method signature supplied',
	LFM_TOKEN_UNAUTHORISED   => 'This token has not yet been authorised',
	LFM_SUBSCRIPTION_REQD    => 'This user has no free radio plays left. Subscription required.',
	LFM_NOT_ENOUGH_CONTENT   => 'There is not enough content to play this station',
	LFM_NOT_ENOUGH_MEMBERS   => 'This group does not have enough members for radio',
	LFM_NOT_ENOUGH_FANS      => 'This artist does not have enough fans for radio',
	LFM_NOT_ENOUGH_NEIGHBORS => 'Thare are not enough neighbors for radio'
);

# Resolves method= parameters to handler functions
$method_map = array(
	'auth.gettoken'         => method_auth_getToken,
	'auth.getsession'       => method_auth_getSession,
	'auth.getmobilesession' => method_auth_getMobileSession,
	'artist.addtags'        => method_artist_addTags,
	'artist.getinfo'        => method_artist_getInfo,
	'artist.gettoptracks'   => method_artist_getTopTracks,
	'artist.gettoptags'     => method_artist_getTopTags,
	'artist.gettags'        => method_artist_getTags,
	'artist.getflattr'      => method_artist_getFlattr,
	'album.addtags'         => method_album_addTags,
	'album.gettoptags'      => method_album_getTopTags,
	'album.gettags'         => method_album_getTags,
	'user.getinfo'          => method_user_getInfo,
	'user.gettopartists'	=> method_user_getTopArtists,
	'user.gettoptracks'     => method_user_getTopTracks,
	'user.getrecenttracks'  => method_user_getRecentTracks,
	'user.gettoptags'       => method_user_getTopTags,
	'user.getpersonaltags'  => method_user_getPersonalTags,
	'user.gettaginfo'       => method_user_getTagInfo,
	'user.getlovedtracks'   => method_user_getLovedTracks,
	'user.getbannedtracks'  => method_user_getBannedTracks,
	'user.getneighbours'    => method_user_getNeighbours,
	'radio.tune'            => method_radio_tune,
	'radio.getplaylist'     => method_radio_getPlaylist,
	'tag.gettoptags'        => method_tag_getTopTags,
	'tag.gettopartists'     => method_tag_getTopArtists,
	'tag.gettopalbums'      => method_tag_getTopAlbums,
	'tag.gettoptracks'      => method_tag_getTopTracks,
	'tag.getinfo'           => method_tag_getInfo,
	'track.addtags'         => method_track_addTags,
	'track.gettoptags'      => method_track_getTopTags,
	'track.gettags'         => method_track_getTags,
	'track.ban'             => method_track_ban,
	'track.love'            => method_track_love,
	'track.unlove'          => method_track_unlove,
	'track.unban'           => method_track_unban,
	'track.updatenowplaying' => method_track_updateNowPlaying,
	'track.scrobble'        => method_track_scrobble,
);

/**
 * user.gettopartists : Get the top artists for a user.
 *
 * ###Description
 * Get the top artists for a user, ordered by play count.
 *
 * ###Parameters
 * * **user** (required)		: Name of the user
 * * **limit** (optional)		: How many items to return. Defaults to 50.
 * * **page** (optional)		: The page to show. Defaults to 1.
 * * **period** (optional)		: The time period to get top artists for - overall|7day|1month|3month|6month|12month. Default is overall.
 * * **streamable** (optional)	: Only show streamable artists. Default is false.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage User
 * @api
 */
function method_user_getTopArtists() {
	if (!isset($_REQUEST['user'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$limit = get_with_default('limit', 50);
	$page = get_with_default('page', 1);
	$period = get_with_default('period', 'overall');
	$streamable = get_with_default('streamable', 0);
	$cache = 600;

	$xml = UserXML::getTopArtists($_REQUEST['user'], $limit, $streamable, $page, $period, $cache);

	respond($xml);
}

/**
 * user.getrecenttracks : Get recently played tracks by a user.
 *
 * ###Description
 * Get recently played tracks by a user, ordered by time.
 *
 * ###Parameters
 * * **user** (required)		: Name of the user.
 * * **limit** (optional)		: How many items to show. Defaults to 50.
 * * **page** (optional)		: The page to show. Defaults to 1.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage User
 * @api
 */
function method_user_getRecentTracks() {
	if (!isset($_REQUEST['user'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$limit = get_with_default('limit', 50);
	$page = get_with_default('page', 1);

	$xml = UserXML::getRecentTracks($_REQUEST['user'], $limit, $page);
	respond($xml);
}

/**
 * user.gettoptags : Get the top tags for a user.
 *
 * ###Description
 * Get the top tags for a user, ordered by tag count.
 *
 * ###Parameters
 * * **user** (required)		: Name of the user.
 * * **limit** (optional)		: How many items to show. Defaults to 50.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage User
 * @api
 */
function method_user_getTopTags() {
	if (!isset($_REQUEST['user'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$limit = get_with_default('limit', 50);

	$cache = 600;

	$xml = UserXML::getTopTags($_REQUEST['user'], $limit, $cache);
	respond($xml);
}

/**
 * user.getpersonaltags : Get a list of items tagged by a user with a specific tag.
 *
 * ###Description
 * Get a list of items (artists, albums or tracks)
 * that has been tagged by a user with a specific tag
 *
 * ###Parameters
 * * **user** (required)		: Name of the user.
 * * **tag** (required)			: Name of the tag.
 * * **taggingtype** (required)	: Type of tag - artist|album|track.
 * * **limit** (optional)		: How many items to show. Defaults to 10.
 * * **page** (optional)		: The page to show. Defaults to 1.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage User
 * @api
 */
function method_user_getPersonalTags() {
	if(!isset($_REQUEST['user']) || !isset($_REQUEST['tag']) || !isset($_REQUEST['taggingtype'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$limit = get_with_default('limit', 10);
	$page = get_with_default('page', 1);

	$streamable = False;
	$cache = 600;

	$xml = UserXML::getPersonalTags($_REQUEST['user'], $_REQUEST['tag'], $_REQUEST['taggingtype'], $limit, $page, $cache, $streamable);
	respond($xml);
}

/**
 * user.gettaginfo : Get info about a user's tag.
 *
 * ###Description
 * Get info about a user's tag.
 *
 * ###Parameters
 * * **user** (required)		: Name of the user.
 * * **tag** (required)			: Name of the tag.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage User
 * @api
 */
function method_user_getTagInfo() {
	if(!isset($_REQUEST['user']) || !isset($_REQUEST['tag'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$cache = 600;

	$xml = UserXML::getTagInfo($_REQUEST['user'], $_REQUEST['tag'], $cache);
	respond($xml);
}

/**
 * user.gettoptracks : Get the top tracks for a user.
 *
 * ###Description
 * Get the top tracks for a user, ordered by play count.
 *
 * ###Parameters
 * * **user** (required)		: Name of the user.
 * * **limit** (optional)		: How many items to show. Defaults to 10.
 * * **page** (optional)		: The page to show. Defaults to 1.
 * * **period** (optional)		: The time period to get top artists for - overall|7day|1month|3month|6month|12month. Default is overall.
 * * **streamable** (optional)	: Only show streamable tracks. Default is false.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage User
 * @api
 */
function method_user_getTopTracks() {
	if (!isset($_REQUEST['user'])) {
		report_failure(LFM_INVALID_PARAMS);
	}
	$limit = get_with_default('limit', 10);
	$streamable = get_with_default('streamable', False);
	$page = get_with_default('page', 1);
	$period = get_with_default('period', 'overall');
	$cache = 600;

	$xml = UserXML::getTopTracks($_REQUEST['user'], $limit, $streamable, $page, $period, $cache);
	respond($xml);
}

/** user.getinfo : Get information about a user
 *
 * ###Description
 * Get information (such as biography and playcount) about a user.
 *
 * ###Parameters
 * * **user** (required)		: Name of the user.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage User
 * @api
 */
function method_user_getInfo() {
	if (!isset($_REQUEST['user'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$xml = UserXML::getInfo($_REQUEST['user']);
	respond($xml);
}

/**
 * user.getlovedtracks : Get the loved tracks for a user.
 *
 * ###Description
 * Get the loved tracks for a user, ordered by time.
 *
 * ###Parameters
 * * **user** (required)		: Name of the user.
 * * **limit** (optional)		: How many items to show. Defaults to 50.
 * * **page** (optional)		: The page to show. Defaults to 1.
 * * **streamable** (optional)	: Only show streamable tracks. Default is false.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage User
 * @api
 */
function method_user_getLovedTracks() {
	if (!isset($_REQUEST['user'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$user = $_REQUEST['user'];

	$limit = get_with_default('limit', 50);
	$page = get_with_default('page', 1);
	$streamable = get_with_default('streamable', False);
	$cache = 600;

	$xml = UserXML::getLovedTracks($user, $limit, $page, $streamable, $cache);
	respond($xml);
}

/**
 * user.getbannedtracks : Get the banned tracks for a user.
 *
 * ###Description
 * Get the banned tracks for a user, ordered by time.
 *
 * ###Parameters
 * * **user** (required)		: Name of the user.
 * * **limit** (optional)		: How many items to show. Defaults to 50.
 * * **page** (optional)		: The page to show. Defaults to 1.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage User
 * @api
 */
function method_user_getBannedTracks() {
	if (!isset($_REQUEST['user'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$user = $_REQUEST['user'];
	$limit = get_with_default('limit', 50);
	$page = get_with_default('page', 1);

	$xml = UserXML::getBannedTracks($user, $limit, $page);
	respond($xml);
}

/**
 * user.getneighbours : Get the neighbours for a user.
 *
 * ###Description
 * Get the neighbours for a user, ordered by relevance.
 *
 * ###Parameters
 * * **user** (required)		: Name of the user.
 * * **limit** (optional)		: How many items to show. Defaults to 50.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage User
 * @api
 */
function method_user_getNeighbours() {
	if (!isset($_REQUEST['user'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$user = $_REQUEST['user'];
	$limit = get_with_default('limit', 50);

	$xml = UserXML::getNeighbours($user, $limit);
	respond($xml);
}

/**
 * artist.addtags : Add tags to an artist.
 *
 * ###Description
 * Add tags to an artist using a comma-separated list of tags.
 * 
 * ###Parameters
 * * **artist** (required)		: Name of the album's artist.
 * * **tags** (required)		: Comma-separated list of tags.
 * * **sk** (required)			: Session key.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 *
 * ###Additional info
 * **This method requires authentication**.
 *
 * **HTTP request method** : POST.
 * - - - 
 *
 * @package Webservice
 * @subpackage Artist
 * @api
 */
function method_artist_addTags() {
	if (!isset($_POST['artist']) || !isset($_POST['tags'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$userid = get_userid();
	$xml = TrackXML::addTags($userid, $_POST['artist'], '', '', $_POST['tags']);
	respond($xml);
}

/** artist.getinfo : Get information about an artist.
 *
 * ###Description
 * Get information about an artist.
 *
 * ###Parameters
 * * **artist** (required)		: Name of the artist.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage Artist
 * @api
 */
function method_artist_getInfo() {
	if (!isset($_REQUEST['artist'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$xml = ArtistXML::getInfo($_REQUEST['artist']);
	respond($xml);
}

/**
 * artist.gettoptracks : Get the top tracks for an aritst.
 *
 * ###Description
 * Get the top tracks for an artist, ordered by play count.
 *
 * ###Parameters
 * * **artist** (required)		: Name of the artist.
 * * **limit** (optional)		: How many items to show. Defaults to 50.
 * * **page** (optional)		: The page to show. Defaults to 1.
 * * **streamable** (optional)	: Only show streamable tracks. Default is false.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage Artist
 * @api
 */
function method_artist_getTopTracks() {
	if (!isset($_REQUEST['artist'])) {
		report_failure(LFM_INVALID_PARAMS);
	}
	$limit = get_with_default('limit', 50);
	$page = get_with_default('page', 1);
	$streamable = get_with_default('streamable', False);
	$cache = 600;

	$xml = ArtistXML::getTopTracks($_REQUEST['artist'], $limit, $streamable, $page, $cache);
	respond($xml);
}

/**
 * artist.gettoptags : Get the top tags for an artist.
 *
 * ###Description
 * Get the top tags used for an artist, ordered by tag count.
 *
 * ###Parameters
 * * **artist** (required)		: Name of the album's artist.
 * * **limit** (optional)		: How many items to show. Defaults to 50.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage Artist
 * @api
 */
function method_artist_getTopTags() {
	if (!isset($_REQUEST['artist'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$limit = get_with_default('limit', 50);

	$cache = 600;	

	$xml = ArtistXML::getTopTags($_REQUEST['artist'], $limit, $cache);
	respond($xml);
}

/**
 * artist.gettags : Get a user's top tags for an artist.
 *
 * ###Description
 * Get the top tags used for an artist, filtered by user name and ordered by tag count.
 *
 * ###Parameters
 * * **artist** (required)		: Name of the artist.
 * * **sk** (required)			: Session key.
 * * **limit** (optional)		: How many items to show. Defaults to 50.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 *
 * ###Additional info
 * **This method requires authentication**
 * - - -
 *
 * @package Webservice
 * @subpackage Artist
 * @api
 * @todo Only require sk if no user specified, see http://www.last.fm/api/show/artist.getTags.
 */
function method_artist_getTags() {
	if (!isset($_REQUEST['artist'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$limit = get_with_default('limit', 50);

	$userid = get_userid();
	$cache = 600;

	$xml = ArtistXML::getTags($_REQUEST['artist'], $userid, $limit, $cache);
	respond($xml);
}

/**
 * artist.getflattr : Get the Flattr id of an artist.
 *
 * ###Description
 * Get the Flattr id of an artist.
 *
 * ###Parameters
 * * **artist**					: Name of the artist.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage Artist
 * @api
 */
function method_artist_getFlattr() {
	if (!isset($_REQUEST['artist'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$xml = ArtistXML::getFlattr($_REQUEST['artist']);
	respond($xml);
}

/**
 * album.addtags : Add tags to an album.
 *
 * ###Description
 * Add tags to an album using a comma-separated list of tags.
 * 
 * ###Parameters
 * * **artist** (required)		: Name of the album's artist.
 * * **album** (required)		: Name of the album.
 * * **tags** (required)		: Comma-separated list of tags.
 * * **sk** (required)			: Session key.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 *
 * ###Additional info
 * **This method requires authentication**.
 *
 * **HTTP request method** : POST.
 * - - - 
 *
 * @package Webservice
 * @subpackage Album
 * @api
 */
function method_album_addTags() {
	if (!isset($_POST['artist']) || !isset($_POST['album']) || !isset($_POST['tags'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$userid = get_userid();
	$xml = TrackXML::addTags($userid, $_POST['artist'], $_POST['album'], '', $_POST['tags']);
	respond($xml);
}

/**
 * album.gettoptags : Get the top tags for an album.
 *
 * ###Description
 * Get the top tags used for an album, ordered by tag count.
 *
 * ###Parameters
 * * **artist** (required)		: Name of the album's artist.
 * * **album** (required)		: Name of the album.
 * * **limit** (optional)		: How many items to show. Defaults to 50.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage Album
 * @api
 */
function method_album_getTopTags() {
	if (!isset($_REQUEST['artist']) || !isset($_REQUEST['album'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$limit = get_with_default('limit', 50);

	$cache = 600;

	$xml = AlbumXML::getTopTags($_REQUEST['artist'], $_REQUEST['album'], $limit, $cache);
	respond($xml);
}

/**
 * album.gettags : Get a user's top tags for an album.
 *
 * ###Description
 * Get the top tags used for an album, filtered by user name and ordered by tag count.
 *
 * ###Parameters
 *
 * * **artist** (required)		: Name of the album's artist.
 * * **album** (required)		: Name of the album.
 * * **sk** (required)			: Session key.
 * * **limit** (optional)		: How many items to show. Defaults to 10.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 *
 * ###Additional info
 * **This method requires authentication**
 * - - -
 *
 * @package Webservice
 * @subpackage Album
 * @api
 * @todo Only require sk if no user specified, see http://www.last.fm/api/show/album.getTags.
 */
function method_album_getTags() {
	if (!isset($_REQUEST['artist']) || !isset($_REQUEST['album'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$limit = get_with_default('limit', 10);

	$userid = get_userid();
	$cache = 600;

	$xml = AlbumXML::getTags($_REQUEST['artist'], $_REQUEST['album'], $userid, $limit, $cache);
	respond($xml);
}

/**
 * auth.gettoken : Get a request token
 *
 * ###Description
 *
 * - - -
 * @todo Documentation
 * @package Webservice
 * @subpackage Auth
 * @api
 */
function method_auth_getToken() {
	global $adodb;

	$key = md5(time() . rand());

	try {
	$result = $adodb->Execute('INSERT INTO Auth (token, expires) VALUES ('
		. $adodb->qstr($key) . ', '
		. (int)(time() + 3600)
		. ')');
	} catch (Exception $e) {
		report_failure(LFM_SERVICE_OFFLINE);
	}

	$xml = simplexml_load_string('<lfm status="ok"><token>' . $key . '</token></lfm>');
	respond($xml);
}
/**
 * auth.getmobilesession : Create a webservice session
 *
 * ###Description
 * Create a webservice session and a session key required for authenticating with other webservice methods,
 * the formula for the authentication token required is:
 *
 *     md5(username+md5(password))
 *
 * ###Parameters
 * * **username** (required)	: Name of the user
 * * **authtoken** (required)	: Authentication token
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @todo parameter 'authtoken' is deprecated on last.fm's version, uses parameter 'password' with HTTPS and POST instead
 * @todo make XML response better (use xml_response)
 * @package Webservice
 * @subpackage Auth
 * @api
 */
function method_auth_getMobileSession() {
	global $adodb;

	$_REQUEST_lower = array_change_key_case($_REQUEST, CASE_LOWER);

	if (!isset($_REQUEST_lower['authtoken']) || !isset($_REQUEST['username'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	// Check for a token that is bound to a user
	try {
		$result = $adodb->GetRow('SELECT username, lower(username) AS lc_username, password FROM Users WHERE '
			. 'lower(username) = lower(' . $adodb->qstr($_REQUEST['username']) . ')');
	} catch (Exception $e) {
		report_failure(LFM_SERVICE_OFFLINE);
	}
	if (is_null($result)) {
		report_failure(LFM_INVALID_TOKEN);
	}

	$username = $result['username'];
	$lc_username = $result['lc_username'];
	$password = $result['password'];
	if (md5($lc_username . $password) != $_REQUEST_lower['authtoken']) {
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
	} catch (Exception $e) {
		report_failure(LFM_SERVICE_OFFLINE);
	}

	if ($_REQUEST['format'] == 'json') {
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

/**
 * auth.getsession : Create a webservice session.
 *
 * ###Description
 *
 *
 * ###Parameters
 * * **token** (required)		: Token
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 * @todo Documentation
 * @todo make XML response better (use xml_response)
 * @package Webservice
 * @subpackage Auth
 * @api
 */
function method_auth_getSession() {
	global $adodb;

	if (!isset($_REQUEST['token'])) {
		report_failure(LFM_INVALID_TOKEN);
	}

	// Check for a token that (1) is bound to a user, and (2) is not bound to a session
	try {
		$username = $adodb->GetOne('SELECT username FROM Auth WHERE '
			. 'token = ' . $adodb->qstr($_REQUEST['token']) . ' AND '
			. 'username IS NOT NULL AND sk IS NULL');
	} catch (Exception $e) {
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
			. 'token = ' . $adodb->qstr($_REQUEST['token']));
	} catch (Exception $e) {
		report_failure(LFM_SERVICE_OFFLINE);
	}

	if ($_REQUEST['format'] == 'json') {
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

/**
 * radio.tune : Tune in to a radio station
 *
 * ###Description
 * Tune in to a radio station
 *
 * ###Parameters
 * * **station** (required)		: Station URL
 * * **sk** (required)			: Session key
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 *
 * ###Additional info
 * **This method requires authentication**.
 *
 * **HTTP request method** : POST.
 * - - - 
 *
 * @todo make XML response better (use xml_response)
 * @package Webservice
 * @subpackage Radio
 * @api
 */
function method_radio_tune() {
	global $adodb;

	if (!isset($_POST['station'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	if (!isset($_POST['sk'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	try {
	$username = $adodb->GetOne('SELECT username FROM Auth WHERE '
		. 'sk = ' . $adodb->qstr($_POST['sk']) . ' AND '
		. 'username IS NOT NULL');
	} catch (Exception $e) {
		report_failure(LFM_SERVICE_OFFLINE);
	}
	if (!$username) {
		report_failure(LFM_INVALID_TOKEN);
	}

	Server::getRadioSession($_POST['station'], $username, $_POST['sk']);
	$stationtype = 'globaltag';
	$stationname = radio_title_from_url($_POST['station']);
	$stationurl = 'http://libre.fm';

	if ($_REQUEST['format'] == 'json') {
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

/**
 * radio.getplaylist : Get playlist from a tuned station
 *
 * ###Description
 * Get playlist from a tuned station
 *
 * ###Parameters
 * * **sk** (required)			: Session key
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 *
 * ###Additional info
 * **This method requires authentication**.
 * - - -
 *
 * @todo Any errors should be in whatever format specified, currently plaintext
 * @package Webservice
 * @subpackage Radio
 * @api
 */
function method_radio_getPlaylist() {
	global $adodb;

	if (!isset($_REQUEST['sk'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	//we return a JSPF (http://wiki.xiph.org/JSPF_Draft) playlist if format=json
	//any errors will still be plaintext (make_playlist can return BADSESSION or FAILED)
	if ($_REQUEST['format'] == 'json') {
		list($title, $res) = make_playlist($_REQUEST['sk'], false, 'json');

		$tracks = array();
		foreach($res as &$row) {
			$track = array(
				'location' => $row['location'],
				'title' => $row['title'],
				'identifier' => $row['id'],
				'album' => $row['album'],
				'creator' => $row['creator'],
				'duration' => $row['duration'],
				'image' => $row['image'],
				'extension' => array(
					'http://alpha.libre.fm/' => array(
						'trackauth' => null,
						'albumid' => null,
						'artistid' => null,
						'recording' => null,
						'artistpage' => $row['artisturl'],
						'albumpage' => $row['albumurl'],
						'trackpage' => $row['trackurl'],
						'buyTrackURL' => null,
						'buyAlbumURL' => null,
						'freeTrackURL' => $row['downloadurl']
					)
				)
			);
			$tracks[] = $track;
		}

		$playlist = array(
			'playlist' => array(
				'title' => $title,
				'creator' => 'libre.fm',
				'date' => date('c'),
				'link' => array('http://www.last.fm/expiry' => 3600),
				'track' => $tracks)
			);

		header('Content-Type: text/javascript');
		print(json_encode($playlist));
	}else{
		//we return XSPF playlists by default
		make_playlist($_REQUEST['sk']);
	}
}

/**
 * track.addtags : Add tags to a track.
 *
 * ###Description
 * Add tags to a track using a comma-separated list of tags.
 * 
 * ###Parameters
 * * **artist** (required)		: Name of the tracks's artist.
 * * **track** (required)		: Name of the tracks.
 * * **tags** (required)		: Comma-separated list of tags.
 * * **sk** (required)			: Session key.
 * * **album** (optional)		: Name of the tracks's album.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 *
 * ###Additional info
 * **This method requires authentication**.
 *
 * **HTTP request method** : POST.
 * - - - 
 *
 * @package Webservice
 * @subpackage Track
 * @api
 */
function method_track_addTags() {
	if (!isset($_POST['artist']) || !isset($_POST['track']) || !isset($_POST['tags'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$userid = get_userid();
	$xml = TrackXML::addTags($userid, $_POST['artist'], $_POST['album'], $_POST['track'], $_POST['tags']);
	respond($xml);
}

/**
 * track.gettoptags : Get the top tags for a track.
 *
 * ###Description
 * Get the top tags used for a track, ordered by tag count.
 *
 * ###Parameters
 * * **artist** (required)		: Name of the track's artist.
 * * **track** (required)		: Name of the track.
 * * **limit** (optional)		: How many items to show. Defaults to 50.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage Track
 * @api
 */
function method_track_getTopTags() {
	if (!isset($_REQUEST['artist']) || !isset($_REQUEST['track'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$limit = get_with_default('limit', 50);

	$cache = 600;

	$xml = TrackXML::getTopTags($_REQUEST['artist'], $_REQUEST['track'], $limit, $cache);
	respond($xml);
}

/**
 * track.gettags : Get a user's top tags for a track.
 *
 * ###Description
 * Get the top tags used for a track, filtered by user name and ordered by tag count.
 *
 * ###Parameters
 *
 * * **artist** (required)		: Name of the track's artist.
 * * **track** (required)		: Name of the track.
 * * **sk** (required)			: Session key.
 * * **limit** (optional)		: How many items to show. Defaults to 50.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 *
 * ###Additional info
 * **This method requires authentication**
 * - - -
 *
 * @package Webservice
 * @subpackage Track
 * @api
 * @todo Only require sk if no user specified, see http://www.last.fm/api/show/track.getTags.
 */
function method_track_getTags() {
	if (!isset($_REQUEST['artist']) || !isset($_REQUEST['track'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$limit = get_with_default('limit', 50);

	$cache = 600;
	
	$userid = get_userid();
	$xml = TrackXML::getTags($_REQUEST['artist'], $_REQUEST['track'], $userid, $limit, $cache);
	respond($xml);
}

/**
 * track.ban : Add a track to the user's banned tracks list.
 *
 * ###Description
 * Add a track to the user's banned tracks list.
 * 
 * ###Parameters
 * * **artist** (required)		: Name of the tracks's artist.
 * * **track** (required)		: Name of the track.
 * * **sk** (required)			: Session key.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 *
 * ###Additional info
 * **This method requires authentication**.
 *
 * **HTTP request method** : POST.
 * - - - 
 *
 * @package Webservice
 * @subpackage Track
 * @api
 */
function method_track_ban() {
	if (!isset($_POST['artist']) || !isset($_POST['track'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$userid = get_userid();
	$xml = TrackXML::ban($_POST['artist'], $_POST['track'], $userid);
	respond($xml);
}

/**
 * track.love : Add a track to the user's loved tracks list.
 *
 * ###Description
 * Add a track to the user's loved tracks list.
 * 
 * ###Parameters
 * * **artist** (required)		: Name of the tracks's artist.
 * * **track** (required)		: Name of the track.
 * * **sk** (required)			: Session key.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 *
 * ###Additional info
 * **This method requires authentication**.
 *
 * **HTTP request method** : POST.
 * - - - 
 *
 * @package Webservice
 * @subpackage Track
 * @api
 */
function method_track_love() {
	if (!isset($_POST['artist']) || !isset($_POST['track'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$userid = get_userid();
	$xml = TrackXML::love($_POST['artist'], $_POST['track'], $userid);
	respond($xml);
}

/**
 * track.unlove : Remove a track from the user's loved tracks list.
 *
 * ###Description
 * Remove a track from the user's loved tracks list.
 * 
 * ###Parameters
 * * **artist** (required)		: Name of the tracks's artist.
 * * **track** (required)		: Name of the track.
 * * **sk** (required)			: Session key.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 *
 * ###Additional info
 * **This method requires authentication**.
 *
 * **HTTP request method** : POST.
 * - - - 
 *
 * @package Webservice
 * @subpackage Track
 * @api
 */
function method_track_unlove() {
	if (!isset($_POST['artist']) || !isset($_POST['track'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$userid = get_userid();
	$xml = TrackXML::unlove($_POST['artist'], $_POST['track'], $userid);
	respond($xml);
}

/**
 * track.unban : Remove a track from the user's banned tracks list.
 *
 * ###Description
 * Remove a track from the user's banned tracks list.
 * 
 * ###Parameters
 * * **artist** (required)		: Name of the tracks's artist.
 * * **track** (required)		: Name of the track.
 * * **sk** (required)			: Session key.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 *
 * ###Additional info
 * **This method requires authentication**.
 *
 * **HTTP request method** : POST.
 * - - - 
 *
 * @package Webservice
 * @subpackage Track
 * @api
 */
function method_track_unban() {
	if (!isset($_POST['artist']) || !isset($_POST['track'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$userid = get_userid();
	$xml = TrackXML::unban($_POST['artist'], $_POST['track'], $userid);
	respond($xml);
}

/**
 * track.updatenowplaying : Submits the user's currently playing track.
 *
 * ###Description
 * Submits the user's currently playing track.
 * 
 * ###Parameters
 * * **artist** (required)      : Artist name.
 * * **track** (required)       : Track name.
 * * **sk** (required)          : Session key.
 * * **album** (optional)		: Album name.
 * * **tracknumber (optional)   : Track's number on the album.
 * * **context** (optional)     : TODO
 * * **mbid** (optional)        : Track's musicbrainz ID.
 * * **duration** (optional)    : Length of the track in seconds.
 * * **albumartist (optional)   : Album's artist.
 * * **api_key (optional)       : Client API key.
 * * **format** (optional)      : Format of response, **xml** or **json**. Default is xml.
 *
 * ###Additional info
 * **This method requires authentication**.
 *
 * **HTTP request method** : POST.
 * - - - 
 *
 * @todo context parameter not used
 * @todo tracknumber parameter not used
 * @todo albumartist parameter not used
 * @package Webservice
 * @subpackage Track
 * @api
 */
function method_track_updateNowPlaying() {
	if (!isset($_POST['artist']) || !isset($_POST['track'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$_POST_lower = array_change_key_case($_POST, CASE_LOWER);

	$userid = get_userid();
	$xml = TrackXML::updateNowPlaying($userid,
		$_POST['artist'],
		$_POST['track'],
		$_POST['album'],
		$_POST_lower['tracknumber'],
		$_POST['context'],
		$_POST['mbid'],
		$_POST['duration'],
		$_POST_lower['albumartist'],
		$_POST['api_key']
	);

	respond($xml);
}

/**
 * track.scrobble : Submits a track for scrobbling.
 *
 * ###Description
 *
 * Submits a track or a batch of tracks for scrobbling.
 * 
 * ###Parameters
 * * **artist[i]** (required)       : Artist name.
 * * **track[i]** (required)        : Track name.
 * * **sk** (required)              : Session key.
 * * **timestamp[i]** (required)    : The time the track started playing (in UNIX time).
 * * **album[i]** (optional)        : Album name.
 * * **context[i]** (optional)      : TODO
 * * **streamid[i]** (optional)     : TODO
 * * **chosenbyuser[i]** (optional) : TODO
 * * **tracknumber[i]** (optional)  : Track's number on album.
 * * **mbid[i]** (optional)         : Track's Musicbrainz ID.
 * * **albumartist[i]** (optional)  : Album's artist.
 * * **duration[i]** (optional)     : Length of the track in seconds.
 * * **api_key (optional)           : Client API key.
 * * **format** (optional)          : Format of response, **xml** or **json**. Default is xml.
 *
 * ###Additional info
 * **This method requires authentication**.
 *
 * **HTTP request method** : POST.
 * - - - 
 *
 * @todo context parameter not used
 * @todo streamid parameter not used
 * @todo chosenbyuser parameter not used
 * @todo tracknumber parameter not used
 * @todo albumartist parameter not used
 * @package Webservice
 * @subpackage Track
 * @api
 */
function method_track_scrobble() {
	if (!isset($_POST['artist']) || !isset($_POST['track']) || !isset($_POST['timestamp'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$_POST_lower = array_change_key_case($_POST, CASE_LOWER);

	$userid = get_userid();
	$xml = TrackXML::scrobble($userid,
		$_POST['artist'],
		$_POST['track'],
		$_POST['timestamp'],
		$_POST['album'],
		$_POST['context'],
		$_POST_lower['streamid'],
		$_POST_lower['chosenbyuser'],
		$_POST_lower['tracknumber'],
		$_POST['mbid'],
		$_POST_lower['albumartist'],
		$_POST['duration'],
		$_POST['api_key']
	);
	respond($xml);
}

/**
 * tag.gettoptags : Get the top tags.
 *
 * ###Description
 * Get the top tags used, ordered by tag count.
 *
 * ###Parameters
 * * **limit** (optional)		: How many items to show. Defaults to 50.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage Tag
 * @api
 */
function method_tag_getTopTags() {
	$limit = get_with_default('limit', 50);

	$cache = 600;

	$xml = TagXML::getTopTags($limit, $cache);
	respond($xml);
}

/**
 * tag.gettopartists : Get the top artists tagged by a tag.
 *
 * ###Description
 * Get the top artists tagged by a specific tag, ordered by tag count.
 *
 * ###Parameters
 * * **tag** (required)			: Name of the tag.
 * * **limit** (optional)		: How many items to show. Defaults to 50.
 * * **page** (optional)		: The page to show. Defaults to 1.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage Tag
 * @api
 */
function method_tag_getTopArtists() {
	if (!isset($_REQUEST['tag'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$limit = get_with_default('limit', 50);
	$page = get_with_default('page', 1);

	$streamable = True;
	$cache = 600;

	$xml = TagXML::getTopArtists($_REQUEST['tag'], $limit, $page, $streamable, $cache);
	respond($xml);
}

/**
 * tag.gettopalbums : Get the top albums tagged by a tag.
 *
 * ###Description
 * Get the top albums tagged by a specific tag, ordered by tag count.
 *
 * ###Parameters
 * * **tag** (required)			: Name of the tag.
 * * **limit** (optional)		: How many items to show. Defaults to 50.
 * * **page** (optional)		: The page to show. Defaults to 1.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage Tag
 * @api
 */
function method_tag_getTopAlbums() {
	if (!isset($_REQUEST['tag'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$limit = get_with_default('limit', 50);
	$page = get_with_default('page', 1);

	$streamable = True;
	$cache = 600;

	$xml = TagXML::getTopAlbums($_REQUEST['tag'], $limit, $page, $streamable, $cache);
	respond($xml);
}

/**
 * tag.gettoptracks : Get the top tracks tagged by a tag.
 *
 * ###Description
 * Get the top tracks tagged by a specific tag, ordered by tag count.
 *
 * ###Parameters
 * * **tag** (required)			: Name of the tag.
 * * **limit** (optional)		: How many items to show. Defaults to 50.
 * * **page** (optional)		: The page to show. Defaults to 1.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage Tag
 * @api
 */
function method_tag_getTopTracks() {
	if (!isset($_REQUEST['tag'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$limit = get_with_default('limit', 50);
	$page = get_with_default('page', 1);

	$streamable = True;
	$cache = 600;

	$xml = TagXML::getTopTracks($_REQUEST['tag'], $limit, $page, $streamable, $cache);
	respond($xml);
}

/**
 * tag.getinfo : Get info about a tag.
 *
 * ###Description
 * Get info about a tag.
 *
 * ###Parameters
 * * **tag** (required)			: Name of the tag.
 * * **format** (optional)		: Format of response, **xml** or **json**. Default is xml.
 * - - -
 *
 * @package Webservice
 * @subpackage Tag
 * @api
 */
function method_tag_getInfo() {
	if (!isset($_REQUEST['tag'])) {
		report_failure(LFM_INVALID_PARAMS);
	}

	$cache = 600;

	$xml = TagXML::getInfo($_REQUEST['tag'], $cache);
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

	if ($_REQUEST['format'] == 'json') {
		$json_data = array('error' => $code, 'message' => $error_text[$code]);
		json_response(json_encode($json_data));
	} else {
		xml_response(XML::error('failed', $code, $error_text[$code]));
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
	if ($_REQUEST['callback']) {
		print($_REQUEST['callback'] . '(' . $data . ');');
	} else {
		print($data);
	}
}

function get_with_default($param, $default) {
	if (isset($_REQUEST[$param])) {
		return $_REQUEST[$param];
	} else {
		return $default;
	}
}

$_REQUEST['method'] = strtolower($_REQUEST['method']);
if (!isset($_REQUEST['method']) || !isset($method_map[$_REQUEST['method']])) {
	report_failure(LFM_INVALID_METHOD);
}

$method = $method_map[$_REQUEST['method']];
$method();
