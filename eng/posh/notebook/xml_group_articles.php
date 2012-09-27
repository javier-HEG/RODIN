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

$folder="../notebook/";
$not_access=0;
$pagename="notebook/xml_group_articles.php";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../../app/exposh/l10n/'.__LANG.'/enterprise.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$id=$DB->escape($_GET["id"]);
$page=isset($_GET["page"])?$_GET["page"]:0;
$user_id=isset($_SESSION['user_id'])?$_SESSION['user_id']:0;

launch_hook('groupbook_header',$pagename);

$DB->getResults($xmlnotebookprofile_isInGroup, $DB->escape($id), $DB->escape($_SESSION["user_id"]));
if ($DB->nbResults()==0)
{
	$groupbookAccessLevel="'3'";
	$userIsMemberOfGroup = 0;
}
else
{
	$groupbookAccessLevel="'3', '2'";
	$userIsMemberOfGroup = 1;
}
$DB->freeResults();

$search=(isset($_GET["search"]) ? $_GET["search"] : "");

$file=new xmlFile();

$file->header("groupbook");

if ($search=="")
{
	$DB->getResults($index_getGroupbookArticles,
					$DB->escape($id),
					$groupbookAccessLevel,
					$DB->escape($page*10));
}
else
{
	//search by tag or plaintxt
	if ($_GET["type"]=="plaintxt")
	{		
		$DB->getResults($index_getGroupbookSearchedArticles,
						$DB->escape($id),
						$groupbookAccessLevel,
						$DB->quote("%".$search."%"),
						$DB->quote("%".$search."%"),
						$DB->escape($page*10));
	}
	else
	{
		$DB->getResults($index_getGroupbookSearchedArticlesTags,
						$DB->escape($id),
						$groupbookAccessLevel,
						$DB->quote($_GET["searchid"]),
						$DB->escape($page*10));
	}
}

while ($row=$DB->fetch(0))
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
	<is_copy>'.$row["is_copy"].'</is_copy>
</article>';
}
$DB->freeResults();

$file->footer();

$DB->close();
?>