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

$widnote_newNote = "INSERT INTO users_notes (user_id, notes) VALUES (%u,%s) ";
$widnote_updateNote = 'UPDATE users_notes SET notes=%s WHERE id=%u AND user_id=%u ';
$widnote_addNote = "INSERT INTO users_notes (user_id, notes) VALUES (%u,%s) ";
$widcal_removeEvent = "
	DELETE	uc
	FROM	users_calendar AS uc,
			users_calendar_id AS uci
	WHERE	cal_id=%u
			AND uc.id=uci.id
			AND user_id=%u ";
$widcal_createNewId = "INSERT INTO users_calendar_id (user_id,status) VALUES(%u,'A') ";
$widcal_createNewEvent = "
	INSERT INTO	users_calendar (id,title,comments,pubdate,time,endtime)
	SELECT		%u,
				title,
				comments,
				pubdate,
				time,
				endtime
	FROM		users_calendar
	WHERE		id=%u
				AND cal_id!=%u ";
$widcal_newModuleId = "INSERT INTO users_calendar_id (user_id,status) VALUES(%u,'A') ";
$widcal_newModuleEvent = "INSERT INTO users_calendar (id,title,comments,pubdate,time,endtime) VALUES (%u,%s,%s,%s,%s,%s) ";
$widcal_addEvent = "
	INSERT INTO	users_calendar (id,title,comments,pubdate,time,endtime)
	SELECT		%u,
				%s,
				%s,
				%s,
				%s,
				%s
	FROM		users_calendar_id
	WHERE		id=%u
				AND user_id=%u ";
$widcal_addNewModId = "INSERT INTO users_calendar_id (user_id,status) VALUES(%u,'A') ";
$widcal_addNewModEvent = "
	INSERT INTO	users_calendar (id,title,comments,pubdate,time,endtime)
	SELECT		%u,
				title,
				comments,
				pubdate,
				time,
				endtime
	FROM		users_calendar
	WHERE		id=%u ";
$widcal_addNewModOldEvents = "INSERT INTO users_calendar (id,title,comments,pubdate,time,endtime) VALUES (%u,%s,%s,%s,%s,%s) ";
$widcal_getEvents = "SELECT cal_id, title, comments, time, endtime FROM users_calendar WHERE id=%u AND pubdate=%s ORDER BY time ";			
$widcal_getMonthEvents = "SELECT cal_id, DAYOFMONTH(pubdate) as day, title, time, endtime FROM users_calendar WHERE id=%u AND MONTH(pubdate)=%s AND YEAR(pubdate)=%s ORDER BY pubdate,time ";
$widtask_RemoveTask = "
	DELETE 	users_tasks
	FROM	users_tasks,
			users_tasks_id
	WHERE 	task_id=%u
			AND users_tasks.id=users_tasks_id.id
			AND user_id=%u ";
$widtask_createNewId = "
	INSERT INTO 	users_tasks_id (user_id,status)
	VALUES			(%u,'A') ";
$widtask_copyTasks = "INSERT INTO users_tasks (id,comments,name,done) SELECT %u,comments,name,done FROM users_tasks WHERE id=%u AND task_id!=%u ";
$widtask_addNewTaskId = "INSERT INTO users_tasks_id (user_id,status) VALUES(%u,'A') ";
$widtask_addNewTask = "
	INSERT INTO 	users_tasks (id,comments,name)
	VALUES 			(%u,%s,%s) ";
$widtask_addNewTaskOnExistingMod = "
	INSERT INTO 	users_tasks (id,comments,name)
	SELECT 			%u,
					%s,
					%s
	FROM 			users_tasks_id
	WHERE 			id=%u
					AND user_id=%u ";
$widtask_updateTask = "
	UPDATE 	users_tasks,
			users_tasks_id
	SET 	name=%s,
			comments=%s
	WHERE 	task_id=%u
			AND users_tasks.id=users_tasks_id.id
			AND user_id=%u ";
