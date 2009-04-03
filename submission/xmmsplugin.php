<?
require ("../includes/functions.php");
require ("cachewriter.php");
//include ("../includes/sessions.php");
function logthis($txt){
//return; //DONT REALLY LOG?
	$filename ="./xmms.log";
        $handle= fopen($filename,'a');
	fputs($handle, $txt."\n");
        fclose($handle);
}

function reply($str){
echo "AUDIOSCROBBLERRESPONSE\n$str\0"; //wrap in protocol for plugin 
exit;
}

// This section checks to see if 'magic quotes' is on, and if so, it
// undoes its effect. This means that any strings with quotes or apostrophes
// in them will have to be manually escaped with addslashes() before
// being sent to the database.
if (get_magic_quotes_gpc()) {

	// Overrides GPC variables
	for (reset($HTTP_GET_VARS); list($k, $v) = each($HTTP_GET_VARS); )
		$$k = stripslashes($v);
	//for (reset($HTTP_POST_VARS); list($k, $v) = each($HTTP_POST_VARS); )
	//	$$k = stripslashes($v);
	//for (reset($HTTP_COOKIE_VARS); list($k, $v) = each($HTTP_COOKIE_VARS); )
//		$$k = stripslashes($v);
 }




$latestversion = "1.41x"; // import from somewhere sensible


parse_str($_SERVER["QUERY_STRING"]);

//reply("AUDIOSCROBBLERFAILED");

//logthis ("LOG: number of subs=".count($playlisttext) );
//logthis ("LOG: first [0] of playlistext = ".$playlisttext[0]);
$numsums = count($playlisttext); //how many submissions in this GET?


require("dbConnect.php");
getDatabase();

$qry = "SELECT pn_pass,lastsubtime,UNIX_TIMESTAMP(),lastsong, CURRENT_TIMESTAMP() FROM pn_users where pn_uname='" . $username . "'";
$result = mysql_query ($qry);

//check username+pass
if(mysql_num_rows($result)<1) reply("AUDIOSCROBBLERBADPASS\nUsername invalid or database error");
$row=mysql_fetch_row($result);
if($password!=$row[0]) reply("AUDIOSCROBBLERBADPASS\nBad password or database error");

//if submissions too close together, complain
//if (abs($row[1] - $row[2]) < 20) reply("AUDIOSCROBBLEROK\nSubmissions too close together, not ok :P");



//save data for insertion to database


$numsubs = count($playlisttext);

//detect if new session, if so then update audio_relations for previous session



$lastsubmittedsong="";
$str="";
$time=$row[4];
$uts = $row[2];
//if ($numsubs==0) $str="NO SUBS";
for ($index=0;$index<$numsubs;$index++){
	$txt = $playlisttext[$index];
	

	$seconds = $duration[$index];
	$path = $filename[$index];
	//$time = $timestamp[$index];

	
	if ($seconds==1 || $seconds==0 || $seconds==-1 ) $seconds=200; // default value	
	$art = substr($txt,0,strpos($txt," - "));
	$tit = substr($txt,strlen($art)+3);
//	logthis ("converted... art=$art   tit=$tit\n");
	fixGayness($art,$tit);
	$art = removeThe($art);
	//$tit = str_replace("\"","\\\"",$tit);
	//$art = str_replace("\"","\\\"",$art);
//	$path = str_replace("%27","\'",$path);
	//$art = mysql_escape_string($art);
	//$tit = mysql_escape_string($tit);
	//$path = mysql_escape_string($path);
	//if same as last song dont submit again.	
	if (strtoupper(($art." - ".$tit))==strtoupper($lastsubmittedsong)){
		if ($numsubs==1) reply("AUDIOSCROBBEROK\nBailed - Duplicate submission\n");
		continue;
	}

	if (strlen($art)<1 || strlen($tit)<1) continue;
			
	
	
	
	$lastsubmittedsong= $art . " - " . $tit;
	$str ="$username::::$seconds::::$art::::$tit::::$time::::$uts::::$path::::$version";
	saveData($str,$xmmscache); 
	//saveData( "$username::::$seconds::::('$art','$tit',$time,$uts,'$username','$path',$seconds,'$version')::::$lastsubmittedsong::::$time",$xmmscache);
	//logthis( "$username::::$seconds::::('$art','$tit',$time,unix_timestamp($time),'$username','$path',$seconds,'$version')::::$sng");
}//loop on index

//if out of date, complain
//if($version!=$latestversion) reply("AUDIOSCROBBLEROUTOFDATE\nVersion $latestversion is now available. Please upgrade :)");
reply("AUDIOSCROBBLEROK");

?>
