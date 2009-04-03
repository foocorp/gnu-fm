
<?
// this file writes backloged files to the database in a nice way

//defines path to backlog files
require ("cachewriter.php");
require ("/www/htdocs/audioscrobbler.com/includes/functions.php");
require ("/www/htdocs/audioscrobbler.com/includes/sessions.php");
require ("dbConnect.php");
if (!getDatabase()) exit;

$sql="INSERT INTO audio_data(artist,song,date,unixtimestamp,user,filename,seconds,version) VALUES \n";
$mf = count($backlogfiles);
print "There are $mf cache files..\n";
//$mf=1;//////////////////////////////////////
$userinfo=array();

for ($i=0; $i<$mf;$i++){
	print "Reading file ".$backlogfiles[$i] . "... ";

	echo "*".@passthru("mv ".$backlogfiles[$i] ." ".$wiproot."/workinprogress.cache")."*\n";
	//$failed=0;
	//while(!rename($backlogfiles[$i], "$wiproot/workinprogress.cache") && $failed <10) $failed++;

//	print "Moved file to $wiproot/workinprogress.cache\n";
	$handle = @fopen ("$wiproot/workinprogress.cache", "r");
	if (!$handle){ print "Nope..\n"; continue; }
	print "Yep...\n";
	while ($handle && !feof ($handle)) {
		//print "getting another line\n";
		$buffer = @fgets($handle);
		if (strlen($buffer)<30) continue;

		echo ".";
		//$str ="$username::::$seconds::::$art::::$tit::::$time::::$uts::::$path::::$version";
		list($username,$seconds,$artist,$song,$time,$unixtime,$path,$version) = explode("::::",$buffer);
		$version=str_replace("\n","",$version);
		$artist=trim($artist); $song=trim($song);
		if(strlen($artist)>0 && strlen($song)>0 && !($artist==$userinfo[$username]["lastartist"] && $song==$userinfo[$username]["lastsongtit"])){
		$userinfo[$username]["count"]++;
		$userinfo[$username]["totalseconds"]+=$seconds;
		$userinfo[$username]["lastsong"]=$artist . " - ". $song;
		$userinfo[$username]["lastartist"]=$artist;
		$userinfo[$username]["lastsongtit"]=$song;
		$userinfo[$username]["lastsubtime"]=$unixtime;
		if (strlen($userinfo[$username]["firstlastsubtime"])<1) $userinfo[$username]["firstlastsubtime"]=$unixtime;
		$sql .= "('$artist','$song','$time',$unixtime,'$username','$path',$seconds,'$version'),\n";
		}else{
			print "Skipping $username: $artist - $song \n";
		}	
	}
	@fclose ($handle);
	unset($handle);
	print "Deleting wip file ... ";
	print "*". @passthru("rm -f $wiproot/workinprogress.cache") . "*\n";


}


if(strlen($sql)<100) exit;

//remove trailing comma
$sql = substr($sql,0,strlen($sql)-2);
//print "SQL=\n$sql \n\n";
print "Mysql extended insert statement:".mysql_query($sql)."\n";

print "\n\n----\n\n";



$lastuser="";
foreach($userinfo as $user => $ar){
/*	if ($user!=$lastuser){
		$lastuser=$user;
		//get their last subtime and check if session ends, in which case calc simdata
		$row=mysql_fetch_row(mysql_query("select lastsubtime from pn_users where pn_uname='$user'"));
		$then=$row[0];
		if ($then<1) $then=99999;
		if(($then+900)<$ar["firstlastsubtime"]){
			print "hasFinishedSession('$user')\n";
			hasFinishedSession($user); 	
		}
	}*/
	//print $user . "  -  " . $ar["count"] . " -- " .$ar["totalseconds"] . "\n";
	$sq="UPDATE pn_users set count=count+".$ar["count"].",totalseconds=totalseconds+".$ar["totalseconds"].",lastsong='".str_replace("\n","",$ar["lastsong"])."',lastsubtime=".$ar["lastsubtime"]." where pn_uname='$user'\n";
	print "\nupdating pn_users for ($user): " . mysql_query($sq);
}
/*

//update globstats, and realtime stats

//globstats:
//assumes db connection exists
$rt = mysql_query("SELECT count(pn_uid), sum(count), sum(totalseconds) FROM pn_users");
$ts = mysql_fetch_row($rt);
$a=array();
$a["numusers"] = $ts[0] - 1;
$a["numsongs"] = $ts[1];
$a{"totalsecs"] = $ts[2];
mysql_query("delete from audio_cache where id='globalstats'");
mysql_query("insert into audio_cache(id,data,birthdate,birthdatetext) values('globalstats','".base64_encode(serialize($a))."',unix_timestamp(), now()");


*/
//realtime stats (TODO)


?>
