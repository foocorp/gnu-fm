#!/usr/bin/env python

import sys, gzip, time
import MySQLdb as ordbms
import csv

class MBArtistIdImport:

	def __init__(self, hostname, username, password, database):
		self.conn = ordbms.connect (host = hostname,
			user = username,
			passwd = password,
			db = database)

		self.cursor = self.conn.cursor ()


	def parse(self, dump):
		for row in csv.reader(dump, dialect='excel-tab'):
			if self.artist_exists(row[1]):
				try:
					self.cursor.execute("UPDATE Artist SET imbid = %s, mbid = %s WHERE name = %s", (row[0], row[2], row[1]));
				except Exception, e:
					print e
			else:
				try:
					self.cursor.execute("INSERT INTO Artist (imbid, name, mbid) VALUES (%s, %s, %s)", (row[0], row[1], row[2]));
				except Exception,  e:
					print e

	def close(self):
		self.cursor.close()
		self.conn.commit()
		self.conn.close()


	def artist_exists(self, artist):
		try:
			self.cursor.execute("SELECT name FROM Artist WHERE name = %s ", (artist,))
			return self.cursor.rowcount != 0
		except:
			return False

if __name__ == "__main__":

	if len(sys.argv) != 6:
		print "Usage: import-mbartists.py <database dump> <mysql hostname> <mysql username> <mysql password> <mysql database>"
		sys.exit(1)

	if sys.argv[1][-2:] == "gz":
		dump = gzip.open(sys.argv[1], "r")
	else:
		dump = open(sys.argv[1], "r")

	importer = MBArtistIdImport(sys.argv[2], sys.argv[3], sys.argv[4], sys.argv[5])
	importer.parse(dump)
	importer.close()
