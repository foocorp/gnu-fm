#!/usr/bin/env python

# Jamendo database dumps can be fetched from: http://img.jamendo.com/data/dbdump_artistalbumtrack.xml.gz

import xml.etree.cElementTree as ElementTree
import sys, gzip, time, os.path, urllib, threading

MAX_THREADS = 10
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
		urllib.urlretrieve(self.url, self.filename)
		running_threads -= 1


class DownloadJamendo:


	def __init__(self, destination):
		if not os.path.exists(destination):
			os.mkdir(destination)
		self.destination = destination


	def parse(self, dump):
		for event, elem in ElementTree.iterparse(dump):
			if elem.tag == "artist":
				self.proc_artist(elem)


	def proc_artist(self, elem):
		for artist_e in elem.getchildren():
			if artist_e.tag == "Albums":
				for album_e in artist_e.getchildren():
					self.proc_album(album_e)


	def proc_album(self, elem):
		for album_e in elem.getchildren():
			if album_e.tag == "Tracks":
				for track_e in album_e.getchildren():
					self.proc_track(track_e)


	def proc_track(self, elem):
		global running_threads
		track_id = None
		track_license = None

		for track_e in elem.getchildren():
			if track_e.tag == "id":
				track_id = int(track_e.text)

			if track_e.tag == "license":
				track_license = track_e.text
		

		if track_id and track_license:
			if self.free_license(track_license):
				trackurl = "http://api.jamendo.com/get2/stream/track/redirect/?id=%d&streamencoding=ogg2" % track_id
				trackfile = os.path.join(self.destination, "%d.ogg" % track_id)
				if os.path.exists(trackfile):
					print "Already downloaded track %d" % track_id
				else:
					while running_threads > MAX_THREADS:
						time.sleep(5)
					print "Downloading %s to %s" % (trackurl, trackfile)
					d = Downloader(trackfile, trackurl)
					d.start()




	def free_license(self, license):
		return ("http://creativecommons.org/licenses/by-sa" in license or "http://creativecommons.org/licenses/by/" in license or "http://artlibre.org/licence.php/lal.html" in license)



if __name__ == "__main__":

	if len(sys.argv) != 3:
		print "Usage: download-jamendo.py <database dump> <destination>"
		sys.exit(1)

	if sys.argv[1][-2:] == "gz":
		dump = gzip.open(sys.argv[1], "r")
	else:
		dump = open(sys.argv[1], "r")

	downloader = DownloadJamendo(sys.argv[2])
	downloader.parse(dump)
