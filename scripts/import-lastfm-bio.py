#!/usr/bin/env python
import psycopg2 as ordbms
import urllib, urllib2
import xml.etree.cElementTree as ElementTree


class ImportLastfmBio:


	def __init__(self):
		self.conn = ordbms.connect ("dbname='librefm'")
                self.cursor = self.conn.cursor()


	def importAll(self):
		"""Imports descriptions for all artists who don't currently have one"""
		self.cursor.execute("SELECT * FROM artist WHERE bio_summary IS NULL AND bio_content IS NULL")
		for artist in self.cursor.fetchall():
			name = artist[0]
			url = "http://ws.audioscrobbler.com/2.0/artist/%s/info.xml" % urllib.quote(name)
			print "\nFetching %s..." % name
			try:
				xml = urllib2.urlopen(url)
				self.parse(xml, name, "http://www.last.fm/music/%s" % urllib.quote(name))
			except urllib2.HTTPError:
				print "Failed."

	def parse(self, xml, name, source):
		for event, elem in ElementTree.iterparse(xml):
			if elem.tag == "bio":
				for bio_e in elem.getchildren():
					if bio_e.tag == "summary":
						summary = bio_e.text
					elif bio_e.tag == "content":
						content = bio_e.text

				if summary:
					summary.strip()
					summary = self.fixUrls(summary)
				if content:
					content.strip()
					content = self.fixUrls(content)

				if summary != None or content != None:
					self.cursor.execute("UPDATE artist SET bio_summary = %s, bio_content = %s, bio_source = %s WHERE name = %s", (summary, content, source, name))
					self.conn.commit()
					print "Imported!"
				else:
					print "No Bio"


	def fixUrls(self, text):
		text.replace("http://www.last.fm/tag/", "/tag/")
		text.replace("http://last.fm/tag/", "/tag/")
		text.replace("http://www.last.fm/music/", "/artist/")
		text.replace("http://last.fm/music/", "/artist/")
		return text

if __name__ == '__main__':
	importer = ImportLastfmBio()
	importer.importAll()
