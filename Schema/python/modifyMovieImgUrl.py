#!/usr/bin/python

import psycopg2
import omdb
import os
from os import walk
from os import rename
import difflib

def getImageFiles():
	f = []
	directory = ''
	for (dirpath, dirnames, filenames) in walk("/Applications/MAMP/htdocs/RottenPotatoes/Web/images/movies"):
		f.extend(filenames)
		directory = dirpath
		break
	return f;

def getMovie(name):
	movie = omdb.title(name)
	return movie;
	#cur.execute("INSERT INTO MovieTopics (topicID,movieID,language,subtitles,country) \
    #  VALUES ('%s', '%s', 'English', 'yes', '%s' )" % (topicID,movieID,country))

def getClosestImg(movieName):
	#print '\n'+movieName
	sname = movieName.strip().replace(' ','_')+'.jpg'
	#print sname
	try:
		return difflib.get_close_matches(movieName.lower(), imgNames)[0]
	except:
		return 'no_img.jpg'


movieNames = []
imgNames = getImageFiles()
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
	#createMovieTopics(movieID)
	movieNames.append(movieName.lower())
	closestImg = 'images/movies/'+getClosestImg(movieName)
	print closestImg
	cur.execute("UPDATE movie SET posterURL = '%s' WHERE movieID = '%s' " % (closestImg,movieID));



conn.commit()
conn.close()