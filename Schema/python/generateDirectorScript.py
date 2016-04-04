#generate the scripts to create topics

f = open('stored250.txt', 'r')
directorList = ['horror','action']

def addDirectorToList(director):
	if director not in directorList:
		directorList.append(director)
	return;

for line in f:
	if 'director' in line:
			splitStr1 = line.split('=')[1]
			splitStr2 = splitStr1.split(',')
			for str in splitStr2:
				addDirectorToList(str.strip())

for g in directorList:
	part1 = "INSERT INTO director(lastName) VALUES (\'"
	part2 = g
	part3 = "\');"
	print part1+part2+part3