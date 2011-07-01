import QtQuick 1.1
import com.nokia.meego 1.0

Page {
    id: stationPage
    anchors.margins: rootWin.pageMargin
    tools: commonTools

    Connections {
        target: serverComm
        onTuned: {
            lblStationName.text = stationName;
        }

    }


    Column {
        anchors.horizontalCenter: parent.horizontalCenter
        spacing: 20

        Image {
            id: imgLibre
            source: "librefm-logo.png"
            anchors.horizontalCenter: parent.horizontalCenter
            z: -1
        }

        Label {
            id: lblStationName
            text: "Tuning in..."
            anchors.horizontalCenter: parent.horizontalCenter
        }

        ButtonRow {
            exclusive: false

            Button {
                id: btnBan
                Image {
                    anchors.centerIn: parent
                    anchors.verticalCenterOffset: -1
                    source: "ban.png"
                }
            }

            Button {
                id: btnTag
                Image {
                    anchors.centerIn: parent
                    source: "image://theme/icon-m-toolbar-tag" + (theme.inverted ? "-inverse" : "")
                }
            }

            Button {
                id: btnPrevious
                Image {
                    anchors.centerIn: parent
                    source: "image://theme/icon-m-toolbar-mediacontrol-previous" + (theme.inverted ? "-inverse" : "")
                }
            }

            Button {
                id: btnPlay
                property bool playing: false;
                Image {
                    id: imgPlay
                    anchors.centerIn: parent
                    visible: false
                    source: "image://theme/icon-m-toolbar-mediacontrol-play" + (theme.inverted ? "-inverse" : "")
                }

                Image {
                    id: imgPause
                    anchors.centerIn: parent
                    source: "image://theme/icon-m-toolbar-mediacontrol-pause" + (theme.inverted ? "-inverse" : "")
                }

                onClicked: {
                    if (imgPlay.visible) {
                        imgPlay.visible = false
                        imgPause.visible = true
                    } else {
                        imgPlay.visible = true
                        imgPause.visible = false
                    }
                }


            }

            Button {
                id: btnNext
                Image {
                    anchors.centerIn: parent
                    source: "image://theme/icon-m-toolbar-mediacontrol-next" + (theme.inverted ? "-inverse" : "")
                }
            }

            Button {
                id: btnSave
                Image {
                    anchors.centerIn: parent
                    source: "image://theme/icon-m-common-save-as" + (theme.inverted ? "-inverse" : "")
                    scale: 0.8
                }
            }

            Button {
                id: btnLove
                Image {
                    anchors.centerIn: parent
                    anchors.verticalCenterOffset: -1
                    source: "love.png"
                }
            }

        }

    }

}
