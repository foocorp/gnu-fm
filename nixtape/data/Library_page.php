<?php

/* GNU FM -- a free network service for sharing your music listening habits

   Copyright (C) 2013 Free Software Foundation, Inc

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

require_once('templating.php');
require_once('data/Library.php');
require_once('user-menu.php');

/**
 * Represents a user's library page.
 *
 */
class Library_page {
	public $user, $section, $menu;
	public $artist, $album, $track, $artists_limit, $albums_limit, $tracks_limit;
	public $page_number, $next_page, $prev_page;
	public $default_artist_image;
	public $tagged_artists, $tagged_albums, $tagged_tracks;

	/**
	 * Create new Library page instance
	 *
	 * @params User $userobj The User object that owns this library.
	 */
	public function __construct($userobj) {
		global $base_url, $theme, $this_user;
		$this->user = $userobj;
		$this->setSection($_GET['section']);
		$this->ownedbyme = $this->user == $this_user;
		$this->menu = $this->getMenu();
		$this->section_url = $this->getURL($this->section);
		$this->artists_limit = 16;
		$this->albums_limit = 15;
		$this->tracks_limit = 200;
		$this->scrobbles_limit = 50;
		$this->loved_limit = 50;
		$this->banned_limit = 50;
		$this->tags_limit = 50;
		$this->default_artist_image = "{$base_url}/themes/{$theme}/img/qm160.png";
		$this->default_album_image = "{$base_url}/themes/{$theme}/img/qm160.png";
		$this->default_track_image = "{$base_url}/themes/{$theme}/img/qm160.png";
		$this->default_tag_image = "{$base_url}/themes/{$theme}/img/qm160.png";

		// Set properties based on url params
		$this->setPageNumber($_GET['page']);
		$this->setSortBy($_GET['sortby']);
		$this->setSortOrder($_GET['sortorder']);
		$this->setStreamable($_GET['streamable']);
		$this->setArtist($_GET['artist']);
		$this->setAlbum($_GET['album']);
		$this->setTrack($_GET['track']);
		$this->setTag($_GET['tag']);

		$this->getPageURLS();
	}

	public function setSection($section) {
		$this->section = $section ? $section : 'music';
	}

	public function setPageNumber($page) {
		$this->page_number = $page ? $page : 1;
	}

	public function setSortBy($sortby) {
		$this->sortby = $sortby ? $sortby : 'plays';
	}

	public function setSortOrder($sortorder) {
		if ($this->sortby == 'name') {
			$this->sortorder = $sortorder ? $sortorder : 'asc';
		} else {
			$this->sortorder = $sortorder ? $sortorder : 'desc';
		}
	}

	public function setStreamable($streamable) {
		$this->streamable = $streamable ? $streamable : False;
	}

	public function setArtist($artist) {
		if ($artist) {
			$this->artist = new Artist($artist);
			$this->artist_url = Server::getArtistURL($this->artist->name);
			$this->artist_library_url = $this->getURL('music', $this->artist->name);
			$this->artist_image = $this->artist->image_small ? $this->artist->image_small : $this->default_artist_image;
		}
	}

	public function setAlbum($album) {
		if ($album) {
			$this->album = new Album($album, $this->artist->name);
			$this->album_url = Server::getAlbumURL($this->artist->name, $this->album->name);
			$this->album_library_url = $this->getURL('music', $this->artist->name, $this->album->name);
			$this->album_image = $this->album->image ? $this->album->image : $this->default_album_image;
		}
	}

	public function setTrack($track) {
		if ($track) {
			$this->track = new Track($track, $this->artist->name);
			$this->track_url = Server::getTrackURL($this->artist->name, null, $this->track->name);
			$this->track_library_url = $this->getURL('music', $this->artist->name, null, $this->track->name);
			$this->track_image = $this->artist_image;
		}
	}

	public function setTag($tag) {
		if ($tag) {
			$this->tag_name = $tag;
			$this->tag_url = Server::getTagURL($tag);
			$this->tag_library_url = $this->getURL('tags', null, null, null, $tag);
			$this->tag_image = $this->default_tag_image;
		}
	}

