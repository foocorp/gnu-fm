<?
// Implements the submissions handshake protocol as detailed at: http://www.last.fm/api/submissions

require_once('auth-utils.php');

$supported_protocols = array("1.2", "1.2.1");

if(isset($_GET['hs'])) {
	//Handshake

	if(!isset($_GET['p']) || !isset($_GET['u']) || !isset($_GET['t']) || !isset($_GET['a'])) {
		die("BADAUTH");
	}

	$protocol = $_GET['p']; $username = $_GET['u']; $timestamp = $_GET['t']; $auth_token = $_GET['a'];

	if(!in_array($protocol, $supported_protocols))  {
		die("FAILED Unsupported protocol version");
	}

	if(isset($_GET['api_key']) && isset($_GET['sk'])) {
		$authed = check_web_auth($username, $auth_token, $timestamp, $_GET['api_key'], $_GET['sk']);
	} else {
		$authed = check_standard_auth($username, $auth_token, $timestamp);
	}

	if(!$authed) {
		die("BADAUTH");
	}

	echo "OK\n";
}


?>
