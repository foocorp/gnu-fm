#include <QDebug>
#include <QCryptographicHash>
#include <QUrl>
#include <QtNetwork/QNetworkRequest>
#include <QtNetwork/QNetworkReply>
#include <QDateTime>
#include <QtXml/QDomDocument>
#include <phonon/AudioOutput>
#include "servercomm.h"

ServerComm::ServerComm(QObject *parent) :
    QObject(parent)
{
    hs_url = "http://turtle.libre.fm/";
    ws_url = "http://alpha.libre.fm/2.0/";
    playlist = new QList<Track>();
    settings = new QSettings("Libre.fm", "Libre.fm");
    currentSong = -1;
    media = new Phonon::MediaObject(this);
    Phonon::AudioOutput *audioOutput = new Phonon::AudioOutput(Phonon::MusicCategory, this);
    Phonon::createPath(media, audioOutput);

    // Check login details
    qDebug() << "Checking settings...";
    if (settings->contains("Auth/username") && settings->contains("Auth/password")) {
        qDebug() << "Logging in...";
        login(settings->value("Auth/username").toString(), settings->value("Auth/password").toString());
        qDebug() << "Username:" << settings->value("Auth/username").toString();
    }
}

void ServerComm::login(const QString &username, const QString &password) {
    qDebug() << "Logging in...";
    if(username.isEmpty() || password.isEmpty()) {
        loginFailed();
        return;
    }
    QString passMD5 = QCryptographicHash::hash(QByteArray(password.toAscii()), QCryptographicHash::Md5).toHex();
    long timestamp = QDateTime::currentDateTime().toTime_t();
    QString token = QCryptographicHash::hash(QByteArray(QString(QString(passMD5) + QString::number(timestamp)).toAscii()), QCryptographicHash::Md5).toHex();
    QString wstoken = QCryptographicHash::hash(QByteArray(QString(username.toLower() + QString(passMD5)).toAscii()), QCryptographicHash::Md5).toHex();
    // Scrobble login
    QNetworkAccessManager *s_netman = new QNetworkAccessManager(this);
    connect(s_netman, SIGNAL(finished(QNetworkReply*)), this, SLOT(scrobbleLoginReply(QNetworkReply*)));
    QUrl url = QUrl(hs_url);
    url.addQueryItem("hs", "true");
    url.addQueryItem("p", "1.2");
    url.addQueryItem("c", "mee");
    url.addQueryItem("t", QString::number(timestamp));
    url.addQueryItem("u", username);
    url.addQueryItem("a", token);
    s_netman->get(QNetworkRequest(url));

    QNetworkAccessManager *ws_netman = new QNetworkAccessManager(this);
    connect(ws_netman, SIGNAL(finished(QNetworkReply*)), this, SLOT(wsLoginReply(QNetworkReply*)));
    // Webservice login
    url = QUrl(ws_url);
    url.addQueryItem("method", "auth.getmobilesession");
    url.addQueryItem("username", username);
    url.addQueryItem("authToken", wstoken);
    qDebug() << "AuthToken:" << wstoken;
    ws_netman->get(QNetworkRequest(url));

    // Save authentication details
    settings->setValue("Auth/username", username);
    settings->setValue("Auth/password", password);
}

void ServerComm::scrobbleLoginReply(QNetworkReply *reply) {
    char line[256];
    reply->readLine(line, 256);
    if (QString(line).contains("BADAUTH")) {
        qDebug() << "Scrobble login failed.";
        loginFailed();
    } else if(QString(line).contains("OK")) {
            reply->readLine(line, 256);
            scrobble_sk = QString(line).trimmed();
            reply->readLine(line, 256);
            np_url = QString(line).trimmed();
            reply->readLine(line, 256);
            scr_url = QString(line).trimmed();
            qDebug() << "Scrobble login complete, scrobble session key is" << scrobble_sk << "Now playing URL:" << np_url << "Scrobble URL:" << scr_url;
            if (!ws_sk.isEmpty()) {
                loginSuccessful();
            }
    }

}

void ServerComm::wsLoginReply(QNetworkReply *reply) {
    QDomDocument xml("wsresponse");
    xml.setContent(reply);
    QDomElement root = xml.documentElement();
    for(QDomNode n = root.firstChild(); !n.isNull(); n = n.nextSibling()) {
        QDomElement e = n.toElement();
        if(!e.isNull()) {
            if(e.tagName() == "error") {
                loginFailed();
                return;
            }

            if(e.tagName() == "session") {
                for(QDomNode c = n.firstChild(); !c.isNull(); c = c.nextSibling()) {
                    QDomElement ce = c.toElement();
                    if(ce.tagName() == "key") {
                        ws_sk = ce.text();
                        qDebug() << "Webservice key:" << ws_sk;
                        if (!scrobble_sk.isEmpty()) {
                            loginSuccessful();
                        }
                    }
                }
            }
        }
    }
}

