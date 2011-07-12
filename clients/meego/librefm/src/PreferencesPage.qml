import QtQuick 1.1
import com.nokia.meego 1.0

Page {
    id: stationPage
    anchors.margins: rootWin.pageMargin
    tools: commonTools

    Image {
        id: imgLibre
        source: "librefm-logo.png"
        anchors.horizontalCenter: parent.horizontalCenter
        z: -1
    }

    Column {
        anchors.centerIn: parent

        Button {
            text: "Change Login Details"
            onClicked: rootWin.openFile("LoginPage.qml")
        }
    }
}
