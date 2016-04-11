-- (40 marks) Create a number of SQL queries to explore this data.  The following is a suggested list of
-- “typical” queries that should be implemented. The general idea is that you should be able to explore
-- the data as contained in your database, in an “ad hoc” fashion.
 
-- Movies, Actors, Directors, Studios and Topics
-- ############################################
-- a. Display all the information about a user‐specified movie. That is, the user should select the
-- name of the movie from a list, and the information as contained in the movie table should then
-- be displayed on the screen.
 
SELECT * 
	FROM movie 
	WHERE movie.name = 'Inception';
 
-- b. Display the full list of actors, and their roles, of a specific movie. That is, the user should select
-- the name of the movie from a list, and all the details of the actors, together with their roles,
-- should be displayed on the screen.*/    
 
SELECT M.name AS movie, A.actorid, A.name AS actor, AP.role, A.dateOfBirth
 	FROM actor A
 	JOIN actorPlays AP ON AP.actorID = A.actorID
 	JOIN movie M ON M.movieID = AP.movieID 
 	WHERE M.name = 'Star Wars';
 
-- c. For each user‐specified category of movie, list the details of the director(s) and studio(s),
-- together with the date that the movie has been released. The user should be able to select the
-- category (e.g. Horror or Nature) from a list.

SELECT M.name AS movie, M.datereleased, D.name AS director, S.name AS studio, T.description AS genre
	FROM director D, movie M, studio S, sponsors SP, topics T, movieTopics MT, directs DR
	WHERE T.topicID = MT.topicID
	AND MT.movieID = M.movieID
	AND M.movieID = SP.movieID
	AND SP.studioID = S.studioID
	AND DR.movieID = M.movieID
	AND DR.directorID = D.directorID
	AND T.description = 'Crime'
	ORDER BY movie, director, studio;

 -- d. Display the information about the actor that appeared the most often in the movies, as
-- contained in your database. Display this information together with the details of the director(s)
-- and the studio(s) that s(he) worked with.

SELECT A.actorID, A.name AS actor, A.dateOfBirth, D.name AS director, S.name AS studio
	FROM actor A, actorPlays AP, director D, movie M, studio S, sponsors SP, directs DR
	WHERE AP.actorID = A.actorID
	AND AP.movieID = M.movieID
	AND M.movieID = SP.movieID
	AND SP.studioID = S.studioID
	AND DR.movieID = M.movieID
	AND DR.directorID = D.directorID
	AND A.actorID = (SELECT actorID
				FROM actorPlays 
				GROUP BY actorID
				ORDER BY count(actorID) DESC
				LIMIT 1);
				

-- e. Display the information about the two actors that appeared the most often together in the
-- movies, as contained in your database.

SELECT A.name
	FROM(
		SELECT T3.ID1 as ID, COUNT(T3.ID1)
		FROM (SELECT DISTINCT T1.ID1 as ID1, T1.ID2 as ID2, T1.MID as MID -- A list of two actors who share more than one movie 
			FROM	(SELECT DISTINCT P1.actorID AS ID1, P2.actorID AS ID2, P1.movieID AS MID -- A list of actor, actor, shared movie
					FROM actorPlays P1, actorPlays P2
					WHERE P1.movieID = P2.movieID
					AND P1.actorID != P2.actorID) as T1,
				(SELECT DISTINCT P1.actorID AS ID1, P2.actorID AS ID2, P1.movieID AS MID
					FROM actorPlays P1, actorPlays P2
					WHERE P1.movieID = P2.movieID
					AND P1.actorID != P2.actorID) as T2
			WHERE T1.ID1 = T2.ID1
			AND T1.ID2 = T2.ID2
			AND T1.MID != T2.MID
			ORDER BY T1.ID1 ) T3
		GROUP BY T3.ID1
		ORDER BY COUNT(T3.ID1) DESC
		LIMIT 2
		) T,
		actor A
	WHERE A.actorID = T.ID;
	
-- Ratings of movies
-- #################################
-- f. Find the names of the ten movies with the highest overall ratings in your database.

SELECT M.movieID, M.name, (SELECT coalesce(AVG(W.rating), 0) FROM watches W WHERE W.movieID = M.movieID) rating 
	FROM movie M 
	ORDER BY rating DESC, name LIMIT 10;

-- g. Find the movie(s) with the highest overall rating in your database. Display all the movie details,
-- together with the topics (tags) associated with it.

SELECT M.movieID, M.name, M.datereleased, T.description AS genre
	FROM movie M, topics T, movietopics MT
	WHERE M.movieID = MT.movieID
	AND MT.topicID = t.topicID
	AND (SELECT coalesce(AVG(W.rating), 0) FROM watches W WHERE W.movieID = M.movieID) = 
		(SELECT coalesce(AVG(W.rating), 0) AS rating FROM watches W GROUP BY W.movieID ORDER BY rating DESC LIMIT 1);


-- h. Find the total number of rating for each movie, for each user. That is, the data should be
-- grouped by the movie, the specific users and the numeric ratings they have received.

