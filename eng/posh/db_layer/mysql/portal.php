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
$authentif_getUser = "
    SELECT  username,
            typ,
            lang,
            long_name,
            activity
    FROM    users
    WHERE   id=%u
        AND md5pass=%s
        AND typ NOT IN ('S','D')
";
$authentif_getUserByName = "
    SELECT  id,
            md5pass AS password,
            typ,
            lang,
            long_name,
            activity,
			md5user
    FROM    users
    WHERE   username=%s
        AND typ NOT IN ('S','D')
";
$xmltabs_getTabs = "
	SELECT	id,
			name,
			md5pass,
			seq,
			icon,
			style,
			controls,
			loadonstart,
			type,
			status,
			param,
			shared,
            removable
	FROM 	profile
	WHERE	user_id=%u
	ORDER BY seq ASC ";
$xmlsearch_searchModule = "SELECT DISTINCT dir_item.id, SUM(weight) AS wei, dir_item.name FROM search_index, search_keyword, dir_item, dir_cat_item, dir_category 
		WHERE dir_item.id=search_index.item_id AND search_keyword.id=kw_id AND status='O' AND label_simplified IN ('%s') AND dir_item.id=dir_cat_item.item_id AND category_id=dir_category.id AND secured=0 
		GROUP BY search_index.item_id ORDER BY wei DESC,notation DESC LIMIT %u,11 ";
$xmlpages_getTabs = "SELECT id FROM profile WHERE user_id=%u ORDER BY seq ASC ";
$xmlpages_getGroup = "SELECT group_id FROM users_group_map WHERE user_id=%u ";
$xmlpages_getParentGroup = "SELECT parent_id FROM users_group WHERE id=%u ";
$xmlpages_getPageForGroup = "SELECT id FROM pages WHERE group_id=%u ";
$xmlpages_getPageInformation = "SELECT id,name,description,position,type,param,seq,nbcol,showtype,npnb,style,modulealign,removable FROM pages WHERE position=1 AND group_id=%u ORDER BY seq ";
$xmlpages_getModules = "SELECT item_id,posx,posy,posj,x,y,variables,uniq,blocked,minimized FROM pages_module WHERE page_id=%u ";
$opmlexport_getPortal = "SELECT id,name FROM profile WHERE user_id=%u ";
$opmlexport_getModules = "SELECT variables,website,name FROM module,dir_item WHERE item_id=id AND format='R' AND profile_id=%u AND user_id=%u ";
$xmlmymodules_getModules = "SELECT dir_item.id,dir_item.name,dir_item.typ,secured FROM dir_item,dir_cat_item,dir_category WHERE editor_id=%u AND status='O' AND item_id=dir_item.id AND category_id=dir_category.id ORDER BY name ";
$xmlmymodules_getTempModules = "SELECT id,name FROM temp_dir_item WHERE editor_id=%u AND status='N' ORDER BY name ";
$xmlmodules_getTabInfo = "SELECT name,style,def,md5pass,width,controls,advise,showtype,usereader,nbnews,modulealign FROM profile WHERE user_id=%u AND id=%u ";
$xmlmodules_getModules = "SELECT 
                                id,dir_item.url AS url,posx,posy,posj,
                                x,y,variables,height,minwidth,
                                sizable,name,website,status,uniq,
                                format,nbvariables,blocked,minimized,usereader,
                                autorefresh,dir_item.views,dir_item.icon,dir_item.l10n
                            FROM dir_item, 
                                module 
                            WHERE 
                                module.user_id=%u 
                                AND module.profile_id=%u 
                                AND module.item_id=dir_item.id 
                            ORDER BY 
                                posx,
                                posy,uniq ";
$xmlfeeds_getRefreshDate = "
    SELECT  url,
            title,
            (NOW()-lastloadedtime) AS delai,
            lastloadedid,
            http_last_modified
    FROM    dir_rss
    WHERE   id = %u ";
$xmlfeeds_setLoadDate = "UPDATE dir_rss SET lastloadedtime=NOW() WHERE id=%u AND (NOW()-lastloadedtime)>%u ";
$xmlfeeds_setLastId = "
    UPDATE  dir_rss
    SET     lastloadedid = %s,
            http_last_modified = %s
    WHERE   id = %u ";
