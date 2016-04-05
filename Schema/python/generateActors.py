#!/usr/bin/python

import psycopg2
import omdb

def getMovie(name):
	movie = omdb.title(name)
	return movie;

def createActors(movieID):
	actors = []
	try:
		actors = movie.actors.split(',')
	except:
		actors = []
	if len(actors) > 0:
		for actor in actors:
			name = actor.strip()
			name = name.replace('\'','\'\'')
			print name
			cur.execute("INSERT INTO actor (name) \
         	 VALUES ('%s')" % (name))


# connect to the project database
conn = psycopg2.connect(database="CSI2132", user="csi2132", password="csi2132", host="159.203.44.157", port="5432")
print "Opened database successfully"

#fill the database for a movie
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
	createActors(movieID)

conn.commit()
conn.close()