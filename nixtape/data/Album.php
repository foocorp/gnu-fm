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

require_once($install_path . '/database.php');
require_once($install_path . '/data/Artist.php');
require_once($install_path . '/data/Track.php');
require_once($install_path . "/resolve-external.php");
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
		global $mdb2;
		$res = $mdb2->query('SELECT name, artist_name, mbid, image, releasedate FROM Album WHERE '
			. 'name = ' . $mdb2->quote($name, 'text') . ' AND '
			. 'artist_name = ' . $mdb2->quote($artist, 'text'));
		if(!$res->numRows()) {
			$this->name = 'No such album: ' . $name;
		} else {
			$row = sanitize($res->fetchRow(MDB2_FETCHMODE_ASSOC));
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
		global $mdb2;
		$res = $mdb2->query('SELECT COUNT(*) AS scrobbles FROM Scrobbles JOIN Track ON Scrobbles.track = Track.name WHERE Scrobbles.artist = '
			. $mdb2->quote($this->artist_name, 'text') . ' AND Track.album_name ='
			. $mdb2->quote($this->name, 'text'));
		if(PEAR::isError($res)) {
			reportError($res->getMessage(), $res->getUserInfo());
		} else if (!$res->numRows()) {
			$c = 0;
		} else {
			$row = sanitize($res->fetchRow(MDB2_FETCHMODE_ASSOC));
			$c = $row['scrobbles'];
		}
		return $c;
	}

	/**
	 * Retrieves all the tracks in an album
	 *
	 * @return An array of Track objects
	 */
	function getTracks() {
		global $mdb2;
		$res = $mdb2->query('SELECT name, artist_name FROM Track WHERE artist_name = '
			. $mdb2->quote($this->artist_name, 'text') . ' AND album_name = '
			. $mdb2->quote($this->name));
		while($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC)) {
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
	global $mdb2;

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

		$license = $mdb2->quote($license);
		$image = $mdb2->quote($image);
		$album = $mdb2->quote($album);
		$artist = $mdb2->quote($artist);

		$sql = ('UPDATE Album SET image = '
			. ($image) . ', '
			. ' artwork_license = '
			. ($license) . ' WHERE artist_name = '. ($artist) 
			. ' AND name = '	. ($album));

		$res = $mdb2->query($sql);

		if(PEAR::isError($res)) {
			die('FAILED ' . $res->getMessage() . ' query was :' . $sql);
		}

	}
	
}
