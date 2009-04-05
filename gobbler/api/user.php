<?php
require_once('../database.php');
require_once('./xml.php');

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
	$user_node->addChild("location", $row['location']);

	return($xml);
    }
}
?>
