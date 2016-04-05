#!/usr/bin/env python

# Import the imdb package.
import omdb
import imdb

def saveTitle(movie,fo):
	title = movie.title
	fo.write('\nmovie='+str(title))
	return;

def saveDirector(movie,fo):
	director = movie.director
	fo.write('\ndirector='+str(director))
	return;

def savePoster(movie,fo):
	poster = movie.poster
	fo.write('\nposter='+str(poster))
	return;

def saveActors(movie,fo):
	actors = movie.actors
	fo.write('\nactor='+str(actors))
	return;

def saveRating(movie,fo):
	rated = movie.rated
	fo.write('\nrated='+str(rated))
	return;

def saveGenre(movie,fo):
	genre = movie.genre
	fo.write('\ngenre='+str(genre))
	return;

def saveReleased(movie,fo):
	released = movie.released
	fo.write('\nreleased='+str(released))
	return;

#open up the input and output files
f = open('top250.txt', 'r')
fo = open("stored250.txt", "a")
#erase the output
fo.seek(0)
fo.truncate()

#go through the list of 250 movie titles
for line in f:
	try:
		movie = omdb.title(line)
		print movie.title
		print movie.director
		saveTitle(movie,fo)
		saveDirector(movie,fo)
		savePoster(movie,fo)
		saveActors(movie,fo)
		saveRating(movie,fo)
		saveGenre(movie,fo)
		saveReleased(movie,fo)
	except:
		print''

f.close()
fo.close()
	