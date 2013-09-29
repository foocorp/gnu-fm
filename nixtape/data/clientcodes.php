<?php

/* GNU FM -- a free network service for sharing your music listening habits

   Copyright (C) 2013 Free Software Foundation, Inc

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

//placeholder for non-free clients with unknown code
$nonfree_code = '';
//placeholder for non-free clients with unknown api keys
$nonfree_apikey = 'thisisafakekeyfornonfreeclients';
// TODO We could return this instead of real name if apikey = $nonfree_apikey
$nonfree_name = 'Unknown non-free client';
// TODO We could return this instead of real url if apikey = $nonfree_apikey
$nonfree_url = 'urltosomethinghere';

//placeholder for free clients with unknown client codes
$free_code = '';
//placeholder for free clients with unknown api keys
$free_apikey = '';

/* Array of client arrays (client code, api key, name, url, free software)
 *
 * If we can find a free client's api key in their source code repo we consider it ok to add the api key here.
 * It is ok for clients to only have a code or a apikey or both, as some clients will use the 1.2 and/or the 2.0 API.
 * For non-free clients we default to a fake api key for now, which we'll change on request(?).
 */
$clients = array(
	array('code' => 'amk', 'api_key' => $free_apikey, 'name' => 'Amarok 1.4.x plugin', 'url' => 'http://amarok.kde.org/', 'free' => True),
	array('code' => 'amp', 'api_key' => $nonfree_apikey, 'name' => 'Aimp2', 'url' => 'http://www.aimp.ru/', 'free' => False),
	array('code' => 'amy', 'api_key' => $free_apikey, 'name' => 'Anomaly', 'url' => 'https://chrome.google.com/webstore/detail/ajbkmdgmhbjjhephmgbpgialfbnjbmkb', 'free' => True),
	array('code' => 'ark', 'api_key' => $free_apikey, 'name' => 'Amarok', 'url' => 'http://amarok.kde.org/', 'free' => True),
	array('code' => 'ass', 'api_key' => $free_apikey, 'name' => 'Last.fm player', 'url' => 'http://www.last.fm/download', 'free' => True),
	array('code' => 'atu', 'api_key' => $free_apikey, 'name' => 'aTunes', 'url' => 'http://www.atunes.org/', 'free' => True),
	array('code' => 'aud', 'api_key' => $free_apikey, 'name' => 'Audacious', 'url' => 'http://audacious-media-player.org/', 'free' => True),
	array('code' => 'bil', 'api_key' => $nonfree_apikey, 'name' => 'billy', 'url' => 'http://www.sheepfriends.com/?page=billy', 'free' => False),
	array('code' => 'blu', 'api_key' => $free_apikey, 'name' => 'Bluemindo', 'url' => 'http://bluemindo.codingteam.net/', 'free' => True),
	array('code' => 'bsh', 'api_key' => $free_apikey, 'name' => 'Banshee', 'url' => 'http://banshee-project.org/', 'free' => True),
	array('code' => 'bwt', 'api_key' => $nonfree_apikey, 'name' => 'Bowtie', 'url' => 'http://www.bowtieapp.com/', 'free' => False),
	array('code' => 'cmu', 'api_key' => $free_apikey, 'name' => 'CmuScrobbler', 'url' => 'http://n.ethz.ch/%7edflatz/cmuscrobbler/', 'free' => True),
	array('code' => 'cpl', 'api_key' => $free_apikey, 'name' => 'cplay scrobbler', 'url' => 'http://sebi.tla.ro/cplay_scrobbler', 'free' => True),
	array('code' => 'cub', 'api_key' => $free_apikey, 'name' => 'Cuberok', 'url' => 'http://code.google.com/p/cuberok/', 'free' => True),
	array('code' => 'dbl', 'api_key' => $free_apikey, 'name' => 'Decibel Audio Player', 'url' => 'http://decibel.silent-blade.org/', 'free' => True),
	array('code' => 'ddb', 'api_key' => $free_apikey, 'name' => 'DeaDBeeF', 'url' => 'http://deadbeef.sourceforge.net/', 'free' => True),
	array('code' => 'dms', 'api_key' => $free_apikey, 'name' => 'donky mpdscrob', 'url' => 'http://github.com/mjhayes/donky/tree/master', 'free' => True),
	array('code' => 'ecm', 'api_key' => $nonfree_apikey, 'name' => 'Electrocompaniet Network Media Player', 'url' => 'http://www.electrocompaniet.no', 'free' => False),
	array('code' => 'ems', 'api_key' => $free_apikey, 'name' => 'EMMS', 'url' => 'http://www.gnu.org/software/emms/', 'free' => True),
	array('code' => 'exa', 'api_key' => $free_apikey, 'name' => 'Exaile', 'url' => 'http://www.exaile.org/', 'free' => True),
	array('code' => 'foo', 'api_key' => $free_apikey, 'name' => 'foobar2000', 'url' => 'http://www.foobar2000.org/', 'free' => True),
	array('code' => 'gmb', 'api_key' => $free_apikey, 'name' => 'gmusicbrowser', 'url' => 'http://gmusicbrowser.org/', 'free' => True),
	array('code' => 'gmm', 'api_key' => $free_apikey, 'name' => 'Goggles Music Manager', 'url' => 'http://code.google.com/p/gogglesmm/', 'free' => True),
	array('code' => 'gst', 'api_key' => $nonfree_apikey, 'name' => 'GimmeSome Tune', 'url' => 'http://www.eternalstorms.at/gimmesometune/', 'free' => False),
	array('code' => 'gua', 'api_key' => $free_apikey, 'name' => 'Guayadeque', 'url' => 'http://sourceforge.net/projects/guayadeque/', 'free' => True),
	array('code' => 'her', 'api_key' => $free_apikey, 'name' => 'Herrie', 'url' => 'http://herrie.info', 'free' => True),
	array('code' => 'isp', 'api_key' => $free_apikey, 'name' => 'iSproggler', 'url' => 'http://iSproggler.org/', 'free' => True),
	array('code' => 'jaj', 'api_key' => $free_apikey, 'name' => 'Jajuk', 'url' => 'http://jajuk.info/', 'free' => True),
	array('code' => 'ldr', 'api_key' => $free_apikey, 'name' => 'Libre Droid', 'url' => 'http://linux.mikeasoft.com/libredroid/', 'free' => True),
	array('code' => '', 'api_key' => 'default_gnufm_32_char_identifier', 'name' => $site_name, 'url' => $base_url, 'free' => True),
	array('code' => 'lfm', 'api_key' => 'werereallylibrefmhonestwouldilie', 'name' => 'Libre.fm', 'url' => 'http://libre.fm', 'free' => True),
	array('code' => 'lib', 'api_key' => $free_apikey, 'name' => 'LibreTunes', 'url' => 'http://libretunes.sourceforge.net/', 'free' => True),
	array('code' => 'liv', 'api_key' => $free_apikey, 'name' => 'Livewwwire', 'url' => 'http://ciarang.com', 'free' => True),
	array('code' => 'lpd', 'api_key' => $free_apikey, 'name' => 'lastPod', 'url' => 'http://www.lastpod.org', 'free' => True),
	array('code' => 'lsb', 'api_key' => $free_apikey, 'name' => 'libreScrobbleBerry', 'url' => 'https://gitorious.org/librescrobbleberry', 'free' => True),
	array('code' => 'lsd', 'api_key' => $free_apikey, 'name' => 'lastfmsubmitd', 'url' => 'http://www.red-bean.com/decklin/lastfmsubmitd/', 'free' => True),
	array('code' => 'mae', 'api_key' => $free_apikey, 'name' => 'maemoscrobbler', 'url' => 'https://garage.maemo.org/projects/maemoscrobbler', 'free' => True),
	array('code' => 'maf', 'api_key' => $free_apikey, 'name' => 'mafw-lastfm', 'url' => 'https://garage.maemo.org/projects/mafw-lastfm', 'free' => True),
	array('code' => 'mcl', 'api_key' => $nonfree_apikey, 'name' => 'MOCp-Scrobbler', 'url' => 'http://github.com/fluxid/mocp-scrobbler', 'free' => False),
	array('code' => 'mcn', 'api_key' => $free_apikey, 'name' => 'mpdcron', 'url' => 'http://mpd.wikia.com/wiki/Client:MPDCRON', 'free' => True),
	array('code' => 'mdc', 'api_key' => $free_apikey, 'name' => 'mpdscribble', 'url' => 'http://musicpd.org/', 'free' => True),
	array('code' => 'mlr', 'api_key' => $free_apikey, 'name' => 'mobbler', 'url' => 'http://code.google.com/p/mobbler/', 'free' => True),
	array('code' => 'mmo', 'api_key' => $nonfree_apikey, 'name' => 'MediaMonkey', 'url' => 'http://www.mediamonkey.com/', 'free' => False),
	array('code' => 'mms', 'api_key' => $free_apikey, 'name' => 'Maemo Scrobbler', 'url' => 'http://github.com/felipec/maemo-scrobbler', 'free' => True),
	array('code' => 'moc', 'api_key' => $free_apikey, 'name' => 'music on console', 'url' => 'http://moc.daper.net/', 'free' => True),
	array('code' => 'mp5', 'api_key' => 'a0ed2629d3d28606f67d7214c916788d', 'name' => 'mpdas', 'url' => 'http://50hz.ws/mpdas/', 'free' => True),
	array('code' => 'mpc', 'api_key' => $free_apikey, 'name' => 'Scrobby', 'url' => 'http://unkart.ovh.org/scrobby/', 'free' => True),
	array('code' => 'osx', 'api_key' => $nonfree_apikey, 'name' => 'iTunes', 'url' => 'http://www.apple.com/itunes/', 'free' => False),
	array('code' => 'pyj', 'api_key' => $free_apikey, 'name' => 'pyjama', 'url' => 'https://launchpad.net/pyjama', 'free' => True),
	array('code' => 'qcd', 'api_key' => $nonfree_apikey, 'name' => 'Quintessential Media Player', 'url' => 'http://www.quinnware.com/', 'free' => False),
	array('code' => 'qlb', 'api_key' => $free_apikey, 'name' => 'Quod Libet', 'url' => 'http://code.google.com/p/quodlibet/', 'free' => True),
	array('code' => 'qmm', 'api_key' => $free_apikey, 'name' => 'Qmmp', 'url' => 'http://qmmp.ylsoftware.com', 'free' => True),
	array('code' => 'qmn', 'api_key' => $free_apikey, 'name' => 'QMPDClient', 'url' => 'http://bitcheese.net/wiki/QMPDClient', 'free' => True),
	array('code' => 'qts', 'api_key' => $free_apikey, 'name' => 'QTScrobbler', 'url' => 'http://qtscrob.sourceforge.net/', 'free' => True),
	array('code' => 'rbx', 'api_key' => $free_apikey, 'name' => 'Rhythmbox', 'url' => 'http://projects.gnome.org/rhythmbox/', 'free' => True),
	array('code' => 'sbd', 'api_key' => $free_apikey, 'name' => 'Songbird', 'url' => 'http://www.getsongbird.com/', 'free' => True),
	array('code' => 'scb', 'api_key' => $free_apikey, 'name' => 'Scrobbl', 'url' => 'http://www.last.fm/group/scrobbl', 'free' => True),
	array('code' => 'sfm', 'api_key' => $free_apikey, 'name' => 'shell-fm', 'url' => 'http://nex.scrapping.cc/shell-fm/', 'free' => True),
	array('code' => 'sls', 'api_key' => $free_apikey, 'name' => 'Simple Last.fm Scrobbler', 'url' => 'http://code.google.com/p/a-simple-lastfm-scrobbler/', 'free' => True),
	array('code' => 'sna', 'api_key' => $free_apikey, 'name' => 'Sonata', 'url' => 'http://sonata.berlios.de/', 'free' => True),
	array('code' => 'srd', 'api_key' => $free_apikey, 'name' => 'scrobd', 'url' => 'http://codingteam.net/project/scrobd', 'free' => True),
	array('code' => 'spc', 'api_key' => $free_apikey, 'name' => 'scmpc', 'url' => 'http://ngls.zakx.de/scmpc/', 'free' => True),
	array('code' => 'spm', 'api_key' => $nonfree_apikey, 'name' => 'Spotify Mobile', 'url' => 'http://www.spotify.com/uk/mobile/overview/', 'free' => False),
	array('code' => 'spy', 'api_key' => $nonfree_apikey, 'name' => 'Spotify', 'url' => 'http://spotify.com/', 'free' => False),
	array('code' => 'ss7', 'api_key' => $free_apikey, 'name' => 'SqueezeScrobbler 7', 'url' => 'http://www.slimdevices.com/pi_features.html', 'free' => True),
	array('code' => 'sub', 'api_key' => $free_apikey, 'name' => 'Subsonic', 'url' => 'http://www.subsonic.org', 'free' => True),
	array('code' => 'tmh', 'api_key' => $free_apikey, 'name' => 'scrobble for iPhone', 'url' => 'http://www.nodomain.org/scrobble/', 'free' => True),
	array('code' => 'tng', 'api_key' => '75d20fb472be99275392aefa2760ea09', 'name' => 'Clementine Player', 'url' => 'http://code.google.com/p/clementine-player/', 'free' => True),
	array('code' => 'trt', 'api_key' => $nonfree_apikey, 'name' => 'Trout', 'url' => 'http://skwire.dcmembers.com/wb/pages/software/trout.php', 'free' => False),
	// TST IS FOR TESTING array('code' => 'tst', 'api_key' => $nonfree_apikey', 'name' => 'Ecoute', 'url' => 'http://ecouteapp.com/', 'free' => False),
	array('code' => 'vag', 'api_key' => $free_apikey, 'name' => 'vagalume', 'url' => 'http://vagalume.igalia.com/', 'free' => True),
	array('code' => 'vlc', 'api_key' => $free_apikey, 'name' => 'VLC', 'url' => 'http://videolan.org', 'free' => True),
	array('code' => 'wa2', 'api_key' => $nonfree_apikey, 'name' => 'WinAmp 2', 'url' => 'http://www.winamp.com/', 'free' => False),
	array('code' => 'wmp', 'api_key' => $nonfree_apikey, 'name' => 'Windows Media Player', 'url' => 'http://www.microsoft.com/windows/WindowsMedia/', 'free' => False),
	array('code' => 'xbm', 'api_key' => $free_apikey, 'name' => 'XBMC Media Center', 'url' => 'http://xbmc.org', 'free' => True),
	array('code' => 'xm2', 'api_key' => $free_apikey, 'name' => 'XMMS2', 'url' => 'http://www.xmms2.org/', 'free' => True),
	array('code' => 'xmp', 'api_key' => $free_apikey, 'name' => 'xmp-scrobbler', 'url' => 'http://www.un4seen.com/forum/?topic=5398.0', 'free' => True),
	array('code' => 'xsp', 'api_key' => $free_apikey, 'name' => 'xmms2-scrobbler-py', 'url' => 'http://code.google.com/p/xmms2-scrobbler-py/', 'free' => True),
	array('code' => 'yan', 'api_key' => $free_apikey, 'name' => 'Jerboa', 'url' => 'http://git.fredemmott.co.uk/?ph1.yanihp;asummary', 'free' => True),
	array('code' => 'you', 'api_key' => $free_apikey, 'name' => 'Youamp', 'url' => 'http://www.rojtberg.net/workspace/youamp/', 'free' => True),
	array('code' => 'zom', 'api_key' => $free_apikey, 'name' => 'ZOMG', 'url' => 'http://zomg.alioth.debian.org/', 'free' => True),
	array('code' => $free_code, 'api_key' => 'thisisthelibreimport2pythonthing', 'name' => 'libreimport v2', 'url' => 'https://gitorious.org/fmthings/lasttolibre', 'free' => True),
	array('code' => $free_code, 'api_key' => 'thisisthelibrelovepythonthing123', 'name' => 'librelove', 'url' => 'https://gitorious.org/fmthings/misc', 'free' => True),
);

