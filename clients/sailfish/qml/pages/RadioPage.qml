import QtQuick 2.0
import QtMultimedia 5.0
import Sailfish.Silica 1.0

Page {

    property string station: "";

    Component.onCompleted: {
        tune(station);
    }

    MediaPlayer {
        id: player
    }

    Column {

        anchors.fill: parent

        Label {
            id: stationName
            anchors.horizontalCenter: parent.horizontalCenter;
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
                source: player.playing ? "image://theme/icon-m-pause"
                                       : "image://theme/icon-m-play"
                opacity: play.pressed ? 0.4 : 1.0
            }
            MouseArea {
                id: play
                anchors.fill: parent
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
                            stationName.text = st.childNodes[j].childNodes[0].nodeValue;
                        }
                    }
                    fetchPlaylist();
                }
            }
        });
    }

    function fetchPlaylist() {
        request("method=radio.getplaylist&sk=" + wsKey, "post", function(doc) {
            console.log(doc.responseText) ;
            var e = doc.responseXML.documentElement;
            for(var i = 0; i < e.childNodes.length; i++) {
                console.log(e.childNodes[i].nodeName);
            }
        });
    }

}
