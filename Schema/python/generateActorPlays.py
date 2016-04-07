#!/usr/bin/python

import psycopg2
import omdb

def getMovie(movieName):
	movie = omdb.title(movieName)
	return movie;

def getActorID(name):
	actorID = ''
	cur.execute("SELECT actorID from actor WHERE name = '%s' " % name)
	rows2 = cur.fetchall()
	for row in rows2:
		actorID = row[0]
	return actorID

def createActorPlays(movieID):
	actors = []
	try:
		actors = movie.actors.split(',') #can return multiple actors seperated by a ','
	except:
		actors = []
	if len(actors) > 0:
		for actor in actors:
			name = actor.strip() # remove white space
			name = name.replace('\'','\'\'') # escape quotes for the db
			actorID = getActorID(name)
			part1 = "INSERT INTO actorPlays(movieID,actorID) VALUES (\'"
			part2 = str(movieID)
			part3 = "\',\'"
			part4 = str(actorID)
			part5 = "\');"
			if (len(part2) > 0) and (len(part4) > 0):
				print part1+part2+part3+part4+part5
			#print str(actorID) + '-' + str(movieID)
			#cur.execute("INSERT INTO actorPlays (movieID,actorID) \
         	# VALUES ('%s','%s')" % (movieID,actorID))


# connect to the project database
conn = psycopg2.connect(database="CSI2132", user="csi2132", password="csi2132", host="159.203.44.157", port="5432")
print "Opened database successfully"

# initialize some variables for the movie
movieName = ''
movieID = ''

# get the list of movies
cur = conn.cursor()
cur.execute("SELECT * FROM movie")
rows = cur.fetchall()
for row in rows:
	movieID = row[0]
	movieName = row[1]
	movie = getMovie(movieName)
	createActorPlays(movieID)

conn.commit()
conn.close()