	private function getMenu() {
		$submenu = array(
			array('name' => _('Music'), 'section' => 'music', 'url' => $this->getURL('music')),
			array('name' => _('Scrobbles'), 'section' => 'scrobbles', 'url' => $this->getURL('scrobbles')),
			array('name' => _('Loved tracks'), 'section' => 'loved', 'url' => $this->getURL('loved')),
			array('name' => _('Banned tracks'), 'section' => 'banned', 'url' => $this->getURL('banned')),
			array('name' => _('Tags'), 'section' => 'tags', 'url' => $this->getURL('tags'))
		);
		foreach ($submenu as &$item) {
			$item['active'] = (strtolower($item['section']) == strtolower($this->section));
		}
		return $submenu;
	}

	public function getURL($section = null, $artist = null, $album = null, $track = null, $tag = null, $params = null) {
		global $friendly_urls, $base_url;

		if ($friendly_urls) {
			$myurl = $base_url . '/user/' . $this->user->name . '/library/';

			if ($section) {
				$myurl .= $section . '/';
			}

			if ($artist) {
				$myurl .= rewrite_encode($artist) . '/';
			}
			if ($album) {
				if (!$track) {
					$myurl .= rewrite_encode($album) . '/';
				}
			}
			if ($track) {
				if ($album) {
					$myurl .= rewrite_encode($album) . '/';
				} else {
					$myurl .= '_/';
				}
				$myurl .= rewrite_encode($track) . '/';
			}
			if($tag) {
				$myurl .= rewrite_encode($tag) . '/';
			}
			if($params) {
				$myurl .= '?' . $params;
			}
		} else {
			$myurl = $base_url . '/user-library.php?';
			$myurl .= 'user=' . rawurlencode($this->user->name);

			if ($section) {
				$myurl .= '&section=' .rawurlencode($section);
			}
			if ($artist) {
				$myurl .= '&artist=' . rawurlencode($artist);
			}
			if ($album) {
				$myurl .= '&album=' . rawurlencode($album);
			}
			if ($track) {
				$myurl .= '&track=' . rawurlencode($track);
			}
			if ($tag) {
				$myurl .= '&tag=' . rawurlencode($tag);
			}
			if($params) {
				$myurl .= '&' . $params;
			}

		}
		return $myurl;
	}

	public function getArtists() {
		global $adodb;
		$offset = ($this->page_number - 1) * $this->artists_limit;

		$query = 'SELECT s.artist, a.image_small AS image, COUNT(s.artist) AS freq, COUNT(DISTINCT s.userid) AS listeners, MAX(a.streamable) AS streamable FROM Scrobbles AS s LEFT JOIN Artist AS a ON (s.artist=a.name) WHERE s.userid=?';
		$params = array($this->user->uniqueid);

		if ($this->streamable) {
			$query .= ' AND streamable=1';
		}

		/* BEGIN temporary limit query to one month in the past so we dont melt libre.fm server */
		$query .= ' AND s.time > ' . (int) (time() - (3600 * 24 * 30));
		/* END temporary limit */

		$query .= ' GROUP BY s.artist, image';

		if($this->sortby) {
			if($this->sortby == 'name') {
				if($this->sortorder == 'desc') {
					$query .= ' ORDER BY s.artist DESC, freq DESC';
				}else{
					$query .= ' ORDER BY s.artist ASC, freq DESC';
				}
			}else if ($this->sortby == 'plays') {
				if($this->sortorder == 'asc') {
					$query .= ' ORDER BY freq ASC, s.artist ASC';
				}else{
					$query .= ' ORDER BY freq DESC, s.artist ASC';
				}
			}
		}else{
			$query .= ' ORDER BY freq DESC, s.artist ASC';
		}
	   
		$query .= '	LIMIT ? OFFSET ?';
		$params[] = (int) $this->artists_limit;
		$params[] = (int) $offset;

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		$data = $adodb->CacheGetAll(600, $query, $params);

		foreach($data as &$item) {
			$item['artistlibraryurl'] = $this->getURL('music', $item['artist']);
			$item['artisturl'] = Server::getArtistURL($item['artist']);

			if (!$item['image']) {
				$item['image'] = $this->default_artist_image;
			}
		}
		$this->artists = $data;
		return $data;
	}

