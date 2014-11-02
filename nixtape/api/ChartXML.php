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
require_once($install_path . '/data/Chart.php');
require_once($install_path . '/data/Server.php');
require_once('xml.php');

/**
 * Class with functions that returns XML-formatted data for charts.
 *
 * These functions are mainly used by web service methods.
 *
 * @package API
 */
class ChartXML {

	public static function getTopArtists($limit, $page, $streamable, $cache) {

		$offset = ($page - 1) * $limit;

		try {
			$res = Chart::getTopArtists($limit, $offset, $streamable, $cache);
		} catch (Exception $e) {
			return XML::error('error', '7', 'Invalid resource specified');
		}

		$xml = new SimpleXMLElement('<lfm status="ok"></lfm>');
		$root = $xml->addChild('artists');
		$root->addAttribute('page', $page);
		$root->addAttribute('perPage', $limit);
		foreach($res as &$row) {
			$artist_node = $root->addChild('artist');
			$artist = new Artist($row['artist']);
			$artist_node->addChild('name', repamp($artist->name));
			$artist_node->addChild('mbid', $artist->mbid);
			$artist_node->addChild('url', $artist->getURL());
			$artist_node->addChild('streamable', $artist->streamable);
			$image_small = $artist_node->addChild('image', repamp($artist->image_small));
			$image_small->addAttribute('size', 'small');
			$image_medium = $artist_node->addChild('image', repamp($artist->image_medium));
			$image_medium->addAttribute('size', 'medium');
			$image_large = $artist_node->addChild('image', repamp($artist->image_large));
			$image_large->addAttribute('size', 'large');
		}

		return $xml;

	}
	
}

