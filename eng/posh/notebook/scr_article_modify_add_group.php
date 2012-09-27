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

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="notebook/scr_article_modify_add_group.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');



$artid=$_POST["artid"];
$gid=$_POST["gid"];
$is_copy=(isset($_POST["is_copy"])) ? $_POST["is_copy"] : 0;

$id=$_SESSION['user_id'];

$title = isset($_POST["title"])?$_POST["title"]:"";
$desc = isset($_POST["desc"])?$_POST["desc"]:"";
$access = isset($_POST["access"])?$_POST["access"]:"";
	
if ($artid==0)
{

	$astatus = "O";
	// add the article
	$DB->execute($scrarticlemodifyadd_addArticle,$DB->noHTML($title),$DB->noJavascript($desc),$DB->noHTML($_POST["kw"]),$DB->quote($access));
	$artid=$DB->getId();
	//add the link article - user
	$DB->execute($scrarticlemodifyadd_attributeUserToArticle, $DB->escape($id), $DB->escape($artid), $DB->escape($id), $DB->escape($id));

	//add the link article - group
	if($access == 1) $astatus = "M";
	//add the link article - groupbook
	$DB->execute($scrarticlemodifyadd_attributeGroupToArticle, $gid, $artid, $DB->quote($astatus), $DB->escape($id), $DB->escape($id), 0);

	// add the news for the users data feed
	$DB->execute($xmlnetworknews_insertNews,$DB->escape($_SESSION['user_id']),"'2'",$DB->noHTML($title),$DB->quote("id=".$_SESSION['user_id']."&artid=".$artid),$DB->quote($access));
}
else
{
	// modify the article
	$DB->execute($scrarticlemodifyadd_updateArticleGroup, $DB->noHTML($title), $DB->noJavascript($desc), $DB->noHTML($_POST["kw"]), $DB->escape($artid), $DB->escape($id));

	// suppress the keywords
	$DB->execute($scrarticlemodifyadd_delOldKeywords, $DB->quote($artid));
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

$DB->close();

header("location:index_group.php?id=".$gid);
?>