/**
 * Get name, url and license type of a scrobble client based on client code or api key
 *
 * @param string $code Client code
 * @param string $apikey Client api key
 * @return array ('code' => string, 'api_key' => string, 'name' => string, 'url' => string, 'free' => boolean)
 *
 * @todo Rework our wiki page at http://bugs.foocorp.net/projects/librefm/wiki/Clients
 *		and create a nixtape page with general info on how to get unknown clients identified,
 *		wiki doesnt consider api keys and is a pain to work with so we should point to bug tracker
 *		instead	and remove client codes from wiki
 */
function getClientData($code=null, $apikey=null) {
	global $clients;

	// We return a 'Unknown client' array if both code and api key is invalid or not known to us
	$unknown_client = array('code' => $code, 'api_key' => $apikey, 'name' => 'Unknown client', 'url' => 'http://bugs.foocorp.net/projects/librefm/wiki/Clients', 'free' => False);

	if ((strlen($code) != 3) && (strlen($apikey) != 32)) {
		return 	$unknown_client;
	}

	for($i = 0; $i < count($clients); $i++) {
		if (strlen($code) == 3 && $clients[$i]['code'] == $code) {
			return $clients[$i];
		} else if (strlen($apikey) == 32 && $clients[$i]['api_key'] == $apikey) {
			return $clients[$i];
		}
	}
	return $unknown_client;
}
