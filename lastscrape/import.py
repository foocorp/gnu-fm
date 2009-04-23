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


usage = "%prog <USERNAME> <SERVER> <SCROBBLE DUMP>"
parser = OptionParser(usage=usage)
opts,args = parser.parse_args()
if len(args) != 3:
    parser.error("All arguments are required.")

username,server,data = args
if server[:7] != "http://":
    server = "http://%s" % (server,)
password = getpass.getpass()

timestamp = int(time.time())
token = md5hash(md5hash(password).hexdigest() + str(timestamp)).hexdigest()
auth_url = "%s/?hs=true&p=1.2&u=%s&t=%d&a=%s&c=imp" % (server, username,
                                                       timestamp, token)
response = urlopen(auth_url).read()
lines = response.split("\n")
if lines[0] != "OK":
    parser.error("Server returned: %s" % (response,))
session_id = lines[1]
submit_url = lines[3]

def post(post_data):
    post_data.append(('s', session_id))
    response = urlopen(submit_url, urlencode(post_data)).read()
    if response != "OK\n":
        parser.error("Server returned: %s" % (response,))

i = 0
post_data = []
for line in file(data):
    artist,track,timestamp = line.strip().split("\t")
    dt = datetime.strptime(timestamp, "%Y-%m-%dT%H:%M:%SZ")
    timestamp = str(int(time.mktime(dt.timetuple())))
    post_data += [('a[%d]' % i, artist), ('t[%d]' % i, track),
                  ('i[%d]' % i, timestamp)]
    print "Adding to post %s playing %s" % (artist, track)
    i += 1
    if i > 49:
        print "Posting..."
        post(post_data)
        i = 0
        post_data = []
        time.sleep(1)

if len(post_data) > 0:
    post(post_data)
