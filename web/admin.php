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
require_once('auth.php');
require_once('database.php');
require_once('templating.php');


function sendEmail($email) {
    global $mdb2;
    $code = md5(md5($username) . time());
    $mdb2->query('INSERT INTO Invitations (inviter, code) VALUES (' . $mdb2->quote($username, 'text') . ', ' . $mdb2->quote($code, 'text') . ')');
      
    $url = $base_url . '/register.php?authcode=' . $code;
    $headers = 'From: Libre.fm Invitations <invitations@libre.fm>';
    $subject = 'Libre.fm Invitation';
    $body = 'Hi!' . "\n\n" . 'You requested an invite to libre.fm, and here it is! Just click the link and fill in your details.';
    mail($email, $subject, $body, $headers);
    unset($url, $subject, $body, $headers);
}

if ($userlevel < 2) {	
    $smarty->assign("error", "Error!");
    $smarty->assign("details", "Invalid privileges.");
    $smarty->display("error.tpl");
    die();
} else {
    $action = $_GET['action'];
    if (isset($action)) {
	if ($action == "invite") {
	    if (!isset($_GET['email'])) {	
	        $smarty->assign("error", "Error!");
	        $smarty->assign("details", "Missing email.");
		$smarty->display("error.tpl");
		die();
	    } else {
		// Send the email
		sendEmail($_GET['email']);
		$smarty->assign('sent', true);
	    }
	} else {
	    $smarty->assign('error', "Error!");
	    $smarty->assign('error', 'Missing argument!');
	    $smarty->display('error.tpl');
	    die();
	}
    }
    
}

$res = $mdb2->query("SELECT email FROM Invitation_Request ORDER BY time ASC");
$data = $res->fetchAll(MDB2_FETCHMODE_ASSOC);
$smarty->assign('emails', $data);
$smarty->display('admin.tpl');
?>
