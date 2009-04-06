<?php
require_once('PHPUnit/Framework.php');
require_once('HTTP/Request.php');

class SubmissionsTest extends PHPUnit_Framework_TestCase
{
	// Check we can login with standard authentication
	public function testStandardAuth() {
		$result = $this->standardLogin("testuser", "password");
		$this->assertEquals("OK", trim($result[0]));
		sleep(1);
	}


	// Make sure we get BADAUTH if we give an incorrect password
	public function testFailedStandardAuth() {
		$result = $this->standardLogin("testuser", "icanhazsecurity?");
		$this->assertEquals("BADAUTH", trim($result[0]));
		sleep(1);
	}


	public function testScrobble() {
		$result = $this->standardLogin("testuser", "password");
		$this->assertEquals("OK", trim($result[0]));

		$session_id = trim($result[1]);
		$scrobble_server = trim($result[3]);
		$result = $this->scrobble($scrobble_server, $session_id, "Richard Stallman", 
		    "The Free Software Song", "b25b9595-54ed-7605-8ac2-20b7b2e0a026");
		$this->assertEquals("OK", trim($result));
		sleep(1);
	}


	public function testNowPlaying() {
		$result = $this->standardLogin("testuser", "password");
		$this->assertEquals("OK", trim($result[0]));

		$session_id = trim($result[1]);
		$nowplaying_server = trim($result[2]);
		$result = $this->nowPlaying($nowplaying_server, $session_id, "The Libre.fm Players", "Let Freedom Ring",
		    "b25b9595-54ed-7605-8ac2-20b7b2e0a026");
		$this->assertEquals("OK", trim($result));
		sleep(1);
	}


	private function standardLogin($username, $password) {
		require("../config.php");

		$timestamp = time();
		$token = md5(md5($password) . $timestamp);
		$response = file($submissions_server . "/?hs=true&p=1.2&u=$username&t=$timestamp&a=$token");
		return $response;
	}


	private function scrobble($server, $session_id, $artist, $track) {
		$r = new HTTP_Request($server);
		$r->setMethod(HTTP_REQUEST_METHOD_POST);
		$r->addPostData('s', $session_id);
		$r->addPostData('a[0]', $artist);
		$r->addPostData('t[0]', $track);
		$r->addPostData('i[0]', time());
		$r->addPostData('m[0]', $mbid);
		$r->addPostData('o[0]', 'U');
		$r->sendRequest();
		return $r->getResponseBody();
	}


	private function nowPlaying($server, $session_id, $artist, $track, $mbid) {
		$r = new HTTP_Request($server);
		$r->setMethod(HTTP_REQUEST_METHOD_POST);
		$r->addPostData('s', $session_id);
		$r->addPostData('a', $artist);
		$r->addPostData('t', $track);
		$r->sendRequest();
		return $r->getResponseBody();
	}
}
