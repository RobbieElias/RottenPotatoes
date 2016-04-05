#!/usr/bin/env python

# Import the imdb package.
import imdb

# Create the object that will be used to access the IMDb's database.
ia = imdb.IMDb() # by default access the web.

f = open('top250.txt', 'r')
for line in f:
	splitStr = line.split("=")
	title = splitStr[0]
	if "'" in title:
		title = title.replace("'", "''")
	date = splitStr[1].strip()
	s_result = ia.search_movie(line)
	if len(s_result) > 0:
		movie = s_result[0]
		ia.update(movie)
		#director = movie['director'][0] # get a list of Person objects.
		part1 = "INSERT INTO movie(name, dateReleased) VALUES (\'"
		part2 = title
		part3 = "\', "
		part4 = date
		part5 = ");"
		print part1+part2+part3+part4+part5
	else:
		print '------------- could not find this title' + title
