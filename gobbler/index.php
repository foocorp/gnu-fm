<?php
require_once('database.php');
require_once('version.php');

if (!isset($config_version) || $config_version != $version) {
	die("Configuration file is out of date. Please delete it (and associated database) and <a href='install.php'>reinstall</a>."); //TODO: Upgrade script for release versions
}

require_once('submissions-handshake.php');
?>
