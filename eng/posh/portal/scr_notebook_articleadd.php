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
# Add items in notebook
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder     ="";
$not_access =1;
$isScript   =true;
$isPortal   =false;
$pagename   ="portal/scr_notebook_articleadd.php";
$granted    ="I";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();

$file->header();

$id=$_POST["oid"];
$type=$_POST["type"];
$linkid=$_POST["linked"];

if (2 == $_POST["access"])
{
	$status = "M";
}
else
{
	$status = "O";
}

$pubtitle = isset($_POST["pubtitle"]) ? $_POST["pubtitle"] : "";
$desc = isset($_POST["desc"]) ? $_POST["desc"] : "";
$kw = isset($_POST["kw"]) ? $_POST["kw"] : "";
$faid = isset($_POST["faid"]) ? $_POST["faid"] : 0;
$access = isset($_POST["access"]) ? $_POST["access"] : "";

$DB->execute($scrnotebookarticleadd_addArticle,$DB->noHTML($pubtitle),$DB->noJavascript($desc),$DB->noHTML($kw),$DB->escape($faid),$DB->escape($type),$DB->escape($linkid),$DB->quote($access));

if ($DB->nbAffected() != 0)
{
	$noteid = $DB->getId();

	$DB->execute($scrnotebookarticleadd_addLink,$DB->escape($_SESSION['user_id']),$DB->escape($noteid),$DB->escape($_SESSION['user_id']),$DB->escape($_SESSION['user_id']));
	
	echo "<ret>".$type."_".$id."_".($type=="4" ? $linkid : $noteid)."</ret><msg><![CDATA[".lg("notebookUpdated")."]]></msg>";
}
else
{
	echo "<ret>-1</ret><err><![CDATA[".lg("technicalIssue")."]]></err>";
}

//add keywords
if ( $kw!="" )
{
	$keyword=explode(",",$_POST["kw"]);
	$keywordSimplified=explode(",",$_POST["kwformated"]);
	for ($i=0;$i<count($keyword);$i++)
	{
		$selkw=$keywordSimplified[$i];
		$DB->getResults($scrsendtofriend_getKeyword,$DB->noHTML($selkw));
		if ($DB->nbResults()==0)
		{
			$DB->execute($scrsendtofriend_addNewKeyword,$DB->noHTML($keywordSimplified[$i]),$DB->noHTML($selkw));
			$kwid=$DB->getId();
		}
		else
		{
			$row = $DB->fetch(0);
			$kwid=$row["id"];
		}
		$DB->freeResults();

		//portal keywords
		if ($type=="4" && $access=="3")
		{
			$DB->execute($scrsendtofriend_insertKeyword,$DB->escape($linkid),$DB->escape($kwid));
		}
		//notebook article keywords
		$DB->execute($scrnotebookarticleadd_addNewKeywords,$DB->escape($noteid),$DB->escape($kwid));
	}
}

// Add article to groups
$artid = $noteid;
$cpt = 0;
while (isset($_POST["gId$cpt"]))
{
	$groupId = $_POST["gId$cpt"];
	$DB->execute($scrarticlemodifyadd_attributeGroupToArticle,
                    $DB->escape($groupId),
                    $DB->escape($_SESSION["user_id"]),
                    $DB->escape($_SESSION["user_id"]),
                    1,
                    $DB->escape($artid));

	if ($DB->nbAffected() != 0)
	{
		//increment trackback number
		$DB->execute($scrnotebook_updateTrackbackNb,
                        $DB->escape($artid));
	}
	$cpt++;
}

$DB->close();

$file->status(1);

$file->footer();
?>