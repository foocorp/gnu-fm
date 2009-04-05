<?php
//  DO NOT CHANGE THE ORDER IN WHICH THIS STUFF IS SENT - THE CLIENT WILL GET CONFUSED!
include("../currentversion.php");
// FIRST THING TO SEND IS CURRENT VERSION
echo $currentversion;
echo "\n";
// NOW SEND VERSION OF CONTOL CENTRE
echo $currentCCversion;
echo "\n";
// WINAMP DLL
echo "http://audioscrobbler.com/controlcentre/gen_audioscrobbler.dll\n";
//CC UPDATE
echo "http://audioscrobbler.com/controlcentre/AudioscrobblerCC.exe\n";
//RANDOM EXE TO RUN, MISC UPDATES
echo "NONE\n";
//MISC VERSION NUIMBER
echo $currentMisc;
echo "\n";


?>
