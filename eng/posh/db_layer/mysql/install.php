<?php
/*
	Copyright (c) PORTANEO.

	This file is part of POSH (Portaneo Open Source Homepage) http://sourceforge.net/projects/posh/.

	POSH is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version

	POSH is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with Posh.  If not, see <http://www.gnu.org/licenses/>.
*/
$install_createDB="CREATE DATABASE %s DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ";
$install_listExistingTables="SHOW TABLES like 'adm_config' ";
$install_bugcorrection101 = "UPDATE adm_config AS v1, adm_config AS v2 SET v1.value=v2.value WHERE v1.parameter='POSHVERSION' AND v2.parameter='INSTALLINGPOSHVE' AND v2.value='1.0.1';";
$install_getPreviousVersion="SELECT value FROM adm_config WHERE parameter='POSHVERSION' ";
$install_setServer = "UPDATE adm_config set value=%s WHERE parameter='SERVER' ";
$install_setLogin = "UPDATE adm_config set value=%s WHERE parameter='LOGIN' ";
$install_setPass = "UPDATE adm_config set value=%s WHERE parameter='PASS' ";
$install_setDB = "UPDATE adm_config set value=%s WHERE parameter='DB' ";
$install_setPoshVersion = "UPDATE adm_config set value=%s WHERE parameter='POSHVERSION' ";
$install_updInstallingPoshVersion = "UPDATE adm_config set value=%s WHERE parameter LIKE %s ";
$install_insertInstallingPoshVersion = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('INSTALLINGPOSHVERSION',%s,'str','P') ";
$install_updDBType = "UPDATE adm_config set value=%s WHERE parameter='DBTYPE' ";
$install_insertDBType = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('DBTYPE',%s,'str','P') ";
$install_updInstallType = "UPDATE adm_config set value=%s WHERE parameter='INSTALLTYPE' ";
$install_insertInstallType = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('INSTALLTYPE',%s,'int','P') ";
$install_setStep3 = "UPDATE adm_config set value='3' WHERE parameter='INSTALLATIONSTEP' ";
$install_insertStep3 = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('INSTALLATIONSTEP','3','str','P') ";
$install_getFirstTheme = "SELECT name FROM adm_themes ORDER BY seq LIMIT 1 ";
$install_setApname = 'UPDATE adm_config SET value=%s WHERE parameter="apname" ';
$install_setAppname = 'UPDATE adm_config SET value=%s WHERE parameter="APPNAME" ';
$install_setUseGroup = 'UPDATE adm_config SET value=%s WHERE parameter="useGroup" ';
$install_setLocalFolder = 'UPDATE adm_config SET value=%s WHERE parameter="LOCALFOLDER" ';
$install_setUserModule = 'UPDATE adm_config SET value=%s WHERE parameter="USERMODULE" ';
$install_setUserModuleJs = 'UPDATE adm_config SET value=%s WHERE parameter="userModuleJs" ';
$install_setStep4 = "UPDATE adm_config SET value='4' WHERE parameter='INSTALLATIONSTEP' ";
$install_setMenuPosition = "UPDATE adm_config SET value=%s WHERE parameter='menuposition' ";
$install_setDefTheme = "UPDATE adm_themes SET name='classic_blue' WHERE seq=0 ";
$install_getRssModules = "SELECT user_id,profile_id,uniq,variables FROM module,dir_item WHERE format='R' AND item_id=dir_item.id ";
$install_getIdOfFeed = "SELECT id FROM dir_rss WHERE url=%s ";
$install_initFeed = "INSERT INTO dir_rss (url,icon,lastloadedid,lastloadedtime) VALUES (%s,0,'','0000-00-00 00:00:00') ";
$install_setRssVars = "UPDATE module SET variables=%s WHERE user_id=%u AND profile_id=%u AND uniq=%u ";
$install_getRssInPages = "SELECT page_id,uniq,variables FROM pages_module,dir_item WHERE format='R' AND item_id=dir_item.id ";
$install_setRssVarInPages = "UPDATE pages_module SET variables=%s WHERE page_id=%u AND uniq=%u ";
$install_getRssDirModules = "SELECT id,defvar FROM dir_item WHERE format='R' ";
$install_setRssVarInDir = "UPDATE dir_item SET defvar=%s WHERE id=%u ";
$install_setStep5 = "UPDATE adm_config SET value='5' WHERE parameter='INSTALLATIONSTEP' ";
$install_getAdminUsers = "SELECT username FROM users WHERE typ='A' ";
$install_addAdminUser = "INSERT INTO users(username,pass,long_name,typ,lastconnect_date,md5pass, md5user,lang) VALUES (%s,AES_ENCRYPT(%s,%s),'administrator','A',CURRENT_DATE,MD5(%s),%s,%s)";
$install_setKey = "UPDATE adm_config SET value=%s WHERE parameter='KEY' ";
$install_removeInstallationStep = "DELETE FROM adm_config WHERE parameter='INSTALLATIONSTEP' ";
$install_setInstalledVersion = "UPDATE adm_config AS ac1, adm_config AS ac2 SET ac1.value=ac2.value WHERE ac1.parameter='POSHVERSION' AND ac2.parameter LIKE 'INSTALLINGPOSHVE%' ";
$install_getParam = "SELECT value FROM adm_config WHERE parameter=%s ";
$install_checkIfModuleExists = "SELECT name FROM dir_item WHERE id=%u AND status='O' ";
$install_getAllParams = "SELECT parameter,value,datatype,desttype FROM adm_config ";
$install_getHeadLinks = "
    SELECT  id,
            uniq_id,
            type,
            label,
            comment,
            clss,
            images,
            fct,
            status,
            seq,
            anonymous,
            connected,
            admin,
            position
    FROM    adm_headlinks
    WHERE   status='O'
    ORDER BY seq ASC
