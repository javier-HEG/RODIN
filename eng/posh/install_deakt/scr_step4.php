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
/* UTF8 encoding : é ô à ù */
$not_access=0;
require_once('confinstall.inc.php');
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/install.lang.php');
require_once('../includes/file.inc.php');
require_once('functions.inc.php');

//require('../includes/pagegeneration.inc.php');

$seq=1;
$col=1;
$pos=1;
$dirid=(__LANG=="fr")?2:3;
$totalAddedCat=0;

$DB->getResults($install_getDimension,$DB->quote('dimension'));
$row = $DB->fetch(0);
$dimension = $row['value'];
$DB->freeResults();

$DB->getResults($install_getWidgetCategoryId,$DB->quote(__LANG));
if ($DB->nbResults()>0) {
    $row = $DB->fetch(0);
    $dirid = $row['id'];
    $DB->freeResults();
}

$DB->getResults($install_getMaxSeqProperties);
$row = $DB->fetch(0);
$newSeq = $row["seq"];
$DB->freeResults();

for ($i=0;$i<count($__AVLANGS);$i++)
{
    //get the cateory ID
    $DB->getResults($install_getWidgetCategoryId,$DB->quote($__AVLANGS[$i]));
    $row = $DB->fetch(0);
    $catid = $row['id'];
    $catname = $row['name'];
    $DB->freeResults();    
    //get the SEQ for this category
    $DB->getResults($install_getSeqFromLang,$DB->escape($catid));
    if ($DB->nbResults()==0) {
        $chk = $DB->execute($install_setProperties,$DB->escape($catid),$DB->escape($newSeq));
        $totalAddedCat++;
        if ($chk) {
            //$dimension.=',{"seq":"'.$newSeq.'","name":"'.$catname.'","id":'.$catid.',"lg":"'.$__AVLANGS[$i].'"}';
            $dimension.=',{"seq":"0","name":"'.$catname.'","id":'.$catid.',"lg":"'.$__AVLANGS[$i].'"}';
        }
        $newSeq++;
    } 
}
if ($totalAddedCat!=0) {
    //insert new dimension
    $DB->execute($install_updateDimension,$DB->quote($dimension),$DB->quote('dimension'));
}

