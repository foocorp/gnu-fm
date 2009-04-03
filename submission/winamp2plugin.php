<?
include("../currentversion.php");
include("../includes/functions.php");
require("cachewriter.php");

//include("../includes/sessions.php");
$dolog = false;
function logthis($txt){
	//if (!$dolog) return;
	$filename ="winamp2.log";
        $handle= fopen($filename,'a');
	fputs($handle, $txt."\n");
        fclose($handle);
}

function reply($s){
	print $s;
	exit;
}

parse_str(rawurldecode($_POST['data'])); // extract information from the post
logthis(rawurldecode($_POST['data']));
require("dbConnect.php");
$db= getDatabase();
if (!$db){
reply("FAILED\nDatabase down... try soon");
}

mysql_select_db ("audioscrobbler");

$qry = "SELECT pn_pass,lastsubtime,UNIX_TIMESTAMP(),lastsong, CURRENT_TIMESTAMP() FROM pn_users where pn_uname='" . $username . "'";
$result = mysql_query ($qry);


if(!$result) $result = mysql_query ($qry);
//if(!$result) $result = mysql_query ($qry);
//if(!$result) $result = mysql_query ($qry);
if(!$result) reply ("FAILED\nTemporary glitch - Database unavailable");

//check username+pass
if(mysql_num_rows($result)<1) reply("BADPASS\nUsername invalid or database error");
$row=mysql_fetch_row($result);
if($password!=$row[0]) reply("BADPASS\nBad password or database error");

$uts = $row[2];

// The password is correct at this point.






$numsubs = 1;
$numsubs = count($song);
//if ($numsubs>1) logthis("there are ".$numsubs." incoming submissions from ".$username);else logthis("one submission received from ".$username);




//check all the submissions and insert into database
for ($index=0;$index<$numsubs;$index++){

	if (isset($artist[$index])){

		//if no length, make one up
		if ($duration[$index]<2) $duration[$index]=200; // default value
		if ($duration[$index]>(3600*2)){
		//	 $duration[$index]=round($duration[$index]/1000000); // default value
		//	if ($duration<2) $duration=200;
			continue;
		}

		fixGayness($artist[$index],$song[$index]);
		if (strlen($artist[$index])<1 || strlen($song[$index])<1) continue;

		$artist[$index] = removeThe($artist[$index]);

		$str ="$username::::".$duration[$index]."::::".$artist[$index]."::::".$song[$index]."::::".$timestamp[$index]."::::$uts::::".str_replace("\n","",$path[$index])."::::$version";
		saveData($str,$wa2cache);

	}

}//end for


echo "OK\n";


?>
