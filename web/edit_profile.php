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

if($logged_in == false)
{
	$smarty->assign('error', 'Error!');
	$smarty->assign('details', 'Not logged in! You shouldn\'t be here!');
	$smarty->display('error.tpl');
	die();
}

# Doesn't seem to work - $user = $_SESSION['user'];
$user = new User($_SESSION['user']->name);

$errors = array();

if ($_POST['submit'])
{
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

	if (!empty($_POST['password_1']))
	{
		if ($_POST['password_1'] != $_POST['password_2'])
			$errors[] = "Passwords do not match.";
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
		
		if (!empty( $_POST['password_1'] ))
			$user->password = md5($_POST['password_1']);
		
		$user->save();

		header("Location: " . $base_url . "/user/" . $user->name);
		exit;
	}

	if (isset($errors[0]))
	{
		header("Content-Type: text/plain");
		print_r($errors);
		exit;
	}
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
	
	if ($_POST['submit'])
	{
		$smarty->assign("id",           $_POST['id']);
		$smarty->assign('fullname',     $_POST['fullname']);
		$smarty->assign('bio',          $_POST['bio']);
		$smarty->assign('homepage',     $_POST['homepage']);
		$smarty->assign('location',     $_POST['location']);
		$smarty->assign('location_uri', $_POST['location_uri']);
		$smarty->assign('avatar_uri',   $_POST['avatar_uri']);
	}
	else
	{
		$smarty->assign("id",           ($user->webid_uri));
		$smarty->assign('fullname',     ($user->fullname));
		$smarty->assign('bio',          ($user->bio));
		$smarty->assign('homepage',     ($user->homepage));
		$smarty->assign('location',     ($user->location));
		$smarty->assign('location_uri', ($user->location_uri));
		$smarty->assign('avatar_uri',   ($user->avatar_uri));
	}

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

