#!/usr/bin/env python
import psycopg2 as ordbms
import urllib, urllib2
import xml.etree.cElementTree as ElementTree


class SetArtistStreamable:


	def __init__(self):
		self.conn = ordbms.connect ("dbname='librefm'")
                self.cursor = self.conn.cursor()


	def updateAll(self):
		"""Sets artists streamable property if they have streamable tracks already in the database"""
		self.cursor.execute("SELECT DISTINCT(artist.name) FROM artist INNER JOIN track on artist.name=artist_name WHERE track.streamable = 1")
		for artist in self.cursor.fetchall():
			name = artist[0]
			print "marking %s as streamable... " % name
			self.cursor.execute("UPDATE artist SET streamable = 1 WHERE name = %s", (name,))
		print "Applying changes... ",
		self.conn.commit()
		print "done."

if __name__ == '__main__':
	sas = SetArtistStreamable()
	sas.updateAll()
