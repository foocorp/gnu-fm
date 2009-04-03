<?

/*

Example script to predict how much i'll like an aritst
call with query string: ?usecache=yes&artist=Rage%20Against%20the%20Machine   for example

*/

//require("dbConnect.php");
//getDatabase();

set_time_limit (0);

$dblink = mysql_connect("localhost", "josh" , "");
mysql_select_db("audioscrobbler");

//echo "finding artists similar to $artist by looking at peoples top artists\n";

function calcProfile($who){
	print "calcProfile($who)\n";
	$q = mysql_query("select artist,count(artist) as c from audio_data where user='$who' group by artist order by c desc limit 100 ");

	$good = array();$bad=array();

	while ($row = mysql_fetch_row($q)){
		if ($row[1]>2) $good[$row[0]]=$row[1];
	}

	$qq = mysql_query("select artist,count(artist) as c from audio_data where user='$who' group by artist order by c asc ");

	$bad = array();

	while ($row = mysql_fetch_row($qq)){
		if($row[1]<6) $bad[$row[0]]=$row[1];
	}

	$bad=array_reverse($bad);
	$max=0;
	$rank=0;
	$mingood=0;

	$scores = array();

	foreach($good as $a => $s){
		if ($s<20) break;
		if ($max==0) $max=log($s);
		$s = (log($s)*3/$max)+4;
		if ($s==0) $s=1 ;
		$s=round($s);
		print ++$rank . ") \t $s \t $a \n";
		$scores[$a]=$s;
		$mingood=$s;
	}

	$max=0;
	$rank=0;

	foreach($bad as $a => $s){
		if($s>5 || $s>=$mingood) break;
		if($max==0) $max=$s;
		$s=round($s*3/$max);
		if ($s==0) $s=1 ;
		//$s=round($s);
		print ++$rank.") \t $s \t $a \n";
		$scores[$a]=$s;
	}
	mysql_query("delete from acf_profiles where user='$who'");
	mysql_query("insert into acf_profiles(artistscores,user) values('".base64_encode(serialize($scores))."','$who')");
	return $scores;

} //end func

function readProfile($who){
	$q=mysql_query("select artistscores from acf_profiles where user='$who'");
	if (mysql_num_rows($q)==0) {
		print "WARNING: profile for $who not found... calculating it now";
		calcProfile($who);
		$q=mysql_query("select artistscores from acf_profiles where user='$who'");
	}

	while($row=mysql_fetch_row($q)) return unserialize(base64_decode($row[0]));
}

function loadProfiles(){
	$filename = "d:/web/audioscrobbler/profiles.cache";
	$handle = fopen ($filename, "rb");
	$contents = fread ($handle, filesize ($filename));
	fclose ($handle);
	return unserialize($contents);
}

function saveProfiles($arr){
	print "\nSAVING PROFILES.\n";
	$filename = "d:/web/audioscrobbler/profiles.cache";
	$handle = fopen($filename, 'wb');
	fwrite($handle, serialize($arr));
	fclose($handle);
}

function mylog($txt){
//print $txt . "\n";
//flush();
}

//get a users top artists as percentages
function getTopArtistsByUser($uname){
	$sql = "select artist,count(artist) as freq from audio_data where user='" . mysql_escape_string($uname) . "' group by artist order by freq desc";
	$last = mysql_query($sql);
	$result=array();
	$max=0;
	while ($last10 = mysql_fetch_row($last)){
		if ($max==0) $max=$last10[1];
		if ($last10[1]<5) break;
		$result[$last10[0]]=$last10[1]/$max;
	}
	return $result;
}

function predictArtistRating($user,$predictartist,$topartsforall,$myNN, &$accuracy){

	$myNNweights = array();
	$maxNN =0;
	foreach($myNN as $u => $s){
		if ($maxNN==0) $maxNN=$s;
		$myNNweights[$u] = $s/$maxNN;
	}

	//print "working out how much you like $predictartist\n";


	$sumws=0;
	$matches=0;
	$matchesw=0;

	foreach($myNN as $u => $s){
		if (isset( $topartsforall[$u][$predictartist] )){
			$sumws += $topartsforall[$u][$predictartist] * log(1+ $myNNweights[$u]);
			$matches++;
			$matchesw += $myNNweights[$u];
			//print "$u \t ". $topartsforall[$u][$predictartist] ."\n";
		}
	}
	if ($matchesw==0) return -1; 
	$answer = $sumws/$matchesw;
	$accuracy = ($matchesw/$matches);
	return $answer;
}

