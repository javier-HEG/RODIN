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
$scrrssaddtodir_addRssFeed = "INSERT INTO temp_dir_item (
                                    url,defvar,name,description,typ,
                                    status,format,
                                    height,minwidth,sizable,website,editor_id,nbvariables,
                                    creation_date,lastmodif_date,lang,keywords,usereader,autorefresh) 
                                    VALUES (
                                        %s,%s,%s,%s,%s,
                                        'N','R',100,
                                        280,1,%s,%u,1,CURRENT_DATE,CURRENT_DATE,
                                        %s,%s,%u,1) ";
$scrrssaddtodir_addRssDir = "INSERT INTO temp_dir_cat_item (item_id, category_id,first) VALUES (%u,%u,'Y') ";
$scrrssadd1_addModule = "INSERT INTO temp_dir_item (url,
            defvar,name,description,typ,status,format,height,
            minwidth,sizable,website,editor_id,nbvariables,creation_date,lastmodif_date,lang) VALUES 
            ('','nb=5',%s,%s,%s,'C','R',100,280,1,
            %s,%u,1,CURRENT_DATE,CURRENT_DATE,%s) ";
$scrrssadd1_updateUrl = "UPDATE temp_dir_item SET url='../modules/module%u.php?' WHERE id=%u ";
$scrrssadd1_addRedactorLink = "INSERT INTO redactor_map_item_feed (item_url,feed_id) VALUES ('../modules/module%u.php?',%u) ";
/**  temp_dir **/
$screxpertadd1_updateModule = "UPDATE temp_dir_item 
                                        SET     name=%s,
                                                description=%s,
                                                format=%s,
                                                height=%u,
                                                minwidth=280 
                                            WHERE 
                                                id=%u 
                                                AND editor_id=%u ";
$screxpertadd1_updateUrl = "UPDATE 
                                    temp_dir_item 
                                    SET url=%s 
                                        WHERE id=%u ";
$screxpertadd1_updateVars = "UPDATE temp_dir_item 
                                    SET defvar=%s,nbvariables=%u 
                                        WHERE id=%u ";
$screxpertadd1_insertDir = "INSERT INTO temp_dir_cat_item 
                                    (item_id,category_id,first) 
                                    VALUES
                                    (%u,38,'Y') ";
$screxpertadd0_addModule = "INSERT INTO temp_dir_item 
                                (   url,defvar,name,description,typ,
                                    status,format,height,minwidth,sizable,
                                    website,editor_id,nbvariables,creation_date,lastmodif_date,
                                    lang
                                ) 
                                VALUES 
                                (   '','','','','U',
                                    'C','',0,280,1,
                                    '',%u,0,CURRENT_DATE,CURRENT_DATE,
                                    %s
                                ) ";
                                
/** new requests **/                                 
$tutorial_addModule = "INSERT INTO temp_dir_item 
                                (   url,defvar,name,description,typ,
                                status,format,height,minwidth,sizable,
                                website,editor_id,nbvariables,creation_date,
                                lastmodif_date,
                                lang,logo,keyword,
                                keywords,autorefresh,views
                                ) 
                                VALUES 
                                (   %s,%s,%s,%s,'U',
                                 %s,%s,%u,%u,%u,
                                  %s,%u,%u,CURRENT_DATE,CURRENT_DATE,
                                  %s,%s,%s,
                                  %s,%s,%s
                                ) ";
$tutorial_updateModule = "UPDATE  temp_dir_item SET 
								defvar = %s,
								name = %s,
								description = %s,
								typ='U',
                                status = %s,
								format = %s,
								height = %u,
								minwidth = %u,
								sizable = %u,
                                website = %s,
								editor_id = %u,
								nbvariables = %u,
                                lastmodif_date = CURRENT_DATE ,
                                lang = %s,
								logo = %s,
								keyword = %s,
                                keywords = %s,
								autorefresh=%s,
                                views=%s
                                WHERE id = %s ";
$tutorial_updateValidatedModule = "UPDATE  dir_item SET 
								defvar = %s,
								name = %s,
								description = %s,
								typ='U',
                                status = %s,
								format = %s,
								height = %u,
								minwidth = %u,
								sizable = %u,
                                website = %s,
								editor_id = %u,
								nbvariables = %u,
                                lastmodif_date = CURRENT_DATE ,
                                lang = %s,
								autorefresh=%s
                                WHERE id = %s
                                 ";    
                                                        
$tutorial_insertTempDirCat = "INSERT INTO temp_dir_cat_item 
                                    (item_id,category_id,first) 
                                    VALUES
                                    (%u,%u,'Y') ";
$tutorial_updateTempDirCat = "UPDATE temp_dir_cat_item,temp_dir_item 
                                SET 
                                category_id=%u,
                                temp_dir_item.lang=%s 
                                WHERE 
                                item_id=%u 
                                AND item_id=id 
                                AND editor_id=%u "; 
$tutorial_insertSource = "INSERT INTO dir_item_external (item_id,source,xmlmodule,url,last_updated,status,view)
                                        SELECT %u,source,xmlmodule,url,CURRENT_DATE,'validated',view
                                        FROM   temp_dir_item_external
                                        WHERE  item_id=%u
                                ";
$tutorial_insertTempL10NSource = "INSERT INTO temp_dir_item_external_language
                                        (item_id,lang,url,
                                            source,view,viewtype,params,
                                            last_updated)
                                        VALUES
                                        (%u,%s,%s,%s,%s,%s,%s,NOW())
                                ";
