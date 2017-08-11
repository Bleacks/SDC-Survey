#############################################################
# Database regeneration
#############################################################

DROP DATABASE sdc;
CREATE DATABASE sdc;
USE sdc;

#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------


#------------------------------------------------------------
# Table: GenericSurvey
#------------------------------------------------------------

CREATE TABLE GenericSurvey(
        idGS            int (11) Auto_increment  NOT NULL ,
        Title           Varchar (100) ,
        Description     Varchar (100) ,
        More            Varchar (250) ,
        Lifespan        Int ,
        SubmissionLimit Int ,
        PRIMARY KEY (idGS ) ,
        INDEX (Title )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: GenericQuestion
#------------------------------------------------------------

CREATE TABLE GenericQuestion(
        idGQ     int (11) Auto_increment  NOT NULL ,
        Text     Varchar (100) ,
        Required Bool ,
        Other    Bool ,
        Type     Int ,
        idGS     Int ,
        PRIMARY KEY (idGQ ) ,
        INDEX (Text )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: GenericAnswer
#------------------------------------------------------------

CREATE TABLE GenericAnswer(
        idGA int (11) Auto_increment  NOT NULL ,
        Text Varchar (50) ,
        idGQ Int ,
        PRIMARY KEY (idGA ) ,
        INDEX (Text )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Survey
#------------------------------------------------------------

CREATE TABLE Survey(
        idS        int (11) Auto_increment  NOT NULL ,
        StartedAt  Datetime ,
        FinishedAt Datetime ,
        Document   Varchar (200) ,
        idU        Int ,
        idIT       Int ,
        PRIMARY KEY (idS ) ,
        INDEX (Document )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Users
#------------------------------------------------------------

CREATE TABLE Users(
        idU       int (11) Auto_increment  NOT NULL ,
        FirstName Varchar (40) ,
        LastName  Varchar (40) ,
        Email     Varchar (30) ,
        Pass      Varchar (60) ,
        City      Varchar (10) ,
        Age       Int ,
        Status    Bool ,
        Admin     Bool ,
        idG       Int ,
        PRIMARY KEY (idU )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Groups
#------------------------------------------------------------

CREATE TABLE Groups(
        idG  int (11) Auto_increment  NOT NULL ,
        Name Varchar (60) ,
        PRIMARY KEY (idG )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Token
#------------------------------------------------------------

CREATE TABLE Token(
        idT      Varchar (25) NOT NULL ,
        LastUsed Datetime ,
        idU      Int ,
        PRIMARY KEY (idT )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: PendingSub
#------------------------------------------------------------

CREATE TABLE PendingSub(
        idU          Int NOT NULL ,
        idPS         Varchar (10) NOT NULL ,
        SubscribedAt Datetime ,
        PRIMARY KEY (idU )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Recovery
#------------------------------------------------------------

CREATE TABLE Recovery(
        Code        Varchar (10) NOT NULL ,
        Email       Varchar (30) ,
        GeneratedAt Datetime ,
        PRIMARY KEY (Code ) ,
        INDEX (Email )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Iteration
#------------------------------------------------------------

CREATE TABLE Iteration(
        idIT    int (11) Auto_increment  NOT NULL ,
        BeginAt Datetime NOT NULL ,
        idGS    Int ,
        PRIMARY KEY (idIT )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Answers
#------------------------------------------------------------

CREATE TABLE Answers(
        idGQ Int NOT NULL ,
        idS  Int NOT NULL ,
        idGA Int NOT NULL ,
        PRIMARY KEY (idGQ ,idS ,idGA )
)ENGINE=InnoDB;

ALTER TABLE GenericQuestion ADD CONSTRAINT FK_GenericQuestion_idGS FOREIGN KEY (idGS) REFERENCES GenericSurvey(idGS);
ALTER TABLE GenericAnswer ADD CONSTRAINT FK_GenericAnswer_idGQ FOREIGN KEY (idGQ) REFERENCES GenericQuestion(idGQ);
ALTER TABLE Survey ADD CONSTRAINT FK_Survey_idU FOREIGN KEY (idU) REFERENCES Users(idU);
ALTER TABLE Survey ADD CONSTRAINT FK_Survey_idIT FOREIGN KEY (idIT) REFERENCES Iteration(idIT);
ALTER TABLE Users ADD CONSTRAINT FK_Users_idG FOREIGN KEY (idG) REFERENCES Groups(idG);
ALTER TABLE Token ADD CONSTRAINT FK_Token_idU FOREIGN KEY (idU) REFERENCES Users(idU);
ALTER TABLE Iteration ADD CONSTRAINT FK_Iteration_idGS FOREIGN KEY (idGS) REFERENCES GenericSurvey(idGS);
ALTER TABLE Answers ADD CONSTRAINT FK_Answers_idGQ FOREIGN KEY (idGQ) REFERENCES GenericQuestion(idGQ);
ALTER TABLE Answers ADD CONSTRAINT FK_Answers_idS FOREIGN KEY (idS) REFERENCES Survey(idS);
ALTER TABLE Answers ADD CONSTRAINT FK_Answers_idGA FOREIGN KEY (idGA) REFERENCES GenericAnswer(idGA);



#############################################################
# Testing values
#############################################################

INSERT INTO `genericsurvey` (`idGS`, `Title`, `Description`, `More`, `Lifespan`, `SubmissionLimit`) VALUES
(1, 'Illimité', 'Compte réinitialisé tous les jours', 'Les réponses associée à ce questionnaire sont gérée par pool d\'une journée', 7, 0),
(2, 'Cinq', 'Compte réinitialisé tous les jours', 'Les réponses associée à ce questionnaire sont gérée par pool d\'une journée et limité à 5 soumissions', 7, 5),
(3, 'Unique', 'Compte réinitialisé tous les jours', 'Les réponses associée à ce questionnaire sont quotidienne et uniques', 7, 1);

INSERT INTO `genericquestion` (`idGQ`, `Text`, `Other`, `Type`, `idGS`) VALUES
(1, 'Quand avez-vous travaillé ?', 1, 1, 1),
(2, 'Combien de temps avez-vous travaillé ?', 1, 2, 1),
(3, 'A quel moment de la journée avez-vous travaillé ?', 0, 1, 1),
(4, 'Quels outils avez-vous utilisés ?', 1, 3, 1);

INSERT INTO `genericanswer` (`idGA`, `Text`, `idGQ`) VALUES
(1, 'Matin', 1),
(2, 'Midi', 1),
(3, 'Soir', 1),
(4, 'Nuit', 1),
(5, 'Une heure', 2),
(6, 'Deux heures', 2),
(7, 'Matin', 3),
(8, 'Après-midi', 3),
(9, 'Soir', 3),
(10, 'Lumion', 4),
(11, 'Dropbox', 4),
(12, 'Illustrator', 4),
(13, 'Téléphone', 4),
(14, 'PowerPoint', 4),
(15, 'Skype', 4);

INSERT INTO `users` (`idU`, `FirstName`, `LastName`, `Email`, `Pass`, `City`, `Age`, `Status`, `Admin`, `idG`) VALUES
(1, 'Test', 'User', 'test@user.fr', '$2y$10$/P91ociOc1taPWk1DC7gReqxXTPTGIt6iM9w3M.yuJne8kvtBJgp6', '1', 21, NULL, NULL, NULL),
(2, 'Maxime', 'Dolet', 'maxime.dolet@list.lu', '$2y$10$/P91ociOc1taPWk1DC7gReqxXTPTGIt6iM9w3M.yuJne8kvtBJgp6', 'Belval', 21, 1, 1, NULL);

INSERT INTO `iteration` (`idIT`, `BeginAt`, `idGS`) VALUES
(1, '2017-08-03 00:00:00', 1),
(2, '2017-08-03 00:00:00', 2),
(3, '2017-08-03 00:00:00', 3),
(4, '2017-07-03 00:00:00', 2);

INSERT INTO `survey` (`idS`, `StartedAt`, `FinishedAt`, `Document`, `idU`, `idIT`) VALUES
(1, '2017-08-03 11:00:00', '2017-08-03 12:00:00', 'Le document', 1, 1),
(2, '2017-08-03 11:00:00', '2017-08-03 12:00:00', 'Le document', 1, 1),
(3, '2017-08-03 11:00:00', '2017-08-03 12:00:00', 'Le document', 1, 2),
(4, '2017-08-03 11:00:00', '2017-08-03 12:00:00', 'Le document', 1, 2),
(5, '2017-08-03 11:00:00', '2017-08-03 12:00:00', 'Le document', 1, 4),
(6, '2017-08-03 11:00:00', '2017-08-03 12:00:00', 'Le document', 1, 2),
(7, '2017-08-03 11:00:00', '2017-08-03 12:00:00', 'Le document', 1, 3);