$xmlfeeds_setTitle = "UPDATE dir_rss SET title=%s WHERE id=%u ";
$xmlfeeds_getItems = "
    SELECT id,title,link,description,image,
	       video,audio,pubdate,source,0 AS status
      FROM feed_articles
     WHERE feed_id=%u
  ORDER BY id DESC
     LIMIT %u,%u
";
$xmlcheckfeed_getIcon = "SELECT id,icon FROM dir_rss WHERE url=%s ";
$xmlcheckfeed_setUrl = "INSERT INTO dir_rss (url,icon,title,lastloadedid) VALUES (%s,0,'','') ";
$xmlgroup_getName = "SELECT name FROM users_group WHERE id=%u ";
$xmlgroup_getChildrenName = "SELECT id, name FROM users_group WHERE parent_id = %u ORDER BY name ";
$scrunlock_checkPass = "SELECT user_id FROM profile WHERE user_id=%u AND id=%u AND md5pass=%s ";
$scrsuppersonal_deleteTab = "DELETE FROM profile WHERE user_id=%u AND id=%u ";
$scrsuppersonal_deleteModules = "DELETE FROM module WHERE user_id=%u AND profile_id=%u ";
$scrsuppersonal_updateTabPos = "UPDATE profile SET seq=seq-1 WHERE user_id=%u AND seq>%u ";
$scrsubscribe_checkUser = "SELECT id,username,pass,long_name,typ,lastconnect_date,md5pass,md5user,lang,description,picture,stat,activity,extra,keywords FROM users WHERE username=%s AND typ IN ('I','N')";
$scrsubscribe_addUser = "INSERT INTO users(username,pass,typ,lastconnect_date,md5pass,long_name,md5user,lang,statdate) VALUES (%s,'','J',CURRENT_DATE,%s,%s,%s,%s,'0000-00-00')";
$scrsubscribe_log = "INSERT INTO log (action,pubdate,param1) VALUES (3,CURRENT_DATE,%u) ";
$scrsubscribe_addNewsletter = "INSERT INTO portaneo_newsletter_users (email, status, reg_date) VALUES (%s,'Y', CURRENT_DATE) ";
$scrsubscribe_updNewsletter = "UPDATE portaneo_newsletter_users SET status='Y' WHERE email=%s ";
$scrsetshowtype_update = "UPDATE profile SET showtype=%s WHERE user_id=%u AND id=%u ";
$scrsetdefault_setDefault = "UPDATE profile SET def='N' WHERE user_id=%u ";
$scrsetdefault_chooseDefault = "UPDATE profile SET def='Y' WHERE user_id=%u AND id=%u ";
$scrsavevar_updateVar = "UPDATE module SET variables=%s WHERE user_id=%u AND profile_id=%u AND uniq=%u ";
$scrnewspaper_setNbNews = "UPDATE profile SET nbnews=%u WHERE user_id=%u AND id=%u ";
$scrmymodules_removeTempMod = "DELETE FROM temp_dir_item WHERE editor_id=%u AND id=%u ";
$scrmymodules_removeTempModDirectory = "DELETE FROM temp_dir_cat_item WHERE item_id=%u ";
$scrmovetab_moveRight = "
    UPDATE  profile
    SET     seq = seq + 1
    WHERE   user_id = %u
        AND seq >= %u
";
$scrmovetab_movedTab = "
    UPDATE  profile
    SET     seq = %u
    WHERE   user_id = %u
        AND id = %u
";
$scrmovetab_moveLeft = "
    UPDATE  profile
    SET     seq = seq - 1
    WHERE   user_id = %u
        AND seq > %u
";
$scrminimize_minimizeModule = "UPDATE module SET minimized=%u WHERE user_id=%u AND profile_id=%u AND uniq=%u ";
$scrfeed_setAsRead = "INSERT INTO feed_articles_read (article_id,user_id,status) VALUES (%u,%u,1) ";
$scrfeed_setAsUnread = "DELETE FROM feed_articles_read WHERE article_id=%u AND user_id=%u ";
$screxchangemod_update = "UPDATE module SET item_id=%u, variables='' WHERE user_id=%u AND profile_id=%u AND item_id=%u ";
$scrcreateportal_getTabs = "SELECT MAX(seq) FROM profile WHERE user_id=%u ORDER BY id ASC ";

