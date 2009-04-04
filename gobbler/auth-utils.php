<?
require_once('database.php');

function check_web_auth($username, $token, $timestamp, $api_key, $sk) {
	// Validates authentication using a web services token
	global $mdb2;

}


function check_standard_auth($username, $token, $timestamp) {
	// Validates authentication using a standard authentication token
	global $mdb2;

	$result = $mdb2->query("SELECT password FROM Users WHERE username=" . $mdb2->quote($username, 'text'));
	if (PEAR::isError($result) || !$result->numRows()) {
		// TODO: Log failures somewhere
		return false;
	}

	$pass = $result->fetchOne(0);
	$check_token = md5($pass . $timestamp);

	return $check_token == $token;
}


?>
