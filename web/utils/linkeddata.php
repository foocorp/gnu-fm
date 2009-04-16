<?php

function identifierScrobbleEvent ($username, $artist, $track, $time, $mbid=NULL, $ambid=NULL, $lmbid=NULL)
{
	global $base_url;
	$microhash = substr(md5($artist . '//' . $track), 0, 4);
	return $base_url . sprintf('/user/%s#%s.%s', urlencode($username), urlencode($time), urlencode($microhash));
}

function identifierTrack ($username, $artist, $track, $time, $mbid=NULL, $ambid=NULL, $lmbid=NULL)
{
	if (!empty($mbid))
	{
		return sprintf('http://dbtune.org/musicbrainz/resource/track/%s', strtolower($mbid));
	}
	else
	{
		return identifierScrobbleEvent($username, $artist, $track, $time, $mbid, $ambid, $lmbid) . '.track';
	}
}

function identifierArtist ($username, $artist, $track, $time, $mbid=NULL, $ambid=NULL, $lmbid=NULL)
{
	# Eventually look up MBIDs from Artists table?
	if (!empty($ambid))
	{
		return sprintf('http://dbtune.org/musicbrainz/resource/artist/%s', strtolower($ambid));
	}
	else
	{
		return identifierScrobbleEvent($username, $artist, $track, $time, $mbid, $ambid, $lmbid) . '.artist';
	}
}

function identifierAlbum ($username, $artist, $track, $time, $mbid=NULL, $ambid=NULL, $lmbid=NULL)
{
	# Eventually look up MBIDs from Artists table?
	if (!empty($lmbid))
	{
		return sprintf('http://dbtune.org/musicbrainz/resource/record/%s', strtolower($lmbid));
	}
	else
	{
		return identifierScrobbleEvent($username, $artist, $track, $time, $mbid, $ambid, $lmbid) . '.album';
	}
}

