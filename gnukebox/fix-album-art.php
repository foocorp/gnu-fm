<?php
/* Libre.fm -- a free network service for sharing your music listening habits

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

    @apache_setenv('no-gzip', 1);
    @ini_set('zlib.output_compression', 0);
    @ini_set('implicit_flush', 1);


header('Content-type: text/html; charset=utf-8');
require_once('database.php');

	$adodb->SetFetchMode(ADODB_FETCH_ASSOC);
	$res = $adodb->GetAll("SELECT name, artist_name, image, artwork_license FROM Album WHERE artwork_license IS NULL LIMIT 5000");

		if(!$res) {
			die("sql error");
		}
		foreach($res as &$row) {
		echo "<img width=50 src=" . go_get_album_art($row['artist_name'], $row['name']) ." />&nbsp;";

		sleep (3);
		for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }

		ob_implicit_flush(1)  ;
		}
		echo "</ol>";

	function go_get_album_art($artist, $album){
		global $adodb;

  $Access_Key_ID = "1EST86JB355JBS3DFE82"; // this is mattl's personal key :)

        $SearchIndex='Music';
$Keywords=urlencode($artist.' '.$album);
        $Operation = "ItemSearch";
$Version = "2007-07-16";
        $ResponseGroup = "ItemAttributes,Images";
$request=
        "http://ecs.amazonaws.com/onca/xml"
                . "?Service=AWSECommerceService"
. "&AssociateTag=" . $Associate_tag
. "&AWSAccessKeyId=" . $Access_Key_ID
. "&Operation=" . $Operation
. "&Version=" . $Version
. "&SearchIndex=" . $SearchIndex
. "&Keywords=" . $Keywords
. "&ResponseGroup=" . $ResponseGroup;

$aws_xml = simplexml_load_file($request) or die("xml response not loading");

$image = $aws_xml->Items->Item->MediumImage->URL;
       
       if (!$image) { $image = "/i/qm50.png"; $license="librefm";}

	if ($image) {

	 
          if ($license == "") { $license = "amazon"; }

	  $license = $adodb->qstr($license);
	  $image = $adodb->qstr($image);
	  $album = $adodb->qstr($album);
	  $artist = $adodb->qstr($artist);

		  $sql = ("UPDATE Album SET image = " 

			  . ($image) . ", "

			  . " artwork_license = "

			. ($license) . " WHERE artist_name = "
                        . ($artist) . " AND name = "
				      . ($album));

		  $res = $adodb->Execute($sql);

		if(!$res) {
		  die("FAILED query was :" . $sql);
		}

	return $image;
	}
}
?>
