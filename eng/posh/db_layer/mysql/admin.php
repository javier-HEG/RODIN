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
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
$index_getNbOfModules = "SELECT count(id) AS nb FROM dir_item WHERE status = 'O' ";
$index_getNbOfModulesToValidate = "SELECT count(*) AS nb FROM temp_dir_item,temp_dir_cat_item WHERE id=item_id AND status = 'N'";
$index_getNbOfUsers = "SELECT count(*) AS nb FROM users WHERE typ = 'I'";
$index_getPagesName = "SELECT name,id FROM pages WHERE group_id=0 ORDER BY seq";
$index_getNbOfPortals = "SELECT count(*) AS nb FROM portals WHERE status = 'O'";
$index_getNbOfPortalsToValidate = "SELECT count(id) AS nb FROM portals,portals_category WHERE status = 'N' AND id=portal_id ";
$tabs_getTabs = "SELECT id,name,label,type,param FROM adm_tabs ORDER BY id";
$communication_getConfig = "SELECT parameter,value FROM adm_config ";
$communication_sentEmails = "SELECT id,UNIX_TIMESTAMP(sentdate) AS date,subject,sender,receiver FROM contact_sentitems WHERE status='S' ORDER BY sentdate DESC ";
$communication_sentEmailDetail = "SELECT UNIX_TIMESTAMP(sentdate) AS date,subject,message,sender,receiver FROM contact_sentitems WHERE id=%u ";
$admin_getTabFunctions = "SELECT label,description,link FROM adm_tabs_fct WHERE tabname=%s ";
$plugin_getPluginList = "SELECT name FROM adm_plugins WHERE status='O' ORDER BY name ";
$config_getParameters = "SELECT parameter,value,datatype,desttype,category FROM adm_config ORDER BY category";
$configfeatures_getHeadLinks = "SELECT id,uniq_id,type,label,comment,clss,images,fct,status,seq,anonymous,connected,admin FROM adm_headlinks ORDER BY seq ASC ";
$configfeatures_getParameters = "SELECT parameter,value FROM adm_config ";
$configtheme_getThemes = "SELECT name FROM adm_themes ORDER BY seq ASC ";
$module_getMainDirectory = "SELECT id,name,seq,lang FROM dir_category,dir_cat_properties WHERE parent_id=0 AND category_id=id ORDER BY seq ";
$portals_getPortalsList = "	SELECT portals.name,portals.description,portals.status,category_id,dir_category.name AS name2 FROM portals,portals_category,dir_category 
							WHERE portals.id=portal_id AND dir_category.id=category_id AND portals.id=%u ";
$portals_getModules = "SELECT id,name FROM portals_module, dir_item WHERE item_id=id AND portal_id=%u ";
$module_getTempCategory = "SELECT id,name 
                            FROM 
                                dir_category, 
                                temp_dir_cat_item 
                            WHERE 
                                item_id=%u 
                                AND id=category_id ";
$module_getModulesToValidate = "SELECT  tdi.id,name,tdi.url,tdi.defvar,
                                        u.username,u.long_name,
                                        tdi.description,tdi.height,tdi.keywords,
                                        tdi.minwidth,tdi.sizable,
                                        tdi.format,tdi.website,tdi.nbvariables,
                                        tdi.autorefresh,tdi.lang,
                                        tdi.l10n,tdi.views,tdi.logo
                                    FROM 
                                        temp_dir_item tdi,
                                        users u
                                    WHERE 
                                        tdi.id=%u 
                                        AND tdi.status='N' 
                                        AND tdi.editor_id=u.id ";
$module_getCategory = "SELECT id,name FROM dir_category, dir_cat_item WHERE item_id=%u AND id=category_id ";
$module_getModule = "SELECT id,name,url,defvar,description,typ,status,height,minwidth,sizable,format,website,nbvariables,autorefresh,icon FROM dir_item WHERE id=%u ";
$module_getKeywords = "SELECT id, label, weight FROM search_index, search_keyword WHERE kw_id=id AND item_id=%u ORDER BY weight DESC,label ";
$users_groupNumber = "SELECT count(*) FROM users_group WHERE parent_id=%u ";
$users_getUsersNumber = "SELECT count(*) FROM users_group_map WHERE group_id=%u ";
$users_deleteGroup = "DELETE FROM users_group WHERE id=%u ";
$users_getGroupName = "SELECT name FROM users_group WHERE id=%u ";
$module_getSubCategoryNumber = "SELECT count(*) FROM dir_category WHERE parent_id=%u ";
$module_getTempSubCatNumber = "SELECT count(*) FROM dir_cat_item WHERE category_id=%u ";
$module_getParentDirectory = "SELECT parent_id,lang FROM dir_category WHERE id=%u ";
$module_removeDirectory = "DELETE FROM dir_category WHERE id=%u ";
$module_getDirectoryType = "SELECT typ,lang FROM dir_category WHERE id=%u ";
$module_getCategoryName = "SELECT name,lang FROM dir_category WHERE id=%u ";
$users_getUserInformation = "SELECT username,long_name,typ,lang FROM users WHERE id=%u ";
$users_getUserGroup = "SELECT id, name FROM users_group, users_group_map WHERE id=group_id AND user_id=%u ";
$cachewaiting_getModules = "SELECT dir_item.id, dir_item.name FROM dir_item, dir_cat_item, dir_category 
							WHERE dir_item.typ<>'A' AND status='O' AND dir_item.id=item_id AND dir_category.id=category_id AND secured=0 AND first='Y' ORDER BY creation_date DESC, dir_item.id DESC LIMIT 0,6 ";
$pages_getPagesList ="SELECT 
                            name,
                            description,position,type,
                            param,nbcol,showtype,npnb,style,
                            modulealign,controls,icon,removable 
                        FROM 
                            pages 
                        WHERE id=%u ";
$pages_getModules = "SELECT item_id,posx,posy,posj,x,y,variables,uniq,height,name,minwidth,sizable,url,format,website,nbvariables,blocked,minimized FROM pages_module,dir_item WHERE item_id=dir_item.id AND page_id=%u ";
$pages_selectMainGroups = "SELECT id, name FROM users_group WHERE parent_id=0 ";
$pages_getTabsList = "	SELECT pages.id,name,description,ref_pages_mode.label AS mode,ref_pages_type.label AS type,param,seq FROM pages,ref_pages_mode,ref_pages_type 
						WHERE ref_pages_mode.id=pages.position AND ref_pages_type.id=type AND group_id=%u ORDER BY seq ";
$communication_setBarType = "UPDATE adm_config SET value=%s WHERE parameter='bartype' ";
$communcation_insertBarType = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('bartype',%s,'int','A') ";
$admin_setLang = "UPDATE users SET lang=%s WHERE id=%u ";
$configfeatures_deleteHeadLinks = "DELETE FROM adm_headlinks ";
$configfeatures_insertHeadLinks = "INSERT INTO adm_headlinks (id,uniq_id,type,label,comment,clss,images,fct,status,seq,anonymous,connected,admin) VALUES (%u,%s,%s,%s,%s,%s,%s,%s,'O',%u,%u,%u,%u) ";
$configfeatures_setUseReader = "UPDATE adm_config SET value=%s WHERE parameter='usereader' ";
$configfeatures_insertUseReader = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('usereader',%s,'int','J') ";
$configfeatures_setShowTabIcon = 'UPDATE adm_config SET value=%s WHERE parameter="showtabicon" ';
$configfeatures_insertShowTabIcon = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('showtabicon',%s,'int','J') ";
$configfeatures_setColumnChange = "UPDATE adm_config SET value=%s WHERE parameter='columnchange' ";
$configfeatures_insertColumChange = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('columnchange',%s,'int','J') ";
$configfeatures_setCtrlHiding = "UPDATE adm_config SET value=%s WHERE parameter='ctrlhiding' ";
$configfeatures_insertCtrlHiding = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('ctrlhiding',%s,'int','J') ";
$configfeatures_setDoubleProtection = "UPDATE adm_config SET value=%s WHERE parameter='doubleprotection' ";
$configfeatures_insertDoubleProtection = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('doubleprotection',%s,'int','J') ";
$configfeatures_setShowRssCell = "UPDATE adm_config SET value=%s WHERE parameter='showrsscell' ";
$configfeatures_insertShowRssCell = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('showrsscell',%s,'int','J') ";
$configfeatures_setShowModuleSearch = "UPDATE adm_config SET value=%s WHERE parameter='showModuleSearch' ";
$configfeatures_insertShowModuleSearch = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('showModuleSearch',%s,'int','J') ";
$configfeatures_setShowModuleExpl = "UPDATE adm_config SET value=%s WHERE parameter='showModuleExpl' ";
$configfeatures_insertShowModuleExpl = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('showModuleExpl',%s,'int','J') ";
$cachegeneration_setZeroQuantity = "
    UPDATE  dir_category
    SET     quantity = 0,
            secured_quantity = 0
