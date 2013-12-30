#ifndef SETTINGS_H
#define SETTINGS_H

#include <QObject>
#include <QVariant>
#include <QSettings>


class Settings : public QObject
{
    Q_OBJECT

public:
    explicit Settings(QObject *parent = 0);
    explicit Settings(const Settings &settings);

public slots:
    void setValue(const QString &setting, const QVariant &value);
    QVariant value(const QString &setting, const QVariant &defaultValue);
    QVariant value(const QString &setting);
    bool remove(const QString &setting);
    void clear();

private:
    QSettings settings;

signals:
    void valueChanged(const QString &setting, const QVariant &value);
};

#endif // SETTINGS_H
