﻿-- Raw schema code used to generate the Database
SET search_path TO 'MovieRecommender';

CREATE TABLE movieUser (
   userID             SERIAL,
   password           text NOT NULL,
   lastname           text NOT NULL,   
   firstname          text NOT NULL,
   email              text UNIQUE NOT NULL,
   city               text,
   province           text,
   country            text,
   CONSTRAINT proper_email CHECK (email ~* '^[A-Za-z0-9._%-]+@[A-Za-z0-9.-]+[.][A-Za-z]+$'),
   PRIMARY KEY (userID)
);

CREATE TABLE profile (
   userID            int REFERENCES movieuser(userid) ON DELETE CASCADE,
   ageRange          int4range CHECK (ageRange = '[0,18)' OR ageRange = '[18,26)' OR ageRange = '[26,40)' or ageRange = '[40,)'),
   gender            text CHECK (gender = 'male' OR gender = 'female' OR gender = 'other'),
   occupation        text,
   deviceUsed        text,
   PRIMARY KEY (userID)
);

CREATE TABLE movie (
   movieID           SERIAL,
   name              text NOT NULL,
   dateReleased      int CHECK (dateReleased >= 1900 AND dateReleased <= 2100),
   posterUrl         text,
   PRIMARY KEY (movieID)
);

CREATE TABLE watches (
   userID            int REFERENCES movieUser(userID) ON DELETE CASCADE,
   movieID           int REFERENCES movie(movieID) ON DELETE CASCADE,
   dateWatched       timestamp DEFAULT CURRENT_TIMESTAMP,   
   rating            int CHECK (rating >= 1 AND rating <= 5),
   PRIMARY KEY (movieID,userID)
);

CREATE TABLE topics (
   topicID           SERIAL,
   description       text UNIQUE NOT NULL,
   PRIMARY KEY (topicId)
);

CREATE TABLE movieTopics (
   topicID           int REFERENCES topics(topicID),
   movieID           int REFERENCES movie(movieID) ON DELETE CASCADE,
   language          text,   
   subtitles         text CHECK (subtitles = 'Yes' OR subtitles = 'No'),
   country           text,
   PRIMARY KEY (movieID,topicId)
);

CREATE TABLE studio (
   studioID          SERIAL,
   name              text NOT NULL,  
   country           text,
   PRIMARY KEY (studioId)
);

CREATE TABLE sponsors (
   studioID          int REFERENCES studio(studioID) ON DELETE CASCADE,
   movieID           int REFERENCES movie(movieID) ON DELETE CASCADE,
   PRIMARY KEY (studioId,movieID)
 );

CREATE TABLE director (
   directorID        SERIAL,
   name              text NOT NULL,  
   dateOfBirth       date,
   PRIMARY KEY (directorId)
); 

CREATE TABLE directs (
   movieID           int REFERENCES movie(movieID) ON DELETE CASCADE,
   directorID        int REFERENCES director(directorID) ON DELETE CASCADE,
   PRIMARY KEY (movieId,directorID)
);

CREATE TABLE actor (
   actorID           SERIAL,
   name              text NOT NULL,  
   dateOfBirth       date,
   PRIMARY KEY (actorId)
);

CREATE TABLE actorPlays (
   movieID           int REFERENCES movie(movieID) ON DELETE CASCADE,
   actorID           int REFERENCES actor(actorID) ON DELETE CASCADE,
   role              text,
   PRIMARY KEY (actorId,movieID)
);
