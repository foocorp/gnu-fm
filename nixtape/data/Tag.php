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

require_once($install_path . '/database.php');
require_once($install_path . '/data/sanitize.php');

/**
 * Provides access to functions used for retrieving data involving tags.
 */
class Tag {

	/**
	 * Get various data involving tags, used by other tag related functions.
	 *
	 * This function is not meant to be used directly,
	 * it is easier to use higher-level functions such as Tag::getTopTags()
	 * to get the data needed.
	 *
	 * @param int $cache Caching period of sql query in seconds
	 * @param int $limit The number of results to return
	 * @param int $offset The position of the first result to return
	 * @param int $user The userid to return results for
	 * @param string $artist The artist to return results for
	 * @param string $album The album to return results for
	 * @param string $track The track to return results for
	 * @param string $tag The tag to return results for
	 * @param string $taggingtype The type of tag to return results for (artist|album|track)
	 * @param boolean $streamable Only return streamable results if True
	 * @return array An array of results
	 */
	function _getTagData($cache=600, $limit=null, $offset=null, $user=null, $artist=null, $album=null,
	                               $track=null, $tag=null, $taggingtype=null, $streamable=False) {
		global $adodb;

		$whereuser = ' WHERE userid=' . (int)$user;
		$anduser = ' AND userid=' . (int)$user;
		$whereartist = ' WHERE LOWER(artist) = LOWER(' . $adodb->qstr((string)$artist) . ')';
		$wherestream = '';
		$andartist = ' AND LOWER(artist) = LOWER(' . $adodb->qstr((string)$artist) . ')';
		$wheretag = ' WHERE LOWER(tag) = LOWER(' . $adodb->qstr((string)$tag) . ')';
		$andtag = ' AND LOWER(tag) = LOWER(' . $adodb->qstr((string)$tag) . ')';
		$andalbum = ' AND LOWER(album) = LOWER(' . $adodb->qstr((string)$album) . ')';
		$andtrack = ' AND LOWER(track) = LOWER(' . $adodb->qstr((string)$track) . ')';
		$hasartist = ' AND artist IS NOT NULL AND artist <> \'\'';
		$hasalbum = ' AND album IS NOT NULL AND album <> \'\'';
		$hastrack = ' AND track IS NOT NULL AND track <> \'\'';
		$orderfreq = ' ORDER BY freq DESC';

		if($streamable) {
			$wherestream = ' INNER JOIN Artist ON Tags.artist=Artist.name WHERE Artist.streamable=1';
			$whereuser = $anduser;
			$whereartist = $andartist;
			$wheretag = $andtag;
		}

		if($user) {
			if($artist) {
				if($album) {
					//Album->getTags	
					$query = 'SELECT tag, COUNT(tag) AS freq FROM Tags' . $whereuser . $andartist . $andalbum . ' GROUP BY tag';
				} elseif($track) {
					//Track->getTags	
					$query = 'SELECT tag, COUNT(tag) AS freq FROM Tags' . $whereuser . $andartist . $andtrack . ' GROUP BY tag';
				} else {
					//Artist->getTags
					$query = 'SELECT tag, COUNT(tag) AS freq FROM Tags' . $whereuser . $andartist . ' GROUP BY tag';
				}
			} else {
				if($tag) {
					if(strtolower($taggingtype)=='artist') {
						//User->getPersonalTags (artist)
						$query = 'SELECT artist FROM Tags' . $wherestream . $whereuser . $andtag . $hasartist;
					} elseif(strtolower($taggingtype)=='album') {
						//User->getPersonalTags (album)
						$query = 'SELECT artist, album FROM Tags' . $wherestream . $whereuser . $andtag . $hasalbum;
					} elseif(strtolower($taggingtype)=='track') {
						//User->getPersonalTags (track)
						$query = 'SELECT artist, track FROM Tags' . $wherestream . $whereuser . $andtag . $hastrack;
					} elseif($taggingtype) {
						//Invalid taggingtype
						throw new Exception("Invalid taggingtype: " . $taggingtype);
					} else {
						//User->getTagInfo
						$query = 'SELECT tag, COUNT(tag) AS freq FROM Tags' . $whereuser . $andtag . ' GROUP BY tag';
					}
				} else {
					//User->getTopTags
					$query = 'SELECT tag, COUNT(tag) AS freq FROM Tags' . $whereuser . ' GROUP BY tag' . $orderfreq;
				}
			}
		} else {
			if($artist) {
				if($album) {
					//Album->getTopTags
					$query = 'SELECT tag, COUNT(tag) AS freq FROM Tags' .$whereartist . $andalbum . ' GROUP BY tag' . $orderfreq;
				} elseif($track) {
					//Track->getTopTags
					$query = 'SELECT tag, COUNT(tag) AS freq FROM Tags' . $whereartist . $andtrack . ' GROUP BY tag' . $orderfreq;
				} else {
					//Artist->getTopTags
					$query = 'SELECT tag, COUNT(tag) AS freq FROM Tags' . $whereartist . ' GROUP BY tag' . $orderfreq;
				}
			} elseif($tag) {
				if(strtolower($taggingtype)=='artist') {
					//Tag::getTopArtists
					$query = 'SELECT artist, COUNT(artist) AS freq FROM Tags' . $wherestream . $wheretag . $hasartist . ' GROUP BY artist' . $orderfreq;
				} elseif(strtolower($taggingtype)=='album') {
					//Tag::getTopAlbums
					$query = 'SELECT artist, album, COUNT(album) AS freq FROM Tags' . $wherestream . $wheretag . $hasalbum . ' GROUP BY album, artist' . $orderfreq;
				} elseif(strtolower($taggingtype)=='track') {
					//Tag::getTopTracks
					$query = 'SELECT artist, track, COUNT(track) AS freq FROM Tags' . $wherestream . $wheretag . $hastrack . ' GROUP BY track, artist' . $orderfreq;
				} elseif($taggingtype) {
						//Invalid taggingtype
						throw new Exception("Invalid taggingtype: " . $taggingtype);
				} else {
					//Tag::getInfo
					$query = 'SELECT tag, COUNT(tag) AS freq FROM Tags' . $wheretag . ' GROUP BY tag';
				}
			} else {
				//Tag::getTopTags
				$query = 'SELECT tag, COUNT(tag) AS freq FROM Tags GROUP BY tag' . $orderfreq;
			}
		}

		if($limit) {
			$query .= ' LIMIT ' . (int)$limit;
		}
		if($offset) {
			$query .= ' OFFSET ' . (int)$offset;
		}


		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		$res = $adodb->CacheGetAll($cache, $query);

		if(!$res) {
			return null;
		}
		
		foreach($res as &$i) {
			$row = sanitize($i);
			$result[] = $row;
		}

		return $result;

	}

