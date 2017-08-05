DROP INDEX IF EXISTS [IDX_ActivityDateTime] 
 ON [UsersActivity]; 

ALTER TABLE [UsersActivity]
ALTER COLUMN [ActivityDateTime] DateTime2;

CREATE NONCLUSTERED INDEX [IDX_ActivityDateTime] 
 ON [UsersActivity] ([ActivityDateTime] ASC)

ALTER TABLE [Users]
ALTER COLUMN [LastLogged] DateTime2;

ALTER TABLE [Users]
ALTER COLUMN [Created] DateTime2;

ALTER TABLE [Users]
ALTER COLUMN [PwTokenExpire] DateTime2;

