#!/usr/bin/env python
#-*- coding: utf-8 -*-
"""usage: lastscrape.py USER [OUTPUT_FILE]"""
import sys
import time
import codecs
import urllib2
from BeautifulSoup import BeautifulSoup

sys.stdout = codecs.lookup('utf-8')[-1](sys.stdout)

def parse_page(page):
	"""Parse a page of recently listened tracks and return a list."""
	soup = BeautifulSoup(urllib2.urlopen(page))
	for row in soup.find('table', 'candyStriped tracklist').findAll('tr'):
		artist, track, timestamp = parse_track(row)
		# Tracks submitted before 2005 have no timestamp
		if artist and track:
			yield (artist, track, timestamp)

def parse_track(row):
	"""Return a tuple containing track data."""
	try:
		track_info = row.find('td', 'subjectCell')
		artist, track = track_info.findAll('a')
		timestamp = row.find('abbr')
		artist = artist.contents[0].strip()
		track = track.contents[0].strip()
		timestamp = str(timestamp).split('"')[1].strip()
		return (artist, track, timestamp)
	except:
		# Parsing failed
		print 'parsing failed'
		return (None, None, None)

def fetch_tracks(user, request_delay=0.5):
	"""Fetch all tracks from a profile page and return a list."""
	url = 'http://last.fm/user/%s/tracks' % user
	soup = BeautifulSoup(urllib2.urlopen(url))
	try:
		num_pages = int(soup.find('a', 'lastpage').contents[0])
	except:
		num_pages = 1
	for cur_page in range(1, num_pages + 1):
		tracks = parse_page(url + '?page=' + str(cur_page))
		for artist, track, timestamp in tracks:
			yield (artist, track, timestamp)
		if cur_page < num_pages:
			time.sleep(request_delay)

def main(*args):
	if len(args) == 2:
		# Print to stdout
		print u'Artist\tTrack\tTimestamp'
		for artist, track, timestamp in fetch_tracks(args[1]):
			print u'%s\t%s\t%s' % (artist, track, timestamp)
	elif len(args) == 3:
		# Write to file
		f = codecs.open(args[2], 'w', 'utf-8')
		f.write(u'Artist\tTrack\tTimestamp\n')
		for artist, track, timestamp in fetch_tracks(args[1]):
			f.write(u'%s\t%s\t%s\n' % (artist, track, timestamp))
		f.close()
	else:
		print __doc__

if __name__ == '__main__':
	sys.exit(main(*sys.argv))
