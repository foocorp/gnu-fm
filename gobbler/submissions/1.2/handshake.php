<?
// Implements the submissions handshake protocol as detailed at: http://www.last.fm/api/submissions

require_once('auth-utils.php');
require_once('config.php');

$supported_protocols = array("1.2", "1.2.1");


if(!isset($_GET['p']) || !isset($_GET['u']) || !isset($_GET['t']) || !isset($_GET['a'])) {
	die("BADAUTH\n");
}

$protocol = $_GET['p']; $username = $_GET['u']; $timestamp = $_GET['t']; $auth_token = $_GET['a'];

if(!in_array($protocol, $supported_protocols))  {
	die("FAILED Unsupported protocol version\n");
}

if(isset($_GET['api_key']) && isset($_GET['sk'])) {
	$authed = check_web_auth($username, $auth_token, $timestamp, $_GET['api_key'], $_GET['sk']);
} else {
	$authed = check_standard_auth($username, $auth_token, $timestamp);
}

if(!$authed) {
	die("BADAUTH\n");
}

$session_id = md5($auth_token . time());
$res = $mdb2->query("INSERT INTO Scrobble_Sessions(username, sessionid, expires) VALUES ("
	. $mdb2->quote($username, "text") . ","
	. $mdb2->quote($session_id, "text") . ","
	. $mdb2->quote(time() + 86400) . ")");

if(PEAR::isError($res)) {
	die("FAILED " . $res->getMessage() . "\n");
}

echo "OK\n";
echo $session_id . "\n";
echo $submissions_server . "/nowplaying/1.2/\n";
echo $submissions_server . "/submissions/1.2/\n";

?>