//$createportal_addModule_old = "INSERT INTO module (item_id, user_id, profile_id, posx, posy, posj,x,y, typ, variables,uniq,blocked,minimized,feed_id) 
	//SELECT id, %s, %u,%u,%u,%u,%u,%u,'D',%s,%u,%u,%u,%u FROM dir_item WHERE id=%u ";
	
$createportal_addModule = "INSERT INTO module (item_id,user_id,profile_id,posx,posy,posj,x,y,typ,variables,uniq,blocked,minimized,feed_id) 
							VALUES (%u,%s,%u,%u,%u,%u,%u,%u,'D',%s,%u,%u,%u,%u)";	
	
$scrcreateportal_addTab = "INSERT INTO profile (user_id, name, width, style, def, creation_date, modif_date, height, cacheurl,showtype,nbnews,seq,icon,modulealign,type,param,page_id,removable) 
	VALUES (%u, %s, %u, %u,'N', CURRENT_DATE, CURRENT_DATE, 5,%s,%s,%u,%u,%s,%s,%s,%s,%u,%u) ";
$fri_scrcreateportal_addTab = "INSERT INTO profile (user_id, name, width, style, def, creation_date, modif_date, height, cacheurl,showtype,nbnews,seq,icon,modulealign,type,param,page_id,removable) 
	VALUES (%u, %s, %u, %u,'N', CURRENT_DATE, CURRENT_DATE, 5,%s,%s,%u,%u,%s,%s,%s,%s,%u,%u) ";
$scrcontrols_updateCtrl = "UPDATE profile SET controls=%s WHERE user_id=%u AND id=%u ";
$scrconfig_addNewModule = "INSERT INTO module (item_id, user_id, profile_id, posx, posy,x,y, typ, variables,uniq,feed_id,shared) SELECT id,%u,%u,1,99,0,0,'D',%s,%u,%u,%s  FROM dir_item WHERE id=%u ";
$scrconfigplace_addNewMod = "INSERT INTO module (item_id,user_id,profile_id,posx,posy,posj,x,y,typ,variables,uniq,feed_id) VALUES (%u,%u,%u,%u,%u,%u,%u,%u,'D',%s,%u,%u) ";
$scrconfigplace_removeMod = "DELETE FROM module WHERE user_id=%u AND profile_id=%u AND item_id=%u AND uniq=%u ";
$scrconfigplace_updateMod = "UPDATE module 
                             SET 
                                    posx=%u, 
                                    posy=%u, 
                                    posj=%u, 
                                    x=%u, 
                                    y=%u 
                             WHERE 
                                    user_id=%u 
                                    AND profile_id=%u 
                                    AND item_id=%u 
                                    AND uniq=%u ";
$scrconfigoptions_setDefAll = "UPDATE profile SET def='N' WHERE user_id=%u ";
$scrconfigoptions_setDef = "UPDATE profile SET def='Y' WHERE user_id=%u AND id=%u ";
$scrconfigoptions_getPass = "SELECT md5pass FROM users WHERE id=%s ";
$scrchangeuniq_setUniq = "UPDATE module SET uniq=%u WHERE user_id=%u AND profile_id=%u AND posx=%u AND posy=%u ";
$scrchangepwd_changePass = "
    UPDATE  users
    SET     pass='',
            md5pass=MD5(%s)
    WHERE   id=%u
        AND md5pass=MD5(%s)
