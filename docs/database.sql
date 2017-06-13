DROP TABLE IF EXISTS CollecRole;
DROP TABLE IF EXISTS Role;
DROP TABLE IF EXISTS UserTool;
DROP TABLE IF EXISTS CollecTool;
DROP TABLE IF EXISTS PersoTool;
DROP TABLE IF EXISTS Tool;
DROP TABLE IF EXISTS SCollec;
DROP TABLE IF EXISTS SPerso;
DROP TABLE IF EXISTS PendingSub;
DROP TABLE IF EXISTS Users;
DROP TABLE IF EXISTS Document;
DROP TABLE IF EXISTS Groups;

CREATE TABLE Role (
   RoleId	int(3) AUTO_INCREMENT,
   RoleName varchar(50) NOT NULL,
   PRIMARY KEY (RoleId)
);

CREATE TABLE Groups (
   GroupId    int(6) AUTO_INCREMENT,
   GroupName  VARCHAR(30) NOT NULL,
   GroupImage VARCHAR(100),
   PRIMARY KEY (GroupId)
);

CREATE TABLE Users (
   UserId      int(6) AUTO_INCREMENT,
   GroupId     int(6),
   FirstName	VARCHAR(30),
   LastName	   VARCHAR(30),
   Email        VARCHAR(40),
   Pass        VARCHAR(50), -- // TODO: Changer la longueur du varchar à celle du hash
   Admin       boolean,
   SubDate     Datetime,
   PRIMARY KEY (UserId),
   CONSTRAINT fk_user_groupid FOREIGN KEY (GroupId)
   REFERENCES Groups(GroupId)
);

CREATE TABLE Document (
	DocId			int(6),
	DocTitle		VARCHAR(50),
	DocContent	VARCHAR(500),
	PRIMARY KEY (DocId)
);

CREATE TABLE SCollec (
   SurvId      int(6) AUTO_INCREMENT,
   UserId      int(6),
   UseTime     int(6),
   StartDate   Datetime,
   FinishDate  Datetime,
   DocId       int(6),
   Context     int(1),        -- Time and Place : 4 Option (both same or different)
   PRIMARY KEY (SurvId),
   CONSTRAINT fk_scollec_userid FOREIGN KEY (UserId)
   REFERENCES Users(UserId),
   CONSTRAINT fk_scollec_docid FOREIGN KEY (DocId)
   REFERENCES Document(DocId)
);

CREATE TABLE SPerso (
   SurvId      int(6) AUTO_INCREMENT,
   UserId      int(6),
   UseTime     int(6),
   StartDate   Datetime,
   FinishDate  Datetime,
   DocId       int(6),
   PRIMARY KEY (SurvId),
   CONSTRAINT fk_sperso_userid FOREIGN KEY (UserId)
   REFERENCES Users(UserId),
   CONSTRAINT fk_sperso_docid FOREIGN KEY (DocId)
   REFERENCES Document(DocId)
);

CREATE TABLE CollecRole (
   SurvId      int(6),
   RoleId      int(6),
   NbPeople    int(6),
   PRIMARY KEY (SurvId, RoleId),
   CONSTRAINT fk_collecrole_survid FOREIGN KEY (SurvId)
   REFERENCES SCollec(SurvId),
   CONSTRAINT fk_collecrole_roleid FOREIGN KEY (RoleId)
   REFERENCES Role(RoleId)
);

CREATE TABLE Tool (
   ToolId      int(6) AUTO_INCREMENT,
   ToolName    VARCHAR(40),
   Category    int(2),           -- Category defines if : Tool / Focus / Activity / Abstraction
   PRIMARY KEY (ToolId)
);

CREATE TABLE UserTool (
   UserId      int(6),
   ToolId      int(6),
   PRIMARY KEY (UserID, ToolId),
   CONSTRAINT fk_usertool_userid FOREIGN KEY (UserId)
   REFERENCES Users(UserId),
   CONSTRAINT fk_usertool_toolid FOREIGN KEY (ToolId)
   REFERENCES Tool(ToolId)
);

CREATE TABLE CollecTool (
   SurvId      int(6),
   ToolId      int(6),
   PRIMARY KEY (SurvId, ToolId),
   CONSTRAINT fk_collectool_survid FOREIGN KEY (SurvId)
   REFERENCES SCollec(SurvId),
   CONSTRAINT fk_collectool_toolid FOREIGN KEY (ToolId)
   REFERENCES Tool(ToolId)
);

CREATE TABLE PersoTool (
   SurvId      int(6),
   ToolId      int(6),
   PRIMARY KEY (SurvId, ToolId),
   CONSTRAINT fk_persotool_survid FOREIGN KEY (SurvId)
   REFERENCES SPerso(SurvId),
   CONSTRAINT fk_persotool_toolid FOREIGN KEY (ToolId)
   REFERENCES Tool(ToolId)
);

CREATE TABLE PendingSub (
	UserId		int(6),
	Token		VARCHAR(10),
	SubDate		Datetime,
	PRIMARY KEY (UserId, Token),
	CONSTRAINT fk_pendingsub_userid FOREIGN KEY (UserId)
	REFERENCES Users(UserId)
);

DROP EVENT IF EXISTS CleanPendingSub;
-- Cleans pending subscription older than 1 day, comes in addition of the PhP duplicate token gestion
CREATE EVENT CleanPendingSub
	ON SCHEDULE EVERY '1' DAY
	STARTS NOW()
DO
	DELETE FROM PendingSub WHERE TIMESTAMPDIFF(DAY, SubDate, NOW());
   DELETE FROM Users WHERE Users.UserId like UserId;
