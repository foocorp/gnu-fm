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
require_once ('database.php');
require_once ('templating.php');
require_once ('data/User.php');
require_once ('utils/random_code_generator.php');
if ($logged_in == false) {
	$smarty->assign('error', 'Error!');
	$smarty->assign('details', 'Not logged in! You shouldn\'t be here!');
	$smarty->display('error.tpl');
	die ();
} elseif ( isset ($_GET['code'])) {
	$mdb2->exec("DELETE FROM Delete_Request WHERE expires < " . $mdb2->quote(time(), "integer"));

	$user = new User($_SESSION['user']->name);
	$username = $user->name;
	$code = $_GET['code'];
	$res = $mdb2->query("SELECT * FROM Delete_Request WHERE username = ".$mdb2->quote($username, 'text').' AND code = '.$mdb2->quote($code, 'text'));
	if (PEAR::isError($res)) {
		print_r($res);
		exit ;
	}
	if (!$res->numRows()) {
		$error = 'Invalid code.';
		$smarty->assign('error', 'Error!');
		$smarty->assign('details', $error);
		$smarty->display('error.tpl');
		die ();
	} else {
		$mdb2->exec("DELETE FROM Users WHERE lower(username) = ".$mdb2->quote(strtolower($username), 'text'));
		$mdb2->exec("DELETE FROM Scrobble_Sessions WHERE username = ".$mdb2->quote($username, 'text'));
		$mdb2->exec("DELETE FROM Delete_Request WHERE username = ".$mdb2->quote($username, 'text'));
		$mdb2->exec("DELETE FROM Auth WHERE username = ".$mdb2->quote($username, 'text'));
		$mdb2->exec("DELETE FROM Group_Members WHERE member = ".$mdb2->quote($username, 'text'));
		$mdb2->exec("DELETE FROM Radio_Sessions WHERE username = ".$mdb2->quote($username, 'text'));
		$mdb2->exec("DELETE FROM Recovery_Request WHERE username = ".$mdb2->quote($username, 'text'));
		$mdb2->exec("DELETE FROM Scrobbles WHERE username = ".$mdb2->quote($username, 'text'));
		session_destroy();
		header("Location: index.php");
	}
} else {
	$user = new User($_SESSION['user']->name);
	$code = generateCode();
	$username = $user->name;
	$email = $user->email;
	$expire = time()+86400;
	$mdb2->exec("INSERT INTO Delete_Request (code, expires, username) VALUES (".$mdb2->quote($code, 'text').', '.$mdb2->quote($expire, 'text').",".$mdb2->quote($username, 'text').')');
	$url = $base_url."/delete_profile.php?code=".$code;
	$content = "Hi!\n\nSomeone from the IP address ".$_SERVER['REMOTE_ADDR']." requested account deletion @ libre.fm.  To remove this account click: \n\n".$url."\n\n- The Libre.fm Team";
	$headers = 'From: Libre.fm <account@libre.fm>';
	$subject = 'Libre.fm Account Delete Request - Action needed!';
	mail($email, $subject, $text, $headers);
	$smarty->display('delete_profile.tpl');
}
?>
