import QtQuick 2.0
import Sailfish.Silica 1.0

Page {

    SilicaFlickable {
        anchors.fill: parent;
        contentHeight: col.height + logo.height;
        contentWidth: parent.width;

        Image {
            id: logo
            source: "../images/librefm-logo.png"
            anchors.horizontalCenter: parent.horizontalCenter
            anchors.top: parent.top
            anchors.topMargin: 50
        }

        Column {
            id: col;
            anchors.top: logo.bottom
            anchors.topMargin: 50;
            width: parent.width

            Button {
                text: "Your Loved Station"
                anchors.horizontalCenter: parent.horizontalCenter
                onClicked: {
                    playStation("librefm://user/" + wsUser + "/loved");
                }
            }

            Button {
                text: "Your Recommendations Station"
                anchors.horizontalCenter: parent.horizontalCenter
                onClicked: {
                    playStation("librefm://user/" + wsUser + "/recommended");
                }
            }

            Button {
                text: "Your Mix Station"
                anchors.horizontalCenter: parent.horizontalCenter
                onClicked: {
                    playStation("librefm://user/" + wsUser + "/mix");
                }
            }

            Button {
                text: "Tag Station"
                anchors.horizontalCenter: parent.horizontalCenter
            }

            Button {
                text: "Community Station"
                anchors.horizontalCenter: parent.horizontalCenter
                onClicked: {
                    playStation("librefm://community/loved");
                }
            }

        }
    }

}

