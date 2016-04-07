#!/usr/bin/python

import psycopg2
import omdb

actorList = []

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
			if name not in actorList:
				actorList.append(name)
				part1 = "INSERT INTO actor(name) VALUES (\'"
				part2 = name
				part3 = "\');"
				print part1+part2+part3
			return;
	


# connect to the project database
conn = psycopg2.connect(database="CSI2132", user="csi2132", password="csi2132", host="159.203.44.157", port="5432")
print "Opened database successfully"

#variables for the movie
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