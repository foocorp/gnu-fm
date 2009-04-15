<?php

function identifierScrobbleEvent ($username, $artist, $track, $time, $mbid=NULL, $ambid=NULL)
{
	global $base_url;
	$microhash = substr(md5($artist . '//' . $track), 0, 4);
	return $base_url . sprintf('/user/%s#%s.%s', urlencode($username), urlencode($time), urlencode($microhash));
}

function identifierTrack ($username, $artist, $track, $time, $mbid=NULL, $ambid=NULL)
{
	if (!empty($mbid))
	{
		return sprintf('http://dbtune.org/musicbrainz/resource/track/%s', strtolower($mbid));
	}
	else
	{
		return identifierScrobbleEvent($username, $artist, $track, $time, $mbid, $ambid) . '.track';
	}
}

function identifierArtist ($username, $artist, $track, $time, $mbid=NULL, $ambid=NULL)
{
	# Eventually look up MBIDs from Artists table?
	if (!empty($ambid))
	{
		return sprintf('http://dbtune.org/musicbrainz/resource/artist/%s', strtolower($ambid));
	}
	else
	{
		return identifierScrobbleEvent($username, $artist, $track, $time, $mbid, $ambid) . '.artist';
	}
}

