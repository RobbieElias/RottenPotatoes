#generate the scripts to create topics
#this generates a console output that can be copied into the seed file
f = open('stored250.txt', 'r')
genreList = ['horror','action']

def addGenreToList(genre):
	if genre not in genreList:
		genreList.append(genre)
	return;

for line in f:
	if 'genre' in line:
		splitStr1 = line.split('=')[1]
		splitStr2 = splitStr1.split(',')
		for str in splitStr2:
			addGenreToList(str.strip())

for g in genreList:
	part1 = "INSERT INTO topics(description) VALUES (\'"
	part2 = g
	part3 = "\');"
	print part1+part2+part3