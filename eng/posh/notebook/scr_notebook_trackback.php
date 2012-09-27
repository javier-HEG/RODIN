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
# Get an article in my notebook
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : éàèù
# ***************************************

$folder="";
$not_access=1;
$isScript=true;
$isPortal=false;
$pagename="notebook/scr_notebook_trackback.php";
$granted="I";
//includes
require_once('includes.php');
require_once('../db_layer/'.__DBTYPE.'/enterprise.php');
require_once('../includes/xml.inc.php');

$artid=$_POST["artid"];
$owner=$_POST["owner"];

$file=new xmlFile();

$file->header();

$source=$_POST["source"];
$tzSource = explode('_', $source);

if ('D' != $source )
{
	if(sizeof($tzSource)>0)
	{
		if($tzSource[0]=='id')
		{
			if ($_SESSION['user_id']!=$owner)
			{
				// assign article to user 
				$DB->execute($scrnotebook_addTrackback,$DB->escape($_SESSION['user_id']),$DB->escape($owner),$DB->escape($tzSource[1]),$DB->escape($artid));

				if ($DB->nbAffected()==1)
				{
					//add news in newsfeed
					$DB->execute($xmlnetworknews_insertNewsWithoutTitle,$DB->escape($_SESSION['user_id']),"'7'",$DB->quote("id=".$_SESSION['user_id']."&artid=".$artid),$DB->escape($artid));
					//increment trackback number
					$DB->execute($scrnotebook_updateTrackbackNb,$DB->escape($artid));
			
					$file->status(1);
				}
			}
		}
		else
		{
			// assign article to groupbook
			$DB->execute($scrarticlemodifyadd_attributeGroupToArticle, $DB->escape($tzSource[1]), $DB->escape($owner), $DB->escape($_SESSION["user_id"]), 1, $DB->escape($artid));

			if ($DB->nbAffected()==1) // && $_SESSION['user_id']!=$owner)
			{
				//increment trackback number
				$DB->execute($scrnotebook_updateTrackbackNb, $DB->escape($artid));

				$file->status(1);
			}
		}
	}	
}

$file->footer();

$DB->close();
?>