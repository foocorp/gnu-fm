#!/usr/bin/env python

##### CONFIG #####

SERVER = "turtle.libre.fm"
USER = "testuser"
PASSWORD = "password"


##################

import gobble, datetime

print "Handshaking..."
gs = gobble.GobbleServer(SERVER, USER, PASSWORD, 'tst')
time = datetime.datetime.now() - datetime.timedelta(days=1) # Yesterday
track = gobble.GobbleTrack("Richard Stallman", "Free Software Song", time)
gs.add_track(track)
print "Submitting..."
gs.submit()
print "Done!"
