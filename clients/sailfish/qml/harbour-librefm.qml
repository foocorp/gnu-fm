import QtQuick 2.0
import Sailfish.Silica 1.0
import "pages"

ApplicationWindow
{
    property string wsUrl: "http://libre.fm/2.0/";
    property string wsKey: "";
    initialPage: LoginPage { }
    cover: Qt.resolvedUrl("cover/CoverPage.qml")

    Rectangle {
        id: errorRect
        color: Theme.highlightColor
        width: parent.width
        height: 32
        visible: false

        Label {
            id: errorBanner
            color: "black"
            font.pixelSize: 20
            anchors.centerIn: parent
            text: ""
        }

        NumberAnimation on opacity {
            id: errorRectFadeOut
            from: 1
            to: 0
            duration: 10000
        }
    }

    function request(url, callback) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = (function(mxhr) {
            return function() { if(mxhr.readyState === XMLHttpRequest.DONE) { callback(mxhr); } }
        })(xhr);
        xhr.open('GET', url, true);
        xhr.send('');
    }

    function showError(e) {
        console.log(e.text);
        errorBanner.text = e.nodeValue;
        errorRect.visible = true;
        errorRectFadeOut.stop();
        errorRectFadeOut.start();
        console.log(e.nodeValue);
    }
}
