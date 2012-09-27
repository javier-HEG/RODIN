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
# classify an article
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="portal/scr_article_classify.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header();

$id = isset($_POST["id"])?$_POST["id"]:0;
$oldOwner=($_POST["owner"]==0?$_SESSION['user_id']:$_POST["owner"]);

// add article in notebook
$desc=strip_tags($_POST["desc"]).'<br /><br /><div class="notebooklink"><a href="'.$_POST["link"].'" target="_blank"><img src="'.$_POST["icon"].'" align="absmiddle" /> '.$_POST["title"].'</a><br />'.$_POST["source"].' ('.$_POST["dat"].')</div>';
$title = isset($_POST["title"])?$_POST["title"]:"";
$kw = isset($_POST["kw"])?$_POST["kw"]:"";
$faid = isset($_POST["faid"])?$_POST["faid"]:"";
$priv = isset($_POST["priv"])?$_POST["priv"]:"";
$link = isset($_POST["link"])?$_POST["link"]:"";
$kwformated = isset($_POST["kwformated"])?$_POST["kwformated"]:"";

$DB->execute($scrarticleclassify_addInNotebook,$DB->noHTML($title),$DB->quote($desc),$DB->noHTML($kw),$DB->escape($faid),$DB->escape($id),$DB->quote($priv));
$noteid=$DB->getId();
//connect new article with owner	
$DB->execute($scrarticleclassify_addUserLink,$DB->escape($_SESSION['user_id']),$DB->escape($noteid),$DB->escape($oldOwner),$DB->escape($oldOwner));
//add news in the network feed
$DB->execute($xmlnetworknews_insertNews,$DB->escape($_SESSION['user_id']),"'2'",$DB->noHTML($title),$DB->noHTML($link),$DB->quote($priv));
//suppress article from list
$DB->execute($scrsuparticle_removeArticle,$DB->escape($_SESSION['user_id']),$DB->escape($id));
	
//add keywords
if ($kw!="")
{
	$keyword=explode(",",$kw);
	$keywordSimplified=explode(",",$kwformated);
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

		//$DB->execute($scrarticleclassify_addKeywordLink,$_SESSION['user_id'],$id,$kwid);
		$DB->execute($scrarticlemodifyadd_addNewKeywords,$DB->escape($noteid),$DB->escape($kwid));
	}
}
$DB->close();

$file->status(1);
$file->returnData($_POST["act"]);

$file->footer();
?>