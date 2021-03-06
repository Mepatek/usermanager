/* ---------------------------------------------------- */
/*  Generated by Enterprise Architect Version 12.1 		*/
/*  Created On : 20-lis-2016 16:18:46 				*/
/*  DBMS       : SQL Server 2012 						*/
/* ---------------------------------------------------- */

/* Drop Foreign Key Constraints */

IF EXISTS (SELECT 1 FROM dbo.sysobjects WHERE id = object_id(N'[FK_UsersActivity_Users]') AND OBJECTPROPERTY(id, N'IsForeignKey') = 1) 
ALTER TABLE [UsersActivity] DROP CONSTRAINT [FK_UsersActivity_Users]
GO

/* Drop Tables */

IF EXISTS (SELECT 1 FROM dbo.sysobjects WHERE id = object_id(N'[UsersActivity]') AND OBJECTPROPERTY(id, N'IsUserTable') = 1) 
DROP TABLE [UsersActivity]
GO

/* Create Tables */

CREATE TABLE [UsersActivity]
(
	[UserActivityID] int NOT NULL IDENTITY (1, 1),
	[UserID] int NOT NULL,
	[IP] varchar(50) NULL,
	[ActivityType] varchar(30) NOT NULL,
	[ActivityDateTime] datetime2 NOT NULL,
	[Description] text NULL
)
GO

/* Create Primary Keys, Indexes, Uniques, Checks */

ALTER TABLE [UsersActivity] 
 ADD CONSTRAINT [PK_UsersActivity]
	PRIMARY KEY CLUSTERED ([UserActivityID] ASC)
GO

CREATE NONCLUSTERED INDEX [IXFK_UsersActivity_Users] 
 ON [UsersActivity] ([UserID] ASC)
GO

CREATE NONCLUSTERED INDEX [IDX_ActivityDateTime] 
 ON [UsersActivity] ([ActivityDateTime] ASC)
GO

CREATE NONCLUSTERED INDEX [IDX_ActivityType] 
 ON [UsersActivity] ([ActivityType] ASC)
GO

CREATE NONCLUSTERED INDEX [IDX_IP] 
 ON [UsersActivity] ([IP] ASC)
GO

/* Create Foreign Key Constraints */

ALTER TABLE [UsersActivity] ADD CONSTRAINT [FK_UsersActivity_Users]
	FOREIGN KEY ([UserID]) REFERENCES [Users] ([UserID]) ON DELETE Cascade ON UPDATE Cascade
GO