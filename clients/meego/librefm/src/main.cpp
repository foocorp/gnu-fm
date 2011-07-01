#include <QApplication>
#include <QDeclarativeContext>
#include <QDeclarativeView>

#include "servercomm.h"

int main(int argc, char *argv[])
{
    QApplication app(argc, argv);
    QDeclarativeView view;
    view.setSource(QUrl::fromLocalFile("src/librefm.qml"));
    //view.setSource(QUrl::fromLocalFile(DATADIR "/librefm/librefm.qml"));
    QObject *root = (QObject*)(view.rootObject());

    ServerComm sc;
    view.rootContext()->setContextProperty("serverComm", &sc);
    QObject::connect(root, SIGNAL(login(QString, QString)), &sc, SLOT(login(QString, QString)));
    QObject::connect(root, SIGNAL(launchStation(QString, QString)), &sc, SLOT(launchStation(QString, QString)));
    QObject::connect((QObject*)view.engine(), SIGNAL(quit()), &app, SLOT(quit()));

    view.showFullScreen();
    return app.exec();
}

