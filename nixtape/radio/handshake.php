<?php
/* Libre.fm -- a free network service for sharing your music listening habits

   Copyright (C) 2009 Libre.fm Project

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

require_once('../database2.php');

if(!isset($_GET['username']) || !isset($_GET['passwordmd5'])) {
	die("BADAUTH\n");
}

$username = $_GET['username'];
$passmd5 = $_GET['passwordmd5'];

$res = $adodb->GetOne('SELECT username FROM Users WHERE username = ' . $adodb->qstr($username) . ' AND password = ' . $adodb->qstr($passmd5));
if (!$res) {
	die("BADAUTH\n");
}

$session = md5($passmd5 . time());

$adodb->Execute('DELETE FROM Radio_Sessions WHERE expires < ' . (int)(time()));

$adodb->Execute('INSERT INTO Radio_Sessions (username, session, expires) VALUES ( ' . $adodb->qstr($username) . ', ' . $adodb->qstr($session) . ', ' . (int)(time() + 259200) . ')');

echo 'session=' . $session . "\n";
echo "stream_url=this.is.broken.{$username}.example.com\n";
echo "subscriber=0\n";
echo "framehack=0..\n";
echo "base_url=alpha.libre.fm\n";
echo "base_path=/radio\n";
echo "info_message=\n";
echo "fingerprint_upload_url=http://this.is.broken.example.com/fingerprint/upload.php\n";
echo "permit_bootstrap=0\n";
echo "freetrial=0\n";

?>
