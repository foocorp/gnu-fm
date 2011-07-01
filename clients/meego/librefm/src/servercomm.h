#ifndef SERVERCOMM_H
#define SERVERCOMM_H

#include <QObject>
#include <QString>
#include <QSettings>
#include <QtXml/QDomDocument>
#include <QtNetwork/QNetworkAccessManager>
#include <QList>
#include <phonon/MediaObject>
#include "track.h"

class ServerComm : public QObject
{
    Q_OBJECT
public:
    explicit ServerComm(QObject *parent = 0);

private:
    QString ws_sk;
    QString scrobble_sk;
    QString np_url;
    QString scr_url;
    QString hs_url;
    QString ws_url;
    int currentSong;
    QSettings *settings;
    QList<Track> *playlist;
    Phonon::MediaObject *media;
    void parseTrack(QDomNode trackNode);

signals:
    void loginFailed();
    void loginSuccessful();
    void tuned(QString stationName);
    void playing(QString artist, QString album, QString title, QString imageurl);

private slots:
    void wsLoginReply(QNetworkReply *reply);
    void scrobbleLoginReply(QNetworkReply *reply);
    void tuneReply(QNetworkReply *reply);
    void playlistReply(QNetworkReply *reply);

public slots:
    void login(const QString &username, const QString &password);
    void tuneStation(const QString &station);
    void getPlaylist();
    void loadSong(int song);
    void play();
    void pause();
    void next();
    void prev();
    void love();
    void ban();

};

#endif // SERVERCOMM_H
