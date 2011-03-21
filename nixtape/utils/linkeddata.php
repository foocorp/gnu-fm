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
 * Generates linked data URIs for various concepts including scrobble events,
 * artists, albums and tracks. General technique is to create a dbtune.org
 * URI if we have the appropriate MusicBrainz ID. Otherwise, create a URI
 * based on the scrobble time if known. Lastly, create one based on the libre.fm
 * artist page.
 */

require_once($install_path . 'data/Server.php');

function identifierScrobbleEvent ($username, $artist, $track, $album, $time, $mbid=NULL, $ambid=NULL, $lmbid=NULL)
{
	if (!($username && $artist && $track && $time))
		return null;

	$microhash = substr(md5($artist . '//' . $track), 0, 4);
	return sprintf('%s#%s.%s', Server::getUserURL($username), rawurlencode($time), rawurlencode($microhash));
}

function identifierArtist ($username, $artist, $track, $album, $time, $mbid=NULL, $ambid=NULL, $lmbid=NULL)
{
	if (!empty($ambid))
	{
		return sprintf('http://dbtune.org/musicbrainz/resource/artist/%s', strtolower($ambid));
	}

	$u = identifierScrobbleEvent($username, $artist, $track, $album, $time, $mbid, $ambid, $lmbid) . '.artist';
	if ($u != '.artist') return $u;

	return sprintf('%s#artist', Server::getArtistURL($artist));
}

function identifierAlbum ($username, $artist, $track, $album, $time, $mbid=NULL, $ambid=NULL, $lmbid=NULL)
{
	if (!empty($lmbid))
	{
		return sprintf('http://dbtune.org/musicbrainz/resource/record/%s', strtolower($lmbid));
	}

	$u = identifierScrobbleEvent($username, $artist, $track, $album, $time, $mbid, $ambid, $lmbid) . '.album';
	if ($u != '.album') return $u;

	return sprintf('%s#album', Server::getAlbumURL($artist, $album));
}

function identifierTrack ($username, $artist, $track, $album, $time, $mbid=NULL, $ambid=NULL, $lmbid=NULL)
{
	if (!empty($mbid))
	{
		return sprintf('http://dbtune.org/musicbrainz/resource/track/%s', strtolower($mbid));
	}

	$u = identifierScrobbleEvent($username, $artist, $track, $album, $time, $mbid, $ambid, $lmbid) . '.track';
	if ($u != '.track') return $u;

	return sprintf('%s#track', Server::getTrackURL($artist, $album, $track));
}
