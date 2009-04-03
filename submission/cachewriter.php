<?

// function to write to cache file.

// locations of files for storing cache.
$backlogroot ="/www/htdocs/audioscrobbler.com/submission/backlog";
$wiproot ="/www/htdocs/audioscrobbler.com/submission/workinprogress";

$xmmscache = "$backlogroot/xmms.cache";
$wa2cache = "$backlogroot/wa2.cache";
$wa3cache = "$backlogroot/wa3.cache";
$itunescache = "$backlogroot/itunes.cache";


$backlogfiles = array();
$backlogfiles[0] = $xmmscache;
$backlogfiles[1] = $wa2cache;
$backlogfiles[2] = $itunescache;
$backlogfiles[3] = $wa3cache;



function saveData($qs,$filename){
$msg="";
//$qs=str_replace($qs,"\n"," "); // replace newline with whitespace
$handle = fopen($filename, 'a');
if (!flock($handle,LOCK_EX)) $msg .= "ERROR LOCKING CACHE FILE\n";
fwrite($handle, $qs."\n");
if(!flock($handle,LOCK_UN)) $msg .= "ERROR UNLOCKING FILE\n";
fclose($handle);
return $msg;
}
?>
