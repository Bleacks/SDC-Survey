DROP TABLE IF EXISTS Answer;
DROP TABLE IF EXISTS AnsweredSurvey;
DROP TABLE IF EXISTS OtherAnswer;
DROP TABLE IF EXISTS PendingSub;
DROP TABLE IF EXISTS Token;
DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS Groups;
DROP TABLE IF EXISTS Question;
DROP TABLE IF EXISTS Survey;

CREATE TABLE Survey (
	idS				int(6) AUTO_INCREMENT,
	StartedAt		Datetime,
	FinishedAt		Datetime,
	Document		VARCHAR(250),
	Name			VARCHAR(50),
	Type			int(2),
	PRIMARY KEY (idS)
);

CREATE TABLE Question (
	idQ				int(6) AUTO_INCREMENT,
	Question		VARCHAR(50),
	PRIMARY KEY (idQ)
);

CREATE TABLE Groups (	-- TODO: Voir pour ajouter un lien vers une image
	idG				int(6) AUTO_INCREMENT,
	Name			VARCHAR(30),
	PRIMARY KEY (idG)
);

CREATE TABLE Users (
	idU				int(6) AUTO_INCREMENT,
	idG				int(6),
	FirstName		VARCHAR(30),
	LastName		VARCHAR(30),
	Email			VARCHAR(30),
	Pass			VARCHAR(50), -- TODO: Adapter à la taille du hash de sortie
	City			VARCHAR(30),
	Age				int(2),
	Status 			boolean,
	Admin			boolean,
	PRIMARY KEY (idU),
	CONSTRAINT fk_users_idg FOREIGN KEY (idG)
	REFERENCES Groups(idG)
);

CREATE TABLE Token (
	idT				VARCHAR(25),
	idU				int(6),
	lastUsed		Datetime,
	PRIMARY KEY (idT),
	CONSTRAINT fk_users_idu FOREIGN KEY (idU)
	REFERENCES Users(idU)
);

CREATE TABLE Answer (
	idA				int(6) AUTO_INCREMENT,
	idQ				int(6),
	ChosenAt		Datetime,
	Answer			int(3),
	PRIMARY KEY (idA),
	CONSTRAINT fk_answer_idq FOREIGN KEY (idQ)
	REFERENCES Question(idQ)
);

CREATE TABLE AnsweredSurvey (
	idS				int(6),
	idU				int(6),
	PRIMARY KEY (idS, idU),
	CONSTRAINT fk_answeredsurvey_ids FOREIGN KEY (idS)
	REFERENCES Survey(idS),
	CONSTRAINT fk_answeredsurvey_idu FOREIGN KEY (idU)
	REFERENCES Users(idU)
);

CREATE TABLE OtherAnswer (
	idQ				int(6),
	idU				int(6),
	ChosenAt		Datetime,
	PRIMARY KEy (idQ, idU),
	CONSTRAINT fk_otheranswer_idq FOREIGN KEY (idQ)
	REFERENCES Question(idQ),
	CONSTRAINT fk_otheranswer_idU FOREIGN KEY (idU)
	REFERENCES Users(idU)
);

CREATE TABLE PendingSub (
	Token			VARCHAR(10),
	idU				int(6),
	SubscribedAt	Datetime,
	PRIMARY KEY (Token),
	CONSTRAINT fk_pendingsub_idu FOREIGN KEY (idU)
	REFERENCES Users(idu)
);

DROP EVENT IF EXISTS CleanPendingSub;
-- Cleans pending subscription older than 1 day, comes in addition of the PhP duplicate token gestion

CREATE EVENT CleanPendingSub
	ON SCHEDULE EVERY '1' DAY
	STARTS NOW()
DO
    DELETE Users FROM Users INNER JOIN PendingSub ON Users.idU = PendingSub.idU WHERE TIMESTAMPDIFF(HOUR, SubscribedAt, NOW()) > 24;
	DELETE PendingSub FROM PendingSub WHERE TIMESTAMPDIFF(HOUR, SubscribedAt, NOW()) > 24;