";
$cachegeneration_removeTempDirectories = "
    DELETE FROM temp_category
";
$cachegeneration_5thDirectoryLevel = "
    INSERT INTO temp_category (category_id, quantity, gener1, gener2, gener3, gener4, pass, secured) 
	SELECT category_id, count(*), cat2.id, cat3.id, cat4.id, cat5.id, '1', cat1.secured 
	FROM dir_item, dir_cat_item, dir_category as cat1, dir_category as cat2, dir_category as cat3, dir_category as cat4, dir_category as cat5 
	WHERE cat1.id=category_id AND cat2.id=cat1.parent_id AND cat3.id=cat2.parent_id AND cat4.id=cat3.parent_id AND cat5.id=cat4.parent_id AND cat5.parent_id=0 
	AND dir_item.id=dir_cat_item.item_id AND dir_item.status = 'O' 
	GROUP BY category_id, cat2.id, cat3.id, cat4.id, cat5.id, cat1.secured";
$cachegeneration_4thDirectoryLevel = "
    INSERT INTO temp_category (category_id, quantity, gener1, gener2, gener3, gener4, pass, secured) 
	SELECT category_id, count(*), cat2.id, cat3.id, cat4.id, 0 as id5, '1', cat1.secured
	FROM dir_item, dir_cat_item, dir_category as cat1, dir_category as cat2, dir_category as cat3, dir_category as cat4 
	WHERE cat1.id=category_id AND cat2.id=cat1.parent_id AND cat3.id=cat2.parent_id AND cat4.id=cat3.parent_id AND cat4.parent_id=0 
	AND dir_item.id=dir_cat_item.item_id AND dir_item.status='O' 
	GROUP BY category_id, cat2.id, cat3.id, cat4.id, id5, cat1.secured ";
$cachegeneration_3rdDirectoryLevel = "
    INSERT INTO temp_category (category_id, quantity, gener1, gener2, gener3, gener4, pass, secured) 
	SELECT category_id, count(*), cat2.id, cat3.id, 0 as id4, 0 as id5, '1', cat1.secured 
	FROM dir_item, dir_cat_item, dir_category as cat1, dir_category as cat2, dir_category as cat3 
	WHERE cat1.id=category_id AND cat2.id=cat1.parent_id AND cat3.id=cat2.parent_id AND cat3.parent_id=0 
	AND dir_item.id=dir_cat_item.item_id AND dir_item.status='O' 
	GROUP BY category_id, cat2.id, cat3.id, id4, id5, cat1.secured ";
$cachegeneration_2ndDirectoryLevel = "
    INSERT INTO temp_category (category_id, quantity, gener1, gener2, gener3, gener4, pass, secured) 
	SELECT category_id, count(*), cat2.id, 0 as id3, 0 as id4, 0 as id5, '1', cat1.secured 
	FROM dir_item, dir_cat_item, dir_category as cat1, dir_category as cat2 
	WHERE cat1.id=category_id AND cat2.id=cat1.parent_id AND cat2.parent_id=0 
	AND dir_item.id=dir_cat_item.item_id AND dir_item.status='O' 
	GROUP BY category_id, cat2.id, id3, id4, id5, cat1.secured";
$cachegeneration_1stDirectoryLevel = "
    INSERT INTO temp_category (category_id, quantity, gener1, gener2, gener3, gener4, pass, secured) 
	SELECT category_id, count(*), 0 as id2, 0 as id3, 0 as id4, 0 as id5, '1', cat1.secured 
	FROM dir_item, dir_cat_item, dir_category as cat1 
	WHERE cat1.id=category_id AND cat1.parent_id=0 
	AND dir_item.id=dir_cat_item.item_id AND dir_item.status='O' 
	GROUP BY category_id, id2, id3, id4, id5, cat1.secured";
$cachegeneration_mystery1stLevel = "
    INSERT INTO temp_category (category_id, quantity, pass, secured) 
	SELECT gener1, sum(quantity), '2', secured
    FROM temp_category
    WHERE gener1 > 0
    GROUP BY gener1, secured ";
$cachegeneration_mystery2ndLevel = "
    INSERT INTO temp_category (category_id, quantity, pass, secured) 
	SELECT gener2, sum(quantity), '2', secured
    FROM temp_category
    WHERE gener2 > 0
    GROUP BY gener2, secured ";
$cachegeneration_mystery3rdLevel = "
    INSERT INTO temp_category (category_id, quantity, pass, secured) 
	SELECT gener3, sum(quantity), '2', secured
    FROM temp_category
    WHERE gener3 > 0
    GROUP BY gener3, secured";
$cachegeneration_mystery4thLevel = "
    INSERT INTO temp_category (category_id, quantity, pass, secured) 
	SELECT gener4, sum(quantity), '2', secured
    FROM temp_category
    WHERE gener4 > 0
    GROUP BY gener4, secured";
$cachegeneration_mystery5thLevel = "
    INSERT INTO temp_category (category_id, quantity, pass, secured) 
	SELECT category_id, sum(quantity), '3', secured
    FROM temp_category
    GROUP BY category_id, secured";
$cachegeneration_updateTempCat = "
    UPDATE dir_category, temp_category 
    SET dir_category.quantity = temp_category.quantity 
    WHERE dir_category.id = temp_category.category_id
    AND pass = '3'
    AND temp_category.secured = 0";
$cachegeneration_updateTempCatSecured = "
    UPDATE dir_category, temp_category 
    SET dir_category.secured_quantity = temp_category.quantity 
    WHERE dir_category.id = temp_category.category_id
    AND pass = '3'
    AND temp_category.secured = 1";
//$cachegeneration_getDirectoryId = "SELECT id,lang FROM dir_category,dir_cat_properties WHERE dir_category.id=dir_cat_properties.category_id ";
$cachegeneration_getDirectoryId = "SELECT id,lang,parent_id FROM dir_category WHERE secured=0 ";
$cachegeneration_getModuleId = "SELECT dir_item.id FROM dir_item, dir_cat_item, dir_category WHERE dir_item.id=item_id AND dir_category.id=category_id AND secured=0 ";
$cachegeneration_getPortalsId = "SELECT id FROM portals, portals_category WHERE id=portal_id ";
$cachegeneration_getPagesId = "SELECT id FROM pages WHERE position IN (1,3) ORDER BY seq LIMIT 1 ";
$cachegeneration_getPersonalizedPages = "SELECT id FROM pages WHERE type=1 ";
$cacheupdate_getModuleNumber = "SELECT count(id) AS nb FROM dir_item, dir_cat_item WHERE status='O' and id=item_id AND typ<>'A' AND first='Y' AND lang='fr' ";
$communication_getUsersList = "SELECT username FROM users WHERE typ='I' ";
$communication_addSentItem = "INSERT INTO contact_sentitems (sender,subject,message,receiver,sentdate,status) VALUES (%s,%s,%s,%s,CURRENT_DATE,'S') ";
$communication_setRssInfo = "UPDATE adm_config SET value=%s WHERE parameter='rssinfo' ";
$communication_insertRssInfo = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('rssinfo',%s,'str','J') ";
$communication_setInfoBar = "UPDATE adm_config SET value=%s WHERE parameter='barclosing' ";
$communication_insertInfoBar = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('barclosing',%s,'int','J') ";
$communication_setBarTextHtml = "UPDATE adm_config SET value=%s WHERE parameter='bartexthtml' ";
$communication_insertBarTextHtml = "INSERT INTO adm_config 
                                            (parameter,value,datatype,desttype) 
                                            VALUES 
                                            ('bartexthtml',%s,'str','J') ";
$users_removeUser = "
    DELETE FROM users
    WHERE       id=%u
";
$config_setLocalfolder = "UPDATE adm_config SET value=%s WHERE parameter='LOCALFOLDER' ";
$config_insertLocalfolder = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('LOCALFOLDER',%s,'str','P') ";

