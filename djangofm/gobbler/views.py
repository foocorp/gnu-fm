from datetime import datetime
import md5
import random

from django.http import HttpResponse

from gobbler.models import Album, Artist, Gobble, GobblerUser, Md5Password, NowPlaying, Session, Track
from web.views import frontpage


def handshake_11(request):
    g = request.GET
    try:
        client_id,client_version,user = g['c'], g['v'], g['u']
    except KeyError:
        return HttpResponse("FAILED The request was malformed\nINTERVAL 0\n")
    challenge = md5.new(str(random.random())).hexdigest()
    try:
        guser = GobblerUser.objects.get(username=user)
    except GobblerUser.DoesNotExist:
        return HttpResponse("BADUSER\nINTERVAL 0\n")
    session,created = Session.objects.get_or_create(user=guser)
    session.key = challenge
    session.save()
    return HttpResponse("\n".join(["UPTODATE",
                                   challenge,
                                   "http://127.0.0.1:80/protocol_1.1/",
                                   "INTERVAL 0"]))


def handshake_12(request):
    g = request.GET
    try:
        client_id,client_version,user,timestamp,auth = (
            g['c'], g['v'], g['u'], g['t'], g['a'])
    except KeyError:
        return HttpResponse("FAILED The request was malformed\n")
    try:
        guser = GobblerUser.objects.get(username=user)
    except GobblerUser.DoesNotExist:
        return HttpResponse("BADAUTH\n")
    expected_token = md5.new(guser.get_md5() + timestamp)
    if auth != expected_token.hexdigest():
        return HttpResponse("BADAUTH\n")
    session_id = expected_token.copy()
    session_id.update(str(datetime.now()))
    session,created = Session.objects.get_or_create(user=guser)
    session.key = session_id.hexdigest()
    session.save()
    return HttpResponse("\n".join(["OK",
                                   session_id.hexdigest(),
                                   "http://127.0.0.1:80/nowplaying/",
                                   "http://127.0.0.1:80/protocol_1.2/"]))


def index(request):
    g = request.GET
    handshake = g.get('hs', None)
    if handshake:
        try:
            protocol_version = g['p']
        except KeyError:
            return HttpResponse("FAILED The request was malformed\n")
        if protocol_version == "1.2" or protocol_version == "1.2.1":
            return handshake_12(request)
        elif protocol_version == "1.1":
            return handshake_11(request)
        else:
            return HttpResponse("FAILED Unsupported protocol version\n")
    else:
        return frontpage(request)


def now_playing(request):
    p = request.POST
    session_id,artist_name,track_name,album_name,length,tracknumber,mbid = (
        p['s'], p['a'], p['t'], p['b'], p['l'], p['n'], p['m'])
    try:
        session = Session.objects.get(key=session_id)
    except Session.DoesNotExist:
        return HttpResponse("BADSESSION\n")
    artist,c = Artist.objects.get_or_create(name=artist_name)
    album,c = Album.objects.get_or_create(name=album_name,
                                          artist=artist)
    track,c = Track.objects.get_or_create(name=track_name,
                                          album=album,
                                          track_number=tracknumber,
                                          length=length,
                                          mbid=mbid)
    try:
        np = NowPlaying.objects.get(user=session.user)
    except NowPlaying.DoesNotExist:
        np = NowPlaying()
        np.user = session.user
    np.track = track
    np.save()
    return HttpResponse("OK\n")


def _count_submissions(request):
    i = 0
    while True:
        try:
            p['a[%d]' % i+1]
            i += 1
        except:
            break
    return i


def _get_info(p, j):
    artist_name = p['a[%d]' % j]
    track_name = p['t[%d]' % j]
    album_name = p['b[%d]' % j]
    time = p['i[%d]' % j]
    mbid = p['m[%d]' % j]
    length = p['l[%d]' % j]
    return artist_name, track_name, album_name, time, mbid, length


def protocol_11(request):
    try:
        p = request.POST
        try:
            guser = GobblerUser.objects.get(username=p['u'])
        except GobblerUser.DoesNotExist:
            return HttpResponse("BADAUTH\nINTERVAL 0\n")
        try:
            session = Session.objects.get(user=guser)
        except Session.DoesNotExist:
            return HttpResponse("BADAUTH\nINTERVAL 0\n")
        expected_token = md5.new(guser.get_md5() + session.key)
        if p['s'] != expected_token.hexdigest():
            return HttpResponse("BADAUTH\nINTERVAL 0\n")
        i = _count_submissions(request)
        for j in range(i+1):
            artist_name,track_name,album_name,time,mbid,length = _get_info(p, j)
            artist,c = Artist.objects.get_or_create(name=artist_name)
            album,c = Album.objects.get_or_create(name=album_name,
                                                  artist=artist)
            track,c = Track.objects.get_or_create(name=track_name,
                                                  length=length,
                                                  album=album,
                                                  mbid=mbid)
            dt = datetime.strptime(time.replace("\0", ""), "%Y-%m-%d %H:%M:%S")
            Gobble.objects.create(user=guser,
                                  track=track,
                                  time=dt,
                                  source="U",
                                  length=length)
        return HttpResponse("OK\nINTERVAL 0\n")
    except:
        import traceback
        traceback.print_exc()
        raise


def protocol_12(request):
    p = request.POST
    try:
        session = Session.objects.get(key=p['s'])
    except Session.DoesNotExist:
        return HttpResponse("BADSESSION\n")
    i = _count_submissions(request)
    for j in range(i+1):
        artist_name,track_name,album_name,time,mbid,length = _get_info(p, j)
        source = p['o[%d]' % j]
        rating = p['r[%d]' % j]
        tracknumber = p['n[%d]' % j]
        artist,c = Artist.objects.get_or_create(name=artist_name)
        album,c = Album.objects.get_or_create(name=album_name,
                                              artist=artist)
        track,c = Track.objects.get_or_create(name=track_name,
                                              album=album,
                                              track_number=tracknumber,
                                              length=length,
                                              mbid=mbid)
        dt = datetime.fromtimestamp(float(time))
        gobble = Gobble.objects.create(user=session.user,
                                       track=track,
                                       time=dt,
                                       source=source,
                                       rating=rating,
                                       length=length)
    return HttpResponse('OK\n')
