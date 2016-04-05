#!/usr/bin/python

import psycopg2
import omdb

def getMovie(movieName):
	movie = omdb.title(movieName)
	return movie;

def getDirectorID(name):
	directorID = 0
	cur.execute("SELECT directorID from director WHERE lastName = '%s' " % name)
	rows2 = cur.fetchall()
	for row in rows2:
		directorID = row[0]
	return directorID

def createDirects(movieID):
	directors = []
	try:
		directors = movie.director.split(',')
	except:
		directors = []
	if len(directors) > 0:
		for director in directors:
			name = director.strip()
			name = name.replace('\'','\'\'')
			directorID = getDirectorID(name)
			if directorID > 0:
				print str(directorID) + '-' + str(movieID)
				cur.execute("INSERT INTO directs (movieID,directorID) \
         	 	VALUES ('%s','%s')" % (movieID,directorID))


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
	createDirects(movieID)

conn.commit()
conn.close()