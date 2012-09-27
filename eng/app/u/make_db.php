<?php 

/*
	
	CREATE DATABASE `RODIN` DEFAULT CHARACTER SET utf8 COLLATE latin2_general_ci;
	
	CREATE TABLE `RODIN`.`SEARCH` (
	`sid` VARCHAR( 80 ) NOT NULL ,
	`Query` TEXT NOT NULL
	) ENGINE = MYISAM CHARACTER SET utf8 COLLATE latin2_general_ci COMMENT = 'Search startet by the 	
	RODIN User' 
 	ALTER TABLE `search` ADD PRIMARY KEY ( `sid` )  
	ALTER TABLE `search` ADD INDEX ( `sid` )  
	 
	 CREATE TABLE `RODIN`.`result` (
	`sid` VARCHAR( 40 ) NOT NULL ,
	`sid` VARCHAR( 40 ) NOT NULL ,
	`newsid` VARCHAR( 40 ) NOT NULL ,
	`datasource` VARCHAR( 80 ) NOT NULL ,
	`xpointer` TEXT NULL ,
	`node` TEXT NOT NULL COMMENT 'Nodename - like xml' AFTER `xpointer` ,
    `follow` VARCHAR( 2 ) NOT NULL DEFAULT 'cr' COMMENT 'Directive for pretty printing (cr,bl)'
	`attribute` VARCHAR( 80 ) NOT NULL ,
	`type` VARCHAR( 80 ) NOT NULL ,
	`value` TEXT NOT NULL
	`url` TEXT NOT NULL ,
	`visible` BOOL NOT NULL ;
	) ENGINE = MYISAM COMMENT = 'All result of a datasource sorted by seq are stored up to a search
	 id sid' 
 	ALTER TABLE `result` ADD INDEX ( `sid` )  

CREATE TABLE `rodin`.`widgets_constellation` (
`sid` VARCHAR( 80 ) NOT NULL ,
`block` INT NOT NULL COMMENT 'Each Block a tab-representation',
`datasource` VARCHAR( 80 ) NOT NULL ,
PRIMARY KEY ( `sid` )
) ENGINE = MYISAM COMMENT = 'Stores Widgets Constellations to be used to combine results';


CREATE USER 'RODIN_TUSER'@'%' IDENTIFIED BY '**********';

GRANT ALL PRIVILEGES ON * . * TO 'RODIN_TUSER'@'%' IDENTIFIED BY '**********' WITH GRANT OPTION MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;

GRANT ALL PRIVILEGES ON `RODIN_TUSER\_%` . * TO 'RODIN_TUSER'@'%'


CREATE TABLE `rodinmac_st`.`userRDWprefs` (
`prefsuser` VARCHAR( 50 ) NOT NULL COMMENT 'Der eingeloggte User (web remoteuser)',
`datasource` VARCHAR( 80 ) NOT NULL COMMENT 'URL des RDW',
`application_id` VARCHAR( 40 ) NOT NULL COMMENT 'ID des RDW (bei Merfaheinsatz)',
`queryprefs` VARCHAR( 2048 ) NOT NULL COMMENT 'Querystring-Segment verwendet als Suchfilter'
) ENGINE = MYISAM CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `rodinmac_st``userRDWprefs` ADD PRIMARY KEY ( `prefsuser` , `datasource` , `application_id` ) ;


CREATE TABLE `rodinmac_eng`.`R_CALL` (
`cid` VARCHAR( 120 ) NOT NULL COMMENT 'Call ID',
`from` VARCHAR( 30 ) NOT NULL COMMENT 'Who is calling',
`to` VARCHAR( 120 ) NOT NULL COMMENT 'Who is called (and shoud answer)',
`call_timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`input` TEXT NOT NULL COMMENT 'Input of caller to called',
`output` TEXT NOT NULL COMMENT 'answered output from called to caller',
`answer_timestamp` TIMESTAMP NOT NULL ,
PRIMARY KEY ( `cid` , `from` , `to` , `call_timestamp` )
) ENGINE = MYISAM COMMENT = 'CALL LOGGING TABLE';
ALTER TABLE `R_CALL` ADD INDEX ( `from` , `to` ) ;
ALTER TABLE `R_CALL` ADD INDEX ( `cid` ) 



CREATE TABLE `rodinmac_eng`.`src_interface` (
`Name` VARCHAR( 30 ) NOT NULL COMMENT 'The name of the interface',
`Server` VARCHAR( 100 ) NOT NULL COMMENT 'Der Server des Interface WITHOUT path',
`Port` INT NOT NULL COMMENT 'Port of server',
`AuthUser` VARCHAR( 20 ) NOT NULL COMMENT 'User needed to access the SRC interface'
`AuthPasswd` VARCHAR( 20 ) NOT NULL COMMENT 'Password needed to access the SRC interface'
`Path` VARCHAR( 512 ) NOT NULL COMMENT 'Path of Interface from server',
`Start` VARCHAR( 128 ) NOT NULL COMMENT 'Name of the Start servlet or program',
`Refine` VARCHAR( 128 ) NOT NULL COMMENT 'Name of the Refine servlet or program',
`Activated` BOOL NOT NULL COMMENT 'Flag to use this record as an SRC interface',
`Created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ,
`Modified` TIMESTAMP NOT NULL ,
PRIMARY KEY ( `Name` )
) ENGINE = MYISAM COMMENT = 'Each record contains an SRC interface definition';


*/
?>