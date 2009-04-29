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
require_once('data/sanitize.php');
require_once('data/Server.php');
require_once('data/User.php');

if ( strtolower(substr($mdb2->phptype, 0, 5)) == 'mysql'  )
	$random = 'RAND';
elseif ( strtolower(substr($mdb2->phptype, 0, 5)) == 'mssql'  )
	$random = 'NEWID';  // I don't think we try to support MSSQL, but here's how it's done theoretically anyway
else
	$random = 'RANDOM';  // postgresql, sqlite, possibly others
	
if ($_REQUEST['country'])
{
	$q = sprintf("SELECT u.* FROM Users u INNER JOIN Places p ON u.location_uri=p.location_uri AND p.country=%s ORDER BY %s() LIMIT 100",
		$mdb2->quote(strtoupper($_REQUEST['country']), 'text'),
		$random);
	
	$res = $mdb2->query($q);
	
	while ( $row = $res->fetchRow(MDB2_FETCHMODE_ASSOC) )
	{
		$userlist[] = new User($row['username'], $row);		
	}
	
	$smarty->assign('country', strtoupper($_REQUEST['country']));
	$res = $mdb2->query(sprintf("SELECT * FROM Countries WHERE country=%s LIMIT 1",
		$mdb2->quote(strtoupper($_REQUEST['country']), 'text')));
	if ( $row = $res->fetchRow(MDB2_FETCHMODE_ASSOC) )
	{
		$smarty->assign('country_info', $row);
	}
	
	$smarty->assign('userlist', $userlist);
	
	$smarty->assign('extra_head_links', array(
			array(
				'rel' => 'meta',
				'type' => 'application/rdf+xml' ,
				'title' => 'FOAF',
				'href' => $base_url.'/rdf.php?fmt=xml&page='.htmlentities($_SERVER['REQUEST_URI'])
				)
		));
		
	$smarty->display('location-country.tpl');
}

else
{
	$smarty->assign('error', 'Location not found');
	$smarty->assign('details', 'Shall I call in a missing locations report?');
	$smarty->display('error.tpl');
}
