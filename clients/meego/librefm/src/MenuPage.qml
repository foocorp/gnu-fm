import QtQuick 1.1
import com.nokia.meego 1.0

Page {
    id: menuPage
    anchors.margins: rootWin.pageMargin

    Image {
        id: towerImg
        anchors.horizontalCenter: parent.horizontalCenter
        source: "librefm-tower.png"
        z: -1
    }

    states: [
        State {
            name: "inLandscape"
            when: !rootWin.inPortrait
            PropertyChanges {
                target: grid_menus
                rows: 1
                columns: 2
                spacing: 200
                anchors.verticalCenterOffset: 50
                anchors.horizontalCenterOffset: -60
            }
        },
        State {
            name: "inPortrait"
            when: rootWin.inPortrait
            PropertyChanges {
                target: grid_menus
                anchors.verticalCenterOffset: -72
                anchors.horizontalCenterOffset: 0
                rows: 2
                columns: 1
                spacing: 50
            }
        }
    ]

    Grid {
        id: grid_menus
        anchors.verticalCenter: parent.verticalCenter
        anchors.horizontalCenter: parent.horizontalCenter

        Column {
            id: col_buttons
            spacing: 20

            Button {
                text: "New Station"
            }

            Button {
                text: "Preferences"
            }

            Button {
                text: "About"
                onClicked: rootWin.openFile("About.qml")
            }

            Button {
                text: "Quit"
                onClicked: Qt.quit()
            }

        }

        Column {
            id: col_recent
            spacing: 10

            Label {
                id: lbl_recent
                text: "Recent Stations"
                font.weight: Font.Bold
            }

            ListModel {
                id: pagesModel
                ListElement {
                    station: "librefm://user/Elleo/loved"
                    title: "Elleo's Loved Station"
                }
                ListElement {
                    station: "librefm://user/Elleo/mix"
                    title: "Elleo's Mix Station"
                }
                ListElement {
                    station: "librefm://artist/Ani+DiFranco/similarartists"
                    title: "Ani DiFranco"
                }
                ListElement {
                    station: "librefm://globaltags/folk"
                    title: "Folk Tagged"
                }
                ListElement {
                    station: "librefm://community/loved"
                    title: "Community Favourites"
                }

            }

            ListView {
                id: listView
                height: 300
                anchors.top: lbl_recent.bottom
                model: pagesModel

                delegate:  Item {
                    id: listItem
                    height: 44
                    width: 300

                    BorderImage {
                        id: background
                        anchors.fill: parent
                        visible: mouseArea.pressed
                        source: "image://theme/meegotouch-list-background-pressed-center"
                    }

                    Row {
                        anchors.fill: parent

                        Column {
                            anchors.verticalCenter: parent.verticalCenter

                            Label {
                                id: mainText
                                text: model.title
                                font.pixelSize: 22
                            }
                        }
                    }

                    Image {
                        source: "image://theme/icon-m-common-drilldown-arrow" + (theme.inverted ? "-inverse" : "")
                        anchors.right: parent.right;
                        anchors.verticalCenter: parent.verticalCenter
                    }

                    MouseArea {
                        id: mouseArea
                        anchors.fill: background
                        onClicked: {
                            rootWin.openFile("StationPage.qml")
                            rootWin.tuneStation(station)
                        }
                    }
                }
            }
            ScrollDecorator {
                flickableItem: listView
            }
        }

    }
}
