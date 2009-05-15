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

<?php if (isset($_POST['username'], $_POST['api_key'], $_POST['token'])) {
	// Authenticate the user using the submitted password
	try {
		$result = $adodb->GetOne('SELECT username FROM Users WHERE '
				. 'username = ' . $adodb->qstr($_POST['username']) . ' AND '
				. 'password = ' . $adodb->qstr(md5($_POST['password'])));
	}
	catch (exception $e) {
		die('Database error');
	}
	if (!$result)
		die('Authentication failed');

	// Bind the user to the token and cancel the expiration rule
	try {
		$result = $adodb->Execute('UPDATE Auth SET '
				. 'username = ' . $adodb->qstr($_POST['username']) . ', '
				. 'expires = 0 '
				. 'WHERE '
				. 'token = ' . $adodb->qstr($_POST['token']));
	}
	catch (exception $e) {
		die('Database error');
	}
	?>

		<p>Thank you very much, <?php print($_POST['username']); ?>.  Your authorization has been recorded.</p>

		<p>You may now close the browser.</p>

		<?php } elseif (!isset($_GET['api_key'], $_GET['token'])) { ?>

			<p>Must submit an api_key and token to proceed.</p>

				<?php
		} else {

			// Ensures the token exists and is not already bound to a user
			try {
				$result = $adodb->GetRow('SELECT * FROM Auth WHERE '
						. 'token = ' . $adodb->qstr($_GET['token']) . ' AND '
						. 'username IS NULL');
			}
			catch (exception $e) {
				die('Database error');
			}
			if (!$result)
				die('Invalid token');
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
