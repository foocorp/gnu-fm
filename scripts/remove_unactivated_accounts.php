<?php

/* GNU FM -- a free network service for sharing your music listening habits

   Copyright (C) 2009 Free Software Foundation, Inc

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

// Requires a valid config like gnukebox / nixtape
require_once('config.php');
require_once('adodb/adodb-exceptions.inc.php');
require_once('adodb/adodb.inc.php');

try {
	$adodb =& NewADOConnection($connect_string);
} catch (exception $e) {
	var_dump($e);
	adodb_backtrace($e->gettrace());
}

$sql_update = 'UPDATE AccountActivation SET expires = ' . (time()+(86400*2)) . 
	' WHERE expires < ' . time();

try {
	$adodb->Execute($sql);
	print "Updated field 'expires' in table.\n";
} catch (exception $e) {}

$sql = 'SELECT a.username,authcode,email FROM
	accountactivation a LEFT JOIN users u 
	ON a.username=u.username 
	WHERE u.active=0';

try {
	$res = $adodb->GetAll($sql);
	print "Fetched data.\n";
} catch (exception $e) {}

$headers = 'From: Libre.fm Account Activation <account@libre.fm>';
$subject = 'Libre.fm - Have you forgotten us?';

print "Mail body: $mail";

foreach($res as $row) {
	$username = $row['username'];
	$email = $row['email'];
	$authcode = $row['authcode'];

	$url = $base_url . '/register.php?auth=' . $authcode;

	print "Username: $username, URL: $url";

	$mail_body = "Hi!\n\nHave you forgotten to activate your account at Libre.fm? If so, just follow this link to activate
		your account within 48 hours, after which time your profile and activation code will be permanently deleted from 
		our database.\n\n";
	$mail_body .= $url . "\n\n - The Libre.fm Team";

	//mail($email, $subject, $mail_body, $headers);
}

?>
