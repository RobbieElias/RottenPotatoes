-- Put raw schema code here
SET search_path TO 'MovieRecommender';

--Please comment out the drop tables instead of deleting
DROP TABLE movieUser CASCADE;
DROP TABLE profile;
DROP TABLE movie CASCADE;
DROP TABLE actorPlays CASCADE;
DROP TABLE role CASCADE;

CREATE TABLE movieUser (
   userID             int  not null,
   password           text not null,
   lastname           text,   
   firstname          text,
   email              text,
   city               text,
   province           text,
   country            text,
   PRIMARY KEY (userID)
);

CREATE TABLE profile (
   userID            int REFERENCES movieuser(userid) ON DELETE CASCADE,
   ageRange          int,
   yearBorn          int,   
   gender            text,
   occupation        text,
   deviceUsed        text,
   PRIMARY KEY (userID)
);

CREATE TABLE movie (
   movieID           int not null,
   ageRange          int,
   yearBorn          int,   
   gender            text,
   occupation        text,
   deviceUsed        text,
   PRIMARY KEY (movieID)
);

CREATE TABLE watches (
   userID            int REFERENCES movieUser(userID) ON DELETE CASCADE,
   movieID           int REFERENCES movie(movieID) ON DELETE CASCADE,
   dateWatched       date,   
   rating            int,
   PRIMARY KEY (movieID,userID)
);

CREATE TABLE topics (
   topicID           int NOT NULL,
   description       text,
   PRIMARY KEY (topicId)
);

CREATE TABLE movieTopics (
   topicID           int REFERENCES topics(topicID),
   movieID           int REFERENCES movie(movieID) ON DELETE CASCADE,
   language          text,   
   subtitles         text,
   country           text,
   PRIMARY KEY (movieID,topicId)
);

CREATE TABLE studio (
   studioID          int NOT NULL,
   name              text,  
   country           text,
   PRIMARY KEY (studioId)
);

CREATE TABLE sponsors (
   studioID          int REFERENCES studio(studioID) ON DELETE CASCADE,
   movieID           int REFERENCES movie(movieID) ON DELETE CASCADE,
   PRIMARY KEY (studioId,movieID)
 );

CREATE TABLE director (
   directorID        int NOT NULL,
   lastName          text,  
   dateOfBirth       date,
   PRIMARY KEY (directorId)
); 

CREATE TABLE directs (
   studioID          int REFERENCES studio(studioID) ON DELETE CASCADE,
   directorID        int REFERENCES director(directorID) ON DELETE CASCADE,
   PRIMARY KEY (studioId,directorID)
);

CREATE TABLE actor (
   actorID           int NOT NULL,
   lastName          text,  
   firstname         text,
   dateOfBirth       date,
   PRIMARY KEY (actorId)
);

CREATE TABLE actorPlays (
   movieID           int REFERENCES movie(movieID) ON DELETE CASCADE,
   actorID           int REFERENCES actor(actorID) ON DELETE CASCADE,
   PRIMARY KEY (actorId,movieID)
);

CREATE TABLE role (
   roleID            int,
   actorID           int REFERENCES actor(actorID) ON DELETE CASCADE,
   name              text,
   PRIMARY KEY (roleID)
);



