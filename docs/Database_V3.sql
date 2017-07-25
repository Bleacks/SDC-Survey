DROP TABLE IF EXISTS PendingSub;
DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS Groups;
DROP TABLE IF EXISTS Token;

DROP TABLE IF EXISTS Answers;
DROP TABLE IF EXISTS GenericAnswer;
DROP TABLE IF EXISTS GenericQuestion;
DROP TABLE IF EXISTS Survey;
DROP TABLE IF EXISTS GenericSurvey;

#------------------------------------------------------------
#        Script MySQL.
#------------------------------------------------------------


#------------------------------------------------------------
# Table: GenericSurvey
#------------------------------------------------------------

CREATE TABLE GenericSurvey(
        idGS     int (11) Auto_increment  NOT NULL ,
        Name     Varchar (100) ,
        Cooldown Int ,
        PRIMARY KEY (idGS ) ,
        INDEX (Name )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: GenericQuestion
#------------------------------------------------------------

CREATE TABLE GenericQuestion(
        idGQ     int (11) Auto_increment  NOT NULL ,
        Question Varchar (100) ,
        idGS     Int ,
        PRIMARY KEY (idGQ ) ,
        INDEX (Question )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: GenericAnswer
#------------------------------------------------------------

CREATE TABLE GenericAnswer(
        idGA   int (11) Auto_increment  NOT NULL ,
        Answer Varchar (50) ,
        idGQ   Int ,
        PRIMARY KEY (idGA ) ,
        INDEX (Answer )
)ENGINE=InnoDB;


#------------------------------------------------------------
# Table: Survey
#------------------------------------------------------------

CREATE TABLE Survey(
        idS        int (11) Auto_increment  NOT NULL ,
        StartedAt  Datetime ,
        FinishedAt Datetime ,
        Document   Varchar (200) ,
        idGS       Int ,
        idU        Int ,
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
        idPS      Varchar (10) NOT NULL ,
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
        idPS         Varchar (10) NOT NULL ,
        SubscribedAt Datetime ,
        PRIMARY KEY (idPS )
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
ALTER TABLE Survey ADD CONSTRAINT FK_Survey_idGS FOREIGN KEY (idGS) REFERENCES GenericSurvey(idGS);
ALTER TABLE Survey ADD CONSTRAINT FK_Survey_idU FOREIGN KEY (idU) REFERENCES Users(idU);
ALTER TABLE Users ADD CONSTRAINT FK_Users_idG FOREIGN KEY (idG) REFERENCES Groups(idG);
ALTER TABLE Users ADD CONSTRAINT FK_Users_idPS FOREIGN KEY (idPS) REFERENCES PendingSub(idPS);
ALTER TABLE Token ADD CONSTRAINT FK_Token_idU FOREIGN KEY (idU) REFERENCES Users(idU);
ALTER TABLE Answers ADD CONSTRAINT FK_Answers_idGQ FOREIGN KEY (idGQ) REFERENCES GenericQuestion(idGQ);
ALTER TABLE Answers ADD CONSTRAINT FK_Answers_idS FOREIGN KEY (idS) REFERENCES Survey(idS);
ALTER TABLE Answers ADD CONSTRAINT FK_Answers_idGA FOREIGN KEY (idGA) REFERENCES GenericAnswer(idGA);
