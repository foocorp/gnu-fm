<?php

/* GNU FM -- a free network service for sharing your music listening habits

   Copyright (C) 2009 Free Software Foundation, Inc

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


if (isset($_POST['request'])) {
    $errors = '';
    $email = $_POST['email'];

    $validator = new EmailAddressValidator();
    if (!$validator->check_email_address($email)) {
	$errors .= 'You must enter a valid e-mail address('.$email.').<br />';
    }

    unset($validator);

    if(empty($errors)) {
	$adodb->Execute('INSERT INTO Invitation_Request (email, time, status) VALUES('
	    . $adodb->qstr($email) . ', '
	    . time() . ', 0)');
	$smarty->assign('reg', true);
    } else {
	$smarty->assign('errors', $errors);
    }
}

$smarty->display("request.tpl");
