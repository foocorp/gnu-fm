import QtQuick 1.1
import com.nokia.meego 1.0

Page {
    id: stationPage
    anchors.margins: rootWin.pageMargin
    tools: preferenceTools

    property int button_height: 100
    property int button_width: 220
    property string station_type: ""

    states: [
        State {
            name: "inLandscape"
            when: !rootWin.inPortrait
            PropertyChanges {
                target: grid_station_types
                rows: 2
                columns: 3
            }
        },
        State {
            name: "inPortrait"
            when: rootWin.inPortrait
            PropertyChanges {
                target: grid_station_types
                rows: 3
                columns: 2
            }
        }
    ]


    Image {
        id: imgLibre
        source: "librefm-logo.png"
        anchors.horizontalCenter: parent.horizontalCenter
        z: -1
    }

    Grid {
        id: grid_station_types
        anchors.centerIn: parent
        anchors.verticalCenterOffset: 60
        spacing: 10

        Button {
            height: button_height
            width: button_width
            text: "Tag Station"
            onClicked: {
                station_type = "globaltags";
                lbl_input_name.text = "Tag Station"
                txt_input.placeholderText = "e.g. folk, female vocals, rock, guitar, violin"
                grid_station_types.visible = false;
                grid_input.visible = true;
            }
        }

        Button {
            height: button_height
            width: button_width
            text: "Artist Station"
            onClicked: {
                station_type = "artist";
                lbl_input_name.text = "Similar Artist Station"
                txt_input.placeholderText = "e.g. The Acousticals, Brad Sucks, Hungry Lucy"
                grid_station_types.visible = false;
                grid_input.visible = true;
            }
        }

        Button {
            height: button_height
            width: button_width
            text: "Loved Station"
            onClicked: {
                grid_station_types.visible = false;
                grid_loved_types.visible = true;
            }
        }

        Button {
            height: button_height
            width: button_width
            text: "Recommendation\nStation"
            onClicked: {
                grid_station_types.visible = false;
                grid_recommended_types.visible = true;
            }
        }

        Button {
            height: button_height
            width: button_width
            text: "Mix Station"
            onClicked: {
                grid_station_types.visible = false;
                grid_mix_types.visible = true;
            }
        }

        Button {
            height: button_height
            width: button_width
            text: "Neighbourhood\nStation"
            onClicked: {
                grid_station_types.visible = false;
                grid_neighbourhood_types.visible = true;
            }
        }

    }

    Grid {
        id: grid_loved_types
        anchors.centerIn: parent
        anchors.verticalCenterOffset: 60
        spacing: 10
        visible: false

        Button {
            height: button_height
            width: button_width
            text: "Your Loved\nStation"
            onClicked: {
                rootWin.openFile("StationPage.qml");
                rootWin.tuneStationByName("my-loved");
            }
        }

        Button {
            height: button_height
            width: button_width
            text: "Someone Else's\nLoved Station"
            onClicked: {
                station_type = "loved";
                lbl_input_name.text = "Loved Station";
                txt_input.placeholderText = "e.g. elleo, mattl, etc.";
                grid_loved_types.visible = false;
                grid_input.visible = true;
            }
        }

        Button {
            height: button_height
            width: button_width
            text: "The Community's\nLoved Station"
            onClicked: {
                rootWin.openFile("StationPage.qml");
                rootWin.tuneStation("librefm://community/loved");
            }
        }
    }

    Grid {
        id: grid_recommended_types
        anchors.centerIn: parent
        anchors.verticalCenterOffset: 60
        spacing: 10
        visible: false

        Button {
            height: button_height
            width: button_width
            text: "Your Recommendation\nStation"
            onClicked: {
                rootWin.openFile("StationPage.qml");
                rootWin.tuneStationByName("my-recommended");
            }
        }

        Button {
            height: button_height
            width: button_width
            text: "Someone Else's\nRecommendation Station"
            onClicked: {
                station_type = "recommended";
                lbl_input_name.text = "Recommendation Station";
                txt_input.placeholderText = "e.g. elleo, mattl, etc.";
                grid_recommended_types.visible = false;
                grid_input.visible = true;
            }
        }
    }

    Grid {
        id: grid_mix_types
        anchors.centerIn: parent
        anchors.verticalCenterOffset: 60
        spacing: 10
        visible: false

        Button {
            height: button_height
            width: button_width
            text: "Your Mix Station"
            onClicked: {
                rootWin.openFile("StationPage.qml");
                rootWin.tuneStationByName("my-mix");
            }
        }

        Button {
            height: button_height
            width: button_width
            text: "Someone Else's\nMix Station"
            onClicked: {
                station_type = "mix";
                lbl_input_name.text = "Mix Station";
                txt_input.placeholderText = "e.g. elleo, mattl, etc.";
                grid_mix_types.visible = false;
                grid_input.visible = true;
            }
        }
    }

    Grid {
        id: grid_neighbourhood_types
        anchors.centerIn: parent
        anchors.verticalCenterOffset: 60
        spacing: 10
        visible: false

        Button {
            height: button_height
            width: button_width
            text: "Your Neighbourhood\nStation"
            onClicked: {
                rootWin.openFile("StationPage.qml");
                rootWin.tuneStationByName("my-neighbourhood");
            }
        }

        Button {
            height: button_height
            width: button_width
            text: "Someone Else's\nNeighbourhood Station"
            onClicked: {
                station_type = "neighbours";
                lbl_input_name.text = "Neighbourhood Station";
                txt_input.placeholderText = "e.g. elleo, mattl, etc.";
                grid_neighbourhood_types.visible = false;
                grid_input.visible = true;
            }
        }
    }

    Grid {
        id: grid_input
        anchors.centerIn: parent
        anchors.verticalCenterOffset: 60
        spacing: 10
        visible: false
        rows: 3
        columns: 1

        Label {
            anchors.horizontalCenter: parent.horizontalCenter
            id: lbl_input_name
            font.weight: Font.Bold
            font.pixelSize: 30
        }

        TextField {
            id: txt_input
            width: 400
        }

        Button {
            text: "Listen"
            width: 400
            onClicked: {
                rootWin.openFile("StationPage.qml");
                if (station_type == "artist") {
                    rootWin.tuneStation("librefm://artist/" + txt_input.text + "/similarartists");
                } else if (station_type == "globaltags") {
                    rootWin.tuneStation("librefm://globaltags/" + txt_input.text);
                } else {
                    rootWin.tuneStation("librefm://user/" + txt_input.text + "/" + station_type);
                }
                txt_input.text = "";
            }
        }

    }

    ToolBarLayout {
            id: preferenceTools
            visible: true
            ToolIcon { iconId: "toolbar-back"; onClicked: {
                    if(grid_station_types.visible == true) {
                        pageStack.pop();
                    } else {
                        grid_station_types.visible = true;
                        grid_loved_types.visible = false;
                        grid_recommended_types.visible = false;
                        grid_mix_types.visible = false;
                        grid_neighbourhood_types.visible = false;
                        grid_input.visible = false;
                    }
                }
            }
    }

}
