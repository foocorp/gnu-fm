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


class JSONEncoder {


	/**
	 * Encode a SimpleXML object in JSON format matching Last.fm's output
	 *
	 * @param $xml - A SimpleXMLElement
	 * @return A JSON formatted string
	 */
	public static function encodeXML($xml) {
		if($xml->getName() == 'lfm' && count($xml->children())) {
			$xml = $xml->children();
		}
		$json_data[$xml->getName()] = JSONEncoder::recursivelyEncodeXML($xml);
		return json_encode($json_data);
	
	}

	public static function recursivelyEncodeXML($xml) {
		$json_data = array();

		if(count($xml->children())) {
			foreach($xml->children() as $child) {
				if(array_key_exists($child->getName(), $json_data)) {
					if(!array_key_exists(0, $json_data[$child->getName()])) {
						$a = array();
						$a[] = $json_data[$child->getName()];
						$json_data[$child->getName()] = $a;
					}
					$json_data[$child->getName()][] = JSONEncoder::recursivelyEncodeXML($child);
				} else {
					$json_data[$child->getName()] = JSONEncoder::recursivelyEncodeXML($child);
				}
			}
			if(count($xml->attributes())) {
				$json_data['@attr'] = array();
				foreach($xml->attributes() as $k => $v) {
					$json_data['@attr'][$k] = (string) $v;
				}
			}
		} else {
			// Leaf node
			if(count($xml->attributes())) {
				$json_data['#text'] = (string) $xml;
				foreach($xml->attributes() as $k => $v) {
					$json_data[$k] = (string) $v;
				}
			} else {
				$json_data = (string) $xml;
			}
		}

		return $json_data;
	}


}
