#!/usr/bin/python

import os.path
import sys
sys.path.append(os.path.join(sys.path[0], '../scripts'))

from datetime import datetime
import getpass
from gobble import get_parser, GobbleServer, GobbleTrack
import time
from urllib import urlencode
from urllib2 import urlopen


if __name__ == '__main__':
    usage = "%prog [-s <SERVER>] <USERNAME> <SCROBBLE DUMP>"
    parser = get_parser(usage=usage)
    opts,args = parser.parse_args()
    if len(args) != 2:
        parser.error("All arguments are required.")

    username,data = args
    server = opts.server
    password = getpass.getpass()
    gobbler = GobbleServer(server, username, password)

    for line in file(data):
        artist,track,timestamp = line.strip().split("\t")
        dt = datetime.strptime(timestamp, "%Y-%m-%dT%H:%M:%SZ")
        gobbler.add_track(GobbleTrack(artist, track, dt))
        print "Adding to post %s playing %s" % (artist, track)
    gobbler.submit()
