from datetime import datetime
from optparse import OptionParser
from urllib2 import urlopen

from BeautifulSoup import BeautifulSoup


if __name__ == '__main__':
    usage = "%prog <USERNAME> <WEB SERVER>"
    parser = OptionParser(usage=usage)
    opts,args = parser.parse_args()
    if len(args) != 2:
        parser.error("All arguments are required.")

    username,server = args
    if server[:7] != "http://":
        server = "http://%s" % (server,)
    soup = BeautifulSoup(urlopen('%s/user/%s/recent-tracks'
                                    % (server, username)))
    gobbles_list = soup.find('ul', {'class': 'gobbles'})
    timestamp = gobbles_list.find('li')['about'].split('#')[1].split('.')[0]
    print datetime.fromtimestamp(float(timestamp))
