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
require_once($install_path . '/data/Album.php');
require_once('xml.php');

class AlbumXML {

	public static function getTopTags($artist, $album) {

		$album = new Album($album, $artist);
		if (!$album) {
			return(XML::error('failed', '7', 'Invalid resource specified'));
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('toptags', null);
		$root->addAttribute('artist', $album->artist_name);
		$root->addAttribute('album', $album->name);

		$tags = $album->getTopTags();
		foreach($tags as &$tag) {
			$tag_node = $root->addChild('tag', null);
			$tag_node->addChild('name', repamp($tag['tag']));
			$tag_node->addChild('count', $tag['freq']);
			$tag_node->addChild('url', Server::getTagURL($tag['tag']));
		}

		return $xml;
	}

}