	/**
	 * Get top tags, ordered by tag count
	 *
	 * @param int $limit The number of tags to return (default is 10)
	 * @param int $offset The position of the first tag to return (default is 0)
	 * @param int $cache Caching period of query in seconds (default is 600)
	 * @return array Tag details ((tag, freq) .. )
	 */
	function getTopTags($limit=10, $offset=0, $cache=600) {
		return Tag::_getTagData($cache, $limit, $offset);
	}

	/**
	 * Get top artists tagged with tag, ordered by tag count
	 *
	 * @param string $tag The tag to return artists for
	 * @param int $limit The number of artists to return (default is 10)
	 * @param int $offset The position of the first artist to return (default is 0)
	 * @param boolean $streamable Only return streamable artists if True (default is True)
	 * @param int $cache Caching period of query in seconds (default is 600)
	 * @return array Artist details ((artist, freq) .. )
	 */
	function getTopArtists($tag, $limit=10, $offset=0, $streamable=True, $cache=600) {
		if(isset($tag)) {
			return Tag::_getTagData($cache, $limit, $offset, null, null, null, null, $tag, 'artist', $streamable);
		}
	}

	/**
	 * Get top albums tagged with tag, ordered by tag count
	 *
	 * @param string $tag The tag to return albums for
	 * @param int $limit The number of albums to return (default is 10)
	 * @param int $offset The position of the first album to return (default is 0)
	 * @param boolean $streamable Only return albums by streamable artists if True (default is True)
	 * @param int $cache Caching period of query in seconds (default is 600)
	 * @return array Album details ((artist, album, freq) .. )
	 */
	function getTopAlbums($tag, $limit=10, $offset=0, $streamable=True, $cache=600) {
		if(isset($tag)) {
			return Tag::_getTagData($cache, $limit, $offset, null, null, null, null, $tag, 'album', $streamable);
		}
	}

	/**
	 * Get top tracks tagged with tag, ordered by tag count
	 *
	 * @param string $tag The tag to return tracks for
	 * @param int $limit The number of tracks to return (default is 10)
	 * @param int $offset The position of the first track to return (default is 0)
	 * @param boolean $streamable Only return tracks by streamable artists if True (default is True)
	 * @param int $cache Caching period of query in seconds (default is 600)
	 * @return array Track details ((artist, track, freq) .. )
	 */
	function getTopTracks($tag, $limit=10, $offset=0, $streamable=True, $cache=600) {
		if(isset($tag)) {
			return Tag::_getTagData($cache, $limit, $offset, null, null, null, null, $tag, 'track', $streamable);
		}
	}

	/**
	 * Get tag count for tag
	 *
	 * @param string $tag The tag to return tag count for
	 * @param int $cache Caching period of query in seconds (default is 600)
	 * @return array Tag details ((tag, freq) .. )
	 */
	function getInfo($tag, $cache=600) {
		if(isset($tag)) {
			return Tag::_getTagData($cache, 1, 0, null, null, null, null, $tag);
		}
	}

}
