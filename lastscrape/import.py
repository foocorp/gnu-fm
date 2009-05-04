#!/usr/bin/python
from datetime import datetime
import getpass
# The md5 module is deprecated since Python 2.5
try:
    import hashlib
    md5hash = hashlib.md5
except ImportError:
    import md5
    md5hash = md5.new
from optparse import OptionParser
import time
from urllib import urlencode
from urllib2 import urlopen


class GobbleException(Exception):

    pass


class GobbleServer(object):

    def __init__(self, server_name, username, password):
        if server_name[:7] != "http://":
            server_name = "http://%s" % (server_name,)
        self.name = server_name
        self.password = password
        self.post_data = []
        self.session_id = None
        self.submit_url = None
        self.username = username
        self._handshake()


    def _handshake(self):
        timestamp = int(time.time())
        token = (md5hash(md5hash(self.password).hexdigest()
                    + str(timestamp)).hexdigest())
        auth_url = "%s/?hs=true&p=1.2&u=%s&t=%d&a=%s&c=imp" % (self.name,
                                                               self.username,
                                                               timestamp, token)
        response = urlopen(auth_url).read()
        lines = response.split("\n")
        if lines[0] != "OK":
            raise GobbleException("Server returned: %s" % (response,))
        self.session_id = lines[1]
        self.submit_url = lines[3]

    def submit(self):
        if len(self.post_data) == 0:
            return
        self.post_data.append(('s', self.session_id))
        response = urlopen(self.submit_url,
                           urlencode(self.post_data)).read()
        if response != "OK\n":
            raise GobbleException("Server returned: %s" % (response,))
        self.post_data = []
        time.sleep(1)

    def add_track(self, artist, track, dt):
        timestamp = str(int(time.mktime(dt.utctimetuple())))
        i = len(self.post_data) / 3
        if i > 49:
            self.submit()
            i = 0
        self.post_data += [('a[%d]' % i, artist), ('t[%d]' % i, track),
                           ('i[%d]' % i, timestamp)]


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
