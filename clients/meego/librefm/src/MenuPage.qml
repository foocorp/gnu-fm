import QtQuick 1.1
import com.nokia.meego 1.0

Page {
    id: menuPage
    property int buttonWidth: 300
    anchors.margins: rootWin.pageMargin

    Image {
        id: towerImg
        anchors.centerIn: parent
        source: "librefm-tower.png"
        z: -1
    }


    Grid {
        rows: screen.orientation == Screen.Landscape || screen.orientation == Screen.LandscapeInverted ? 2 : 1
        anchors.verticalCenter: parent.verticalCenter
        anchors.verticalCenterOffset: 40
        anchors.horizontalCenter: parent.horizontalCenter
        spacing: 200

        Column {
            id: col_recent
            spacing: 10

            Label {
                id: lbl_recent
                text: "Recent Stations"
                font.weight: Font.Bold
                width: buttonWidth
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
                    width: buttonWidth

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
    }
}
