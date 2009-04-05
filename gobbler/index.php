<?php
require_once('database.php');
require_once('version.php');

if (!isset($config_version) || $config_version != $version) {
	die("Configuration file is out of date. Please delete it (and associated database) and <a href='install.php'>reinstall</a>."); //TODO: Upgrade script for release versions
}

if (isset($_GET['hs']) && isset($_GET['p'])) {
	if (substr($_GET['p'], 0, 3) == "1.2") {
		require_once('submissions/1.2/handshake.php');
	} elseif (substr($_GET['p'], 0, 3) == "1.1") {
		require_once('submissions/1.1/handshake.php');
	}
}
?>
