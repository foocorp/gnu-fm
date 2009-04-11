# Libre.fm -- a free network service for sharing your music listening habits
#
# Copyright (C) 2009 Libre.fm Project
#
# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Affero General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Affero General Public License for more details.
#
# You should have received a copy of the GNU Affero General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.

from django.db.models import Count
from django.shortcuts import get_object_or_404, render_to_response
from django.template import RequestContext

from gobbler.models import Album, Artist, Gobble, GobblerUser


def album(request, artist_slug, album_slug):
    artist = get_object_or_404(Artist, slug=artist_slug)
    album = get_object_or_404(Album, artist=artist, slug=album_slug)
    tracks = album.track_set.order_by('track_number')
    return render_to_response("album.html", {'album': album, 'tracks': tracks},
                              context_instance=RequestContext(request))


def artist(request, slug):
    artist = get_object_or_404(Artist, slug=slug)
    albums = artist.album_set.all()
    return render_to_response("artist.html", {'albums': albums,
                                              'artist': artist},
                              context_instance=RequestContext(request))


def explore_artists(request):
    artists = Artist.objects.annotate(Count('album__track__gobble'))
    artists = [{'artist': artist, 'count': artist.album__track__gobble__count}
                for artist in artists.order_by('-album__track__gobble__count')]
    return render_to_response("explore_artists.html", {'artists': artists[:10]},
                              context_instance=RequestContext(request))


def frontpage(request):
    recent_gobbles = Gobble.objects.order_by('-id')[:10]
    return render_to_response("frontpage.html",
                              {'recently_gobbled': recent_gobbles},
                              context_instance=RequestContext(request))


def profile(request, username):
    user = get_object_or_404(GobblerUser, username=username)
    gobbles = user.gobble_set.order_by('-id')[:20]
    return render_to_response("profile.html", {'gobbles': gobbles, 'user': user})
