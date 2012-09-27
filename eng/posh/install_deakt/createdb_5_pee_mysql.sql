
>1.2.0;

CREATE TABLE `users_messages` (`id` int(10) unsigned NOT NULL auto_increment,`user_id` mediumint(8) unsigned NOT NULL default '0',`msg` text character set utf8 collate utf8_unicode_ci NOT NULL,PRIMARY KEY  (`id`),KEY `user_id` (`user_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
UPDATE adm_config SET value='false' WHERE parameter='allowredactor';
CREATE TABLE `redactor_feeds` (`id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT ,`title` VARCHAR( 100 ) NOT NULL ,`description` TEXT NOT NULL ,`createdby` MEDIUMINT UNSIGNED NOT NULL ,`creationdate` DATE NOT NULL ,PRIMARY KEY ( `id` ) );
CREATE TABLE `redactor_map_feeds` (`user_id` MEDIUMINT UNSIGNED NOT NULL ,`feed_id` SMALLINT UNSIGNED NOT NULL ,`admin` TINYINT( 1 ) UNSIGNED NOT NULL );
CREATE TABLE `redactors` (`user_id` MEDIUMINT UNSIGNED NOT NULL ,`creator` TINYINT( 1 ) UNSIGNED NOT NULL );
UPDATE adm_config SET value='{"fct":"openHelp()","label":lg("lblHelp"),"img":"-","comment":lg("lblFirstUsage"),"anonymous":true,"connected":false},{"fct":"p_addContent.menu()","label":lg("lblAddContent"),"img":"ico_menu_add.gif","comment":lg("lblAddContent2"),"anonymous":true,"connected":true},{"fct":"p_edit.menu()","label":lg("lblPersonalize"),"img":"ico_menu_tools.gif","comment":lg("lblPersonalize"),"anonymous":true,"connected":true},{"fct":"showSave()","label":"<B>"+lg("lblSave")+"</B>","img":"ico_menu_disk.gif","comment":lg("lblSavePage"),"anonymous":true,"connected":false}' WHERE parameter="headlinks";
UPDATE `adm_config` SET `parameter` = 'useArchive', `value` = 'true', `datatype` = 'int', `desttype` = 'J' WHERE  CONVERT(`parameter` USING utf8) = 'useArchive';
UPDATE `adm_config` SET `parameter` = 'useSharing', `value` = 'true', `datatype` = 'int', `desttype` = 'J' WHERE  CONVERT(`parameter` USING utf8) = 'useSharing';
UPDATE `adm_config` SET `parameter` = 'useContact', `value` = 'true', `datatype` = 'int', `desttype` = 'J' WHERE  CONVERT(`parameter` USING utf8) = 'useContact';
UPDATE `adm_config` SET `parameter` = 'ARCHIVE', `value` = 'true', `datatype` = 'int', `desttype` = 'P' WHERE  CONVERT(`parameter` USING utf8) = 'ARCHIVE';
ALTER TABLE `portals` ADD `md5check` VARCHAR( 32 ) NOT NULL ;
ALTER TABLE `redactor_feeds` ADD `md5url` VARCHAR( 42 ) NOT NULL ;
CREATE TABLE `redactor_map_item_feed` (`item_url` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`feed_id` SMALLINT UNSIGNED NOT NULL );
CREATE TABLE `redactor_articles` (`id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,`title` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`chapo` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`text` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`status` CHAR( 1 ) DEFAULT 'O' NOT NULL ,`redactor_id` INT UNSIGNED NOT NULL ,`pubdate` DATE NOT NULL ,`creator_id` INT UNSIGNED NOT NULL ,`creationdate` DATE NOT NULL ,PRIMARY KEY ( `id` ) );
CREATE TABLE `redactor_map_article_feed` (`feed_id` SMALLINT UNSIGNED NOT NULL ,`article_id` MEDIUMINT UNSIGNED NOT NULL );


>1.2.1;

UPDATE adm_config SET value='false' WHERE parameter='allowredactor';
ALTER TABLE `redactor_map_item_feed` ADD `item_id` MEDIUMINT UNSIGNED NOT NULL FIRST ;
UPDATE redactor_map_item_feed, dir_item SET item_id=id WHERE item_url=url;

>1.3.0;

ALTER TABLE `redactor_feeds` ADD `url` VARCHAR( 250 ) NOT NULL AFTER `description` ;

>1.3.1;

ALTER TABLE redactor_articles ADD url VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER chapo;
ALTER TABLE `redactor_articles` CHANGE `url` `url` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;
INSERT INTO `adm_tabs_fct` ( `tabname` , `fctname` , `label` , `link` , `description` ) VALUES ('pagestab', 'userportals', 'exampleMgmt', 'admin/pages_portals.php', 'exampleMgmtTxt');

UPDATE adm_config SET value = REPLACE (value,'{"fct":"p_addContent.menu()","label":lg("lblAddContent"),"img":"ico_menu_add.gif","comment":lg("lblAddContent2"),"anonymous":true,"connected":true},','') WHERE parameter = 'headlinks';
UPDATE adm_config SET value = REPLACE (value,'{"fct":"p_edit.menu()","label":lg("lblPersonalize"),"img":"ico_menu_tools.gif","comment":lg("lblPersonalize"),"anonymous":true,"connected":true},','') WHERE parameter = 'headlinks';

>1.4.0;

ALTER TABLE `users_messages` CHANGE `msg` `title` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `users_messages` ADD `description` TEXT NOT NULL ,ADD `status` CHAR( 1 ) DEFAULT 'U' NOT NULL ,ADD `senddate` DATE NOT NULL ,ADD `sender_id` MEDIUMINT UNSIGNED NOT NULL ,ADD `linked_message` INT UNSIGNED DEFAULT '0' NOT NULL ,ADD `folder_id` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL ;
DELETE FROM adm_headlinks;
INSERT INTO `adm_headlinks` VALUES (1, 'lblHelp', 'lblFirstUsage', '', '', 'openHelp()', 'O', 0, 1, 0, 0);
INSERT INTO `adm_headlinks` VALUES (2, 'lblAddContent', 'lblAddContent2', 'b', 'ico_menu_add.gif', 'p_addContent.menu()', 'O', 0, 1, 1, 0);
INSERT INTO `adm_headlinks` VALUES (3, 'myportaneo', '', '', '', '', 'O', 0, 0, 1, 0);
INSERT INTO `adm_headlinks` VALUES (3, 'home', '', '', 'ico_home2.gif', 'p_app.openHome()', 'O', 1, 0, 1, 0);
INSERT INTO `adm_headlinks` VALUES (3, 'myNetwork', '', '', 'mynetwork.gif', '$p.network.dashboard.load()', 'O', 2, 0, 1, 0);
INSERT INTO `adm_headlinks` VALUES (3, 'lblArchive', '', '', 'myinfo.gif', 'goArchive()', 'O', 3, 0, 1, 0);
INSERT INTO `adm_headlinks` VALUES (3, 'lblArchive2', '', '', 'mymodules.gif', 'goIndex()', 'O', 4, 0, 1, 0);
INSERT INTO `adm_headlinks` VALUES (4, 'search', 'searchInPortaneo', '', 'ico_menu_search.gif', '$p.search.init()', 'O', 0, 0, 1, 0);
INSERT INTO `adm_headlinks` VALUES (5, 'lblPersonalize', 'optionsOfThisPage', '', '', 'p_edit.menu()', 'O', 0, 1, 1, 0);
INSERT INTO `adm_headlinks` VALUES (6, 'lblSave', 'lblSavePage', 'b', 'ico_menu_disk.gif', 'showSave()', 'O', 0, 1, 0, 0);
INSERT INTO `adm_headlinks` VALUES (7, 'lblConnect', 'lblConnect', 'b', '', 'return connectBox()', 'O', 0, 1, 0, 0);
INSERT INTO `adm_headlinks` VALUES (8, 'lblDisconnect', 'lblDisconnect', '', '', 'logout()', 'O', 0, 0, 1, 1);
ALTER TABLE `users_friends` ADD `friend_id` MEDIUMINT UNSIGNED NOT NULL ;
CREATE TABLE `network` (`user_id` MEDIUMINT UNSIGNED NOT NULL ,`friend_id` MEDIUMINT UNSIGNED NOT NULL ,`description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL );
ALTER TABLE `network` ADD UNIQUE `uniq_network` ( `user_id` , `friend_id` );
CREATE TABLE `network_keywords` (`user_id` MEDIUMINT UNSIGNED NOT NULL ,`friend_id` MEDIUMINT UNSIGNED NOT NULL ,`kw_id` MEDIUMINT UNSIGNED NOT NULL );
ALTER TABLE `users_articles` ADD `source` VARCHAR( 100 ) NOT NULL ,ADD `icon` VARCHAR( 40 ) NOT NULL ,ADD `pubdate` DATE NOT NULL ,ADD `classified` TINYINT( 1 ) UNSIGNED NOT NULL ,ADD `description` TEXT NOT NULL, ADD `private` TINYINT( 1 ) UNSIGNED NOT NULL ;
ALTER TABLE `users_articles` CHANGE `link` `link` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
CREATE TABLE `users_articles_keywords` (`user_id` MEDIUMINT UNSIGNED NOT NULL ,`article_id` INT UNSIGNED NOT NULL ,`kw_id` MEDIUMINT UNSIGNED NOT NULL );
update `adm_config` set value='true' WHERE parameter='enterprise';
CREATE TABLE `notebook_article` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,`title` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`pubdate` DATETIME NOT NULL ,`icon` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`commentsnb` SMALLINT UNSIGNED NOT NULL ,`status` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'O' NOT NULL ,`type` TINYINT UNSIGNED NOT NULL ,`linked_id` INT UNSIGNED NOT NULL ,PRIMARY KEY ( `id` ) );
ALTER TABLE `notebook_article` ADD `trackbacknb` SMALLINT UNSIGNED NOT NULL ;
CREATE TABLE `notebook_article_users` (`user_id` MEDIUMINT UNSIGNED NOT NULL ,`article_id` INT UNSIGNED NOT NULL ,`owner_id` MEDIUMINT UNSIGNED NOT NULL );
CREATE TABLE `portals_keywords` (`portal_id` MEDIUMINT UNSIGNED NOT NULL ,`kw_id` MEDIUMINT UNSIGNED NOT NULL );
ALTER TABLE `portals_keywords` ADD UNIQUE `uniq_portals_keywords` ( `portal_id` , `kw_id` );
UPDATE `adm_config` SET `value` = 'false' WHERE `parameter` = 'USEMAGIC';
CREATE TABLE `dir_item_shared` (`chk` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`item_id` MEDIUMINT UNSIGNED NOT NULL ,`vars` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,PRIMARY KEY ( `chk` ) );
UPDATE `adm_config` SET `parameter` = 'option', `value` = '{"fct":"$p.friends.menu(4,jsprof)","label":lg("lblShare"),"img":"ico_menu_share.gif","comment":lg("lblShare"),"anonymous":false,"connected":true}', `datatype` = 'arr', `desttype` = 'J' WHERE  CONVERT(`parameter` USING utf8) = 'option';
CREATE TABLE `notebook_comments` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,`article_id` INT UNSIGNED NOT NULL ,`user_id` MEDIUMINT UNSIGNED NOT NULL ,`message` TEXT NOT NULL ,`pubdate` DATETIME NOT NULL ,PRIMARY KEY ( `id` ) );
ALTER TABLE `notebook_article_users` ADD UNIQUE `unique_articleforuser` ( `user_id` , `article_id` );
UPDATE `adm_plugins` SET name='Enterprise main tools',link='enterprise_edition/enterprise_edition.php',dependency='no',status='O' WHERE name='Enterprise tools';