	public function getAlbums() {
		//todo sort order
		global $adodb;
		$offset = ($this->page_number - 1) * $this->albums_limit;

		$query = 'SELECT MAX(s.artist) AS artist, MAX(s.album) AS album, MAX(a.image) AS image, COUNT(s.album) AS freq, COUNT(DISTINCT s.userid) AS listeners, MAX(t.streamable) AS streamable FROM Scrobbles AS s LEFT JOIN Scrobble_Track AS st ON (s.stid=st.id) LEFT JOIN Track AS t ON (st.track=t.id) LEFT JOIN Album AS a ON (t.artist_name=a.artist_name AND t.album_name=a.name) WHERE s.album IS NOT NULL AND s.userid=?';
		$params = array($this->user->uniqueid);
		
		if ($this->streamable) {
			$query .= ' AND t.streamable=1';
		}

		if ($this->artist->name) {
			$query .= ' AND lower(s.artist)=lower(?)';
			$params[] = $this->artist->name;
		}
	
		$query .= ' GROUP BY lower(s.artist), lower(s.album) ORDER BY freq DESC, lower(s.artist) ASC, lower(s.album) ASC LIMIT ? OFFSET ?';
		$params[] = $this->albums_limit;
		$params[] = $offset;

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		$data = $adodb->CacheGetAll(600, $query, $params);

		foreach($data as &$item) {
			$item['artisturl'] = Server::getArtistURL($item['artist']);
			$item['albumurl'] = Server::getAlbumURL($item['artist'], null, $item['album']);
			$item['image'] = resolve_external_url($item['image']);

			$item['artistlibraryurl'] = $this->getURL('music', $item['artist']);
			$item['albumlibraryurl'] = $this->getURL('music', $item['artist'], $item['album']);
			if (!$item['image']) {
				$item['image'] = $this->default_album_image;
			}
		}
		$this->albums = $data;
		return $data;
	}

	public function getTracks() {
		//todo sort order
		global $adodb;
		$offset = ($this->page_number - 1) * $this->tracks_limit;

		$query = 'SELECT MAX(s.artist) AS artist, MAX(s.track) AS track, COUNT(s.track) AS freq, COUNT(DISTINCT s.userid) AS listeners, MAX(tr.streamable) AS streamable, (SELECT 1 FROM Tags AS t WHERE s.artist=t.artist AND s.track=t.track AND s.userid=t.userid LIMIT 1) AS tagged, (SELECT 1 FROM Loved_Tracks AS lt WHERE s.artist=lt.artist AND s.track=lt.track AND s.userid=lt.userid LIMIT 1) AS loved FROM Scrobbles AS s LEFT JOIN Scrobble_Track AS st ON (s.stid=st.id) LEFT JOIN Track AS tr ON (st.track=tr.id) WHERE s.userid=?';
		$params = array($this->user->uniqueid);
		
		if ($this->streamable) {
			$query .= ' AND tr.streamable=1';
		}

		if ($this->artist->name) {
			$query .= ' AND lower(s.artist)=lower(?)';
			$params[] = $this->artist->name;
			if ($this->album->name) {
				$query .= ' AND lower(s.album)=lower(?)';
				$params[] = $this->album->name;
			}
		}
	
		$query .= ' GROUP BY s.artist, s.track, s.userid ORDER BY freq DESC, s.artist ASC, s.track ASC LIMIT ? OFFSET ?';
		$params[] = (int) $this->tracks_limit;
		$params[] = (int) $offset;

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		$data = $adodb->CacheGetAll(600, $query, $params);

		foreach($data as &$item) {
			$item['artisturl'] = Server::getArtistURL($item['artist']);
			$item['trackurl'] = Server::getTrackURL($item['artist'], null, $item['track']);
			$item['artistlibraryurl'] = $this->getURL('music', $item['artist']);
			$item['tracklibraryurl'] = $this->getURL('music', $item['artist'], null, $item['track']);
		}
		$this->tracks = $data;
		return $data;
	}

