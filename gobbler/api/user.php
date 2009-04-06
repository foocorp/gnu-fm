<?php
require_once('../database.php');
require_once('xml.php');

class User {

    public static function getInfo($username) {
	global $mdb2;

	$res = $mdb2->query("SELECT * FROM Users WHERE username=" . $mdb2->quote($username, 'text'));
	if (PEAR::isError($res) || !$res->numRows()) {
	    return(XML::error("failed", "7", "Invalid resource specified"));
	}

	$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);

	$xml = new SimpleXMLElement("<lfm status=\"ok\"></lfm>");
	$user_node = $xml->addChild("user", null);
	$user_node->addChild("name", $row['username']);
	$user_node->addChild("email", $row['email']);
	$user_node->addChild("homepage", $row['homepage']);
	$user_node->addChild("location", $row['location']);
	$user_node->addChild("bio", $row['bio']);
	$user_node->addChild("profile_created", strftime("%c", $row['created']));
	if (isset($row['modified']))
	    $user_node->addChild("profile_updated", strftime("%c", $row['modified']));

	return($xml);
    }

    public static function getTopTracks($username, $time) {
	global $mdb2;

	$timestamp;
	if (!isset($time)) 
	    $time = "overall";

	if (strcmp($time, "overall") == 0) {
	    $timestamp = 0;
	} else if (strcmp($time, "3month") == 0) {
	    $timestamp = strtotime('-3 months');
	} else if (strcmp($time, "6month") == 0) {
	    $timestamp = strtotime('-6 months');
	} else if (strcmp($time, "9month") == 0) {
	    $timestamp = strtotime('-9 months');
	} else if (strcmp($time, "12month") == 0) {
	    $timestamp = strtotime('-12 months');
	} else {
	    return(XML::error("error", "13", "Invalid method signature supplied"));
	}

	$res = $mdb2->query("SELECT Track.*, Artist.mbid artmbid, COUNT(*) AS freq 
	    FROM Track, Scrobbles,Artist 
	    WHERE Scrobbles.username =" . $mdb2->quote($username, 'text') . "
	    AND Scrobbles.track = Track.name AND Scrobbles.time > " . $timestamp . " AND Track.artist = Artist.name 
	    GROUP BY Track.name ORDER BY freq DESC");

	if (PEAR::isError($res) || !$res->numRows()) {
	    return(XML::error("failed", "7", "Invalid resource specified"));
	}
    
	$xml = new SimpleXMLElement("<lfm status=\"ok\"></lfm>");

	$root = $xml->addChild("toptracks", null);
	$root->addAttribute("user", $username);
	$root->addAttribute("type", $time);
	$i = 1;
	while(($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))) {

	    $track = $root->addChild("track", null);
	    $track->addAttribute("rank", $i);
	    $track->addChild("name", htmlentities($row['name']));
	    $track->addChild("playcount", $row['freq']);
	    $i++;
	}

	return($xml);

    }

    public static function getRecentTracks($user, $limit) {
	global $mdb2;

	if (!isset($limit)) {
	    $limit = 10;
	}

	$res = $mdb2->query("SELECT Track . * , COUNT( * ) AS freq
	    FROM Track, Scrobbles
	    WHERE Scrobbles.username = " . $mdb2->quote($user, 'text') . "
	    AND Scrobbles.track = Track.name
	    GROUP BY Track.name
	    LIMIT 10");

	if (PEAR::isError($res) || !$res->numRows()) {
	    return(XML::error("error", "7", "Invalid resource specified"));
	}

	$xml = new SimpleXMLElement("<lfm status=\"ok\"></lfm>");
	$root = $xml->addChild("recenttracks", null);
	$root->addAttribute("user", $user);

	while (($row = $res->fetchRow(MDB2_FETCHMODE_ASSOC))) {
	    $track = $root->addChild("track", null);
	    $artist = $track->addChild("artist", $row['artist']);
	    $artist->addAttribute("mbid", $row['artmbid']);
	    $track->addChild("name", htmlentities($row['name']));
	}

	return($xml);

    }	
}
?>
