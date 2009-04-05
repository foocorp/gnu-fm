<?

require_once("HTTP/Request.php");

if($argc != 5) {
	die("Usage: 'php5 import.php <username> <password> <server> <scrobble dump>\n");
}

$username = $argv[1];
$password = $argv[2];
$server = $argv[3];
$data = file($argv[4]);

$timestamp = time();
$token = md5(md5($password) . $timestamp);
$response = file($server . "/?hs=true&p=1.2&u=$username&t=$timestamp&a=$token");

if(trim($response[0]) != "OK") {
	die("Couldn't login\n");
}

$session_id = trim($response[1]);
$submissions_server = trim($response[3]);

$r = new HTTP_Request($submissions_server);
$r->setMethod(HTTP_REQUEST_METHOD_POST);

for($i = 0; $i < count($data); $i++) {
	$row = explode("\t", $data[$i]);

	$track = $row[0];
	$artist = $row[1];
	$time = strtotime($row[2]);
	if(!$time) {
		$time = time();
	}

	$r->addPostData('s', $session_id);
	$r->addPostData('a['.$i.']', $artist);
	$r->addPostData('t['.$i.']', $track);
	$r->addPostData('i['.$i.']', $time);
}

$r->sendRequest();
echo $r->getResponseBody();
?>
