<?php
require_once('../database.php');
require_once('xml.php');

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
	    return(XML::error("failed", "7", "Invalid resource specified"));
	}

	$xml = new SimpleXMLElement("<lfm status=\"ok\"></lfm>");
	$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

	$artist = $xml->addChild("artist", null);
	$artist->addChild("name", utf8_encode($row['name']));
	$artist->addChild("mbid", $row['mbid']);
	$artist->addChild("streamable", $row['streamable']);

	$bio = $artist->addChild("bio", null);
	$bio->addChild("published", $row['bio_published']);
	$bio->addChild("summary", repamp($row['bio_summary']));
	$bio->addChild("content", repamp($row['bio_content']));

	$res->free();

	return($xml);
    }
    
    public static function getTopTracks($artist) {
	global $mdb2;

	$res = $mdb2->query("SELECT Track.*, COUNT(*) AS freq, COUNT(DISTINCT Scrobbles.username) AS dist 
	    FROM Scrobbles,Track 
	    WHERE Scrobbles.track = Track.name 
	    AND Track.artist =" . $mdb2->quote($artist, 'text') . 
	    " GROUP BY Track.name 
	    ORDER BY freq DESC LIMIT 50");

	if (PEAR::isError($res) || !$res->numRows()) {
	    return(XML::error("failed", "7", "Invalid resource specified"));
	}

	$xml = new SimpleXMLElement("<lfm status=\"ok\"></lfm>");
	$root = $xml->addChild("toptracks", null);
	$root->addAttribute("artist", repamp($artist));
	$i = 1;

	// Loop over every result and add as children to "toptracks".
	// Encode trackname as utf8 and replace bad symbols with html-equivalents
	while (($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))) {
	    $track = $root->addChild("track", null);
	    $track->addAttribute("rank", $i);
	    $track->addChild("name", repamp($row['name']));
	    $track->addChild("mbid", $row['mbid']);
	    $track->addChild("playcount", $row['freq']);
	    $track->addChild("listeners", $row['dist']);
	    $i++;
	}
	return($xml);	
    }

}

?>
