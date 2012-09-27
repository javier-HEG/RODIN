<?php
# ************** LICENCE ****************
/*
	Copyright (c) PORTANEO.

	This file is part of POSH (Portaneo Open Source Homepage) http://sourceforge.net/projects/posh/.

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
# Refresh all feeds
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

set_time_limit(1500); // if many feeds, loading can be long
$folder     ="";
$not_access =0;
//$granted="I";
$pagename   ="portal/scr_refresh_all_feeds.php";
//includes
require_once('../includes/config.inc.php');
require_once('../includes/connection_'.__DBTYPE.'.inc.php');
require('../db_layer/'.__DBTYPE.'/portal.php');
require_once('../includes/plugin.api.php');
if (file_exists('../includes/plugins.inc.php'))
include_once('../includes/plugins.inc.php');
require_once('../includes/feed.inc.php');

//manage security to this script
if (!isset($_GET["key"]) || $_GET["key"]!=__KEY) exit();

global $DB;
if (empty($DB))
	$DB = new connection(__SERVER,__LOGIN,__PASS,__DB);

$proxy = __useproxy ? __PROXYSERVER.":".__PROXYPORT : "";
$userAccessThisFeed = false;
$lastWeek = time() - 604800;
$nbOfFeedsLoaded = 0;

$rows = $DB->select(FETCH_ARRAY,"SELECT id,url,auth,UNIX_TIMESTAMP(lastaccess) AS accessdate,proxy FROM dir_rss");
foreach ($rows as $row)
{
	//if the feed has been accessed during the past week
	if ($row["accessdate"] > $lastWeek)
	{
		if ($row["proxy"] != "")
			$proxy = $row["proxy"];

		$pfid = $row["id"];
		$refreshDelay = 900; // 15 minutes
		$rssurl = $row["url"];
		$auth = $row["auth"];

		if (empty($auth))
		{
			echo "load feed :".$row["url"]." (proxy:".$proxy.")";

			include('../includes/refreshfeed.inc.php');
            $nbOfFeedsLoaded ++;
		
			echo "<br />";
		}
	}
}
$DB->close;

error_log("All feeds automatically loaded ! (".$nbOfFeedsLoaded.")");
?>