$config_setNotificationemail = "UPDATE adm_config SET value=%s WHERE parameter='NOTIFICATIONEMAIL' ";
$config_insertNotificationemail = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('NOTIFICATIONEMAIL',%s,'str','P') ";
$configgenerate_getParameters = "SELECT parameter,value,datatype,desttype FROM adm_config ";

$configgenerate_getHeadLinks = "
	SELECT	id,
			uniq_id,
			type,
			label,
			comment,
			clss,
			images,
			fct,
			anonymous,
			connected,
			admin,
			seq,
			position
	FROM	adm_headlinks
	WHERE	status='O'
	ORDER BY seq ";
$configgenerate_getHeadLink = "SELECT label,comment,clss,images,fct,anonymous,connected,admin FROM adm_headlinks WHERE status='O' AND id=%u AND seq>0 ORDER BY seq ";
$plugin_removePlugin = 'DELETE FROM adm_plugins WHERE name=%s ';
$plugin_addNewPlugin = "INSERT INTO adm_plugins (name,link,dependency) VALUES (%s,%s,%s) ";
$plugin_getPlugins = "SELECT link FROM adm_plugins WHERE status='O' ";
$configtheme_removeAll = 'DELETE FROM adm_themes ';
$configtheme_addNewTheme = 'INSERT INTO adm_themes (name,seq) VALUES(%s,%u) ';
$configtheme_deleteTheme = 'DELETE FROM adm_themes WHERE id=%u  ';
$configtheme_updateConfigVariable='
   UPDATE adm_config
      SET value="%s"
    WHERE parameter="themeList"
';
$configtheme_insertConfigVariable = '
 INSERT INTO adm_config (parameter,value,datatype,desttype)
      VALUES ("themeList","%s","arr","J")
';
$configtheme_updateDefTheme = "UPDATE adm_config SET value=%s WHERE parameter='theme' ";
$configtheme_insertDefTheme = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('theme',%s,'str','J') ";
$module_addNewDirectory = "INSERT INTO dir_category (name,parent_id,typ,lang,secured) VALUES (%s,%u,%s,%s,%u) ";
$module_updateDirectory = 'UPDATE dir_category SET name=%s,secured=%u WHERE id=%u ';
$module_getDirectoryParent = "SELECT parent_id,lang FROM dir_category where id=%u ";
$module_moveDirectory = "UPDATE dir_category SET parent_id=%u WHERE id=%u ";
$users_addGroup = "INSERT INTO users_group (name,parent_id) VALUES (%s,%u) ";
$module_updateGroupName = "UPDATE users_group SET name=%s WHERE id=%u ";
$users_getGroupParent = "SELECT parent_id FROM users_group where id=%u ";
$users_moveGroup = 'UPDATE users_group SET parent_id=%u WHERE id=%u ';
$module_updateModule = "UPDATE 
                            dir_item 
                        SET 
                            url=%s,
                            name=%s,
                            description=%s,
                            typ=%s,
                            status=%s,
                            height=%u,
                            minwidth=%u,
                            sizable=%u,
                            website=%s,
                            lastmodif_date=CURRENT_DATE,
                            updated='Y',
                            views=%s 
                        WHERE id=%u ";
$module_updateModuleDirectory = "UPDATE dir_cat_item SET category_id=%u WHERE item_id=%u AND first='Y' ";
$module_removeKeywords = "DELETE FROM search_index WHERE item_id=%u ";
$module_getKeyword = "SELECT id FROM search_keyword WHERE label_simplified=%s";
$module_addKeyword = "INSERT INTO search_keyword (label,label_simplified) VALUES (%s,%s) ";
$module_addModuleKeyword = "INSERT INTO search_index (kw_id, item_id, weight) VALUES (%u,%u,%u) ";
$module_getTempModule = "SELECT 
                                format,temp_dir_item.typ,
                                defvar,sizable,logo,username AS edemail, 
                                keyword, temp_dir_item.lang,
                                temp_dir_item.l10n,temp_dir_item.views 
                            FROM 
                                temp_dir_item, 
                                users 
                            WHERE 
                                temp_dir_item.id=%u 
                                AND editor_id=users.id ";
$module_validateModule = "INSERT INTO 
                            dir_item 
                                (url, name, description, defvar, 
                                    typ, status, height, format, minwidth, 
                                    sizable, website, editor_id, nbvariables, 
                                    creation_date, lastmodif_date, lang, usereader, 
                                    autorefresh, views, l10n) 
                                    SELECT %s, %s, %s,defvar, 
                                            typ, 'O', %u, format, %u, 
                                            sizable, %s, editor_id, nbvariables, 
                                            creation_date, CURRENT_DATE, lang, 
                                            usereader, autorefresh, views, l10n 
                                        FROM 
                                            temp_dir_item 
                                        WHERE id=%u ";
$module_updateSource = "UPDATE dir_item_external 
                                SET 
                                item_id = %u,
                                url=%s,
                                status=%s
                                WHERE item_id = %u
                                ";
$module_getSource = "SELECT source,xmlmodule,url,view 
                            FROM dir_item_external
                            WHERE
                                item_id=%u";  
$module_removeTempModule = "DELETE FROM temp_dir_item WHERE id=%u ";
$module_addDirectoryTempModule = "
    INSERT INTO dir_cat_item (
                item_id,
                category_id,
                first)
    SELECT      %u,
                category_id,
                'N'
    FROM        temp_dir_cat_item
    WHERE       item_id = %u
        AND     first='N' ";
$module_addModuleSubDirectory = "INSERT into dir_cat_item (item_id, category_id, first) SELECT %u,%u, 'Y' FROM temp_dir_cat_item WHERE item_id=%u AND first='Y' ";
$module_addDirectoryModule = "INSERT into dir_cat_item (item_id, category_id, first) VALUES (%u,%u,'N')";
$module_removeTempDirectory = "DELETE FROM temp_dir_cat_item WHERE item_id=%u ";
$module_addRedactorFeed = "INSERT INTO redactor_map_item_feed (item_id,item_url,feed_id) VALUES (%u,'',%u) ";
$module_getValidationKeyword = "SELECT id FROM search_keyword WHERE label_simplified=%s ";
$module_addValidationKeyword = "INSERT INTO search_keyword (label,label_simplified) VALUES (%s,%s) ";
$module_addValidationModuleKeyword = "INSERT INTO search_index (kw_id, item_id, weight) VALUES (%u,%u,%u) ";
$module_removeValidationModule = "UPDATE temp_dir_item SET status='C' WHERE id=%u ";
$pages_getSequence = "SELECT seq FROM pages WHERE id=%u ";
$pages_moveNextSeqLeft = "UPDATE pages SET seq=seq-1 WHERE seq>%u ";
$pages_moveNextSeqRight = "UPDATE pages SET seq=seq+1 WHERE seq>=%u ";
$pages_updateSeq = "UPDATE pages SET seq=%u WHERE id=%u ";
$pages_getPagesId = "SELECT id FROM pages WHERE position IN (1,3) AND group_id=0 ORDER BY seq LIMIT 1 ";
//$oages_getPersonalizedPageId = "SELECT id FROM pages WHERE type=1 AND position=1";
$oages_getPersonalizedPageId = "SELECT id,position FROM pages WHERE type=1";
$pages_getPageSequence = "SELECT seq FROM pages WHERE id=%u ";
$pages_updatePageSeqForGroup = "UPDATE pages SET seq=seq-1 WHERE seq>%u AND group_id=%u ";
$pages_removePage = "DELETE FROM pages WHERE id=%u ";
$rootdirectory_addDirectory = "INSERT INTO dir_category (name,parent_id,typ,quantity,updated,lang) VALUES (%s,0,'O',0,'Y',%s) ";
$rootdirectory_addProperties = "INSERT INTO dir_cat_properties (category_id,seq) VALUES (%u,%u) ";
$rootdirectory_updateSeq = "UPDATE dir_cat_properties SET seq=%s WHERE category_id=%u AND seq=%s ";
$rootdirectory_updateName = "UPDATE dir_category SET name=%s,lang=%s WHERE id=%u ";
$rootdirectory_getDirectory = "SELECT id,name,seq,lang FROM dir_category,dir_cat_properties WHERE parent_id=0 AND category_id=id ORDER BY seq ";
$rootdirectory_getDimension = "UPDATE adm_config SET value=%s WHERE parameter='dimension' ";
$rootdirectory_insertDimension = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('dimension',%s,'arr','J') ";
$pages_setProperties = 'UPDATE pages SET name=%s, description=%s, position=%u, type=%u, param=%s, icon=%s WHERE id=%u ';
$pages_getMaxSeq = 'SELECT (MAX(seq)+1) AS nseq FROM pages WHERE group_id=%u ';
$pages_addNew = "INSERT INTO pages (group_id,name,description,position,type,param,seq,icon,removable) VALUES (%u,%s,%s,%u,%u,%s,%u,%s,%u) ";
$pages_updateSubProperties = "UPDATE pages SET nbcol=%u,showtype=%u,npnb=%u,style=%u,modulealign=%s,controls=%s WHERE id=%u ";
$pages_removeModules = "DELETE FROM pages_module WHERE page_id=%u ";
$pages_addModules = "INSERT INTO pages_module (item_id,page_id,posx,posy,posj,x,y,variables,uniq,blocked,minimized) VALUES (%u,%u,%u,%u,%u,%u,%u,%s,%u,%u,%u) ";
$portal_update = "UPDATE portals SET name=%s,description=%s,status=%s WHERE id=%u ";
$portal_updateDirectory = "UPDATE portals_category SET category_id=%u WHERE portal_id=%u ";
$rootdirectory_removeProperties = "DELETE FROM dir_cat_properties WHERE category_id=%u AND seq=%u ";
$rootdirectory_updateProperties = "UPDATE dir_cat_properties SET seq=CONV(seq,10,10)-1 WHERE seq>%u ";
$users_addNew = "
    INSERT INTO users (
            username,
            pass,
            long_name,
            typ,
            lastconnect_date,
            md5pass,
            md5user,lang
            )
    VALUES (
            %s,
            '',
            %s,
            %s,
            CURRENT_DATE,
            MD5(%s),
            %s,
            %s)
