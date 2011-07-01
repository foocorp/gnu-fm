#include <QDebug>
#include <QCryptographicHash>
#include <QUrl>
#include <QtNetwork/QNetworkRequest>
#include <QtNetwork/QNetworkReply>
#include <QDateTime>
#include <QtXml/QDomDocument>
#include "servercomm.h"

ServerComm::ServerComm(QObject *parent) :
    QObject(parent)
{
    hs_url = "http://turtle.libre.fm/";
    ws_url = "http://alpha.libre.fm/2.0/";
    settings = new QSettings("Libre.fm", "Libre.fm");

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
    QDomNode n = root.firstChild();
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

    QNetworkAccessManager *tune_netman = new QNetworkAccessManager(this);
    connect(tune_netman, SIGNAL(finished(QNetworkReply*)), this, SLOT(tuneReply(QNetworkReply*)));
    QUrl url = QUrl(ws_url);
    url.addQueryItem("method", "radio.tune");
    url.addQueryItem("sk", ws_sk);
    url.addQueryItem("station", station);
    tune_netman->get(QNetworkRequest(url));
}
