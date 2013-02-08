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

/**
 * Get name, url and license type of a scrobble client based on client code
 *
 * @param string $code Client code
 * @return array ("name" => string, "url" => string, "free" => boolean)
 */
function getClientData($code=null, $api_key=null) {
	global $base_url, $site_name;

	$clients = array(
		"amk" => array("name" => "Amarok 1.4.x plugin", "url" => "http://amarok.kde.org/", "free" => True),
		"amp" => array("name" => "Aimp2", "url" => "http://www.aimp.ru/", "free" => False),
		"amy" => array("name" => "Anomaly", "url" => "https://chrome.google.com/webstore/detail/ajbkmdgmhbjjhephmgbpgialfbnjbmkb", "free" => True),
		"ark" => array("name" => "Amarok", "url" => "http://amarok.kde.org/", "free" => True),
		"ass" => array("name" => "Last.fm player", "url" => "http://www.last.fm/download", "free" => True),
		"atu" => array("name" => "aTunes", "url" => "http://www.atunes.org/", "free" => True),
		"aud" => array("name" => "Audacious", "url" => "http://audacious-media-player.org/", "free" => True),
		"bil" => array("name" => "billy", "url" => "http://www.sheepfriends.com/?page=billy", "free" => False),
		"blu" => array("name" => "Bluemindo", "url" => "http://bluemindo.codingteam.net/", "free" => True),
		"bsh" => array("name" => "Banshee", "url" => "http://banshee-project.org/", "free" => True),
		"bwt" => array("name" => "Bowtie", "url" => "http://www.bowtieapp.com/", "free" => False),
		"cmu" => array("name" => "CmuScrobbler", "url" => "http://n.ethz.ch/%7edflatz/cmuscrobbler/", "free" => True),
		"cpl" => array("name" => "cplay scrobbler", "url" => "http://sebi.tla.ro/cplay_scrobbler", "free" => True),
		"cub" => array("name" => "Cuberok", "url" => "http://code.google.com/p/cuberok/", "free" => True),
		"dbl" => array("name" => "Decibel Audio Player", "url" => "http://decibel.silent-blade.org/", "free" => True),
		"ddb" => array("name" => "DeaDBeeF", "url" => "http://deadbeef.sourceforge.net/", "free" => True),
		"dms" => array("name" => "donky mpdscrob", "url" => "http://github.com/mjhayes/donky/tree/master", "free" => True),
		"ems" => array("name" => "EMMS", "url" => "http://www.gnu.org/software/emms/", "free" => True),
		"exa" => array("name" => "Exaile", "url" => "http://www.exaile.org/", "free" => True),
		"foo" => array("name" => "foobar2000", "url" => "http://www.foobar2000.org/", "free" => True),
		"gmb" => array("name" => "gmusicbrowser", "url" => "http://gmusicbrowser.org/", "free" => True),
		"gmm" => array("name" => "Goggles Music Manager", "url" => "http://code.google.com/p/gogglesmm/", "free" => True),
		"gst" => array("name" => "GimmeSome Tune", "url" => "http://www.eternalstorms.at/gimmesometune/", "free" => False),
		"gua" => array("name" => "Guayadeque", "url" => "http://sourceforge.net/projects/guayadeque/", "free" => True),
		"isp" => array("name" => "iSproggler", "url" => "http://iSproggler.org/", "free" => True),
		"jaj" => array("name" => "Jajuk", "url" => "http://jajuk.info/", "free" => True),
		"ldr" => array("name" => "Libre Droid", "url" => "http://linux.mikeasoft.com/libredroid/", "free" => True),
		"lfm" => array("name" => $site_name, "url" => $base_url, "free" => True),
		"lib" => array("name" => "LibreTunes", "url" => "http://libretunes.sourceforge.net/", "free" => True),
		"liv" => array("name" => "Livewwwire", "url" => "http://ciarang.com", "free" => True),
		"lpd" => array("name" => "lastPod", "url" => "http://www.lastpod.org", "free" => True),
		"lsd" => array("name" => "lastfmsubmitd", "url" => "http://www.red-bean.com/decklin/lastfmsubmitd/", "free" => True),
		"mae" => array("name" => "maemoscrobbler", "url" => "https://garage.maemo.org/projects/maemoscrobbler", "free" => True),
		"maf" => array("name" => "mafw-lastfm", "url" => "https://garage.maemo.org/projects/mafw-lastfm", "free" => True),
		"mcl" => array("name" => "MOCp-Scrobbler", "url" => "http://github.com/fluxid/mocp-scrobbler", "free" => False),
		"mcn" => array("name" => "mpdcron", "url" => "http://mpd.wikia.com/wiki/Client:MPDCRON", "free" => True),
		"mdc" => array("name" => "mpdscribble", "url" => "http://musicpd.org/", "free" => True),
		"mlr" => array("name" => "mobbler", "url" => "http://code.google.com/p/mobbler/", "free" => True),
		"mmo" => array("name" => "MediaMonkey", "url" => "http://www.mediamonkey.com/", "free" => False),
		"mms" => array("name" => "Maemo Scrobbler", "url" => "http://github.com/felipec/maemo-scrobbler", "free" => True),
		"moc" => array("name" => "music on console", "url" => "http://moc.daper.net/", "free" => True),
		"mp5" => array("name" => "mpdas", "url" => "http://50hz.ws/mpdas/", "free" => True),
		"mpc" => array("name" => "Scrobby", "url" => "http://unkart.ovh.org/scrobby/", "free" => True),
		"osx" => array("name" => "iTunes", "url" => "http://www.apple.com/itunes/", "free" => False),
		"pyj" => array("name" => "pyjama", "url" => "https://launchpad.net/pyjama", "free" => True),
		"qcd" => array("name" => "Quintessential Media Player", "url" => "http://www.quinnware.com/", "free" => False),
		"qlb" => array("name" => "Quod Libet", "url" => "http://code.google.com/p/quodlibet/", "free" => True),
		"qmm" => array("name" => "Qmmp", "url" => "http://qmmp.ylsoftware.com/index_en.php", "free" => True),
		"qmn" => array("name" => "QMPDClient", "url" => "http://bitcheese.net/wiki/QMPDClient", "free" => True),
		"qts" => array("name" => "QTScrobbler", "url" => "http://qtscrob.sourceforge.net/", "free" => True),
		"rbx" => array("name" => "Rhythmbox", "url" => "http://projects.gnome.org/rhythmbox/", "free" => True),
		"sbd" => array("name" => "Songbird", "url" => "http://www.getsongbird.com/", "free" => True),
		"scb" => array("name" => "Scrobbl", "url" => "http://www.last.fm/group/scrobbl", "free" => True),
		"sfm" => array("name" => "shell-fm", "url" => "http://nex.scrapping.cc/shell-fm/", "free" => True),
		"sls" => array("name" => "Simple Last.fm Scrobbler", "url" => "http://code.google.com/p/a-simple-lastfm-scrobbler/", "free" => True),
		"sna" => array("name" => "Sonata", "url" => "http://sonata.berlios.de/", "free" => True),
		"srd" => array("name" => "scrobd", "url" => "http://codingteam.net/project/scrobd", "free" => True),
		"spc" => array("name" => "scmpc", "url" => "http://ngls.zakx.de/scmpc/", "free" => True),
		"spm" => array("name" => "Spotify Mobile", "url" => "http://www.spotify.com/uk/mobile/overview/", "free" => False),
		"spy" => array("name" => "Spotify", "url" => "http://spotify.com/", "free" => False),
		"ss7" => array("name" => "SqueezeScrobbler 7", "url" => "http://www.slimdevices.com/pi_features.html", "free" => True),
		"sub" => array("name" => "Subsonic", "url" => "http://www.subsonic.org", "free" => True),
		"tmh" => array("name" => "scrobble for iPhone", "url" => "http://www.nodomain.org/scrobble/", "free" => True),
		"tng" => array("name" => "Clementine Player", "url" => "http://code.google.com/p/clementine-player/", "free" => True),
		"trt" => array("name" => "Trout", "url" => "http://skwire.dcmembers.com/wb/pages/software/trout.php", "free" => False),
		// TST IS FOR TESTING "tst" => array("name" => "Ecoute", "url" => "http://ecouteapp.com/", "free" => False),
		"vag" => array("name" => "vagalume", "url" => "http://vagalume.igalia.com/", "free" => True),
		"vlc" => array("name" => "VLC", "url" => "http://videolan.org", "free" => True),
		"wa2" => array("name" => "WinAmp 2", "url" => "http://www.winamp.com/", "free" => False),
		"wmp" => array("name" => "Windows Media Player", "url" => "http://www.microsoft.com/windows/WindowsMedia/", "free" => False),
		"xbm" => array("name" => "XBMC Media Center", "url" => "http://xbmc.org", "free" => True),
		"xm2" => array("name" => "XMMS2", "url" => "http://www.xmms2.org/", "free" => True),
		"xmp" => array("name" => "xmp-scrobbler", "url" => "http://www.un4seen.com/forum/?topic=5398.0", "free" => True),
		"xsp" => array("name" => "xmms2-scrobbler-py", "url" => "http://code.google.com/p/xmms2-scrobbler-py/", "free" => True),
		"yan" => array("name" => "Jerboa", "url" => "http://git.fredemmott.co.uk/?ph1.yanihp;asummary", "free" => True),
		"you" => array("name" => "Youamp", "url" => "http://www.rojtberg.net/workspace/youamp/", "free" => True),
		"zom" => array("name" => "ZOMG", "url" => "http://zomg.alioth.debian.org/", "free" => True),
	);

	$clients2 = array(
		"thisisthelibreimport2pythonthing" => array("name" => "libreimport v2", "url" => "https://gitorious.org/fmthings/lasttolibre", "free" => True),
		"thisisthelibrelovepythonthing123" => array("name" => "librelove", "url" => "https://gitorious.org/fmthings/lasttolibre", "free" => True),
		"hellothisisthegnufmwebsiteplayer" => array("name" => $site_name, "url" => $base_url, "free" => True),
	);

	// 3 char client code
	if (strlen($code) == 3) {
		if(array_key_exists($code, $clients)) {
			return $clients[$code];
		}else{
			return Null;
		}

	// quick n dirty way to get 2.0 client info, TODO add api_key field to our clients array instead and write code to find it
	// 32 char api_key
	} elseif (strlen($api_key) == 32) {
		if(array_key_exists($api_key, $clients2)) {
			return $clients2[$api_key];
		}else{
			return Null;
		}
	} else {
		return Null;
	}

}
