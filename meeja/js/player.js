/*
   GNU FM -- a free network service for sharing your music listening hab""s

   Copyright (C) 2009 Free Software Foundation, Inc

   @licstart  The following is the entire license notice for the
   JavaScript code in this page.

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

   @licend  The above is the entire license notice
   for the JavaScript code in this page.
*/

var scrobbled, now_playing;
var artist, album, track, session_key, radio_key;
var playlist = [], current_song = 0;
var player_ready = false;
var playable_songs = false;
var streaming = false;

/**
 * Initialises the javascript player (player.tpl must also be included on the target page)
 *
 * @param array list A playlist in the form ([artist, album, track, trackurl], [...]) or false if playing a radio stream
 * @param string sk Scrobble session key or false if the user isn't logged in
 * @param string rk Radio session key or false if streaming isn't required
 */
function playerInit(list, sk, rk) {
	var audio = document.getElementById("audio");
	if (!list) {
		// We're playing a stream instead of a playlist
		streaming = true;
	}

	session_key = sk;
	radio_key = rk;

	if(typeof audio.duration == "undefined") {
		//Browser doesn't support <audio>
		if(streaming) {
			audio.replaceWith("<p>Sorry, you need a browser capable of using the HTML 5 &lt;audio&gt; element to enjoy the streaming service via the Javascript player.</p>");
		}
		return;
	}
	$("#fallbackembed").replaceWith(""); // Get rid of the fallback embed, otherwise some html5 browsers will play it in addition to the js player
	if (streaming) {
		// Get playlist from radio service
		getRadioPlaylist();
	} else {
		// Otherwise we have a static playlist
		playlist = list;
		playerReady();
	}
}

/**
 * Finishes the player initialisation when the playlist has been loaded
 */
function playerReady() {
	var audio = document.getElementById("audio");

	populatePlaylist();
	if(!playable_songs) {
		return;
	}
	loadSong(0);
	audio.pause();
	audio.addEventListener("ended", songEnded, false);
	updateProgress();
	$("#play").fadeTo("normal", 1);
	$("#progressbar").progressbar({ value: 0 });
	$("#player > #interface").show();
	player_ready = true;
}

/**
 * Begins playback
 */
function play() {
	var audio = document.getElementById("audio");
	audio.play();
	if(!now_playing) {
		nowPlaying();
	}
	$("#play").fadeTo("normal", 0.5);
	$("#pause").fadeTo("normal", 1);
	$("#seekforward").fadeTo("normal", 1);
	$("#seekback").fadeTo("normal", 1);
}

/**
 * Pauses playback
 */
function pause() {
	var audio = document.getElementById("audio");
	audio.pause();
	$("#play").fadeTo("normal", 1);
	$("#pause").fadeTo("normal", 0.5);
	$("#seekforward").fadeTo("normal", 0.5);
	$("#seekback").fadeTo("normal", 0.5);
}

/**
 * Seeks backwards 10 seconds in the current song
 */
function seekBack() {
	var audio = document.getElementById("audio");
	audio.currentTime = audio.currentTime - 10;
}

/**
 * Seeks forwards 10 seconds in the current song
 */
function seekForward() {
	var audio = document.getElementById("audio");
	audio.currentTime = audio.currentTime + 10;
}

/**
 * Updates the progress bar every 900 milliseconds
 */
function updateProgress() {
	var audio = document.getElementById("audio");
	if (audio.duration > 0) {
		$("#progressbar").progressbar('option', 'value', (audio.currentTime / audio.duration) * 100);
	}
	if (!scrobbled && audio.currentTime > audio.duration / 2) {
		scrobble();
	}
	$("#currenttime").text(friendlyTime(audio.currentTime));
	$("#duration").text(friendlyTime(audio.duration));
	setTimeout("updateProgress()", 900)
}

/**
 * Called automatically when a song finished. Loads the next song if there is one
 */
function songEnded() {
	var audio = document.getElementById("audio");
	if(current_song == playlist.length - 1) {
		pause();
	} else {
		loadSong(current_song+1);
		play();
	}
}

/**
 * Outputs the HTML playlist
 */
function populatePlaylist() {
	var i, url;
	//Clear the list
	$("#playlist > #songs").text("");
	for(i = 0; i < playlist.length; i++) {
		url = playlist[i]["url"];
		// Remove non-streamable tracks
		if (url == "") {
			playlist.pop(song); // hur, pop song.
		} else {
			playable_songs = true;
		}
		$("#playlist > #songs").append("<li id='song-" + i + "'><a href='#' onclick='playSong(" + i + ")'>" + playlist[i]["artist"] + " - " + playlist[i]["track"] + "</li>");
	}
	$("#song-" + current_song).css({fontWeight : "bold"});
}

/**
 * Shows/Hides the HTML playlist display
 */
