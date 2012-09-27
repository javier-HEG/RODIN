>1.0.0;

DROP TABLE IF EXISTS `adm_config`;
CREATE TABLE `adm_config` (
  `parameter` varchar(16) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `value` text character set utf8 collate utf8_unicode_ci NOT NULL,
  `datatype` char(3) NOT NULL default '',
  `desttype` char(1) NOT NULL default '',
  PRIMARY KEY  (`parameter`)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COMMENT='application configuration parameters';
INSERT INTO `adm_config` VALUES ('theme', 'default', 'str', 'J');
INSERT INTO `adm_config` VALUES ('themeNb', '1', 'int', 'J');
INSERT INTO `adm_config` VALUES ('maxModNb', '15', 'int', 'J');
INSERT INTO `adm_config` VALUES ('footer', 'powered by portaneo', 'str', 'J');
INSERT INTO `adm_config` VALUES ('USERMODULE', 'I', 'str', 'P');
INSERT INTO `adm_config` VALUES ('userModuleJs', 'I', 'str', 'J');
INSERT INTO `adm_config` VALUES ('dimension', '{"seq":"0","name":"Widgets -fr","id":2,"lg":"fr"},{"seq":"0","name":"Widget -en","id":3,"lg":"en"}', 'arr', 'J');
INSERT INTO `adm_config` VALUES ('option', '{"fct":"p_addContent.menu()","label":lg("lblAddContent"),"img":"ico_menu_add.gif","comment":lg("lblAddContent2"),"anonymous":true,"connected":true},{"fct":"p_edit.menu()","label":lg("lblPersonalize"),"img":"ico_menu_tools.gif","comment":lg("lblPersonalize"),"anonymous":true,"connected":true},{"fct":"showSave()","label":"<B>"+lg("lblSave")+"</B>","img":"ico_menu_disk.gif","comment":lg("lblSavePage"),"anonymous":true,"connected":false}', 'arr', 'J');
INSERT INTO `adm_config` VALUES ('apname', '', 'str', 'J');
INSERT INTO `adm_config` VALUES ('useArchive', 'false', 'int', 'J');
INSERT INTO `adm_config` VALUES ('rssinfo', '', 'str', 'J');
INSERT INTO `adm_config` VALUES ('useGroup', 'false', 'int', 'J');
INSERT INTO `adm_config` VALUES ('useGSearch', 'false', 'int', 'J');
INSERT INTO `adm_config` VALUES ('useList', 'false', 'int', 'J');
INSERT INTO `adm_config` VALUES ('USEMAGIC', 'true', 'int','P');
INSERT INTO `adm_config` VALUES ('SERVER', '', 'str', 'P');
INSERT INTO `adm_config` VALUES ('LOGIN', '', 'str', 'P');
INSERT INTO `adm_config` VALUES ('PASS', '', 'str', 'P');
INSERT INTO `adm_config` VALUES ('DB', '', 'str', 'P');
INSERT INTO `adm_config` VALUES ('LOCALFOLDER', '', 'str', 'P');
INSERT INTO `adm_config` VALUES ('SUPPORTEMAIL', '', 'str', 'P');
INSERT INTO `adm_config` VALUES ('KEY', '', 'str', 'P');
INSERT INTO `adm_config` VALUES ('APPNAME', '', 'str', 'P');
INSERT INTO `adm_config` VALUES ('AVLANGS', 'array("en","fr")', 'arr', 'P');
INSERT INTO `adm_config` VALUES ('FRIENDEMAIL', '', 'str', 'P');
INSERT INTO `adm_config` VALUES ('useSharing', 'false', 'int', 'J');
INSERT INTO `adm_config` VALUES ('useNotation', 'false', 'int', 'J');
INSERT INTO `adm_config` VALUES ('useContact', 'false', 'int', 'J');
INSERT INTO `adm_config` VALUES ('headlinks', '{"fct":"openHelp()","label":lg("lblHelp"),"img":"-","comment":lg("lblFirstUsage"),"anonymous":true,"connected":false},{"fct":"link(\\"index.php?lang=en\\")","label":"EN","img":"-","comment":"English","anonymous":true,"connected":false},{"fct":"link(\\"index.php?lang=fr\\")","label":"FR","img":"-","comment":"Fran&ccedil;ais","anonymous":true,"connected":false},{"fct":"p_banner.option.show()","label":lg("lblShowOpt"),"img":"-","comment":"","anonymous":false,"connected":true},{"fct":"goIndex()","label":lg("lblArchive2"),"img":"-","comment":"","anonymous":false,"connected":true}', 'arr', 'J');
INSERT INTO `adm_config` VALUES ('useNewsletter', 'false', 'int', 'J');
INSERT INTO `adm_config` VALUES ('useConditions', 'false', 'int', 'J');
INSERT INTO `adm_config` VALUES ('conditionComment', '', 'str', 'J');
INSERT INTO `adm_config` VALUES ('nbicons', '40', 'int', 'J');
INSERT INTO `adm_config` VALUES ('ARCHIVE', 'false', 'int', 'P');
DROP TABLE IF EXISTS `adm_tabs`;
CREATE TABLE `adm_tabs` (
  `id` tinyint(3) unsigned NOT NULL auto_increment,
  `name` varchar(30) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `label` varchar(30) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `type` tinyint(3) unsigned NOT NULL default '0',
  `param` varchar(60) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `adm_tabs` VALUES (1, 'modulestab', 'Modules', 4, 'modules.php');
INSERT INTO `adm_tabs` VALUES (2, 'userstab', 'Utilisateurs', 4, 'users.php');
INSERT INTO `adm_tabs` VALUES (3, 'pagestab', 'Pages', 4, 'pages.php');
INSERT INTO `adm_tabs` VALUES (4, 'configstab', 'Configuration', 4, 'config.php');
DROP TABLE IF EXISTS `dir_cat_item`;
CREATE TABLE `dir_cat_item` (
  `item_id` mediumint(8) unsigned NOT NULL default '0',
  `category_id` smallint(5) unsigned NOT NULL default '0',
  `first` char(1) NOT NULL default ''
) TYPE=MyISAM DEFAULT CHARSET=utf8 COMMENT='cross table between categories and items' ;
DROP TABLE IF EXISTS `dir_cat_properties`;
CREATE TABLE `dir_cat_properties` (
  `category_id` smallint(5) unsigned NOT NULL default '0',
  `seq` char(1) NOT NULL default ''
) TYPE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO dir_cat_properties (category_id,seq) VALUES (2,0);
INSERT INTO dir_cat_properties (category_id,seq) VALUES (3,1);
DROP TABLE IF EXISTS `dir_category`;
CREATE TABLE `dir_category` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(60) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `parent_id` smallint(6) NOT NULL default '0',
  `typ` char(1) NOT NULL default 'O',
  `path` varchar(255) default NULL,
  `quantity` smallint(6) NOT NULL default '0',
  `updated` char(1) NOT NULL default 'Y',
  `lang` char(2) DEFAULT 'fr' NOT NULL default 'Y',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COMMENT='category of the directory' AUTO_INCREMENT=5000 ;
INSERT INTO dir_category (id,name,parent_id,typ,path,quantity,updated,lang) VALUES(2,'Widgets -fr',0,'O','',1,'Y','fr');
INSERT INTO dir_category (id,name,parent_id,typ,path,quantity,updated,lang) VALUES(3,'Widgets -en',0,'O','',1,'Y','en');
DROP TABLE IF EXISTS `dir_item`;
CREATE TABLE `dir_item` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `url` varchar(150) NOT NULL default '',
  `defvar` varchar(150) character set utf8 collate utf8_unicode_ci default NULL,
  `name` varchar(30) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `description` varchar(250) character set utf8 collate utf8_unicode_ci default NULL,
  `typ` char(1) NOT NULL default '',
  `status` char(1) NOT NULL default '',
  `format` char(1) NOT NULL default 'I',
  `size` smallint(5) unsigned NOT NULL default '0',
  `minwidth` smallint(5) unsigned NOT NULL default '280',
  `sizable` char(1) NOT NULL default '1',
  `website` varchar(50) character set utf8 collate utf8_unicode_ci default NULL,
  `editor_id` mediumint(8) unsigned NOT NULL default '0',
  `nbvariables` tinyint(3) unsigned NOT NULL default '0',
  `creation_date` date NOT NULL default '0000-00-00',
  `lastmodif_date` date NOT NULL default '0000-00-00',
  `notation` tinyint(3) unsigned NOT NULL default '0',
  `voter_nb` smallint(6) NOT NULL default '0',
  `updated` char(1) NOT NULL default 'Y',
  `nbusers` smallint(5) unsigned NOT NULL default '0',
  `sorting` smallint(5) unsigned NOT NULL default '0',
  `lang` char(2) NOT NULL default 'en',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=10000;
DROP TABLE IF EXISTS `app_notation`;
CREATE TABLE `app_notation` (
  `item_id` mediumint(8) unsigned NOT NULL default '0',
  `caract1` tinyint(3) unsigned NOT NULL default '0',
  `caract2` tinyint(3) unsigned NOT NULL default '0',
  `caract3` tinyint(3) unsigned NOT NULL default '0',
  `used` char(1) NOT NULL default 'N'
) TYPE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `module`;
CREATE TABLE `module` (
  `item_id` mediumint(8) unsigned NOT NULL default '0',
  `user_id` int(10) unsigned NOT NULL default '0',
  `profile_id` tinyint(3) unsigned NOT NULL default '0',
  `posx` tinyint(3) unsigned NOT NULL default '0',
  `posy` tinyint(3) unsigned NOT NULL default '0',
  `posj` tinyint(3) unsigned NOT NULL default '0',
  `typ` char(1) NOT NULL default 'D',
  `variables` varchar(250) character set utf8 collate utf8_unicode_ci default NULL,
  `uniq` smallint(5) unsigned NOT NULL default '1',
  KEY `ind_module` (`user_id`,`profile_id`,`item_id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `pages`;
CREATE TABLE `pages` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(30) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `description` text character set utf8 collate utf8_unicode_ci NOT NULL,
  `mode` char(1) NOT NULL default '',
  `type` char(1) NOT NULL default '',
  `param` varchar(150) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `seq` tinyint(3) unsigned NOT NULL default '0',
  `nbcol` tinyint(3) unsigned NOT NULL default '3',
  `showtype` tinyint(3) unsigned NOT NULL default '0',
  `npnb` tinyint(3) unsigned NOT NULL default '15',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COMMENT='pages configuration';
INSERT INTO `pages` VALUES (1, 'Home', 'Homepage example', '1', '1', '/', 1, 3, 0, 20);
DROP TABLE IF EXISTS `pages_module`;
CREATE TABLE `pages_module` (
  `item_id` mediumint(8) unsigned NOT NULL default '0',
  `page_id` smallint(5) unsigned NOT NULL default '0',
  `posx` tinyint(3) unsigned NOT NULL default '0',
  `posy` tinyint(3) unsigned NOT NULL default '0',
  `posj` tinyint(3) unsigned NOT NULL default '0',
  `variables` varchar(250) character set utf8 collate utf8_unicode_ci default NULL,
  `uniq` smallint(5) unsigned NOT NULL default '1'
) TYPE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `portals`;
CREATE TABLE `portals` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `name` varchar(40) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `description` varchar(200) character set utf8 collate utf8_unicode_ci default NULL,
  `status` char(1) NOT NULL default 'N',
  `author` int(10) unsigned NOT NULL default '0',
  `nbcol` char(1) NOT NULL default '3',
  `style` tinyint(1) unsigned NOT NULL default '1',
  `mode` char(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `portals_category`;
CREATE TABLE `portals_category` (
  `portal_id` mediumint(8) unsigned NOT NULL default '0',
  `category_id` smallint(5) unsigned NOT NULL default '0'
) TYPE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `portals_module`;
CREATE TABLE `portals_module` (
  `portal_id` mediumint(8) unsigned NOT NULL default '0',
  `item_id` mediumint(8) unsigned NOT NULL default '0',
  `posx` tinyint(3) unsigned NOT NULL default '0',
  `posy` tinyint(3) unsigned NOT NULL default '0',
  `posj` tinyint(3) unsigned NOT NULL default '0',
  `variables` varchar(250) NOT NULL default ''
) TYPE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `profile`;
CREATE TABLE `profile` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `number` tinyint(3) unsigned NOT NULL default '0',
  `name` varchar(14) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `width` tinyint(3) unsigned NOT NULL default '1',
  `height` tinyint(3) unsigned NOT NULL default '1',
  `lang` char(2) NOT NULL default 'fr',
  `def` char(1) NOT NULL default 'N',
  `style` tinyint(3) unsigned NOT NULL default '1',
  `refresh` tinyint(3) unsigned NOT NULL default '0',
  `pass` varchar(16) default NULL,
  `creation_date` date NOT NULL default '0000-00-00',
  `modif_date` date NOT NULL default '0000-00-00',
  `md5pass` varchar(32) NOT NULL default '',
  `menu` char(1) NOT NULL default 'o',
  `cacheurl` varchar(36) NOT NULL default '',
  `controls` char(1) NOT NULL default 'Y',
  `advise` char(1) NOT NULL default 'Y',
  `showtype` char(1) NOT NULL default '0',
  `nbnews` tinyint(3) unsigned NOT NULL default '20',
  `seq` tinyint(3) unsigned NOT NULL default '0',
  KEY `ind_profile` (`user_id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COMMENT='available profiles for the users';
DROP TABLE IF EXISTS `ref_pages_mode`;
CREATE TABLE `ref_pages_mode` (
  `id` tinyint(3) unsigned NOT NULL default '0',
  `label` varchar(30) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `ref_pages_mode` VALUES (1, 'Anonymous');
INSERT INTO `ref_pages_mode` VALUES (2, 'Connected');
INSERT INTO `ref_pages_mode` VALUES (3, 'Anonymous & connected');
DROP TABLE IF EXISTS `ref_pages_type`;
CREATE TABLE `ref_pages_type` (
  `id` tinyint(3) unsigned NOT NULL default '0',
  `label` varchar(30) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;
INSERT INTO `ref_pages_type` VALUES (1, 'personalizable portal');
INSERT INTO `ref_pages_type` VALUES (2, 'HTML page');
INSERT INTO `ref_pages_type` VALUES (3, 'javascript function');
INSERT INTO `ref_pages_type` VALUES (4, 'Redirection');
DROP TABLE IF EXISTS `search_index`;
CREATE TABLE `search_index` (
  `kw_id` mediumint(8) unsigned NOT NULL default '0',
  `item_id` mediumint(8) unsigned NOT NULL default '0',
  `weight` tinyint(3) unsigned NOT NULL default '0'
) TYPE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `search_keyword`;
CREATE TABLE `search_keyword` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `label` varchar(30) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `label` (`label`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `temp_category`;
CREATE TABLE `temp_category` (
  `category_id` smallint(6) NOT NULL default '0',
  `quantity` smallint(6) NOT NULL default '0',
  `gener1` smallint(5) unsigned default NULL,
  `gener2` smallint(5) unsigned default NULL,
  `gener3` smallint(5) unsigned default NULL,
  `gener4` smallint(5) unsigned default NULL,
  `pass` char(1) NOT NULL default ''
) TYPE=MyISAM DEFAULT CHARSET=utf8 COMMENT='temp table for category computing';
DROP TABLE IF EXISTS `temp_dir_cat_item`;
CREATE TABLE `temp_dir_cat_item` (
  `item_id` mediumint(9) NOT NULL default '0',
  `category_id` smallint(6) NOT NULL default '0',
  `first` char(1) NOT NULL default ''
) TYPE=MyISAM DEFAULT CHARSET=utf8 COMMENT='cross table between categories and items';
DROP TABLE IF EXISTS `temp_dir_item`;
CREATE TABLE `temp_dir_item` (
  `id` mediumint(9) NOT NULL auto_increment,
  `defvar` varchar(150) character set utf8 collate utf8_unicode_ci default NULL,
  `url` varchar(150) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `logo` varchar(150) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `name` varchar(30) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `description` varchar(250) character set utf8 collate utf8_unicode_ci default NULL,
  `keyword` varchar(200) character set utf8 collate utf8_unicode_ci default NULL,
  `typ` char(1) NOT NULL default '',
  `status` char(1) NOT NULL default '',
  `format` char(1) NOT NULL default 'I',
  `size` smallint(5) unsigned NOT NULL default '0',
  `minwidth` smallint(5) unsigned NOT NULL default '280',
  `sizable` char(1) NOT NULL default '1',
  `website` varchar(50) character set utf8 collate utf8_unicode_ci default NULL,
  `editor_id` mediumint(8) unsigned NOT NULL default '0',
  `nbvariables` tinyint(3) unsigned NOT NULL default '0',
  `creation_date` date NOT NULL default '0000-00-00',
  `lastmodif_date` date NOT NULL default '0000-00-00',
  `notation` tinyint(3) unsigned NOT NULL default '0',
  `voter_nb` smallint(6) NOT NULL default '0',
  `updated` char(1) NOT NULL default 'Y',
  `nbusers` smallint(5) unsigned NOT NULL default '0',
  `sorting` smallint(5) unsigned NOT NULL default '0',
  `lang` char(2) NOT NULL default 'en',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(64) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `pass` tinyblob NOT NULL,
  `long_name` varchar(100) character set utf8 collate utf8_unicode_ci default NULL,
  `typ` char(1) NOT NULL default '',
  `lastconnect_date` date NOT NULL default '0000-00-00',
  `md5pass` varchar(32) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `md5user` varchar(32) character set utf8 collate utf8_unicode_ci default NULL,
  `lang` char(2) NOT NULL default 'fr',
  PRIMARY KEY  (`id`),
  KEY `username` (`username`)
) TYPE=MyISAM DEFAULT CHARSET=utf8 COMMENT='user table';
DROP TABLE IF EXISTS `users_alert`;
CREATE TABLE `users_alert` (
  `email` varchar(64) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `keyword` varchar(32) character set utf8 collate utf8_unicode_ci NOT NULL default ''
) TYPE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `users_articles`;
CREATE TABLE `users_articles` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `title` varchar(100) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `link` varchar(150) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `users_favorites`;
CREATE TABLE `users_favorites` (
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `profile_id` tinyint(3) unsigned NOT NULL default '0',
  `number` smallint(5) unsigned NOT NULL default '0',
  `name` varchar(30) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `url` varchar(200) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  KEY `ind_favorites` (`user_id`,`profile_id`,`number`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `users_favorites` ADD `link_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
DROP TABLE IF EXISTS `users_friends`;
CREATE TABLE `users_friends` (
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `email` varchar(64) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  KEY `user_id` (`user_id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `users_group`;
CREATE TABLE `users_group` (
  `id` smallint(5) unsigned NOT NULL auto_increment,
  `name` varchar(64) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `parent_id` smallint(5) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `users_group_map`;
CREATE TABLE `users_group_map` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `group_id` smallint(5) unsigned NOT NULL default '0'
) TYPE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `users_notes`;
CREATE TABLE `users_notes` (
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `profile_id` tinyint(3) unsigned NOT NULL default '0',
  `number` smallint(3) unsigned NOT NULL default '0',
  `notes` text character set utf8 collate utf8_unicode_ci,
  KEY `ind_note` (`user_id`,`profile_id`,`number`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;
DROP TABLE IF EXISTS `users_tasks`;
CREATE TABLE `users_tasks` (
  `user_id` mediumint(8) unsigned NOT NULL default '0',
  `profile_id` tinyint(3) unsigned NOT NULL default '0',
  `number` smallint(5) unsigned NOT NULL default '0',
  `category` varchar(30) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  `name` varchar(50) character set utf8 collate utf8_unicode_ci NOT NULL default '',
  KEY `ind_tasks` (`user_id`,`profile_id`,`number`)
) TYPE=MyISAM DEFAULT CHARSET=utf8;
ALTER TABLE `users_tasks` ADD `task_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;

>1.0.1;

INSERT INTO `adm_config` VALUES ('barcolnb', '7', 'int', 'J');
INSERT INTO `adm_config` VALUES ('POSHVERSION', '1.0.1', 'str', 'P');
ALTER TABLE `users` CHANGE `pass` `pass` TINYBLOB NOT NULL;
ALTER TABLE `module` ADD `blocked` TINYINT( 1 ) UNSIGNED NOT NULL default '0';
ALTER TABLE `pages_module` ADD `blocked` TINYINT( 1 ) UNSIGNED DEFAULT '0' NOT NULL ;
ALTER TABLE `portals_module` ADD `blocked` TINYINT( 1 ) UNSIGNED DEFAULT '0' NOT NULL ;

>1.1.0;

INSERT INTO `adm_config` VALUES ('showHomeBar', '1', 'int', 'J');
ALTER TABLE `users_notes` CHANGE `profile_id` `profile_id` TINYINT( 3 ) UNSIGNED DEFAULT '0',CHANGE `number` `number` SMALLINT( 3 ) UNSIGNED DEFAULT '0';
ALTER TABLE `users_notes` DROP INDEX `ind_note`;
ALTER TABLE `users_notes` ADD `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
UPDATE module AS m, users_notes AS un SET m.variables=CONCAT(m.variables,'&noteid=',un.id) WHERE m.item_id=84 AND m.user_id=un.user_id AND m.profile_id=un.profile_id AND un.number=m.uniq;
CREATE TABLE `users_favorites_id` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,status CHAR(1) NULL,`temp` VARCHAR( 30 ) NOT NULL ,PRIMARY KEY ( `id` ) );
INSERT INTO users_favorites_id(status,temp) SELECT 'A',CONCAT( user_id, '_', profile_id, '_', number ) FROM users_favorites GROUP BY user_id, profile_id, number;
ALTER TABLE `users_favorites` DROP INDEX `ind_favorites` ;
ALTER TABLE `users_favorites` ADD `id` INT UNSIGNED NOT NULL AFTER `link_id` ;
UPDATE users_favorites AS u,users_favorites_id AS ui SET u.id=ui.id WHERE ui.temp=CONCAT(user_id, '_', profile_id, '_', number);
ALTER TABLE `users_favorites_id` DROP `temp` ;
UPDATE module AS m, users_favorites as uf SET m.variables=CONCAT(m.variables,'&linkid=',uf.id) WHERE m.item_id=85 AND m.user_id=uf.user_id AND m.profile_id=uf.profile_id AND uf.number=m.uniq;
CREATE TABLE `users_tasks_id` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,status CHAR(1) NULL,`temp` VARCHAR( 30 ) NOT NULL ,PRIMARY KEY ( `id` ) );
INSERT INTO users_tasks_id(status,temp) SELECT 'A',CONCAT( user_id, '_', profile_id, '_', number ) FROM users_tasks GROUP BY user_id, profile_id, number;
ALTER TABLE `users_tasks` DROP INDEX `ind_tasks` ;
ALTER TABLE `users_tasks` ADD `id` INT UNSIGNED NOT NULL AFTER `task_id` ;
UPDATE users_tasks AS u,users_tasks_id AS ui SET u.id=ui.id WHERE ui.temp=CONCAT(user_id, '_', profile_id, '_', number);
ALTER TABLE `users_tasks_id` DROP `temp` ;
UPDATE module AS m, users_tasks as uf SET m.variables=CONCAT(m.variables,'&taskid=',uf.id) WHERE m.item_id=295 AND m.user_id=uf.user_id AND m.profile_id=uf.profile_id AND uf.number=m.uniq;
INSERT INTO `adm_config` VALUES ('txtnote', 'You can change this default note text in the admin panel, in the general configuration.', 'str', 'J');
INSERT INTO `adm_config` VALUES ('menuposition', 'v', 'str', 'J');
UPDATE `adm_config` SET value='' WHERE parameter='option';
UPDATE `adm_config` SET value='{"fct":"openHelp()","label":lg("lblHelp"),"img":"-","comment":lg("lblFirstUsage"),"anonymous":true,"connected":false},{"fct":"p_addContent.menu()","label":lg("lblAddContent"),"img":"ico_menu_add.gif","comment":lg("lblAddContent2"),"anonymous":true,"connected":true},{"fct":"p_edit.menu()","label":lg("lblPersonalize"),"img":"ico_menu_tools.gif","comment":lg("lblPersonalize"),"anonymous":true,"connected":true},{"fct":"showSave()","label":"<B>"+lg("lblSave")+"</B>","img":"ico_menu_disk.gif","comment":lg("lblSavePage"),"anonymous":true,"connected":false},{"fct":"link(\\"index.php?lang=en\\")","label":"EN","img":"-","comment":"English","anonymous":true,"connected":false},{"fct":"link(\\"index.php?lang=fr\\")","label":"FR","img":"-","comment":"Français","anonymous":true,"connected":false},{"fct":"goIndex()","label":lg("lblArchive2"),"img":"-","comment":"","anonymous":false,"connected":true}' WHERE parameter='headlinks';
ALTER TABLE `users_tasks` ADD `done` CHAR( 1 ) DEFAULT 'N' NOT NULL ;
CREATE TABLE `users_calendar_id` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,`status` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,PRIMARY KEY ( `id` ) );
CREATE TABLE `users_calendar` (`cal_id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,`id` INT UNSIGNED NOT NULL ,`title` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`date` DATE NOT NULL ,`time` VARCHAR( 5 ) NOT NULL ,`ampm` CHAR( 1 ) DEFAULT 'A',PRIMARY KEY ( `cal_id` ) );
INSERT INTO `adm_config` VALUES ('rssrefreshdelay', '60', 'int', 'J');
ALTER TABLE `module` ADD `minimized` TINYINT( 1 ) UNSIGNED DEFAULT '0' NOT NULL ;
ALTER TABLE `pages_module` ADD `minimized` TINYINT( 1 ) UNSIGNED DEFAULT '0' NOT NULL ;
ALTER TABLE `portals_module` ADD `minimized` TINYINT( 1 ) UNSIGNED DEFAULT '0' NOT NULL ;
INSERT INTO `adm_config` VALUES ('debugmode', 'false', 'int', 'J');
INSERT INTO `adm_config` VALUES ('defaultmode', 'anonymous', 'str', 'P');
UPDATE adm_config SET desttype='A' WHERE parameter='useGroup';
ALTER TABLE `pages` ADD `group_id` SMALLINT UNSIGNED DEFAULT '0' NOT NULL AFTER `id` ;
CREATE TABLE `log` (`action` TINYINT UNSIGNED NOT NULL ,`date` DATE NOT NULL ,`param1` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL ,`param2` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL ,`param3` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL );
CREATE TABLE `stats_processing_log` (`action` TINYINT UNSIGNED ZEROFILL NOT NULL ,`date` DATE NOT NULL ,`param1` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL ,`param2` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL );
CREATE TABLE `stats_final` (`action` TINYINT UNSIGNED ZEROFILL NOT NULL ,`date` DATE NOT NULL ,`result1` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL ,`result2` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL );
INSERT INTO `adm_tabs` ( `id` , `name` , `label` , `type` , `param` ) VALUES (5, 'statstab', 'Statistics', '4', 'stats.php');
CREATE TABLE `dir_rss` (`id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,`url` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`icon` TINYINT( 1 ) UNSIGNED NOT NULL ,PRIMARY KEY ( `id` ) ,INDEX ( `url` ) );
INSERT INTO `adm_config` VALUES ('showicon', 'true', 'int', 'J');
INSERT INTO `adm_config` VALUES ('useproxy', 'false', 'int', 'A');
INSERT INTO `adm_config` VALUES ('PROXYSERVER', '', 'str', 'P');
INSERT INTO `adm_config` VALUES ('PROXYPORT', '', 'str', 'P');
INSERT INTO `adm_config` VALUES ('PROXYCONNECTION', '', 'str', 'P');

>1.2.0;

INSERT INTO `adm_config` VALUES ('proxypacfile', '', 'str', 'A');
CREATE TABLE `users_mail_providers` (`provider_ext` varchar(32) collate utf8_unicode_ci NOT NULL default '',`provider` varchar(16) collate utf8_unicode_ci NOT NULL default '',`webmail` varchar(64) collate utf8_unicode_ci NOT NULL default '',`serveur` varchar(32) collate utf8_unicode_ci NOT NULL default '',`port` varchar(5) collate utf8_unicode_ci NOT NULL default '',`protocole` varchar(32) collate utf8_unicode_ci NOT NULL default '',`extension` varchar(30) collate utf8_unicode_ci NOT NULL default '',PRIMARY KEY  (`provider_ext`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO `users_mail_providers` VALUES ('wanadoo.fr', 'WANADOO', 'http://www.wanadoo.fr', 'pop.wanadoo.fr', '110', '/pop3/notls', '');
INSERT INTO `users_mail_providers` VALUES ('laposte.net', 'LA POSTE', 'http://www.laposte.net', 'pop.laposte.net', '110', '/pop3/notls', '');
INSERT INTO `users_mail_providers` VALUES ('club-internet.fr', 'CLUB INTERNET', 'http://flashmail.club-internet.fr', 'pop3.club-internet.fr', '110', '/pop3/notls', '');
INSERT INTO `users_mail_providers` VALUES ('free.fr', 'FREE', 'http://www.free.fr', 'pop.free.fr', '110', '/pop3/notls', '');
INSERT INTO `users_mail_providers` VALUES ('freesurf.fr', 'FREESURF', 'http://www.freesurf.fr', 'pop.freesurf.fr', '110', '/pop3/notls', '');
INSERT INTO `users_mail_providers` VALUES ('netcourrier.com', 'NETCOURRIER', 'http://www.netcourrier.com', 'mail.netcourrier.com', '110', '/pop3/notls', '');
INSERT INTO `users_mail_providers` VALUES ('neuf.fr', 'NEUF', 'http://webmail.neuf.fr', 'pop.neuf.fr', '110', '/pop3/notls', '');
INSERT INTO `users_mail_providers` VALUES ('tiscali.fr', 'TISCALI', 'http://login.aliceadsl.fr', 'pop.tiscali.fr', '110', '/pop3/notls', '');
INSERT INTO `users_mail_providers` VALUES ('yahoo.fr', 'YAHOO', 'http://www.yahoo.fr', 'pop.mail.yahoo.fr', '110', '/pop3/notls', '');
INSERT INTO `users_mail_providers` VALUES ('tele2.fr', 'TELE2', 'http://www.tele2.fr', 'pop.tele2.fr', '110', '/pop3/notls', '');
INSERT INTO `users_mail_providers` VALUES ('aol.fr', 'AOL', 'http://aolmail.aol.fr', 'imap.fr.aol.com', '143', '/notls', '');
INSERT INTO `users_mail_providers` VALUES ('9online.fr', '9 ONLINE', 'http://webmail.neuf.fr', 'pop.9online.fr', '110', '/pop3/notls', '');
INSERT INTO `users_mail_providers` VALUES ('cegetel.net', 'CEGETEL', 'http://www.cegetel.net', 'pop.cegetel.net', '110', '/pop3/notls', '');
INSERT INTO `users_mail_providers` VALUES ('gmail.com', 'GMAIL', 'http://www.gmail.com', 'pop.gmail.com', '110', '/pop3/notls', '@gmail.com');
INSERT INTO `users_mail_providers` VALUES ('ifrance.com', 'IFRANCE', 'http://web.ifrance.com', 'pop.ifrance.com', '110', '/pop3/notls', '');
INSERT INTO `users_mail_providers` VALUES ('magic.fr', 'MAGIC.FR', 'http://webmail.magic.fr', 'pop2.magic.fr', '110', '/pop3/notls', '');
INSERT INTO `users_mail_providers` VALUES ('noos.fr', 'NOOS', 'http://webmail.noos.fr', 'pop.noos.fr', '110', '/pop3/notls', '');
INSERT INTO `users_mail_providers` VALUES ('orange.fr', 'ORANGE', 'http://www.orange.fr', 'pop.orange.fr', '110', '/pop3/notls', '');
CREATE TABLE `users_mail` (`id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,`user_id` mediumint(8) unsigned NOT NULL,`provider` varchar(16) collate utf8_unicode_ci default NULL,`webmail` varchar(64) collate utf8_unicode_ci default NULL,`serveur` varchar(32) collate utf8_unicode_ci default NULL,`port` varchar(5) collate utf8_unicode_ci default NULL,`protocole` varchar(32) collate utf8_unicode_ci default NULL,`user` varchar(32) collate utf8_unicode_ci default NULL,`pass` varchar(32) collate utf8_unicode_ci default NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE `profile` ADD `usereader` CHAR( 1 ) DEFAULT '1' NOT NULL AFTER `advise` ;
ALTER TABLE `profile` CHANGE `name` `name` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
UPDATE adm_config SET value='false' WHERE parameter='USEMAGIC';
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('useoverview', 'true', 'int', 'J');
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('allowredactor', 'false', 'int', 'A');
UPDATE adm_config SET value = REPLACE (value,',{"fct":"goIndex()","label":lg("lblArchive2"),"img":"-","comment":"","anonymous":false,"connected":true}','') WHERE parameter = 'headlinks';
ALTER TABLE `dir_item` CHANGE `url` `url` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

>1.2.3;

UPDATE `users_mail_providers` SET `protocole` = '/pop3' WHERE CONVERT( `users_mail_providers`.`provider_ext` USING utf8 ) = 'orange.fr' LIMIT 1 ;
UPDATE `users_mail_providers` SET `serveur` = 'pop.orange.fr',`protocole` = '/pop3' WHERE CONVERT( `users_mail_providers`.`provider_ext` USING utf8 ) = 'wanadoo.fr' LIMIT 1 ;
CREATE TABLE `adm_plugins` (`name` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`link` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`type` VARCHAR( 15 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`status` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'O' NOT NULL );
ALTER TABLE `profile` ADD `icon` VARCHAR( 150 ) ;
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('usereader', 'true', 'int', 'J');
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('showtabicon', 'true', 'int', 'J');
UPDATE `adm_config` SET `desttype` = 'A' WHERE CONVERT( `parameter` USING utf8 ) = 'LOCALFOLDER' LIMIT 1 ;
INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ("displayrssdesc","true","int","J");
UPDATE adm_config SET value='60' WHERE parameter='nbicons';
ALTER TABLE `temp_dir_item` ADD `keywords` VARCHAR( 250 ) NOT NULL ;
INSERT INTO `adm_tabs` ( `name` , `label` , `type` , `param` ) VALUES ('comtab', 'Communication', '4', 'communication.php');
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('bartype', '0', 'int', 'A');
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('bartexthtml', '', 'str', 'J');

>1.3.0;

ALTER TABLE `adm_plugins` CHANGE `type` `dependency` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `adm_plugins` ADD PRIMARY KEY ( `name` ) ;
UPDATE adm_plugins SET dependency='no';
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('accountType', 'mail', 'str', 'A');
CREATE TABLE `contact_sentitems` (`id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,`sender` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`subject` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`message` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`receiver` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`sentdate` DATE NOT NULL ,`status` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'S' NOT NULL ,PRIMARY KEY ( `id` ));
UPDATE `users_mail_providers` SET `port` = '995',`protocole` = '/pop3/ssl' WHERE `provider_ext` = 'gmail.com';
CREATE TABLE `adm_themes` (`name` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`seq` TINYINT UNSIGNED NOT NULL );
INSERT INTO `adm_themes` ( `name` , `seq` ) VALUES ('classic_blue', '1');
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('barclosing', 'false', 'int', 'J');
ALTER TABLE `temp_dir_item` ADD `usereader` TINYINT( 1 ) UNSIGNED DEFAULT '1' NOT NULL ,ADD `autorefresh` TINYINT( 1 ) UNSIGNED DEFAULT '0' NOT NULL ;
ALTER TABLE `dir_item` ADD `usereader` TINYINT( 1 ) UNSIGNED DEFAULT '1' NOT NULL ,ADD `autorefresh` TINYINT( 1 ) UNSIGNED DEFAULT '0' NOT NULL ;
UPDATE dir_item SET autorefresh=1 WHERE id=350;
ALTER TABLE `users_calendar` ADD `comment` VARCHAR( 250 ) NOT NULL AFTER `title` ;
ALTER TABLE `users_calendar` ADD `endtime` VARCHAR( 5 ) NOT NULL AFTER `ampm` ;
ALTER TABLE `users_tasks` CHANGE `category` `comment` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `users_mail` CHANGE `user` `user` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;

>1.3.1;

ALTER TABLE `users_favorites` DROP `user_id` ,DROP `profile_id` ,DROP `number` ;
ALTER TABLE `users_favorites_id` ADD `user_id` INT UNSIGNED DEFAULT '0' AFTER `id` ;
ALTER TABLE `users_tasks` DROP `user_id` ,DROP `profile_id` ,DROP `number` ;
ALTER TABLE `users_tasks_id` ADD `user_id` INT UNSIGNED DEFAULT '0' AFTER `id` ;
ALTER TABLE `users_calendar_id` ADD `user_id` INT UNSIGNED DEFAULT '0' AFTER `id` ;
ALTER TABLE `users_favorites` ADD `tags` VARCHAR( 100 ) ;

ALTER TABLE `adm_tabs` CHANGE `param` `param` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
CREATE TABLE `adm_tabs_fct` (`tabname` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`fctname` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`label` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`link` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL );
INSERT INTO `adm_tabs_fct` ( `tabname` , `fctname` , `label` , `link` , `description` ) VALUES ('comtab', 'infobar', 'informationBar', 'admin/communication_infobar.php', 'informationBarTxt');
INSERT INTO `adm_tabs_fct` ( `tabname` , `fctname` , `label` , `link` , `description` ) VALUES ('comtab', 'emailing', 'emailing', 'admin/communication_emailing.php', 'emailingTxt');
INSERT INTO `adm_tabs_fct` ( `tabname` , `fctname` , `label` , `link` , `description` ) VALUES ('statstab', 'statistics', 'appStats', 'admin/stats_compute.php', 'appStats');
INSERT INTO `adm_tabs_fct` ( `tabname` , `fctname` , `label` , `link` , `description` ) VALUES ('configstab', 'generalsettings', 'appGeneralConfiguration', 'admin/config_general.php', 'appGeneralConfigurationTxt');
INSERT INTO `adm_tabs_fct` ( `tabname` , `fctname` , `label` , `link` , `description` ) VALUES ('configstab', 'themes', 'appThemeConfiguration', 'admin/config_theme.php', 'appThemeConfigurationTxt');
INSERT INTO `adm_tabs_fct` ( `tabname` , `fctname` , `label` , `link` , `description` ) VALUES ('configstab', 'plugins', 'appPluginsConfiguration', 'admin/config_plugins.php', 'appPluginsConfigurationTxt');
INSERT INTO `adm_tabs_fct` ( `tabname` , `fctname` , `label` , `link` , `description` ) VALUES ('pagestab', 'tabs', 'tabMgmt', 'admin/pages_tabs.php', 'tabMgmtTxt2');
INSERT INTO `adm_tabs_fct` ( `tabname` , `fctname` , `label` , `link` , `description` ) VALUES ('userstab', 'usersmgmt', 'userMgmt', 'admin/users_mgmt.php', 'userMgmt');
INSERT INTO `adm_tabs_fct` ( `tabname` , `fctname` , `label` , `link` , `description` ) VALUES ('modulestab', 'modulesmgmt', 'modMgmt', 'admin/modules_mgmt.php', 'modMgmt');
UPDATE `adm_tabs` SET `param` = 'admin/modules.php' WHERE name='modulestab' ;
UPDATE `adm_tabs` SET `param` = 'admin/users.php' WHERE name='userstab' ;
UPDATE `adm_tabs` SET `param` = 'admin/pages.php' WHERE name='pagestab' ;
UPDATE `adm_tabs` SET `param` = 'admin/config.php' WHERE name='configstab' ;
UPDATE `adm_tabs` SET `param` = 'admin/stats.php' WHERE name='statstab' ;
UPDATE `adm_tabs` SET `param` = 'admin/communication.php' WHERE name='comtab' ;

ALTER TABLE `dir_item` CHANGE `defvar` `defvar` VARCHAR( 230 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;

UPDATE adm_config SET value='80' WHERE parameter='nbicons';
CREATE TABLE `adm_log` (`id` MEDIUMINT UNSIGNED NOT NULL AUTO_INCREMENT ,`log` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`logdate` DATE NOT NULL ,`typ` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'O' NOT NULL ,PRIMARY KEY ( `id` ) );

>1.3.2;

INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('columnchange', 'true', 'int', 'J');
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('ctrlhiding', 'true', 'int', 'J');
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('doubleprotection', 'true', 'int', 'J');
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('alloweditmenu', 'true', 'int', 'J');
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('allowaddmenu', 'true', 'int', 'J');
UPDATE adm_config SET value = REPLACE (value,'{"fct":"p_addContent.menu()","label":lg("lblAddContent"),"img":"ico_menu_add.gif","comment":lg("lblAddContent2"),"anonymous":true,"connected":true},','') WHERE parameter = 'headlinks';
UPDATE adm_config SET value = REPLACE (value,'{"fct":"p_edit.menu()","label":lg("lblPersonalize"),"img":"ico_menu_tools.gif","comment":lg("lblPersonalize"),"anonymous":true,"connected":true},','') WHERE parameter = 'headlinks';
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('showrsscell', 'true', 'int', 'J');
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('showModuleSearch', 'true', 'int', 'J');
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('showModuleExpl', 'true', 'int', 'J');
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('portaldirtype', 'group', 'str', 'A');

>1.4.0;

ALTER TABLE `profile` ADD `loadonstart` TINYINT UNSIGNED DEFAULT '0' NOT NULL ;
INSERT INTO `adm_tabs_fct` ( `tabname` , `fctname` , `label` , `link` , `description` ) VALUES ('configstab', 'featuresaccess', 'featuresAccess', 'admin/config_features.php', 'featuresAccessTxt');
CREATE TABLE `adm_headlinks` (`id` TINYINT UNSIGNED NOT NULL ,`label` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`comment` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`clss` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`images` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`fct` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`status` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`seq` TINYINT UNSIGNED NOT NULL );
ALTER TABLE `adm_headlinks` ADD `anonymous` TINYINT( 1 ) UNSIGNED NOT NULL ,ADD `connected` TINYINT( 1 ) UNSIGNED NOT NULL ,ADD `admin` TINYINT( 1 ) UNSIGNED NOT NULL ;
INSERT INTO `adm_headlinks` VALUES (1, 'lblHelp', 'lblFirstUsage', '', '', 'openHelp()', 'O', 0, 1, 0, 0);
INSERT INTO `adm_headlinks` VALUES (2, 'lblAddContent', 'lblAddContent2', 'b', 'ico_menu_add.gif', 'p_addContent.menu()', 'O', 0, 1, 1, 0);
INSERT INTO `adm_headlinks` VALUES (3, 'lblArchive2', '', '', '', 'goIndex()', 'O', 0, 0, 1, 0);
INSERT INTO `adm_headlinks` VALUES (4, 'lblPersonalize', 'optionsOfThisPage', '', '', 'p_edit.menu()', 'O', 0, 1, 1, 0);
INSERT INTO `adm_headlinks` VALUES (5, 'lblSave', 'lblSavePage', 'b', 'ico_menu_disk.gif', 'showSave()', 'O', 0, 1, 0, 0);
INSERT INTO `adm_headlinks` VALUES (6, 'lblConnect', 'lblConnect', 'b', '', 'return connectBox()', 'O', 0, 1, 0, 0);
INSERT INTO `adm_headlinks` VALUES (7, 'lblDisconnect', 'lblDisconnect', 'b', '', 'logout()', 'O', 0, 0, 1, 1);
ALTER TABLE `dir_item` CHANGE `size` `height` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `temp_dir_item` CHANGE `size` `height` SMALLINT( 5 ) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `pages` CHANGE `mode` `position` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `portals` CHANGE `mode` `position` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT '0' NOT NULL;
ALTER TABLE `users_calendar` CHANGE `comment` `comments` VARCHAR( 250 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,CHANGE `date` `pubdate` DATE DEFAULT '0000-00-00' NOT NULL;
ALTER TABLE `log` CHANGE `date` `pubdate` DATE DEFAULT '0000-00-00' NOT NULL;
ALTER TABLE `stats_processing_log` CHANGE `date` `pubdate` DATE DEFAULT '0000-00-00' NOT NULL;
ALTER TABLE `stats_final` CHANGE `date` `treatdate` DATE DEFAULT '0000-00-00' NOT NULL;
ALTER TABLE `users_tasks` CHANGE `comment` `comments` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `users_mail` CHANGE `user` `username` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE `profile` CHANGE `number` `id` TINYINT( 3 ) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `adm_config` CHANGE `parameter` `parameter` VARCHAR( 32 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('blockedModulePreventPageRemoval', 'true', 'int', 'J');
ALTER TABLE `pages` ADD `style` TINYINT UNSIGNED DEFAULT '1' NOT NULL ;
ALTER TABLE `users` ADD `description` TEXT NOT NULL ,ADD `picture` VARCHAR( 200 ) NOT NULL ;
UPDATE `adm_tabs_fct` SET `description` = 'appStatsTxt' WHERE label = 'appStats';
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('enterprise', 'false', 'int', 'A');

CREATE TABLE `feed_articles` (`id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,`feed_id` MEDIUMINT UNSIGNED NOT NULL ,`title` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`link` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`image` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`video` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`audio` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`pubdate` VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`uniqid` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,PRIMARY KEY ( `id` ) );
CREATE TABLE `feed_articles_read` (`article_id` INT UNSIGNED NOT NULL ,`user_id` MEDIUMINT UNSIGNED NOT NULL ,`status` TINYINT( 1 ) UNSIGNED NOT NULL );
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('registerfeeds', 'true', 'int', 'A');
ALTER TABLE `dir_rss` ADD `lastloadedid` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;
ALTER TABLE `dir_rss` ADD `lastloadedtime` DATETIME NOT NULL ;
ALTER TABLE `adm_tabs` ADD UNIQUE `uniq_tab_name` ( `name` );
ALTER TABLE `adm_tabs_fct` ADD UNIQUE `uniq_fct_name` ( `fctname` ) ;
ALTER TABLE `dir_rss` ADD `title` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL AFTER `url` ,ADD `auth` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci AFTER `title` ;
ALTER TABLE `temp_dir_item` CHANGE `typ` `typ` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,CHANGE `status` `status` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,CHANGE `format` `format` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'I' NOT NULL ,CHANGE `sizable` `sizable` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT '1' NOT NULL ,CHANGE `updated` `updated` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'Y' NOT NULL ,CHANGE `lang` `lang` VARCHAR( 2 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'en' NOT NULL ,CHANGE `keywords` `keywords` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;
ALTER TABLE `dir_rss` CHANGE `title` `title` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,CHANGE `auth` `auth` VARCHAR( 64 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL ,CHANGE `lastloadedid` `lastloadedid` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

> 1.4.1;

ALTER TABLE `adm_headlinks` ADD UNIQUE `uniq_admheadlinks` ( `id` , `label` , `anonymous` , `connected` , `admin` );
ALTER TABLE `adm_headlinks` CHANGE `images` `images` VARCHAR( 100 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

> 1.4.2;

UPDATE adm_config SET value=REPLACE(value,'français','fran&ccedil;ais') WHERE parameter='headlinks';
ALTER TABLE `module` ADD `x` SMALLINT UNSIGNED DEFAULT '0' NOT NULL AFTER `posj` ,ADD `y` SMALLINT UNSIGNED DEFAULT '0' NOT NULL AFTER `x` ;
ALTER TABLE `pages_module` ADD `x` SMALLINT UNSIGNED DEFAULT '0' NOT NULL AFTER `posj` ,ADD `y` SMALLINT UNSIGNED DEFAULT '0' NOT NULL AFTER `x` ;
ALTER TABLE `portals_module` ADD `x` SMALLINT UNSIGNED DEFAULT '0' NOT NULL AFTER `posj` ,ADD `y` SMALLINT UNSIGNED DEFAULT '0' NOT NULL AFTER `x` ;
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('moduleAlign', 'true', 'int', 'J');
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('moduleAlignDefault', 'true', 'int', 'J');
ALTER TABLE `profile` ADD `modulealign` CHAR( 1 ) DEFAULT 'Y' NOT NULL ;
ALTER TABLE `pages` ADD `modulealign` CHAR( 1 ) DEFAULT 'Y' NOT NULL ;
ALTER TABLE `portals` ADD `modulealign` CHAR( 1 ) DEFAULT 'Y' NOT NULL ;
ALTER TABLE `adm_headlinks` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `adm_log` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `adm_config` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `adm_plugins` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `adm_tabs` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `adm_tabs_fct` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `adm_themes` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `app_notation` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `contact_sentitems` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `dir_cat_item` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `dir_cat_properties` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `dir_category` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `dir_item` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `dir_rss` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `feed_articles` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `feed_articles_read` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `log` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `module` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `pages` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `pages_module` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `portals` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `portals_category` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `portals_module` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `profile` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `ref_pages_mode` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `ref_pages_type` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `search_index` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `search_keyword` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `stats_final` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `stats_processing_log` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `temp_category` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `temp_dir_cat_item` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `temp_dir_item` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `users` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `users_alert` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `users_articles` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `users_calendar` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `users_calendar_id` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `users_favorites` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `users_favorites_id` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `users_friends` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `users_group` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `users_group_map` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `users_notes` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `users_tasks` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `users_tasks_id` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `feed_articles` CHANGE `link` `link` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `users_favorites_id` CHANGE `status` `status` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE `users_tasks_id` CHANGE `status` `status` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL;
ALTER TABLE `users_calendar` CHANGE `comments` `comments` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,CHANGE `time` `time` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,CHANGE `ampm` `ampm` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'A',CHANGE `endtime` `endtime` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;

INSERT INTO `adm_config` VALUES ('homeDivs', '', 'arr', 'J');
UPDATE `adm_config` SET `desttype` = 'A' WHERE CONVERT( `parameter` USING utf8 ) = 'POSHVERSION' LIMIT 1 ;
INSERT INTO `adm_config` VALUES ('application', 'P.O.S.H', 'str', 'A');
ALTER TABLE `users` ADD `stat` VARCHAR( 200 ) NOT NULL ;
UPDATE `dir_item` SET `minwidth` = '320' WHERE `id` =295 LIMIT 1 ;

> 1.4.3;

ALTER TABLE `dir_category` ADD `secured` TINYINT( 1 ) UNSIGNED DEFAULT '0' NOT NULL ;
CREATE TABLE `users_group_category_map` (`group_id` SMALLINT UNSIGNED NOT NULL ,`category_id` SMALLINT UNSIGNED NOT NULL);
ALTER TABLE `profile` ADD `type` CHAR( 1 ) NOT NULL DEFAULT '1', ADD `param` VARCHAR( 150 ) NOT NULL ;

INSERT INTO `adm_config` VALUES ('loadlatestpageonstart', 'true', 'int', 'J');
UPDATE adm_headlinks SET id=id+1 WHERE label='lblSave';
INSERT INTO adm_headlinks (id,label,comment,clss,images,fct,status,seq,anonymous,connected,admin) SELECT id-1,'lblHelp','','','','p_help.open()','O',0,1,1,0 FROM adm_headlinks WHERE label='lblSave';
UPDATE adm_config SET desttype='A' WHERE parameter='SUPPORTEMAIL';
ALTER TABLE `dir_item` CHANGE `url` `url` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;
ALTER TABLE `temp_dir_item` CHANGE `url` `url` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL;

> 1.4.4;

ALTER TABLE `users` ADD `extra` VARCHAR( 250 ) ;
INSERT INTO `adm_config` VALUES ('template', 'default', 'str', 'A');
UPDATE `adm_config` SET `value` = REPLACE(value,'jsprof','p_tabs.selId') WHERE  CONVERT(`parameter` USING utf8) = 'option';
INSERT INTO `adm_config` VALUES ('maxPageNb', '30', 'int', 'J');
ALTER TABLE `search_keyword` ADD `label_simplified` VARCHAR( 30 ) NOT NULL ;
UPDATE search_keyword SET label_simplified=label;
ALTER TABLE `search_keyword` DROP INDEX `label` ;
ALTER TABLE `search_keyword` ADD INDEX `ind_labelsimplified` ( `label_simplified` );

> 1.5.2;

INSERT INTO `adm_config` VALUES ('refreshFeedsDelai', '1800', 'int', 'A');
INSERT INTO `adm_config` VALUES ('feedsAutoLoading', 'false', 'int', 'A');
CREATE TABLE `widget_addressbook` (`add_id` int(10) unsigned NOT NULL auto_increment,`id` int(10) unsigned NOT NULL default '0',`firstname` varchar(30) collate utf8_unicode_ci NOT NULL default '',`lastname` varchar(30) collate utf8_unicode_ci NOT NULL default '',`email` varchar(60) collate utf8_unicode_ci NOT NULL default '',`company` varchar(60) collate utf8_unicode_ci NOT NULL default '',`func` varchar(30) collate utf8_unicode_ci NOT NULL default '',`phone1` varchar(30) collate utf8_unicode_ci NOT NULL default '',`phone2` varchar(30) collate utf8_unicode_ci NOT NULL default '',`other` varchar(150) collate utf8_unicode_ci NOT NULL default '',`tags` varchar(100) character set utf8 default NULL,PRIMARY KEY  (`add_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
CREATE TABLE `widget_addressbook_id` (`id` int(10) unsigned NOT NULL auto_increment,`user_id` int(10) unsigned default '0',`status` char(1) collate utf8_unicode_ci default NULL,PRIMARY KEY  (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ;
ALTER TABLE `feed_articles_read` ADD PRIMARY KEY ( `article_id` , `user_id` );
INSERT INTO `adm_tabs_fct` ( `tabname` , `fctname` , `label` , `link` , `description` ) VALUES ('configstab', 'langtab', 'langSelection', 'admin/config_lang.php', 'langSelectionTxt');

> 1.5.3;

ALTER TABLE `module` ADD UNIQUE `module_unique` ( `user_id` , `profile_id` , `uniq` ) ;

> 1.5.4;

ALTER TABLE `pages` ADD `controls` CHAR( 1 ) DEFAULT 'Y';
ALTER TABLE `dir_cat_item` CHANGE `first` `first` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'Y' NOT NULL;
ALTER TABLE `feed_articles` CHANGE `image` `image` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `video` `video` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `audio` `audio` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `pubdate` `pubdate` VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `uniqid` `uniqid` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
ALTER TABLE `profile` CHANGE `cacheurl` `cacheurl` VARCHAR( 36 ) CHARACTER SET utf8 COLLATE utf8_general_ci,CHANGE `param` `param` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
ALTER TABLE `temp_category` CHANGE `pass` `pass` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci ;
ALTER TABLE `temp_dir_cat_item` CHANGE `first` `first` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'Y' NOT NULL ;
ALTER TABLE `temp_dir_item` CHANGE `keywords` `keywords` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `users` CHANGE `pass` `pass` TINYBLOB,CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,CHANGE `picture` `picture` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci,CHANGE `stat` `stat` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
ALTER TABLE `users_calendar` CHANGE `comments` `comments` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `time` `time` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
ALTER TABLE `users_tasks` CHANGE `comments` `comments` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `widget_addressbook` CHANGE `firstname` `firstname` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `lastname` `lastname` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `email` `email` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `company` `company` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `func` `func` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `phone1` `phone1` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `phone2` `phone2` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `other` `other` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci ;

> 2.0.0;

UPDATE `adm_config` SET `desttype` = 'A' WHERE CONVERT( `parameter` USING utf8 ) = 'AVLANGS' LIMIT 1 ;
UPDATE adm_config SET value=REPLACE(value,"array(","") WHERE parameter="AVLANGS";
UPDATE adm_config SET value=REPLACE(value,")","") WHERE parameter="AVLANGS";
INSERT INTO `adm_config` VALUES ('NOTIFICATIONEMAIL', '', 'str', 'P');
INSERT INTO `adm_tabs_fct` ( `tabname` , `fctname` , `label` , `link` , `description` ) VALUES ('configstab', 'notifications', 'appNotificationConfiguration', 'admin/config_notifications.php', 'appNotificationConfigurationTxt');
CREATE TABLE `adm_mail` (`id` int(3) NOT NULL auto_increment,`libelle` varchar(20) collate utf8_unicode_ci NOT NULL default '',`lang` varchar(2) collate utf8_unicode_ci NOT NULL default '',`subject` varchar(100) collate utf8_unicode_ci NOT NULL default '',`message` text collate utf8_unicode_ci NOT NULL,`sender` varchar(60) collate utf8_unicode_ci default NULL,`copy` varchar(200) collate utf8_unicode_ci default NULL,PRIMARY KEY  (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=8 ;
INSERT INTO `adm_mail` VALUES (1, 'validInscription', 'fr', 'Validation de votre inscription sur %site', 'Bonjour,\r\n\r\nFélicitation pour la création de votre compte.\r\nVoici vos informations personnelles de connection :\r\nemail: %email\r\nPassword: %password\r\n\r\nCes paramètres vous donneront accès à votre compte.\r\n\r\nCordialement\r\n%site\r\n%link\r\n\r\n%unsuscribe', NULL, NULL);
INSERT INTO `adm_mail` VALUES (2, 'validInscription', 'en', 'Confirmation of your subscription of %site', 'Hello,\r\n\r\nCongratulations for creating your brand new account. \r\nHere is your personal connection information :\r\nemail: %email\r\nPassword: %password\r\n\r\nThese parameters will give you access to your account.\r\n\r\nBest regards\r\n%site\r\n%link\r\n\r\n%unsuscribe', NULL, NULL);
INSERT INTO `adm_mail` VALUES (3, 'validWidget', 'fr', 'Validation de votre widget sur %site', 'Bonjour,\r\n\r\nFélicitation, votre widget "%description" a été validé par un administrateur.\r\nVous pouvez l''utiliser dès à présent dans votre portail.\r\n\r\nCordialement\r\n%site\r\n%link\r\n\r\n%unsuscribe', NULL, NULL);
INSERT INTO `adm_mail` VALUES (4, 'validWidget', 'en', 'Validation of your widget on %site', 'Hello,\r\n\r\nCongratulations, your widget %description has been validated by an administrator. \r\nFrom now on, you can use it in your portal.\r\n\r\nSee you soon,\r\n%site\r\n%link', NULL, NULL);
INSERT INTO `adm_mail` VALUES (5, 'getPassword', 'fr', 'Mot de passe mit a jour sur %site', 'Bonjour,\r\n\r\nSuite à votre demande, nous avons réinitialisé votre mot de passe. \r\n\r\nVoici vos identifiants:\r\nmail : %email\r\npassword : %password\r\n\r\nCordialement,\r\n%site\r\n%link\r\n\r\n\r\n%unsuscribe', NULL, NULL);
INSERT INTO `adm_mail` VALUES (6, 'getPassword', 'en', 'Password updated on %site', 'Hello,\r\n\r\nYour password has been reset as requested. Here is your new account informations :\r\n\r\nemail:%email\r\npassword:%password\r\n\r\nSee you soon,\r\n%site\r\n%link\r\n\r\n%unsuscribe', NULL, NULL);
ALTER TABLE `dir_rss` ADD `proxy` VARCHAR( 50 ) NOT NULL ,ADD `last_user_access` DATE NOT NULL ;
DELETE FROM adm_headlinks WHERE label IN ('lblHelp','lblAddContent','lblArchive2','lblPersonalize','lblSave','lblConnect','lblDisconnect','lblHelp');
ALTER TABLE `adm_headlinks` ADD `uniq_id` VARCHAR( 30 ) NOT NULL AFTER `id` ,ADD `type` VARCHAR( 30 ) DEFAULT 'link' NOT NULL AFTER `uniq_id` ;
INSERT INTO adm_headlinks (id,uniq_id,type,label,comment,clss,images,fct,status,seq,anonymous,connected,admin) VALUES (1,'lab_hello','label','lblHello','','','','','O',1,0,1,1);
INSERT INTO adm_headlinks (id,uniq_id,type,label,comment,clss,images,fct,status,seq,anonymous,connected,admin) VALUES (2,'link_name','link','%username%','','','','p_network.myprofile()','O',2,0,1,1);
INSERT INTO adm_headlinks (id,uniq_id,type,label,comment,clss,images,fct,status,seq,anonymous,connected,admin) VALUES (3,'lab_par1','label','(','','','','','O',3,0,1,1);
INSERT INTO adm_headlinks (id,uniq_id,type,label,comment,clss,images,fct,status,seq,anonymous,connected,admin) VALUES (4,'link_logout','link','lblDisconnect','lblDisconnect','','','$p.app.logout()','O',4,0,1,1);
INSERT INTO adm_headlinks (id,uniq_id,type,label,comment,clss,images,fct,status,seq,anonymous,connected,admin) VALUES (5,'lab_par2','label',')','','','','','O',5,0,1,1);
INSERT INTO adm_headlinks (id,uniq_id,type,label,comment,clss,images,fct,status,seq,anonymous,connected,admin) VALUES (6,'lab_sep1','label','|','','','','','O',6,0,1,0);
INSERT INTO adm_headlinks (id,uniq_id,type,label,comment,clss,images,fct,status,seq,anonymous,connected,admin) VALUES (7,'link_menu','link','menu','menu','b','ico_menu.gif','$p.app.menu.open()','O',7,1,1,0);
INSERT INTO adm_headlinks (id,uniq_id,type,label,comment,clss,images,fct,status,seq,anonymous,connected,admin) VALUES (8,'lab_sep3','label','|','','','','','O',8,1,0,0);
INSERT INTO adm_headlinks (id,uniq_id,type,label,comment,clss,images,fct,status,seq,anonymous,connected,admin) VALUES (9,'link_save','link','lblSave','lblSavePage','b','ico_menu_disk.gif','$p.app.connection.saveMenu()','O',9,1,0,0);
INSERT INTO adm_headlinks (id,uniq_id,type,label,comment,clss,images,fct,status,seq,anonymous,connected,admin) VALUES (10,'lab_sep4','label','|','','','','','O',10,1,0,0);
INSERT INTO adm_headlinks (id,uniq_id,type,label,comment,clss,images,fct,status,seq,anonymous,connected,admin) VALUES (11,'link_login','link','lblConnect','lblConnect','b','','$p.app.connection.menu()','O',11,1,0,0);
ALTER TABLE `dir_rss` CHANGE `last_user_access` `lastaccess` DATE DEFAULT '0000-00-00' NOT NULL ;
ALTER TABLE `users` CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,CHANGE `picture` `picture` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci,CHANGE `stat` `stat` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
ALTER TABLE `dir_item` CHANGE `name` `name` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ;
ALTER TABLE `dir_rss` CHANGE `lastloadedid` `lastloadedid` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `lastloadedtime` `lastloadedtime` DATETIME DEFAULT '0000-00-00 00:00:00',CHANGE `proxy` `proxy` VARCHAR( 50 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `lastaccess` `lastaccess` DATE DEFAULT '0000-00-00' ;
CREATE TABLE `applications` (`id` SMALLINT NOT NULL AUTO_INCREMENT ,`title` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`description` TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`creation_date` DATE NOT NULL ,`icon` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,`action` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,PRIMARY KEY ( `id` ) );
CREATE TABLE `applications_groups_map` (`application_id` MEDIUMINT NOT NULL ,`group_id` SMALLINT NOT NULL ,PRIMARY KEY ( `application_id` , `group_id` ) );
UPDATE `adm_tabs` SET `id` = '9' WHERE `id` =8 LIMIT 1 ;
UPDATE `adm_tabs` SET `id` = '8' WHERE `id` =7 LIMIT 1 ;
UPDATE `adm_tabs` SET `id` = '7' WHERE `id` =6 LIMIT 1 ;
UPDATE `adm_tabs` SET `id` = '6' WHERE `id` =5 LIMIT 1 ;
INSERT INTO `adm_tabs` ( `id` , `name` , `label` , `type` , `param` ) VALUES ('5', 'applicationtab', 'applications', '4', 'admin/applications.php');
INSERT INTO `adm_tabs_fct` ( `tabname` , `fctname` , `label` , `link` , `description` ) VALUES ('applicationtab', 'applicationsaccess', 'applicationsAccess', 'admin/application_management.php', '');
ALTER TABLE `pages` ADD `icon` VARCHAR( 100 ) ;
ALTER TABLE `dir_cat_item` CHANGE `first` `first` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'Y' NOT NULL;
ALTER TABLE `feed_articles` CHANGE `image` `image` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `video` `video` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `audio` `audio` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `pubdate` `pubdate` VARCHAR( 40 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `uniqid` `uniqid` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
ALTER TABLE `profile` CHANGE `cacheurl` `cacheurl` VARCHAR( 36 ) CHARACTER SET utf8 COLLATE utf8_general_ci,CHANGE `param` `param` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
ALTER TABLE `temp_category` CHANGE `pass` `pass` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci ;
ALTER TABLE `temp_dir_cat_item` CHANGE `first` `first` CHAR( 1 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'Y' NOT NULL ;
ALTER TABLE `temp_dir_item` CHANGE `keywords` `keywords` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `users` CHANGE `pass` `pass` TINYBLOB,CHANGE `description` `description` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci,CHANGE `picture` `picture` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_general_ci,CHANGE `stat` `stat` VARCHAR( 200 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
ALTER TABLE `users_calendar` CHANGE `comments` `comments` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `time` `time` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
ALTER TABLE `users_tasks` CHANGE `comments` `comments` VARCHAR( 250 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci;
ALTER TABLE `widget_addressbook` CHANGE `firstname` `firstname` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `lastname` `lastname` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `email` `email` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `company` `company` VARCHAR( 60 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `func` `func` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `phone1` `phone1` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `phone2` `phone2` VARCHAR( 30 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci,CHANGE `other` `other` VARCHAR( 150 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
ALTER TABLE `module` ADD `old_id` TINYINT UNSIGNED;
ALTER TABLE `profile` ADD `old_id` TINYINT UNSIGNED;
UPDATE profile SET old_id=id;
UPDATE module SET old_id=profile_id;
ALTER TABLE `profile` DROP `id` ;
ALTER TABLE `profile` DROP INDEX `ind_profile` ;
ALTER TABLE `profile` ADD `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST ;
ALTER TABLE `module` CHANGE `profile_id` `profile_id` INT( 3 ) UNSIGNED DEFAULT '0' NOT NULL;
ALTER TABLE `module` DROP INDEX `ind_module` ;
ALTER TABLE `module` DROP INDEX `module_unique` ;
UPDATE module,profile SET profile_id=profile.id WHERE module.user_id=profile.user_id AND module.old_id=profile.old_id;
ALTER TABLE `module` ADD INDEX `ind_module` ( `profile_id` , `uniq` );
INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('menuDefaultStatus',1,'int','J');
UPDATE adm_config SET value='2' WHERE parameter='loadlatestpageonstart';
INSERT INTO `adm_tabs_fct` VALUES ('userstab', 'usersinfos', 'userInfos', 'admin/users_infos_config.php', 'userInfos');
CREATE TABLE `adm_userinfo` ( `id` int(3) NOT NULL auto_increment, `label` varchar(100) collate utf8_unicode_ci NOT NULL default '', `type` varchar(50) collate utf8_unicode_ci NOT NULL default '', `options` varchar(200) collate utf8_unicode_ci default NULL, `mandatory` binary(1) NOT NULL default '', `editable` binary(1) NOT NULL default '', `public` binary(1) NOT NULL default '', PRIMARY KEY  (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `users_info` ( `user_id` int(11) NOT NULL default '0', `info_id` int(11) NOT NULL default '0', `parameters` text character set utf8, `ispublic` binary(1) NOT NULL default '', PRIMARY KEY  (`user_id`,`info_id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE `users` CHANGE `lastconnect_date` `lastconnect_date` DATETIME DEFAULT '0000-00-00' NOT NULL;
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('useChat', 'false', 'int', 'J');
CREATE TABLE `widget_html` (`id` int(10) unsigned NOT NULL auto_increment,`user_id` mediumint(8) unsigned NOT NULL default '0',`content` text collate utf8_unicode_ci,PRIMARY KEY  (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE `users` ADD `activity` CHAR( 1 ) DEFAULT 'x' NOT NULL AFTER `stat` ;
INSERT INTO `adm_config` (`parameter` ,`value` ,`datatype` ,`desttype`) VALUES ('addPagePermission', 'true', 'int', 'J');
INSERT INTO `adm_config` (`parameter` ,`value` ,`datatype` ,`desttype`) VALUES ('displayAllLanguageModules', 'true', 'int', 'J');
INSERT INTO `adm_config` (`parameter` ,`value` ,`datatype` ,`desttype`) VALUES ('userChangePermission', 'true', 'int', 'J');
INSERT INTO `adm_config` (`parameter` ,`value` ,`datatype` ,`desttype`) VALUES ('passwordChangePermission', 'true', 'int', 'J');
INSERT INTO `adm_config` (`parameter` ,`value` ,`datatype` ,`desttype`) VALUES ('showModuleRefresh', 'true', 'int', 'J');
INSERT INTO `adm_config` (`parameter` ,`value` ,`datatype` ,`desttype`) VALUES ('showModuleClose', 'true', 'int', 'J');
INSERT INTO `adm_config` (`parameter` ,`value` ,`datatype` ,`desttype`) VALUES ('showModuleConfigure', 'true', 'int', 'J');
INSERT INTO `adm_config` (`parameter` ,`value` ,`datatype` ,`desttype`) VALUES ('showModuleMinimize', 'true', 'int', 'J');
INSERT INTO `adm_config` (`parameter` ,`value` ,`datatype` ,`desttype`) VALUES ('showModuleTitle', 'true', 'int', 'J');
ALTER TABLE `module` ADD `feed_id` MEDIUMINT UNSIGNED DEFAULT '0' NOT NULL ;
ALTER TABLE `feed_articles` ADD `source` VARCHAR( 255 ) NULL ;
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('getNbArticleOfArchive', 'true', 'int', 'P');
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('usePrivateModules', 'true', 'int', 'J');
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('useRssDelete', 'false', 'int', 'J');
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('displayrsssource', 'false', 'int', 'J');
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('displayrssimages', '2', 'int', 'J');
UPDATE `adm_config` SET `value` = '1' WHERE CONVERT( `parameter` USING utf8 ) = 'displayrssdesc' LIMIT 1 ;
CREATE TABLE `adm_tabs_map` ( `user_id` tinyint(3) NOT NULL default '0', `tab_id` tinyint(3) NOT NULL default '0', PRIMARY KEY  (`user_id`,`tab_id`) ) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
UPDATE `adm_config` SET value='0' WHERE parameter='showHomeBar';
ALTER TABLE `profile` ADD `status` TINYINT( 1 ) DEFAULT '0' NOT NULL;
ALTER TABLE `profile` ADD `page_id` TINYINT( 3 ) UNSIGNED DEFAULT NULL;
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('rand', 'abc', 'str', 'P');
INSERT INTO `adm_config` VALUES ('displayThemeSelector', 'true', 'int', 'J');
INSERT INTO `adm_config` VALUES ('displayPublicPages', 'true', 'int', 'J');
ALTER TABLE `dir_cat_item` ADD PRIMARY KEY ( `item_id` , `category_id` );
ALTER TABLE `search_index` ADD INDEX `ind_searchindex_kwid` ( `kw_id` );
ALTER TABLE `search_index` ADD INDEX `ind_searchindex_itemid` ( `item_id` );
ALTER TABLE `users_group_category_map` ADD INDEX `ind_usergroupcategorymap_groupid` ( `group_id` );
ALTER TABLE `users_group_category_map` ADD INDEX `ind_usergroupcategorymap_catid` ( `category_id` );
ALTER TABLE `users_group_map` ADD INDEX `ind_usersgroupmap_groupid` ( `user_id` );
ALTER TABLE `users_group_map` ADD INDEX `ind_usersgroupmap_catid` ( `group_id` );
ALTER TABLE `users` ADD `keywords` VARCHAR( 255 ) ;
INSERT INTO `adm_config` VALUES ('tabType', 'navigator', 'str', 'J');
UPDATE `dir_item` SET `name` = 'RSS',`description` = '' WHERE `id` =86;
ALTER TABLE `feed_articles` ADD `loaddate` DATE AFTER `pubdate`;
INSERT INTO adm_tabs_map (user_id, tab_id) SELECT users.id,adm_tabs.id FROM users,adm_tabs WHERE users.typ='A';

> 2.0.1;

DELETE FROM adm_tabs_map;
INSERT INTO adm_tabs_map (user_id, tab_id) SELECT users.id,adm_tabs.id FROM users,adm_tabs WHERE users.typ='A';
UPDATE `adm_config` SET value='false' WHERE parameter='getNbArticleOfArchive';
ALTER TABLE `dir_item` ADD INDEX `idx_status` ( `status` );
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('themeList', '"default"', 'arr', 'J');
UPDATE `adm_config` SET `value` = REPLACE(value,'p_tabs.selId','$p.app.tabs.selId') WHERE  CONVERT(`parameter` USING utf8) = 'option';

> 2.0.2;

INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('displayOnlyPublicPagesForTheUser', 'true', 'int', 'A');
CREATE TABLE `translation` (  `id` int(10) unsigned NOT NULL auto_increment,  `label` varchar(255) NOT NULL,  `message` text,  `lang` varchar(2) NOT NULL,  `status` enum('translation','selected','deleted') default 'translation',  `translation_status` enum('pending','checking','translated') default 'pending',`typefile` int(10) unsigned NOT NULL, `usage_label` int(10) unsigned NOT NULL, `modifier` enum('maintener','admin','user') default NULL, `last_updated` int(11) default NULL,PRIMARY KEY  (`id`),  UNIQUE KEY `un_label_lang_usage` (`label`,`lang`,`usage_label`),  KEY `idx_label` (`label`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
CREATE TABLE `app_contact` (  `dest_id` int(10) unsigned NOT NULL auto_increment,  `user_id` int(11) NOT NULL,  `email` varchar(255) collate utf8_unicode_ci NOT NULL,   `texte` text collate utf8_unicode_ci,   `modifdate` datetime default NULL,    `name` varchar(255) collate utf8_unicode_ci default NULL,    `titre` varchar(255) collate utf8_unicode_ci default NULL,  `statut` varchar(255) collate utf8_unicode_ci default NULL, PRIMARY KEY  (`dest_id`)) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
UPDATE `adm_config` SET datatype='int' WHERE parameter='useRssDelete';
UPDATE `adm_config` SET datatype='int' WHERE parameter='displayrsssource';
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('showTabOptions', 'true', 'int', 'J');
INSERT INTO `adm_config` ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('moveWidgetsInTabs', 'true', 'int', 'J');
INSERT INTO `adm_mail`(libelle,lang,subject,message) VALUES ( "emailConfirmation", "fr", "Confirmation de votre inscription sur %site", "Bonjour,\r\n\r\nNous avons bien enregistré vos paramètres personnels mais votre compte n'est pas encore activé.\r\nPour valider votre compte, veuillez cliquer sur le lien suivant : %linkportal/login.php?id=%id&chk=%key\r\n\r\nMerci et à bientôt sur %site.\r\n%link\r\n\r\n%unsuscribe");
INSERT INTO `adm_mail`(libelle,lang,subject,message) VALUES ( "emailConfirmation", "en", "Confirmation of your subscription on %site", "Hello,\r\n\r\nWe have take in account your personal parameters but your account is not yet validated.\r\nTo validate your account, please click on the following link : %linkportal/login.php?id=%id&chk=%key\r\n\r\nSee you soon on %site.\r\n%link\r\n\r\n%unsuscribe");

> 2.0.3;

UPDATE adm_headlinks SET images='' WHERE uniq_id = 'link_menu';
CREATE TABLE `users_control` (`login` varchar(60) NOT NULL, `ip` varchar(11) collate utf8_unicode_ci NOT NULL, `date` datetime NOT NULL, `number_of_try` tinyint NOT NULL DEFAULT 0, PRIMARY KEY  (`login`,`ip`,`date`));
INSERT INTO adm_config ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('numberOfTry',0,'int','P');
INSERT INTO adm_config ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('connectionDateRange',3600,'int','P');
INSERT INTO adm_config ( `parameter` , `value` , `datatype` , `desttype` ) VALUES ('captcha','false','int','A');
CREATE TABLE `captcha_codes` (`id` varchar(60) NOT NULL, `code` varchar(11) collate utf8_unicode_ci NOT NULL, PRIMARY KEY  (`id`));

> 2.0.4;

INSERT INTO `adm_mail`(libelle,lang,subject,message) VALUES ( "getNewPassword", "fr","Obtenir un nouveau mot de passe sur %site", "Bonjour,\r\n\r\nSuite à votre demande, nous vous envoyons ce lien pour réinitialiser votre mot de passe.\r\n%setnewpwd\r\n\r\nCordialement\r\n%link\n%site\r\n\r\n%unsuscribe");
INSERT INTO `adm_mail`(libelle,lang,subject,message) VALUES ( "getNewPassword", "en","Get a new password on %site", "Hello,\r\n\r\nFollowing your request\r\n\r\nWe send you this link to change your password.\r\n\r\n%setnewpwd\r\n\r\nBest regards\n%link\r\n%site\r\n\r\n%unsuscribe");
INSERT INTO `adm_mail`(libelle,lang,subject,message) VALUES ( "getNewPassword", "de","Get a new password on %site", "Hello,\r\n\r\nFollowing your request\r\n\r\nWe send you this link to change your password.\r\n\r\n%setnewpwd\r\n\r\nBest regards\n%link\r\n%site\r\n\r\n%unsuscribe");
INSERT INTO `adm_mail`(libelle,lang,subject,message) VALUES ( "getNewPassword", "es","Get a new password on %site", "Hello,\r\n\r\nFollowing your request\r\n\r\nWe send you this link to change your password.\r\n\r\n%setnewpwd\r\n\r\nBest regards\n%link\r\n%site\r\n\r\n%unsuscribe");
INSERT INTO `adm_mail`(libelle,lang,subject,message) VALUES ( "getNewPassword", "it","Get a new password on %site", "Hello,\r\n\r\nFollowing your request\r\n\r\nWe send you this link to change your password.\r\n\r\n%setnewpwd\r\n\r\nBest regards\n%link\r\n%site\r\n\r\n%unsuscribe");
INSERT INTO `adm_config` VALUES ('MD5KEY', '', 'str', 'A');
ALTER TABLE module CHANGE variables variables TEXT CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;
INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('SESSION','myhomepage','str','P');
UPDATE adm_config SET desttype='P' WHERE parameter='MD5KEY';

> 2.0.5;

INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('tabsCanBeRenamed','true','int','J');
INSERT INTO `adm_config` (`parameter` ,`value` ,`datatype` ,`desttype` ) VALUES ('widgetTitleUpdatable', 'true', 'int', 'J');
UPDATE `adm_config` SET value='false' WHERE parameter='showicon';

> 2.1.0a1;

DELETE FROM adm_config WHERE parameter='MD5KEY';
ALTER TABLE `adm_config` ADD `category` VARCHAR( 20 );
UPDATE adm_config SET category = 'yourApplication' WHERE parameter IN ('APPNAME','IPADD','LOCALFOLDER','USERMODULE');
UPDATE adm_config SET category = 'dbConnection' WHERE parameter IN ('server','login','db','useproxy','proxyserver','proxyport','proxypacfile');
UPDATE adm_config SET category = 'adminInterface' WHERE parameter IN ('usegroup');
UPDATE adm_config SET category = 'thePortals' WHERE parameter IN ('loadlatestpageonstart','defaultmode','menuDefaultStatus','accounttype','menuposition','moduleAlignDefault','maxPageNb','showHomeBar','blockedModulePreventPageRemoval','addPagePermission','footer','useconditions','passwordChangePermission','debugmode','numberOfTry','userChangePermission','captcha','connectionDateRange');
UPDATE adm_config SET category = 'theFeeds' WHERE parameter IN ('displayrssdesc','displayrsssource','displayrssimages');
UPDATE adm_config SET category = 'theModules' WHERE parameter IN ('showicon','useoverview','maxModNb','txtnote','rssrefreshdelay','displayAllLanguageModules','showModuleClose','showModuleRefresh','showModuleConfigure','showModuleMinimize','showModuleTitle');
UPDATE adm_config SET category = 'emailSending' WHERE parameter IN ('supportemail','notificationemail','friendemail');
UPDATE adm_config SET desttype='A' WHERE parameter='NOTIFICATIONEMAIL';
UPDATE adm_config SET desttype='A' WHERE parameter='APPNAME';
UPDATE adm_config SET desttype='A' WHERE parameter='defaultmode';
CREATE TABLE `adm_group_map` (`user_id` INT( 3 ) NOT NULL ,`group_id` INT( 3 ) NOT NULL ,INDEX ( `user_id` , `group_id` ), UNIQUE KEY `users_group`(`user_id`,`group_id`)) ENGINE = MYISAM DEFAULT CHARSET=utf8;
ALTER TABLE `users` ADD `statdate` DATE NOT NULL AFTER `stat` ;
ALTER TABLE `module` ADD `shared` VARCHAR( 16 ) ;
ALTER TABLE `profile` ADD `shared` VARCHAR( 16 ) NOT NULL ;
UPDATE `adm_config` SET `desttype` = 'A' WHERE CONVERT( `parameter` USING utf8 ) = 'usereader' LIMIT 1 ;
UPDATE adm_headlinks SET fct="$p.network.myprofile()" WHERE fct="p_network.myprofile()";
UPDATE adm_headlinks SET comment='Search' WHERE comment='search';
INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('displayRssDate','true','int','J');
INSERT INTO `adm_config` (`parameter` ,`value` ,`datatype` ,`desttype` ) VALUES ('useNetwork', 'false', 'int', 'J');
INSERT INTO `adm_config` (`parameter` ,`value` ,`datatype` ,`desttype` ) VALUES ('useNotebook', 'false', 'int', 'J');
ALTER TABLE `adm_headlinks` ADD `position` VARCHAR( 10 ) DEFAULT 'right' NOT NULL ;
UPDATE adm_headlinks SET seq=38,position='left' WHERE uniq_id='lab_sep3';
UPDATE adm_headlinks SET label="menu",comment="menu",seq=40,position="left",clss='' WHERE uniq_id="link_menu";
INSERT INTO `adm_headlinks` ( `id` , `uniq_id` , `type` , `label` , `comment` , `clss` , `images` , `fct` , `status` , `seq` , `anonymous` , `connected` , `admin` , `position` ) VALUES ('0', 'link_widgets', 'link', 'lblAddContent', 'lblAddContent', '', 'ico_menu_add.gif', '$p.app.menu.widget.open()', 'O', '9', '1', '1', '0', 'left'), ('0', 'lab_sep10', 'label', '|', '', '', '', '', 'O', '10', '1', '1', '0', 'left');
CREATE TABLE `dir_item_external` (`item_id` int(10) unsigned NOT NULL,`source` text collate utf8_unicode_ci NOT NULL, `xmlmodule` text collate utf8_unicode_ci NOT NULL,  `url` varchar(255) collate utf8_unicode_ci NOT NULL,  `last_updated` datetime NOT NULL,  `status` enum('quarantine','validated','deleted') collate utf8_unicode_ci NOT NULL,  KEY `item_id` (`item_id`) ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
CREATE TABLE `dir_item_external_language` (`item_id` int(10) NOT NULL,`lang` varchar(3) collate utf8_unicode_ci NOT NULL,   `url` varchar(255) collate utf8_unicode_ci NOT NULL,`source` text collate utf8_unicode_ci NOT NULL,`last_updated` datetime NOT NULL,   KEY `item_id` (`item_id`,`lang`) ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
INSERT INTO `adm_config` (`parameter` ,`value` ,`datatype` ,`desttype` ) VALUES ('uploadMaxFileSize', '30000000', 'int', 'P');
INSERT INTO `adm_config` (`parameter` ,`value` ,`datatype` ,`desttype` ) VALUES ('uploadAllowedExtensions', '.pdf,.doc,.txt,.jpg,.png,.bmp,.gif,.xls,.ppt,.pps,.odt,.rtf', 'str', 'P');
UPDATE `adm_tabs` SET type=5;
INSERT INTO `adm_tabs` VALUES (8, 'supporttab', 'Support', 5, 'admin/support.php');
INSERT INTO `adm_tabs_fct` ( `tabname` , `fctname` , `label` , `link` , `description` ) VALUES ('supporttab', 'support', 'supportMgmt', 'admin/support_main.php', 'supportMgmtTxt');
INSERT INTO `adm_tabs_fct` ( `tabname` , `fctname` , `label` , `link` , `description` ) VALUES ('statstab', 'modulestats', 'moduleStats', 'admin/stats_modules.php', 'moduleStatsTxt');
ALTER TABLE `pages` ADD `removable` TINYINT( 1 ) UNSIGNED NOT NULL default '1';
ALTER TABLE `profile` ADD `removable` TINYINT( 1 ) UNSIGNED NOT NULL default '1';
UPDATE `pages` SET removable='1' WHERE id=1;
INSERT INTO `adm_config` (`parameter` ,`value` ,`datatype` ,`desttype` ) VALUES ('restrictOnExistingTags', 'false', 'int', 'J');
ALTER TABLE dir_item ADD COLUMN views VARCHAR(255);
ALTER TABLE temp_dir_item ADD COLUMN views VARCHAR(255);
ALTER TABLE dir_item_external ADD COLUMN view VARCHAR(255);
ALTER TABLE dir_item_external ADD UNIQUE un_item_view(item_id,view);
CREATE TABLE `temp_dir_item_external` (`item_id` int(10) unsigned NOT NULL, `source` text collate utf8_unicode_ci NOT NULL, `xmlmodule` text collate utf8_unicode_ci NOT NULL, `url` varchar(255) collate utf8_unicode_ci NOT NULL, `last_updated` datetime NOT NULL, `status` enum('quarantine','validated','deleted') collate utf8_unicode_ci NOT NULL, `view` varchar(255) collate utf8_unicode_ci default NULL, UNIQUE KEY `un_item_view` (`item_id`,`view`), KEY `item_id` (`item_id`)) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE `module` ADD COLUMN currentview VARCHAR(255);
UPDATE adm_headlinks SET seq=seq+1 WHERE seq>2;
INSERT INTO `adm_headlinks` ( `id` , `uniq_id` , `type` , `label` , `comment` , `clss` , `images` , `fct` , `status` , `seq` , `anonymous` , `connected` , `admin` , `position` ) VALUES ('20', 'span_availability', 'label', '', '', '', '', '', 'O', '3', '0', '1', '0', 'right');
ALTER TABLE `dir_rss` ADD `iconid` VARCHAR( 100 ) NOT NULL;
UPDATE `adm_mail` SET `message` = "Bonjour,\r\n\r\nFélicitation pour la création de votre compte.\r\nVoici vos informations personnelles de connection :\r\nemail: %email\r\nPassword: %password\r\n\r\nCes paramètres vous donneront accès à votre compte.\r\n\r\nCordialement\r\n%site\r\n%link\r\n\r\n%unsubscribe" WHERE `id` = 1;
UPDATE `adm_mail` SET `message` = "Hello,\r\n\r\nCongratulations for creating your brand new account. \r\nHere is your personal connection information :\r\nemail: %email\r\nPassword: %password\r\n\r\nThese parameters will give you access to your account.\r\n\r\nBest regards\r\n%site\r\n%link\r\n\r\n%unsubscribe" WHERE `id` = 2;
UPDATE `adm_mail` SET `message` = 'Bonjour,\r\n\r\nFélicitation, votre widget "%description" a été validé par un administrateur.\r\nVous pouvez l''utiliser dès à présent dans votre portail.\r\n\r\nCordialement\r\n%site\r\n%link\r\n\r\n%unsubscribe' WHERE `id` = 3;
UPDATE `adm_mail` SET `message` = "Hello,\r\n\r\nCongratulations, your widget %description has been validated by an administrator. \r\nFrom now on, you can use it in your portal.\r\n\r\nSee you soon,\r\n%site\r\n%link\r\n\r\n%unsubscribe" WHERE `id` = 4;
UPDATE `adm_mail` SET `message` = "Bonjour,\r\n\r\nSuite à votre demande, nous avons réinitialisé votre mot de passe. \r\n\r\nVoici vos identifiants:\r\nmail : %email\r\npassword : %password\r\n\r\nCordialement,\r\n%site\r\n%link\r\n\r\n\r\n%unsubscribe" WHERE `id` = 5;
UPDATE `adm_mail` SET `message` = "Hello,\r\n\r\nYour password has been reset as requested. Here is your new account informations :\r\n\r\nemail:%email\r\npassword:%password\r\n\r\nSee you soon,\r\n%site\r\n%link\r\n\r\n%unsubscribe" WHERE `id` = 6;
UPDATE `adm_mail` SET `message` = "Bonjour,\r\n\r\nNous avons bien enregistré vos paramètres personnels mais votre compte n'est pas encore activé.\r\nPour valider votre compte, veuillez cliquer sur le lien suivant : %linkportal/login.php?id=%id&chk=%key\r\n\r\nMerci et à bientôt sur %site.\r\n%link\r\n\r\n%unsubscribe" WHERE `id` = 8;
UPDATE `adm_mail` SET `message` = "Hello,\r\n\r\nWe have take in account your personal parameters but your account is not yet validated.\r\nTo validate your account, please click on the following link : %linkportal/login.php?id=%id&chk=%key\r\n\r\nSee you soon on %site.\r\n%link\r\n\r\n%unsubscribe" WHERE `id` = 9;
UPDATE `adm_mail` SET `message` = "Bonjour,\r\n\r\nSuite à votre demande, nous vous envoyons ce lien pour réinitialiser votre mot de passe.\r\n%setnewpwd\r\n\r\nCordialement\r\n%link\n%site\r\n\r\n%unsubscribe" WHERE `id` = 10;
UPDATE `adm_mail` SET `message` = "Hello,\r\n\r\nFollowing your request\r\n\r\nWe send you this link to change your password.\r\n\r\n%setnewpwd\r\n\r\nBest regards\n%link\r\n%site\r\n\r\n%unsubscribe" WHERE `id` = 11;
UPDATE `adm_mail` SET `message` = "Hello,\r\n\r\nFollowing your request\r\n\r\nWe send you this link to change your password.\r\n\r\n%setnewpwd\r\n\r\nBest regards\n%link\r\n%site\r\n\r\n%unsubscribe" WHERE `id` = 12;
UPDATE `adm_mail` SET `message` = "Hello,\r\n\r\nFollowing your request\r\n\r\nWe send you this link to change your password.\r\n\r\n%setnewpwd\r\n\r\nBest regards\n%link\r\n%site\r\n\r\n%unsubscribe" WHERE `id` = 13;
UPDATE `adm_mail` SET `message` = "Hello,\r\n\r\nFollowing your request\r\n\r\nWe send you this link to change your password.\r\n\r\n%setnewpwd\r\n\r\nBest regards\n%link\r\n%site\r\n\r\n%unsubscribe" WHERE `id` = 14;
DELETE FROM `adm_config` WHERE parameter='apname';
ALTER TABLE `dir_item` ADD `icon` VARCHAR( 255 ) NOT NULL;
ALTER TABLE temp_dir_item_external ADD COLUMN type CHAR(1);
ALTER TABLE dir_item_external ADD COLUMN type CHAR(1);
ALTER TABLE dir_item_external_language ADD COLUMN viewtype VARCHAR(255) after source;
ALTER TABLE `dir_item_external_language` DROP INDEX item_id;
ALTER TABLE dir_item_external_language ADD UNIQUE item_id(item_id,lang,viewtype);
CREATE TABLE `temp_dir_item_external_language` ( `item_id` int(10) NOT NULL,  `lang` varchar(3) collate utf8_unicode_ci NOT NULL,  `url` varchar(255) collate utf8_unicode_ci NOT NULL,  `source` text collate utf8_unicode_ci NOT NULL,  `viewtype` varchar(255) collate utf8_unicode_ci default NULL,  `view` varchar(255) collate utf8_unicode_ci NOT NULL,  `last_updated` datetime NOT NULL,  UNIQUE KEY `item_id` (`item_id`,`lang`,`viewtype`) ) DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
ALTER TABLE `dir_item`  ADD COLUMN l10n varchar(255);
ALTER TABLE `temp_dir_item`  ADD COLUMN l10n varchar(255);
UPDATE `adm_config` SET desttype='A' WHERE parameter='debugmode';
ALTER TABLE `temp_dir_item_external` CHANGE `item_id` `item_id` INT( 10 ) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE temp_dir_item ADD COLUMN id_dir_item mediumint(9);
ALTER TABLE temp_dir_item_external ADD COLUMN id_dir_item mediumint(9);
UPDATE `dir_item` SET `views` = 'home,canvas' WHERE `id` =112;

> 2.1.0rc;

INSERT INTO `adm_config` (`parameter` ,`value` ,`datatype` ,`desttype` ) VALUES ('useRating', 'false', 'int', 'J');
ALTER TABLE `feed_articles_read` ADD COLUMN `article_rating` int(1) default NULL;
ALTER TABLE `feed_articles_read` ADD COLUMN `rating_timestamp` int(11) default NULL;
ALTER TABLE `dir_item_external_language` ADD COLUMN `view` VARCHAR( 255 ) NOT NULL AFTER `viewtype`;
ALTER TABLE `dir_item_external_language` DROP INDEX `item_id`;
ALTER TABLE `dir_item_external_language` ADD UNIQUE `item_id` ( `item_id` , `lang` , `view` );
ALTER TABLE `temp_dir_item_external_language` DROP INDEX `item_id`;
ALTER TABLE `temp_dir_item_external_language`  ADD UNIQUE `item_id` ( `item_id` , `lang` , `view` );
INSERT INTO `adm_config` (`parameter` ,`value` ,`datatype` ,`desttype` ) VALUES ('allowGetWidgetOnMySite', 'true', 'int', 'J');
DELETE FROM `adm_headlinks` WHERE uniq_id='lab_hello';
ALTER TABLE `dir_item_external_language` MODIFY COLUMN lang VARCHAR(6);
INSERT INTO `adm_config` (`parameter` ,`value` ,`datatype` ,`desttype` ) VALUES ('inactiveAccountsDelay', '90', 'int', 'P');
ALTER TABLE `dir_item_external_language` TYPE = MYISAM;
ALTER TABLE users MODIFY lang VARCHAR(5) NOT NULL DEFAULT 'en';
ALTER TABLE temp_dir_item_external_language MODIFY COLUMN  lang VARCHAR(6);
ALTER TABLE temp_dir_item_external_language ADD COLUMN params TEXT AFTER source;
ALTER TABLE dir_item_external_language ADD COLUMN params TEXT AFTER source;
ALTER TABLE `users_mail` CHANGE `pass` `pass` BLOB DEFAULT NULL;
ALTER TABLE `temp_dir_item` CHANGE `name` `name` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,CHANGE `lang` `lang` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT 'en' NOT NULL;
ALTER TABLE `dir_item` CHANGE `name` `name` VARCHAR( 75 ) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL ,CHANGE `lang` `lang` VARCHAR( 5 ) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT 'en' NOT NULL;
ALTER TABLE translation MODIFY COLUMN lang VARCHAR(5) NOT NULL;
ALTER TABLE adm_mail MODIFY COLUMN lang VARCHAR(5) NOT NULL DEFAULT '';
ALTER TABLE profile MODIFY COLUMN lang VARCHAR(5) NOT NULL DEFAULT 'en';

> 2.1.0;

ALTER TABLE `dir_category` ADD `secured_quantity` SMALLINT UNSIGNED DEFAULT '0' NOT NULL ;
ALTER TABLE `temp_category` ADD `secured` TINYINT UNSIGNED DEFAULT '0' NOT NULL ;
ALTER TABLE dir_item MODIFY COLUMN defvar TEXT;
ALTER TABLE temp_dir_item MODIFY COLUMN defvar TEXT;

> 2.1.1;

UPDATE adm_config SET value='"en","fr","de"' WHERE parameter="AVLANGS";
INSERT INTO dir_category (name,parent_id,typ,path,quantity,updated,lang) VALUES('Widgets -de',0,'O','',1,'Y','de');
ALTER TABLE `dir_rss` ADD `http_last_modified` VARCHAR( 50 ) NOT NULL AFTER `lastloadedtime` ;

> 2.1.2;