";
$install_getHeadLink = "
    SELECT  label,
            comment,
            clss,
            images,
            fct,
            anonymous,
            connected,
            admin,
            position
    FROM    adm_headlinks
    WHERE   status='O'
        AND id=%u
        AND seq>0
    ORDER BY seq
";
$install_setUseproxy = 'UPDATE adm_config SET value=%s WHERE parameter="useproxy" ';
$install_activateEnterprisePlugin = "INSERT INTO `adm_plugins` VALUES ('Enterprise main tools', 'enterprise_edition/enterprise_edition.php', 'no', 'O') ";
$install_updateEnterpriseVersion = "UPDATE adm_config SET value=%s WHERE parameter='PEEVERSION'";
$install_updateFidField = "UPDATE module SET feed_id=%u WHERE user_id=%u AND profile_id=%u AND uniq=%u";
$install_getMaxAdmTabsId = "SELECT MAX(id) as id FROM adm_tabs";
$install_rootUserTabs = "INSERT INTO adm_tabs_map (user_id, tab_id) SELECT users.id,adm_tabs.id FROM users,adm_tabs WHERE users.typ='A'";
$install_setmd5Key = "UPDATE adm_config SET value=%s WHERE parameter='MD5KEY' ";
$install_replaceIcoExtInModule = "
	UPDATE	module
	SET		variables=REPLACE(variables,'.ico','')
";
$install_replaceIcoExtInPages = "
	UPDATE	pages_module
	SET		variables=REPLACE(variables,'.ico','')
";
$install_getUsersInfo = "SELECT id, long_name, picture FROM users WHERE picture LIKE '../cache%'";
$install_setUsersPictureUrl = "UPDATE users SET picture=%s WHERE id=%u";
$install_getWidgetIdWithoutIcon = "SELECT id FROM dir_item WHERE icon IS NULL OR icon=''";
$install_updateWidgetIcon = "UPDATE dir_item SET icon=%s WHERE id=%u";
$install_getWidgetIdFromTempDirItem = "SELECT id FROM temp_dir_item WHERE logo IS NULL OR logo=''";
$install_updateWidgetIconTempDirItem = "UPDATE temp_dir_item SET logo=%s WHERE id=%u";
$install_getWidgetCategoryId="SELECT id,name FROM dir_category WHERE lang=%s ";
$install_getCatProperties="SELECT category_id,seq FROM dir_cat_properties WHERE category_id=%u";
$install_getMaxSeqProperties="SELECT MAX(seq+1) as seq FROM dir_cat_properties";
$install_setProperties="INSERT INTO dir_cat_properties (category_id,seq) VALUES (%u,%u)";
$install_getSeqFromLang="SELECT seq FROM dir_cat_properties WHERE category_id=%u";
$install_getDimension="SELECT value FROM adm_config WHERE parameter=%s";
$install_updateDimension="UPDATE adm_config SET value=%s WHERE parameter=%s";
$install_setRegisterFeeds="UPDATE adm_config SET value=%s WHERE parameter='registerFeeds'";
?>