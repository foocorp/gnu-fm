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
require_once('data/User.php');
require_once('data/TagCloud.php');

# This function tends to be quite useful. Might move it somewhere else so
# that it can be included and reused in other files.
function getPostVar ($postvar, $fallback)
{
	if (isset($_POST[$postvar]))
		return $_POST[$postvar];
	return $fallback;
}

if($logged_in == false)
{
	$smarty->assign('error', 'Error!');
	$smarty->assign('details', 'Not logged in! You shouldn\'t be here!');
	$smarty->display('error.tpl');
	die();
}

$user = $_SESSION['user'];

$errors = array();

if (!empty($_POST['id']))
{
	# Need better URI validation, but this will do for now. I think
	# PEAR has a suitable module to help out here.
	if ( !preg_match('/^[a-z0-9\+\.\-]+\:/i', $_POST['id']) )
		$errors[] = "WebID must be a URI.";
	if ( preg_match('/\s/', $_POST['id']) )
		$errors[] = "WebID must be a URI. Valid URIs cannot contain whitespace.";
}

if (!empty($_POST['homepage']))
{
	# Need better URI validation, but this will do for now. I think
	# PEAR has a suitable module to help out here.
	if ( !preg_match('/^[a-z0-9\+\.\-]+\:/i', $_POST['homepage']) )
		$errors[] = "Homepage must be a URI.";
	if ( preg_match('/\s/', $_POST['homepage']) )
		$errors[] = "Homepage must be a URI. Valid URIs cannot contain whitespace.";
}

if (!empty($_POST['avatar_uri']))
{
	# Need better URI validation, but this will do for now. I think
	# PEAR has a suitable module to help out here.
	if ( !preg_match('/^[a-z0-9\+\.\-]+\:/i', $_POST['avatar_uri']) )
		$errors[] = "Avatar must be a URI.";
	if ( preg_match('/\s/', $_POST['avatar_uri']) )
		$errors[] = "Avatar must be a URI. Valid URIs cannot contain whitespace.";
}

if (!empty($_POST['location_uri']))
{
	# Currently only allow geonames URIs, but there's no reason we can't accept
	# others at some point in the future. (e.g. dbpedia)
	if ( !preg_match('/^http:\/\/sws.geonames.org\/[0-9]+\/$/', $_POST['location_uri']) )
		$errors[] = "This should be a geonames.org semantic web service URI.";
}

if (!isset($errors[0]))
{
	# Currently we don't allow them to change e-mail as we probably should
	# have some kind of confirmation login to do so.
	$user->id           = $_POST['id'];
	$user->fullname     = $_POST['fullname'];
	$user->homepage     = $_POST['homepage'];
	$user->bio          = $_POST['bio'];
	$user->location     = $_POST['location'];
	$user->location_uri = $_POST['location_uri'];
	$user->avatar_uri   = $_POST['avatar_uri'];
}

if(isset($user->name))
{
	# Stuff which cannot be changed.
	$smarty->assign("acctid", $user->acctid);
	$smarty->assign('avatar', $user->getAvatar());
	$smarty->assign('user',   $user->name);

	# Stuff which cannot be changed *here*
	$smarty->assign('userlevel', $user->userlevel);
	
	# Stuff which cannot be changed *yet*
	$smarty->assign('email', $user->email);
	
	# This is what we're going to let them change.
	$smarty->assign("id",           getPostVar('id',           $user->id));
	$smarty->assign('fullname',     getPostVar('fullname',     $user->fullname));
	$smarty->assign('bio',          getPostVar('bio',          $user->bio));
	$smarty->assign('homepage',     getPostVar('homepage',     $user->homepage));
	$smarty->assign('location',     getPostVar('location',     $user->location));
	$smarty->assign('location_uri', getPostVar('location_uri', $user->location_uri));
	$smarty->assign('avatar_uri',   getPostVar('avatar_uri',   $user->avatar_uri));

	# And display the page.
	$smarty->assign('errors', $errors);
	$smarty->display('edit_profile.tpl');
}

else
{
	$smarty->assign('error', 'User not found');
	$smarty->assign('details', 'Shall I call in a missing persons report? This shouldn\'t happen.');
	$smarty->display('error.tpl');
}

