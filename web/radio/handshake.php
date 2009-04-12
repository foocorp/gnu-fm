<?
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

// fakes the radio handshake protocol

if(!isset($_GET['username']) || !isset($_GET['passwordmd5'])) {
	die("BADAUTH\n");
}

echo "session=00000000000000000000000000000000\n"
echo "stream_url=this.is.broken.$_GET['username'].example.com\n"
echo "subscriber=0\n"
echo "framehack=0..\n"
echo "base_url=alpha.libre.fm\n"
echo "base_path=/radio\n"
echo "info_message=\n"
echo "fingerprint_upload_url=http://this.is.broken.example.com/fingerprint/upload.php\n"
echo "permit_bootstrap=0\n"
echo "freetrial=0\n"

?>