";
$scrchangepwd_changePass_md5 = "UPDATE users SET md5pass=MD5(%s) WHERE md5pass=%s and username=%s";
$scrchangepwd_changePortalPass = "UPDATE profile SET md5pass=MD5(%s) WHERE user_id=%u and md5pass=MD5(%s) ";
$scraddstats_logPortalOpening = "INSERT INTO log (action,pubdate,param1,param2) VALUES (2,CURRENT_DATE,%u,%u) ";
$scraddstats_logModuleOpening = "INSERT INTO log (action,pubdate,param1,param2,param3) SELECT 1,CURRENT_DATE,user_id,item_id,variables FROM module WHERE user_id=%u AND profile_id=%u ";
$scrothertab_getNewPos = "SELECT MAX(posy)+1 AS newpos FROM module WHERE posx=1 AND profile_id=%u AND user_id=%u ";
$scrothertab_updateModule = "UPDATE module SET profile_id=%u,posx=1,posy=%u,x=30,y=150,uniq=0 WHERE user_id=%u AND uniq=%u AND profile_id=%u ";
$scrintab_getNewPos = "SELECT MAX(posy)+1 AS newpos FROM module WHERE posx=1 AND profile_id=%u AND user_id=%u ";
$scrintab_addModule = "INSERT INTO module (item_id, user_id,profile_id,posx,posy,x,y,typ,variables,uniq,blocked,minimized,feed_id) SELECT id,%u,%u,1,%u,30,150,typ,%s,0,0,0,%u FROM dir_item WHERE id=%u ";
$fri_scrintab_addModule = "INSERT INTO module 
(item_id, user_id,profile_id,posx,posy,posj,x,y,typ,variables,uniq,blocked,minimized,feed_id) 
SELECT item_id,%u,%u,	       posx,posy,posj,x,y,typ,%s,				0,	 0,		 0,				   %u FROM module WHERE item_id=%u AND user_id = %u and profile_id = %u";
$passwordmissing_updatePass = "UPDATE users SET md5pass=%s WHERE username=%s AND typ!='A' ";
$addtoapplication_getModule = "
	SELECT	a.id,
			a.name,
			a.url,
			a.height,
			a.format,
			b.variables,
			a.nbvariables
	FROM	dir_item AS a,
			module AS b
	WHERE	b.shared=%s
		AND b.uniq=%u
		AND b.profile_id=%u
		AND a.id=b.item_id
	LIMIT	0,1 ";
$addtoapplication_getRssInfo = "SELECT id,name,url,height,format,%s,nbvariables,defvar FROM dir_item,dir_cat_item WHERE id=item_id AND id=%u LIMIT 0,1 ";
$addtoapplication_getUserRssInfo = "SELECT id,name,url,height,format,%s,nbvariables,defvar FROM dir_item WHERE id=86 LIMIT 0,1 ";
$addtoapplication_getModuleById = "SELECT id,name,url,height,format,defvar,nbvariables FROM dir_item,dir_cat_item WHERE id=item_id AND id=%u LIMIT 0,1 ";
$xmlsearch_searchSecuredModule = "SELECT DISTINCT dir_item.id, SUM(weight) AS wei, dir_item.name FROM search_index, search_keyword, dir_item, dir_cat_item, users_group_category_map, users_group_map 
		WHERE dir_item.id=search_index.item_id AND search_keyword.id=kw_id AND status='O' AND label_simplified IN ('%s') AND dir_item.id=dir_cat_item.item_id AND dir_cat_item.category_id=users_group_category_map.category_id AND users_group_category_map.group_id=users_group_map.group_id AND users_group_map.user_id=%u 
		GROUP BY search_index.item_id ORDER BY wei DESC,notation DESC ";
$xmlexplorer_getItems = "
    SELECT  a.id,
            a.name,
            a.icon
    FROM    dir_item AS a,
            dir_cat_item AS b,
            users_group_category_map AS c,
            users_group_map AS d
    WHERE   a.id = b.item_id
        AND b.category_id = %u
        AND b.category_id = c.category_id
        AND c.group_id = d.group_id
        AND d.user_id = %u ";
$xmldirectory_getChildrenDirectoryXml = "
    SELECT  id,
            name,
            quantity,
            secured,
            secured_quantity,
            lang
    FROM    dir_category
    WHERE   parent_id = %u
    ORDER BY name ";
$xmlautocompletion_get="SELECT id,label FROM search_keyword WHERE label_simplified LIKE %s ORDER BY label_simplified LIMIT 0,10 ";
$getKeyWordId = "SELECT id FROM search_keyword WHERE label_simplified=%s ";
$addKeyword = "INSERT INTO search_keyword (label,label_simplified) VALUES (%s,%s) ";
$scrChangeLang = "UPDATE users SET lang=%s WHERE id=%u ";
$config_getNotification = "SELECT id,lang,subject,message,sender,copy FROM adm_mail WHERE lang=%s AND libelle='%s'";
$xmlfeeds_setLoadAndAccessDate = "UPDATE dir_rss SET lastloadedtime=NOW(),lastaccess=CURRENT_DATE WHERE id=%u AND (NOW()-lastloadedtime)>%u ";
$xmlapplications_getApps = "
	SELECT	DISTINCT a.id,
			a.title,
			a.icon,
			a.action
	FROM	applications AS a,
			applications_groups_map AS b,
			users_group_map AS c
	WHERE	c.user_id=%u
			AND c.group_id=b.group_id
			AND b.application_id=a.id
	ORDER BY a.title";
