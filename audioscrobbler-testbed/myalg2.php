<pre>
<?

/*

Example script to predict how much i'll like an aritst
call with query string: ?usecache=yes&artist=Rage%20Against%20the%20Machine&user=rj  for example

*/

// these 2 lines establish a database connection, and select the right database. change them to suit.
set_time_limit (0);

$dblink = mysql_connect("localhost", "josh" , "");
mysql_select_db("audioscrobbler");

//
// pass it a username, it calculates that users "profile"
// returns an assoc array   $myarray["artist"]=score  all scores normalised to 1
// the first entry has value 1 (the most listened to) they go down in order.
// 
function calcProfile($who){

	print "calcProfile($who)...\n"; flush();
	$sql = "select artist,count(artist) as freq from audio_data where user='" . mysql_escape_string($who) . "' group by artist order by freq desc";
	$last = mysql_query($sql);
	$result=array();
	$cnt=0;
	$max=0;
	while($row = mysql_fetch_row($last)){
		if ($max==0) $max=$row[1];
		if($cnt++>50) break;
		$result[$row[0]]=($row[1]/$max);
		//print ($row[1]/$max) . "\t - \t".$row[0]."\n"; flush(); 
	}
	//now put their profile (the $result array) in the database
	mysql_query("delete from acf_profiles where user='$who'");
	mysql_query("insert into acf_profiles(artistscores,user) values('".base64_encode(serialize($result))."','$who')");
	return $result;
	
	
} //end func


//grab a users profile from the database cache
function readProfile($who,$force=false){
	$q=mysql_query("select artistscores from acf_profiles where user='$who'");
	if (mysql_num_rows($q)==0 || $force) {
		print "WARNING: rebuilding top-artists profile for $who .. ";
		calcProfile($who); //dum-dee-dum...
		$q=mysql_query("select artistscores from acf_profiles where user='$who'");
	}
	while($row=mysql_fetch_row($q)) return unserialize(base64_decode($row[0])); //should only be one value in the db, return it.
}

//load big array of everyones profiles from disk
function loadAllProfiles(){
	$filename = "d:/web/audioscrobbler/profiles.cache";
	$handle = fopen ($filename, "rb");
	$contents = fread ($handle, filesize ($filename));
	fclose ($handle);
	return unserialize($contents);
}

//save big array of everyones profiles to disk
function saveAllProfiles($arr){
	print "\nSAVING PROFILES.\n";
	$filename = "d:/web/audioscrobbler/profiles.cache";
	$handle = fopen($filename, 'wb');
	fwrite($handle, serialize($arr));
	fclose($handle);
}

//doesnt do alot.
function mylog($txt){
//print $txt . "\n";
//flush();
}

// synonym of readProfile
function getTopArtistsByUser($uname){
	return readProfile($uname); // grab this users top artist profile
}


