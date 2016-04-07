#!/usr/bin/python

import psycopg2
import omdb

def getMovie(name):
	movie = omdb.title(name)
	return movie;

def createMovieTopics(movieID):
	country = 'None'
	try:
		country = movie.country
	except:
		country = 'None'

	topic = 'None'
	topicID = 24
	try:
		topic = movie.genre.split(',')[0].strip()
	except:
		topic = 'None'
	
	#find the id of the topic
	cur.execute("SELECT topicID from topics WHERE description = '%s' " % topic)
	rows2 = cur.fetchall()
	for row in rows2:
		topicID = row[0]
	part1 = "INSERT INTO movieTopics(topicID,movieID,language,subtitles,country) VALUES (\'"
	part2 = str(topicID)
	part3 = "\',\'"
	part4 = str(movieID)
	part5 = "\',\'"
	part6 = "English"
	part7 = "\',\'"
	part8 = "Yes"
	part9 = "\',\'"
	part10 = country
	part11 = "\');"
	print part1+part2+part3+part4+part5+part6+part7+part8+part9+part10+part11
	#cur.execute("INSERT INTO MovieTopics (topicID,movieID,language,subtitles,country) \
    #  VALUES ('%s', '%s', 'English', 'yes', '%s' )" % (topicID,movieID,country))



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
	createMovieTopics(movieID)

conn.commit()
conn.close()