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
# comment rss xml file
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

header("content-type: application/xml");
$folder="../groupbook/";
$not_access=0;
$isScript=true;
$isPortal=false;
$useTabs=false;
$pagename="groupbook/comments_rss.php";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../../app/exposh/l10n/'.__LANG.'/enterprise.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');


$id=$_GET["id"];
$artid=$_GET["artid"];
$user_id=isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>';


?>
<rss version="2.0" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
<channel>
<?php

if ($user_id == 0)
{
	$groupbookAccessLevel="'3'";
	$userIsMemberOfGroup = 0;
	//get group title
	$DB->getResults($scrgroup_getGetGroupName, $DB->escape($gid), 0);
	$row=$DB->fetch(0);
	$title=$row['name'];
	$DB->freeResults();
}
else
{
	$DB->getResults($xmlnotebookprofile_isInGroup, $DB->escape($gid), $DB->escape($user_id));
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

	$row=$DB->fetch(0);
	$title=$row['name'];

	$DB->freeResults();
}

echo '<title>'.$title.'</title>';

//get the latest comments for this article
$DB->getResults($commentsrss_getRssArticleCommentsGroups,DB->escape($artid),DB->escape($groupbookAccessLevel),DB->escape($userIsMemberOfGroup));
while ($row=$DB->fetch(0))
{
	echo "<item>";
	echo "<title><![CDATA[".$row["long_name"]." : ".substr($row["message"],0,20)."...]]></title>";
	//echo "<link><![CDATA[".__LOCALFOLDER."groupbook/detail.php?id=".$id."&artid=".$artid."#comments]]></link>";
	echo '<link><![CDATA[javascript:$p.notebook.open('.$id.',"group","'.$title.'",indef,'.$artid.',indef,"comments")]]></link>';
	echo "<description><![CDATA[".$row["message"]."]]></description>";
	echo "<pubdate>".$row["pubdate"]."</pubdate>";
	echo "</item>";
}
$DB->freeResults();


$DB->close();


?>
</channel>
</rss>