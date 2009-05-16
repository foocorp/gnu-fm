<?php
/* Libre.fm -- a free network service for sharing your music listening habits

   Copyright (C) 2009 Libre.fm Project

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
require_once($install_path . '/data/Server.php');
require_once($install_path . '/config.php'); // Should already be required though.

class TagCloud {

	/*
	 * returns an array counting appareances of a given field and his corresponding font-size.
	 * @param string $table table name to be queried
	 * @param string $field field name to count
	 * @param integer $limit limit of the query
	 * @param string $constraint username or artistname depending on field
	 * inaccurate @param integer $sizes quantity of possible sizes
	 * inaccurate @param float $max_font_size maximum font size (px, em, %, etc)
	 * @return array tagcloud
	 */
	static function generateTagCloud($table, $field, $limit = 40, $constraint = null, $constrained_field = false) {
		global $adodb;
		if (!is_string($field))          return false;
		if (!is_string($table))          return false;
		if (!is_integer($limit))         return false;
		$sizes = array('xx-large', 'x-large', 'large', 'medium', 'small', 'x-small', 'xx-small');
		$query = 'SELECT ' . $field . ', count(*) AS count FROM ' . $table;
		$query .= (!is_null($constraint)) ? ' WHERE ' : null;
		if ($constrained_field) {
			$query .= (!is_null($constraint)) ? $constrained_field  . ' = ' . $adodb->qstr($constraint) : null;
		} elseif ($field == 'track') {
			$query .= (!is_null($constraint)) ? ' artist = ' . $adodb->qstr($constraint) : null;
		} else {
			$query .= (!is_null($constraint)) ? ' username = ' . $adodb->qstr($constraint) : null;
		}
		$query .= ' GROUP BY ' . $field . ' ORDER BY count DESC LIMIT ' . $limit;
		$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
		$res = $adodb->CacheGetAll(7200,$query);
		if (!$res) {
			throw new Exception('ERROR ' . $query);
		} else {
			foreach($res as $count => &$i) {
				$i['size'] = $sizes[(int) ($count/(count($res)/7))];
			}
			foreach($res as &$i){
				$i['pageurl'] = Server::getArtistURL($i['artist']);
			}
			sort($res);
			return $res;
		}
	}

	/**
	 * Returns the preferred table to generate scrobble data from.
	 *
	 * @param string $area The are where we're displaying scrobble data; one of 'main', 'user', 'group'. Optional: defaults to 'main'.
	 * @return string Usually 'Scrobbles' or 'Free_Scrobbles'.
	 * @author tobyink
	 */
	static function scrobblesTable ($area = 'main')
	{
		// This array can be set up in config.php
		global  $scrobblecloud_table;

		if (!empty($scrobblecloud_table[$area]))
		{
			return $scrobblecloud_table[$area];
		}

		if ($area == 'main')
		{
			return 'Free_Scrobbles';
		}

		return 'Scrobbles';
	}
}
?>
