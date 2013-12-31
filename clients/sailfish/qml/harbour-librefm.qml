import QtQuick 2.0
import Sailfish.Silica 1.0
import "pages"

ApplicationWindow
{
    property string wsUrl: "http://libre.fm/2.0/";
    property string wsKey: "";
    property string wsUser: "";

    initialPage: Component { LoginPage { } }
    cover: Qt.resolvedUrl("cover/CoverPage.qml")

    Component {
        id: menuPage
        MenuPage {

        }
    }

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

    function request(params, method, callback) {
        var xhr = new XMLHttpRequest();
        xhr.onreadystatechange = (function(mxhr) {
            return function() { if(mxhr.readyState === XMLHttpRequest.DONE) { callback(mxhr); } }
        })(xhr);
        if(method === "post") {
            xhr.open('POST', wsUrl, true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.setRequestHeader("Content-length", params.length);
            xhr.setRequestHeader("Connection", "close");
            xhr.send(params);
        } else {
            xhr.open('GET', wsUrl + "/?" + params, true);
            xhr.send('');
        }
    }

    function showError(e) {
        console.log(e.text);
        errorBanner.text = e.childNodes[0].nodeValue;
        errorRect.visible = true;
        errorRectFadeOut.stop();
        errorRectFadeOut.start();
        console.log(e.nodeValue);
    }

    function playStation(s) {
        console.log("Lodaing station: " + s);
        var component = Qt.createComponent("pages/RadioPage.qml");
        if( component.status === Component.Error ) {
            console.debug("Error: "+ component.errorString() );
        }
        var radioPage = component.createObject(pageStack, { station: s });
        pageStack.push(radioPage);
    }
}