$widtask_createNewMod = "INSERT INTO users_tasks_id (user_id,status) VALUES(%u,'A') ";
$widtask_insertExistingTasks = "INSERT INTO users_tasks (id,comments,name,done) SELECT %u,comments,name,done FROM users_tasks WHERE id=%u ";
$widtask_newModInsertTask = "INSERT INTO users_tasks (id,comments,name) VALUES (%u,%s,%s) ";
$widtask_newModUpdateTask = "UPDATE users_tasks SET name=%s,comments=%s WHERE task_id=%u ";
$widtask_changeStatus = "UPDATE users_tasks SET done=%s WHERE task_id=%u ";
$widmail_checkProvider = "SELECT provider,webmail,serveur,port,protocole FROM users_mail_providers WHERE provider_ext=%s ";
$widmail_configure = "
    INSERT INTO users_mail (
        user_id,
        provider,
        webmail,
        serveur,
        port,
        protocole,
        username,
        pass) 
    VALUES (%u,
        %s,
        %s,
        %s,
        %s,
        %s,
        %s,
        AES_ENCRYPT(%s,'%s'))
";
$widmail_getMailInfo = "
    SELECT  serveur,
            port,
            protocole,
            username,
            AES_DECRYPT(pass,'%s') AS dpass,
            webmail
    FROM    users_mail
    WHERE   id = %u
        AND user_id = %u
";
$widmail_isExistAccount = "
    SELECT  id,
            username
    FROM    users_mail
    WHERE   username = %u
        AND user_id = %u
        AND serveur = %s
        AND port = %s
        AND pass = AES_ENCRYPT(%s,'%s')
";
$widlink_removeLink = "
	DELETE 	uf
	FROM 	users_favorites AS uf,
			users_favorites_id AS ufi
	WHERE 	link_id=%u
			AND uf.id=ufi.id
			AND user_id=%u ";
$widlink_createNewId = "INSERT INTO users_favorites_id (user_id,status) VALUES(%u,'A') ";
$widlink_copyLinks = 'INSERT INTO users_favorites (id,name,url,tags) SELECT %u,name,url,tags FROM users_favorites WHERE id=%u AND link_id!=%u ';
$widlink_addNewLinkId = "INSERT INTO users_favorites_id (user_id,status) VALUES(%u,'A') ";
$widlink_addNewLink = "INSERT INTO users_favorites (id,name,url,tags) VALUES (%u,%s,%s,%s) ";
$widlink_addNewLinkOnExistingMod = "INSERT INTO users_favorites (id,name,url,tags) SELECT %u,%s,%s,%s FROM users_favorites_id WHERE id=%u AND user_id=%u ";
$widlink_updateLink = "
	UPDATE 	users_favorites,
			users_favorites_id
	SET 	name=%s,
			url=%s,
			tags=%s
	WHERE 	link_id=%u
			AND users_favorites.id=users_favorites_id.id
			AND user_id=%u ";
$widlink_createNewMod = "INSERT INTO users_favorites_id (user_id,status) VALUES(%u,'A') ";
$widlink_insertExistingLink = "INSERT INTO users_favorites (id,name,url,tags) SELECT %u,name,url,tags FROM users_favorites WHERE id=%u ";
$widlink_newModInsertLink = "INSERT INTO users_favorites (id,name,url,tags) VALUES (%u,%s,%s,%s) ";
$widlink_newModUpdateLink = "UPDATE users_favorites SET name=%s,url=%s,tags=%s WHERE link_id=%u ";
$widhtml_newContent = "INSERT INTO widget_html (user_id, content) VALUES (0,%s) ";
$widhtml_updateContent = 'UPDATE widget_html SET content=%s WHERE id=%u ';
$widAddr_RemoveContact="
	DELETE 		a
	FROM		widget_addressbook AS a,
				widget_addressbook_id AS b
	WHERE 		add_id=%u
				AND a.id=b.id
				AND user_id=%u ";
$widAddr_createNewId="
	INSERT INTO	widget_addressbook_id (user_id,status)
	VALUES		(%u,'A') ";
$widAddr_copyContact="
	INSERT INTO	widget_addressbook (id,firstname,lastname,email,company,func,phone1,phone2,other,tags)
	SELECT		%u,
				firstname,
				lastname,
				email,
				company,
				func,
				phone1,
				phone2,
				other,
				tags
	FROM		widget_addressbook
	WHERE		id=%u
				AND add_id!=%u ";
$widAddr_addNewContactId="
	INSERT INTO	widget_addressbook_id (user_id,status)
	VALUES		(%u,'A') ";
$widAddr_addNewContact="
	INSERT INTO	widget_addressbook (id,firstname,lastname,email,company,func,phone1,phone2,other,tags)
	VALUES		(%u,%s,%s,%s,%s,%s,%s,%s,%s,%s) ";