> 1.4.1;

INSERT INTO `adm_headlinks` ( `id` , `label` , `comment` , `clss` , `images` , `fct` , `status` , `seq` , `anonymous` , `connected` , `admin` ) VALUES ('3', 'myNotebook', '', '', 'ico_notebook.gif', '$p.notebook.open()', 'O', '4', '0', '1', '0');
ALTER TABLE `users_articles` CHANGE `link` `link` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;
ALTER TABLE `notebook_article_users` ADD `pubdate` DATETIME NOT NULL ;
ALTER TABLE `notebook_article_users` ADD `take_on` MEDIUMINT UNSIGNED NOT NULL AFTER `owner_id` ;
ALTER TABLE `users_articles` ADD `feedarticle_id` INT UNSIGNED NOT NULL AFTER `pubdate` ;
ALTER TABLE `notebook_article` ADD `feedarticle_id` INT UNSIGNED NOT NULL AFTER `icon` ;
UPDATE `adm_config` SET `parameter` = 'defaultmode', `value` = 'connected', `datatype` = 'str', `desttype` = 'P' WHERE  CONVERT(`parameter` USING utf8) = 'defaultmode';
ALTER TABLE `notebook_comments` CHANGE `message` `message` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;
ALTER TABLE `redactor_articles` CHANGE `status` `status` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT 'O';
ALTER TABLE `redactor_feeds` CHANGE `title` `title` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,CHANGE `url` `url` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,CHANGE `md5url` `md5url` VARCHAR( 42 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;
ALTER TABLE `users_calendar` CHANGE `comments` `comments` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,CHANGE `time` `time` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,CHANGE `ampm` `ampm` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT 'A',CHANGE `endtime` `endtime` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;
ALTER TABLE `users_favorites_id` CHANGE `status` `status` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL ;
ALTER TABLE `users_tasks_id` CHANGE `status` `status` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL ;

> 1.4.2;

ALTER TABLE `dir_item_shared` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `network` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `network_keywords` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `notebook_article` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `notebook_article_users` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `notebook_comments` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `portals_keywords` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `redactor_articles` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `redactor_feeds` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `redactor_map_article_feed` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `redactor_map_article_feed` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `redactor_map_feeds` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `redactor_map_item_feed` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `redactors` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `users_articles_keywords` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;

UPDATE adm_config SET value='{"col":1,"row":1,"fct":"$p.network.information.summary()","anonymous":false,"connected":true},{"col":2,"row":1,"fct":"p_pages.summary()","anonymous":false,"connected":true}' WHERE parameter='homeDivs';
CREATE TABLE `network_news` (`user_id` MEDIUMINT UNSIGNED NOT NULL ,`pubdate` DATETIME NOT NULL ,`type` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`title` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`link` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ) CHARACTER SET utf8 COLLATE utf8_unicode_ci;
INSERT INTO `adm_config` VALUES ('PEEVERSION', '2.1.2', 'str', 'A');
UPDATE `adm_config` SET `value` = 'Portaneo Enterprise Edition' WHERE CONVERT( `parameter` USING utf8 ) = 'application' LIMIT 1 ;
ALTER TABLE `network_news` ADD `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;

> 1.4.3;

UPDATE adm_config SET value='' WHERE parameter='footer';
UPDATE adm_config SET value='0' WHERE parameter='showHomeBar';
UPDATE `adm_config` SET  value='2.1.3' WHERE parameter='PEEVERSION';

> 1.4.4;

CREATE TABLE `notebook_article_keywords` (`article_id` INT UNSIGNED NOT NULL ,`kw_id` MEDIUMINT UNSIGNED NOT NULL );
ALTER TABLE `notebook_article` ADD `keywords` VARCHAR( 250 ) NOT NULL AFTER `description` ;
UPDATE notebook_article SET status='3';
ALTER TABLE `notebook_comments` ADD `status` CHAR( 1 ) DEFAULT 'O' NOT NULL ;
ALTER TABLE `users_articles` CHANGE `private` `private` TINYINT( 1 ) UNSIGNED DEFAULT '3' NOT NULL;
UPDATE users_articles SET private =3 WHERE private =0;
ALTER TABLE `network_news` ADD `status` VARCHAR( 1 ) DEFAULT '3' NOT NULL ;
INSERT INTO notebook_article (title,description,pubdate,icon,feedarticle_id,commentsnb,status,type,linked_id,trackbacknb) SELECT title,CONCAT(description,"<br /><br /><div class=notebooklink><a href='",link,"' target='_blank'><img src='","../modules/pictures/",icon,"' align='absmiddle' /> ",title,"</a></div>"),pubdate,icon,feedarticle_id,0,1,10,id,0 FROM users_articles WHERE classified=1 AND private=1;
UPDATE users_articles SET private=7 WHERE classified=1 AND private=1;
INSERT INTO notebook_article_keywords (article_id,kw_id) SELECT a.id,kw_id FROM notebook_article AS a,users_articles_keywords AS c WHERE a.linked_id=c.article_id AND a.type=10;
INSERT INTO notebook_article_users (user_id,article_id,owner_id,take_on,pubdate) SELECT c.user_id,a.id,c.user_id,c.user_id,c.pubdate FROM notebook_article AS a,users_articles AS c WHERE a.linked_id=c.id AND a.type=10;
UPDATE notebook_article SET linked_id=feedarticle_id,type=2 WHERE type=10;
UPDATE adm_config SET value='{"col":1,"row":1,"fct":"$p.network.information.summary","anonymous":false,"connected":true},{"col":2,"row":1,"fct":"$p.network.profile.summary","anonymous":false,"connected":true},{"col":2,"row":2,"fct":"$p.article.summary","anonymous":false,"connected":true},{"col":2,"row":3,"fct":"$p.notebook.summary","anonymous":false,"connected":true},{"col":2,"row":4,"fct":"p_pages.summary","anonymous":false,"connected":true}' WHERE parameter='homeDivs';

> 1.5.4;

ALTER TABLE `dir_item_shared` CHANGE `vars` `vars` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `network` CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `notebook_article` CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `keywords` `keywords` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `icon` `icon` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci ;

> 2.0.0;

ALTER TABLE `portals` ADD `type` CHAR( 1 ) DEFAULT '1' NOT NULL ,ADD `param` VARCHAR( 150 ) NOT NULL ;
CREATE TABLE notebook_groups(id int not null primary key auto_increment,name varchar(100) ,created_by int(10) unsigned,creation_date date,private tinyint(1),status char(1)  default '1');
CREATE TABLE notebook_groups_users_map(user_id int(10) unsigned,group_id int(10) unsigned,status char(1)  default 'I');
CREATE TABLE notebook_groups_articles_map(group_id int(10) unsigned,article_id int(10) unsigned,status char(1) default 'O', owner_id int(10) unsigned, user_id int(10) unsigned, is_copy int(10) unsigned);
DELETE FROM adm_headlinks WHERE label IN ('myportaneo','home','myNetwork','lblArchive','search','myNotebook');
UPDATE adm_headlinks SET seq=seq+2 WHERE seq>6;
INSERT INTO adm_headlinks (id,uniq_id,type,label,comment,clss,images,fct,status,seq,anonymous,connected,admin) VALUES (12,'link_home','link','home','home','','','p_app.openHome()','O',7,0,1,0);
INSERT INTO adm_headlinks (id,uniq_id,type,label,comment,clss,images,fct,status,seq,anonymous,connected,admin) VALUES (13,'lab_sep5','label','|','','','','','O',8,0,1,0);
UPDATE adm_headlinks SET seq=seq+4 WHERE seq>10;
INSERT INTO adm_headlinks (id,uniq_id,type,label,comment,clss,images,fct,status,seq,anonymous,connected,admin) VALUES (14,'link_notebook','link','myNotebook','myNotebook','','ico_notebook.gif','$p.notebook.open()','O',11,0,1,0);
INSERT INTO adm_headlinks (id,uniq_id,type,label,comment,clss,images,fct,status,seq,anonymous,connected,admin) VALUES (15,'lab_sep6','label','|','','','','','O',12,0,1,0);
INSERT INTO adm_headlinks (id,uniq_id,type,label,comment,clss,images,fct,status,seq,anonymous,connected,admin) VALUES (16,'form_search','form','','search','','ico_search.gif','$p.search.shown=false;$p.search.start(indef,this.text.value);','O',13,0,1,0);
UPDATE `adm_headlinks` SET `connected` = '1' WHERE `id` =8 AND CONVERT( `label` USING utf8 ) = '|' AND `anonymous` =1 AND `connected` =0 AND `admin` =0 LIMIT 1 ;
UPDATE adm_config SET value='3' WHERE parameter='loadlatestpageonstart';
UPDATE adm_config SET value='true' WHERE parameter='useChat';
CREATE TABLE `network_chat` (`id` int(10) unsigned NOT NULL auto_increment,`owner_id` int(10) unsigned NOT NULL default '0',`callee_id` int(10) unsigned NOT NULL default '0',`status` char(1) collate utf8_unicode_ci NOT NULL default 'N',`title` varchar(32) collate utf8_unicode_ci default NULL,`pubdate` date NOT NULL default '0000-00-00',PRIMARY KEY  (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `users_notification` (`user_id` INT UNSIGNED NOT NULL ,`notification_id` INT UNSIGNED NOT NULL ,`type` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'X' NOT NULL ,INDEX ( `user_id` ) ) CHARACTER SET utf8 COLLATE utf8_unicode_ci;
CREATE TABLE `network_chat_message` (`id` int(10) unsigned NOT NULL auto_increment,`chat_id` int(10) unsigned NOT NULL default '0',`send_id` int(10) unsigned NOT NULL default '0',`dest_id` int(10) unsigned NOT NULL default '0',`message` text collate utf8_unicode_ci,`status` char(1) collate utf8_unicode_ci NOT NULL default 'N',PRIMARY KEY  (`id`),KEY `index_chatid` (`chat_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
UPDATE adm_config SET value='{"col":1,"row":1,"fct":"$p.network.information.summary","anonymous":false,"connected":true},{"col":2,"row":1,"fct":"$p.app.pages.summary","anonymous":false,"connected":true},{"col":2,"row":2,"fct":"$p.notebook.summary","anonymous":false,"connected":true}' WHERE parameter='homeDivs';

> 2.0.3;

UPDATE adm_headlinks SET images='' WHERE uniq_id = 'link_notebook';

> 2.1.0a1;

INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` , `category` ) VALUES ('networkIsPublic', 'true', 'int', 'A', 'collaboration');
UPDATE adm_headlinks SET fct = '$p.app.openHome()',images='' WHERE uniq_id='link_home';
UPDATE adm_headlinks SET fct = '$p.notebook.open()' WHERE uniq_id='link_notebook';
UPDATE adm_headlinks SET fct = '$p.search.shown=false;$p.search.start(indef,this.text.value);' WHERE uniq_id='form_search';
UPDATE `adm_config` SET `parameter` = 'option', `value` = '{"fct":"$p.friends.menu(4,tab[$p.app.tabs.sel].id)","label":lg("lblShare"),"img":"ico_menu_share.gif","comment":lg("lblShare"),"anonymous":false,"connected":true}', `datatype` = 'arr', `desttype` = 'J' WHERE  CONVERT(`parameter` USING utf8) = 'option';
UPDATE adm_config SET value='{"col":1,"row":1,"fct":"$p.network.information.summary","anonymous":false,"connected":true},{"col":2,"row":1,"fct":"$p.app.pages.summary","anonymous":false,"connected":true},{"col":2,"row":2,"fct":"$p.notebook.summary","anonymous":false,"connected":true}' WHERE parameter='homeDivs';
ALTER TABLE `notebook_groups_users_map` ADD INDEX `ind_notebookgroupsusermap` ( `user_id` ,`group_id` );
UPDATE adm_config SET value='true' WHERE parameter='useNetwork';
UPDATE adm_config SET value='true' WHERE parameter='useNotebook';
UPDATE adm_headlinks SET position='left' WHERE uniq_id='link_home';
UPDATE adm_headlinks SET position='left' WHERE uniq_id='lab_sep5';
UPDATE adm_headlinks SET position='left' WHERE uniq_id='link_notebook';
DELETE FROM adm_headlinks WHERE uniq_id='lab_sep6';
INSERT INTO `adm_headlinks` (`id`, `uniq_id`, `type`, `label`, `comment`, `clss`, `images`, `fct`, `status`, `seq`, `anonymous`, `connected`, `admin`, `position`) VALUES (17, 'lab_sep6', 'label', '|', '', '', '', '', 'O', 15, 0, 1, 0, 'left');
INSERT INTO `adm_headlinks` ( `id` , `uniq_id` , `type` , `label` , `comment` , `clss` , `images` , `fct` , `status` , `seq` , `anonymous` , `connected` , `admin` , `position` ) VALUES ('0', 'link_network', 'link', 'myNetwork', 'myNetwork', '', '', '$p.network.dashboard.myNetwork()', 'O', '15', '0', '1', '0', 'left'), ('0', 'lab_sep13', 'label', '|', '', '', '', '', 'O', '16', '0', '1', '0', 'left');
INSERT INTO `adm_headlinks` ( `id` , `uniq_id` , `type` , `label` , `comment` , `clss` , `images` , `fct` , `status` , `seq` , `anonymous` , `connected` , `admin` , `position` ) VALUES ('0', 'link_group', 'link', 'myGroups', 'myGroups', '', '', '$p.group.buildPage()', 'O', '20', '0', '1', '0', 'left');
CREATE TABLE `documents` (`id` INT NOT NULL AUTO_INCREMENT ,`title` VARCHAR( 255 ) NOT NULL ,`link` VARCHAR( 255 ) NOT NULL ,`version` SMALLINT NOT NULL ,`creation_date` DATE NOT NULL ,`modif_date` DATE NOT NULL ,`size` INT NOT NULL ,PRIMARY KEY ( `id` ) );
CREATE TABLE `notebook_article_document_map` (`article_id` INT NOT NULL ,`document_id` INT NOT NULL ,INDEX ( `article_id` ) );
ALTER TABLE `notebook_groups` ADD `picture` VARCHAR( 255 ) NOT NULL AFTER `name` ;
ALTER TABLE `notebook_groups` ADD `description` TEXT NOT NULL AFTER `private` ;
INSERT INTO adm_userinfo (label,type,options,mandatory,editable,public) VALUES ('Job',1,'',0,1,1),('Country',2,'China;France;Germany;Italy;Japan;Marroco;Spain;UK;US',1,1,1);

> 2.1.0rc;

UPDATE adm_config SET value='{"col":1,"row":1,"fct":"$p.network.information.summary","anonymous":false,"connected":true},{"col":2,"row":1,"fct":"$p.network.alert.summary","anonymous":false,"connected":true},{"col":2,"row":2,"fct":"$p.app.pages.summary","anonymous":false,"connected":true}' WHERE parameter='homeDivs';
CREATE TABLE `network_alerts` (`id` INT NOT NULL auto_increment,`user_id` INT NOT NULL ,`type` TINYINT NOT NULL ,`referer_id` INT NOT NULL , `referer_name` VARCHAR(100) collate utf8_unicode_ci NOT NULL default '' ,PRIMARY KEY ( `id` ) );

> 2.1.1

UPDATE adm_config SET value='{"col":1,"row":1,"fct":"$p.network.information.summary","anonymous":false,"connected":true},{"col":2,"row":1,"fct":"$p.network.alert.summary","anonymous":false,"connected":true},{"col":2,"row":2,"fct":"$p.app.pages.summary","anonymous":false,"connected":true}' WHERE parameter='homeDivs';
