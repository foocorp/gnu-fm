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

/**
 * Encodes an URL component in a mod_rewrite friendly way, handling plus,
 * ampersand, hash and slash signs.
 *
 * @param string The text to encode
 * @return string A mod_rewrite compatible encoding of the given text.
 */
function rewrite_encode($url) {
	$url = urlencode($url);
	$url = preg_replace('/%2B/', '%252B', $url); // +
	$url = preg_replace('/%2F/', '%252F', $url); // /
	$url = preg_replace('/%26/', '%2526', $url); // &
	$url = preg_replace('/%23/', '%2523', $url); // #
	return $url;
}
