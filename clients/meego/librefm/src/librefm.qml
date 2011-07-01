import QtQuick 1.1
import com.nokia.meego 1.0

PageStackWindow {
    id: rootWin
    property int pageMargin: 16
    initialPage: LoginPage { }

    signal login(string username, string password)
    signal tuneStation(string station)
    signal next()
    signal prev()
    signal play()
    signal pause()
    signal love()
    signal ban()

    function openFile(file) {
        var component = Qt.createComponent(file)
        if (component.status == Component.Ready)
            pageStack.push(component);
        else
            console.log("Error loading component:", component.errorString());
    }

    ToolBarLayout {
            id: commonTools
            visible: false
            ToolIcon { iconId: "toolbar-back"; onClicked: { pageStack.pop(); pause(); } }
     }
}
