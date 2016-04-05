#generate the scripts to create users
#this generates a console output that can be copied into the seed file
f = open('userNames.txt', 'r')
actorList = []

def addActorToList(actor):
	if actor not in actorList:
		actorList.append(actor)
	return;

for line in f:
	if 'actor' in line:
		splitStr1 = line.split('=')[1]
		splitStr2 = splitStr1.split(',')
		for str in splitStr2:
			addActorToList(str.strip())

for a in actorList:
	part1 = "INSERT INTO actor(name) VALUES (\'"
	part2 = g
	part3 = "\');"
	print part1+part2+part3