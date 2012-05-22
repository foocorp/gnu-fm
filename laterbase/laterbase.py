# Laterbase, part of GNU FM -- a free network service for sharing your
# music listening habits
#
#    Copyright (C) 2009, 2012 Free Software Foundation, Inc
#
#    This program is free software: you can redistribute it and/or modify
#    it under the terms of the GNU Affero General Public License as published by
#    the Free Software Foundation, either version 3 of the License, or
#    (at your option) any later version.
#
#    This program is distributed in the hope that it will be useful,
#    but WITHOUT ANY WARRANTY; without even the implied warranty of
#    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
#    GNU Affero General Public License for more details.
#
#    You should have received a copy of the GNU Affero General Public License
#    along with this program.  If not, see <http://www.gnu.org/licenses/>.

import sys
import os
import getpass
import fileinput

LATERBASE = os.path.abspath("/home/" + getpass.getuser() + "/.laterbase")

def showhelp():
    print " "
    print "88        db    888888 888888 8888Yb 8888Yb    db    .dP8Y8 888888 "
    print "88       dPYb     88   88__   88__dP 88__dP   dPYb   `Ybo.8 88__   "
    print "88  .o  dP__Yb    88   8888   888Yb  8888Yb  dP__Yb  o.`Y8b 8888   "
    print "88ood8 dP8888Yb   88   888888 88  Yb 88oodP dP8888Yb 8bodP' 888888 "
    print " "
    print "Got a job you really don't want to do? Stick it on the laterbase!"
    print " "
    print "Usage:"
    print " "
    print "(Put this nice python script in somewhere useful like /usr/bin/lb)"
    print " "
    print "Then, just type lb wash car"
    print "And hey Presto, 'wash car' is added to your laterbase."


if (len(sys.argv) > 1):
    if( sys.argv[1] == 'list' ):
        try:
            ins = open( LATERBASE, "r" )
            for line in ins:
                print line
            ins.close()
        except IOError as e:
            print "You don't have anything on your laterbase. Try lb <item> to add something to the laterbase."

    elif( sys.argv[1] == 'help' ):
        showhelp()
    else:
        
        n = ""

        for x in range (1,len(sys.argv)):
            n = n + sys.argv[x] + " "

        n = n.strip()

        try:
            f = open(LATERBASE, "a")
            try:
                f.write (n)
                print "Added '" + n + "' to the laterbase."
            finally:
                f.close()
        except IOError:
            print "Cannot write to ~/.laterbase."


