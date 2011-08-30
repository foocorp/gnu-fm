import QtQuick 1.1
import com.nokia.meego 1.0

Page {
    id: aboutPage
    anchors.margins: rootWin.pageMargin
    tools: commonTools

    Image {
        source: "librefm-logo.png"
        anchors.horizontalCenter: parent.horizontalCenter
        z: -1
    }

    Column {
        spacing: 10
        anchors.verticalCenterOffset: 30
        anchors.centerIn: parent

        Label {
            text: "Libre.fm MeeGo Client 0.1"
            font.pixelSize: 34
        }

        Label {
            text: "Released under the GPL 3.0 or later."
        }

        Label {
            text: "Mike Sheldon <elleo@gnu.org>"
        }

    }
}