";
$users_logUserAdd = "
    INSERT INTO log (
        action,
        pubdate,
        param1
        )
    VALUES (
        3,
        CURRENT_DATE,
        %u
    )
";
$users_updateWithPass = "UPDATE users SET username=%s,long_name=%s,typ=%s ,pass='', md5pass=MD5(%s),md5user=%s,lang=%s WHERE id=%u ";
$users_updateWithoutPass = "UPDATE users SET username=%s,long_name=%s,typ=%s,lang=%s WHERE id=%u ";
$user_removeFromGroup = "DELETE FROM users_group_map WHERE user_id=%u ";
$users_addInGroup = "INSERT INTO users_group_map (user_id,group_id) VALUES (%u,%u) ";
$users_updatePassword = "UPDATE users SET md5pass=%s WHERE id=%u ";
$temcache_getLastModules = "SELECT id, name, description FROM dir_item WHERE lang='fr' ORDER BY id DESC LIMIT 0,10 ";
$stats_computeNbOfUsers = "INSERT INTO stats_final (action,treatdate,result1) 
	SELECT action,pubdate,count(param1) FROM log WHERE action=3 AND pubdate!=CURRENT_DATE GROUP BY action,pubdate ";
$stats_computeOpenings = "INSERT INTO stats_final (action,treatdate,result1) 
	SELECT action,pubdate,count(param1) FROM log WHERE action=2 AND pubdate!=CURRENT_DATE GROUP BY action,pubdate ";
$stats_computeVisitors = "INSERT INTO stats_final (action,treatdate,result1) 
	SELECT 6,pubdate,count(distinct param1) FROM log WHERE action=2 AND pubdate!=CURRENT_DATE GROUP BY pubdate ";
$stats_removeOld = "DELETE FROM log WHERE (action!=4 AND pubdate!=CURRENT_DATE) OR (action=4 AND MONTH(pubdate)!=MONTH(CURRENT_DATE)) ";
$stats_getDailyVisitors = "SELECT DAYOFMONTH(treatdate) AS absc,result1 AS ord FROM stats_final WHERE action=6 AND MONTH(treatdate)=%s ORDER BY absc ";
$stats_getMonthlyVisitors = "SELECT MONTH(treatdate) as absc, sum(result1) AS ord FROM stats_final WHERE action=8 AND YEAR(treatdate)=%s GROUP BY absc ORDER BY absc ";
$stats_getDailyNewUsers = "SELECT DAYOFMONTH(treatdate) AS absc,result1 as ord FROM stats_final WHERE action=3 AND MONTH(treatdate)=%s ORDER BY treatdate ";
$stats_getMonthlyNewUsers = "SELECT MONTH(treatdate) as absc, sum(result1) AS ord FROM stats_final WHERE action=3 AND YEAR(treatdate)=%s GROUP BY absc ORDER BY absc ";
$stats_getDailyOpenings = "SELECT DAYOFMONTH(treatdate) AS absc,result1 AS ord FROM stats_final WHERE action=2 AND MONTH(treatdate)=%s ORDER BY absc ";
$stats_getMonthlyOpenings = "SELECT MONTH(treatdate) as absc,sum(result1) AS ord FROM stats_final WHERE action=2 AND YEAR(treatdate)=%s GROUP BY absc ORDER BY absc ";
//$widnotes_newNote = "INSERT INTO users_notes (user_id, notes) VALUES (0,%s) ";
//$widnotes_updateNote = 'UPDATE users_notes SET notes=%s WHERE id=%u ';	
//$widtask_removeTask = "DELETE FROM users_tasks WHERE task_id=%s ";
//$widtask_addId = 'INSERT INTO users_tasks_id (status) VALUES("A") ';
//$widtask_addTask = "INSERT INTO users_tasks (id,comments,name) VALUES (%u,%s,%s) ";
//$widtask_changeStatus = "UPDATE users_tasks SET done=%s WHERE task_id=%u ";
//$widlink_removeLink = "DELETE FROM users_favorites WHERE link_id=%u ";
//$widlink_addId = 'INSERT INTO users_favorites_id (status) VALUES("A") ';
//$widlink_addLink = "INSERT INTO users_favorites (id,name,url) VALUES (%u,%s,%s) ";
//$widcal_remove = "DELETE FROM users_calendar WHERE cal_id=%u ";
//$widcal_addId = 'INSERT INTO users_calendar_id (status) VALUES("A") ';
//$widcal_addEvent = "INSERT INTO users_calendar (id,title,comments,pubdate,time,endtime) VALUES (%u,%s,%s,%s,%s,%s) ";
//$widcal_getEvent = "SELECT cal_id, title, comments, time, endtime FROM users_calendar WHERE id=%u AND pubdate=%s ORDER BY time ";
//$widcal_getMonthEvents = "SELECT cal_id, DAYOFMONTH(pubdate) as day, title, time, endtime FROM users_calendar WHERE id=%u AND MONTH(pubdate)=%s AND YEAR(pubdate)=%s ORDER BY pubdate,time ";
$users_getAllXml = "SELECT id,username,long_name,typ FROM users ORDER BY username ASC,long_name ASC LIMIT %u,21 ";
$module_searchModule = "SELECT dir_item.id, SUM(weight) AS wei, name FROM search_index, search_keyword, dir_item 
		WHERE dir_item.id=item_id AND search_keyword.id=kw_id AND label_simplified IN (%s) 
		GROUP BY item_id ORDER BY wei DESC,notation DESC LIMIT %u,21 ";
$module_getAllValidXml = "SELECT id, name, status, icon FROM dir_item 
	WHERE status!='S' ORDER BY name ASC, id DESC LIMIT %u,21 ";                                   
$cache_getAllModuleXml = "SELECT id, name, ROUND(notation/20) AS nota, substring(description,1,80) AS descr FROM dir_item 
	WHERE typ<>'A' AND status='O' ORDER BY creation_date DESC, id DESC LIMIT %u,7 ";
$cache_getModule = "
   SELECT c.url
        , c.name
        , c.description
        , c.defvar
        , c.height
        , c.minwidth
        , c.sizable
        , c.format
        , c.website
        , ROUND(c.notation/20) AS nota
        , c.nbvariables
        , c.usereader
        , c.autorefresh
        , c.editor_id
        , d.long_name
        , c.creation_date
        , c.views
        , c.icon
     FROM dir_cat_item AS a
        , dir_category AS b
        , dir_item AS c 
       LEFT JOIN users AS d 
              ON d.id=c.editor_id
			 AND d.typ!='A'
    WHERE c.id=%u 
      AND c.id=a.item_id
      AND b.id=a.category_id
      AND b.secured=0
 ORDER BY name
