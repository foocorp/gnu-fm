#!/bin/bash

if [ -z "$1" ] ; then
    echo "You have to specify a target directory." >&2
    exit 1
fi

BASEDIR="$1"

if [ ! -d $BASEDIR ] ; then
	mkdir -p $BASEDIR
fi

BASEDIR=$(cd "$BASEDIR" && pwd)
DATE=$(date '+%Y-%m-%d')
svn export svn://svn.savannah.nongnu.org/librefm "$BASEDIR"/librefm > /dev/null
cd "$BASEDIR" && mysqldump -h <host> -u <user> -p<password> <database> Artist Album Track Scrobbles >database.dump && gzip -c database.dump > librefm_database_"$DATE".gz


I=$(cd "$BASEDIR"/librefm/trunk/ && grep -c "GNU Affero General Public License" `find . -name "*.php"` --exclude="*Email*"| grep :0 | wc -l)
if [ $I == 0 ]; then
	cd "$BASEDIR" && tar zcf librefm_"$DATE".tar.gz librefm
	rm -r "$BASEDIR"/librefm
else
    echo "Tainted file!" >&2
fi


