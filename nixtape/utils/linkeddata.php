<?php

function identifierScrobbleEvent ($username, $artist, $track, $album, $time, $mbid=NULL, $ambid=NULL, $lmbid=NULL)
{
	global $base_url;

	if (!($username && $artist && $track && $time))
		return null;

	$microhash = substr(md5($artist . '//' . $track), 0, 4);
	return $base_url . sprintf('/user/%s#%s.%s', rawurlencode($username), rawurlencode($time), rawurlencode($microhash));
}

function identifierTrack ($username, $artist, $track, $album, $time, $mbid=NULL, $ambid=NULL, $lmbid=NULL)
{
	if (!empty($mbid))
	{
		return sprintf('http://dbtune.org/musicbrainz/resource/track/%s', strtolower($mbid));
	}
	else
	{
		return identifierScrobbleEvent($username, $artist, $track, $album, $time, $mbid, $ambid, $lmbid) . '.track';
	}
}

function identifierArtist ($username, $artist, $track, $album, $time, $mbid=NULL, $ambid=NULL, $lmbid=NULL)
{
	# Eventually look up MBIDs from Artists table?
	if (!empty($ambid))
	{
		return sprintf('http://dbtune.org/musicbrainz/resource/artist/%s', strtolower($ambid));
	}

	$u = identifierScrobbleEvent($username, $artist, $track, $album, $time, $mbid, $ambid, $lmbid) . '.artist';
	if ($u != '.artist') return $u;

	global $base_url;
	return $base_url . sprintf('/artist/%s#artist', rawurlencode($artist));
}

function identifierAlbum ($username, $artist, $track, $album, $time, $mbid=NULL, $ambid=NULL, $lmbid=NULL)
{
	# Eventually look up MBIDs from Artists table?
	if (!empty($lmbid))
	{
		return sprintf('http://dbtune.org/musicbrainz/resource/record/%s', strtolower($lmbid));
	}
	
	$u = identifierScrobbleEvent($username, $artist, $track, $album, $time, $mbid, $ambid, $lmbid) . '.album';
	if ($u != '.album') return $u;

	global $base_url;
	return $base_url . sprintf('/artist/%s/album/%s#this', rawurlencode($artist), rawurlencode($album));
}