void ServerComm::tuneStation(const QString &station) {
    qDebug() << "Tuning to station: " << station;
    // Clear the playlist
    playlist = new QList<Track>();
    QNetworkAccessManager *tune_netman = new QNetworkAccessManager(this);
    connect(tune_netman, SIGNAL(finished(QNetworkReply*)), this, SLOT(tuneReply(QNetworkReply*)));
    QUrl url = QUrl(ws_url);
    QByteArray params;
    params.append("method=radio.tune");
    params.append("&sk="); params.append(ws_sk);
    params.append("&station="); params.append(station);
    tune_netman->post(QNetworkRequest(url), params);
}

void ServerComm::tuneReply(QNetworkReply *reply) {
    QDomDocument xml("tuneresponse");
    xml.setContent(reply);
    QDomElement root = xml.documentElement();
    for(QDomNode n = root.firstChild(); !n.isNull(); n = n.nextSibling()) {
        QDomElement e = n.toElement();
        if(!e.isNull()) {
            if(e.tagName() == "error") {
                qWarning() << "Station tuning failed.";
            }

            if(e.tagName() == "station") {
                for(QDomNode c = n.firstChild(); !c.isNull(); c = c.nextSibling()) {
                    QDomElement ce = c.toElement();
                    if(ce.tagName() == "name") {
                        QString stationName = ce.text().remove(0, 9); // Remove 'Libre.fm' from the start of station names
                        qDebug() << "Tuned to:" << stationName;
                        tuned(stationName);
                        getPlaylist();
                    }
                }
            }
        }
    }
}

void ServerComm::getPlaylist() {
    qDebug() << "Fetching playlist";

    QNetworkAccessManager *playlist_netman = new QNetworkAccessManager(this);
    connect(playlist_netman, SIGNAL(finished(QNetworkReply*)), this, SLOT(playlistReply(QNetworkReply*)));
    QUrl url = QUrl(ws_url);
    url.addQueryItem("method", "radio.getPlaylist");
    url.addQueryItem("sk", ws_sk);
    playlist_netman->get(QNetworkRequest(url));
}

void ServerComm::playlistReply(QNetworkReply *reply) {
    qDebug() << "Playlist retrieved";
    QDomDocument xml("playlist");
    xml.setContent(reply);
    QDomElement root = xml.documentElement();
    for(QDomNode n = root.firstChild(); !n.isNull(); n = n.nextSibling()) {
        QDomElement e = n.toElement();
        if(!e.isNull()) {
            if(e.tagName() == "trackList") {
                for(QDomNode c = n.firstChild(); !c.isNull(); c = c.nextSibling()) {
                    QDomElement ce = c.toElement();
                    if(ce.tagName() == "track") {
                        parseTrack(c);
                    }
                }
            }
        }
    }
    if(currentSong == -1) {
        loadSong(0);
    }
}

void ServerComm::parseTrack(QDomNode trackNode) {
    Track *t = new Track();
    for(QDomNode n = trackNode.firstChild(); !n.isNull(); n = n.nextSibling()) {
        QDomElement e = n.toElement();
        if(!e.isNull()) {
            if(e.tagName() == "creator") {
                t->artist = e.text();
            } else if(e.tagName() == "album") {
                t->album = e.text();
            } else if(e.tagName() == "title") {
                t->title = e.text();
            } else if(e.tagName() == "location") {
                t->location = e.text();
            } else if(e.tagName() == "image") {
                t->image = e.text();
            }
        }
    }
    qDebug() << "Artist: " << t->artist;
    playlist->append(*t);
}

void ServerComm::loadSong(int song) {
    currentSong = song;
    Track t = playlist->at(song);
    playing(t.artist, t.album, t.title, t.image);
    QUrl url(t.location);
    media->setCurrentSource(url);
    play();

    if (song >= playlist->length() - 3) {
        getPlaylist();
    }
}

void ServerComm::play() {
    media->play();
}

void ServerComm::pause() {
    media->pause();
}

void ServerComm::next() {
    loadSong(++currentSong);
}

void ServerComm::prev() {
    if(currentSong > 0) {
        loadSong(--currentSong);
    }
}
