/*
   Libre.fm -- a free network service for sharing your music listening habits

   Copyright (C) 2009 Libre.fm Project

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
var artist, album, track, session_key;
var playlist, current_song = 0;
var playable_songs = false;

function playerInit(list, sk) {
	//Explicitly call stop() here, since not everything seems to support 'autoplay="false"' yet
	var audio = document.getElementById("audio");
	if(typeof audio.duration == "undefined") {
		//Browser doesn't support <audio>
		return;
	}
	$("#fallbackembed").replaceWith(""); // Get rid of the fallback embed, otherwise html5 browsers will play it in addition to the js player
	playlist = list;
	populatePlaylist();
	if(!playable_songs) {
		alert("No playable songs");
		return;
	}
	session_key = sk;
	loadSong(0);
	audio.pause();
	audio.addEventListener("ended", songEnded, false);
	updateProgress();
	$("#play").fadeTo("normal", 1);
	$("#progressbar").progressbar({ value: 0 });
	scrobbled = false;
	now_playing = false;
	$("#player > #interface").show();
}

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

function pause() {
	var audio = document.getElementById("audio");
	audio.pause();
	$("#play").fadeTo("normal", 1);
	$("#pause").fadeTo("normal", 0.5);
	$("#seekforward").fadeTo("normal", 0.5);
	$("#seekback").fadeTo("normal", 0.5);
}

function seekBack() {
	var audio = document.getElementById("audio");
	audio.currentTime = audio.currentTime - 10;
}

function seekForward() {
	var audio = document.getElementById("audio");
	audio.currentTime = audio.currentTime + 10;
}

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

function songEnded() {
	var audio = document.getElementById("audio");
	if(current_song == playlist.length - 1) {
		pause();
	} else {
		current_song++;
		loadSong(current_song);
		play();
	}
}

function populatePlaylist() {
	var i, url;
	for(i = 0; i < playlist.length; i++) {
		url = playlist[i]["url"];
		// Remove non-streamable tracks
		if (url == "") {
			playlist.pop(song); // hur, pop song.
		} else {
			playable_songs = true;
		}
		$("#playlist > #songs").append("<li><a href='#' onclick='playSong(" + i + ")'>" + playlist[i]["artist"] + " - " + playlist[i]["track"] + "</li>");
	}
}

function togglePlaylist() {
	$("#playlist").toggle(1000);
	$("#showplaylist").toggle();
	$("#hideplaylist").toggle();
}

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

function playSong(song) {
	var audio = document.getElementById("audio");
	loadSong(song);
	play();
}

function loadSong(song) {
	var url = playlist[song]["url"];
	var audio = document.getElementById("audio");
	artist = playlist[song]["artist"];
	album = playlist[song]["album"];
	track = playlist[song]["track"];
	current_song = song;
	scrobbled = false;
	now_playing = false;
	audio.src = url;
	audio.load();

	$("#trackinfo > #artistname").text(artist);
	$("#trackinfo > #trackname").text(track);
}

function friendlyTime(timestamp) {
	mins = Math.floor(timestamp / 60);
	sec = String(Math.floor(timestamp % 60));
	if(sec.length == 1) { sec = "0" + sec }
	return mins + ":" + sec
}
