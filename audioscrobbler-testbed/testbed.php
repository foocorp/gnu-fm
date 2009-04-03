<?

/**
 * TestBed.php
 *
 * misc functions for testing
 *
 *
 * @version 0.0
 * @author Josh Wand <josh@joshwand.com>
 */


require ("dal/class_dal.phps");


/**
 * Short description.
 *
 * Populates the artist table from audio_data
 * @param     
 * @since     0.0
 * @access    private
 * @return    void
 * @update    data time
 */
function BuildArtistTable()
{
	
	ob_end_flush();
	set_time_limit(0);
	echo "<li>Fetching records...";
	flush();
	
	$dal = new dal();
	$dal2 = new dal();
	
	//fetch artists from audio_data table

	$selectstring = "SELECT DISTINCT artist FROM audio_data";
	$dal->q($selectstring);
    
	if ($dal->rowcount <> 0) {
		echo "done!\n";
		flush();
	
		// insert each artist into the artists table

		$insertstring = "INSERT INTO artists(artist_name) VALUES (";
		while ($row = $dal->fetchArray()) {
		    $artist = $row[artist];
			echo "<li> Inserting $artist ...";
			flush();
	
			$dal2->q($insertstring . "'$artist')");
			if ($dal2->rowcount <> 0) {
				echo "done!\n";
				flush();
			} else {
				echo "<li> Insert failed! artist = $artist; lasterror = " . $dal->lastError;    
				flush();
			}
		} // end while

	} else {
	    die("could not query artists!" . $dal->lastError);
	}

} // end func


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


/**
 * Short description.
 *
 * Detail description
 * @param     $artist artist name
 * @since     0.0
 * @access    private
 * @return    int artistID 
 * @update    data time
 */
function getArtistID($artist)
{
    $dal2 = new dal();
	$artist = addslashes($artist);
	$querystring = "SELECT artist_id FROM artists WHERE artist_name='$artist'";
	$dal2->q($querystring);

	if ($dal2->rowcount <> 0) {
	    while ($row = $dal2->fetchArray()) {
	        return $row[artist_id];
	    }
	} else {
	    return 0;
	}

} // end func


/**
 * BuildProfilesTableFromCache - Builds Profiles Table From Cache.
 *
 * Detail description
 * @param     
 * @since     0.0
 * @access    private
 * @return    void
 * @update    data time
 */
function BuildProfilesTableFromCache()
{
	ob_end_flush();
	set_time_limit(0);

	$dal = new dal();
	$profiles = array();
	$user = array();

	echo "loading profiles ... ";
	flush();

	$profiles = loadProfiles();

	echo "done!<br>\n";
	flush();



	foreach ($profiles as $user => $artists) {
		echo $user;
		foreach ($artists as $artist => $score) {
			$dal = new dal();
			$querystring = "INSERT INTO acf_profiles(user,artist_id,score) VALUES('$user', " . getArtistID($artist) . ", $score)";  
			echo "<li>inserting... user = $user, artist = $artist, score = $score...";
			$dal->q($querystring);
			echo $dal->lastQuery;
			if ($dal->rowcount <> 0) {
				echo "done!<br>\n";
			} else {
				echo "error! lasterror=" . $dal->lastQuery;
			}
		}
	}

} // end func


/**
 * Short description.
 *
 * Detail description
 * @param     
 * @since     1.0
 * @access    private
 * @return    void
 * @update    data time
 */
function FindClusterItems($artist_id)
{

   /**

	returns matrix of artist2_id => P(N/m) 
	where N = number of times artist1 and artist2 occur together
	and m = number of times artist occurs across all profiles.
	
   */

	// get list of users who listen to $artist_id
    $dal = new dal();
	$querystring = "SELECT count(user) as num, user FROM acf_profiles WHERE ";
	foreach ($artist_id as $id) {
		$querystring .= "artist_id = $id OR ";
	}
	$querystring .= "artist_id = $artist_id[0] GROUP BY user ORDER BY num DESC";
	$dal->q($querystring);
	
	

	$recs = array();

	if ($dal->rowcount <> 0) {
		$total_artist_occurrences = $dal->rowcount; // this is 'm' as above
		while ($user_row = $dal->fetchArray()) {
			$match_index = $user_row[num];
			if ($match_index <> count($artist_id)) continue; // only count users who listen to all the artists in the array
			$user = $user_row[user];
			// now grab the profiles of all the users who listen to the artists in the array
			$dal2 = new dal();
			$querystring2 = "SELECT * FROM acf_profiles,artists WHERE user = '$user' AND artists.artist_id = acf_profiles.artist_id";
			$dal2->q($querystring2);
			if ($dal2->rowcount <> 0) {
				
				//for each score, increment the counter in recs.
			    while ($record = $dal2->fetchArray()) {
			        $artist2_id = $record[artist_id];
					$artist2_name = $record[artist_name];
					if (in_array($artist2_id, $artist_id)) continue; // don't include any of the original artists in the comparison
					if (!isset($recs[$artist2_name])) $recs[$artist2_name] = 0;
					$recs[$artist2_name]++;
			    }
			} else {
			    die ($dal2->lastError . $dal2->lastQuery);
			}

	    }
	} else {
		
	    die ($dal->lastError . $dal->lastQuery);
	}

	// divide N by m
	foreach ($recs as $artist2_name => $score) {
		$recs[$artist2_name] = ($recs[$artist2_name] / $total_artist_occurrences);
	}


    arsort($recs);

	return $recs;

} // end func

$recs = array();

$query = explode("|",$_GET[q]);

foreach ($query as $index => $artist) {
	$query[$index] = getArtistID($artist);
}

$recs = FindClusterItems($query);

$counter = 0;
if (count($recs) == 0) {
    echo "Looks like no one listens to those " . count($query) . " artists! Try again with a different combination!";
}
foreach ($recs as $artist => $score ) {
	echo "<li>$artist = $score\n";
	$counter++;
	if ($counter == 50) {
		break;	    
	}
}

?>