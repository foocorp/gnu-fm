<?php
require_once('PHPUnit/Framework.php');

class SubmissionsTest extends PHPUnit_Framework_TestCase
{
	// Check we can login with standard authentication
	public function testStandardAuth() {
		$result = $this->standardLogin("testuser", "password");
		$this->assertEquals("OK", trim($result[0]));
	}


	// Make sure we get BADAUTH if we give an incorrect password
	public function testFailedStandardAuth() {
		$result = $this->standardLogin("testuser", "icanhazsecurity?");
		$this->assertEquals("BADAUTH", trim($result[0]));
	}

	private function standardLogin($username, $password) {
		require("../config.php");

		$timestamp = time();
		$token = md5(md5($password) . $timestamp);
		$response = file($submissions_server . "/?hs=true&p=1.2&u=$username&t=$timestamp&a=$token");
		return $response;
	}

}
