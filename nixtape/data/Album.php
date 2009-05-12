<?php

/* Libre.fm -- a free network service for sharing your music listening habits

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

require_once($install_path . '/database2.php');
require_once($install_path . '/data/Artist.php');
require_once($install_path . '/data/Track.php');
require_once($install_path . "/utils/resolve-external.php");
require_once($install_path . '/utils/linkeddata.php');

/**
 * Represents album data
 *
 * General album attributes are accessible as public variables. 
 * Lists of tracks are only generated when requested.
 */
class Album {

	public $name, $artist_name, $mbid, $releasedate, $image;
	public $id;

	/**
	 * Album constructor
	 *
	 * @param string name The name of the album to load
	 * @param string artist The name of the artist who recorded this album
	 */
	function __construct($name, $artist) {
		global $adodb;
		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		$r = $adodb->CacheGetRow(1200,
			'SELECT name, artist_name, mbid, image, releasedate FROM Album WHERE '
			. 'name = ' . $adodb->qstr($name) . ' AND '
			. 'artist_name = ' . $adodb->qstr($artist));
		if(!$w) {
			$this->name = 'No such album: ' . $name;
		} else {
			$row = sanitize($r);
			$this->name = $row['name'];
			$this->mbid = $row['mbid'];
			$this->artist_name = $row['artist_name'];
			$this->releasedate = $row['releasedate'];
			$this->image = resolve_external_url($row['image']);

			$this->id = identifierAlbum(null, $this->artist_name, null, $this->name, null, null, null, $this->mbid);

			// this	hack brought to	you by	mattl
			//if ($row['image'] == ''){
			//go_get_album_art($this->artist_name, $this->name);
			//}
			// mattl hack ovar

			if($this->image == '') {
				$this->image = false;
			}
		}

	}

	function getPlayCount() {
		global $adodb;
		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		try {
		$count = $adodb->CacheGetOne(600,
			'SELECT COUNT(*) AS scrobbles FROM Scrobbles JOIN Track ON Scrobbles.track = Track.name WHERE Scrobbles.artist = '
			. $adodb->qstr($this->artist_name) . ' AND Track.album_name ='
			. $adodb->qstr($this->name));
		}
		catch (exception $e) {
			reportError($res->getMessage(), $res->getUserInfo());
			$c = 0;
		}
		if (!$count) {
			$c = 0;
		} else {
			$c = $count;
		}
		return $c;
	}

	/**
	 * Retrieves all the tracks in an album
	 *
	 * @return An array of Track objects
	 */
	function getTracks() {
		global $adodb;
		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		$res = $adodb->CacheGetAll(600, 'SELECT name, artist_name FROM Track WHERE artist_name = '
			. $adodb->qstr($this->artist_name) . ' AND album_name = '
			. $adodb->qstr($this->name));
		foreach($res as &$row) {
			$tracks[] = new Track($row['name'], $row['artist_name']);
		}

		return $tracks;
	}

	/**
	 * Gives the URL for this album
	 *
	 * @return A string containing the URL of this album
	 */
	function getURL() {
		return Server::getAlbumURL($this->artist_name, $this->name);
	}

}




function go_get_album_art($artist, $album){
	global $adodb;
	$adodb->SetFetchMode(ADODB_FETCH_ASSOC);

	$Access_Key_ID = '1EST86JB355JBS3DFE82'; // this is mattl's personal key :)

	$SearchIndex='Music';
	$Keywords=urlencode($artist.' '.$album);
	$Operation = 'ItemSearch';
	$Version = '2007-07-16';
	$ResponseGroup = 'ItemAttributes,Images';
	$request=
		'http://ecs.amazonaws.com/onca/xml'
		. '?Service=AWSECommerceService'
		. '&AssociateTag=' . $Associate_tag
		. '&AWSAccessKeyId=' . $Access_Key_ID
		. '&Operation=' . $Operation
		. '&Version=' . $Version
		. '&SearchIndex=' . $SearchIndex
		. '&Keywords=' . $Keywords
		. '&ResponseGroup=' . $ResponseGroup;

	$aws_xml = simplexml_load_file($request) or die('xml response not loading');

	$image = $aws_xml->Items->Item->MediumImage->URL;
	$URI = $aws_xml->Items->Item->DetailPageURL;
	
	if ($image) {

		if ($license == '') { $license = 'amazon'; }

		$license = $adodb->qstr($license);
		$image = $adodb->qstr($image);
		$album = $adodb->qstr($album);
		$artist = $adodb->qstr($artist);

		$sql = ('UPDATE Album SET image = '
			. ($image) . ', '
			. ' artwork_license = '
			. ($license) . ' WHERE artist_name = '. ($artist) 
			. ' AND name = '	. ($album));

		try {
			$res = $adodb->Execute($sql);
		}
		catch (exception $e) {
			die('FAILED ' . $e->getMessage() . ' query was :' . $sql);
		}
	}
}