";
$cache_getRssModule = "
   SELECT a.url
        , a.name
		, a.description
		, a.defvar
		, a.height
		, a.minwidth
		, a.sizable
		, a.format
		, a.website
		, ROUND(a.notation/20) AS nota
		, a.nbvariables
		, a.usereader
		, a.autorefresh
		, a.editor_id
		, b.long_name
		, a.creation_date
     FROM dir_item AS a
	      LEFT JOIN users AS b
		         ON b.id=a.editor_id
    WHERE a.id=86
	ORDER BY a.name
";
$cache_getPortal = "SELECT name,description,status,author,nbcol,style,position,modulealign FROM portals WHERE id=%u ";
$cache_getPortalModules = "SELECT item_id,name,posx,posy,posj,x,y,variables,height,website,minwidth,sizable,url,format,nbvariables,blocked,minimized,usereader,autorefresh FROM portals_module,dir_item WHERE portal_id=%u AND item_id=id ORDER BY posx,posy,posj ";
$cache_getChildrenDirectory = "SELECT id, name FROM dir_category WHERE parent_id = %u AND secured=0 ORDER BY name ";
$cache_getPortalInformation = "SELECT id, name FROM portals_category,portals WHERE id = portal_id AND category_id = %u AND status = 'O' ";
$module_getChildrenDirectoryXml = "
    SELECT  id,
            name,
            quantity + secured_quantity AS quantity,
            secured
    FROM    dir_category
    WHERE   parent_id = %u
    ORDER BY name ";
$module_getModuleOfDirectoryXml = "SELECT id, name FROM dir_cat_item, dir_item WHERE id = item_id AND category_id = %u AND status = 'O' ORDER BY sorting ";
$users_getGroupXml = "SELECT name FROM users_group WHERE id IN (%s)";
$users_getChildrenGroupsXml = "SELECT id, name FROM users_group WHERE parent_id = %u and id IN (%s) ORDER BY name ";
$users_getUsersOfGroupXml = "SELECT id, username, long_name, typ FROM users_group_map, users WHERE id = user_id AND group_id = %u AND typ in ('I','A') ORDER BY long_name ASC, username ASC ";
$pagegeneration_getPage = "SELECT name,type,param,nbcol,showtype,npnb,style,modulealign,position,controls FROM pages WHERE id=%u ";
$pagegeneration_getPageModules = "
	SELECT	posx,
			posy,
			posj,
			x,
			y,
			height,
			item_id,
			website,
			name,
			variables,
			minwidth,
			sizable,
			url,
			uniq,
			format,
			nbvariables,
			blocked,
			minimized,
			usereader,
			autorefresh
	FROM	pages_module,
			dir_item 
	WHERE	page_id=%u
		AND dir_item.id=item_id
	ORDER BY uniq ";
$users_searchXml = "SELECT id,username,long_name,typ FROM users 
		WHERE typ in ('I','A') AND (username like %s OR long_name like %s) 
		ORDER BY long_name, username LIMIT %u,21 ";
$pages_getConnectedTabs = "SELECT id,name,type,param,seq,icon FROM pages WHERE group_id=0 AND position IN (2,3) ORDER BY seq ";
$pages_getAnonymousTabs = "SELECT id,name,type,param,seq,icon FROM pages WHERE group_id=0 AND position IN (1,3) ORDER BY seq ";
$module_toValidateXml = "SELECT id, name, status, logo FROM temp_dir_item,temp_dir_cat_item WHERE id=item_id AND status='N' ORDER BY name ASC, id DESC LIMIT %u,21 ";
$module_getNbXml ="SELECT count(*) AS nb FROM dir_item WHERE status = 'O'";
$module_getNbToValidateXml ="SELECT count(*) AS nb 
                                    FROM temp_dir_item,temp_dir_cat_item 
                                        WHERE id=item_id 
                                            AND status = 'N'";
$cache_getDirectoryInformation = "
    SELECT  id,
            parent_id,
            name,
            quantity,
            lang
    FROM    dir_category
    WHERE   id=%u
        AND secured=0
";
$cache_getRootDirectoryChildren = "
    SELECT  id,
            name,
            quantity,
            secured,
            secured_quantity,
            lang
    FROM    dir_category,
            dir_cat_properties
    WHERE   parent_id = %u
        AND id = category_id
        AND secured = 0 ORDER BY seq ASC
";
$cache_getDirectoryChildren = "
    SELECT  id,
            name,
            quantity,
            lang,
            secured,
            secured_quantity
    FROM    dir_category
    WHERE   parent_id = %u
        ORDER BY name ";
$module_getModuleInformationXml = "
    SELECT  id,
            icon,
            name,
            first,
            ROUND(notation/20) AS nota
    FROM    dir_cat_item,
            dir_item
    WHERE   id = item_id
        AND category_id = %u
        AND status = 'O'
        ORDER BY sorting,name ";
$refreshcache_setModuleAsUpdated = "UPDATE dir_item SET updated='Y' WHERE id=%u ";
$refreshcache_getLang = "
    SELECT  lang,
            secured
    FROM    dir_category
    WHERE   id = %u ";
$refreshcache_getModuleNbInDir = "
    SELECT  count(item_id)
    FROM    dir_cat_item,dir_item
    WHERE   category_id = %u
        AND id = item_id
        AND status='O' ";
$refreshcache_getSubDirNb = "
    SELECT  sum(quantity),
            sum(secured_quantity)
    FROM    dir_category
    WHERE   parent_id = %u";
$refreshcache_setQuantity = "
    UPDATE  dir_category
    SET     quantity = %u,
            secured_quantity = %u,
            updated = 'Y'
    WHERE   id = %u ";
$refreshcache_getParentDirectory = "SELECT parent_id FROM dir_category WHERE id=%u ";
$refreshcache_setDirectoryAsUpdated = "UPDATE dir_category SET updated='Y' WHERE id=%u ";
$refreshcache_getPortalLang = "SELECT lang FROM %u WHERE id=%u ";
$refreshcache_getParentId = "SELECT parent_id FROM %u WHERE id=%u ";
$config_saveParamValue = "UPDATE adm_config SET value=%s WHERE parameter=%s";
$stats_computeVisitorsMonth = "INSERT INTO stats_final (action,treatdate,result1) 
	SELECT 8,CONCAT(YEAR(pubdate),'-',MONTH(pubdate),'-01'),count(distinct param1) FROM log WHERE action=4 GROUP BY MONTH(pubdate) ";
$stats_computeArchiveForMonth = "INSERT INTO log (action,pubdate,param1,param2,param3) SELECT '4',pubdate,param1,param2,param3 FROM log WHERE action=2 ";
$configfeatures_setModuleAlign = "UPDATE adm_config SET value=%s WHERE parameter='moduleAlign' ";
$configfeatures_insertModuleAlign = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('moduleAlign',%s,'int','J') ";
$scr_modulevalidateall_getItems = "
	SELECT 		a.id,
				a.url,
				a.name,
				a.description,
				a.height,
				a.minwidth,
                a.sizable,
				a.website,
				b.category_id,
				a.typ,
				a.format,
				a.defvar,
				a.lang,
				a.keywords ,
				c.username ,
                a.views,
                a.l10n,
				a.logo,
				a.id_dir_item
	FROM 		temp_dir_item AS a,
				temp_dir_cat_item AS b,
				users AS c
	WHERE		a.id=b.item_id 
			AND a.editor_id=c.id 
			AND a.status='N'
";
$directory_addGroup = "INSERT INTO users_group_category_map (group_id,category_id) VALUES (%u,%u)";
$directory_getDirGroups = "SELECT id,name FROM users_group,users_group_category_map WHERE group_id=id AND category_id=%u ";
$directory_removeGroups = "DELETE FROM users_group_category_map WHERE category_id=%u ";
$users_removeFromGroups = "
    DELETE FROM users_group_map
    WHERE       user_id = %u
