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
