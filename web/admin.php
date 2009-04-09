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


$res = $mdb2->query("SELECT userlevel FROM Users WHERE username=" . $mdb2->quote($user, 'text'));
if ($res->numRows() != 1) {
    $smarty->assign("error", "Error!");
    $smarty->assign("details", "Invalid user specified.");
    $smarty->display("error.tpl");
    die();
}

$row = $res->fetchRow(MDB2_FETCHMODE_ASSOC);
if ($row['userlevel'] < 2) {	
    $smarty->assign("error", "Error!");
    $smarty->assign("details", "Invalid privileges.");
    $smarty->display("error.tpl");
    die();
} else {
    $smarty->display('admin.tpl');
    echo "Access to admin-panel granted.";

}
