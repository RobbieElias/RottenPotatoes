#!/usr/bin/python
import urllib
import psycopg2
import omdb

def getMoviePoster(name):
	movie = omdb.title(name)
	fname = str(movieName).replace(' ','_').lower()
	fname = fname + '.jpg'
	try:
		urllib.urlretrieve(str(movie.poster), fname)
		print 'retrieved: ' + fname
	except:
		print''
	return movie;


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
	movie = getMoviePoster(movieName)

conn.commit()
conn.close()