if (isset($_POST["favorites"])){
	$DB->sql = "INSERT INTO dir_item (id,url,defvar,name,description,typ,status,format,height,minwidth,sizable,website,editor_id,nbvariables,creation_date,lastmodif_date,notation,voter_nb,updated,nbusers,sorting,lang,usereader,autorefresh) VALUES (85, '../modules/p_links.php?', NULL, '".lg("favorites")."', '".lg("favoritesdesc")."', 'P', 'O', 'M', 246, 280, '1', 'portaneo.net/', 1, 0, '2005-09-29', '2006-04-20', 0, 0, 'Y', 0, 0, '".__LANG."',0,0) ";
	$DB->execute($DB->sql);
	$DB->sql = "INSERT INTO dir_cat_item (item_id,category_id,first) VALUES (85,".$dirid.",'Y') ";
	$DB->execute($DB->sql);
	if (__INSTALLTYPE=="2"){
		$DB->sql = "INSERT INTO pages_module (item_id,page_id,posx,posy,posj,variables,uniq) VALUES (85,1,".$col.",".$pos.",".$seq.",'',".$seq.") ";
		$DB->execute($DB->sql);
		$seq++;
		if ($col==3){$col=1;$pos++;} else {$col++;}
	}
}
if (isset($_POST["notes"])){
	$DB->sql = "INSERT INTO dir_item (id,url,defvar,name,description,typ,status,format,height,minwidth,sizable,website,editor_id,nbvariables,creation_date,lastmodif_date,notation,voter_nb,updated,nbusers,sorting,lang,usereader,autorefresh) VALUES (84, '../modules/p_notes.php?', NULL, '".lg("notes")."', '".lg("notesdesc")."', 'P', 'O', 'M', 246, 280, '1', 'portaneo.net/', 1, 1, '2005-09-29', '2006-04-20', 0, 0, 'Y', 0, 0, '".__LANG."',0,0) ";
	$DB->execute($DB->sql);
	$DB->sql = "INSERT INTO dir_cat_item (item_id,category_id,first) VALUES (84,".$dirid.",'Y') ";
	$DB->execute($DB->sql);
	if (__INSTALLTYPE=="2"){
		$DB->sql = "INSERT INTO pages_module (item_id,page_id,posx,posy,posj,variables,uniq) VALUES (84,1,".$col.",".$pos.",".$seq.",'',".$seq.") ";
		$DB->execute($DB->sql);
		$seq++;
		if ($col==3){$col=1;$pos++;} else {$col++;}
	}
}
if (isset($_POST["rssreader"])){
	$DB->sql = "INSERT INTO dir_item (id,url,defvar,name,description,typ,status,format,height,minwidth,sizable,website,editor_id,nbvariables,creation_date,lastmodif_date,notation,voter_nb,updated,nbusers,sorting,lang,usereader,autorefresh) VALUES (86, '../modules/p_rss.php?', 'nb=5', '".lg("rssreader")."', '".lg("rssreaderdesc")."', 'P', 'O', 'R', 246, 280, '1', 'portaneo.net/', 1, 2, '2005-10-03', '2006-04-20', 0, 0, 'Y', 0, 0, '".__LANG."',1,1) ";
	$DB->execute($DB->sql);
//	$DB->sql = "INSERT INTO dir_cat_item (item_id,category_id,first) VALUES (86,2,'Y') ";
//	$DB->execute();
//	if (__INSTALLTYPE=="2"){
//		$DB->sql = "INSERT INTO pages_module (item_id,page_id,posx,posy,posj,variables,uniq) VALUES (86,1,".$col.",".$pos.",".$seq.",'',".$seq.") ";
//		$DB->execute();
//		$seq++;
//		if ($col==3){$col=1;$pos++;} else {$col++;}
//	}
}
if (isset($_POST["tasks"])){
	$DB->sql = "INSERT INTO dir_item (id,url,defvar,name,description,typ,status,format,height,minwidth,sizable,website,editor_id,nbvariables,creation_date,lastmodif_date,notation,voter_nb,updated,nbusers,sorting,lang,usereader,autorefresh) VALUES (295, '../modules/p_task.php?', '', '".lg("tasks")."', '".lg("taskdesk")."', 'P', 'O', 'M', 100, 200, '1', 'portaneo.net', 1, 0, '2006-10-27', '2006-10-27', 0, 0, 'Y', 0, 0, '".__LANG."',0,0) ";
	$DB->execute($DB->sql);
	$DB->sql = "INSERT INTO dir_cat_item (item_id,category_id,first) VALUES (295,".$dirid.",'Y') ";
	$DB->execute($DB->sql);
	if (__INSTALLTYPE=="2"){
		$DB->sql = "INSERT INTO pages_module (item_id,page_id,posx,posy,posj,variables,uniq) VALUES (295,1,".$col.",".$pos.",".$seq.",'',".$seq.") ";
		$DB->execute($DB->sql);
		$seq++;
		if ($col==3){$col=1;$pos++;} else {$col++;}
	}
}
//if (isset($_POST["planning"])){
//	$DB->sql = "INSERT INTO dir_item (id,url,defvar,name,description,typ,status,format,height,minwidth,sizable,website,editor_id,nbvariables,creation_date,lastmodif_date,notation,voter_nb,updated,nbusers,sorting,lang) VALUES (112, '../modules/pcalendar.php?', NULL, 'Mon Agenda', 'Gérez facilement votre planning.', 'P', 'O', 'I', 246, 400, '0', 'l', 10, 0, '2006-02-08', '2006-07-21', 0, 0, 'Y', 10, 0, 'fr') ";
//	$DB->execute();
//	$DB->sql = "INSERT INTO dir_cat_item (item_id,category_id,first) VALUES (112,2,'Y') ";
//	$DB->execute();
//	$DB->sql = "INSERT INTO pages_module (item_id,page_id,posx,posy,posj,variables,uniq) VALUES (112,1,".$col.",".$pos.",".$seq.",'',".$seq.") ";
//	$DB->execute();
//	$seq++;
//	if ($col==3){$col=1;$pos++;} else {$col++;}
//}
if (isset($_POST["clock"])){
	$DB->sql = "INSERT INTO dir_item (id,url,defvar,name,description,typ,status,format,height,minwidth,sizable,website,editor_id,nbvariables,creation_date,lastmodif_date,notation,voter_nb,updated,nbusers,sorting,lang,usereader,autorefresh) VALUES (152, '../modules/ptime.html?', NULL, '".lg("clock")."', '".lg("clockdesc")."', 'P', 'O', 'I', 26, 280, '1', 'portaneo.net', 1, 0, '2006-02-08', '2006-07-20', 0, 0, 'Y', 0, 0, '".__LANG."',0,0) ";
	$DB->execute($DB->sql);
	$DB->sql = "INSERT INTO dir_cat_item (item_id,category_id,first) VALUES (152,".$dirid.",'Y') ";
	$DB->execute($DB->sql);
	if (__INSTALLTYPE=="2"){
		$DB->sql = "INSERT INTO pages_module (item_id,page_id,posx,posy,posj,variables,uniq) VALUES (152,1,".$col.",".$pos.",".$seq.",'',".$seq.") ";
		$DB->execute($DB->sql);
		$seq++;
		if ($col==3){$col=1;$pos++;} else {$col++;}
	}
}
if (isset($_POST["calculator"])){
	$DB->sql = "INSERT INTO dir_item (id,url,defvar,name,description,typ,status,format,height,minwidth,sizable,website,editor_id,nbvariables,creation_date,lastmodif_date,notation,voter_nb,updated,nbusers,sorting,lang,usereader,autorefresh) VALUES (111, '../modules/pcalc.html?', NULL, '".lg("calc")."', '".lg("calcdesc")."', 'P', 'O', 'I', 102, 336, '1', 'portaneo.com', 1, 0, '2006-02-08', '2006-07-20', 0, 0, 'Y', 0, 0, '".__LANG."',0,0) ";
	$DB->execute($DB->sql);
	$DB->sql = "INSERT INTO dir_cat_item (item_id,category_id,first) VALUES (111,".$dirid.",'Y') ";
	$DB->execute($DB->sql);
	if (__INSTALLTYPE=="2"){
		$DB->sql = "INSERT INTO pages_module (item_id,page_id,posx,posy,posj,variables,uniq) VALUES (111,1,".$col.",".$pos.",".$seq.",'',".$seq.") ";
		$DB->execute($DB->sql);
		$seq++;
		if ($col==3){$col=1;$pos++;} else {$col++;}
	}
}
if (isset($_POST["analogclock"])){
	$DB->sql = "INSERT INTO dir_item (id,url,defvar,name,description,typ,status,format,height,minwidth,sizable,website,editor_id,nbvariables,creation_date,lastmodif_date,notation,voter_nb,updated,nbusers,sorting,lang,usereader,autorefresh) VALUES (340, '../modules/clock.html?', NULL, '".lg("analogc")."', '".lg("analogcdesc")."', 'P', 'O', 'I', 100, 200, '1', 'portaneo.com', 1, 0, '2007-02-08', '2007-02-08', 0, 0, 'Y', 0, 0, '".__LANG."',0,0) ";
	$DB->execute($DB->sql);
	$DB->sql = "INSERT INTO dir_cat_item (item_id,category_id,first) VALUES (340,".$dirid.",'Y') ";
	$DB->execute($DB->sql);
	if (__INSTALLTYPE=="2"){
		$DB->sql = "INSERT INTO pages_module (item_id,page_id,posx,posy,posj,variables,uniq) VALUES (340,1,".$col.",".$pos.",".$seq.",'',".$seq.") ";
		$DB->execute($DB->sql);
		$seq++;
		if ($col==3){$col=1;$pos++;} else {$col++;}
	}
}
if (isset($_POST["calendar"])){
	$DB->sql = "INSERT INTO dir_item (id,url,defvar,name,description,typ,status,format,height,minwidth,sizable,website,editor_id,nbvariables,creation_date,lastmodif_date,notation,voter_nb,updated,nbusers,sorting,lang,usereader,autorefresh,views) VALUES (112, '../modules/p_calendar.php?', NULL, '".lg("calendar")."', '".lg("calendardesc")."', 'P', 'O', 'M', 246, 280, '1', 'portaneo.com/', 1, 0, '2007-03-29', '2007-03-29', 0, 0, 'Y', 0, 0, '".__LANG."',0,0,'home,canvas') ";
	$DB->execute($DB->sql);
	$DB->sql = "INSERT INTO dir_cat_item (item_id,category_id,first) VALUES (112,".$dirid.",'Y') ";
	$DB->execute($DB->sql);
	if (__INSTALLTYPE=="2"){
		$DB->sql = "INSERT INTO pages_module (item_id,page_id,posx,posy,posj,variables,uniq) VALUES (112,1,".$col.",".$pos.",".$seq.",'',".$seq.") ";
		$DB->execute($DB->sql);
		$seq++;
		if ($col==3){$col=1;$pos++;} else {$col++;}
	}
}
if (isset($_POST["email"])){
	$DB->sql = "INSERT INTO dir_item (id,url,defvar,name,description,typ,status,format,height,minwidth,sizable,website,editor_id,nbvariables,creation_date,lastmodif_date,notation,voter_nb,updated,nbusers,sorting,lang,usereader,autorefresh) VALUES (350, '../modules/p_mail.php?', NULL, '".lg("email")."', '".lg("emaildesc")."', 'P', 'O', 'M', 100, 280, '1', 'portaneo.com/', 1, 0, '2007-04-15', '2007-04-15', 0, 0, 'Y', 0, 0, '".__LANG."',0,1) ";
	$DB->execute($DB->sql);
	$DB->sql = "INSERT INTO dir_cat_item (item_id,category_id,first) VALUES (350,".$dirid.",'Y') ";
	$DB->execute($DB->sql);
	if (__INSTALLTYPE=="2"){
		$DB->sql = "INSERT INTO pages_module (item_id,page_id,posx,posy,posj,variables,uniq) VALUES (350,1,".$col.",".$pos.",".$seq.",'',".$seq.") ";
		$DB->execute($DB->sql);
		$seq++;
		if ($col==3){$col=1;$pos++;} else {$col++;}
	}
}
if (isset($_POST["addressbook"])){
	$DB->sql = 'INSERT INTO dir_item (id,url,defvar,name,description,typ,status,format,height,minwidth,sizable,website,editor_id,nbvariables,creation_date,lastmodif_date,notation,voter_nb,updated,nbusers,sorting,lang,usereader,autorefresh) VALUES (401, "../modules/p_addressbook.php?", NULL, "'.lg("addressbook").'", "", "P", "O", "M", 100, 280, "1", "portaneo.com/", 1, 0, "2008-04-01", "2008-04-01", 0, 0, "Y", 0, 0, "'.__LANG.'",0,0) ';
	$DB->execute($DB->sql);
	$DB->sql = "INSERT INTO dir_cat_item (item_id,category_id,first) VALUES (401,".$dirid.",'Y') ";
	$DB->execute($DB->sql);
	if (__INSTALLTYPE=="2"){
		$DB->sql = "INSERT INTO pages_module (item_id,page_id,posx,posy,posj,variables,uniq) VALUES (401,1,".$col.",".$pos.",".$seq.",'',".$seq.") ";
		$DB->execute($DB->sql);
		$seq++;
		if ($col==3){$col=1;$pos++;} else {$col++;}
	}
}
if (isset($_POST["html"])){
	$DB->sql = 'INSERT INTO dir_item (id,url,defvar,name,description,typ,status,format,height,minwidth,sizable,website,editor_id,nbvariables,creation_date,lastmodif_date,notation,voter_nb,updated,nbusers,sorting,lang,usereader,autorefresh) VALUES (402, "../modules/p_html.php?", NULL, "'.lg("htmlWidgetTitle").'", "", "P", "O", "M", 100, 280, "1", "portaneo.com/", 1, 0, "2008-07-01", "2008-07-01", 0, 0, "Y", 0, 0, "'.__LANG.'",0,0) ';
	$DB->execute($DB->sql);
	$DB->sql = "INSERT INTO dir_cat_item (item_id,category_id,first) VALUES (402,".$dirid.",'Y') ";
	$DB->execute($DB->sql);
	if (__INSTALLTYPE=="2"){
		$DB->sql = "INSERT INTO pages_module (item_id,page_id,posx,posy,posj,variables,uniq) VALUES (402,1,".$col.",".$pos.",".$seq.",'',".$seq.") ";
		$DB->execute($DB->sql);
		$seq++;
		if ($col==3){$col=1;$pos++;} else {$col++;}
	}
}

$DB->execute($install_setStep5);

//generate config file
generateConfigFile(false,"","","","");

$DB->close();

header("location:step5.php");
?>