$xmlapplications_getAllApps = "
	SELECT	id,
			title,
			icon,
			action
	FROM	applications
	ORDER BY title";
$xmlpublicpage_getPage = "SELECT id,name,description,position,type,param,seq,nbcol,showtype,npnb,style,modulealign,controls,icon,removable FROM pages WHERE type=1 AND id=%u";
$xmlpublicpage_getWidgets = "SELECT item_id,posx,posy,posj,x,y,variables,uniq,blocked,minimized FROM pages_module WHERE page_id=%u";
$xmlpublicpages_getAllPages = "SELECT id,name,description,group_id FROM pages WHERE type=1 ORDER BY name";
$xmlpublicpages_getAllPagesForTheUserGroup = "
                       (SELECT id,name,description,group_id
                          FROM pages
                         WHERE group_id=0
                           AND type=1
                      ORDER BY name)
                    UNION ALL
                       (SELECT a.id,a.name,a.description,a.group_id
                         FROM pages AS a,
                              users_group_map AS b
                        WHERE a.group_id=b.group_id
                          AND b.user_id=%u
                          AND a.type=1
                        ORDER BY a.name)
                    ";
$xmlpages_getLatestNews = "SELECT 
                                b.id AS aid,b.title AS atitle,
                                c.id AS fid,c.title AS ftitle,b.link,
                                c.iconid
                            FROM 
                                module AS a,
                                feed_articles AS b,
                                dir_rss AS c
                            WHERE a.user_id=%u 
                                    AND a.feed_id!=0 
                                    AND a.feed_id=b.feed_id 
                                    AND c.id=b.feed_id
                            ORDER BY 
                                b.id DESC LIMIT %u,%u";
$xmlpages_getLatestNewsNb = "SELECT 
                                b.id AS aid,b.title AS atitle,
                                c.id AS fid,c.title AS ftitle,
                                b.link,c.iconid 
                            FROM 
                                module AS a,
                                feed_articles AS b,
                                dir_rss AS c
                            WHERE 
                                a.user_id=%u 
                                AND a.feed_id!=0 
                                AND a.feed_id=b.feed_id 
                                AND c.id=b.feed_id";
$xmlpages_getLatestNewsFiltered = "SELECT 
                                        b.id AS aid,b.title AS atitle,
                                        c.id AS fid,c.title AS ftitle,
                                        b.link,c.iconid
                                    FROM module AS a,feed_articles AS b,
                                        dir_rss AS c
                                    WHERE 
                                        a.user_id=%u 
                                        AND a.feed_id!=0 
                                        AND a.feed_id=b.feed_id 
                                        AND c.id=b.feed_id 
                                        AND a.profile_id=%u
                                    ORDER BY b.id DESC LIMIT %u,%u";
$xmlfeeds_getItemsWithStatus = "
    SELECT DISTINCT a.id,a.title,a.link,a.description,a.image,
	       a.video,a.audio,a.pubdate,a.source,b.status
      FROM feed_articles AS a
           LEFT JOIN feed_articles_read AS b
		          ON b.article_id=a.id
		         AND b.user_id=%u
     WHERE a.feed_id=%u
       AND ( b.status<>2 OR b.status is NULL)
  ORDER BY a.id DESC
     LIMIT %u,%u
";
$xmlfeeds_getItemStatus = "
    SELECT status
      FROM feed_articles_read
     WHERE user_id=%u
       AND article_id=%u
