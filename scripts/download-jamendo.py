#!/usr/bin/env python

# Jamendo database dumps can be fetched from: http://img.jamendo.com/data/dbdump_artistalbumtrack.xml.gz

import xml.etree.cElementTree as ElementTree
import sys, gzip, time, os, os.path, urllib, threading, statvfs, magic

JAMENDO_DUMP_URL="http://img.jamendo.com/data/dbdump_artistalbumtrack.xml.gz"

MAX_THREADS = 10
MAX_RETRIES = 5
running_threads = 0

class Downloader(threading.Thread):

	def __init__(self, filename, url):
		global running_threads
		threading.Thread.__init__(self)
		self.filename = filename
		self.url = url
		running_threads += 1

	def run(self):
		global running_threads
		global MAX_RETRIES
		correct_mime = "application/ogg; charset=binary"
		m = magic.open(magic.MAGIC_MIME)
		m.load()
		retries = 0
		current_mime = ""
		while retries < MAX_RETRIES and current_mime != correct_mime:
			urllib.urlretrieve(self.url, self.filename)
			current_mime = m.file(self.filename)
			retries += 1
		if current_mime != correct_mime:
			os.rename(self.filename, '%s.ign' % self.filename[:-4])
		running_threads -= 1


class DownloadJamendo:

	def __init__(self, destination, dump):
		if not os.path.exists(destination):
			os.mkdir(destination)
		self.destination = destination
		self.MAX_FILENAME_LENGTH = os.statvfs(destination)[statvfs.F_NAMEMAX]
		self.dump = dump or None
		if not self.dump:
			print "Downloading Jamendo dump from %s" % JAMENDO_DUMP_URL
			(filename, headers) = urllib.urlretrieve(JAMENDO_DUMP_URL, os.path.join(destination, "dbdump_artistalbumtrack.xml.gz"))
			print "Jamendo dump saved: %s" % filename
			self.dump = gzip.open(filename, "r")


	def parse(self):
		for event, elem in ElementTree.iterparse(self.dump):
			if elem.tag == "artist":
				artist = self.proc_artist(elem)
				self.download_artist(artist)


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


	def download_artist(self, artist):
		global running_threads

		for album in artist["albums"]:
			for track in album["tracks"]:
				if track["id"] and track["name"] and album["name"] and artist["name"] and self.free_license(track["license"]):
					trackurl = "http://api.jamendo.com/get2/stream/track/redirect/?id=%d&streamencoding=ogg2" % track["id"]
					trackfile = "%s-%s-%s-%s" % (track["id"], artist["name"].replace("/", ""), album["name"].replace("/", ""), track["name"].replace("/", " "))
					trackfile = "%s.ogg" % trackfile.encode('utf-8')[:self.MAX_FILENAME_LENGTH-4].decode('utf-8','ignore').encode('utf-8')
					ignorefile = "%s.ign" % trackfile[:-4]
					trackfilepath = os.path.join(self.destination, trackfile)
					ignorefilepath = os.path.join(self.destination, ignorefile)

					if os.path.exists(ignorefilepath):
						print "Found ignore file for %s" % trackfile
						continue

					if os.path.exists(trackfilepath):
						if os.path.getsize(trackfilepath) < 1024:
							print "Removing file with size below 1024 bytes: %s" % trackfilepath
							os.remove(trackfilepath)
						else:
							print "Already downloaded track %s" % trackfilepath
							continue
						
					while running_threads > MAX_THREADS:
						time.sleep(1)
					print "Downloading %s to %s" % (trackurl, trackfilepath)
					d = Downloader(trackfilepath, trackurl)
					d.start()
					tracksymlink = os.path.join(self.destination, "%s.ogg2" % track["id"])
					if os.path.lexists(tracksymlink):
						os.remove(tracksymlink)
					os.symlink(trackfile, tracksymlink)
					# 1 second delay between every new request to be nice to server
					time.sleep(1)



	def free_license(self, license):
		return ("http://creativecommons.org/licenses/by-sa" in license or "http://creativecommons.org/licenses/by/" in license or "http://artlibre.org/licence.php/lal.html" in license)



if __name__ == "__main__":

	if len(sys.argv) < 2:
		print "Usage: download-jamendo.py [<database dump>] <destination>"
		sys.exit(1)

	if len(sys.argv) == 3:
		destination = sys.argv[2]
		if sys.argv[1][-2:] == "gz":
			dump = gzip.open(sys.argv[1], "r")
		else:
			dump = open(sys.argv[1], "r")
	else:
		destination = sys.argv[1]
		dump = None

	downloader = DownloadJamendo(destination, dump)
	downloader.parse()
