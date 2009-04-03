<?

function changelog($allofthem = false){

	echo "Audioscrobbler 0.92 [10/02/03]\r\n";
	echo "------------------------------\r\n";
	echo "0.9 and 0.91 were buggy, they never submitted filename!\r\n";

if (!$allofthem) return;

	echo "Audioscrobbler 0.9 [10/02/03]\r\n";
	echo "-----------------------------\r\n";
	echo "Added option to keep HTTP streams private\r\n";
	echo "Reduced time-to-submission to 50% of song length\r\n";
	echo "Additional error handling\r\n";


if (!$allofthem) return;
	echo "Audioscrobbler 0.85 [02/01/03]\r\n";
	echo "------------------------------\r\n";
	echo "Communicates directly with new servers\r\n";
	echo "Removed unnecessary encryption. (Password still secure)\r\n";
	
	echo "\r\n";

if (!$allofthem) return;

	echo "Audioscrobbler 0.81 [05/12/02]\r\n";
	echo "-----------------------------\r\n";
	echo "All HTTP communications are now threaded\r\n";
	echo "Plugin now writes significat events to logfile\r\n";
	echo "Option in CC to specify maximum size of logfile\r\n";
	echo "Option in CC to disable the plugin\r\n";
	echo "Fixed and tested the offline caching, it should work now!\r\n";
	
	echo "\r\n";
				
	if (!$allofthem) return;

	echo "Audioscrobber 0.71 [02/12/02]\r\n";
	echo "-----------------------------\r\n";
	echo "Wrote my own XOR encrypt/decrypt function in C++ and PHP.\r\n";
	echo "ALL client-server communication now encrypted\r\n";
	echo "Plugin will not submit if winamp instances>1 (using semaphores)\r\n";
	echo "Offline caching is now encrypted\r\n";
	echo "Server side fix for duplicate/hasty submissions\r\n";
	echo "Remove read-only flag on cache file before deletion attempt\r\n";
	
	echo "\r\n";

	echo "Audioscrobber 0.65 [26/11/02]\r\n";
	echo "-----------------------------\r\n";
	echo "Hasty fix to 0.6 that caused double submission of all songs\r\n";
	echo "Rewritten offline caching. Songs played offline are now updated when you connect\r\n";
	
	echo "\r\n";



	echo "Audioscrobber 0.6 [25/11/02]\r\n";
	echo "----------------------------\r\n";
	echo "Control Centre 1.3 now handles updates properly\r\n";
	echo "Password encrytion - MD5 digests are used vs. cleartext\r\n";

        echo "\r\n";



	echo "Audioscrobbler v0.5 [23/11/02] (unreleased)\r\n";
	echo "-------------------------------------------\r\n";
	echo "completely new architecture\r\n";
	echo "stability should be acceptable - i learnt about memory management\r\n";
	echo "Control Centre now handles client updates\r\n";
	echo "offline caching removed as it was buggy\r\n";
	
	//if (!$allofthem) return;

	echo "\r\n";

	echo "Audioscrobbler v0.4 [20/11/02] (unreleased)\r\n";
	echo "-------------------------------------------\r\n";
	echo "minor changes to try and fix stability\r\n";

	echo "\r\n";

        echo "Audioscrobbler v0.3 [19/11/02] (unreleased)\r\n";
        echo "-------------------------------------------\r\n";                              
	echo "resolved failed submissions where & or # appeared in path\r\n";
        echo "fixed some random bugs and glitches\r\n";
	echo "updated installer to warn to close winamp\r\n";
	echo "installer creates start-menu shortcut to readme\r\n";
	
	echo "\r\n";

	echo "Audioscrobbler v0.25 [17/11/02]\r\n";
        echo "-------------------------------\r\n";
	echo "comes with an installer (nulsoft)\r\n";
	echo "you must listen to half the song before submission\r\n";
	echo "stability is getting better\t\n";
	
        echo "\r\n";

        echo "Audioscrobbler v0.2 [16/11/02]\r\n";
        echo "------------------------------\r\n";
        echo "doesn't crash if you are offline :)\r\n";
	echo "caches offline submissions for later in plain text file\r\n";
	echo "fixed bug regarding ' in song name\r\n";
	echo "plugin reports date and time of client instead of using server time\r\n";
	echo "plugin reports soundex hash for artist/song\r\n";
	echo "code cleaned up\r\n";

        echo "\r\n";

        echo "Audioscrobbler v0.1b [07/11/02]\r\n";
        echo "-------------------------------\r\n";
	echo "server time is used to timestamp submissions\r\n";
        echo "only works if you are online\r\n";
	echo "submits playlist text to server\r\n";
	echo "initial release, testing with me and paul\r\n";
	echo "no installer, need some MFC DLLs installed to use\r\n";

}

?>