";
$logMaintenance = "INSERT INTO adm_log(log,logdate,typ) VALUES ('maintenance done !',CURRENT_DATE,'A') ";
$config_getTemplate = "SELECT value FROM adm_config WHERE parameter='template' ";
$config_setTemplate = "UPDATE adm_config SET value=%s WHERE parameter='template'";
$config_deleteTemplate = "DELETE FROM adm_config where template=%s ";
$scr_moduleDuplicate="INSERT INTO dir_item (url,defvar,name,description,typ,status,format,height,minwidth,sizable,website,editor_id,nbvariables,creation_date,lastmodif_date,notation,voter_nb,updated,nbusers,sorting,lang,usereader,autorefresh,views,l10n) SELECT url,defvar,CONCAT(name,' (2)'),description,typ,status,format,height,minwidth,sizable,website,editor_id,nbvariables,creation_date,lastmodif_date,notation,voter_nb,updated,nbusers,sorting,lang,usereader,autorefresh,views,l10n FROM dir_item WHERE id=%u";
$module_getModuleDirectory="SELECT category_id FROM dir_cat_item WHERE item_id=%u";
$module_addModuleDirectory="INSERT INTO dir_cat_item (item_id,category_id,first) VALUES (%u,%u,'Y')";
$module_setNewUrl = "UPDATE dir_item SET url=%s WHERE id=%u";
$users_updatePortalsLanguage = "UPDATE profile SET lang=%s WHERE user_id=%u";
$config_setValue = "UPDATE adm_config SET value=%s WHERE parameter=%s ";
$config_getNotification = "SELECT id,lang,subject,message,sender,copy FROM adm_mail WHERE lang=%s AND libelle='%s' ";
$config_setNotification = "UPDATE adm_mail SET subject=%s, message=%s, sender=%s, copy=%s WHERE id=%u";
$config_setNewNotification = "INSERT INTO adm_mail SET subject=%s, message=%s, sender=%s, copy=%s, lang=%s,libelle=%s";
$config_getNotificationTitles = "SELECT DISTINCT(libelle) FROM adm_mail";
$applications_getApplications = "SELECT id,title,icon,action FROM applications ORDER BY title LIMIT %u,21";
$users_deleteTabs= "
    DELETE FROM profile
    WHERE       user_id = %u
";
$users_deleteModules= "
    DELETE FROM module
    WHERE       user_id = %u
";
$users_deleteProfileModules= "DELETE FROM module WHERE profile_id = %u ";
$applications_getApplicationInformation = "SELECT title, description FROM applications WHERE id=%u";
$application_updateInfo = "UPDATE applications SET title=%s,description=%s WHERE id=%u";
$application_addGroup = "INSERT INTO applications_groups_map (application_id,group_id) VALUES (%u,%u)";
$application_getSelGroups = "SELECT a.id,a.name FROM users_group AS a,applications_groups_map AS b WHERE b.group_id=a.id AND b.application_id=%u ";
$application_removeGroups = "DELETE FROM applications_groups_map WHERE application_id=%u";
$config_setParam = "UPDATE adm_config SET value=%s WHERE parameter=%s ";
$config_insertParam = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES (%s,%s,%s,%s) ";
$users_removeUserInfos = "
    DELETE FROM users_info
    WHERE       user_id=%u
";
$users_addUserInfos = "INSERT INTO users_info (user_id,info_id,parameters,ispublic) VALUES (%u,%u,%s,'1') ";
$users_updateUserInfos = "UPDATE users_info SET parameters=%s WHERE user_id=%u AND info_id=%u ";
$config_updatePagePermission = "UPDATE adm_config SET value=%s WHERE parameter='addPagePermission' ";
$config_insertaddPagePermission =  "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('addPagePermission',%s,'int','J') ";
$criteria_getUserCriterias = "
    SELECT  id,
            label,
            type,
            options,
            mandatory,
            parameters
    FROM    adm_userinfo,
            users_info
    WHERE   adm_userinfo.id = users_info.info_id
    AND     users_info.user_id=%u
";
$criteria_getInformations="SELECT id,label,type,options,mandatory,editable FROM adm_userinfo";
$user_addUserInformations="INSERT INTO adm_userinfo (label,type,options,mandatory,editable,public) VALUES (%s,%u,%s,%u,%u,'0')";
$user_getUserInformations="SELECT id,label,type,options,mandatory,editable,displayon FROM adm_userinfo VALUES (%s,%s,%s,%u,%u,%u)";
$users_userscriteria_delete="DELETE FROM users_info WHERE info_id=%u";
$users_criteria_delete="DELETE FROM adm_userinfo WHERE id=%u";
$user_updateUserInformations="UPDATE adm_userinfo SET label=%s,mandatory=%u,editable=%u WHERE id=%u";
$config_setDisplayrssdesc = "UPDATE adm_config SET value=%s WHERE parameter='displayrssdesc' ";
$config_insertDisplayrssdesc = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('displayrssdesc',%s,'int','J') ";
$config_setDisplayrsssource = "UPDATE adm_config SET value=%s WHERE parameter='displayrsssource'  ";
$config_insertDisplayrsssource = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('displayrsssource',%s,'int','J') ";
$config_setDisplayrssimages = "UPDATE adm_config SET value=%s WHERE parameter='displayrssimages'  ";
$config_insertDisplayrssimages = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('displayrssimages',%s,'int','J') ";
$users_getIdGroup = "SELECT user_id FROM users_group_map,users WHERE users_group_map.user_id=users.id AND typ='I' AND group_id=%u ";
$users_getId = "SELECT DISTINCT id FROM users WHERE typ='I' ";
$users_getProfileId= "SELECT id FROM profile WHERE page_id=%u ";
$users_getUserProfilesId= "SELECT id FROM profile WHERE page_id=%u AND user_id=%u";
$users_getPublicAndGroupPages= "
    SELECT  id,
            name,
            nbcol,
            controls,
            showtype,
            style,
            seq,
            icon,
            modulealign,
            type,
            param,
            removable
    FROM    pages
    WHERE   group_id = %u
        AND position IN (1,3)
";
$users_getPages = "SELECT id FROM pages,users_group_map WHERE pages.group_id = users_group_map.group_id AND user_id=%u ";
$users_deleteToUpdateTabs= "
    DELETE FROM profile
    WHERE       page_id = %u
        AND     user_id = %u
";
$users_deleteGroupProfiles= "DELETE FROM profile WHERE id=%u AND user_id=%u";
$user_addProfilePages = "
    INSERT INTO profile (
        user_id,
        name,
        width,
        style,
        creation_date,
        modif_date,
        controls,
        showtype,
        seq,
        icon,
        modulealign,
        type,
        param,
        page_id,
        removable
    )
    VALUES (
        %u,
        %s,
        %u,
        %u,
        CURRENT_DATE,
        CURRENT_DATE,
        %s,
        %s,
        %u,
        %s,
        %s,
        %s,
        %s,
        %u,
        %u
    )
";
$profile_getMaxSeq = "SELECT (MAX(seq)+1) AS nseq FROM profile WHERE user_id=%u ";
$profile_addPage = "INSERT INTO profile (user_id,name,width,height,style,creation_date,modif_date,controls,showtype,seq,icon,modulealign,type,param,page_id,status,removable) VALUES (%u,%s,%u,5,%u,CURRENT_DATE,CURRENT_DATE,%s,%u,%u,%s,%s,%u,%s,%u,%u,%u) ";
$profile_updatePage = "UPDATE profile SET name=%s,width=%u,modif_date=CURRENT_DATE,controls=%s,showtype=%u,icon=%s,type=%u,param=%s,status=1";
$profile_updatePageid = "UPDATE profile SET page_id=%u WHERE page_id=%u";
$scrconfigplace_addNewMod = "INSERT INTO module (item_id,user_id,profile_id,posx,posy,posj,x,y,typ,variables,uniq,blocked,minimized) VALUES (%u,%u,%u,%u,%u,%u,%u,%u,'D',%s,%u,%u,%u) ";
$page_getPagesModule = "SELECT item_id,page_id,posx,posy,posj,x,y,variables,uniq,blocked,minimized FROM pages_module WHERE page_id=%u ";
$tabs_getAdminTabs = "SELECT id,name,label,type,param FROM adm_tabs,adm_tabs_map WHERE adm_tabs.id=adm_tabs_map.tab_id AND user_id=%u ORDER BY id";
$tabs_getUpdateAdminTabs = "SELECT tab_id FROM adm_tabs_map WHERE user_id=%u";
$users_removeFromTabs = "
    DELETE FROM adm_tabs_map
    WHERE       user_id=%u
