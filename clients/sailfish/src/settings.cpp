#include "settings.h"
#include <QSettings>
#include <QDebug>
#include <QGuiApplication>


Settings::Settings(QObject *parent)
    : QObject(parent)
{
}

Settings::Settings(const Settings &settings)
    : QObject(0)
{
    Q_UNUSED(settings)
}

void Settings::setValue(const QString &setting, const QVariant &value)
{
    settings.setValue(setting, value);
    emit valueChanged(setting, value);
}

QVariant Settings::value(const QString &setting, const QVariant &defaultValue)
{
    return settings.value(setting, defaultValue);
}

QVariant Settings::value(const QString &setting)
{
    return settings.value(setting);
}

bool Settings::remove(const QString &setting)
{
    if (!settings.contains(setting)) {
        return false;
    }

    settings.remove(setting);
    return true;
}

void Settings::clear()
{
    settings.clear();
}
