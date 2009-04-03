#!/usr/bin/perl
#
#
# Are you reading this in your webbrowser?
# If so, go back, right click on the link and "save as"
# Make the file executable (chmod +x ./audioscrobblerinstaller.pl)
# Run it (./audioscrobblerinstaller.pl)
#
#
#
#
#
#
#
#
# Audioscrobbler Installer, by RJ
# Last Modified: 13/03/2003
#
# Don't laugh at my Perl :-P
#


print "\nAudioscrobbler XMMS Plugin Installer v1.3\n";
print "-----------------------------------------\n";



print "Resolving location of XMMS plugins directory...\n";

# if this doesn't find your xmms general plugins dir, then change
# the $following line to specify it yourself, eg $dest = "/my/xmms/general/path";

$dest = "/usr/lib/xmms/General"; #RH8 etc
if(!(-d $dest)){
 $dest = "/usr/X11R6/lib/xmms/General" ; #SuSE 8.1
}
if(!(-d $dest)){
 $dest = `xmms-config --general-plugin-dir`; # should work but sometimes doesnt
}

if(!(-d $dest)){

print "Having problems working out where to install XMMS General Plugins...\n";
print "\nOk, you'll have to tell me exactly where to put it. try \"whereis xmms\" for clues\n";
print "    It will be a folder called General, which should also contain song_change.so\n";
print "    Please specify path without a trailing slash (eg /usr/lib/xmms/General)\n\n";
while (!(-d $dest)){
print "Where: ";
$dest = <STDIN>;
chomp($dest); # hilariously named function
print "\n";
} 

}

print "Attempting installation to : $dest\n";


`echo "Audioscrobbler.com" > $dest/test`;
if (!(-f "$dest/test")){
print "Unable to write to $dest - perhaps you should try again as root?\n\n";
exit;
}else{
print "Testing ability to write to $dest...OK\n";
`rm -f $dest/test`;
}

print "Checking latest version...";
$cver = `wget -qO - http://audioscrobbler.com/controlcentre/queryxmmsinstall.php?option=currentversion`;
if (length($cver)<1){
print "ERROR: Could not contact server\n\n";
exit;
}else{
print "OK\nWill attempt to fetch and install version : $cver\n";
}

print "Preparing to download... ";
$location = `wget -qO - http://audioscrobbler.com/controlcentre/queryxmmsinstall.php?option=location`;
if (length($location)<1){
print "ERROR: Could not contact server\n\n";
exit;
}else{
print "OK\nDownloading: $location\n";
}

$nameoffile = "xmmsplugin$cver.tgz";
if (-f $nameoffile){
`rm $nameoffile`;
}

`wget -O $dest/$nameoffile $location`;
if (!(-f "$dest/$nameoffile")){
	print "ERROR: Download failed.\n\n";
	exit;
}

if (-f "$dest/audioscrobbler.so"){
if (!(`rm $dest/audioscrobbler.so`)){
print "Removed old version of audioscrobbler\n";
}else {
print "ERROR: Unable to remove existing version ($dest/audioscrobbler.so)\n\n";
exit;
}
}

print "Extracting new version from downloaded archive...\n";
$err = `tar xzv --directory=$dest -f $dest/$nameoffile`;

if (-f "$dest/audioscrobbler.so"){

`rm -f $dest/$nameoffile`;

print "\n\nINSTALLATION SUCCESSFUL\n";
print     "-----------------------\n"; 
print "+ Restart XMMS, and press CONTORL+P for preferences
+ Select the 'Effect/General Plugins' tab
+ Double click on Audioscrobbler in the list of General Plugins
+ If you haven't registered yet, head over to Audioscrobbler.com
+ Type in your username and password into the box that appears
+ Click 'Yup'
+ MAKE SURE YOU TICK 'Enable Plugin' ;)
+ Listen to some tunes
+ Check Audioscrobbler.com for your stats \n\n";
}else{
print "ERROR: extracting the plugin from the downloaded archive failed\n";
exit;
}


 

 






