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
# User notebook article detail 
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="../notebook/";
$not_access=0;
$isScript=false;
$isPortal=false;
$useTabs=false;
$pagename="notebook/xml_articles_detail.php";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../../app/exposh/l10n/'.__LANG.'/enterprise.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$artid=$DB->escape($_GET["artid"]);
$user_id=isset($_SESSION['user_id'])?$_SESSION['user_id']:0;

launch_hook('notebook_header',$pagename);

//if (isset($_GET["id"]))
//{
//	$id=$_GET["id"];
//}
//else
//{
//get article owner
$DB->getResults($detail_getArticleOwner,$DB->escape($artid));
$row=$DB->fetch(0);
$id=$row["owner_id"];
$DB->freeResults();
//}

//get Articles access level
if ($user_id==$id)
{
	$notebookAccessLevel=1;
}
else
{
	$DB->getResults($xmlnotebookprofile_isInNetwork,$DB->escape($id),$DB->escape($user_id));
	if ($DB->nbResults()==0) {
		$notebookAccessLevel=3;
	}
	else {
		$notebookAccessLevel=2;
	}
	$DB->freeResults();
}

$file=new xmlFile();

$file->header("article");

// get article information
$DB->getResults($detail_getArticleInfo,$DB->escape($artid));
$row=$DB->fetch(0);
$articleStatus=$row["status"];
if ($articleStatus<$notebookAccessLevel) { exit(); }
echo '
<type>'.$row["type"].'</type>
<title><![CDATA['.$row["title"].']]></title>
<description><![CDATA['.$row["description"].']]></description>
<longname><![CDATA['.$row["long_name"].']]></longname>
<picture><![CDATA['.$row["picture"].']]></picture>
<status>'.$row["status"].'</status>
<linked_id>'.$row["linked_id"].'</linked_id>
<owner_id>'.$row["owner_id"].'</owner_id>
<pubdate>'.$row["pubdate"].'</pubdate>
<tags><![CDATA['.$row["keywords"].']]></tags>
';

$DB->getResults($detail_getDocuments,$DB->escape($artid));
while ($row=$DB->fetch(0))
{
	echo '
<document>
	<id>'.$row["id"].'</id>
	<title><![CDATA['.$row["title"].']]></title>
	<link><![CDATA['.$row["link"].']]></link>
	<size>'.$row["size"].'</size>
</document>
';
}
$DB->freeResults();

//get the trackbacks
$DB->getResults($detail_getTrackbacks,$DB->escape($artid));
if ($DB->nbResults()!=0)
{
	while ($row=$DB->fetch(0))
	{
		echo '
<trackback>
	<type>note</type>
	<id>'.$row["user_id"].'</id>
	<name><![CDATA['.$row["long_name"].']]></name>
	<picture><![CDATA['.$row["picture"].']]></picture>
</trackback>
';
	}
}
$DB->freeResults();

// Trackbacks on groups
$DB->getResults($detail_getTrackbacksGroup,$DB->escape($artid));
if ($DB->nbResults()!=0)
{
	while ($row=$DB->fetch(0))
	{
	echo '
<trackback>
	<type>group</type>
	<id>'.$row["group_id"].'</id>
	<name><![CDATA['.$row["name"].']]></name>
</trackback>
';
	}
}
$DB->freeResults();

//get the comments
$DB->getResults($detail_getComments,$DB->escape($artid));
while ($row=$DB->fetch(0))
{
	echo '
<comment>
	<id>'.$row["id"].'</id>
	<picture><![CDATA['.$row["picture"].']]></picture>
	<userid>'.$row["user_id"].'</userid>
	<longname><![CDATA['.$row["long_name"].']]></longname>
	<pubdate>'.$row["pubdate"].'</pubdate>
	<message><![CDATA['.$row["message"].']]></message>
</comment>
';
}
$DB->freeResults();

$file->footer();

$DB->close();
?>