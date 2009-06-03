#!/bin/sh

/usr/bin/pg_dump -n public -f "/home/librefm/pg_dumps/$(/bin/date '+%Y%m%d').dump"

/usr/bin/pg_dump -t album -t artist -t clientcodes -t countries -t now_playing -t places -t scrobble_track -t similar_artist -t tags -t track -f /home/librefm/pg_dumps/safedump.dump
lzma -f /home/librefm/pg_dumps/safedump.dump
