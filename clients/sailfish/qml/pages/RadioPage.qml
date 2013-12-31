import QtQuick 2.0
import QtMultimedia 5.0
import Sailfish.Silica 1.0

Page {
    id: radioPage;

    property string station: "";
    property int currentTrack: 0;

    Component.onCompleted: {
        tune(station);
    }

    ListModel {
        id: playlist;
    }

    MediaPlayer {
        id: player
        autoPlay: true
        onError: {
            console.log("Track unplayable");
            playNext();
        }
        onStatusChanged: {
            if(status == MediaPlayer.EndOfMedia) {
                playNext();
            }
        }

        onSourceChanged: {
            creator.text = playlist.get(currentTrack).creator;
            title.text = playlist.get(currentTrack).title;
            cover.source = playlist.get(currentTrack).image;
            console.log("Source changed: " + source);
        }
    }

    Column {

        anchors.fill: parent

        PageHeader {
            id: stationName
            anchors.horizontalCenter: parent.horizontalCenter;
        }

        Image {
            id: cover
            width: parent.width;
            height: parent.width;
        }

        Row {
            width: parent.width

            Label {
                id: creator
            }
            Label {
                text: " - "
            }
            Label {
                id: title
            }
        }

        SilicaListView {
            model: playlist
            visible: false;
            height: parent.height / 2.0;
            width: parent.width;
            delegate: Component {
                Row {
                    Label {
                        text: creator
                    }
                    Label {
                        text: " - "
                    }
                    Label {
                        text: title
                    }
                }
            }
        }

    }

    Row {
        id: controls
        spacing: 30
        anchors.bottom: parent.bottom

        Item {
            width: 80; height: 100
            anchors.verticalCenter: parent.verticalCenter
            Image {
                id: favIcon
                anchors.centerIn: parent
                opacity: enabled ? (starArea.pressed ? 0.4 : 1.0) : 0.2
                source: "image://theme/icon-m-favorite"
            }

            MouseArea {
                id: starArea
                anchors.fill: parent
                anchors.margins: -15
            }
        }

        Item {
            width: 80; height: 100
            anchors.verticalCenter: parent.verticalCenter
            Image {
                anchors.centerIn: parent
                source: "image://theme/icon-m-previous-song"
                opacity: previous.pressed ? 0.4 : 1.0
            }
            MouseArea {
                id: previous
                anchors.fill: parent
            }
        }

        Item {
            width: 80; height: 100
            anchors.verticalCenter: parent.verticalCenter
            Image {
                anchors.centerIn: parent
                source: player.playbackState == player.PlayingState ? "image://theme/icon-m-pause"
                                       : "image://theme/icon-m-play"
                opacity: play.pressed ? 0.4 : 1.0
            }
            MouseArea {
                id: play
                anchors.fill: parent
                onClicked: {
                    player.source = playlist.get(currentTrack).location;
                    if(player.playbackState == player.PlayingState) {
                        player.pause();
                    } else {
                        player.play();
                    }
                }
            }
        }

        Item {
            width: 80; height: 100
            anchors.verticalCenter: parent.verticalCenter
            Image {
                anchors.centerIn: parent
                source: "image://theme/icon-m-next-song"
                opacity: next.pressed ? 0.4 : 1.0
            }
            MouseArea {
                id: next
                anchors.fill: parent
                onClicked: {
                    playNext();
                }
            }
        }
    }


    function tune(s) {
        console.log("Tuning to: " + s);
        request("method=radio.tune&station=" + s + "&sk=" + wsKey, "post", function(doc) {
            var e = doc.responseXML.documentElement;
            console.log(doc.responseText);
            for(var i = 0; i < e.childNodes.length; i++) {
                if(e.childNodes[i].nodeName === "error") {
                    showError(e.childNodes[i]);
                }
                if(e.childNodes[i].nodeName === "station") {
                    var st = e.childNodes[i];
                    for(var j = 0; j < st.childNodes.length; j++ ) {
                        if(st.childNodes[j].nodeName === "name") {
                            stationName.title = st.childNodes[j].childNodes[0].nodeValue;
                        }
                    }
                    fetchPlaylist();
                }
            }
        });
    }

    function fetchPlaylist() {
        request("method=radio.getplaylist&sk=" + wsKey, "post", function(doc) {
            var e = doc.responseXML.documentElement;
            for(var i = 0; i < e.childNodes.length; i++) {
                if(e.childNodes[i].nodeName === "error") {
                    showError(e.childNodes[i]);
                }
                if(e.childNodes[i].nodeName === "trackList") {
                    var tl = e.childNodes[i];
                    for(var j = 0; j < tl.childNodes.length; j++) {
                        if(tl.childNodes[j].nodeName === "track") {
                            var t = tl.childNodes[j];
                            var track = {}
                            for(var k = 0; k < t.childNodes.length; k++) {
                                try {
                                    if(t.childNodes[k].nodeName === "location") {
                                        track.location = t.childNodes[k].childNodes[0].nodeValue;
                                    } else if(t.childNodes[k].nodeName === "title") {
                                        track.title= t.childNodes[k].childNodes[0].nodeValue;
                                    } else if(t.childNodes[k].nodeName === "album") {
                                        track.album = t.childNodes[k].childNodes[0].nodeValue;
                                    } else if(t.childNodes[k].nodeName === "creator") {
                                        track.creator = t.childNodes[k].childNodes[0].nodeValue;
                                    } else if(t.childNodes[k].nodeName === "duration") {
                                        track.duration = t.childNodes[k].childNodes[0].nodeValue;
                                    } else if(t.childNodes[k].nodeName === "image") {
                                        track.image = t.childNodes[k].childNodes[0].nodeValue;
                                    }
                                } catch(err) {
                                    console.debug(err.message);
                                }
                            }
                            playlist.append(track)
                        }
                    }
                }
            }
        });
    }

    function playPrev() {
        currentTrack--;
        if(currentTrack < 0) {
            currentTrack = 0;
        }
        player.source = playlist.get(currentTrack).location;
    }

    function playNext() {
        currentTrack++;
        console.log(currentTrack + " / " +  (playlist.count - 1));
        if(currentTrack > playlist.count - 3) {
            fetchPlaylist();
        }

        if(currentTrack > playlist.count) {
            // We're having difficulty getting the next playlist set in TimePicker
            // so play the last song in the playlist again
            currentTrack--;
        }
        player.source = playlist.get(currentTrack).location;
    }

}
