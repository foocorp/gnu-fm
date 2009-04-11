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
	soup = BeautifulSoup(urllib2.urlopen(page))
	page_data = []
	for row in soup.find('table', 'tracklist big').findAll('tr'):
		artist, track, timestamp = parse_track(row)
		# Tracks submitted before 2005 have no timestamp
		if artist and track:
			page_data.append((artist, track, timestamp))
	return page_data

def parse_track(row):
	try:
		artist, track = row.findAll('a', 'primary')
		timestamp = row.find('td', 'border dateCell last')
		artist = artist.contents[0].strip()
		track = track.contents[0].strip()
		timestamp = timestamp.contents[0].strip()
		return (artist, track, timestamp)
	except:
		# Parsing failed
		return (None, None, None)

def fetch_tracks(user, request_delay=0.5):
	url = 'http://last.fm/user/%s/library/recent' % user
	soup = BeautifulSoup(urllib2.urlopen(url))
	try:
		num_pages = int(soup.find('a', 'lastpage').contents[0])
	except:
		num_pages = 1
	
	all_data = []
	for cur_page in range(1, num_pages + 1):
		data = parse_page(url + '?page=' + str(cur_page))
		all_data += data
		if cur_page < num_pages:
			time.sleep(request_delay)
	return all_data

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
