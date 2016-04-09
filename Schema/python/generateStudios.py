#!/usr/bin/python

import psycopg2
import omdb

def getMovie(name):
	movie = omdb.title(name,tomatoes=True)
	return movie;

def getProduction():
	try:
		return movie.production;
	except:
		return 'none';

def addStudio():
	country = 'none'
	production = getProduction()
	try:
		country = movie.country
	except:
		country = 'none'
	part1 = "INSERT INTO studio(name,country) VALUES (\'"
	part2 = production
	part3 = '\',\''
	part4 = country
	part5 = "\');"
	if (country != 'none' and production != 'none' and production != 'N/A' and  not (production in studioList)):
		print part1+part2+part3+part4+part5
		studioList.append(production)



# connect to the project database
conn = psycopg2.connect(database="CSI2132", user="csi2132", password="csi2132", host="159.203.44.157", port="5432")
print "Opened database successfully"

#fill the database for a movie
movieName = ''
movieID = ''

studioList = []

# get the list of movies
cur = conn.cursor()
cur.execute("SELECT * FROM movie")
rows = cur.fetchall()
for row in rows:
	movieID = row[0]
	movieName = row[1]
	movie = getMovie(movieName)
	#print movieName
	addStudio()
	

conn.commit()
conn.close()