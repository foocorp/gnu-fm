#!/usr/bin/python

import datetime
import getpass
from optparse import OptionParser
import subprocess

import mutagen
from mutagen import easyid3

from gobble import GobbleServer


def _parse_date(string):
    process = subprocess.Popen(['date', '-d %s' % (string,), '+%s'],
                               stdout=subprocess.PIPE)
    string = process.communicate()[0].strip()
    return datetime.datetime.utcfromtimestamp(float(string))


if __name__ == '__main__':
    usage = "%prog <USERNAME> <SERVER> <START TIME> <MEDIA FILES>"
    parser = OptionParser(usage=usage)
    opts,args = parser.parse_args()
    if len(args) < 4:
        parser.error("All arguments are required.")

    username,server,start_string = args[:3]
    password = getpass.getpass()
    tracks = args[3:]
    server = GobbleServer(server, username, password)

    dt = _parse_date(start_string)

    for track in tracks:
        f = mutagen.File(track)
        if f is None:
            raise Exception("%s caused problems." % (track,))
        if isinstance(f, mutagen.mp3.MP3):
            f = mutagen.mp3.MP3(track, ID3=easyid3.EasyID3)
        title = f['title'][0]
        artist = f['artist'][0]
        length = f.info.length
        server.add_track(artist, title, dt)
        dt += datetime.timedelta(seconds=length)
    server.submit()