$tutorial_updateTempL10NSource = "
            UPDATE temp_dir_item_external_language
                SET
                    source=%s,
                    params=%s,
                    viewtype=%s
            WHERE 
                item_id= %u
                AND view=%s
                AND lang=%s
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
$tutorial_getL10NSource = "
	SELECT	source,
			url,
            viewtype
	FROM	dir_item_external_language
	WHERE	item_id=%u
            AND lang=%s
            AND view=%s
";               
$tutorial_updateSource = "UPDATE dir_item_external 
                                SET 
                                source=%s,
                                xmlmodule=%s,
                                status=%s
                                WHERE item_id = %u
                                ";

$tutorial_getSource = "SELECT source,xmlmodule,url 
                            FROM dir_item_external
                            WHERE
                                item_id=%u";
                                
$tutorial_updateUrl = "UPDATE temp_dir_item SET
                                    url = %s
                                    WHERE id = %u
                                ";                                
$tutorial_updatel10n = "UPDATE temp_dir_item SET
                                    l10n = %s
                                    WHERE id = %u
                                ";                                             
/** end new requests **/                                
/**  end tmp dir, see other requests on tmp_dir in admin requests**/
$rssaddtodirectory_addFeed = "SELECT id,url FROM dir_rss WHERE id=%u ";
$rssaddtodirectory_getDefvar = "SELECT defvar FROM dir_item WHERE url='../modules/p_rss.php?' ";
$rssadd4_changeModStatus = "UPDATE temp_dir_item SET status='N' WHERE id=%u AND editor_id=%u ";
$rssadd4_getModType = "SELECT typ FROM temp_dir_item WHERE id=%u ";
$rssadd3_setDirectory = "INSERT INTO temp_dir_cat_item (item_id, category_id,first) VALUES (%u,%u,'Y') ";
$rssadd2_getModInformation = "SELECT url,name,defvar,height,format,minwidth,nbvariables FROM temp_dir_item WHERE id=%u AND editor_id=%u ";
$expertadd4_setStatus = "UPDATE temp_dir_item SET status='N' WHERE id=%u AND editor_id=%u ";
$expertadd3_setDirectory = "UPDATE temp_dir_cat_item,temp_dir_item SET category_id=%u,temp_dir_item.lang=%s WHERE item_id=%u AND item_id=id AND editor_id=%u ";
$expertadd3_updateParam = "UPDATE temp_dir_item SET keywords=%s,autorefresh=%u WHERE id=%u ";
$expertadd2_getModule = "SELECT url,name,defvar,height,format,minwidth,nbvariables FROM temp_dir_item WHERE id=%u AND editor_id=%u ";
$tutorial_getModulesInfo = "SELECT  tde.item_id,
                                        tde.source,
                                        tde.xmlmodule,
                                        tde.status as status1,
										di.url,
                                        di.defvar,
                                        di.name,
                                        di.description,
										di.typ,
                                        di.status as status2,
                                        di.format,
                                        di.height,
                                        di.minwidth,di.website,
                                        di.sizable,
                                        di.website,
                                        di.nbvariables,
                                        di.lang,
                                        di.usereader,
                                        di.autorefresh,
                                        di.icon,
                                        di.views,
                                        di.l10n
                                        FROM dir_item_external tde,dir_item di
                                            WHERE 
												tde.item_id=%u AND tde.item_id=di.id";
$tutorial_setTemp_Dir_Item_External="INSERT INTO temp_dir_item_external (item_id,source,xmlmodule,url,last_updated,status,view,type,id_dir_item)  VALUES (%u,%s,%s,%s,CURRENT_DATE,'quarantine',%s,%s,%u) ";                                                
$tutorial_DeleteTemp_Dir_Item_External="DELETE FROM temp_dir_item_external WHERE id_dir_item=%u";
$tutorial_DeleteTemp_Dir_Item_ExternalView="DELETE FROM temp_dir_item_external WHERE item_id=%u AND view=%s";
$tutorial_getNbViews="SELECT view 
                      FROM 
                        temp_dir_item_external 
                      WHERE 
                        item_id=%u";
$tutorial_DeleteTemp_Dir_Item="DELETE FROM temp_dir_item WHERE  id_dir_item=%u";
$tutorial_addModuleModify = "INSERT INTO temp_dir_item 
                                (   url,defvar,name,description,typ,
                                status,format,height,minwidth,sizable,
                                website,editor_id,nbvariables,creation_date,
                                lastmodif_date,
                                lang,logo,keyword,
                                keywords,autorefresh,views,id_dir_item
                                ) 
                                VALUES 
                                (   %s,%s,%s,%s,'U',
                                 %s,%s,%u,%u,%u,
                                  %s,%u,%u,CURRENT_DATE,CURRENT_DATE,
                                  %s,%s,%s,
                                  %s,%s,%s,%u
                                ) ";
$tutorial_UpdateTemp_Dir_Item_External="UPDATE 
                                        temp_dir_item_external 
                                    SET 
                                        source=%s,
                                        xmlmodule=%s,
                                        url=%s,
                                        last_updated=CURRENT_DATE,
                                        status='quarantine',
                                        type=%s 
                                    WHERE 
                                        item_id=%u 
                                        AND view=%s";                  
$dir_rss_setIconIdByPFID="UPDATE dir_rss SET iconid=%s WHERE id=%u";
$tutorial_getItemViews = "SELECT view FROM dir_item_external WHERE item_id=%u";  
$tutorial_updateTitle = "
        UPDATE temp_dir_item
        SET 
            name = %s,
            description = %s
        WHERE
            id=%u    
        ";
?>