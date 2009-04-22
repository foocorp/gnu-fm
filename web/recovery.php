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

require_once('database.php');
require_once('templating.php');
require_once('utils/EmailAddressValidator.php');

global $mdb2;
$errors = '';

function sendEmail($text, $email) {
    $headers = 'From: Libre.fm Recovery <recovery@libre.fm>';
    $subject = 'Libre.fm Password Recovery';
    mail($email, $subject, $text, $headers);
}

if (isset($_GET['code'])) {
    $res = $mdb2->query("SELECT * FROM Recovery_Request WHERE code=" . $mdb2->quote($_GET['code'], 'text'));
    if ($res->numRows() == 0) {
	$errors .= "Invalid recovery token.\n";
	$smarty->assign('errors', $errors);
	$smarty->display('error.tpl');
	die();
    }

    $row = $res->fetchOne(MDB2_FETCHMODE_ASSOC);
    
    $password = "";
    $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
    
    for ($i = 0; $i < 8; $i++) {
	$password .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
    }

    $email = $row['email'];

    $sql = "UPDATE Users SET password=" . $mdb2->quote(md5($password), 'text') . " WHERE email="
	 . $mdb2->quote($email, 'text');

    $mdb2->exec($sql);

    $content = "Hi!\n\nYour password has been set to " . $password . "\n\n - The Libre.fm Team";
    sendEmail($content, $email);
    $smarty->assign('sent', true);
}

if (isset($_POST['user'])) {
    $username = $_POST['user'];

    $res = $mdb2->query("SELECT * FROM Users WHERE username=" 
       . $mdb2->quote($username, 'text'));	

    if (PEAR::isError($res) || $res->numRows() == 0) {
	$errors .= "User not found.\n";
	$smarty->assign('errors', $errors);
	$smarty->display('error.tpl');
	die();
    } 
    $row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
    $code = md5($username . $row['email'] . time());
    $sql = "INSERT INTO Recovery_Request (username, email, code, expires) VALUES("
	. $mdb2->quote($username, 'text') . ", " 
	. $mdb2->quote($row['email'], 'text') . ", "
	. $mdb2->quote($code, 'text') . ", "
	. $mdb2->quote(time() + 86400, 'text') . ")";

    $res = $mdb2->exec($sql);
    if (PEAR::isError($res)) {
	$errors .= "Error on: " . $sql;
	$smarty->assign('errors', $errors);
	$smarty->display('error.tpl');
	die();
    }

    $url = $base_url . "/recovery.php?code=" . $code;
    $content = "Hi!\n\nSomeone from the IP-address " . $_SERVER['REMOTE_ADDR'] . " entered you username " 
	. "in the Password Recovery Form @ libre.fm. To change you password, please visit\n\n"
	. $url . "\n\n- The Libre.fm Team";
    sendEmail($content, $row['email']);
    $smarty->assign('sent', true);	

} 

$smarty->display("recovery.tpl");
?>
