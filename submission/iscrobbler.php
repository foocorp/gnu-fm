<?
$log = false;

require ("../includes/functions.php");
require ("cachewriter.php");

//include ("../includes/sessions.php");

function logthis($txt){
	if (!$log) return;
	$filename ="iscrobbler.log";
        $handle= fopen($filename,'a');
	fputs($handle, $txt."\n");
        fclose($handle);
}


function reply($s){
	print $s;
	exit;
}
// This section checks to see if 'magic quotes' is on, and if so, it
// undoes its effect. This means that any strings with quotes or apostrophes
// in them will have to be manually escaped with addslashes() before
// being sent to the database.
if (get_magic_quotes_gpc()) {

	// Overrides GPC variables
	for (reset($HTTP_POST_VARS); list($k, $v) = each($HTTP_POST_VARS); )
		$$k = stripslashes($v);
	//for (reset($HTTP_POST_VARS); list($k, $v) = each($HTTP_POST_VARS); )
//		$$k = stripslashes($v);
//	for (reset($HTTP_COOKIE_VARS); list($k, $v) = each($HTTP_COOKIE_VARS); )
//		$$k = stripslashes($v);
 }
 
 
 //logthis($_SERVER['QUERY_STRING']);
 
$latestversion = "0.5.2i";
$downloadurl = "http://sourceforge.net/project/showfiles.php?group_id=74512&release_id=142877";


//get them variables the long way (modify for many submissions if needed)
$version=$_POST['version']; 
$artist=urldecode($_POST['artist']);
$title=urldecode($_POST['title']);
$duration=$_POST['duration'];
$filename=urldecode($_POST['filename']);
$time=$_POST['time'];
$username=urldecode($_POST['username']);
$password=$_POST['password'];

if ($duration<1 || $duration > 4000) $duration=200;

//print "username = '$username'  password = '$password' \n";

require("dbConnect.php");
getDatabase();


$qry = "SELECT pn_pass,lastsubtime,UNIX_TIMESTAMP(),lastsong, CURRENT_TIMESTAMP() FROM pn_users where pn_uname='" . $username . "'";
$result = mysql_query ($qry);

if(!$result) reply ("FAILED\nTemporary glitch? Database has vanished :)");

//check username+pass
if(mysql_num_rows($result)<1) reply("BAD PASS\nUsername invalid or database error");
$row=mysql_fetch_row($result);
if($password!=$row[0]) reply("BAD PASS\nBad password or database error");

$uts = $row[2];

// The password is correct at this point.

fixGayness($artist,$title); // tidys up badly named stuff
$artist=trim($artist);
$title=trim($title);

//print "after attempted correction: '$artist' - '$title'\n";

$artist = removeThe($artist);  // removes leading "The " from artist names.

$str ="$username::::$duration::::$artist::::$title::::$time::::$uts::::$filename::::$version";
saveData($str,$itunescache); 

if($version != $latestversion){
	reply("OK OUTOFDATE $downloadurl");
}else{
	reply("OK");
}
?>
