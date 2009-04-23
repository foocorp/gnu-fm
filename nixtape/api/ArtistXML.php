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

require_once($install_path . '/data/Artist.php');
require_once('xml.php');

class ArtistXML {

	/**
	 * Provides artist information in XML format
	 *
	 * @param string $api_key A 32 character API key (currently not checked)
	 * @param string $artistName The name of the artist to retrieve info for
	 * @param string $mbid A music brainz ID (optional), if supplied this will be preferred to the artist name
	 * @param string $lang A 2 character ISO 639 alpha-2 code indicating the language to return the information in
	 * @return A SimpleXMLElement containing the artist's information
	 */
	public static function getInfo($artistName, $api_key=false, $mbid=false, $lang="en") {
		// We assume $api_key is valid and set at this point
		
		if (!isset($artistName) && !isset($mbid)) {
			echo XML::error("failed", "7", "Invalid resource specified");
			return;
		}

		$artist = new Artist($artistName, $mbid);

		if (PEAR::isError($artist)) {	
			return(XML::error("failed", "7", "Invalid resource specified"));
		}

		$xml = new SimpleXMLElement("<lfm status=\"ok\"></lfm>");

		$artistXml = $xml->addChild("artist", null);
		$artistXml->addChild("name", utf8_encode($artist->name));
		$artistXml->addChild("mbid", $artist->mbid);
		$artistXml->addChild("streamable", $artist->streamable);

		$bio = $artistXml->addChild("bio", null);
		$bio->addChild("published", $artist->bio_published);
		$bio->addChild("summary", repamp($artist->bio_summary));
		$bio->addChild("content", repamp($artist->bio_content));

		return($xml);
	}

	public static function getTopTracks($artistName) {

		$artist = new Artist($artistName);

		if (PEAR::isError($artist)) {
			return(XML::error("failed", "7", "Invalid resource specified"));
		}

		$xml = new SimpleXMLElement("<lfm status=\"ok\"></lfm>");
		$root = $xml->addChild("toptracks", null);
		$root->addAttribute("artist", repamp($artist->name));

		$tracks = $artist->getTopTracks(50);

		// Loop over every result and add as children to "toptracks".
		// Encode trackname as utf8 and replace bad symbols with html-equivalents
		for($i = 1; $i < count($tracks); $i++) {
			$track = $root->addChild("track", null);
			$track->addAttribute("rank", $i);
			$track->addChild("name", repamp($tracks[$i]->name));
			$track->addChild("mbid", $tracks[$i]->mbid);
			$track->addChild("playcount", $tracks[$i]->getPlayCount());
			$track->addChild("listeners", $tracks[$i]->getListenerCount());
		}

		return($xml);	
	}

}

?>
