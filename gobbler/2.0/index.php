<?php

require_once('../database.php');
require_once('../api/artist.php');
require_once('../api/user.php');

# Error constants
define("LFM_INVALID_SERVICE",       2);
define("LFM_INVALID_METHOD",        3);
define("LFM_INVALID_TOKEN",         4);
define("LFM_INVALID_FORMAT",        5);
define("LFM_INVALID_PARAMS",        6);
define("LFM_INVALID_RESOURCE",      7);
define("LFM_TOKEN_ERROR",           8);
define("LFM_INVALID_SESSION",       9);
define("LFM_INVALID_APIKEY",       10);
define("LFM_SERVICE_OFFLINE",      11);
define("LFM_SUBSCRIPTION_ERROR",   12);
define("LFM_INVALID_SIGNATURE",    13);
define("LFM_SUBSCRIPTION_REQD",    18);

# Error descriptions as per API documentation
$error_text = array(
	LFM_INVALID_SERVICE         => "Invalid service -This service does not exist",
	LFM_INVALID_METHOD          => "Invalid Method - No method with that name in this package",
	LFM_INVALID_TOKEN           => "Invalid authentication token supplied",
	LFM_INVALID_FORMAT          => "Invalid format - This service doesn't exist in that format",
	LFM_INVALID_PARAMS          => "Invalid parameters - Your request is missing a required parameter",
	LFM_INVALID_RESOURCE        => "Invalid resource specified",
	LFM_TOKEN_ERROR             => "There was an error granting the request token. Please try again later",
	LFM_INVALID_SESSION         => "Invalid session key - Please re-authenticate",
	LFM_INVALID_APIKEY          => "Invalid API key - You must be granted a valid key by last.fm",
	LFM_SERVICE_OFFLINE         => "Service Offline - This service is temporarily offline. Try again later.",
	LFM_SUBSCRIPTION_ERROR      => "Subscription Error - The user needs to be subscribed in order to do that",
	LFM_INVALID_SIGNATURE       => "Invalid method signature supplied",
	LFM_SUBSCRIPTION_REQD       => "This user has no free radio plays left. Subscription required."
);

# Resolves method= parameters to handler functions
$method_map = array(
	"auth.gettoken"             => method_auth_gettoken,
	"auth.getsession"           => method_auth_getsession,
	"artist.gettoptracks"       => method_artist_gettoptracks,
	"user.getinfo"		    => method_user_getinfo,
	"user.gettoptracks"	    => method_user_gettoptracks
    );

function method_user_gettoptracks() {
    if (!isset($_GET['user'])) {
	report_failure(LFM_INVALID_SIGNATURE);
    }

    header("Content-Type: text/xml");
    print(XML::prettyXML(User::getTopTracks($_GET['user'], $_GET['period'])));
}

function method_user_getinfo() {
    if (!isset($_GET['user'])) {
	report_failure(LFM_INVALID_SIGNATURE);
    }
    header("Content-Type: text/xml"); 
    print(XML::prettyXML(User::getInfo($_GET['user'])));
}

function method_artist_gettoptracks() {
    if (!isset($_GET['artist'])) {
	report_failure(LFM_INVALID_SIGNATURE);
    }
    header("Content-Type: text/xml"); 
    print(XML::prettyXML(Artist::getTopTracks($_GET['artist'])));

}

function method_auth_gettoken() {
	global $mdb2;
	
	if (!isset($_GET['api_sig']) || !valid_api_sig($_GET['api_sig']))
		report_failure(LFM_INVALID_SIGNATURE);
	
	$key = md5(time() . rand());
	
	$result = $mdb2->query('INSERT INTO Auth (token, expires) VALUES ('
		. $mdb2->quote($key, 'text') . ", "
		. $mdb2->quote(time() + 3600, 'integer')
		. ")");
	if (PEAR::isError($result))
		report_failure(LFM_SERVICE_OFFLINE);
	
	print("<lfm status=\"ok\">\n");
	print("    <token>$key</token></lfm>");
}

function method_auth_getsession() {
	global $mdb2;
	
	if (!isset($_GET['api_sig']) || !valid_api_sig($_GET['api_sig']))
		report_failure(LFM_INVALID_SIGNATURE);
	
	if (!isset($_GET['token']))
		report_failure(LFM_INVALID_TOKEN);
	
	// Check for a token that (1) is bound to a user, and (2) is not bound to a session
	$result = $mdb2->query('SELECT username FROM Auth WHERE '
		. 'token = ' . $mdb2->quote($_GET['token'], 'text') . ' AND '
		. 'username IS NOT NULL AND sk IS NULL');
	if (PEAR::isError($result))
		report_failure(LFM_SERVICE_OFFLINE);
	if (!$result->numRows())
		report_failure(LFM_INVALID_TOKEN);
	
	$username = $result->fetchOne(0);
	$session = md5(time() . rand());
	
	// Update the Auth record with the new session key
	$result = $mdb2->query('UPDATE Auth SET '
		. 'sk = ' . $mdb2->quote($session, 'text') . ' WHERE '
		. 'token = ' . $mdb2->quote($_GET['token'], 'text'));
	if (PEAR::isError($result))
		report_failure(LFM_SERVICE_OFFLINE);
	
	print("<lfm status=\"ok\">\n");
	print("    <session>\n");
	print("        <name>$username</name>\n");
	print("        <key>$session</key>\n");
	print("        <subscriber>0</subscriber>\n");
	print("    </session>\n");
	print("</lfm>");
}

function valid_api_key($key) {
	return strlen($key) == 32;
}

function valid_api_sig($sig) {
	return strlen($sig) == 32;
}

function report_failure($code) {
	global $error_text;
	
	print("<lfm status=\"failed\">\n");
	print("    <error code=\"$code\">".$error_text[$code]."</error></lfm>");
	die();
}

if (!isset($_GET['method']) || !isset($method_map[$_GET['method']]))
	report_failure(LFM_INVALID_METHOD);

if (!isset($_GET['api_key']) || !valid_api_key($_GET['api_key']))
	report_failure(LFM_INVALID_APIKEY);

$method = $method_map[$_GET['method']];
$method();

?>
