#!/usr/bin/env python

# Jamendo database dumps can be fetched from: http://img.jamendo.com/data/dbdump_artistalbumtrack.xml.gz

import xml.etree.cElementTree as ElementTree
import sys, gzip, time, os, os.path, urllib, threading

class JamendoRewrite:

	def __init__(self, path):
		self.music_path = path
		print "RewriteEngine on"

	def parse(self, dump):
		for event, elem in ElementTree.iterparse(dump):
			if elem.tag == "artist":
				artist = self.proc_artist(elem)
				self.make_rules(artist)

	def proc_artist(self, elem):
		artist = {}
		artist["albums"] = []

		for artist_e in elem.getchildren():

			if artist_e.tag == "name":
				artist["name"] = artist_e.text

			if artist_e.tag == "Albums":
				for album_e in artist_e.getchildren():
					artist["albums"].append(self.proc_album(album_e))

		return artist

	def proc_album(self, elem):

		album = {}
		album["tracks"] = []
		album["name"] = None

		for album_e in elem.getchildren():

			if album_e.tag == "name":
				album["name"] = album_e.text

			if album_e.tag == "Tracks":
				for track_e in album_e.getchildren():
					album["tracks"].append(self.proc_track(track_e))

		return album


	def proc_track(self, elem):
		track = {}
		track["id"] = None
		track["name"] = None
		track["license"] = None

		for track_e in elem.getchildren():
		
			if track_e.tag == "id":
				track["id"] = int(track_e.text)

			if track_e.tag == "name":
				track["name"] = track_e.text

			if track_e.tag == "license":
				track["license"] = track_e.text

		return track


	def make_rules(self, artist):
		for album in artist["albums"]:
			for track in album["tracks"]:
				if track["id"] and track["name"] and album["name"] and artist["name"] and self.free_license(track["license"]):
					filename = "%s-%s-%s" % (artist["name"].replace("/", ""), album["name"].replace("/", ""), track["name"].replace("/", " "))
					filename = filename.encode("utf-8")
					rule = "RewriteRule ^%d\.(...)		%s/%s.$1" % (track['id'], self.music_path, urllib.quote(filename))
					rule = rule.replace("%20", "\ ")
					print rule


	def free_license(self, license):
		return ("http://creativecommons.org/licenses/by-sa" in license or "http://creativecommons.org/licenses/by/" in license or "http://artlibre.org/licence.php/lal.html" in license)



if __name__ == "__main__":

	if len(sys.argv) != 3:
		print "Usage: download-jamendo.py <database dump> /path/to/music_files/"
		sys.exit(1)

	if sys.argv[1][-2:] == "gz":
		dump = gzip.open(sys.argv[1], "r")
	else:
		dump = open(sys.argv[1], "r")

	rewriter = JamendoRewrite(sys.argv[2])
	rewriter.parse(dump)
