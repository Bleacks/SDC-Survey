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
# REMEMBER TO CHANGE THE CONST `FIELDS_LENGTH` IN Database.php ACCORDING TO THESE ONE

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
        Email     Varchar (40) ,
        Pass      Varchar (60) ,
        City      Varchar (40) ,
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
(1, 'Soumissions illimitées', 'Compte réinitialisé tous les jours', 'Les réponses associée à ce questionnaire sont gérée par pool d\'une semaine', 7, 0),
(2, 'Cinq soumissions', 'Compte réinitialisé tous les jours', 'Les réponses associée à ce questionnaire sont gérée par pool d\'une semaine et limité à 5 soumissions', 7, 5),
(3, 'Soumission unique', 'Compte réinitialisé tous les jours', 'Les réponses associée à ce questionnaire sont hedbomadaires et uniques', 7, 1),
(4, 'Questionnaire - Travail collectif', 'Description', 'Détails', 7, 0);

-- Type 1: Multiple choice (Checkbox)
-- Type 2: Unique choice (Select)
-- Type 3: Multiple choice (Chips)
-- Type 4: Unique choice (Radio)
-- Type 5: Group question (checkbox for each member of group)
-- Type 6: Text input

INSERT INTO `genericquestion` (`Text`, `Other`, `Type`, `idGS`, `Required`) VALUES
('Quand avez-vous travaillé ?', 1, 1, 1, 0),
('Combien de temps avez-vous travaillé ?', 1, 2, 1, 0),
('A quel moment de la journée avez-vous travaillé ?', 0, 1, 1, 0),
('Quels outils avez-vous utilisés ?', 1, 3, 1, 1),

('A quel moment de la journée avez-vous travaillé ?', 0, 1, 4, 1),
('Quels outils avez-vous utilisés ?', 1, 3, 4, 1),
('Quel était le mode de travail ?', 0, 5, 4, 1),
('Quelle était le type d\'activité ?', 0, 4, 4, 1),
('Combien de temps avez-vous travaillé ?', 0, 4, 4, 1),
('Focus', 0, 6, 4, 0), -- // TODO: Ajouter la prise en charge du focus (document)
('Quelle à été l\'action prioritaire ?', 0, 2, 4, 1);

INSERT INTO `genericanswer` (`Text`, `idGQ`) VALUES
('Matin', 1),
('Midi', 1),
('Soir', 1),
('Nuit', 1),
('Une heure', 2),
('Deux heures', 2),
('Matin', 3),
('Après-midi', 3),
('Soir', 3),

('Matin', 5),
('Après-midi', 5),
('Soir', 5),

('ArchiCAD', 6),
('Artlantis', 6),
('AutoCAD', 6),
('Dropbox', 6),
('Excel', 6),
('Face à face', 6),
('Facebook', 6),
('Framatalk', 6),
('Google drive', 6),
('Grasshopper-Rhinoceros', 6),
('InDesign', 6),
('Illustrator', 6),
('Kanbanchi', 6),
('Lumion', 6),
('Messenger', 6),
('Mycloud', 6),
('Papier-crayon', 6),
('Photoshop', 6),
('PowerPoint', 6),
('Revit', 6),
('Rhinoceros', 6),
('Sketchboard', 6),
('SketchUp', 6),
('SketSha', 6),
('Skype', 6),
('Teambition', 6),
('Téléphone', 6),
('Word', 6),

('Individuel', 7),

('Collaboration', 8),
('Coopération', 8),
('Voir avec G', 8),
('Voir avec G.G', 8),

-- // TODO: Ajouter les conversions lors de l'exportation
('Quelques minutes', 9),
('Envrion 30\'', 9),
('Environ 60\'', 9),
('Quelques heures', 9),
('Une journée', 9),
('Une demi journée', 9),

('Se coordonner sur les tâches à faire', 11),
('Se coordonner sur les tâches à suivre', 11),
('Formaliser une idée en cours', 11),
('Produire un document final', 11),
('Communiquer de manière formelle', 11),
('Communiquer de manière informelle', 11);




INSERT INTO `groups` (`idG`, `Name`) VALUES
(1, 'le clan des semi-croustillants');

INSERT INTO `users` (`idU`, `FirstName`, `LastName`, `Email`, `Pass`, `City`, `Age`, `Status`, `Admin`, `idG`) VALUES
(1, 'Test', 'User', 'test@user.fr', '$2y$10$/P91ociOc1taPWk1DC7gReqxXTPTGIt6iM9w3M.yuJne8kvtBJgp6', '1', 21, NULL, NULL, 1),
(1, 'Jean-Michel', 'Encadrant', 'admin@email.fr', '$2y$10$/P91ociOc1taPWk1DC7gReqxXTPTGIt6iM9w3M.yuJne8kvtBJgp6', '1', 21, NULL, 1, NULL),
(2, 'Maxime', 'Dolet', 'maxime.dolet@list.lu', '$2y$10$/P91ociOc1taPWk1DC7gReqxXTPTGIt6iM9w3M.yuJne8kvtBJgp6', 'Belval', 21, 1, 1, 1);

INSERT INTO `iteration` (`idIT`, `BeginAt`, `idGS`) VALUES
(1, '2017-14-08 00:00:00', 1),
(2, '2017-14-08 00:00:00', 2),
(3, '2017-14-08 00:00:00', 3),
(4, '2017-14-08 00:00:00', 2);

INSERT INTO `survey` (`idS`, `StartedAt`, `FinishedAt`, `Document`, `idU`, `idIT`) VALUES
(1, '2017-08-03 11:00:00', '2017-08-03 12:00:00', 'Le document', 1, 1),
(2, '2017-08-03 11:00:00', '2017-08-03 12:00:00', 'Le document', 1, 1),
(3, '2017-08-03 11:00:00', '2017-08-03 12:00:00', 'Le document', 1, 2),
(4, '2017-08-03 11:00:00', '2017-08-03 12:00:00', 'Le document', 1, 2),
(5, '2017-08-03 11:00:00', '2017-08-03 12:00:00', 'Le document', 1, 4),
(6, '2017-08-03 11:00:00', '2017-08-03 12:00:00', 'Le document', 1, 2),
(7, '2017-08-03 11:00:00', '2017-08-03 12:00:00', 'Le document', 1, 3);