//get array of who is similar to a certain user  [user]=score
function getSimilarUsers($who, $topartsforall){
	$result = array();
	$mytopartists=getTopArtistsByUser($who);
	$qu = mysql_query("select pn_uname from pn_users where count>500");
	while ($ru = mysql_fetch_row($qu)){
		$user = $ru[0];
		if ($who==$user) continue; // dont compare me to myself :)
		
		flush();
		$toparts =  $topartsforall[$user]; //getTopArtistsByUser($user);
		///print "$user (". count($toparts) >")\n";
		flush();
		$weight=0;$cnt=0;
		foreach($toparts as $art => $score){
			if (array_key_exists($art,$mytopartists)){
				$weight+=($mytopartists[$art] + $score) /2;
				$cnt++;	
			}
		}
		if ($cnt>3) $result[$user]=$weight;
	}
	arsort($result);
	return $result;
}

$me = $_GET['user'];//"rj";

$topartsforall = array();

if (isset($_GET['usecache'])){
	$topartsforall = loadProfiles();
}else{
	$qu = mysql_query("select pn_uname from pn_users where count>500");
	while ($ru = mysql_fetch_row($qu)){
	$topartsforall[$ru[0]]=getTopArtistsByUser($ru[0]);
	}
	saveProfiles($topartsforall);
}

if (!is_array($topartsforall[$me])){
	print "\n**\n**\n** WARNING: your profile is not expansive enough yet... cannot create kNN set.\n** (username is case sensitive - maybe it just didnt recognise you?)\n**\n**";
	exit;
}

print "\n\n";

$allscores=array();

$u = mysql_query("select pn_uname from pn_users where count>500");
while ($row = mysql_fetch_row($u)){
	$allscores[$row[0]]=readProfile($row[0]);
}

print "<pre>Hello $me. This script attempts to find users that are similar to you, then displays which artists you might like based on those users\nCompare the suggesteions against the values shown in your profile below\n\n";

print "Accessing a subset of ".count($allscores)." user profiles for k-Nearset-Neighbour calculations\n\n";
flush();

$predictartist=$_GET['artist'];//"Aerosmith";

if(strlen($predictartist)>0){ 
	$accuracy=0;
	$answer = predictArtistRating($me,$predictartist,$topartsforall, getSimilarUsers($me,$topartsforall),$accuracy); 

	print "\n\nApparently, you should like $predictartist : ( $answer )  accuracy = ". round($accuracy*100,2)." \n";

	if (isset($topartsforall[$me][$predictartist])){
		print "You actually like $predictartist : ( ".$topartsforall[$me][$predictartist]." )\n";
		$diff = ($topartsforall[$me][$predictartist] - $answer);
		print "Difference [predict - actual] : $diff \n";
		$error = round(( $answer - $topartsforall[$me][$predictartist])/$topartsforall[$me][$predictartist] , 2);
		print "The error in the prediction is $error \n\n";
	}
}

// work out other artists i should listen to

print "Scanning for possible new artists to suggest\n";

$artistpredictions = array();
$checkedcounter=0;
$compareusers=0;
foreach ($topartsforall as $u => $profile){
	$compareusers++;
	if ($compareusers>25) break;
	print $u;
	$checkedcounter++;
	if ($checkedcounter>25){
		print "OK\n";
		break;
	}
	$rank=0;
	foreach($profile as $artist => $score){
		$rank++;
		if ($rank>10) continue;
		if (!(isset($artistpredictions[$artist])) && !(isset($topartsforall[$me][$artist]))){
			print "." ; flush();
			//print "$artist... ";flush();
			// unknown artist to me.. predict it!
			$accuracy=0;
			$pred = predictArtistRating($me,$artist,$topartsforall, getSimilarUsers($me,$topartsforall),$accuracy);
			if ($pred>0) $artistpredictions[$artist] = $pred;
			//print "predicted ". $artistpredictions[$artist] . " ---> $artist \n";
		}
	}
} 

arsort($artistpredictions);

print "\n\nThere are " . count($artistpredictions) . " artist predictions available\n";

print "\n\n Predictions for NEW artists you could try:\n\n";
foreach($artistpredictions as $art => $s){
	print round($s,4)." \t $art<br>";
}



print "\n\n\n$me's profile:\n";
foreach ($topartsforall[$me] as $a => $s){
	print round($s,4)." \t $a \n";
}


mysql_close;
?>