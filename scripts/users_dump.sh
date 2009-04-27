#!/bin/sh

cd /home/librefm/turtle/data/wip || exit 1

#we rely on lack of whitespace here
LIST=$(echo "SELECT DISTINCT username FROM Users;" | psql -q -t)

for I in $LIST; do

    cat /home/librefm/scripts/license > $I.text.utf8
    
    echo 'COPY (SELECT * FROM Scrobbles where username='"'$I'"') TO STDOUT WITH CSV HEADER;' | psql -q >> $I.text.utf8

    mv -- $I.text.utf8 /home/librefm/turtle/data/
done