/*
 returns a value in the same range as the 0-1 values in your profile
 which should be the amount that you'll like the specified artist.
 $user = user you are predicting for
 $predictartist = name of artist to predict a value for
 $topartsforall = array["user"]["artist"]=score   --- everyones profiles in one big array (saved in file?)
 $myNN = weighted array of users similar to $user   ($myNN["username"]=similarity-score)
 &$accuracy is set to the amount of confidence of the prediction.. (bit weird)
*/
function predictArtistRating($user,$predictartist,$topartsforall,$myNN, &$accuracy){
	$myNNweights = array();
	$maxNN =0;
	//normalise to 1
	foreach($myNN as $u => $s){
	if ($maxNN==0) $maxNN=$s;
	$myNNweights[$u] = $s/$maxNN;
	}
	//print "working out how much you like $predictartist\n";
	$sumws=0;
	$matches=0;
	$matchesw=0;
	
	// this sees who else from your myNN array listens to this artist
	// and tries to work out.. god i've forgotten exactly what it does.. read the code :P
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

//get array of who is similar to a certain user  
//returns: [user]=score array
//pass it $user = usename of person to find matches for
//pas it $topartsforall = big array of everyones profiles (read from file)
function getSimilarUsers($who, $topartsforall){
	$result = array();
	$mytopartists=readProfile($who);
	$qu = mysql_query("select pn_uname from pn_users where count>500"); // only bother wit users who have listened to over 500 tracks
	$max=0;
		//grab the value of how similar this user is to himself
		$weight=0;$cnt=0;
                foreach($topartsforall[$who] as $art => $score){
                        if (array_key_exists($art,$mytopartists)){
                                $weight+=($mytopartists[$art] + $score) /2;
                                $cnt++;
                        }
                }
	$max=$weight;

	while ($ru = mysql_fetch_row($qu)){
		$user = $ru[0];
//		if ($who==$user) continue; // dont compare me to myself :)
		
		if(!isset($topartsforall[$user])){
//			print "User: $user profile not found.\n";
//			flush();
//			skip 'em
			continue;
		}
		flush();
		$toparts =  $topartsforall[$user]; // get this users top-artists array (eg their profile)
		flush();
		$weight=0;$cnt=0;
		foreach($toparts as $art => $score){ // iterate thru all the artists in their profile
			if (array_key_exists($art,$mytopartists)){ // if i also listen to this...
				$weight+=($mytopartists[$art] + $score) /2; // add the average of my score and his score, indicating we are that much similar
				$cnt++;	
			}
		}
		if ($cnt>3){ //they have to have >3 artist in common with me or they are NOT similar.
			$result[$user]=$weight/$max;
		}
	}
	arsort($result); //sort it in descending order 
	return $result;
}


$me = $_GET['user'];// who are we working on

$topartsforall = array(); //this will hold an array[user][artist]=score for everyones profiles

//if usecache is set, then grab the big array of everyones profiles from disk
//else rebuild it

if (isset($_GET['usecache'])){
	$topartsforall = loadAllProfiles();
}else{
	print "About to rebuild the profile cache..\n"; flush();
	$qu = mysql_query("select pn_uname from pn_users where count>500");
	while ($ru = mysql_fetch_row($qu)){
	$topartsforall[$ru[0]]=readProfile($ru[0],true); //the true forces a rebuild of this data.
	}
	saveAllProfiles($topartsforall); //save the big ol' file to disk for later. it took a while to create.
}

//if this users not in array, they either do not exist, or have not listened to 500 songs yet
if (!is_array($topartsforall[$me])){
print "\n**\n**\n** WARNING: your profile is not expansive enough yet... cannot create kNN set.\n** (username is case sensitive - maybe it just didnt recognise you?)\n**\n**";
exit;
}

print "\n\n";

/*
$allscores=array();

$u = mysql_query("select pn_uname from pn_users where count>500");
while ($row = mysql_fetch_row($u)){
$allscores[$row[0]]=readProfile($row[0]);
}

*/
$mysims=getSimilarUsers($me,$topartsforall);
print "Hello $me. This script attempts to find users that are similar to you, then displays which artists you might like based on those users\nCompare the suggesteions against the values shown in your profile below\n\n";
print "Accessing a subset of ".count($mysims)." similar user profiles for k-Nearset-Neighbour calculations\n\n";
flush();

$predictartist=$_GET['artist'];//"Aerosmith";

if(strlen($predictartist)>0){ 
	$accuracy=0;
	$answer = predictArtistRating($me,$predictartist,$topartsforall, $mysims,$accuracy); 

	print "\n\nApparently, you should like $predictartist : ( $answer )  accuracy = ". round($accuracy*100,2)." \n";

	if (isset($topartsforall[$me][$predictartist])){
		print "You actually like $predictartist : ( ".$topartsforall[$me][$predictartist]." )\n";
		$diff = ($topartsforall[$me][$predictartist] - $answer);
		print "Difference [predict - actual] : $diff \n";
		$error = round(( $answer - $topartsforall[$me][$predictartist])/$topartsforall[$me][$predictartist] , 2);
		print "The error in the prediction is $error \n\n";
	}
}

$su = $mysims;
echo "Similar users\n\n";
print "</pre>";
$max=0;
foreach($su as $u => $s){
if($max==0) $max=$s;

$star="";
for($i=0;$i< round(($s/$max)*100); $i++){
$star .="*";
}
print $star . "  " .round(($s/$max)*100,2) . "% \t   --   <a href='http://audioscrobbler.com/modules.php?op=modload&name=top10&file=userinfo&user=".urlencode($u)."' target='_blank'>$u</a><br>\n";
}


print "<pre>\n\n\n$me's profile:\n";
foreach ($topartsforall[$me] as $a => $s){

print $s." \t $a \n";
}

// work out other artists i should listen to. this takes a while.. 

$useusers = 20; // use this many of my most similar users to find new artists from

if (isset($_GET["scan"])){
	print "\n\nScanning for possible new artists to suggest, by looking at your top $useusers similar users..\n";

	$artistpredictions = array();
	$checkedcounter=0;
	$compareusers=0;
	
	foreach ($mysims as $uu => $ss){
		if ($uu==$me) continue;
		$compareusers++;
		if ($compareusers>$useusers) break;
		print "\n$uu";
		$rank=0;
		foreach($topartsforall[$uu] as $artist => $score){
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
		print round($s,4)." \t $art\n";
	}
}

?></pre>