$widAddr_addNewContactOnExistingMod="
	INSERT INTO	widget_addressbook (id,firstname,lastname,email,company,func,phone1,phone2,other,tags)
	SELECT		%u,%s,%s,%s,%s,%s,%s,%s,%s,%s
	FROM		widget_addressbook_id
	WHERE		id=%u
				AND user_id=%u ";
$widAddr_updateContact="
	UPDATE	widget_addressbook,
			widget_addressbook_id
	SET		firstname=%s,
			lastname=%s,
			email=%s,
			company=%s,
			func=%s,
			phone1=%s,
			phone2=%s,
			other=%s,
			tags=%s
	WHERE	add_id=%u
			AND widget_addressbook.id=widget_addressbook_id.id
			AND user_id=%u ";
$widAddr_createNewMod="
	INSERT INTO	widget_addressbook_id (user_id,status)
	VALUES		(%u,'A') ";
$widAddr_insertExistingContact="
	INSERT INTO	widget_addressbook (id,firstname,lastname,email,company,func,phone1,phone2,other,tags)
	SELECT		%u,
				firstname,
				lastname,
				email,
				company,
				func,
				phone1,
				phone2,
				other,
				tags
	FROM		widget_addressbook
	WHERE		id=%u ";
$widAddr_newModInsertContact="
	INSERT INTO	widget_addressbook (id,firstname,lastname,email,company,func,phone1,phone2,other,tags)
	VALUES		(%u,%s,%s,%s,%s,%s,%s,%s,%s,%s) ";
$widAddr_newModUpdateContact="
	UPDATE	widget_addressbook
	SET		firstname=%s,
			lastname=%s,
			email=%s,
			company=%s,
			func=%s,
			phone1=%s,
			phone2=%s,
			other=%s,
			tags=%s
	WHERE	add_id=%u ";
$tutorial_getSource = "SELECT source,xmlmodule,url 
                            FROM dir_item_external
                            WHERE item_id=%u";
$tutorial_getXMLCode_temp = "SELECT xmlmodule,url 
                            FROM temp_dir_item_external
                            WHERE item_id=%u";                        
$tutorial_getXMLCode = "SELECT xmlmodule,url 
                            FROM dir_item_external
                            WHERE item_id=%u";
$tutorial_getSourceAndView = "SELECT source,xmlmodule,url,type 
                            FROM dir_item_external
                            WHERE item_id=%u  AND view=%s"; 
$tutorial_getTmpSourceAndView = "SELECT source,xmlmodule,url,type
                                FROM temp_dir_item_external
                                WHERE item_id=%u  AND view=%s";  
$tutorial_getSourceAndViewl10n = "SELECT source,url,viewtype 
                                  FROM dir_item_external
                                  WHERE item_id=%u AND view=%s AND lang=%s"; 
$tutorial_getTmpSourceAndViewl10n = "SELECT source,url,viewtype
                                     FROM temp_dir_item_external
                                     WHERE item_id=%u AND view=%s AND lang=%s"; 
$tutorial_getL10NSource = "
	SELECT	source,
			url,
            viewtype
	FROM	dir_item_external_language
	WHERE	item_id=%u
            AND lang=%s
            AND view=%s
";    
$tutorial_getTempL10NSource = "
	SELECT	source,
			url,
            viewtype
	FROM	temp_dir_item_external_language
	WHERE	item_id=%u
            AND lang=%s
            AND view=%s
";
$module_getLastIdByPage = "
	SELECT	MAX(uniq)
	FROM	module
	WHERE	item_id=%u
            AND user_id=%u
            AND profile_id=%u
";
$wid_getTasks = "SELECT ut.task_id,ut.comments,ut.name,ut.done 
                 FROM users_tasks ut
                 INNER JOIN users_tasks_id uti 
                 ON ut.id=uti.id 
                 WHERE ut.id=%u 
                 AND uti.user_id=%u ORDER BY ut.name ";
$widcal_getEventsUser = "SELECT cal_id, title, comments, time, endtime 
                            FROM users_calendar uc 
                            INNER JOIN  users_calendar_id uci 
                            ON uc.id=uci.id 
                            WHERE uc.id=%u 
                            AND uc.pubdate=%s 
                            AND uci.user_id=%u ORDER BY uc.time ";
$widcal_getMonthEventsUser = "SELECT cal_id, DAYOFMONTH(pubdate) as day, title, time, endtime 
                                FROM users_calendar uc  
                                INNER JOIN  users_calendar_id uci 
                                ON uc.id=uci.id 
                                WHERE uc.id=%u 
                                AND MONTH(uc.pubdate)=%s 
                                AND YEAR(uc.pubdate)=%s 
                                AND uci.user_id=%u ORDER BY uc.pubdate,uc.time ";
