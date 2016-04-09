#!/usr/bin/python

import psycopg2
import omdb

def getMovie(movieName):
	movie = omdb.title(movieName,tomatoes=True)
	return movie;

def getStudioID(name):
	studioID = 0
	cur.execute("SELECT studioID from studio WHERE name = '%s' " % name)
	rows2 = cur.fetchall()
	for row in rows2:
		studioID = row[0]
	return studioID

def createSponsors(movieID):
	studios = []
	try:
		studio = movie.production
	except:
		studio = 'none'
	name = studio.strip()
	name = name.replace('\'','\'\'')
	studioID = getStudioID(name)
	if studioID > 0:
		part1 = "INSERT INTO sponsors(movieID,studioID) VALUES (\'"
		part2 = str(movieID)
		part3 = "\',\'"
		part4 = str(studioID)
		part5 = "\');"
		if (len(part2) > 0) and (len(part4) > 0):
			print part1+part2+part3+part4+part5
		#print str(directorID) + '-' + str(movieID)
		#cur.execute("INSERT INTO directs (movieID,directorID) \
 	 	#VALUES ('%s','%s')" % (movieID,directorID))


# connect to the project database
conn = psycopg2.connect(database="CSI2132", user="csi2132", password="csi2132", host="159.203.44.157", port="5432")
print "Opened database successfully"

# initialize some variables
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
	createSponsors(movieID)

conn.commit()
conn.close()