function togglePlaylist() {
	$("#playlist").toggle(1000);
	$("#showplaylist").toggle();
	$("#hideplaylist").toggle();
}

/**
 * Submits a scrobble for the current song if a scrobble session key has been
 * provided. Makes use of a simple proxy to support installations where the
 * gnukebox installation is at a different domain/sub-domain to the nixtape
 * installation.
 */
function scrobble() {
	var timestamp;
	scrobbled = true;
	if(!session_key) {
		//Not authenticated
		return;
	}
	timestamp = Math.round(new Date().getTime() / 1000);
	$.post("/scrobble-proxy.php?method=scrobble", { "a[0]" : artist, "b[0]" : album, "t[0]" : track, "i[0]" : timestamp, "s" : session_key },
		      	function(data){
				if(data.substring(0, 2) == "OK") {
					$("#scrobbled").fadeIn(5000, function() { $("#scrobbled").fadeOut(5000) } );
				} else {
					$("#scrobbled").text(data);
					$("#scrobbled").fadeIn(1000);
				}
		      	}, "text");
}

/**
 * Submits 'now playing' data to the gnukebox server. Like scrobble() this
 * makes use of a proxy.
 */
function nowPlaying() {
	var timestamp;
	var audio = document.getElementById("audio");
	now_playing = true;
	if(!session_key) {
		//Not authenticated
		return;
	}
	timestamp = Math.round(new Date().getTime() / 1000);
	$.post("/scrobble-proxy.php?method=nowplaying", { "a" : artist, "b" : album, "t" : track, "l" : audio.duration, "s" : session_key}, function(data) {}, "text");
}

/**
 * Loads a song and beings playing it.
 *
 * @param int song The song number in the playlist that should be played
 */
function playSong(song) {
	var audio = document.getElementById("audio");
	loadSong(song);
	play();
}

/**
 * Loads a song
 *
 * @param int song The song number in the playlist that should be loaded
 */
function loadSong(song) {
	var url = playlist[song]["url"];
	var audio = document.getElementById("audio");
	artist = playlist[song]["artist"];
	album = playlist[song]["album"];
	track = playlist[song]["track"];

	// Highlight current song in the playlist
	$("#song-" + current_song).css({fontWeight : "normal"});
	$("#song-" + song).css({fontWeight : "bold"});

	current_song = song;
	scrobbled = false;
	now_playing = false;
	audio.src = url;
	audio.load();

	if(streaming && current_song > playlist.length - 3) {
		//Update the playlist before the user reaches the end
		getRadioPlaylist();
	}

	if(current_song > 0) {
		$("#skipback").fadeTo("normal", 1.0);
	} else {
		$("#skipback").fadeTo("normal", 0.5);
	}

	if(current_song < playlist.length - 1) {
		$("#skipforward").fadeTo("normal", 1.0);
	} else {
		$("#skipforward").fadeTo("normal", 0.5);
	}

	$("#trackinfo > #artistname").text(artist);
	$("#trackinfo > #trackname").text(track);
}

/**
 * Retrieves a playlist from the radio streaming service.
 * A radio session key must be supplied when initialising
 * the play for this to work.
 */
function getRadioPlaylist() {
	var tracks, artist, album, title, url, i;
	$.get("/radio/xspf.php", {'sk' : radio_key, 'desktop' : 0}, function(data) {
			parser=new DOMParser();
		      	xmlDoc=parser.parseFromString(data,"text/xml");
			tracks = xmlDoc.getElementsByTagName("track")
			for(i = 0; i < tracks.length; i++) {
				try {
					artist = tracks[i].getElementsByTagName("creator")[0].childNodes[0].nodeValue;
					title = tracks[i].getElementsByTagName("title")[0].childNodes[0].nodeValue;
					album = tracks[i].getElementsByTagName("album")[0].childNodes[0].nodeValue;
					url = tracks[i].getElementsByTagName("location")[0].childNodes[0].nodeValue;
					playlist.push({"artist" : artist, "album" : album, "track" : title, "url" : url});
				} catch(err) {
				}
			}
			if(!player_ready) {
				playerReady();
			} else {
				populatePlaylist();
				// Re-enable the skip forward button now that we have more tracks
				$("#skipforward").fadeTo("normal", 1.0);
			}
		}, "text");
}

/**
 * Plays the song previous to the current one in the playlist
 */
function skipBack() {
	playSong(current_song - 1);
}

/**
 * Plays the song after the current one in the playlist
 */
function skipForward() {
	playSong(current_song + 1);
}

/**
 * Converts a timestamp to "MM:SS" format.
 *
 * @param int timestamp A timestamp in seconds.
 * @return string The provided time in "MM:SS" format
 */
function friendlyTime(timestamp) {
	mins = Math.floor(timestamp / 60);
	sec = String(Math.floor(timestamp % 60));
	if(sec.length == 1) { sec = "0" + sec }
	return mins + ":" + sec
}
