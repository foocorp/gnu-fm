#include <sailfishapp.h>
#include <QSettings>
#include <QQuickWindow>
#include <QGuiApplication>
#include <QQuickView>
#include <QQmlContext>
#include <QDebug>
#include "settings.h"

int main(int argc, char *argv[])
{
    QSettings::setPath(QSettings::NativeFormat, QSettings::UserScope, "/home/nemo/.local/share/harbour-librefm");
    Settings settings;
    QGuiApplication *app = SailfishApp::application(argc, argv);
    QQuickWindow::setDefaultAlphaBuffer(true);
    QQuickView *view = SailfishApp::createView();

    view->rootContext()->setContextProperty("settings", &settings);

    view->setSource(QUrl("/usr/share/harbour-librefm/qml/harbour-librefm.qml"));
    view->setResizeMode(QQuickView::SizeRootObjectToView);
    view->show();

    return app->exec();
}

