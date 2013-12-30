import QtQuick 2.0
import Sailfish.Silica 1.0

Page {
    id: page
    property bool loggingIn: false;

    Component.onCompleted: {
        if(settings.value("user", false) !== false) {
            doLogin(settings.value("user"), settings.value("auth"));
        }
    }

    Image {
        id: logo
        source: "../images/librefm-logo.png"
        anchors.horizontalCenter: parent.horizontalCenter
        anchors.top: parent.top
        anchors.topMargin: 100
    }

    Column {
        id: fields
        visible: !loggingIn
        spacing: 20
        anchors.left: parent.left
        anchors.right: parent.right
        anchors.leftMargin: 10
        anchors.rightMargin: 10
        anchors.top: logo.bottom
        anchors.topMargin: 100

        TextField {
            id: username
            placeholderText: "Username"
            width: parent.width
            inputMethodHints: Qt.ImhNoAutoUppercase | Qt.ImhNoPredictiveText
            Keys.onReturnPressed: {
                if (username.text.length > 0 && password.text.length > 0) {
                    login()
                } else {
                    password.forceActiveFocus()
                }
            }
        }
        TextField {
            id: password
            placeholderText: "Password"
            echoMode: TextInput.Password
            width: parent.width
            inputMethodHints: Qt.ImhNoAutoUppercase | Qt.ImhNoPredictiveText
            Keys.onReturnPressed: {
                if (username.text.length > 0 && password.text.length > 0) {
                    login()
                } else {
                    username.forceActiveFocus()
                }
            }
        }

        Item {
            height: 10
            width: 1
        }

        Button {
            id: button
            text: "Log in"

            anchors.left: parent.left
            anchors.right: parent.right
            anchors.leftMargin: 10
            anchors.rightMargin: 10
            enabled: username.text.length > 0 && password.text.length > 0
            onClicked: {
                login()
            }
        }

        Item {
            height: 20
            width: 1
        }

        Label {
            width: parent.width
            wrapMode: Text.WordWrap
            font.pixelSize: 20
            onLinkActivated: Qt.openUrlExternally(link)
            horizontalAlignment: Text.AlignHCenter
            textFormat: Text.RichText
            text: "<style>a:link { color: " + Theme.highlightColor
                  + "; }</style>Don't have a Libre.fm account?<br><a href='http://libre.fm'>Register for free</a>."
        }
    }

    function doLogin(user, auth) {

        loggingIn = true;
        request(wsUrl + "?method=auth.getmobilesession&username=" + user + "&authToken=" + auth, function (doc) {
            loggingIn = false;
            console.log(doc.responseText)
            var e = doc.responseXML.documentElement;
            for(var i = 0; i < e.childNodes.length; i++) {
                console.log(e.childNodes[i]);
                if(e.childNodes[i].nodeName === "error") {
                    showError(e.childNodes[i]);
                }
                if(e.childNodes[i].nodeName === "key") {
                    settings.setValue("username", user);
                    settings.setValue("authToken", auth);
                    wsKey = e.childNodes[i].nodeValue;
                }
            }

        })

        password.text = ""
    }

    function login() {
        var authToken = Qt.md5(username.text.toLowerCase() + Qt.md5(password.text))
        doLogin(username.text, authToken)
    }

    Column {
        anchors.centerIn: parent
        anchors.verticalCenterOffset: 20
        visible: loggingIn
        spacing: 20

        Label {
            id: loggingText
            anchors.horizontalCenter: parent.horizontalCenter
            text: "Logging in"
        }

        BusyIndicator {
            anchors.horizontalCenter: parent.horizontalCenter
            running: parent.visible
        }
    }
}
