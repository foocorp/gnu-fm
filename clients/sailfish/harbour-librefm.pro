# The name of your app.
# NOTICE: name defined in TARGET has a corresponding QML filename.
#         If name defined in TARGET is changed, following needs to be
#         done to match new name:
#         - corresponding QML filename must be changed
#         - desktop icon filename must be changed
#         - desktop filename must be changed
#         - icon definition filename in desktop file must be changed
TARGET = harbour-librefm

CONFIG += sailfishapp

SOURCES += src/harbour-librefm.cpp \
    src/settings.cpp

OTHER_FILES += qml/harbour-librefm.qml \
    qml/cover/CoverPage.qml \
    rpm/harbour-librefm.spec \
    rpm/harbour-librefm.yaml \
    harbour-librefm.desktop \
    qml/pages/LoginPage.qml \
    qml/images/love.png \
    qml/images/librefm-tower.png \
    qml/images/librefm-logo.png \
    qml/images/librefm.svg \
    qml/images/empty-album.png \
    qml/pages/MenuPage.qml \
    qml/pages/RadioPage.qml

HEADERS += \
    src/settings.h

