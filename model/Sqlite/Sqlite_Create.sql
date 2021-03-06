/* ---------------------------------------------------- */
/*  Generated by Enterprise Architect Version 12.1 		*/
/*  Created On : 20-lis-2016 16:21:13 				*/
/*  DBMS       : SQLite 								*/
/* ---------------------------------------------------- */

/* Drop Tables */

DROP TABLE IF EXISTS 'Roles'
;

DROP TABLE IF EXISTS 'RolesAcl'
;

DROP TABLE IF EXISTS 'Users'
;

DROP TABLE IF EXISTS 'UsersActivity'
;

DROP TABLE IF EXISTS 'UsersAuthDrivers'
;

DROP TABLE IF EXISTS 'UsersRoles'
;

/* Create Tables with Primary and Foreign Keys, Check and Unique Constraints */

CREATE TABLE 'Roles'
(
	'Role' TEXT NOT NULL PRIMARY KEY,
	'RoleName' TEXT,
	'Description' TEXT,
	'Deleted' INTEGER NOT NULL DEFAULT 0
)
;

CREATE TABLE 'RolesAcl'
(
	'AclID' INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	'Role' TEXT NOT NULL,
	'Resource' TEXT NOT NULL,
	'Allow' TEXT,
	'Deny' TEXT,
	CONSTRAINT 'FK_RolesAcl_Roles' FOREIGN KEY ('Role') REFERENCES 'Roles' ('Role') ON DELETE Cascade ON UPDATE Cascade
)
;

CREATE TABLE 'Users'
(
	'UserID' INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	'FullName' TEXT,
	'UserName' TEXT NOT NULL,
	'PwHash' TEXT NOT NULL,
	'Email' TEXT NOT NULL,
	'Phone' TEXT,
	'Title' TEXT,
	'Language' TEXT,
	'Thumbnail' TEXT,
	'PwToken' TEXT,
	'PwTokenExpire' TEXT,
	'Created' TEXT,
	'LastLogged' TEXT,
	'Disabled' INTEGER NOT NULL DEFAULT 0,
	'Deleted' INTEGER NOT NULL DEFAULT 0
)
;

CREATE TABLE 'UsersActivity'
(
	'UserActivityID' INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
	'UserID' INTEGER NOT NULL,
	'IP' TEXT,
	'ActivityType' TEXT NOT NULL,
	'ActivityDateTime' TEXT NOT NULL,
	'Description' TEXT,
	CONSTRAINT 'FK_UsersActivity_Users' FOREIGN KEY ('UserID') REFERENCES 'Users' ('UserID') ON DELETE Cascade ON UPDATE Cascade
)
;

CREATE TABLE 'UsersAuthDrivers'
(
	'UserID' INTEGER NOT NULL,
	'AuthDriver' TEXT NOT NULL,
	'AuthID' TEXT NOT NULL,
	CONSTRAINT 'PK_UsersAuthDrivers' PRIMARY KEY ('UserID','AuthDriver'),
	CONSTRAINT 'FK_UsersAuthDrivers_Users' FOREIGN KEY ('UserID') REFERENCES 'Users' ('UserID') ON DELETE Cascade ON UPDATE Cascade
)
;

CREATE TABLE 'UsersRoles'
(
	'UserID' INTEGER NOT NULL,
	'Role' TEXT NOT NULL,
	CONSTRAINT 'PK_UsersRole' PRIMARY KEY ('UserID','Role'),
	CONSTRAINT 'FK_UsersRole_Users' FOREIGN KEY ('UserID') REFERENCES 'Users' ('UserID') ON DELETE Cascade ON UPDATE Cascade,
	CONSTRAINT 'FK_UsersRoles_Roles' FOREIGN KEY ('Role') REFERENCES 'Roles' ('Role') ON DELETE Cascade ON UPDATE Cascade
)
;

/* Create Indexes and Triggers */

CREATE INDEX 'IDX_Deleted'
 ON 'Roles' ('Deleted' ASC)
;

CREATE INDEX 'IXFK_RolesAcl_Roles'
 ON 'RolesAcl' ('Role' ASC)
;

CREATE INDEX 'IDX_Deleted'
 ON 'Users' ('Deleted' ASC)
;

CREATE INDEX 'IDX_UserName'
 ON 'Users' ('UserName' ASC)
;

CREATE INDEX 'IDX_PwToken'
 ON 'Users' ('PwToken' ASC)
;

CREATE INDEX 'IDX_Disabled'
 ON 'Users' ('Disabled' ASC)
;

CREATE INDEX 'IXFK_UsersActivity_Users'
 ON 'UsersActivity' ('UserID' ASC)
;

CREATE INDEX 'IDX_ActivityDateTime'
 ON 'UsersActivity' ('ActivityDateTime' ASC)
;

CREATE INDEX 'IDX_ActivityType'
 ON 'UsersActivity' ('ActivityType' ASC)
;

CREATE INDEX 'IDX_IP'
 ON 'UsersActivity' ('IP' ASC)
;

CREATE INDEX 'IXFK_UsersAuthDrivers_Users'
 ON 'UsersAuthDrivers' ('UserID' ASC)
;

CREATE INDEX 'IDX_AuthID'
 ON 'UsersAuthDrivers' ('AuthID' ASC)
;

CREATE INDEX 'IXFK_UsersRole_Users'
 ON 'UsersRoles' ('UserID' ASC)
;

CREATE INDEX 'IXFK_UsersRoles_Roles'
 ON 'UsersRoles' ('Role' ASC)
;
