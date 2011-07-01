#ifndef SERVERCOMM_H
#define SERVERCOMM_H

#include <QObject>
#include <QString>
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

signals:
    void loginFailed();
    void loginSuccessful();

public slots:
    void login(const QString &username, const QString &password);
    void launchStation(const QString &station);
    void wsLoginReply(QNetworkReply *reply);
    void scrobbleLoginReply(QNetworkReply *reply);
};

#endif // SERVERCOMM_H
