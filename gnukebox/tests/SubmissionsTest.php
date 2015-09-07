<?php
/* GNUkebox -- a free software server for recording your listening habits

   Copyright (C) 2009, 2015 Free Software Foundation, Inc

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

require_once(__DIR__ . '/../vendor/autoload.php');

class SubmissionsTest extends PHPUnit_Framework_TestCase {
	// Check we can login with standard authentication
	public function testStandardAuth() {
		$result = $this->standardLogin('testuser', 'password');
		$this->assertEquals('OK', trim($result[0]));
		sleep(1);
	}


	// Make sure we get BADAUTH if we give an incorrect password
	public function testFailedStandardAuth() {
		$result = $this->standardLogin('testuser', 'icanhazsecurity?');
		$this->assertEquals('BADAUTH', trim($result[0]));
		sleep(1);
	}


	public function testScrobble() {
		$result = $this->standardLogin('testuser', 'password');
		$this->assertEquals('OK', trim($result[0]));

		$session_id = trim($result[1]);
		$scrobble_server = trim($result[3]);
		$result = $this->scrobble($scrobble_server, $session_id,
								  'Richard Stallman',
			'The Free Software Song', 'b25b9595-54ed-7605-8ac2-20b7b2e0a026');
		$this->assertEquals('OK', trim($result));
		sleep(1);
	}


	public function testNowPlaying() {
		$result = $this->standardLogin('testuser', 'password');
		$this->assertEquals('OK', trim($result[0]));

		$session_id = trim($result[1]);
		$nowplaying_server = trim($result[2]);
		$result = $this->nowPlaying($nowplaying_server, $session_id,
									'The Libre.fm Players', 'Let Freedom Ring',
			'b25b9595-54ed-7605-8ac2-20b7b2e0a026');
		$this->assertEquals('OK', trim($result));
		sleep(1);
	}


	private function standardLogin($username, $password) {
		require('../config.php');

		$timestamp = time();
		$token = md5(md5($password) . $timestamp);
		$response = file($submissions_server . "/?hs=true&p=1.2&u=$username&t=$timestamp&a=$token&c=unittest");
		return $response;
	}


	private function scrobble($server, $session_id, $artist, $track) {
		$r = new HttpRequest($server);
		$r->setMethod(HTTP_METH_POST);
		$r->addPostFields(['s'=>$session_id,
						   'a[0]'=>$artist,
						   't[0]'=>$track,
						   'i[0]'=>time(),
						   'm[0]'=>$mbid,
						   'o[0]'=>'U']);
		$r->send();
		return $r->getResponseBody();
	}


	private function nowPlaying($server, $session_id, $artist, $track, $mbid) {
		$r = new HttpRequest($server);
		$r->setMethod(HTTP_METH_POST);
		$r->addPostFields(['s'=>$session_id,
						   'a'=>$artist,
						   't'=>$track]);
		$r->send();
		return $r->getResponseBody();
	}
}