";
$users_addAdmTabMap = "INSERT INTO adm_tabs_map (user_id,tab_id) VALUES (%u,%u)";
$users_deleteAdmTabMap = "
    DELETE FROM adm_tabs_map
    WHERE       user_id = %u
";
$users_getExistingPublicPages = "SELECT id FROM profile WHERE user_id=%u AND page_id=%u ";
$users_getExistingUserGroup = "SELECT group_id FROM users_group_map WHERE group_id=%u AND user_id=%u ";
$configgenerate_newRand = "UPDATE adm_config SET value='%s' WHERE parameter='rand'";
$tabs_access_control = "SELECT id FROM adm_tabs_map atm,adm_tabs at WHERE atm.tab_id=at.id AND user_id=%u AND name=%s"; 
$users_activateAdmin="
	UPDATE users
		SET typ='A'
			WHERE id=%u
				AND	typ='B'";
$users_activateUser="
	UPDATE users
		SET typ='I'
			WHERE	id=%u
				AND	typ='J'";
$users_inactivateAdmin="
	UPDATE users
		SET typ='B'
			WHERE	id=%u
				AND	typ='A'";
$users_inactivateUser="
	UPDATE users
		SET typ='J'
			WHERE	id=%u
				AND	typ='I'";
$users_controlExistingAccount="
	SELECT count(id) as nb
		FROM users 
			WHERE username = %s ";
$pages_getUserPageSequence="
	SELECT seq 
		FROM profile 
			WHERE page_id=%u 
				AND user_id=%u";
$scr_moduleDuplicateCategory="
    INSERT INTO dir_cat_item (item_id,category_id,first)
       VALUES(%u,%u,%s) ";
$module_getModuleCategory="
    SELECT category_id,first
        FROM dir_cat_item
        WHERE item_id=%u";
$getNbMessages="
    SELECT DISTINCT(label)
        FROM translation";
$getAllMessages="
    SELECT label,lang,message,typefile,usage_label
        FROM translation
        WHERE lang=%s
        ORDER BY usage_label";
$getAllLanguages="
    SELECT DISTINCT lang 
        FROM translation";
$users_deleteProfileModules= "DELETE 
                                FROM module 
                                WHERE profile_id=%u ";
$admin_getSubTabs = "SELECT tabname,fctname,label,description
                            FROM adm_tabs_fct
                            ORDER BY tabname ASC";
$pages_getGroupPages = "
    SELECT  id
    FROM    pages
    WHERE   group_id = %u
";
$users_getCriteriasParameters = "SELECT info_id,parameters FROM users_info WHERE user_id=%u";
$users_getSpecificCriteria = "SELECT parameters FROM users_info WHERE user_id=%u AND info_id=%u";
$admin_addInGroupMap = "INSERT INTO adm_group_map (user_id,group_id) VALUES (%u,%u) ";
$adm_getGroupId = "SELECT group_id FROM adm_group_map WHERE user_id=%u";
$adm_getUserIdByGroup = "SELECT DISTINCT id,username,long_name,typ FROM users u, users_group_map ugm WHERE u.id=ugm.user_id AND ugm.group_id IN (%s) ORDER BY username ASC,long_name ASC LIMIT %u,21 ";
$admin_getUserGroup = "SELECT DISTINCT user_id, group_id FROM adm_group_map WHERE user_id=%u";
$adm_getGroupNameByIdGroup = "SELECT ug.id,ug.name FROM users_group ug WHERE ug.id IN (%s)";
$users_getAllGroupXml = "SELECT name FROM users_group WHERE id = %u ";
$users_getChildrenAllGroupsXml = "SELECT id, name FROM users_group WHERE parent_id = %u ORDER BY name ";
//$users_getNbXml ="SELECT count(*) AS nb FROM users WHERE typ in ('I','A','R')";
$users_getNbXml ="SELECT count(DISTINCT user_id) AS nb FROM users_group_map WHERE group_id in (%s)";
$users_getAllNbXml ="SELECT count(*) AS nb FROM users WHERE typ in ('I','A','R')";
$admin_removeFromGroup = "DELETE FROM adm_group_map WHERE user_id=%u";
$adm_userIdSubGroup = "SELECT DISTINCT id,parent_id FROM users_group u WHERE u.parent_id IN(%s)";
$adm_getUserIdById = "SELECT id,username,long_name,typ FROM users u WHERE u.id IN (%s)";
$pages_selectAdminGroups = "SELECT id, name FROM users_group WHERE id IN (%s) ";
$pages_getAdminTabsList = "
    SELECT pages.id,name,description,ref_pages_mode.label AS mode,ref_pages_type.label AS type,param,seq
    FROM pages,ref_pages_mode,ref_pages_type 
	WHERE ref_pages_mode.id=pages.position AND ref_pages_type.id=type AND group_id IN (%s) ORDER BY seq ";
$module_getChildrenAdminDirectoryXml = "
    SELECT  id,
            name,
            quantity + secured_quantity AS quantity,
            secured
    FROM    dir_category
    WHERE   id IN (%s)
        AND parent_id = %u
    ORDER BY name ";
$users_getGroupCategoryMapXml = "SELECT category_id FROM users_group_category_map WHERE group_id IN (%s)";
$communication_getUsersListByGroup = "SELECT username FROM users WHERE typ='I' AND id IN (%s) ";
$communication_getUsersIdByGroup = "SELECT DISTINCT user_id FROM users_group_map WHERE group_id IN (%s) ";
$index_getNbOfUsersByGroup = "SELECT count(*) AS nb FROM users WHERE typ = 'I' AND id IN (%s)";
$statsmodules_getDailyTopModules = "SELECT name,sum(result2) AS tot FROM stats_final,dir_item WHERE action=4 AND result1=id AND treatdate LIKE %s GROUP BY name ORDER BY tot DESC LIMIT 50 ";
$statsmodules_getDailyTopDirRss = "SELECT name,sum(result2) AS tot FROM stats_final,dir_item WHERE action=7 AND result1=id AND treatdate LIKE %s GROUP BY name ORDER BY tot DESC LIMIT 50 ";
$statsmodules_getDailyTopRss = "SELECT result1,sum(result2) AS tot FROM stats_final WHERE action=5 AND treatdate LIKE %s GROUP BY result1 ORDER BY tot DESC LIMIT 50 ";$statsmodules_getMonthlyTopModules = "SELECT name,sum(result2) AS tot FROM stats_final,dir_item WHERE action=4 AND result1=id AND treatdate LIKE %s GROUP BY name ORDER BY tot DESC LIMIT 50 ";
$statsmodules_getMonthlyTopDirRss = "SELECT name,sum(result2) AS tot FROM stats_final,dir_item WHERE action=7 AND result1=id AND treatdate LIKE %s GROUP BY name ORDER BY tot DESC LIMIT 50 ";
$statsmodules_getMonthlyTopRss = "SELECT result1,sum(result2) AS tot FROM stats_final WHERE action=5 AND treatdate LIKE %s GROUP BY result1 ORDER BY tot DESC LIMIT 50 ";
$statsmodules_countModOpening = "INSERT INTO stats_final (action,treatdate,result1,result2) 
	SELECT 4,pubdate,param2,count(param3) FROM log,dir_item WHERE dir_item.id=param2 AND format!='R' AND action=1 AND param2!=86 AND pubdate!=CURRENT_DATE AND param2!='86' GROUP BY pubdate,param2 ";
$statsmodules_countDirRssOpening = "INSERT INTO stats_final (action,treatdate,result1,result2) 
	SELECT 7,pubdate,param2,count(param3) FROM log,dir_item WHERE dir_item.id=param2 AND format='R' AND action=1 AND param2!=86 AND pubdate!=CURRENT_DATE AND param2!='86' GROUP BY pubdate,param2 ";
$statsmodules_countRssOpening = "INSERT INTO stats_final (action,treatdate,result1,result2) 
	SELECT 5,pubdate,SUBSTRING(param3,LOCATE('rssurl',param3)+7,LOCATE('&',SUBSTRING(param3,LOCATE('rssurl',param3)))-7) AS rssurl,count(param2) FROM log WHERE action=1 AND pubdate!=CURRENT_DATE AND param2='86' GROUP BY pubdate,rssurl ";
