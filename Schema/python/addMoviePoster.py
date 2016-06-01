#!/usr/bin/python

import psycopg2
import omdb

def getMovie(name):
	movie = omdb.title(name)
	return movie;

def addPoster(movieID):
		poster = 'n/a'
		try:
			poster = str(movie.poster)
		except:
			poster = 'n/a'
		print poster
		cur.execute("UPDATE movie SET posterUrl=%s WHERE movieID = (%s)", (poster,movieID));


# connect to the project database
conn = psycopg2.connect(database="CSI2132", user="csi2132", password="csi2132", host="127.0.0.1", port="5432")
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
	addPoster(movieID)

conn.commit()
conn.close()
