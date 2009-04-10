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

require_once('../../database.php');
?>

<html>

<body>

<?php if (isset($_POST['username'], $_POST['api_key'], $_POST['token'])) { ?>

<?php
// Authenticate the user using the submitted password
$result = $mdb2->query('SELECT username FROM Users WHERE '
	. 'username = ' . $mdb2->quote($_POST['username'], 'text') . ' AND '
	. 'password = ' . $mdb2->quote(md5($_POST['password']), 'text'));
if (PEAR::isError($result))
	die("Database error");
if (!$result->numRows())
	die("Authentication failed");

// Bind the user to the token and cancel the expiration rule
$result = $mdb2->query('UPDATE Auth SET '
	. 'username = ' . $mdb2->quote($_POST['username'], 'text') . ', '
	. 'expires = ' . $mdb2->quote(0, 'integer') . ' '
	. 'WHERE '
	. 'token = ' . $mdb2->quote($_POST['token']));
if (PEAR::isError($result))
	die("Database error");
?>

<p>Thank you very much, <?php print($_POST['username']); ?>.  Your authorization has been recorded.</p>

<p>You may now close the browser.</p>

<?php } elseif (!isset($_GET['api_key'], $_GET['token'])) { ?>

<p>Must submit an api_key and token to proceed.</p>

<?php } else { ?>

<?php
// Ensures the token exists and is not already bound to a user
$result = $mdb2->query('SELECT * FROM Auth WHERE '
	. 'token = ' . $mdb2->quote($_GET['token'], 'text') . ' AND '
	. 'username IS NULL');
if (PEAR::isError($result))
	die("Database error");
if (!$result->numRows())
	die("Invalid token");
?>

<form method="post" action="">

<p>Your Username: <input type="text" name="username" /></p>

<p>Your Password: <input type="password" name="password" /></p>

<p>
<input type="submit" value="Submit" />
<input type="hidden" name="api_key" value="<?php print($_GET['api_key']); ?>" />
<input type="hidden" name="token" value="<?php print($_GET['token']); ?>" />
</p>

</form>

<?php } ?>

</body>

</html>
