import QtQuick 1.1
import com.nokia.meego 1.0

Page {
    id: loginPage
    anchors.margins: rootWin.pageMargin

    Connections {
        target: serverComm
        onLoginFailed: {
            msg_logging_in.close();
            msg_login_failed.open();
        }

        onLoginSuccessful: {
            msg_logging_in.close();
            rootWin.openFile("MenuPage.qml");
        }

        onLoggingIn: {
            msg_logging_in.open();
        }
    }

    Image {
        id: imgLibre
        source: "librefm-logo.png"
        anchors.horizontalCenter: parent.horizontalCenter
        z: -1
    }

    Column {
        spacing: 10
        anchors.centerIn: parent
        anchors.verticalCenterOffset: 40
        width: 400

        TextField {
            id: txt_username
            placeholderText: "Username"
            width: parent.width
        }

        TextField {
            id: txt_password
            placeholderText: "Password"
            echoMode: TextInput.PasswordEchoOnEdit
            width: parent.width
        }

        Button {
            text: "Login"
            onClicked: {
                rootWin.login(txt_username.text, txt_password.text)
            }
            width: parent.width
        }

        Button {
            text: "Quit"
            onClicked: Qt.quit()
            width: parent.width
        }
    }

    QueryDialog {
        id: msg_logging_in
        titleText: "Logging in..."
        rejectButtonText: "Cancel"
    }

    QueryDialog {
        id: msg_login_failed
        titleText: "Login failed"
        message: "Invalid username or password"
        acceptButtonText: "Okay"
    }

}
