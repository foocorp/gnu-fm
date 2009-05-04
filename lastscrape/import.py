#!/usr/bin/python

import os.path
import sys
sys.path.append(os.path.join(sys.path[0], '../scripts'))

from datetime import datetime
import getpass
from gobble import GobbleServer
from optparse import OptionParser
import time
from urllib import urlencode
from urllib2 import urlopen


if __name__ == '__main__':
    usage = "%prog <USERNAME> <SERVER> <SCROBBLE DUMP>"
    parser = OptionParser(usage=usage)
    opts,args = parser.parse_args()
    if len(args) != 3:
        parser.error("All arguments are required.")

    username,server,data = args
    password = getpass.getpass()
    gobbler = GobbleServer(server, username, password)

    for line in file(data):
        artist,track,timestamp = line.strip().split("\t")
        dt = datetime.strptime(timestamp, "%Y-%m-%dT%H:%M:%SZ")
        gobbler.add_track(artist, track, dt)
        print "Adding to post %s playing %s" % (artist, track)
    gobbler.submit()
