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

header("content-type: application/xml");
$folder="../notebook/";
$not_access=0;
$isScript=true;
$isPortal=false;
$useTabs=false;
$pagename="notebook/rss.php";
//includes
require_once('includes.php');
require_once('../../app/exposh/l10n/'.__LANG.'/lang.php');
require_once('../../app/exposh/l10n/'.__LANG.'/enterprise.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');

$id=$_GET["id"];
$user_id=isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

echo '<'.'?xml version="1.0" encoding="UTF-8"?'.'>';

?>
<rss version="2.0" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#">
<channel>
<?php

//get Articles access level
if ($user_id==$id)
{
	$notebookAccessLevel=1;
	//get notebook name
	$DB->getResults($xmlnetworkuserdetail_getUser, $DB->escape($id));
	$row=$DB->fetch(0);
	$title=$row['long_name'];
	$DB->freeResults();
}
else
{
	if ($user_id == 0)
	{
		$notebookAccessLevel=3;
		//get notebook name
		$DB->getResults($xmlnetworkuserdetail_getUser, $DB->escape($id));
		$row=$DB->fetch(0);
		$title=$row['long_name'];
		$DB->freeResults();
	}
	else
	{
		$DB->getResults($xmlnotebookprofile_isInNetwork,$DB->escape($id),$DB->escape($user_id));
		if ($DB->nbResults()==0)
		{
			$notebookAccessLevel=3;
		}
		else
		{
			$notebookAccessLevel=2;
		}
		$row=$DB->fetch(0);
		$title=$row['long_name'];
		$DB->freeResults();
	}
}

/*$DB->getResults($rss_getUserInformation,$DB->escape($id));
$row=$DB->fetch(0);
//echo "<title>".$row["long_name"]."</title>";
//echo "<description><![CDATA[".$row["description"]."]]></description>";
//$picture=$row["picture"]==""?__LOCALFOLDER."images/nopicture.gif":$row["picture"];
$DB->freeResults();

$DB->getResults($rss_getRssArticles,$DB->escape($id));
*/

echo '<title>'.$title.'</title>';

$DB->getResults($index_getNotebookArticles,$DB->escape($id),$DB->escape($notebookAccessLevel),0);
while ($row=$DB->fetch(0))
{
	echo "<item>";
	echo "<title>".$row["title"]."</title>";
	//echo "<link><![CDATA[".__LOCALFOLDER."notebook/detail.php?id=".$id."&artid=".$row["id"]."]]></link>";
	echo '<link><![CDATA[javascript:$p.notebook.open('.$id.',"note","'.$title.'",indef,'.$row['id'].')]]></link>';
	echo "<description><![CDATA[".$row["description"]."]]></description>";
	//echo "<enclosure url='".$row["picture"]."' type='image/jpeg' />";
	echo "<pubdate>".$row["pubdate"]."</pubdate>";
	echo "</item>";
}
$DB->freeResults();

$DB->close();

?>
</channel>
</rss>