	public function getScrobbles($nocache=false) {
		global $adodb;
		$cache = $nocache ? 0 : 600;
		$offset = ($this->page_number - 1) * $this->scrobbles_limit;

		$query = 'SELECT s.artist, s.track, s.time, lt.userid as loved, t.streamable FROM Scrobbles s LEFT JOIN Scrobble_Track st ON (s.stid=st.id) LEFT JOIN Track t ON (st.track=t.id) LEFT JOIN Loved_Tracks lt ON (s.artist=lt.artist AND s.track=lt.track AND s.userid=lt.userid) WHERE s.userid=?';
		$params = array($this->user->uniqueid);
		
		if ($this->streamable) {
			$query .= ' AND streamable=1';
		}

		if ($this->artist->name) {
			$query .= ' AND s.artist=?';
			$params[] = $this->artist->name;
		}
	
		$query .= ' ORDER BY time DESC LIMIT ? OFFSET ?';
		$params[] = (int) $this->scrobbles_limit;
		$params[] = (int) $offset;

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		$data = $adodb->CacheGetAll($cache, $query, $params);

		foreach($data as &$item) {
			$item['artisturl'] = Server::getArtistURL($item['artist']);
			$item['trackurl'] = Server::getTrackURL($item['artist'], null, $item['track']);
			$item['timehuman'] = human_timestamp($item['time']);

			$item['artistlibraryurl'] = $this->getURL('music', $item['artist']);
			$item['tracklibraryurl'] = $this->getURL('music', $item['artist'], null, $item['track']);
		}
		$this->scrobbles = $data;
		return $data;
	}

	public function getLovedTracks($nocache=false) {
		global $adodb;
		$cache = $nocache ? 0 : 600;
		$offset = ($this->page_number -1) * $this->loved_limit;

		$query = 'SELECT lt.artist, lt.track, max(lt.time) as time, 1 as loved, MAX(t.streamable) AS streamable FROM Loved_Tracks lt LEFT JOIN Track t ON(lt.artist=t.artist_name AND lt.track=t.name) WHERE lt.userid=?';
		$params = array($this->user->uniqueid);
		
		if ($streamable) {
			$query .= ' AND streamable=1)';
		}

		if ($this->artist->name) {
			$query .= ' AND lt.artist=?';
			$params[] = $this->artist->name;
		}
	
		$query .= ' GROUP BY lt.artist, lt.track ORDER BY time DESC LIMIT ? OFFSET ?';
		$params[] = $this->loved_limit;
		$params[] = $offset;

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		$data = $adodb->CacheGetAll($cache, $query, $params);

		foreach($data as &$item) {
			$item['artisturl'] = Server::getArtistURL($item['artist']);
			$item['trackurl'] = Server::getTrackURL($item['artist'], null, $item['track']);
			$item['timehuman'] = human_timestamp($item['time']);
			$item['artistlibraryurl'] = $this->getURL('music', $item['artist']);
			$item['tracklibraryurl'] = $this->getURL('music', $item['artist'], null, $item['track']);
		}
		$this->loved_tracks = $data;
		return $data;
	}

	public function getBannedTracks($nocache=false) {
		global $adodb;
		$cache = $nocache ? 0 : 600;
		$offset = ($this->page_number -1) * $this->banned_limit;

		$query = 'SELECT bt.artist, bt.track, max(bt.time) as time, 1 as banned, MAX(t.streamable) AS streamable FROM Banned_Tracks bt LEFT JOIN Track t ON(bt.artist=t.artist_name AND bt.track=t.name) WHERE bt.userid=?';
		$params = array($this->user->uniqueid);
		
		if ($this->streamable) {
			$query .= ' AND streamable=1)';
		}

		if ($this->artist->name) {
			$query .= ' AND bt.artist=?';
			$params[] = $this->artist->name;
		}
	
		$query .= ' GROUP BY bt.artist, bt.track ORDER BY time DESC LIMIT ? OFFSET ?';
		$params[] = $this->banned_limit;
		$params[] = $offset;

		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		$data = $adodb->CacheGetAll($cache, $query, $params);

		foreach($data as &$item) {
			$item['artisturl'] = Server::getArtistURL($item['artist']);
			$item['trackurl'] = Server::getTrackURL($item['artist'], null, $item['track']);
			$item['timehuman'] = human_timestamp($item['time']);
			$item['artistlibraryurl'] = $this->getURL('music', $item['artist']);
			$item['tracklibraryurl'] = $this->getURL('music', $item['artist'], null, $item['track']);
		}
		$this->banned_tracks = $data;
		return $data;
	}

