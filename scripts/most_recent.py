#!/usr/bin/python

from datetime import datetime
from optparse import OptionParser
from urllib2 import urlopen

from BeautifulSoup import BeautifulSoup
from gobble import get_parser


if __name__ == '__main__':
    usage = "%prog [-s <WEB SERVER>] <USERNAME>"
    parser = get_parser(usage=usage)
    parser.set_defaults(server='alpha.libre.fm')
    opts,args = parser.parse_args()
    if len(args) != 1:
        parser.error("All arguments are required.")

    username, = args
    server = opts.server
    if server[:7] != "http://":
        server = "http://%s" % (server,)
    soup = BeautifulSoup(urlopen('%s/user/%s/recent-tracks'
                                    % (server, username)))
    gobbles_list = soup.find('ul', {'class': 'gobbles'})
    timestamp = gobbles_list.find('li')['about'].split('#')[1].split('.')[0]
    print datetime.fromtimestamp(float(timestamp))
