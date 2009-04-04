<?php
require_once('../database.php');
require_once('./xml.php');

class Artist {

    public static function getInfo($api_key, $artist, $mbid, $lang) {
	// We assume $api_key is valid and set at this point
	global $mdb2;
	$res;

	if (!isset($artist) && !isset($mbid)) {
	    echo XML::error("failed", "7", "Invalid resource specified");
	    return;
	}

	if (!isset($artist) || isset($mbid)) {
	    $res = $mdb2->query("SELECT * FROM Artist WHERE mbid=" . $mdb2->quote($mbid, 'text'));
	} else if (isset($artist)) {
	    $res = $mdb2->query("SELECT * FROM Artist WHERE name=" . $mdb2->quote($artist, 'text'));
	}

	if (PEAR::isError($res) || !$res->numRows()) {	    
	    echo XML::error("failed", "7", "Invalid resource specified");
	    return;
	}

	$xml = new SimpleXMLElement("<lfm status=\"ok\"></lfm>");
	$row = $res->fetchRow();

	$artist = $xml->addChild("artist", null);
	$artist->addChild("name", $row[0]);
	$artist->addChild("mbid", $row[1]);
	$artist->addChild("url", $row[9]);
	$artist->addChild("streamable", $row[2]);

	$bio = $artist->addChild("bio", null);
	$bio->addChild("published", $row[3]);
	$bio->addChild("summary", $row[5]);
	$bio->addChild("content", $row[4]);


	return($xml);
    }
}

?>
