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

if(!$logged_in) {
	$smarty->display('login.tpl');
	die();
}

if(isset($_POST['invite'])) {

	$errors = '';
	$email = $_POST['email'];

    $mail_validator = new EmailAddressValidator();

	if($mail_validator->check_email_address($email)) {
		$errors .= 'You must enter a valid e-mail address.<br />';
	}

    # clean up
    unset($mail_validator);

	if(empty($errors)) {
		$code = md5(md5($username) . time());
		$mdb2->query('INSERT INTO Invitations (inviter, code) VALUES ('
			. $mdb2->quote($username, 'text') . ', '
			. $mdb2->quote($code, 'text') . ')');

		$url = $base_url . '/register.php?authcode=' . $code;
        $headers = 'From: Libre.fm Invitations <invitations@libre.fm>';
        $subject = 'Libre.fm Invitation';
        $body = 'Hi!' . "\n\n" .
            'Clearly ' . $username . ' really likes you, because he/she\'s inviting you to join http://libre.fm!' . "\n\n" .
            'Just visit ' . $url . ' to sign up, all the cool kids are doing it.\n';

        mail($email, $subject, $body, $headers);

        unset($url, $subject, $body, $headers);

		$smarty->assign('sent', true);
	} else {
		$smarty->assign('errors', $errors);
	}

}

$smarty->display("invite.tpl");
?>