$widcal_createNewEventUser = "
	INSERT INTO	users_calendar (id,title,comments,pubdate,time,endtime)
	SELECT		%u,
				title,
				comments,
				pubdate,
				time,
				endtime
	FROM		users_calendar uc, users_calendar uci
	WHERE		uc.id=%u
				AND uc.cal_id!=%u AND  uc.id=uci.id AND uci.user_id=%u ";
$widAddr_getAddressBook = "SELECT wa.add_id,wa.firstname,wa.lastname,wa.email,wa.company,wa.func,wa.phone1,wa.phone2,wa.other,wa.tags 
                           FROM widget_addressbook wa
                           INNER JOIN widget_addressbook_id wai
                           ON wa.id=wai.id 
                           WHERE wa.id=%u 
                           AND wai.user_id=%u 
                           ORDER BY wa.firstname,wa.lastname";
$wid_getUserLinks = "SELECT uf.link_id,uf.name,uf.url,uf.tags 
                     FROM users_favorites uf 
                     INNER JOIN users_favorites_id ufi 
                     ON uf.id=ufi.id 
                     WHERE uf.id=%u 
                     AND ufi.user_id=%u
                     ORDER BY uf.name";
$widcal_getEventsShared = "SELECT  cal_id, title, comments, time, endtime 
                            FROM users_calendar uc 
                            INNER JOIN  users_calendar_id uci 
                            ON uc.id=uci.id 
                            INNER JOIN module m 
                            ON  m.variables LIKE %s AND shared=%s  AND  m.user_id=%u AND m.uniq=%u 
                            WHERE uc.id=%u 
                            AND uc.pubdate=%s 
                            ORDER BY uc.time ";
$widcal_getMonthEventsShared = "SELECT  cal_id, DAYOFMONTH(pubdate) as day, title, time, endtime 
                                FROM users_calendar uc  
                                INNER JOIN  users_calendar_id uci 
                                ON uc.id=uci.id 
                                INNER JOIN module m 
                                ON  m.variables LIKE %s AND m.shared=%s AND  m.user_id=%u AND m.uniq=%u 
                                WHERE uc.id=%u 
                                AND MONTH(uc.pubdate)=%s 
                                AND YEAR(uc.pubdate)=%s 
                                ORDER BY uc.pubdate,uc.time ";
$wid_getUserLinksShared = "SELECT uf.link_id,uf.name,uf.url,uf.tags 
                           FROM users_favorites uf 
                           INNER JOIN users_favorites_id ufi 
                           ON uf.id=ufi.id 
                           INNER JOIN module m 
                           ON m.variables LIKE %s 
                           AND m.shared=%s 
                           AND m.user_id=%u 
                           AND m.uniq=%u
                           WHERE uf.id=%u 
                           ORDER BY uf.name";
$widAddr_getAddressBookShared = "SELECT wa.add_id,wa.firstname,wa.lastname,wa.email,wa.company,wa.func,wa.phone1,wa.phone2,wa.other,wa.tags 
                           FROM widget_addressbook wa
                           INNER JOIN widget_addressbook_id wai
                           ON wa.id=wai.id 
                           INNER JOIN module m 
                           ON m.variables LIKE %s 
                           AND m.shared=%s 
                           AND m.user_id=%u 
                           AND m.uniq=%u 
                           WHERE wa.id=%u 
                           ORDER BY wa.firstname,wa.lastname";
$wid_getTasksShared = "SELECT ut.task_id,ut.comments,ut.name,ut.done 
                       FROM users_tasks ut
                       INNER JOIN users_tasks_id uti 
                       ON ut.id=uti.id 
                       INNER JOIN module m 
                       ON m.variables LIKE %s 
                       AND m.shared=%s 
                       AND m.user_id=%u 
                       AND m.uniq=%u
                       WHERE ut.id=%u 
                       ORDER BY ut.name ";
$xml_getUserNotes = "SELECT notes 
                     FROM users_notes 
                     WHERE id=%u
                     AND user_id=%u";
$xml_getUserNotesShared = "SELECT notes 
                     FROM users_notes 
                     INNER JOIN module m 
                     ON m.variables LIKE %s 
                     AND m.shared=%s 
                     AND m.user_id=%u 
                     AND m.uniq=%u
                     WHERE id=%u
                     ";
?>