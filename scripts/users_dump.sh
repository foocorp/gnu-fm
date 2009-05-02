#!/bin/sh

cd /home/librefm/turtle/data/wip || exit 1

if [ -x /usr/bin/lockfile-create ]; then
	lockfile-create /var/lock/gnukebox-userdump.lock
	lockfile-touch /var/lock/gnukebox-userdump.lock &
	LOCKER="$!"
else
	echo >&2 "Running without locking."
fi

#we rely on lack of whitespace here
LIST=$(echo "SELECT DISTINCT username FROM Users;" | psql -q -t)

for I in $LIST; do

    cat /home/librefm/scripts/license > $I.text.utf8
    
    echo 'COPY (SELECT * FROM Scrobbles where username='"'$I'"') TO STDOUT WITH CSV HEADER;' | psql -q >> $I.text.utf8

    mv -- $I.text.utf8 /home/librefm/turtle/data/
done

if [ -x /usr/bin/lockfile-create ]; then
	kill "${LOCKER}"
	lockfile-remove /var/lock/gnukebox-userdump.lock
fi