	public function getTags() {
		global $adodb;
		$offset = ($this->page_number -1) * $this->tags_limit;

		$query = 'SELECT t.tag, COUNT(t.tag) AS freq FROM Tags AS t WHERE t.userid=?';
		$params = array($this->user->uniqueid);

		if ($this->artist->name) {
			$query .= ' AND t.artist=?';
			$params[] = $this->artist->name;

			if ($track) {
				$query .= ' AND t.track=?';
				$params[] = $this->track->name;
			}
		}

		$query .= ' GROUP BY t.tag ORDER BY freq DESC, t.tag ASC LIMIT ? OFFSET ?';
		$params[] = $this->tags_limit;
		$params[] = $offset;

		$data = $adodb->CacheGetAll(600, $query, $params);

		foreach($data as &$item) {
			$item['tagurl'] = Server::getTagURL($item['tag']);
			$item['taglibraryurl'] = $this->getURL('tags', null, null, null, $item['tag']);
		}
		$this->tags = $data;
		return $data;
	}

	public function getTaggedArtists($nocache=false) {
		global $adodb;
		$cache = $nocache ? 0 : 600;
		//$offset = ($this->page_number -1) * $this->artists_limit;

		$query = 'SELECT t.artist, t.tag, a.streamable, a.image_small as image FROM Tags t LEFT JOIN Artist a ON (t.artist=a.name) WHERE t.track IS NULL AND t.album IS NULL AND t.tag=? AND t.userid=?';
		$params = array($this->tag_name, $this->user->uniqueid);

		if ($this->streamable) {
			$query .= ' AND a.streamable=1';
		}

		$query .= ' ORDER BY t.artist, a.streamable';
		$data = $adodb->CacheGetAll($cache, $query, $params);

		foreach($data as &$item) {
			$item['artisturl'] = Server::getArtistURL($item['artist']);
			$item['artistlibraryurl'] = $this->getURL('music', $item['artist']);
			if (!$item['image']) {
				$item['image'] = $this->default_artist_image;
			}
		}
		$this->tagged_artists = $data;
		return $data;
	}

	public function getTaggedAlbums($nocache=false) {
		global $adodb;
		$cache = $nocache ? 0 : 600;
		//$offset = ($this->page_number -1) * $this->albums_limit;

		$query = 'SELECT t.tag, t.artist, t.album, MAX(tr.streamable) AS streamable, al.image FROM Tags t LEFT JOIN Album al ON (t.artist=al.artist_name AND t.album=al.name) LEFT JOIN Track tr ON (t.artist=tr.artist_name AND t.album=tr.album_name AND tr.streamable=1) WHERE t.track IS NULL AND t.album IS NOT NULL AND t.tag=? AND t.userid=?';
		$params = array($this->tag_name, $this->user->uniqueid);

		if ($this->streamable) {
			$query .= ' AND tr.streamable=1';
		}

		$query .= ' GROUP BY t.tag, t.artist, t.album, al.image ORDER BY t.artist ASC, t.album ASC';

		$data = $adodb->CacheGetAll($cache, $query, $params);

		foreach($data as &$item) {
			$item['artisturl'] = Server::getArtistURL($item['artist']);
			$item['albumurl'] = Server::getAlbumURL($item['artist'], $item['album']);
			$item['artistlibraryurl'] = $this->getURL('music', $item['artist']);
			$item['albumlibraryurl'] = $this->getURL('music', $item['artist'], $item['album']);
		}
		$this->tagged_albums = $data;
		return $data;
	}

