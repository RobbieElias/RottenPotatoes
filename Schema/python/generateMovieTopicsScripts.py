#generate the scripts to create topics
import omdb

def printTopics(movie):
	
	return;

#open up the input file
f = open('top250.txt', 'r')
#erase the output
fo.seek(0)
fo.truncate()

#go through the list of 250 movie titles
for line in f:
	try:
		movie = omdb.title(line)
		print movie.title
	except:
		print''

f.close()
fo.close()