";
$xmlfeeds_updateItemStatus = "UPDATE feed_articles_read SET status=2 WHERE user_id=%u AND article_id=%u ";
$xmlfeeds_setAsDelete = "INSERT INTO feed_articles_read (article_id,user_id,status) VALUES (%u,%u,2) ";
$criteria_getEditableCriterias = "SELECT id,label,type,options, mandatory,parameters FROM adm_userinfo, users_info WHERE adm_userinfo.id = users_info.info_id AND editable=1 AND users_info.user_id=%u";
$criteria_getCompleteCriterias = "SELECT id,label,type,options,parameters,editable FROM adm_userinfo,users_info WHERE adm_userinfo.id=users_info.info_id AND user_id=%u ";
$users_updateUserInfos = "UPDATE users_info SET parameters=%s WHERE user_id=%u AND info_id=%u";
$criteria_getInformations = "SELECT id,label,type,options,mandatory,editable FROM adm_userinfo";
$users_addUserInfos = "INSERT INTO users_info (user_id,info_id,parameters,ispublic) VALUES (%u,%u,%s,'1') ";
$xmlfeeds_getTotalNbArticles = "SELECT count(*) FROM feed_articles WHERE feed_id=%u ";
$xmlfeeds_getNbUnreadArticles = "SELECT count(a.id) FROM feed_articles AS a,feed_articles_read AS b WHERE a.feed_id=%u AND a.id=b.article_id AND b.user_id=%u AND b.status!=0 ";
$scrconfig_updatetabstatus = "UPDATE profile SET status=%u WHERE id=%u AND user_id=%u";
$xmlmostread_getArticles = "
	SELECT a.id, a.title AS article,c.id AS sourceid,c.title AS source, a.link, count( b.user_id ) AS nb
		FROM feed_articles AS a, feed_articles_read AS b,dir_rss AS c
			WHERE a.id = b.article_id
				AND c.id=a.feed_id
				AND b.status=1
			GROUP BY a.id
			ORDER BY nb DESC
			LIMIT %u,20";
$user_getLang = "SELECT lang FROM users WHERE username=%s";
$scrsubscribe_setUserValidationKey="
  UPDATE users
     SET md5user='%s'
   WHERE id=%u
";
$widgetforyoursite_getWidget="
   SELECT a.id,
          a.height,
		  a.defvar,
		  a.url,
		  a.format,
		  a.nbvariables,
          a.l10n
     FROM dir_item AS a,
	      dir_cat_item AS b,
		  dir_category as c
    WHERE a.id=%u
	      AND a.status='O'
		  AND a.id=b.item_id
		  AND b.category_id=c.id
		  AND c.secured=0
";
$login_checkAccountConfirmation="
   SELECT id
     FROM users
	WHERE md5user=%s
	      AND id=%u 
		  AND typ='J'
";
$login_activateAccount="
  UPDATE users
     SET typ='I'
   WHERE id=%u
";
$authentication_get_logins="
  SELECT login,ip,date 
	FROM users_control 
  WHERE ip=%s 
		AND login=%s 
		AND (now()-date)<%u;
";
$authentication_increment_number_of_try="
	UPDATE	users_control
	SET		number_of_try=number_of_try+1
	WHERE	login=%s
		AND ip=%s
		AND (now()-date)<%u;
";
$authentication_get_number_of_try="
	SELECT	number_of_try
	FROM	users_control
	WHERE	login=%s
		AND ip=%s
		AND (now()-date)<%u;
";
$authentication_get_date="
	SELECT	unix_timestamp(date) AS date
	FROM	users_control
	WHERE	login=%s
		AND ip=%s
		AND (now()-date)<%u;
";
$authentication_enter_new_try="
   INSERT INTO	users_control (
				login,
				ip,
				date,
				number_of_try
				)
	VALUES		(
				%s,
				%s,
				now(),
				1
				) ";
$authentication_get_code="
	SELECT	code
	FROM	captcha_codes
	WHERE	id=%s ";
    
$authentication_erase_code="
	DELETE FROM	captcha_codes
	WHERE		code=%s ";
    
global $mail_getNotification;
$mail_getNotification = "
	SELECT	id,
			lang,
			subject,
			message,
			sender,
			copy 
    FROM	adm_mail 
	WHERE	lang=%s 
	AND		libelle=%s ";
$authentication_updateConnectDate="
	UPDATE	users
	SET		lastconnect_date=NOW()
	WHERE	id=%u
";
$getUserpage="
	SELECT	id
	FROM	profile
	WHERE	user_id=%u
	AND		page_id=%u
