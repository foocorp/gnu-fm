<?
// Implements the submissions handshake protocol 1.1 as detailed at: http://www.audioscrobbler.net/wiki/Protocol1.1.merged
//
// By sending the timestamp as the md5 challenge then creating the session key from md5(md5($password) . $timestamp) we can
// force a 1.1 client to give us a session key that can be used by the 1.2 protocol handler, so we only handle handshakes for
// 1.1 then pass all submissions off to the 1.2 handler.

require_once('auth-utils.php');
require_once('config.php');

$supported_protocols = array("1.1");


if(!isset($_GET['p']) || !isset($_GET['u'])) {
	die("FAILED\n");
}

$protocol = $_GET['p']; $username = $_GET['u']; 

if(!in_array($protocol, $supported_protocols))  {
	die("FAILED Unsupported protocol version\n");
}

$timestamp = time();

$res = $mdb2->query("SELECT password FROM Users WHERE username = ". $mdb2->quote($username, "text"));
if(PEAR::isError($res)) {
	die("FAILED " . $res->getMessage() . "\n");
}
if(!$res->numRows()) {
	die("BADUSER\n");
}
$password = $res->fetchOne(0);
$session_id = md5($password . $timestamp);
$res = $mdb2->query("INSERT INTO Scrobble_Sessions(username, sessionid, expires) VALUES ("
	. $mdb2->quote($username, "text") . ","
	. $mdb2->quote($session_id, "text") . ","
	. $mdb2->quote(time() + 86400) . ")");

if(PEAR::isError($res)) {
        die("FAILED " . $res->getMessage() . "\n");
}

echo "UPTODATE\n";
echo $timestamp . "\n";
echo $submissions_server . "/submissions/1.2/\n";
echo "INTERVAL 0\n";

?>