	public function getTaggedTracks($nocache=false) {
		global $adodb;
		$cache = $nocache ? 0 : 600;
		$offset = ($this->page_number -1) * $this->tags_limit;

		$query = 'SELECT t.tag, t.artist, t.track, MAX(tr.streamable) AS streamable FROM Tags t LEFT JOIN Track tr ON (t.artist=tr.artist_name AND t.track=tr.name) WHERE t.track IS NOT NULL AND t.tag=? AND t.userid=?';
		$params = array($this->tag_name, $this->user->uniqueid);

		if ($this->streamable) {
			$query .= ' AND tr.streamable=1';
		}

		$query .= ' GROUP BY t.tag, t.artist, t.track ORDER BY t.artist ASC, t.track ASC';
		$data = $adodb->CacheGetAll($cache, $query, $params);

		foreach($data as &$item) {
			$item['artisturl'] = Server::getArtistURL($item['artist']);
			$item['trackurl'] = Server::getTrackURL($item['artist'], null, $item['track']);
			$item['artistlibraryurl'] = $this->getURL('music', $item['artist']);
			$item['tracklibraryurl'] = $this->getURL('music', $item['artist'], null, $item['track']);
		}
		$this->tagged_tracks = $data;
		return $data;
	}

	public function getPageURLS() {
		//todo clean up this mess
		$gets = $_GET;
		$prettygets = array('user' => 1, 'section' => 1, 'artist' => 1, 'album' => 1, 'track' => 1, 'tag' => 1);
		$urldiff = array_diff_key($gets, $prettygets);

		$page_next_params = $urldiff;
		$page_next_params['page'] = (int)$this->page_number + 1;
		$page_next_tail = '';
		foreach($page_next_params as $k => $v) {
			$page_next_tail .= "{$k}={$v}&";
		}
		$url_page_next = $this->getURL($this->section, $this->artist->name, $this->album->name, $this->track->name, $this->tag_name, $page_next_tail);

		$page_prev_params = $urldiff;
		$page_prev_params['page'] = ($this->page_number > 1) ? $this->page_number - 1 : 1;
		$page_prev_tail = '';
		foreach($page_prev_params as $k => $v) {
			$page_prev_tail .= "{$k}={$v}&";
		}
		$url_page_prev = $this->getURL($this->section, $this->artist->name, $this->album->name, $this->track->name, $this->tag_name, $page_prev_tail);

		$sort_name_params = $urldiff;
		$sort_name_params['sortby'] = 'name';
		unset($sort_name_params['page']);
		$sort_name_params['sortorder'] = ($this->sortby == 'name' && $sort_name_params['sortorder'] == 'asc') ? 'desc' : 'asc';
		$sort_name_tail = '';
		foreach($sort_name_params as $k => $v) {
			$sort_name_tail .= "{$k}={$v}&";
		}
		$url_sort_name = $this->getURL($this->section, $this->artist->name, $this->album->name, $this->track->name, $this->tag_name, $sort_name_tail);

		$sort_plays_params = $urldiff;
		$sort_plays_params['sortby'] = 'plays';
		unset($sort_plays_params['page']);
		$sort_plays_params['sortorder'] = ($this->sortby == 'plays' && $sort_plays_params['sortorder'] == 'desc') ? 'asc' : 'desc';
		$sort_plays_tail = '';
		foreach($sort_plays_params as $k => $v) {
			$sort_plays_tail .= "{$k}={$v}&";
		}
		$url_sort_plays = $this->getURL($this->section, $this->artist->name, $this->album->name, $this->track->name, $this->tag_name, $sort_plays_tail);

		$streamable_params = $urldiff;
		unset($streamable_params['page']);
		if ($this->streamable) {
			unset($streamable_params['streamable']);
		} else {
			$streamable_params['streamable'] = true;
		}
		$streamable_tail = '';
		foreach($streamable_params as $k => $v) {
			$streamable_tail .= "{$k}={$v}&";
		}
		$url_streamable = $this->getURL($this->section, $this->artist->name, $this->album->name, $this->track->name, $this->tag_name, $streamable_tail);


		$this->urls = array(
			'page_next' => $url_page_next,
			'page_prev' => $url_page_prev,
			'sort_name' => $url_sort_name,
			'sort_count' => $url_sort_plays,
			'streamable' => $url_streamable,
		);

		//return var_dump(array($urldiff, $this->pageurls));
	}

}