";                  
$tutorial_getSource = "
	SELECT	source,
			xmlmodule,
			url 
	FROM	dir_item_external
	WHERE	item_id=%u
";
$tutorial_getL10NSource = "
	SELECT	source,
			url 
	FROM	dir_item_external_language
	WHERE	item_id=%u
            lang=%s,
            view=%s,
            viewtype=%s
";
$scrunsharepage = "
	UPDATE	profile
	SET		shared=''
	WHERE	user_id=%u
	AND		id=%u
";
$getTopArticles = "
	SELECT	a.id,
			a.title,
			count(b.article_id) AS nb
	FROM	feed_articles a,
			feed_articles_read b
	WHERE	a.id=b.article_id
	GROUP BY a.title
	ORDER BY nb DESC
	LIMIT	0,10
";
$getXmlItem = "
	SELECT	a.id,
			a.name,
			a.url,
			a.height,
			a.minwidth,
			a.sizable,
			a.format,
			a.defvar,
			a.description,
			a.website,
			a.nbvariables,
			a.usereader,
			a.autorefresh,
			a.editor_id,
			a.creation_date,
			e.long_name,
            a.views,
            a.icon,
            a.l10n
	FROM	dir_item AS a,
			dir_cat_item AS b,
			users_group_category_map AS c,
			users_group_map AS d,
			users AS e
	WHERE	a.id = %u
		AND a.id = item_id
		AND b.category_id = c.category_id
		AND c.group_id = d.group_id
		AND user_id = %u
		AND e.id = editor_id
";
$xmlcheckfeed_setUrlAndIcon = "INSERT INTO dir_rss (url,icon,title,lastloadedid,iconid) VALUES (%s,0,'','',%s) ";
$xmlcheckfeed_getIconId = "SELECT id,icon,iconid FROM dir_rss WHERE url=%s ";
$xmlfeeds_setIconId = "UPDATE dir_rss SET iconid=%s WHERE id=%u ";
$users_unsubscribe = "UPDATE users SET typ='J' WHERE id=%u AND md5user=%s";
$adm_getGroupId = "SELECT group_id FROM adm_group_map WHERE user_id=%u";
$users_getGroupCategoryMapXml = "SELECT category_id FROM users_group_category_map WHERE group_id IN (%s)";    
$module_getChildrenAdminDirectoryXml = "SELECT id, name, quantity, secured FROM dir_category WHERE id IN (%s) AND parent_id=%u ORDER BY name ";                            
$users_getWidgetsCategories = "SELECT id, name, quantity, secured FROM users_group_category_map,dir_category,users_group_map WHERE users_group_map.group_id=users_group_category_map.group_id AND dir_category.id=users_group_category_map.category_id AND users_group_map.user_id=%u AND parent_id=%u";

//the following requests are linked to rating
/*
$xml_count_article_ratings = "
	SELECT 	COUNT(article_rating) AS counter 
	FROM 	feed_articles_read 
	WHERE 	article = '$article_id'
";
*/
$xml_article_ratings = "
	SELECT 	article_rating 
	FROM 	feed_articles_read
	WHERE 	article_id = %u
";
$xml_user_control_rating = "
	SELECT article_id
	FROM feed_articles_read
	WHERE user_id = %u
	AND article_id = %u
";
$scr_change_article_rating = "
	UPDATE 	feed_articles_read 
	SET 	article_rating = %u 
	WHERE 	user_id = %u
	AND 	article_id = %u
";
/*
$scr_delete_rating = "
	UPDATE 	feed_articles_read 
	SET		article_rating = NULL
	WHERE 		user_id = %u
	AND 		article_id = %u
";
*/
$scr_delete_rating = "
	DELETE FROM	feed_articles_read 
	WHERE 		user_id = %u
	AND 		article_id = %u
";
/*
$xml_count_infeed_article_ratings = "
	SELECT 	COUNT(article_rating) AS counter
	FROM 	feed_articles_read R,
			feed_articles F 
	WHERE 	user_id = '$user_id' 
	AND		feed_id = '$feed_id' 
	AND		R.article_id = F.id 
";
*/
$xml_feed_user_implicit_rating = "
	SELECT 	article_rating
	FROM 	feed_articles_read R,
			feed_articles F 
	WHERE 	user_id = %u
	AND		feed_id = %u
	AND 	R.article_id = F.id 
