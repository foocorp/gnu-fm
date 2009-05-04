try:
    import hashlib
    md5hash = hashlib.md5
except ImportError:
    import md5
    md5hash = md5.new
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


