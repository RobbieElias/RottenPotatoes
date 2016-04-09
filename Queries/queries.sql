# Put SQL queries in this directory
/*(40 marks) Create a number of SQL queries to explore this data.  The following is a suggested list of
“typical” queries that should be implemented. The general idea is that you should be able to explore
the data as contained in your database, in an “ad hoc” fashion.

Movies, Actors, Directors, Studios and Topics
##################################################
a. Display all the information about a user‐specified movie. That is, the user should select the
name of the movie from a list, and the information as contained in the movie table should then
be displayed on the screen.*/

SELECT * 
	FROM movie 
	WHERE movie.name = "Inception";

/*b. Display the full list of actors, and their roles, of a specific movie. That is, the user should select
the name of the movie from a list, and all the details of the actors, together with their roles,
should be displayed on the screen.*/    

SELECT a.name, a.role, a.dateOfBirth
	FROM actor a, movie m
	WHERE movie.

/*c. For each user‐specified category of movie, list the details of the director(s) and studio(s),
together with the date that the movie has been released. The user should be able to select the
category (e.g. Horror or Nature) from a list.

d. Display the information about the actor that appeared the most often in the movies, as
contained in your database. Display this information together with the details of the director(s)
and the studio(s) that s(he) worked with.

e. Display the information about the two actors that appeared the most often together in the
movies, as contained in your database.
   
Ratings of movies
#################################
f. Find the names of the ten movies with the highest overall ratings in your database.

g. Find the movie(s) with the highest overall rating in your database. Display all the movie details,
together with the topics (tags) associated with it.

h. Find the total number of rating for each movie, for each user. That is, the data should be
grouped by the movie, the specific users and the numeric ratings they have received.

i. Display the details of the movies that have not been rated since January 2016.

j. Find the names, release dates and the names of the directors of the movies that obtained rating
that is lower than any rating given by user X. Order your results by the dates of the ratings.
(Here, X refers to any user of your choice.)

k. List the details of the Type Y movie that obtained the highest rating. Display the movie name
together with the name(s) of the rater(s) who gave these ratings. (Here, Type Y refers to any
movie type of your choice, e.g. Horror or Romance.)  

l. Provide a query to determine whether Type Y movies are “more popular” than other movies.  
(Here, Type Y refers to any movie type of your choice, e.g. Nature.) Yes, this query is open to
your own interpretation!
Users and their ratings

m. Find the names, join‐date and profiling information (age‐range, gender, and so on) of the users
that give the highest overall ratings. Display this information together with the names of the
movies and the dates the ratings were done.

n. Find the names, join‐date and profiling information (age‐range, gender, and so on) of the users
that rated a specific movie (say movie Z) the most frequently. Display this information together
with their comments, if any. (Here movie Z refers to a movie of your own choice, e.g. The
Hundred Foot Journey).*/

/*o. Find the names and emails of all users who gave ratings that are lower than that of a rater with
a name called John Smith. (Note that there may be more than one rater with this name).*/

/*p. Find the names and emails of the users that provide the most diverse ratings within a specific
genre. Display this information together with the movie names and the ratings. For example,
Jane Doe may have rated terminator 1 as a 1, Terminator 2 as a 10 and Terminator 3 as a 3.  
Clearly, she changes her mind quite often!*/