";
$scr_rate_article = "
	INSERT INTO feed_articles_read(
				article_id,
				user_id,
				status,
				article_rating,
				rating_timestamp
				) 
	VALUES 		( 
				%u,
				%u,
				'1',
				%u,
				%u
				)
";
/*
$xml_count_user_ratings = "
	SELECT 	COUNT(article_rating) AS counter
	FROM 	feed_articles_read 
	WHERE 	user_id = '$user_id'
";
*/
$xml_user_ratings = "
	SELECT 	article_rating 
	FROM 	feed_articles_read 
	WHERE 	user_id = %u
";
$xml_user_article_rating = "
	SELECT 	article_rating 
	FROM 	feed_articles_read 
	WHERE 	article_id= %u 
	AND 	user_id = %u
";
$xmlpages_getUnratedArticlesNb = "
	SELECT 		F.id as aid,
				F.title as atitle,
				S.id as fid,
				S.title as ftitle, 
				F.link as link,
				M.item_id as mod_id,
				M.uniq as uniq,
				P.seq as seq
	FROM		feed_articles_read R,
				feed_articles F,
				dir_rss S,
				module M,
				profile P
	WHERE 		R.user_id = %u 
	AND			M.profile_id = P.id
	AND			M.user_id = R.user_id
	AND	 		R.article_id = F.id 
	AND 		M.feed_id = F.feed_id
	AND			F.feed_id = S.id
	AND			R.status = 1
	AND			R.article_rating IS NULL		
";
$xmlpages_getUnratedArticles = "
	SELECT 		F.id as aid,
				F.title as atitle,
				S.id as fid,
				S.title as ftitle, 
				F.link as link,
				M.item_id as mod_id,
				M.uniq as uniq,
				P.seq as seq
	FROM		feed_articles_read R,
				feed_articles F,
				dir_rss S,
				module M,
				profile P
	WHERE 		R.user_id = %u 
	AND			M.profile_id = P.id
	AND			M.user_id = R.user_id
	AND	 		R.article_id = F.id 
	AND 		M.feed_id = F.feed_id
	AND			F.feed_id = S.id
	AND			R.status = 1
	AND			R.article_rating IS NULL
    ORDER BY 	F.id DESC LIMIT %u,%u
";
$xmlpages_getUnratedArticlesFiltered = "
	SELECT 		F.id as aid,
				F.title as atitle,
				S.id as fid,
				S.title as ftitle, 
				F.link as link,
				M.item_id as mod_id,
				M.profile_id as tab_id,
				M.uniq as uniq
	FROM		feed_articles_read R,
				feed_articles F,
				dir_rss S,
				module M
	WHERE 		R.user_id = %u 
	AND			M.user_id = R.user_id
	AND	 		R.article_id = F.id 
	AND 		M.feed_id = F.feed_id
	AND			F.feed_id = S.id
	AND			R.status = 1
	AND			R.article_rating IS NULL
	AND			M.profile_id = %u
    ORDER BY 	F.id DESC LIMIT %u,%u
";
$module_getChildrenDirectoryXml = "
    SELECT  id,
            name,
            quantity,
            secured
    FROM    dir_category
    WHERE   parent_id = %u
    ORDER BY name ";
$explorer_getCurrentDirectory = "
    SELECT  name,
            secured,
            parent_id,
            quantity + secured_quantity AS q
    FROM    dir_category
    WHERE   id = %u";
$xmlmodules_getInfoModules = "SELECT 
                                id,dir_item.url AS url,posx,posy,posj,
                                x,y,variables,height,minwidth,
                                sizable,name,website,status,uniq,
                                format,nbvariables,blocked,minimized,usereader,
                                autorefresh,dir_item.views,dir_item.icon,dir_item.l10n,shared
                            FROM dir_item, 
                                module 
                            WHERE 
                                module.user_id=%u 
                                AND module.profile_id=%u 
                                AND module.item_id=dir_item.id 
                            ORDER BY 
                                posx,
                                posy,uniq ";
$xmlconfig_getMaxUniq = "SELECT (MAX(uniq)+1) AS uniq 
                         FROM module 
                         WHERE user_id=%u 
                         AND profile_id=%u";
 ?>
