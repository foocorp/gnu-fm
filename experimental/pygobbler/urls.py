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

from django.conf.urls.defaults import *
from django.contrib import admin

from gobbler.views import index, now_playing, protocol_11, protocol_12
from web.views import album, artist, explore_artists, profile

admin.autodiscover()

urlpatterns = patterns('',
    (r'^admin/(.*)', admin.site.root),
    (r'^$', index),
    (r'^protocol_1.1/$', protocol_11),
    (r'^protocol_1.2/$', protocol_12),
    (r'^nowplaying/$', now_playing),
    (r'^explore/artists/$', explore_artists),
    (r'^music/(.*)/(.*)/$', album),
    (r'^music/(.*)/$', artist),
    (r'^login/$', 'django.contrib.auth.views.login'),
    (r'^profile/(.*)/$', profile),
)