$xml_getsupport="SELECT id,log,logdate,typ FROM adm_log ORDER BY id DESC LIMIT %u,100";
$support_deleteAllLogs="DELETE FROM adm_log";
$support_deleteOldLogs="DELETE FROM adm_log WHERE logdate < %s";
$support_deleteStats="DELETE FROM stats_final WHERE treatdate!=CURRENT_DATE";
$dir_rss_setIconId="UPDATE dir_rss SET iconid=%s WHERE iconid=%s ";
$module_deleteModuleCategory="DELETE FROM dir_cat_item WHERE item_id=%u AND category_id=%u";
$users_getMd5user="SELECT id, md5user FROM users WHERE username=%s";
$module_addDirItemExternal="INSERT INTO dir_item_external
SELECT %u,source,xmlmodule,url,CURRENT_DATE,'validated',view,type
FROM temp_dir_item_external
WHERE item_id=%u";
$dir_item_setIcon="UPDATE dir_item SET icon=%s WHERE id=%u ";
$scr_moduleUpdateIcon="UPDATE dir_item SET icon=%s WHERE id=%u";
$modules_deleteTempCat="DELETE FROM temp_dir_cat_item WHERE item_id=%u";
$modules_deleteTempDirItem="DELETE FROM temp_dir_item WHERE id=%u";
$modules_deleteTempDirItemExternal="DELETE FROM temp_dir_item_external WHERE item_id=%u";
$module_getModuleInfoOfDirectoryXml = "SELECT id, name, icon FROM dir_cat_item, dir_item WHERE id = item_id AND category_id = %u AND status = 'O' ORDER BY sorting ";
$cache_getModuleXml = "
   SELECT c.url
        , c.name
        , c.description
        , c.defvar
        , c.height
        , c.minwidth
        , c.sizable
        , c.format
        , c.website
        , ROUND(c.notation/20) AS nota
        , c.nbvariables
        , c.usereader
        , c.autorefresh
        , c.editor_id
        , d.long_name
        , c.creation_date
		, c.icon
        , c.views
        , c.l10n
     FROM dir_cat_item AS a
        , dir_category AS b
        , dir_item AS c 
       LEFT JOIN users AS d 
              ON d.id=c.editor_id
			 AND d.typ!='A'
    WHERE c.id=%u 
      AND c.id=a.item_id
      AND b.id=a.category_id
      AND b.secured=0
 ORDER BY name
";
$module_getModuleInformationByXml = "
	SELECT id
		, name
		, first
		, ROUND(notation/20) AS nota
		, icon 
		FROM dir_cat_item
		, dir_item 
		WHERE id = item_id AND category_id = %u 
			AND status = 'O' 
		ORDER BY sorting,name 
	";
$module_getTempModuleToValidate = "
    SELECT  format,
            temp_dir_item.typ,
            defvar,
            sizable,
            logo,
            username AS edemail,
            keyword,
            temp_dir_item.lang,
            id_dir_item,
            l10n,
            views
    FROM    temp_dir_item,
            users
    WHERE   temp_dir_item.id = %u
        AND editor_id = users.id ";
$modules_deleteDirItemExternal="DELETE FROM dir_item_external WHERE item_id=%u";
$module_updateDirItemExternal = " UPDATE dir_item_external SET source=%s, xmlmodule=%s, url=%s, view=%s, type=%s WHERE item_id=%u";
$module_getDirItemExternal = "SELECT source,xmlmodule,url,view,type FROM temp_dir_item_external WHERE item_id=%u";
$module_getAdminAllowedWidgets = "SELECT dir_item.id, dir_item.name, dir_item.status, dir_item.icon FROM dir_cat_item,adm_group_map,users_group_category_map,dir_item WHERE adm_group_map.group_id=users_group_category_map.group_id AND users_group_category_map.category_id=dir_cat_item.category_id AND dir_cat_item.item_id=dir_item.id AND dir_item.status!='S' AND adm_group_map.user_id=%u ORDER BY name ASC, id DESC LIMIT %u,21";                          
$module_getAdminAllowedGroupsId = "SELECT users_group_category_map.group_id FROM dir_cat_item, users_group_category_map WHERE users_group_category_map.category_id=dir_cat_item.category_id AND dir_cat_item.category_id = %u AND users_group_category_map.category_id IN (%s)";
$module_getAdminAllowedWidgetsToValidate = "SELECT temp_dir_item.id, temp_dir_item.name, temp_dir_item.status, temp_dir_item.logo FROM temp_dir_cat_item,adm_group_map,users_group_category_map,temp_dir_item WHERE temp_dir_item.id=temp_dir_cat_item.item_id AND adm_group_map.group_id=users_group_category_map.group_id AND users_group_category_map.category_id=temp_dir_cat_item.category_id AND temp_dir_cat_item.item_id=temp_dir_item.id AND temp_dir_item.status='N' AND adm_group_map.user_id=%u ORDER BY name ASC, id DESC LIMIT %u,21";
$module_searchModuleAllowed = "SELECT dir_item.id, SUM(weight) AS wei, name FROM search_index, search_keyword, dir_item , dir_cat_item, adm_group_map, users_group_category_map WHERE dir_item.id=search_index.item_id AND dir_item.id=dir_cat_item.item_id AND search_keyword.id=kw_id AND label_simplified IN (%s) AND adm_group_map.group_id=users_group_category_map.group_id AND users_group_category_map.category_id=dir_cat_item.category_id AND adm_group_map.user_id=%u GROUP BY dir_item.id ORDER BY wei DESC,notation DESC LIMIT %u,21 ";
$scr_modulevalidateall_getAllowedItems = "
	SELECT 		a.id,a.url,a.name,a.description,a.height,a.minwidth,a.website,
                b.category_id,
				a.typ,a.format,a.defvar,a.lang,a.keywords ,
				c.username ,
                a.views,a.logo,
				a.id_dir_item
	FROM 		temp_dir_item AS a,
				temp_dir_cat_item AS b,
				users AS c,
                adm_group_map AS gmap,
                users_group_category_map AS gcat
	WHERE		a.id=b.item_id 
    AND         a.editor_id=c.id 
    AND         a.status='N'
    AND         gmap.group_id=gcat.group_id
    AND         gcat.category_id=b.category_id
    AND         gmap.user_id=%u ";
$users_searchXmlAllowed = "SELECT DISTINCT id,username,long_name,typ 
                    FROM users,users_group_map
            		WHERE users_group_map.group_id IN (%s)
                    AND users.id = users_group_map.user_id
                    AND typ in ('I','A') 
                    AND (username like %s OR long_name like %s) 
            		ORDER BY long_name, username LIMIT %u,21 ";
$admin_removeFromGroupByGroup = "DELETE FROM adm_group_map WHERE group=%u";
$index_getPagesNameAllowed = "SELECT name,id FROM pages,adm_group_map WHERE pages.group_id=adm_group_map.group_id AND user_id=%u";
$module_getItemId = "SELECT item_id FROM dir_item_external WHERE item_id=%u";
$modules_addTempDirItemExternalLanguage="INSERT INTO dir_item_external_language (item_id,lang,url,source,viewtype,view,last_updated)
SELECT %u,lang,url,source,viewtype,view,CURRENT_DATE
FROM temp_dir_item_external_language
WHERE item_id=%u";
$modules_deleteTempDirItemExternalLanguage="DELETE FROM temp_dir_item_external_language WHERE item_id=%u";
$modules_getDirItemExternalLanguage="SELECT 
                                        lang,source,viewtype,view 
                                    FROM dir_item_external_language 
                                    WHERE 
                                        item_id=%u";


$module_getModuleAndView = "SELECT id,name,url,defvar,description,typ,status,height,minwidth,sizable,format,website,nbvariables,autorefresh,icon,views FROM dir_item WHERE id=%u ";
$scr_moduleDuplicateExternal="INSERT INTO dir_item_external SELECT %u,source,xmlmodule,url,CURRENT_DATE,'validated',view,type FROM dir_item_external WHERE item_id=%u";
$scr_moduleDuplicateExternalLanguage="INSERT INTO dir_item_external_language SELECT %u,lang,url,source,params,viewtype,view,CURRENT_DATE FROM dir_item_external_language WHERE item_id=%u";
$users_deleteToUpdateTabsByPageId= "
    DELETE FROM profile
    WHERE       page_id = %u";
?>
