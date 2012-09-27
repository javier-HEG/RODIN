<?php
# ************** LICENCE ****************
/*
	Copyright (c) PORTANEO.

	This file is part of COLLABORATION SUITE of POSH http://sourceforge.net/projects/posh/.

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
$xmlnbportals_countPortals ="SELECT count(id) AS nb FROM portals,portals_category WHERE id=portal_id AND status = 'O'";
$xmlredactorfeeds_getFeeds = "SELECT id,title,admin FROM redactor_feeds AS rf,redactor_map_feeds AS rmf WHERE rf.id=rmf.feed_id AND user_id=%u ORDER BY title ";
$xmlportaltovalidate_getTempPortals = "SELECT id, name, status FROM portals, portals_category
	WHERE status='N' AND id=portal_id ORDER BY name ASC, id DESC LIMIT %u,21 ";
$xmlportalsdir_getChildren = "SELECT id, name, %s FROM %s WHERE parent_id = %u ORDER BY name ";
$xmlportalsdir_getPortals = "SELECT id, name FROM portals, portals_category WHERE id = portal_id AND category_id = %u AND status = 'O' ";
$xmlportal_getPortal = "
	SELECT	name,
			status,
			user_id,
			width,
			style,
			showtype,
			modulealign
	FROM	profile
	WHERE	id = %u
	AND		shared = '3' ";
$xmlportal_getModules = "
	SELECT	item_id,
			name,
			posx,
			posy,
			posj,
			x,
			y,
			variables,
			height,
			website,
			minwidth,
			sizable,
			url,
			format,
			nbvariables,
			usereader,
			autorefresh
	FROM	module,
			dir_item 
	WHERE	profile_id = %u
		AND item_id = id
		AND shared = '3'
	ORDER BY posx,
			posy,
			posj";
$xmlcacheportaldir_getChildren = "SELECT id, name FROM %s WHERE parent_id = %u ORDER BY name ";
$xmlcacheportaldir_getPortals = "SELECT id, name FROM portals_category,portals WHERE id = portal_id AND category_id = %u AND status = 'O' ";
$xmlcacheportal_getPortal = "SELECT name,description,status,author,nbcol,style,position FROM portals, portals_category WHERE id=%u AND id=portal_id ";
$xmlcacheportal_getModules = "SELECT item_id,name,posx,posy,posj,x,y,variables,height,website,minwidth,sizable,url,format,nbvariables,blocked,usereader,autorefresh FROM portals_module,dir_item 
	WHERE portal_id=%u AND item_id=id ORDER BY posx,posy,posj ";
$statsmodules_getDailyTopModules = "SELECT name,sum(result2) AS tot FROM stats_final,dir_item WHERE action=4 AND result1=id AND MONTH(treatdate)=%s AND YEAR(treatdate)=%s GROUP BY name ORDER BY tot DESC LIMIT 50 ";
$statsmodules_getMonthlyTopModules = "SELECT name,sum(result2) AS tot FROM stats_final,dir_item WHERE action=4 AND result1=id AND YEAR(treatdate)=%s GROUP BY name ORDER BY tot DESC LIMIT 50 ";
$statsmodules_getDailyTopDirRss = "SELECT name,sum(result2) AS tot FROM stats_final,dir_item WHERE action=7 AND result1=id AND MONTH(treatdate)=%s AND YEAR(treatdate)=%s GROUP BY name ORDER BY tot DESC LIMIT 50 ";
$statsmodules_getMonthlyTopDirRss = "SELECT name,sum(result2) AS tot FROM stats_final,dir_item WHERE action=7 AND result1=id AND YEAR(treatdate)=%s GROUP BY name ORDER BY tot DESC LIMIT 50 ";
$statsmodules_getDailyTopRss = "SELECT result1,sum(result2) AS tot FROM stats_final WHERE action=5 AND MONTH(treatdate)=%s AND YEAR(treatdate)=%s GROUP BY result1 ORDER BY tot DESC LIMIT 50 ";
$statsmodules_getMonthlyTopRss = "SELECT result1,sum(result2) AS tot FROM stats_final WHERE action=5 AND YEAR(treatdate)=%s GROUP BY result1 ORDER BY tot DESC LIMIT 50 ";
$frmportalmodify_getPortal = "SELECT portals.name,portals.description,portals.status,category_id,dirtable.name AS name2 FROM portals,portals_category,%s AS dirtable 
	WHERE portals.id=portal_id AND dirtable.id=category_id AND portals.id=%u ";
$frmportalmodify_getModules = "SELECT id,name FROM portals_module, dir_item WHERE item_id=id AND portal_id=%u ";
$scrportalmodify_updatePortal = "UPDATE portals SET name=%s,description=%s,status=%s WHERE id=%u ";
$scrportalmodify_updateDirectory = "UPDATE portals_category SET category_id=%u WHERE portal_id=%u ";
$xmlnbportaltovalidate_count ="SELECT count(id) AS nb FROM portals,portals_category WHERE id=portal_id AND status = 'N'";
$statsmodules_countModOpening = "INSERT INTO stats_final (action,treatdate,result1,result2) 
	SELECT 4,pubdate,param2,count(param3) FROM log,dir_item WHERE dir_item.id=param2 AND format!='R' AND action=1 AND param2!=86 AND pubdate!=CURRENT_DATE AND param2!='86' GROUP BY pubdate,param2 ";
$statsmodules_countDirRssOpening = "INSERT INTO stats_final (action,treatdate,result1,result2) 
	SELECT 7,pubdate,param2,count(param3) FROM log,dir_item WHERE dir_item.id=param2 AND format='R' AND action=1 AND param2!=86 AND pubdate!=CURRENT_DATE AND param2!='86' GROUP BY pubdate,param2 ";
$statsmodules_countRssOpening = "INSERT INTO stats_final (action,treatdate,result1,result2) 
	SELECT 5,pubdate,SUBSTRING(param3,LOCATE('rssurl',param3)+7,LOCATE('&',SUBSTRING(param3,LOCATE('rssurl',param3)))-7) AS rssurl,count(param2) FROM log WHERE action=1 AND pubdate!=CURRENT_DATE AND param2='86' GROUP BY pubdate,rssurl ";
$articlemodifyadd_getUser = "
	SELECT	username,
			long_name,
			picture,
			description,
			stat,
			keywords
	FROM	users
	WHERE	id = %u ";
//$articlemodifyadd_getKeywords = "SELECT label FROM network_keywords,search_keyword WHERE kw_id=search_keyword.id AND user_id=%u AND friend_id=0 ";
$articlemodifyadd_getNotebookInfo = "SELECT a.title,a.description,a.keywords,a.status FROM notebook_article AS a,notebook_article_users AS b WHERE a.id=%u AND b.article_id=a.id AND b.owner_id=b.user_id AND b.user_id=%u ";
$articlemodifyadd_getGroupbookInfo = "SELECT a.title, a.description, a.keywords, a.status FROM notebook_article AS a, notebook_groups_articles_map AS b WHERE a.id=%u AND b.article_id=a.id AND b.owner_id=b.user_id AND b.group_id=%u ";
$scrnotebook_addTrackback = "
	INSERT INTO	notebook_article_users (
				user_id,
				article_id,
				owner_id,
				take_on,
				pubdate)
	SELECT		%u,
				id,
				%u,
				%u,
				NOW()
	FROM		notebook_article
	WHERE		status = 3
		AND		id = %u";
$scrnotebook_updateTrackbackNb = "
	UPDATE	notebook_article
	SET		trackbacknb = trackbacknb+1
	WHERE	id = %u ";
$scrnotebook_decreaseTrackbackNb = "
	UPDATE	notebook_article
	SET		trackbacknb = trackbacknb-1
	WHERE	id = %u ";
$detail_getArticleOwner = "
	SELECT	owner_id
	FROM	notebook_article_users
	WHERE	article_id = %u
	LIMIT 	1 ";
$detail_getArticleInfo = "
	SELECT 	users.id AS userid,
			long_name,
			picture,
			title,
			a.description,
			a.keywords,
			a.pubdate,
			type,
			commentsnb,
			trackbacknb,
			owner_id,
			linked_id,
			a.status
	FROM	notebook_article AS a,
			notebook_article_users,
			users 
	WHERE	a.id = article_id
		AND users.id = owner_id
		AND a.id = %u
		AND a.status != 'D' ";

$detail_getGroupArticleInfo = "SELECT users.id AS userid, long_name, title, a.description, a.keywords, a.pubdate, type, commentsnb, trackbacknb, linked_id, a.status FROM notebook_article a, notebook_groups_articles_map as m, users 
	WHERE a.id=m.article_id AND users.id=owner_id AND a.id=%u AND m.group_id=%u AND a.status!='D' ";

$detail_getTrackbacks = "
	SELECT	user_id,
			long_name,
			picture
	FROM	notebook_article_users,
			users
	WHERE	article_id = %u
		AND user_id = users.id
		AND user_id != owner_id ";
$detail_getTrackbacksGroup = "
	SELECT	m.group_id,
			g.name
	FROM	notebook_groups_articles_map AS m,
			notebook_groups AS g
	WHERE	m.article_id = %u
		AND m.is_copy = 1
		AND m.group_id = g.id"; 

$detail_getComments = "SELECT notebook_comments.id,user_id,long_name,pubdate,message,picture FROM notebook_comments,users WHERE article_id=%u AND users.id=user_id AND notebook_comments.status='O' ";
$index_getNotebookArticles = "
	SELECT	a.id,
			users.id AS userid,
			long_name,
			picture,
			title,
			a.description,
			a.keywords,
			a.pubdate,
			type,
			commentsnb,
			trackbacknb,
			linked_id,
			a.status,
			owner_id
	FROM	notebook_article as a,
			notebook_article_users as au,
			users
	WHERE	a.id = article_id
		AND users.id = au.owner_id
		AND au.user_id = %u
		AND a.status != 'D'
		AND a.status >= %u
	ORDER BY au.pubdate DESC,
			a.id DESC
	LIMIT	%u,11 ";
$index_getNotebookSearchedArticles = "
	SELECT	a.id,
			users.id AS userid,
			long_name,
			picture,
			title,
			a.description,
			a.keywords,
            a.pubdate,
			type,
			commentsnb,
			trackbacknb,
			linked_id,
            a.status,
			au.owner_id
    FROM	notebook_article as a, 
            notebook_article_users as au, 
            users
    WHERE	a.id = article_id 
		AND users.id = au.owner_id 
        AND user_id = %u 
		AND a.status != 'D' 
		AND a.status >= %u 
		AND (title LIKE %s OR a.description LIKE %s)
	ORDER BY au.pubdate DESC,
			a.id DESC
	LIMIT	%u,11 ";
$index_getLatestArticles = "SELECT notebook_article.id,user_id,title,long_name FROM notebook_article,notebook_article_users,users WHERE status='3' AND article_id=notebook_article.id AND owner_id=users.id ORDER BY notebook_article.id DESC LIMIT 0,10 ";
//$rss_getUserInformation = "SELECT username,long_name,picture,description FROM users WHERE id=%u ";
//$rss_getRssArticles = "SELECT id,title,notebook_article.description,notebook_article_users.pubdate FROM notebook_article,notebook_article_users 
//	WHERE notebook_article.id=article_id AND user_id=%u AND notebook_article.status='3' ORDER BY notebook_article_users.pubdate DESC, notebook_article.id DESC LIMIT 0,20 ";
$scrarticlemodifyadd_addArticle = "
	INSERT INTO	notebook_article (
				title,
				description,
				keywords,
				pubdate,
				type,
				status
				)
	VALUES		(
				%s,
				%s,
				%s,
				NOW(),
				1,
				%s
				) ";
$scrarticlemodifyadd_attributeUserToArticle = "
	INSERT INTO notebook_article_users (
				user_id,
				article_id,
				owner_id,
				take_on,
				pubdate
				)
	VALUES		(
				%u,
				%u,
				%u,
				%u,
				NOW()
				) ";
$scrarticlemodifyadd_updateArticle = "
	UPDATE	notebook_article,
			notebook_article_users
	SET		title = %s,
			description = %s,
			keywords = %s,
			status = %u
	WHERE	id = %u
		AND id = article_id
		AND owner_id = %u ";
$scrnotebookcommentadd_addComment = "INSERT INTO notebook_comments (article_id,user_id,message,pubdate,status) VALUES (%u,%u,%s,NOW(),'O') ";
$scrnotebookcommentadd_updComment = "UPDATE notebook_article SET commentsnb=commentsnb+1 WHERE id=%u ";
$addportaltoapplication_getPortal = "
	SELECT	name
	FROM	profile
	WHERE	id = %u
		AND shared = %s ";
$addportaltoapplication_getModules = "
	SELECT	b.id,
			b.name,
			a.variables
	FROM	module AS a,
			dir_item AS b
	WHERE	a.profile_id = %u
		AND a.item_id = b.id
		AND a.shared = %s
	ORDER BY a.uniq";
$frmnetworkupdateprofile_updUser = "
	UPDATE	users
	SET		description = %s,
			keywords = %s
	WHERE	id = %u ";
$frmnetworkupdateprofile_updUserWPict = "
	UPDATE	users
	SET		description = %s,
			keywords = %s,
			picture = %s
	WHERE	id = %u ";
$frmnetworkupdateprofile_removeKeywords = "DELETE FROM network_keywords WHERE user_id=%u AND friend_id=0 ";
$frmnetworkupdateprofile_getNetwork = "SELECT id FROM search_keyword WHERE label_simplified=%s ";
$frmnetworkupdateprofile_addKeyword = "INSERT INTO search_keyword (label,label_simplified) VALUES (%s,%s) ";
$frmnetworkupdateprofile_linkKeyword = "INSERT INTO network_keywords (user_id,friend_id,kw_id) VALUES (%u,0,%u) ";
$frmnetworkupdateprofile_getUser = "SELECT picture,description FROM users WHERE id=%u ";
$frmnetworkupdateprofile_getKeywords = "SELECT label FROM network_keywords,search_keyword WHERE user_id=%u AND friend_id=0 and search_keyword.id=kw_id ";
//$scrarticleclassify_classify = "UPDATE users_articles SET classified=1, private=%u, description='%s' WHERE user_id=%u AND id=%u ";
$scrarticleclassify_getFromOtherArchive = "INSERT INTO users_articles (user_id,title,link,source,icon,pubdate,feedarticle_id,classified,description,private) 
	SELECT %u,title,link,source,icon,pubdate,feedarticle_id,1,%s,%u FROM users_articles WHERE user_id=%u AND id=%u ";
$scrarticleclassify_addInNotebook = "
	INSERT INTO	notebook_article (
				title,
				description,
				keywords,
				pubdate,
				feedarticle_id,
				type,
				linked_id,
				status
				)
	VALUES		(
				%s,
				%s,
				%s,
				NOW(),
				%u,
				2,
				%u,
				%s
				) ";
$scrarticleclassify_addUserLink = "INSERT INTO notebook_article_users (user_id,article_id,owner_id,take_on,pubdate) VALUES (%u,%u,%u,%u,NOW()) ";
//$scrarticleclassify_updateClass = "UPDATE users_articles SET private=%u, description='%s' WHERE user_id=%u AND id=%u ";
$scrarticleclassify_getId = "SELECT id FROM notebook_article WHERE linked_id=%u ";
$scrarticleclassify_newArticle = "
	INSERT INTO notebook_article (
				title,description,keywords,pubdate,feedarticle_id,type,linked_id) VALUES (%s,%s,%s,NOW(),%u,2,%u) ";
$scrarticleclassify_newArticleUser = "INSERT INTO notebook_article_users (user_id,article_id,owner_id,take_on,pubdate) VALUES (%u,%u,%u,%u,NOW()) ";
$scrarticleclassify_updArticle = "UPDATE notebook_article SET description=%s,keywords=%s WHERE linked_id=%u ";
$scrarticleclassify_removeKeywords = 'DELETE FROM users_articles_keywords WHERE user_id=%u AND article_id=%u ';
$scrarticleclassify_getKeyWordId = "SELECT id FROM search_keyword WHERE label_simplified=%s ";
$scrarticleclassify_addKeyword = "INSERT INTO search_keyword (label,label_simplified) VALUES (%s,%s) ";
//$scrarticleclassify_addKeywordLink = "INSERT INTO users_articles_keywords (user_id, article_id, kw_id) VALUES (%u,%u,%u) ";
$scrcontact_send = "INSERT INTO contact (dest_id, user_id, name, email, titre, texte, statut, modifdate) VALUES (%u,0, 'internaute',%s,%s,%s,'I', CURRENT_DATE) ";
$scrgroup_adduser="
	INSERT INTO	notebook_groups_users_map (
				user_id,
				group_id,
				status
				)
	SELECT		%u,
				%u,
				%s
	FROM		notebook_groups AS a,
				notebook_groups_users_map AS b
	WHERE		a.id = b.group_id
		AND		b.user_id = %u
		AND		b.group_id = %u
		AND		b.status = 'O'
		AND		(a.private = 0
                    OR (a.private = 1 AND a.created_by = %u)
                )
";
$scrgroup_countuser = "
    SELECT  user_id
    FROM    notebook_groups_users_map
    WHERE   user_id = %u
        AND group_id = %u
";
$scrnetworkremoveuser_removeUser = "DELETE FROM network WHERE user_id=%u AND friend_id=%u ";
$scrnetworkremoveuser_removeKeywords = "DELETE FROM network_keywords WHERE user_id=%u AND friend_id=%u ";
$scrnetworkadduser_addUser = "INSERT INTO network (user_id,friend_id,description) VALUES (%u,%u,%s) ";
$scrnetworkadduser_updateUser = "UPDATE network SET description=%s WHERE user_id=%u AND friend_id=%u ";
$scrnetworkadduser_removeKeywords = "DELETE FROM network_keywords WHERE user_id=%u AND friend_id=%u ";
$scrnetworkadduser_getKeyword = "SELECT id FROM search_keyword WHERE label_simplified=%s ";
$scrnetworkadduser_addNewKeyword = "INSERT INTO search_keyword (label,label_simplified) VALUES (%s,%s) ";
$scrnetworkadduser_insertKeyword = "INSERT INTO network_keywords (user_id, friend_id, kw_id) VALUES (%u,%u,%u) ";
$scrnotebookarticleadd_addLink = "INSERT INTO notebook_article_users (user_id,article_id,owner_id,take_on,pubdate) VALUES (%u,%u,%u,%u,NOW()) ";
$scrreadmessage_setAsRead = "UPDATE users_messages SET status='R' WHERE user_id=%u AND id=%u ";
$scrnotation_vote = "INSERT INTO app_notation (item_id, caract1, caract2, caract3) VALUES %u ";
$scrsavearticle_saveArticle = "INSERT INTO users_articles (user_id,title,link,source,icon,pubdate,feedarticle_id) VALUES (%u,%s,%s,%s,%s,%s,%u) ";
//$scrsendtofriend_shareModule = "INSERT INTO dir_item_shared (chk,item_id,vars) VALUES (%s,%u,%s) ";
$scrsendtofriend_shareModule = "
	UPDATE	module
	SET		shared = %s
	WHERE	user_id = %u
		AND profile_id = %u
		AND uniq = %u
";
$scrsendtofriend_setWidgetsAsShared = "
	UPDATE	module
	SET		shared = %s
	WHERE	user_id = %u
		AND profile_id = %u
";
$scrsendtofriend_sendPortalEmail = "INSERT INTO portals (name,description,author,nbcol,style,position,status) VALUES (%s,'',%u,%u,%u,%u,'O') ";
$scrsendtofriend_sharePublicPortal = "UPDATE portals SET name=%s,description=%s,md5check='%s' WHERE id=%u ";
$scrsendtofriend_getKeyword = "SELECT id FROM search_keyword WHERE label_simplified=%s ";
$scrsendtofriend_addNewKeyword = "INSERT INTO search_keyword (label,label_simplified) VALUES (%s,%s) ";
$scrsendtofriend_insertKeyword = "INSERT INTO portals_keywords (portal_id, kw_id) VALUES (%u,%u) ";
$scrsendtofriend_sharePrivPortal = "UPDATE portals SET md5check=%s WHERE id=%u ";
$scrsendtofriend_sharePortalModules = "INSERT INTO portals_module (portal_id,item_id,posx,posy,posj,x,y,variables) 
	SELECT %u,item_id,posx,posy,posj,x,y,variables FROM module WHERE user_id=%u AND profile_id=%u ";
$scrsuparticle_removeArticle = "DELETE FROM users_articles WHERE user_id=%u AND id=%u ";
$scrsuparticle_removeKeywords = "DELETE FROM users_articles_keywords WHERE user_id=%u AND article_id=%u ";
$scrsupmessage_removeMsg = "DELETE FROM users_messages WHERE user_id=%u AND id=%u ";
$xmlpagesearch_searchPortal = "
	SELECT DISTINCT	a.id,
					name
	FROM			profile AS a,
					portals_keywords AS b,
					search_keyword AS c
	WHERE			a.id = b.portal_id
		AND			c.id = b.kw_id
		AND			c.label_simplified IN (%s)
		AND			a.shared = '3'
	LIMIT			0,10 ";
$xmlnetworkusers_getUserById = "
	SELECT	b.id,
			b.long_name,
			b.username,
			b.picture,
			b.stat,
			b.statdate,
			b.statdate,
			a.description,
			b.activity,
			b.lastconnect_date,
			NOW() AS dbdate
	FROM	network AS a,
			users AS b
	WHERE 	a.user_id = %u
		AND b.typ = 'I'
		AND a.friend_id = b.id
	ORDER BY b.long_name ASC
	LIMIT	%u,21";
$xmlnetworkusers_getUserByKeyword = "
	SELECT	b.id,
			b.long_name,
			b.username,
			b.picture,
			b.stat,
			b.statdate,
			c.description,
			b.activity,
			b.lastconnect_date,
			NOW() AS dbdate
	FROM	network_keywords AS a,
			users AS b,
			network AS c
	WHERE	a.user_id = %u
		AND c.user_id = a.user_id
		AND b.typ = 'I'
		AND a.kw_id = %u
		AND a.friend_id = b.id
		AND a.friend_id = c.friend_id
	ORDER BY b.long_name ASC
	LIMIT	%s,21";
$xmlnetworkusers_getUsersByGroup = "
	SELECT	b.id,
			b.long_name,
			b.username,
			b.picture,
			b.stat,
			b.statdate,
			b.activity,
			b.lastconnect_date,
			NOW() AS dbdate
	FROM	users_group_map AS a,
			users AS b
	WHERE	a.group_id = %u
		AND b.typ = 'I'
		AND a.user_id = b.id
	ORDER BY b.long_name ASC";
$xmlnetworkusers_getDesc = "SELECT c.friend_id as id, c.description FROM users AS b, network AS c WHERE b.typ = 'I' AND c.friend_id = b.id AND c.user_id =%u";
$xmlnetworkusers_getUsersByNotebook = "
	SELECT	b.id,
			b.long_name,
			b.username,
			b.picture,
			b.stat,
			b.statdate,
			b.activity,
			b.lastconnect_date,
			NOW() AS dbdate,
			c.id AS created_by
	FROM	notebook_groups_users_map AS a,
			users AS b,
			users AS c,
			notebook_groups AS d
	WHERE	a.group_id = %u
		AND b.typ = 'I'
		AND a.user_id = b.id
		AND a.status = 'O'
		AND c.id = d.created_by
		AND d.id = a.group_id
	ORDER BY b.long_name ASC";
$xmlnetworkuserdetail_getUser = "
	SELECT	username,
			long_name,
			picture
	FROM	users
	WHERE	id = %u
";
$xmlnetworkuserdetail_getMyDescription = "
	SELECT	description
	FROM	network
	WHERE	user_id = %u
		AND friend_id = %u ";
$xmlnetworkuserdetail_getMyKeywords = "
	SELECT	label
	FROM	network_keywords,
			search_keyword
	WHERE	kw_id = search_keyword.id
		AND user_id = %u
		AND friend_id = %u ";
$xmlnetworksearch_getUserByName = "SELECT id,long_name,picture FROM users WHERE username LIKE %s AND typ='I' ORDER BY long_name ";
$xmlnetworksearch_getUserGroup = "SELECT a.id, a.name FROM users_group AS a, users_group_map AS b WHERE b.user_id=%u AND a.id=b.group_id ORDER BY a.name ASC ";
$xmlnetworksearch_getUserWorkingGroups = "
    SELECT  a.id,
            a.name,
            a.description,
            a.picture,
            a.created_by,
            b.status
    FROM    notebook_groups AS a,
            notebook_groups_users_map AS b
    WHERE   b.user_id = %u
        AND a.id = b.group_id
        AND b.status in (%s)
    ORDER BY b.status ASC,
            a.name ASC
";
$xmlnetworksearch_getUserAuthWorkingGroups = "
    SELECT  a.id,
            a.name,
            a.description,
            a.picture,
            a.created_by,
            a.status
    FROM    notebook_groups a,
            notebook_groups_users_map AS d
    WHERE   (a.created_by = %u 
            OR  (created_by != %u AND private = 0)
            )
        AND d.group_id = a.id
        AND d.user_id = %u
        AND d.status = 'O'
        AND a.id NOT IN (
                SELECT  c.id
                FROM    notebook_groups_users_map AS b,
                        notebook_groups AS c
                WHERE   c.id = b.group_id
                    AND b.user_id = %u
                )
    ORDER BY name ASC
";
$xmlnetworksearch_getUserByKeywords = "SELECT DISTINCT users.id,long_name,picture FROM users,network_keywords,search_keyword 
	WHERE search_keyword.label_simplified IN ('%s') AND typ='I' AND search_keyword.id=kw_id AND ((users.id=user_id AND friend_id=0) OR (user_id=%u AND friend_id=users.id)) ORDER BY long_name LIMIT %u,11 ";
$xmlnetworkmyprofile_getProfile = "SELECT username,long_name,picture,description,stat,activity,keywords FROM users WHERE id=%u ";
$xmlnetworkmyprofile_getKeywords = "SELECT label FROM network_keywords,search_keyword WHERE kw_id=search_keyword.id AND user_id=%u AND friend_id=0 ";
$xmlnetworkkeywords_getMyKeywords = "SELECT DISTINCT kw_id,label FROM network_keywords,search_keyword WHERE user_id=%u AND friend_id!=0 AND search_keyword.id=kw_id ORDER BY label ASC ";
$xmlnbmessage_countmessages = "SELECT count(*) FROM users_messages WHERE user_id=%u AND status='U' ";
$xmlmessages_getMessages = "SELECT users_messages.id,title,description,users_messages.status,long_name,senddate FROM users_messages,users 
	WHERE user_id=%u AND users_messages.sender_id=users.id ORDER BY senddate DESC,users_messages.id DESC LIMIT %u,%u ";
$xmlfriends_getFriends = "SELECT email,long_name FROM users_friends,users WHERE user_id=%u AND users.id=friend_id ";
$xmlcheckfeedproxy_getRss = "SELECT id,icon FROM dir_rss WHERE url=%s ";
$xmlcheckfeedproxy_addFeed = "INSERT INTO dir_rss (url,icon,proxy) VALUES (%s,0,%s) ";
$xmlcheckfeedproxy_updateIcon = "UPDATE dir_rss SET icon=1 WHERE id=%u ";
$xmlarticlessearch_search = "SELECT DISTINCT users_articles.id,users_articles.user_id,long_name,title,link,pubdate 
	FROM users_articles,users_articles_keywords,search_keyword,users
	WHERE label_simplified IN (%s) AND users_articles.user_id!=%u AND private=0 AND users_articles_keywords.user_id=users.id AND users_articles.id=article_id AND search_keyword.id=kw_id LIMIT %u,11";
//SELECT DISTINCT users_articles.id,users_articles.user_id,long_name,title,link,pubdate FROM (users,users_articles) 
//	LEFT JOIN (users_articles_keywords,search_keyword) ON (users_articles.id=article_id AND search_keyword.id=kw_id AND users_articles_keywords.user_id=users.id) 
//	WHERE (label IN ('%s') OR title LIKE %s ) AND users_articles.user_id=users.id AND users.id!=%u AND private=0 LIMIT %u,11 ";
$xmlarticlessearch_searchInMyArchive = "SELECT DISTINCT users_articles.id,0 AS user_id,'' as long_name,title,link,pubdate FROM users_articles,users_articles_keywords,search_keyword 
	WHERE users_articles.id=article_id AND search_keyword.id=kw_id AND label_simplified IN (%s) AND users_articles_keywords.user_id=%u 
	LIMIT %u,11 ";
$xmlarticleslistforkey_getArticles = "SELECT id,title,link,icon,source,pubdate,description,private FROM users_articles WHERE user_id=%u AND classified!=0 ";
$xmlarticleslistforkey_getArticlesForKeyword = "SELECT users_articles.id,title,link,icon,source,pubdate,description,private FROM users_articles_keywords,users_articles WHERE users_articles_keywords.user_id=%u AND kw_id=%u AND article_id=users_articles.id ";
$xmlarticleskeywords_getKeywords = "SELECT DISTINCT kw_id,label FROM users_articles_keywords,search_keyword WHERE user_id=%u AND search_keyword.id=kw_id ORDER BY label ASC ";
$xmlarticlesdetail_getArticle = "SELECT title,link,icon,source,pubdate FROM users_articles WHERE id=%u ";
$xmlarticlesmydetail_getArticle = "SELECT title,link,private,description,icon,feedarticle_id,source,pubdate FROM users_articles WHERE id=%u AND user_id=%u ";
$xmlarticlesdetail_getKeywords = "SELECT label FROM users_articles_keywords,search_keyword WHERE kw_id=search_keyword.id AND user_id=%u AND article_id=%u ";
$xmlarticles_getArticles = "SELECT id,title,link,source,icon,pubdate,feedarticle_id FROM users_articles WHERE user_id=%u AND classified=0 LIMIT %u,%u ";
$xmlfriendportal_getPortal = "
	SELECT	name,
			status,
			user_id,
			width,
			style,
			type,
			param
	FROM	profile
	WHERE	id=%u
		AND shared = %s ";
$xmlfriendportal_getModules = "
	SELECT	item_id,
			name,
			posx,
			posy,
			posj,
			x,
			y,
			variables,
			height,
			website,
			minwidth,
			sizable,
			url,
			format,
			nbvariables,
			blocked,
			usereader,
			autorefresh
	FROM	module,
			dir_item
	WHERE	profile_id = %u
		AND item_id = id
		AND shared = %s
	ORDER BY posx,
			posy,
			posj ";
$widtaskshared_removeTask = "DELETE FROM users_tasks WHERE task_id=%u ";
$widtaskshared_addNewTaskId = "INSERT INTO users_tasks_id (status) VALUES('A') ";
$widtaskshared_addNewTask = "INSERT INTO users_tasks (id,comments,name) VALUES (%u,%s,%s) ";
$widtaskshared_insertTask = "INSERT INTO users_tasks (id,comments,name) VALUES (%u,%s,%s) ";
$widtaskshared_updStatus = "UPDATE users_tasks SET done=%s WHERE task_id=%u ";
$widnoteshared_newNote = "INSERT INTO users_notes (user_id, notes) VALUES (0,%s) ";
$widnoteshared_updNote = "UPDATE users_notes SET notes=%s WHERE id=%u ";
$widmailWOImap_checkProv = "SELECT provider,webmail,serveur,port,protocole FROM users_mail_providers WHERE provider_ext=%s ";
$widmailWOImap_configure = "INSERT INTO users_mail (user_id,provider, webmail, serveur, port, protocole, username, pass) VALUES (%u,%s,%s,%s,':%s',%s,%s,%s) ";
$widmailWOImap_getEmailInfo = "SELECT serveur,port,protocole,username,pass,webmail FROM users_mail WHERE id=%u AND user_id=%u ";
$widlinkshared_removeLink = "DELETE FROM users_favorites WHERE link_id=%u ";
$widlinkshared_newModId = "INSERT INTO users_favorites_id (status) VALUES('A') ";
$widlinkshared_firstLink = "INSERT INTO users_favorites (id,name,url) VALUES (%u,%s,%s) ";
$widlinkshared_addLink = "INSERT INTO users_favorites (id, name, url) VALUES (%u,%s,%s) ";
$widcalshared_removeEvent = "DELETE FROM users_calendar WHERE cal_id=%u ";
$widcalshared_addModId = "INSERT INTO users_calendar_id (status) VALUES('A') ";
$widcalshared_firstEvent = "INSERT INTO users_calendar (id,title,comments,pubdate,time,endtime) VALUES (%u,%s,%s,%s,%s,%s) ";
$widcalshared_addEvent = "INSERT INTO users_calendar (id,title,comments,pubdate,time,endtime) VALUES (%u,%s,%s,%s,%s,%s) ";
$widcalshared_getEvents = "SELECT cal_id, title, comments, time, endtime FROM users_calendar WHERE id=%u AND pubdate=%s ORDER BY time ";
$widcalshared_getMonthEvents = "SELECT cal_id, DAYOFMONTH(pubdate) as day, title, time, endtime FROM users_calendar WHERE id=%u AND MONTH(pubdate)=%s AND YEAR(pubdate)=%s ORDER BY pubdate,time ";
$scrredactorfeed_updFeed = "UPDATE redactor_feeds SET title=%s,description=%s WHERE id=%u ";
$scrredactorarticle_addArticle = "INSERT INTO redactor_articles (title,chapo,url,text,status,redactor_id,pubdate,creator_id,creationdate) VALUES (%s,%s,%s,%s,%s,%u,CURRENT_DATE,%u,CURRENT_DATE) ";
$scrredactorarticle_updArticle = "UPDATE redactor_articles SET url='%s?noplink=1' WHERE id=%u ";
$scrredactorarticle_createUserLink = "INSERT INTO redactor_map_article_feed (feed_id,article_id) VALUES (%u,%u) ";
$scrredactorarticle_updArticleInfo = "UPDATE redactor_articles SET title=%s,chapo=%s,url=%s,text=%s,status=%s,redactor_id=%u,pubdate=CURRENT_DATE WHERE id=%u ";
$scrcreaterss_addFeed = "INSERT INTO redactor_feeds (title,description,url,createdby,creationdate,md5url) VALUES (%s,%s,%s,%u,CURRENT_DATE,'feed_%s.xml') ";
$scrcreaterss_checkOwner = "INSERT INTO redactor_map_feeds (user_id, feed_id, admin) VALUES (%u,%u,1) ";
$scrcreaterss_addTempModule = "INSERT INTO temp_dir_item (url,defvar,name,description,typ,status,format,height,minwidth,sizable,website,editor_id,nbvariables,creation_date,lastmodif_date,lang,keywords,usereader,autorefresh) VALUES ('../modules/p_rss.php?','pfid=%u&fid=%u&nb=5&rssurl=%s',%s,%s,'R','N','R',100,280,1,'',%u,1,CURRENT_DATE,CURRENT_DATE,'%s','%s',%u,1) ";
$scrcreaterss_addTempModuleDir = "INSERT INTO temp_dir_cat_item (item_id, category_id,first) VALUES (%u,%u,'Y') ";
$redactorword_getFeedInfo = "SELECT id,title FROM redactor_feeds,redactor_map_feeds WHERE id=%u AND user_id=%u AND feed_id=id ";
$redactorword_getArticles = "SELECT ra.id,title,chapo,text FROM redactor_articles AS ra,redactor_map_article_feed WHERE ra.id=article_id AND feed_id=%u ORDER BY pubdate DESC, id DESC ";
$redactorfeedupdate_getFeedId = "SELECT feed_id FROM redactor_map_item_feed,dir_item WHERE id=item_id AND id=%u ";
$redactorfeed_getTitle = "SELECT id,title FROM redactor_feeds,redactor_map_feeds WHERE id=%u AND user_id=%u AND feed_id=id ";
$redactorfeed_getFeedTitle = "SELECT title,description FROM redactor_feeds WHERE id=%u ";
$redactorfeed_getFeedInfo = "SELECT id,title,description,md5url FROM redactor_feeds,redactor_map_feeds WHERE id=%u AND user_id=%u AND feed_id=id ";
$redactorfeed_getArticles = "SELECT ra.id,title,UNIX_TIMESTAMP(pubdate) AS pubdate,UNIX_TIMESTAMP(creationdate) AS creationdate,status,u1.username AS uu1,u1.long_name AS ul1,u2.username AS uu2,u2.long_name AS ul2 FROM redactor_articles AS ra,redactor_map_article_feed,users AS u1,users AS u2 
	WHERE ra.id=article_id AND feed_id=%u AND u1.id=redactor_id AND u2.id=creator_id ORDER BY pubdate DESC, id DESC LIMIT %u,%u ";
$scrredactorgeneratefeed_getArticles = "SELECT id,title,chapo,url,text,UNIX_TIMESTAMP(pubdate) AS pdate FROM redactor_articles,redactor_map_article_feed 
	WHERE id=article_id AND feed_id=%u AND status='O' ORDER BY pubdate DESC, id DESC LIMIT 0,20 ";
$redactorarticlemodif_getArticleInfo = "SELECT title,chapo,url,text,status FROM redactor_articles WHERE id=%u ";
$redactorarticlemodif_getReaderInfo = "SELECT usereader FROM redactor_map_item_feed,dir_item WHERE id=item_id AND feed_id=%u ";
$portals_getAllXml = "SELECT id, name, status FROM portals WHERE status in ('O','N') AND md5check='' ORDER BY name ASC, id DESC LIMIT %u,21 ";
$frmportalmodify_getPortal2 = "SELECT name,description,status FROM portals WHERE id=%u ";
$xmlnetworksearch_getUserByNamePart = "SELECT id,long_name,picture FROM users WHERE long_name LIKE %s AND typ='I' ORDER BY long_name LIMIT %u,11";
$scrnotebookarticleadd_addArticle = "
	INSERT INTO	notebook_article (
				title,
				description,
				keywords,
				pubdate,
				feedarticle_id,
				type,
				linked_id,
				status)
	VALUES		(%s,
				%s,
				%s,
				NOW(),
				%u,
				%u,
				%u,
				%s) ";
$scrcreaterss_addRssFeed = "INSERT INTO dir_rss (url,title,icon,lastloadedid,lastloadedtime) VALUES ('%s',%s,1,'','0000-00-00') ";
$xmlnetworksummary_getUpdates = "
	SELECT	a.user_id,
			a.pubdate,
			a.type,
			a.title,
			a.link,
			picture,
			long_name,
            'network' AS name
	FROM	network_news AS a,
			users,
			network
	WHERE	a.status = '3'
		AND network.friend_id = a.user_id
		AND network.user_id = %u
		AND users.id = a.user_id
	ORDER BY a.pubdate
	DESC LIMIT %u,%u";
$xmlnetworksummary_getUserUpdates = "
	SELECT	a.id,
			pubdate,
			type,
			title,
			link,
			long_name,
			picture
	FROM	network_news AS a,
			users AS b
	WHERE	user_id = %u
		AND b.id = user_id
		AND a.status = '3'
	ORDER BY id DESC
	LIMIT	0,20";
$xmlnetworknews_insertNews = "
	INSERT INTO	network_news (
				user_id,
				pubdate,
				type,
				title,
				link,
				status
				)
	VALUES		(%u,
				NOW(),
				%s,
				%s,
				%s,
				%s)
";
$xmlnetworknews_insertNewsWithoutTitle = "
	INSERT INTO	network_news (
				user_id,
				pubdate,
				type,
				title,
				link,
				status
				)
	SELECT		%u,
				NOW(),
				%s,
				title,
				%s,
				status
	FROM		notebook_article
	WHERE		id = %u
";
$scrnetworkupdatemyprofile_update = "
	UPDATE	users
	SET		stat = %s,
			statdate=CURRENT_DATE
	WHERE	id = %u";
$scrsupnews_removeNews = "
	DELETE FROM	network_news 
	WHERE		user_id=%u
		AND		id = %u";
$xmlnetworkusers_getFollowers = "
	SELECT	b.id,
			b.long_name,
			b.username,
			b.picture,
			b.stat,
			b.statdate,
			b.description,
			b.activity,
			b.lastconnect_date,
			NOW() AS dbdate
	FROM	network AS a,
			users AS b
	WHERE	a.user_id = b.id
		AND a.friend_id = %u
	ORDER BY b.long_name
	LIMIT	%u,21";
$xmlnetworkusers_getDirectory = "
	SELECT	id,
			long_name,
			username,
			picture,
			stat,
			statdate,
			description,
			activity,
			lastconnect_date,
			NOW() AS dbdate
	FROM	users
	WHERE	typ = 'I'
	ORDER BY long_name
	LIMIT	%u,21";
$xmlnetworkusers_getDirectoryByInitial = "
	SELECT	id,
			long_name,
			username,
			picture,
			stat,
			statdate,
			description,
			activity,
			lastconnect_date,
			NOW() AS dbdate
	FROM	users
	WHERE	typ = 'I'
		AND long_name LIKE %s
	ORDER BY long_name
	LIMIT	%u,21";
$scrindexsetkey_setaccount = "UPDATE adm_config SET value=%s WHERE parameter='ACCOUNT' ";
$scrindexsetkey_insertaccount = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('ACCOUNT',%s,'str','P') ";
$scrindexsetkey_setkey = "UPDATE adm_config SET value=%s WHERE parameter='PKEY' ";
$scrindexsetkey_insertkey = "INSERT INTO adm_config (parameter,value,datatype,desttype) VALUES ('PKEY',%s,'str','P') ";
$scrarticlemodifyadd_delOldKeywords = "DELETE FROM notebook_article_keywords WHERE article_id=%s";
$scrarticlemodifyadd_addNewKeywords = "INSERT INTO notebook_article_keywords (article_id,kw_id) VALUES (%u,%u) ";
$sidebar_tagList = "SELECT a.id,a.label,COUNT(a.id) AS nb FROM search_keyword AS a, notebook_article_keywords AS b,notebook_article_users AS c,notebook_article AS d WHERE user_id=%u AND c.article_id=b.article_id AND b.kw_id=a.id AND b.article_id=d.id AND d.status>=%u GROUP BY a.id ORDER BY label ";
$index_getNotebookSearchedArticlesTags = "
	SELECT	na.id,
			u.id AS userid,
			u.long_name,
			u.picture,
			na.title,
			na.description,
			na.keywords,
			na.pubdate,
			na.type,
			na.commentsnb,
			na.trackbacknb,
			na.linked_id,
			na.status,
			nau.owner_id
	FROM	notebook_article na,
			notebook_article_users nau,
			users u,
			notebook_article_keywords nak
	WHERE	na.id = nau.article_id 
		AND u.id = nau.owner_id 
		AND nau.user_id = %u 
		AND na.status != 'D' 
		AND na.status >= %u 
		AND nak.kw_id = %u 
		AND nak.article_id = na.id 
	ORDER BY nau.pubdate DESC,
			na.id DESC
	LIMIT	%u,11 ";
$xmlnotebookprofile_isInNetwork = "
	SELECT	a.user_id,
			b.long_name
	FROM	network AS a,
			users AS b
	WHERE	b.id = %u
		AND	a.user_id = b.id
		AND friend_id = %u
";
$xmlnotebookprofile_isInGroup="
	SELECT	a.user_id,
			b.name
	FROM	notebook_groups_users_map AS a,
			notebook_groups AS b
	WHERE	b.id = %u
		AND a.group_id = b.id
		AND a.user_id = %u
		AND a.status = 'O'
";
$notebook_changeArticleStatus="
	UPDATE	notebook_article,
			notebook_article_users
	SET		status = %s
	WHERE	id = %u
		AND id = article_id
		AND owner_id = %u ";
$notebook_removeComment="UPDATE notebook_comments SET status='D' WHERE id=%u AND user_id=%u ";
$notebook_decreaseCommentNb="UPDATE notebook_article,notebook_comments SET commentsnb=commentsnb-1 WHERE notebook_comments.id=%u AND notebook_comments.article_id=notebook_article.id ";
$xmlnotebooksearch_otherNotebookSearch = "
	SELECT DISTINCT	e.id,
					b.user_id,
					long_name,
					e.title,
					e.pubdate,
					d.picture,
					e.description 
	FROM			notebook_article_keywords AS a,
					notebook_article_users AS b,
					search_keyword AS c,
					users AS d,
					notebook_article AS e
	WHERE			label_simplified IN (%s)
		AND			b.user_id != %u
		AND			e.status = '3'
		AND			a.article_id = e.id
		AND			a.kw_id = c.id
		AND			a.article_id = b.article_id
		AND			b.user_id = d.id
        ORDER BY    e.pubdate
		LIMIT		%u,11
";
$xmlnotebooksearch_mynotebooksearch = "
	SELECT DISTINCT	e.id,
					b.user_id,
					'' AS long_name,
					e.title,
					d.picture,
					e.pubdate 
	FROM			notebook_article_keywords AS a,
					notebook_article_users AS b,
					search_keyword AS c,
					notebook_article AS e,
					users AS d
	WHERE			label_simplified IN (%s)
		AND 		b.user_id = %u
		AND			b.user_id = b.owner_id
		AND			e.status != 'D'
		AND			a.article_id = e.id
		AND			a.kw_id = c.id
		AND			a.article_id = b.article_id
		AND			d.id = b.user_id
        ORDER BY    e.pubdate
		LIMIT		%u,11
";
$xmlnotebooksearch_allNotebookSearch = "
	SELECT DISTINCT	e.id,
					b.user_id,
					long_name,
					e.title,
					e.pubdate,
					d.picture,
					e.description 
	FROM			notebook_article_keywords AS a,
					notebook_article_users AS b,
					search_keyword AS c,
					users AS d,
					notebook_article AS e
	WHERE			label_simplified IN (%s)
		AND			(
                        (b.user_id != %u
                            AND			e.status = '3'
                        )
                    OR
                        (b.user_id = %u
                            AND			b.user_id = b.owner_id
                            AND			e.status != 'D'
                        )
                    )
		AND			a.article_id = e.id
		AND			a.kw_id = c.id
		AND			a.article_id = b.article_id
		AND			b.user_id = d.id
        ORDER BY    e.pubdate
		LIMIT		%u,11
";
$scrnotebookarticleadd_addNewKeywords = "INSERT INTO notebook_article_keywords (article_id,kw_id) VALUES (%u,%u) ";
$commentsrss_getArticleInformation = "
	SELECT	title
	FROM	notebook_article
	WHERE	id = %u
		AND status >= %u
	LIMIT	0,20";
$commentsrss_getRssArticleComments = "
	SELECT	b.long_name,
			a.message,
			a.pubdate
	FROM	notebook_comments AS a,
			users AS b,
			notebook_article AS c
	WHERE	a.article_id = %u
		AND c.id = a.article_id
		AND a.user_id = b.id
		AND a.status = 'O'
		AND c.status >= %u
	ORDER BY a.id DESC
	LIMIT	0,20";
$commentsrss_getRssComments = "
	SELECT	a.article_id,
			long_name,
			message,
			a.pubdate,
			b.picture
	FROM	notebook_comments AS a,
			users AS b,
			notebook_article AS c,
			notebook_article_users AS d
	WHERE	a.article_id = c.id
		AND a.user_id = b.id
		AND d.article_id = c.id
		AND d.user_id = %u
		AND c.status != 'D'
		AND c.status >= %u
		AND a.status = 'O'
	ORDER BY a.id DESC
	LIMIT	0,20";
$commentsrss_getRssArticleCommentsGroups = "
	SELECT	b.long_name,
			a.message,
			a.pubdate
	FROM	notebook_comments AS a,
			users AS b,
			notebook_article AS c
	WHERE	a.article_id = %u
		AND c.id = a.article_id
		AND a.user_id = b.id
		AND a.status = 'O'
		AND c.status >= %u
		AND c.private <= %u
	ORDER BY a.id DESC
	LIMIT	0,20";
$xmlarticles_getNbArticles = "SELECT count(id) AS nb FROM users_articles WHERE user_id=%u AND classified=0 ";
$xmlmycomments_getComments = "SELECT a.article_id,d.user_id,long_name,message FROM notebook_comments AS a, users AS b,notebook_article_users AS d WHERE a.user_id=b.id AND d.article_id=a.article_id AND d.user_id=%u AND a.status='O' ORDER BY a.id DESC LIMIT %u,20";
//$scrsendtofriend_sendPortal = "INSERT INTO portals (name,description,author,nbcol,style,position,status,type,param) SELECT name,'',%u,width,style,%u,'O',type,param FROM profile WHERE id=%u AND user_id=%u ";
$scrsendtofriend_setPortalSharingInfo = "
	UPDATE	profile
	SET		shared = %s,
			md5pass = ''
	WHERE	user_id = %u
		AND	id=%u
	";
$index_getGroupbookArticles = "
	SELECT	a.id,
			a.commentsnb,
			a.trackbacknb,
			a.title,
			a.description,
			a.keywords,
			a.pubdate,
			a.linked_id,
			a.type,
			a.status,
			m.owner_id,
			u.id AS userid,
			u.long_name,
			u.picture,
			m.group_id,
			m.is_copy
	FROM 	notebook_article AS a,
			notebook_groups AS b,
			notebook_groups_articles_map AS m,
			users AS u
	WHERE 	a.id = m.article_id
		AND b.id = %u
		AND m.group_id = b.id
		AND a.status IN (%s)
		AND m.owner_id = u.id
	ORDER BY a.id DESC
	LIMIT	%u,11";
$index_getArticlesByGroup = "SELECT f.id, f.title, f.description, f.pubdate, 1 as type, 'nomlong' as long_name, am.status as status, 10 as linked_id, 2 as owner_id, 2 as userid, 0 as trackbacknb, 0 as commentsnb FROM feed_articles as f, notebook_groups_articles_map as am WHERE am.group_id = %d and f.id=am.article_id ORDER BY f.id DESC LIMIT %u,11";
$groupbook_removeComment="UPDATE notebook_comments SET status='D' WHERE id=%u AND user_id=%u ";
$groupbook_decreaseCommentNb="UPDATE notebook_article,notebook_comments SET commentsnb=commentsnb-1 WHERE notebook_comments.id=%u AND notebook_comments.article_id=notebook_article.id ";
$scrgroupbook_add = "
    INSERT INTO notebook_groups (
                    name,
                    description,
                    created_by,
                    creation_date,
                    private
                )
    VALUES      (
                    %s,
                    %s,
                    %u,
                    CURRENT_DATE,
                    %u
                )
";
$scrgroupbook_map_add="INSERT INTO notebook_groups_users_map (user_id, group_id, status) values(%u, %u, %s)";
$xmlgroupbook_getByName = "
    SELECT  count(*) as exist
    FROM    notebook_groups
    WHERE   UCASE(name) = %s
";
$scrgroupbook_map_remove = "
    DELETE FROM notebook_groups_users_map
    WHERE       user_id = %u
        AND     group_id = %u
";
$scrgroupbook_map_update = "
    UPDATE  notebook_groups_users_map
    SET     status = %s
    WHERE   user_id = %u
        AND group_id = %u
";
$xmlarticles_getNoteGroup = "SELECT id AS groupId,name,created_by,creation_date,private,status FROM notebook_groups WHERE id=%u ";
$index_getGroupbookSearchedArticles = "
	SELECT	a.id,
			u.id AS userid,
			u.long_name,
			u.picture,
			title,
			a.description,
			a.keywords,
			a.pubdate,
			type,
			commentsnb,
			trackbacknb,
			linked_id,
			a.status,
			m.owner_id,
			m.group_id,
			m.is_copy
	FROM	notebook_article as a,
			users AS u,
			notebook_groups_articles_map as m
	WHERE 	a.id = article_id
		AND m.group_id = %u
		AND a.status IN (%s)
		AND (title LIKE %s OR a.description LIKE %s)
		AND m.owner_id = u.id 
	ORDER BY a.id DESC
	LIMIT	%u,11";
$index_getGroupbookSearchedArticlesTags ="
	SELECT 	a.id,
			a.commentsnb,
			a.trackbacknb,
			a.title,
			a.description,
			a.keywords,
			a.pubdate,
			a.linked_id,
			a.type,
			m.status,
			m.owner_id,
			u.id AS userid,
			u.long_name,
			u.picture,
			m.group_id,
			m.is_copy
	FROM	notebook_article AS a,
			notebook_groups_articles_map AS m,
			users AS u,
			search_keyword AS k,
			notebook_article_keywords AS n
	WHERE	a.id = m.article_id
		AND m.group_id = %u
		AND a.status in (%s)
		AND m.owner_id = u.id
		AND k.id = %s
		AND n.kw_id = k.id
		AND a.id = n.article_id
	ORDER BY a.id DESC
	LIMIT	%u,11";
$xmlgroup_getGroupsUser = "SELECT id,name FROM notebook_groups,notebook_groups_users_map WHERE  id = group_id AND user_id = %u AND notebook_groups_users_map.status='O'";
$groupbook_changeArticleStatus = "UPDATE notebook_groups_articles_map SET status=%s WHERE article_id=%u AND group_id=%u ";
$scrarticlemodifyadd_attributeGroupToArticle = "
	INSERT INTO	notebook_groups_articles_map (
				group_id,
				article_id,
				status,
				owner_id,
				user_id,
				is_copy)
	SELECT 		%u,
				id,
				status,
				%u,
				%u,
				%u
	FROM 		notebook_article
	WHERE		id = %u
		AND		status in (2,3)";
$xmlgroup_isUserMemberGroup = "SELECT COUNT(*) AS nb FROM notebook_groups_users_map as um, notebook_groups_articles_map as am WHERE user_id=%u AND um.group_id=%u AND um.status='O' AND am.group_id = um.group_id AND am.article_id=%u";
$xmlgroup_isArticleInGroup = "SELECT COUNT(*) AS articleInGroup FROM notebook_groups_articles_map WHERE group_id=%u AND article_id=%u";
$xmlgroup_getOwnerArticleInGroup = "SELECT distinct owner_id FROM notebook_groups_articles_map WHERE article_id=%u";
$xmlgroup_isArticleInNotebook = "SELECT COUNT(*) AS articleIn FROM notebook_article_users WHERE user_id=%u AND article_id=%u";
$scrgroupbook_map_update="UPDATE notebook_groups_users_map set status=%s WHERE user_id=%u AND group_id=%u ";
$xmlGroupbook_getMembre="SELECT u.id as userid , u.long_name, u.description, u.username, u.stat, u.picture FROM users AS u JOIN notebook_groups_users_map AS um ON um.user_id = u.id WHERE um.group_id=%u AND um.status='O' ORDER BY long_name ASC";
$sidebar_tagListGroupbook="SELECT a.id, a.label, COUNT(a.id) AS nb FROM search_keyword AS a, notebook_article_keywords AS b, notebook_groups_articles_map AS c, notebook_article AS d WHERE c.group_id=%u AND c.article_id=b.article_id AND b.kw_id=a.id AND b.article_id=d.id AND c.status in (%s) GROUP BY a.id ORDER BY label";
$rss_getRssArticlesGroup="SELECT id, title, notebook_article.description, notebook_article.pubdate FROM notebook_article, notebook_groups_articles_map WHERE notebook_groups_articles_map.status='O' AND notebook_groups_articles_map.group_id=%u AND notebook_groups_articles_map.article_id=notebook_article.id ORDER BY notebook_article.pubdate DESC, notebook_article.id DESC LIMIT 0,20";
$commentsrss_getRssCommentsGroup = "
	SELECT	a.article_id,
			b.long_name,
			a.message,
			a.pubdate,
			b.picture
	FROM	notebook_comments AS a,
			users AS b,
			notebook_article AS c,
			notebook_groups_articles_map AS e
	WHERE	a.article_id = c.id
		AND a.user_id = b.id
		AND c.status in (%s)
		AND a.status = 'O'
		AND e.group_id = %u
		AND e.article_id = a.article_id
	ORDER BY a.id DESC
	LIMIT	0,20";
$scrgroupbook_deleteGroupArticleMap = "DELETE FROM notebook_groups_articles_map WHERE article_id=%u AND group_id=%u";
$scrnotebook_deleteUserArticle = "DELETE FROM notebook_article_users WHERE user_id=%u AND article_id=%u";
$scrgroupbook_getGroupPrivate = "SELECT created_by, private FROM notebook_groups AS g WHERE g.id=%u";
$scrarticlemodifyadd_updateArticleGroup = "
	UPDATE	notebook_article,
			notebook_groups_articles_map
	SET		title = %s,
			description = %s,
			keywords = %s
	WHERE	id = %u
		AND id = article_id
		AND owner_id = %u ";
$xmlnetworkinfo_getNetworkNb = "SELECT COUNT(*) AS networknb FROM network WHERE user_id=%u";
$xmlnetworkinfo_getRefererNb = "SELECT COUNT(*) AS referernb FROM network WHERE friend_id=%u";
$scrchat_setactivity = "UPDATE users SET lastconnect_date=NOW(),activity=%s WHERE id=%u";
$xmlnetworkconnectedusers = "SELECT a.id,a.long_name,a.activity,a.lastconnect_date,NOW() AS dbdate FROM users AS a,network AS b WHERE b.user_id=%u AND b.friend_id=a.id AND a.activity IN ('o','a') ";
$scrchat_newchat = "INSERT INTO network_chat (owner_id,callee_id,status,title,pubdate) VALUES (%u,%u,'n',%s,CURRENT_DATE)";
$scrchat_newnotification = "
    INSERT INTO users_notification (user_id,notification_id,type)
    VALUES      (%u,%u,%s)
";
$scrchat_newmessage = "INSERT INTO network_chat_message (chat_id,send_id,dest_id,message,status) VALUES (%u,%u,%u,%s,'s')";
$scrchat_getNotification = "
    SELECT  notification_id,
            type
    FROM    users_notification
    WHERE   user_id=%u
";
$scrchat_deleteNotifications = "DELETE FROM users_notification WHERE user_id=%u";
$scrchat_getNewChats = "SELECT a.id,a.owner_id,b.long_name FROM network_chat AS a,users AS b WHERE b.id=a.owner_id AND a.callee_id=%u AND a.status='n'";
$scrchat_setChatsAreOpened = "UPDATE network_chat SET status='o' WHERE callee_id=%u AND status='n'";
$scrchat_getMessages = "SELECT chat_id,message FROM network_chat_message WHERE dest_id=%u AND chat_id IN (%s) AND status='s' ORDER BY id ASC";
$scrchat_setMessagesAsRead = "UPDATE network_chat_message SET status='r' WHERE dest_id=%u AND chat_id IN (%s) AND status='s'";
$xmlnetworkusers_getExcludedMembers = "SELECT friend_id from network WHERE user_id=%u";
$xmlnetworkusers_getSameFriends = "
	SELECT	c.id,
			c.long_name,
			c.username,
			c.description,
			c.stat,
			c.statdate,
			c.activity,
			c.lastconnect_date,
			Now() AS dbdate,
			c.picture,
			count(b.user_id) AS nbrel
	FROM	network AS a,
			network AS b,
			users AS c
	WHERE	a.user_id = %u
		AND a.friend_id = b.user_id
		AND a.user_id != a.friend_id
		AND a.user_id != b.user_id
		AND a.user_id != b.friend_id
		AND b.user_id != b.friend_id
		AND b.friend_id = c.id
	GROUP BY c.id
	HAVING COUNT(b.user_id) > 1
	ORDER BY nbrel DESC
	LIMIT	%u,21";
$xmlchat_getArchive = "SELECT a.id,a.title,a.pubdate,b.id AS id1,b.long_name AS name1,c.long_name AS name2 FROM network_chat AS a,users AS b,users AS c WHERE (a.owner_id=%u OR a.callee_id=%u) AND a.owner_id=b.id AND a.callee_id=c.id ORDER BY id DESC LIMIT %u,20";
$xmlchat_getArchiveDetail = "SELECT b.long_name,a.message FROM network_chat_message AS a, users AS b WHERE a.chat_id=%u AND a.send_id=b.id AND (a.send_id=%u OR a.dest_id=%u) ORDER BY a.id ASC";
$xmlmostcomments_getArticle = "SELECT a.id,a.title,a.pubdate,a.commentsnb FROM notebook_article AS a,notebook_article_users AS b WHERE a.status!='D' AND a.id=b.article_id AND b.user_id=%u ORDER BY commentsnb DESC LIMIT %u,20";
$xmlnetworkusers_getSameTag = "
	SELECT	c.id,
			c.long_name,
			c.username,
			c.description,
			c.stat,
			c.activity,
			c.lastconnect_date,
			Now() AS dbdate,
			c.picture,
			count(b.user_id) AS nbcommontags
	FROM	network_keywords AS a,
			network_keywords AS b,
			users AS c
	WHERE	a.user_id = %u
		AND a.kw_id = b.kw_id
		AND a.user_id != b.user_id
		AND a.friend_id = 0
		AND b.friend_id = 0
		AND b.user_id = c.id
	GROUP BY c.id
	HAVING	COUNT(b.user_id)>1
	ORDER BY nbcommontags DESC
	LIMIT	%u,21";
$criteria_getPublicCriterias = "SELECT label,type,options,parameters FROM adm_userinfo,users_info WHERE adm_userinfo.id=users_info.info_id AND ispublic=1 AND user_id=%u ";
$frmnetworkprofile_getUser = "SELECT picture,description,username,long_name,keywords FROM users WHERE id=%u ";
$scrgroup_getUserLangUsername = "SELECT lang,username FROM users WHERE id=%u";
$scrgroup_getGetGroupName = "
	SELECT	name
	FROM	notebook_groups
	WHERE	id = %u
		AND	private <= %u";
$xmlgroup_getNbGroups = "SELECT user_id,group_id,status FROM notebook_groups_users_map WHERE user_id=%u AND status='O'";
$xmlnetwork_groupsearch = "
	SELECT	a.id,
			a.name,
			b.long_name,
			a.creation_date
	FROM	notebook_groups AS a,
			users AS b
	WHERE	a.created_by = b.id
		AND	a.private = 0
		AND	a.name LIKE %s
";
$scrgroupadd_join="
	INSERT INTO	notebook_groups_users_map (
				user_id,
				group_id,
				status
				)
	SELECT		%u,
				id,
				%s
	FROM		notebook_groups
	WHERE		id = %u
		AND		private = 0;
";
$scrarticlemodifyadd_addDocument = "
	INSERT INTO	documents (
                title,
                link,
                version,
                creation_date,
                modif_date,
                size)
	VALUES		(%s,
				%s,
				'1.0.0',
				CURRENT_DATE,
				CURRENT_DATE,
				%u)
";
$scrarticlemodifyadd_mapDocument = "
	INSERT INTO	notebook_article_document_map (article_id,document_id)
	SELECT		article_id,
				%u
	FROM		notebook_article_users
	WHERE		article_id = %u
		AND		user_id = %u
		AND		owner_id = %u
";
$scrarticlemodifyadd_removeDocuments = "
	DELETE FROM	notebook_article_document_map
	WHERE		article_id = %u
";
$notebook_removeLinkNotebookArticle = "
    DELETE FROM notebook_article_users
    WHERE       article_id = %u
        AND     user_id = %u
";
$notebook_getNotebookProperties = "
    SELECT  a.long_name,
            a.picture,
            b.user_id
    FROM    users AS a
        LEFT JOIN   network AS b
            ON      a.id = b.friend_id
            AND     b.user_id = %u
    WHERE   a.id = %u
";
$notebook_getGroupbookProperties = "
    SELECT  a.name,
            a.picture,
            a.private,
            b.user_id,
            a.created_by,
            a.description
    FROM    notebook_groups AS a
        LEFT JOIN   notebook_groups_users_map AS b
            ON      a.id = b.group_id
            AND     b.user_id = %u
            AND     b.status = 'O'
    WHERE   a.id = %u
";
$xmlnetwork_getUserWorkingGroups = "
    SELECT  a.id,
            a.name,
            a.description,
            a.picture,
            a.created_by,
            a.status
    FROM    notebook_groups a,
            notebook_groups_users_map AS d
    WHERE   private = 0
        AND d.group_id = a.id
        AND d.user_id = %u
        AND d.status = 'O'
        AND a.private = 0
    ORDER BY a.name ASC
";
$scr_group_update = "
    UPDATE  notebook_groups
    SET     name = %s,
            picture = %s,
            private = %u,
            description = %s
    WHERE   created_by = %u
        AND id = %u
";
//get the document
$detail_getDocuments = "
	SELECT	a.id,
			a.title,
			a.link,
			a.size
	FROM	documents AS a,
			notebook_article_document_map AS b
	WHERE	b.article_id=%u
		AND	a.id=b.document_id
";
$xmlnetworksummary_getGroupsUpdates = "
SELECT		a.title,
            '9' AS type,
            d.id AS user_id,
            d.long_name,
            d.picture,
            e.name,
            a.pubdate,
            CONCAT('id=',e.id,'&artid=',a.id) AS link
FROM		notebook_article AS a,
            notebook_groups_users_map AS b,
            notebook_groups_articles_map AS c,
            users AS d,
            notebook_groups AS e
WHERE		b.user_id = %u
        AND	b.status = 'O'
        AND	b.group_id = c.group_id
        AND	c.article_id = a.id
        AND	c.status NOT IN ('1','D')
        AND	d.id = c.owner_id
        AND	e.id = b.group_id
ORDER BY    a.pubdate DESC,
            c.owner_id
LIMIT       %u,%u
";
$xmlcheckfeedproxy_setIcon = "
    UPDATE  dir_rss
    SET     icon = 1,
            iconid = %s
    WHERE id = %u
";
$xmlcheckfeedproxy_getRssAndIcon = "
    SELECT  id,
            icon,
            iconid
    FROM    dir_rss
    WHERE   url = %s
";
$scrAlertAdd = "
    INSERT INTO network_alerts (
            user_id,
            type,
            referer_id,
            referer_name)
    VALUES  (%u,
            %u,
            %u,
            %s)
";
$xmlnetwork_getAlerts = "
    SELECT  type,
            referer_id,
            referer_name
    FROM    network_alerts
    WHERE   user_id = %u
    ORDER BY id DESC
    LIMIT   %u,11
";
$xml_display_library = "
	SELECT 	R.article_rating AS article_rating,
			R.article_id AS article_id,
			R.rating_timestamp AS rating_timestamp,
			S.title AS feed_title, 
			S.iconid AS icon,
			F.title AS article_title, 
			F.link AS link,
			M.uniq AS uniq,
			P.seq AS seq
	FROM	feed_articles_read R,
			feed_articles F,
			dir_rss S, 
			module M,
			profile P
	WHERE 	R.user_id = %u 
	AND	 	R.article_id = F.id 
	AND		R.article_rating IS NOT NULL
	AND		F.feed_id = S.id
	AND		M.user_id = R.user_id
	AND 	M.feed_id = F.feed_id
	AND		M.profile_id = P.id
	LIMIT	%u,%u
";
$scrsendtofriend_getSharedValue = "
    SELECT shared 
    FROM module 
    WHERE user_id = %u
		AND profile_id = %u
		AND uniq = %u
";
?>