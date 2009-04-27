<?php

/* Libre.fm -- a free network service for sharing your music listening habits

   Copyright (C) 2009 Libre.fm Project

   This program is free software: you can redistribute it and/or modify
   it under the terms of the GNU Affero General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   This program is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU Affero General Public License for more details.

   You should have received a copy of the GNU Affero General Public License
   along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

require_once('MDB2.php');
require_once('version.php');
require_once('utils/get_absolute_url.php');

if(file_exists("config.php")) {
	die("A configuration file already exists. Please delete <i>config.php</i> if you wish to reinstall.");
}

if (isset($_POST['install'])) {

	//Get the database connection string
	$dbms = $_POST['dbms'];
	if($dbms == "sqlite") {
		$filename = $_POST['filename'];
		$connect_string = "sqlite:///" . $filename;
	} else {
		$connect_string = $dbms . "://" . $_POST['username'] . ":" . $_POST['password'] . "@" . $_POST['hostname'] . ":" . $_POST['port'] . "/" . $_POST['dbname'];
	}

	// Check the connection
	$mdb2 =& MDB2::connect($connect_string);
	if (PEAR::isError($mdb2)) {
		die($mdb2->getMessage());
	}
	$mdb2->disconnect();

	$install_path = dirname(__FILE__) . "/";

	$default_theme = $_POST['default_theme'];
	$base_url = $_POST['base_url'];
	$submissions_server = $_POST['submissions_server'];

	//Write out the configuration
	$config = "<?php\n \$config_version = " . $version .";\n \$connect_string = '" . $connect_string . "';\n \$default_theme = '" . $default_theme . "';\n \$base_url = '" . $base_url . "';\n \$submissions_server = '" . $submissions_server . "';\n \$install_path = '" . $install_path . "'; ?>";

	$conf_file = fopen("config.php", "w");
	$result = fwrite($conf_file, $config);
	fclose($conf_file);

	if(!$result) {
		$print_config = str_replace("<", "&lt;", $config);
		die("Unable to write to file '<i>config.php</i>'. Please create this file and copy the following in to it: <br /><pre>" . $print_config . "</pre>");
	}	

	die("Configuration completed successfully!");
}

?>
<html>
	<head>
		<title>Libre.fm Website Installer</title>
		<script type='text/javascript'>
			function showSqlite() {
				document.getElementById("sqlite").style.visibility = "visible";
				document.getElementById("networkdbms").style.visibility = "hidden";
			}

			function showNetworkDBMS() {
				document.getElementById("sqlite").style.visibility = "hidden";
				document.getElementById("networkdbms").style.visibility = "visible";
			}
		</script>
	</head>

	<body onload="showSqlite()">
		<h1>Libre.fm Website Installer</h1>
		<p>Before installing the libre.fm website please be sure that you've installed and configured gobbler, as this creates all the database tables.</p>
		<form method="post">
			<h2>Database</h2>
			Database Management System: <br />
			<input type="radio" name="dbms" value="sqlite" onclick='showSqlite()' checked>SQLite (use an absolute path)</input><br />
			<input type="radio" name="dbms" value="mysql" onclick='showNetworkDBMS()'>MySQL</input><br />
			<input type="radio" name="dbms" value="pgsql" onclick='showNetworkDBMS()'>PostgreSQL</input><br />
			<br />
			<div id="sqlite">
				Filename: <input type="text" name="filename" /><br />
			</div>
			<div id="networkdbms">
				Hostname: <input type="text" name="hostname" /><br />
				Port: <input type="text" name="port" /><br />
				Database: <input type="text" name="dbname" /><br />
				Username: <input type="text" name="username" /><br />
				Password: <input type="password" name="password" /><br />
			</div>
			<br />
			<h2>General</h2>
			Default Theme: <select name="default_theme">
			<?php	
				$dir = opendir("themes");
				while($theme = readdir($dir)) {
					if(is_dir("themes/" . $theme) && $theme[0] != ".")  {
						echo "<option>" . $theme . "</option>";
					}
				}
			?>
			</select><br />
			Base URL: <input type="text" name="base_url" value="<?php echo getAbsoluteURL(); ?>" /><br />
			Submissions Server: <input type="text" name="submissions_server" /> (URL to your gnukebox install)<br />
			<br /><br />
			<input type="submit" value="Install" name="install" />
		</form>
	</body>
</html>


