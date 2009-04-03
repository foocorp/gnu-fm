<?

if(!file_exists("config.php")) {
	die("Please run the <a href='install.php'>Install</a> script to configure your installation");
}

require_once("config.php")
require_once('MDB2.php');

$mdb2 =& MDB2::connect($connect_string);
if (PEAR::isError($mdb2)) {
	die($mdb2->getMessage());
}

?>
