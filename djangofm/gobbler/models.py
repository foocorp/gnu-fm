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

import md5

from django.contrib import admin
from django.contrib.auth.models import get_hexdigest, User
from django.contrib.auth.admin import UserAdmin
from django.template.defaultfilters import slugify
from django.db import models


RATING_CHOICES = (
    ('L', 'Love'),
    ('B', 'Ban'),
    ('S', 'Skip')
)


SOURCE_CHOICES = (
    ('P', 'Chosen by the user'),
    ('R', 'Non-personalised broadcast'),
    ('E', 'Personalised recommendation except Last.fm'),
    ('L', 'Last.fm'),
    ('U', 'Unknown')
)


class GobblerUser(User):

    def set_password(self, raw_password):
        import random
        algo = 'sha1'
        salt = get_hexdigest(algo, str(random.random()), str(random.random()))[:5]
        hsh = get_hexdigest(algo, salt, raw_password)
        self.password = '%s$%s$%s' % (algo, salt, hsh)
        pwd,created = Md5Password.objects.get_or_create(user=self)
        pwd.password = md5.new(raw_password).hexdigest()
        pwd.save()

    def get_md5(self):
        pwd = Md5Password.objects.get(user=self)
        return pwd.password

    class Meta:
        proxy = True

admin.site.register(GobblerUser, UserAdmin)


class Artist(models.Model):
    name = models.CharField(max_length=255, unique=True)
    slug = models.SlugField(unique=True, editable=False)

    def get_absolute_url(self):
        return "/music/%s/" % (self.slug,)

    def save(self, force_insert=False, force_update=False):
        self.slug = slugify(self.name)
        super(Artist, self).save(force_insert, force_update)

    def __unicode__(self):
        return self.name


class Album(models.Model):
    name = models.CharField(max_length=256)
    artist = models.ForeignKey(Artist)
    slug = models.SlugField(editable=False)

    def get_absolute_url(self):
        return "%s%s/" % (self.artist.get_absolute_url(), self.slug)

    def save(self, force_insert=False, force_update=False):
        self.slug = slugify(self.name)
        super(Album, self).save(force_insert, force_update)

    def __unicode__(self):
        return "%s by %s" % (self.name, self.artist)

    class Meta:
        unique_together = (('artist', 'name'), ('artist', 'slug'))


class Track(models.Model):
    name = models.CharField(max_length=256)
    track_number = models.PositiveSmallIntegerField(blank=True, null=True)
    length = models.PositiveSmallIntegerField(blank=True)
    album = models.ForeignKey(Album, null=True)
    mbid = models.CharField(max_length=256, blank=True)
    slug = models.SlugField(editable=False)

    def get_absolute_url(self):
        return "%s%s/" % (self.album.get_absolute_url(), self.name)

    def save(self, force_insert=False, force_update=False):
        self.slug = slugify(self.name)
        super(Track, self).save(force_insert, force_update)

    def __unicode__(self):
        return "%s - %s" % (self.album.artist, self.name)

    class Meta:
        unique_together = ('album', 'slug')


class Session(models.Model):
    user = models.OneToOneField(GobblerUser, primary_key=True)
    key = models.CharField(max_length=32)


class Md5Password(models.Model):
    user = models.OneToOneField(GobblerUser, primary_key=True)
    password = models.CharField(max_length=32)


class NowPlaying(models.Model):
    user = models.OneToOneField(GobblerUser, primary_key=True)
    track = models.ForeignKey(Track)

    @property
    def artist(self):
        return self.track.album.artist

    def __unicode__(self):
        return "%s is playing %s" % (self.user, self.track)


class Gobble(models.Model):
    user = models.ForeignKey(GobblerUser)
    track = models.ForeignKey(Track)
    time = models.DateTimeField()
    source = models.CharField(choices=SOURCE_CHOICES, max_length=1)
    rating = models.CharField(choices=RATING_CHOICES, max_length=1,
                              blank=True)
    length = models.PositiveSmallIntegerField()

    @property
    def artist(self):
        return self.track.album.artist

    def __unicode__(self):
        return "%s at %s" % (self.user, self.time)

admin.site.register(Gobble)
