<?php
/////////////////////////////////
// This little script is for testing submissions using the latest
// AudioScrobbler client protocl described at:
// http://audioscrobbler.sf.net/wiki/index.php/ClientProtocol
// Current limitations: It doesn't support multiple submissions, ie,
// queue dumping, and it doesn't ever send the INTERVAL message.
// A more sophisticated script would be necessary for that, this
// is just phidauex's quicky knockup.

// This section checks to see if 'magic quotes' is on, and if so, it
// undoes its effect. This means that any strings with quotes or apostrophes
// in them will have to be manually escaped with addslashes() before
// being sent to the database.

/* if (get_magic_quotes_gpc()) {

	// Overrides GPC variables
	for (reset($HTTP_GET_VARS); list($k, $v) = each($HTTP_GET_VARS); )
		$$k = stripslashes($v);
	for (reset($HTTP_POST_VARS); list($k, $v) = each($HTTP_POST_VARS); )
		$$k = stripslashes($v);
	for (reset($HTTP_COOKIE_VARS); list($k, $v) = each($HTTP_COOKIE_VARS); )
		$$k = stripslashes($v);
 } */

// This associative array links the client to its current version.
// I just guessed for all these, so either modify your program to send
// what this array expects, or edit the CVS copy, or bug phidauex
// or someone else to edit it for you. Since this is just a tester, it
// doesn't necessarily have to stay current with your actual versioning.
$versions = array('osx' => '0.1',   /* iScrobbler */
                  'wa2' => '0.1',   /* Winamp 2   */
                  'wa3' => '0.1',   /* Winamp 3   */
                  'xms' => '0.1',   /* XMMS       */
                  'wmp' => '0.1',   /* Windows MP */
                  'slm' => '0.1',   /* SliMP3     */
                  'mmj' => '0.1',   /* MusicMatch */
                  'foo' => '0.1',   /* FooBar     */
                  'qcd' => '0.1');  /* QCD        */

// This is the download URL that clients are directed to. This is just
// set as the AS home page for now, the real script can have a different
// URL for each client.
$downloadurl = 'http://audioscrobbler.com';

// This sets the submission URL, which is the URL to this script for the
// case of testing.
$submissionurl = 'http://audioscrobbler.sourceforge.net/submissiontest.php';

// if this variable exists then that means this is a handshake transaction. 
if($_GET['hs'] == 'true') {
  // Check for version inequality.
  if($_GET['v'] != $versions[$_GET['c']]) {
    echo "UPDATE ".$downloadurl."\n";
  } else {
    echo "UPTODATE\n";
  }
  
  echo $submissionurl."\n";
  echo "INTERVAL 0\n";
} else {
  // This isn't a handshake transaction, so go about your business.
  
  // Set the user/password and generate a hash.
  $localusername = "test";
  $localpassword = "testpass";
  $localpasswordhash = md5($localpassword);
  
  //get them variables the long way (modify for many submissions if needed)
  $artist = $_POST['a'];
  $title = $_POST['s'];
  $duration = $_POST['l'];
  $time = $_POST['d'];
  $username = $_POST['u'];
  $password = $_POST['p'];

  ///////// Write the data to the file.
  $datafile = fopen('submissiontest.txt', 'a+');
	
  if(!$datafile) {
    DIE("FAILED Datefile could not be opened\n");
  }

  fputs($datafile, "---- Submission Contents ----\n");
  fputs($datafile, "---- Contains ".count($a)." entries  ----\n");
  
  fputs($datafile, "Username       u = ".$username."\n");
  fputs($datafile, "Password       p = ".$password."\n");
  fputs($datafile, "Local Pass       = ".$localpasswordhash."\n");
  
  for($i=0; $i < count($a); $i++)
  {
    fputs($datafile, "Artist      a[".$i."] = ".$artist[$i]."\n");
    fputs($datafile, "Song Title  s[".$i."] = ".$title[$i]."\n");
    fputs($datafile, "Length      l[".$i."] = ".$duration[$i]."\n");
    fputs($datafile, "Date/Time   d[".$i."] = ".$time[$i]."\n");
  }
  
  fputs($datafile, "\n");

  fclose($datafile);
  
  /////////

  if($password == $localpasswordhash) {
    echo "OK\n";
  } else {
    echo "BADPASS\n";
  }
  
}

?>
