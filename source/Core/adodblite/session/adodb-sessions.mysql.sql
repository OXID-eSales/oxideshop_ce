-- $CVSHeader$

CREATE DATABASE /*! IF NOT EXISTS */ adodb_sessions;

DROP TABLE /*! IF EXISTS */ oxsessions;

CREATE TABLE /*! IF NOT EXISTS */ oxsessions (
	ID INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
	SessionID VARCHAR(64), 
	session_data MEDIUMTEXT DEFAULT '', 
	expiry INT(11),
	expireref	VARCHAR(250)	DEFAULT '',
	INDEX (SessionID),
	INDEX expiry (expiry)
);
