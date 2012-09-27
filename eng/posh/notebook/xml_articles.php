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
# User notebook
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder = "../notebook/";
$not_access = 0;
$pagename = "notebook/xml_articles.php";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../../app/exposh/l10n/'.__LANG.'/enterprise.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$id = $_GET["id"];
$page = isset($_GET["page"]) ? $_GET["page"] : 0;
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

launch_hook('notebook_header',$pagename);

//get Articles access level
if ($user_id == $id)
{
	$notebookAccessLevel = 1;
}
else
{
	$DB->getResults($xmlnotebookprofile_isInNetwork,$DB->escape($id),$DB->escape($user_id));
	if ($DB->nbResults() == 0)
	{
		$notebookAccessLevel = 3;
	}
	else
	{
		$notebookAccessLevel = 2;
	}
	$DB->freeResults();
}

$search = (isset($_GET["search"]) ? $_GET["search"] : "");

/**
 * \details  take the groups to which belong the reader 
 * */
/*$DB->getResults($xmlgroup_getGroupsUser,$DB->escape($user_id));
$tGroupsUser = array();
while($row=$DB->fetch(0))
{
	$tGroup = array();
	$tGroup["group_id"] = $row["id"];
	$tGroup["name"] = $row["name"];
	array_push($tGroupsUser, $tGroup);
}
$DB->freeResults();
*/

if ($search == "")
{
	$DB->getResults($index_getNotebookArticles,
                            $DB->escape($id),
                            $DB->escape($notebookAccessLevel),
                            ($page*10));
}
else
{
	//search by tag or plaintxt
	if ($_GET["type"] == "plaintxt")
	{
		$DB->getResults($index_getNotebookSearchedArticles,
                                $DB->escape($id),
                                $DB->escape($notebookAccessLevel),
                                $DB->quote("%".$search."%"),
                                $DB->quote("%".$search."%"),
                                $DB->escape(($page*10))
                                );
	}
	else
	{
		$DB->getResults($index_getNotebookSearchedArticlesTags,
                            $DB->escape($id),
                            $notebookAccessLevel,
                            $DB->escape($search),
                            ($page*10)
                            );
	}
}

$file = new xmlFile();

$file->header("notebook");

while ($row = $DB->fetch(0))
{
	echo '
<article>
	<id>'.$row["id"].'</id>
	<type>'.$row["type"].'</type>
	<title><![CDATA['.$row["title"].']]></title>
	<description><![CDATA['.$row["description"].']]></description>
	<longname><![CDATA['.$row["long_name"].']]></longname>
	<picture><![CDATA['.$row["picture"].']]></picture>
	<status>'.$row["status"].'</status>
	<pubdate>'.$row["pubdate"].'</pubdate>
	<ownerid>'.$row["owner_id"].'</ownerid>
	<linkedid>'.$row["linked_id"].'</linkedid>
	<trackbacknb>'.$row["trackbacknb"].'</trackbacknb>
	<commentnb>'.$row["commentsnb"].'</commentnb>
	<tags><![CDATA['.$row["keywords"].']]></tags>
</article>';
}
$DB->freeResults();

$file->footer();

$DB->close();
?>