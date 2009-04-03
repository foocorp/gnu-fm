<?php

$log = false;

include ("../includes/functions.php");
require("cachewriter.php");
require("dbConnect.php");
getDatabase();

function logthis($txt){
	if (!$log) return;
	$filename ="winamp3.log";
        $handle= fopen($filename,'a');
	fputs($handle, $txt."\n");
        fclose($handle);
}

// This section checks to see if 'magic quotes' is on, and if so, it
// undoes its effect. This means that any strings with quotes or apostrophes
// in them will have to be manually escaped with addslashes() before
// being sent to the database.
if (get_magic_quotes_gpc()) {

	// Overrides GPC variables
//	for (reset($HTTP_POST_VARS); list($k, $v) = each($HTTP_POST_VARS); )
//		$$k = stripslashes($v);
	for (reset($HTTP_GET_VARS); list($k, $v) = each($HTTP_GET_VARS); )
		$$k = stripslashes($v);
///	for (reset($HTTP_COOKIE_VARS); list($k, $v) = each($HTTP_COOKIE_VARS); )
//		$$k = stripslashes($v);
 }
 
 
//logthis($_SERVER['QUERY_STRING']);
 
$latestversion = "0.1w3";

$downloadurl = "http://audioscrobbler.com/modules.php?op=modload&name=Downloads&file=index";


//get them variables the long way (modify for many submissions if needed)
$version=$_GET['version']; 
$artist=urldecode($_GET['artist']);
$title=urldecode($_GET['title']);
$duration=$_GET['duration'];
$filename=urldecode($_GET['filename']);
$time=$_GET['time'];
$username=urldecode($_GET['username']);
$password=$_GET['password'];

if ($duration<1 || $duration > 4000) $duration=200; // sanity check


function reply($s){
print $s; exit;
}


$qry = "SELECT pn_pass,lastsubtime,UNIX_TIMESTAMP(),lastsong, CURRENT_TIMESTAMP() FROM pn_users where pn_uname='" . $username . "'";
$result = mysql_query ($qry);

if(!$result) reply ("FAILED\nTemporary glitch? Database has vanished :)");

//check username+pass
if(mysql_num_rows($result)<1) reply("BADPASS\nUsername invalid or database error");
$row=mysql_fetch_row($result);
if($password!=$row[0]) reply("BADPASS\nBad password or database error");

$uts = $row[2];

// The password is correct at this point.

fixGayness($artist,$title); // tidys up badly named stuff
$artist=trim($artist);
$title=trim($title);

//print "after attempted correction: '$artist' - '$title'\n";

$artist = removeThe($artist);  // removes leading "The " from artist names.

$str ="$username::::$duration::::$artist::::$title::::$time::::$uts::::$filename::::$version";
saveData($str,$wa3cache); 

if($version != $latestversion){
	reply("OUTOFDATE\n$downloadurl");
}else{
	reply("OK");
}

?>
