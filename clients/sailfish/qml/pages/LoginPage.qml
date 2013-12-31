import QtQuick 2.0
import Sailfish.Silica 1.0

Page {
    property bool loggingIn: false;

    Component.onCompleted: {
        if(settings.value("username", false) !== false) {
            doLogin(settings.value("username"), settings.value("authToken"));
        }
    }

    Image {
        id: logo
        source: "../images/librefm-logo.png"
        anchors.horizontalCenter: parent.horizontalCenter
        anchors.top: parent.top
        anchors.topMargin: 50
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
        anchors.topMargin: 50

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
            font.pointSize: 12
            onLinkActivated: Qt.openUrlExternally(link)
            horizontalAlignment: Text.AlignHCenter
            textFormat: Text.RichText
            text: "<style>a:link { color: " + Theme.highlightColor
                  + "; }</style>Don't have a Libre.fm account?<br><a href='http://libre.fm'>Register for free</a>."
        }
    }

    function doLogin(user, auth) {

        loggingIn = true;
        request("method=auth.getmobilesession&username=" + user + "&authToken=" + auth, "get", function (doc) {
            loggingIn = false;
            var e = doc.responseXML.documentElement;
            for(var i = 0; i < e.childNodes.length; i++) {
                if(e.childNodes[i].nodeName === "error") {
                    showError(e.childNodes[i]);
                }
                if(e.childNodes[i].nodeName === "session") {
                    var sess = e.childNodes[i];
                    for(var j = 0; j < sess.childNodes.length; j++ ) {
                        if(sess.childNodes[j].nodeName === "key") {
                            settings.setValue("username", user);
                            settings.setValue("authToken", auth);
                            wsKey = sess.childNodes[j].childNodes[0].nodeValue;
                            wsUser = user;
                            pageStack.clear();
                            pageStack.push(menuPage);
                            console.log(wsKey);
                        }
                    }
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
        anchors.verticalCenterOffset: 100
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
