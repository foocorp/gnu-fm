#!/usr/bin/env python
import random

s = open("scrobbledump.sql", "r")
o = open("scrobbles.anonymous.sql", "w")

datasection = False
usermap = {}
#track, artist, "time", mbid, album, source, rating, length, stid, userid, track_tsv, artist_tsv
for line in s.readlines():
	if line.rstrip() == "\.":
		datasection = False
	if datasection:
		data = line.split("\t")
		uid = data[9]
		if uid in usermap:
			data[9] = str(usermap[uid])
		else:
			newid = random.randint(0, 1000000)
			while newid in usermap:
				newid = random.randint(0, 1000000)
			usermap[uid] = newid
			data[9] = str(newid)
		o.write("\t".join(data))
	else:
		o.write(line)
	if line[:4] == "COPY":
		datasection = True
s.close()
o.close()

