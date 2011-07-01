#ifndef SERVERCOMM_H
#define SERVERCOMM_H

#include <QObject>
#include <QString>
#include <QSettings>
#include <QtNetwork/QNetworkAccessManager>

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
    QSettings *settings;

signals:
    void loginFailed();
    void loginSuccessful();
    void tuned(QString stationName);

public slots:
    void login(const QString &username, const QString &password);
    void tuneStation(const QString &station);
    void wsLoginReply(QNetworkReply *reply);
    void scrobbleLoginReply(QNetworkReply *reply);
    void tuneReply(QNetworkReply *reply);
};

#endif // SERVERCOMM_H
