#!/usr/bin/python

import datetime
import getpass
from optparse import OptionParser
import subprocess
import sys

import mutagen
from mutagen import easyid3

from gobble import get_parser, GobbleServer, GobbleTrack


def _parse_date(string):
    process = subprocess.Popen(['date', '-d %s' % (string,), '+%s'],
                               stdout=subprocess.PIPE)
    string = process.communicate()[0].strip()
    return datetime.datetime.utcfromtimestamp(float(string))


def _get_date(start_string=None, tracks=None):
    if start_string is not None:
        dt = _parse_date(start_string)
        input = ''
        while input not in ['y', 'n']:
            input = raw_input("Did you mean '%s UTC'? [Y/n]: " % (dt,)).lower()
        if input == 'n':
            sys.exit()
    else:
        offset = datetime.timedelta()
        for track in tracks:
            offset += datetime.timedelta(seconds=_get_track(track).info.length)
        dt = datetime.datetime.now() - offset
    return dt


def _get_track(filename):
    f = mutagen.File(filename)
    if f is None:
        raise Exception("%s caused problems." % (track,))
    if isinstance(f, mutagen.mp3.MP3):
        f = mutagen.mp3.MP3(filename, ID3=easyid3.EasyID3)
    return f


if __name__ == '__main__':
    usage = "%prog [--server <SERVER>] <USERNAME> <START TIME> <MEDIA FILES>"
    parser = get_parser(usage=usage)
    parser.add_option('-j', '--just-finished', action="store_true",
                      help="Works out START TIME as if you've just finished"
                           " listening to MEDIA FILES.  START TIME argument"
                           " will be treated as a media file, so don't pass"
                           " it.")
    parser.set_defaults(just_finished=False)
    opts,args = parser.parse_args()
    if opts.just_finished:
        expected_args = 2
    else:
        expected_args = 3
    if len(args) < expected_args:
        parser.error("All arguments are required.")

    username = args.pop(0)
    start_string = None
    if not opts.just_finished:
        start_string = args.pop(0)
    server = opts.server
    password = getpass.getpass()
    tracks = args
    server = GobbleServer(server, username, password)

    if opts.just_finished:
        dt = _get_date(tracks=tracks)
    else:
        dt = _get_date(start_string)

    for track in tracks:
        f = _get_track(track)
        title = f['title'][0]
        artist = f['artist'][0]
        length = f.info.length
        album = f['album'][0]
        tracknumber = f['tracknumber'][0]
        t = GobbleTrack(artist, title, dt, album=album, length=length,
                        tracknumber=tracknumber)
        server.add_track(t)
        dt += datetime.timedelta(seconds=length)
    server.submit()
