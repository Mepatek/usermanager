/* ---------------------------------------------------- */
/*  Generated by Enterprise Architect Version 12.1 		*/
/*  Created On : 20-lis-2016 16:17:38 				*/
/*  DBMS       : MySql 						*/
/* ---------------------------------------------------- */

SET FOREIGN_KEY_CHECKS=0 
;

/* Drop Tables */

DROP TABLE IF EXISTS `UsersActivity` CASCADE
;

/* Create Tables */

CREATE TABLE `UsersActivity`
(
	`UserActivityID` INTEGER NOT NULL AUTO_INCREMENT,
	`UserID` INTEGER NOT NULL,
	`IP` VARCHAR(50) 	 NULL,
	`ActivityType` VARCHAR(30) NOT NULL,
	`ActivityDateTime` DATETIME NOT NULL,
	`Description` TEXT 	 NULL,
	CONSTRAINT `PK_UsersActivity` PRIMARY KEY (`UserActivityID` ASC)
)

;

/* Create Primary Keys, Indexes, Uniques, Checks */

ALTER TABLE `UsersActivity` 
 ADD INDEX `IXFK_UsersActivity_Users` (`UserID` ASC)
;

ALTER TABLE `UsersActivity` 
 ADD INDEX `IDX_ActivityDateTime` (`ActivityDateTime` ASC)
;

ALTER TABLE `UsersActivity` 
 ADD INDEX `IDX_ActivityType` (`ActivityType` ASC)
;

ALTER TABLE `UsersActivity` 
 ADD INDEX `IDX_IP` (`IP` ASC)
;

/* Create Foreign Key Constraints */

ALTER TABLE `UsersActivity` 
 ADD CONSTRAINT `FK_UsersActivity_Users`
	FOREIGN KEY (`UserID`) REFERENCES `Users` (`UserID`) ON DELETE Cascade ON UPDATE Cascade
;

SET FOREIGN_KEY_CHECKS=1 
;