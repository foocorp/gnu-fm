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
 * @return array ("name" => string, "url" => string, "FLOSS" => boolean)
 */
function getClientData($code) {
	$clients = array(
		"amk" => array("name" => "Amarok 1.4.x plugin", "url" => "http://amarok.kde.org/", "FLOSS" => True),
		"amp" => array("name" => "Aimp2", "url" => "http://www.aimp.ru/", "FLOSS" => False),
		"amy" => array("name" => "Anomaly", "url" => "https://chrome.google.com/webstore/detail/ajbkmdgmhbjjhephmgbpgialfbnjbmkb", "FLOSS" => True),
		"ark" => array("name" => "Amarok", "url" => "http://amarok.kde.org/", "FLOSS" => True),
		"ass" => array("name" => "Last.fm player", "url" => "http://www.last.fm/download", "FLOSS" => True),
		"atu" => array("name" => "aTunes", "url" => "http://www.atunes.org/", "FLOSS" => True),
		"aud" => array("name" => "Audacious", "url" => "http://audacious-media-player.org/", "FLOSS" => True),
		"bil" => array("name" => "billy", "url" => "http://www.sheepfriends.com/?page=billy", "FLOSS" => False),
		"blu" => array("name" => "Bluemindo", "url" => "http://bluemindo.codingteam.net/", "FLOSS" => True),
		"bsh" => array("name" => "Banshee", "url" => "http://banshee-project.org/", "FLOSS" => True),
		"bwt" => array("name" => "Bowtie", "url" => "http://www.bowtieapp.com/", "FLOSS" => False),
		"cmu" => array("name" => "CmuScrobbler", "url" => "http://n.ethz.ch/%7edflatz/cmuscrobbler/", "FLOSS" => True),
		"cpl" => array("name" => "cplay scrobbler", "url" => "http://sebi.tla.ro/cplay_scrobbler", "FLOSS" => True),
		"cub" => array("name" => "Cuberok", "url" => "http://code.google.com/p/cuberok/", "FLOSS" => True),
		"dbl" => array("name" => "Decibel Audio Player", "url" => "http://decibel.silent-blade.org/", "FLOSS" => True),
		"ddb" => array("name" => "DeaDBeeF", "url" => "http://deadbeef.sourceforge.net/", "FLOSS" => True),
		"dms" => array("name" => "donky mpdscrob", "url" => "http://github.com/mjhayes/donky/tree/master", "FLOSS" => True),
		"ems" => array("name" => "EMMS", "url" => "http://www.gnu.org/software/emms/", "FLOSS" => True),
		"exa" => array("name" => "Exaile", "url" => "http://www.exaile.org/", "FLOSS" => True),
		"foo" => array("name" => "foobar2000", "url" => "http://www.foobar2000.org/", "FLOSS" => True),
		"gmb" => array("name" => "gmusicbrowser", "url" => "http://gmusicbrowser.org/", "FLOSS" => True),
		"gmm" => array("name" => "Goggles Music Manager", "url" => "http://code.google.com/p/gogglesmm/", "FLOSS" => True),
		"gst" => array("name" => "GimmeSome Tune", "url" => "http://www.eternalstorms.at/gimmesometune/", "FLOSS" => False),
		"gua" => array("name" => "Guayadeque", "url" => "http://sourceforge.net/projects/guayadeque/", "FLOSS" => True),
		"isp" => array("name" => "iSproggler", "url" => "http://iSproggler.org/", "FLOSS" => True),
		"jaj" => array("name" => "Jajuk", "url" => "http://jajuk.info/", "FLOSS" => True),
		"ldr" => array("name" => "Libre Droid", "url" => "http://linux.mikeasoft.com/libredroid/", "FLOSS" => True),
		"lfm" => array("name" => "Libre.fm", "url" => "http://libre.fm", "FLOSS" => True),
		"lib" => array("name" => "LibreTunes", "url" => "http://libretunes.sourceforge.net/", "FLOSS" => True),
		"liv" => array("name" => "Livewwwire", "url" => "http://ciarang.com", "FLOSS" => True),
		"lpd" => array("name" => "lastPod", "url" => "http://www.lastpod.org", "FLOSS" => True),
		"lsd" => array("name" => "lastfmsubmitd", "url" => "http://www.red-bean.com/decklin/lastfmsubmitd/", "FLOSS" => True),
		"mae" => array("name" => "maemoscrobbler", "url" => "https://garage.maemo.org/projects/maemoscrobbler", "FLOSS" => True),
		"maf" => array("name" => "mafw-lastfm", "url" => "https://garage.maemo.org/projects/mafw-lastfm", "FLOSS" => True),
		"mcl" => array("name" => "MOCp-Scrobbler", "url" => "http://github.com/fluxid/mocp-scrobbler", "FLOSS" => False),
		"mcn" => array("name" => "mpdcron", "url" => "http://mpd.wikia.com/wiki/Client:MPDCRON", "FLOSS" => True),
		"mdc" => array("name" => "mpdscribble", "url" => "http://musicpd.org/", "FLOSS" => True),
		"mlr" => array("name" => "mobbler", "url" => "http://code.google.com/p/mobbler/", "FLOSS" => True),
		"mmo" => array("name" => "MediaMonkey", "url" => "http://www.mediamonkey.com/", "FLOSS" => False),
		"mms" => array("name" => "Maemo Scrobbler", "url" => "http://github.com/felipec/maemo-scrobbler", "FLOSS" => True),
		"moc" => array("name" => "music on console", "url" => "http://moc.daper.net/", "FLOSS" => True),
		"mp5" => array("name" => "mpdas", "url" => "http://50hz.ws/mpdas/", "FLOSS" => True),
		"mpc" => array("name" => "Scrobby", "url" => "http://unkart.ovh.org/scrobby/", "FLOSS" => True),
		"osx" => array("name" => "iTunes", "url" => "http://www.apple.com/itunes/", "FLOSS" => False),
		"pyj" => array("name" => "pyjama", "url" => "https://launchpad.net/pyjama", "FLOSS" => True),
		"qcd" => array("name" => "Quintessential Media Player", "url" => "http://www.quinnware.com/", "FLOSS" => False),
		"qlb" => array("name" => "Quod Libet", "url" => "http://code.google.com/p/quodlibet/", "FLOSS" => True),
		"qmm" => array("name" => "Qmmp", "url" => "http://qmmp.ylsoftware.com/index_en.php", "FLOSS" => True),
		"qmn" => array("name" => "QMPDClient", "url" => "http://bitcheese.net/wiki/QMPDClient", "FLOSS" => True),
		"qts" => array("name" => "QTScrobbler", "url" => "http://qtscrob.sourceforge.net/", "FLOSS" => True),
		"rbx" => array("name" => "Rhythmbox", "url" => "http://projects.gnome.org/rhythmbox/", "FLOSS" => True),
		"sbd" => array("name" => "Songbird", "url" => "http://www.getsongbird.com/", "FLOSS" => True),
		"scb" => array("name" => "Scrobbl", "url" => "http://www.last.fm/group/scrobbl", "FLOSS" => True),
		"sfm" => array("name" => "shell-fm", "url" => "http://nex.scrapping.cc/shell-fm/", "FLOSS" => True),
		"sls" => array("name" => "Simple Last.fm Scrobbler", "url" => "http://code.google.com/p/a-simple-lastfm-scrobbler/", "FLOSS" => True),
		"sna" => array("name" => "Sonata", "url" => "http://sonata.berlios.de/", "FLOSS" => True),
		"srd" => array("name" => "scrobd", "url" => "http://codingteam.net/project/scrobd", "FLOSS" => True),
		"spc" => array("name" => "scmpc", "url" => "http://ngls.zakx.de/scmpc/", "FLOSS" => True),
		"spm" => array("name" => "Spotify Mobile", "url" => "http://www.spotify.com/uk/mobile/overview/", "FLOSS" => False),
		"spy" => array("name" => "Spotify", "url" => "http://spotify.com/", "FLOSS" => False),
		"ss7" => array("name" => "SqueezeScrobbler 7", "url" => "http://www.slimdevices.com/pi_features.html", "FLOSS" => True),
		"sub" => array("name" => "Subsonic", "url" => "http://www.subsonic.org", "FLOSS" => True),
		"tmh" => array("name" => "scrobble for iPhone", "url" => "http://www.nodomain.org/scrobble/", "FLOSS" => True),
		"tng" => array("name" => "Clementine Player", "url" => "http://code.google.com/p/clementine-player/", "FLOSS" => True),
		"trt" => array("name" => "Trout", "url" => "http://skwire.dcmembers.com/wb/pages/software/trout.php", "FLOSS" => False),
		// TST IS FOR TESTING "tst" => array("name" => "Ecoute", "url" => "http://ecouteapp.com/", "FLOSS" => False),
		"vag" => array("name" => "vagalume", "url" => "http://vagalume.igalia.com/", "FLOSS" => True),
		"vlc" => array("name" => "VLC", "url" => "http://videolan.org", "FLOSS" => True),
		"wa2" => array("name" => "WinAmp 2", "url" => "http://www.winamp.com/", "FLOSS" => False),
		"wmp" => array("name" => "Windows Media Player", "url" => "http://www.microsoft.com/windows/WindowsMedia/", "FLOSS" => False),
		"xbm" => array("name" => "XBMC Media Center", "url" => "http://xbmc.org", "FLOSS" => True),
		"xm2" => array("name" => "XMMS2", "url" => "http://www.xmms2.org/", "FLOSS" => True),
		"xmp" => array("name" => "xmp-scrobbler", "url" => "http://www.un4seen.com/forum/?topic=5398.0", "FLOSS" => True),
		"xsp" => array("name" => "xmms2-scrobbler-py", "url" => "http://code.google.com/p/xmms2-scrobbler-py/", "FLOSS" => True),
		"yan" => array("name" => "Jerboa", "url" => "http://git.fredemmott.co.uk/?ph1.yanihp;asummary", "FLOSS" => True),
		"you" => array("name" => "Youamp", "url" => "http://www.rojtberg.net/workspace/youamp/", "FLOSS" => True),
		"zom" => array("name" => "ZOMG", "url" => "http://zomg.alioth.debian.org/", "FLOSS" => True),
	);

	if(array_key_exists($code, $clients)) {
		return $clients[$code];
	}else{
		return Null;
	}
}
