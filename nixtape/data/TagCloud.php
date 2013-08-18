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

require_once($install_path . '/data/Server.php');
require_once($install_path . '/data/Artist.php');
require_once($install_path . '/data/Album.php');
require_once($install_path . '/data/Track.php');
require_once($install_path . '/data/Tag.php');

/**
 * Provides access to functions for generating tag clouds
 */
class TagCloud {

	/**
	 * Generate array for use when building tag clouds
	 *
	 * The *size* value in the resulting array corresponds to a CSS font-size in the range of xx-small to xx-large.
	 *
	 * Examples
	 * --------
	 * Get an array with a track's top 10 tags: ((name, count, size, pageurl) .. )
	 *
	 * `$trackTags = generateTagCloud('tags', 'tag', 10, 'track', array($trackname, $artistname));`
	 *
	 * Get an array with a user's top 10 streamable artists: ((name, count, size, pageurl) .. )
	 *
	 * `$userArtists = generateTagCloud('artists', 'artist', 10, 'userid', $userid)`
	 *
	 * Get an array with the top 10 of all loved tracks: ((name, count, size, artist_name, pageurl) .. )
	 *
	 * `$lovedTracks = generateTagCloud('loved', 'track', 10, null, null, False);`
	 *
	 * @param string $set The set to return data from (artists|tracks|tags|loved)
	 * @param string $item The item to count in the set (artist|track|tag)
	 * @param int $limit Max amount of items to return
	 * @param string $constraint_type The type of constraint to filter by (artist|track|tag|userid|null)
	 * @param string|array $constraint The constraint value to filter by
	 * @param bool $streamable Only return streamable artists/albums/tracks
	 * @param int $cache int The caching period in seconds
	 * @return array Items ((name, count, size, pageurl) .. )
	 */
	static function generateTagCloud($set, $item, $limit=40, $constraint_type=null, $constraint=null, $streamable = True, $cache=7200) {

		$sizes = array('xx-large', 'x-large', 'large', 'medium', 'small', 'x-small', 'xx-small');

		if ($set == 'artists') {
			if ($item == 'artist') {
				if ($constraint_type == 'userid') {
					$res = Server::getTopArtists($limit, 0, $streamable, null, null, $constraint, $cache);
				} else if (is_null($constraint_type)) {
					$res = Server::getTopArtists($limit, 0, $streamable, null, null, null, $cache);
				}
			} else {
				throw new Exception("Not a valid tagcloud item: " . $item);
			}

		} else if ($set == 'loved') {
			if ($item == 'artist') {
				if ($constraint_type == 'userid') {
					$res = Server::getLovedArtists($limit, 0, $streamable, $constraint, $cache);
				} else if (is_null($constraint_type)) {
					$res = Server::getLovedArtists($limit, 0, $streamable, null, $cache);
				}
			} else if ($item == 'track') {
				if ($constraint_type == 'userid') {
					$res = Server::getLovedTracks($limit, 0, $streamable, null, $constraint, $cache);
				} else if ($constraint_type == 'artist') {
					$res = Server::getLovedTracks($limit, 0, $streamable, $constraint, null, $cache);
				} else if (is_null($constraint_type)) {
					$res = Server::getLovedTracks($limit, 0, $streamable, null, null, $cache);
				}
			} else {
				throw new Exception("Not a valid tagcloud item: " . $item);
			}

		} else if ($set == 'tracks') {
			if ($item == 'track') {
				if ($constraint_type == 'userid') {
					$res = Server::getTopTracks($limit, 0, $streamable, null, null, null, $constraint, $cache);
				} else if ($constraint_type == 'artist') {
					$res = Server::getTopTracks($limit, 0, $streamable, null, null, $constraint, null, $cache);
				} else if (is_null($constraint_type)) {
					$res = Server::getTopTracks($limit, 0, $streamable, null, null, null, null, $cache);
				}
			} else {
				throw new Exception("Not a valid tagcloud item: " . $item);
			}

		} else if ($set == 'tags') {
			if ($item == 'tag') {
				if ($constraint_type == 'artist') {
					$artist = new Artist($constraint);
					$res = $artist->getTopTags($limit, 0, $cache);
				} else if ($constraint_type == 'album') {
					// $constraint needs to be an array of (album_name, artist_name)
					$album = new Album($constraint[0], $constraint[1]);
					$res = $album->getTopTags($limit, 0, $cache);
				} else if ($constraint_type == 'track') {
					// $constraint needs to be an array of (track_name, artist_name)
					$track = new Track($constraint[0], $constraint[1]);
					$res = $track->getTopTags($limit, 0, $cache);
				}
			} else if ($item == 'artist') {
				if ($constraint_type == 'tag') {
					$res = Tag::getTopArtists($constraint, $limit, 0, $streamable, $cache);
				}
			} else if ($item == 'track') {
				if ($constraint_type == 'tag') {
					$res = Tag::getTopTracks($constraint, $limit, 0, $streamable, $cache);
				}
			} else {
				throw new Exception("Not a valid tagcloud item: " . $item);
			}

		} else {
			throw new Exception("Not a valid tagcloud set: " . $set);
		}

		if(!$res) {
			return array();
		}
		
		$tagcloud = array();
		$i=0;
		foreach ($res as &$row) {
			$tagcloud[$i]['name'] = $row[$item];
			$tagcloud[$i]['count'] = $row['freq'];
			$tagcloud[$i]['size'] = $sizes[(int) ($i/(count($res)/7))];
			if ($item == 'artist') {
				$tagcloud[$i]['pageurl'] = Server::getArtistURL($row[$item]);
			} else if ($item == 'tag') {
				$tagcloud[$i]['pageurl'] = Server::getTagURL($row[$item]);
			} else if ($item == 'track') {
				$tagcloud[$i]['artist_name'] = $row['artist'];
				$tagcloud[$i]['pageurl'] = Server::getTrackURL($row['artist'], null, $row[$item]);
			}
			$i++;
		}

		sort($tagcloud);
		return $tagcloud;
	}
}
