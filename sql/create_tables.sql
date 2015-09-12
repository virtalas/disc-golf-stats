CREATE TABLE Player(
playerid SERIAL PRIMARY KEY,
firstname varchar(50) NOT NULL,
lastname varchar(50),
username varchar(50) NOT NULL,
password varchar(50) NOT NULL
);

CREATE TABLE Course(
courseid SERIAL PRIMARY KEY,
name varchar(50) NOT NULL,
city varchar(50)
);

CREATE TABLE Hole(
holeid SERIAL PRIMARY KEY,
courseid INTEGER REFERENCES Course(courseid),
hole_num smallint NOT NULL,
par smallint NOT NULL
);

CREATE TABLE Game(
gameid SERIAL PRIMARY KEY,
courseid INTEGER REFERENCES Course(courseid),
gamedate timestamp,
comment varchar(300),
rain boolean DEFAULT FALSE,
wet_no_rain boolean DEFAULT FALSE,
windy boolean DEFAULT FALSE,
variant boolean DEFAULT FALSE,
dark boolean DEFAULT FALSE,
led boolean DEFAULT FALSE,
snow boolean DEFAULT FALSE
);

CREATE TABLE Score(
scoreid SERIAL PRIMARY KEY,
gameid INTEGER REFERENCES Game(gameid),
holeid INTEGER REFERENCES Hole(holeid),
playerid INTEGER REFERENCES Player(playerid),
stroke smallint NOT NULL,
ob smallint DEFAULT 0
);
