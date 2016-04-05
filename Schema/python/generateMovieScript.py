#!/usr/bin/env python
#this generates a console output that can be copied into the seed file

# Import the imdb package.
import imdb

# Create the object that will be used to access the IMDb's database.
ia = imdb.IMDb() # by default access the web.

#open the text file with the top 250 movies
f = open('top250.txt', 'r')
for line in f:
	splitStr = line.split("=")
	
	#extract the title from the line
	title = splitStr[0]
	if "'" in title:
		title = title.replace("'", "''") #need to escape quotes for inputting into db
	
	#extract the date released
	date = splitStr[1].strip()
	s_result = ia.search_movie(line)
	
	if len(s_result) > 0:
		movie = s_result[0]
		ia.update(movie)
		part1 = "INSERT INTO movie(name, dateReleased) VALUES (\'"
		part2 = title
		part3 = "\', "
		part4 = date
		part5 = ");"
		print part1+part2+part3+part4+part5
	else:
		print '------------- could not find this title' + title