SELECT U.firstName, U.lastName, M.name AS movie, W.rating
	FROM movieUser U, movie M, watches W
	WHERE W.userID = U.userID
	AND M.movieID = W.movieID
	AND W.rating IS NOT NULL
	ORDER BY movie, firstname, lastname;

-- i. Display the details of the movies that have not been rated since January 2016.

SELECT M.name, M.movieID, M.dateReleased
	FROM movie M
	WHERE (SELECT COUNT(*)
		FROM watches W
		WHERE W.movieID = M.movieID
		AND W.rating IS NOT NULL
		AND W.dateWatched > '2016-01-01') = 0;
	
-- j. Find the names, release dates and the names of the directors of the movies that obtained rating
-- that is lower than any rating given by user X. Order your results by the dates of the ratings.
-- (Here, X refers to any user of your choice.)

SELECT M.movieID, M.name AS movie, M.dateReleased, D.name AS director, W.rating, W.datewatched
	FROM movie M, watches W, director D, directs DR
	WHERE M.movieID = DR.movieID
	AND DR.directorID = D.directorID
	AND W.movieId = M.movieID
	AND W.rating < ANY (SELECT rating
				FROM watches WHERE userid = 60) -- change userid here
	ORDER BY W.datewatched DESC;

-- k. List the details of the Type Y movie that obtained the highest rating. Display the movie name
-- together with the name(s) of the rater(s) who gave these ratings. (Here, Type Y refers to any
-- movie type of your choice, e.g. Horror or Romance.)  

SELECT M.name AS movie, U.firstName, U.lastName, W.rating
	FROM movie M, movieUser U, watches W
	WHERE M.movieID = W.movieId
	AND W.userID = U.userID
	AND M.movieID = (SELECT W2.movieID 
				FROM watches W2, movieTopics MT, TOPICS T 
				WHERE W2.movieID = MT.movieID 
				AND T.topicID = MT.topicID 
				AND T.description = 'Crime' -- change genre here 
				GROUP BY W2.movieID 
				ORDER BY AVG(W2.rating) 
				DESC LIMIT 1);

-- l. Provide a query to determine whether Type Y movies are “more popular” than other movies.  
-- (Here, Type Y refers to any movie type of your choice, e.g. Nature.) Yes, this query is open to
-- your own interpretation!

SELECT TC.description, T.S
	FROM(SELECT T.TID AS TID, sum(T.R) AS S
		FROM(SELECT T.topicID AS TID, W.rating AS R
			FROM topics T, watches W, movieTopics MT
			WHERE MT.movieID = W.movieID
			AND MT.topicID = T.topicID
			AND W.rating NOTNULL) T
		GROUP BY TID
		ORDER BY SUM(T.R) DESC) T,topics TC
	WHERE TC.topicID = T.TID
	ORDER BY T.S DESC;
	
-- m. Find the names, join‐date and profiling information (age‐range, gender, and so on) of the users
-- that give the highest overall ratings. Display this information together with the names of the
-- movies and the dates the ratings were done.

SELECT U.firstName, U.lastName, P.ageRange, P.gender, P.ageRange, P.occupation, W.rating, W.dateWatched, M.name AS movie
	FROM movieUser U, profile P, watches W, movie M
	WHERE P.userID = U.userID
	AND W.userID = U.userID
	AND W.rating NOTNULL
	AND M.movieID = W.movieID
	AND (SELECT AVG(W2.rating) FROM watches W2 WHERE W2.userID = U.userID) >= 4 -- users with average ratings of 4+
	ORDER BY firstName, lastName DESC;
	
-- o. Find the names and emails of all users who gave ratings that are lower than that of a rater with
-- a name called John Smith. (Note that there may be more than one rater with this name).*/

SELECT U.firstName, U.lastName, U.email
	FROM movieUser U, watches W
	WHERE U.userID = W.userID
	AND W.rating < ANY (SELECT  W.rating
				FROM watches W, movieUser U
				WHERE W.userID = U.userID
				AND U.firstName = 'John'
				AND U.lastName = 'Smith')
	GROUP BY U.firstName, U.lastName, U.email
	ORDER BY U.firstName, U.lastName;

-- p. Find the names and emails of the users that provide the most diverse ratings within a specific
-- genre. Display this information together with the movie names and the ratings. For example,
-- Jane Doe may have rated terminator 1 as a 1, Terminator 2 as a 10 and Terminator 3 as a 3.  
-- Clearly, she changes her mind quite often!

SELECT U.firstName, U.lastName, U.email, T.description AS genre, M.name AS movie, W.rating
	FROM movieUser U, movie M, topics T, movieTopics MT, watches W
	WHERE U.userID = W.userID
	AND W.movieID = M.movieID
	AND MT.movieID = M.movieID
	AND T.topicID = MT.topicID
	AND W.rating IS NOT NULL
	AND T.description = 'Animation' -- change genre here
	AND EXISTS (SELECT 1 
			FROM watches W2, movieTopics MT2 
			WHERE W2.movieID = MT2.movieID 
			AND W2.userID = U.userID 
			AND MT2.topicID = MT.topicID 
			HAVING (MAX(W2.rating) - MIN(W2.rating)) >= 3)
	ORDER BY firstName, lastName, rating;


	
