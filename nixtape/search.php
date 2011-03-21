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
require_once('data/sanitize.php');
require_once('data/Server.php');
require_once('data/TagCloud.php');

$search_term = $_GET['search_term'];
$search_type = $_GET['search_type'];

$results = Server::search($search_term, $search_type);

$smarty->assign('pageheading', 'Results for: ' . $search_term);
$smarty->assign('search_term', $search_term);
$smarty->assign('search_type', $search_type);
$smarty->assign('results', $results);

$smarty->display("search.tpl");
