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

class XML {
    public static function prettyXML($xml) {
	$dom = new DOMDocument('1.0'); 
	$dom->preserveWhitespace = false;
	$dom->loadXML($xml->asXML());
	$dom->formatOutput = true;
	return($dom->saveXML());
    } 

    public static function error($status, $errcode, $errtext) {
	$xml = new SimpleXMLElement("<lfm></lfm>");
	$xml->addAttribute("status", $status);
	$error = $xml->addChild("error", $errtext);
	$error->addAttribute("code", $errcode);
	return($xml);
    }
}

function repamp($input) {
    $input = str_replace('&', '&amp;', $input);
    return($input);
}
?>
