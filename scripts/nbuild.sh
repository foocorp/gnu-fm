#!/bin/sh

if [ -z "$1" ] ; then
    echo "You have to specify a target directory." >&2
    exit 1
fi

BASEDIR="$1"

BASEDIR=$(cd "$BASEDIR" && pwd)

svn export svn://svn.savannah.nongnu.org/librefm "$BASEDIR"/librefm

I=$(cd "$BASEDIR"/librefm/trunk/ && grep -c "GNU Affero General Public License" `find . -name "*.php"` --exclude="*Email*"| grep :0 | wc -l)
if [ $I == 0 ]; then
    echo "All clear!"
	cd "$BASEDIR" && tar zcf librefm_$(date '+%Y-%m-%d').tar.gz librefm
	rm -r "$BASEDIR"/librefm
else
    echo "Tainted file!"
fi


