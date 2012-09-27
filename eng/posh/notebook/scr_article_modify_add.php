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
# ***************************************
# Save article 
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder     ="";
$not_access =1;
$granted    ="I";
$pagename   ="notebook/scr_article_modify_add.php";

//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$artid = $DB->escape($_POST["artid"]);
$id = $DB->escape($_SESSION['user_id']);

$file=new xmlFile();

$file->header();

$title = isset($_POST["title"])?$_POST["title"]:"";
$desc = isset($_POST["desc"])?$_POST["desc"]:"";
$access = isset($_POST["access"])?$_POST["access"]:"";
$kw = isset($_POST["kw"])?$_POST["kw"]:"";
$gid = isset($_POST["gid"])?$_POST["gid"]:"";
	
if ($artid==0)
{
	// add the article
	$DB->execute($scrarticlemodifyadd_addArticle,$DB->noHTML($title),$DB->noJavascript($desc),$DB->noHTML($kw),$DB->quote($access));
	$artid=$DB->getId();
	//add the link article - user
	$DB->execute($scrarticlemodifyadd_attributeUserToArticle,$DB->escape($id),$DB->escape($artid),$DB->escape($id),$DB->escape($id));
	//if article is in group book
	if (isset($_POST['gid']))
	{
		// add article in notebook
		$DB->execute($scrarticlemodifyadd_attributeGroupToArticle, $DB->escape($gid),$DB->escape($id),$DB->escape($id),1,$DB->escape($artid));
		//update trackback nb
		if ($DB->nbAffected()==1)
		{
			//increment trackback number
			$DB->execute($scrnotebook_updateTrackbackNb,$DB->escape($artid));
		}
	}
	// add the news for the users data feed
	$DB->execute($xmlnetworknews_insertNews,$id,"'1'",$DB->noHTML($title),$DB->quote("id=".$_SESSION['user_id']."&artid=".$artid),$DB->quote($access));
}
else
{
	// modify the article
	$DB->execute($scrarticlemodifyadd_updateArticle,$DB->noHTML($title),$DB->noJavascript($desc),$DB->noHTML($kw),$DB->escape($access),$DB->escape($artid),$DB->escape($id));
	// suppress the keywords
	$DB->execute($scrarticlemodifyadd_delOldKeywords,$DB->quote($artid));
}

//add keywords
if ($_POST["kw"]!="")
{
	$keyword=explode(",",$_POST["kw"]);
	$keywordSimplified=explode(",",$_POST["kwformated"]);
	for ($i=0;$i<count($keyword);$i++)
	{
		$selkw=$keywordSimplified[$i];
		$DB->getResults($scrarticleclassify_getKeyWordId,$DB->noHTML($selkw));
		if ($DB->nbResults()==0)
		{
			$DB->execute($scrarticleclassify_addKeyword,$DB->noHTML($keyword[$i]),$DB->noHTML($selkw));
			$kwid=$DB->getId();
		}
		else
		{
			$row = $DB->fetch(0);
			$kwid=$row["id"];
		}
		$DB->freeResults();

		$DB->execute($scrarticlemodifyadd_addNewKeywords,$DB->escape($artid),$DB->escape($kwid));
	}
}

//documents management
$DB->execute($scrarticlemodifyadd_removeDocuments,$DB->escape($artid));

$inc=0;
while (isset($_POST['fn'.$inc]))
{
	//insert document in document table
	$DB->execute($scrarticlemodifyadd_addDocument,$DB->quote($_POST['fn'.$inc]),$DB->quote($_POST['fl'.$inc]),$DB->escape($_POST['fs'.$inc]));
	$docId = $DB->getId();

	//create mapping to article
	$DB->execute($scrarticlemodifyadd_mapDocument,$DB->escape($docId),$DB->escape($artid),$DB->escape($_SESSION['user_id']),$DB->escape($_SESSION['user_id']));

	$inc++;
}

$file->status(1);

$file->footer();

$DB->close();
?>