#ifndef TRACK_H
#define TRACK_H

#include <QObject>
#include <QString>

class Track
{
public:
    explicit Track();
    QString location;
    QString title;
    QString album;
    QString artist;
    QString image;


signals:

public slots:

};

#endif // TRACK_H
