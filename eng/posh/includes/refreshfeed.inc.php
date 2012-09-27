<?php
	# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
	//check if feed has been refreshed lately
	$DB->getResults($xmlfeeds_getRefreshDate,$DB->escape($pfid));
	$row = $DB->fetch(0);
	$rssurl = $row['url'];
	$title = $row['title'];
	$delai = $row['delai'];
	$lastloadedid = $row['lastloadedid'];
    $lastmodifiedDB = $row['http_last_modified'];
	$DB->freeResults();
	
	if ($delai > $refreshDelay)
	{
		//update last_feed_access only if a user action launch this loading (not an automatic loading) => feed is currently accessed and in use
		if (!isset($userAccessThisFeed) || $userAccessThisFeed)
        {
			$DB->execute($xmlfeeds_setLoadAndAccessDate,$DB->escape($pfid),$DB->escape($refreshDelay));
        }
		else
        {
			$DB->execute($xmlfeeds_setLoadDate,$DB->escape($pfid),$DB->escape($refreshDelay));
        }
		if ($DB->nbAffected()==1)
		{
			//get feed information & content
			$feed = new feed($rssurl);
			launch_hook('xmlfeeds_headeradditions',$feed,$proxy);
			$feed->load();
            
            //get the last modified date (web server information)
            $lastmodifiedRSS = $feed->http->get_header_value('Last-Modified');
            if ($lastmodifiedRSS == '') {
                $lastmodifiedRSS = date("Y-m-d H:i:s",time()-($refreshDelay + 200));
            }            
            if ( $lastmodifiedRSS != '' && $lastmodifiedRSS != $lastmodifiedDB  )
            {
    			$RSSarticles = $feed->getNewArticles($lastloadedid,$title);
    			if (count($RSSarticles) > 1)
    			{
    				$DB->sql = "INSERT INTO feed_articles (feed_id,title,link,description,image,video,audio,pubdate,loaddate,uniqid,source) VALUES ";
    				$nbArticles = count($RSSarticles);
    				for ($i = $nbArticles-1;$i != 0;$i--){
    					$DB->sql .= "(".$pfid.",".$DB->quote($RSSarticles[$i]["title"])
                                                .",".$DB->quote($RSSarticles[$i]["link"])
                                                .",".$DB->quote($RSSarticles[$i]["desc"])
                                                .",".(isset($RSSarticles[$i]["image"])?$DB->quote($RSSarticles[$i]["image"]):"''")
                                                .",".(isset($RSSarticles[$i]["video"])?$DB->quote($RSSarticles[$i]["video"]):"''")
                                                .",".(isset($RSSarticles[$i]["audio"])?$DB->quote($RSSarticles[$i]["audio"]):"''")
                                                .",".$DB->quote($RSSarticles[$i]["date"])
                                                .",CURRENT_DATE,"
                                                .$DB->quote($RSSarticles[$i]["id"])
                                                .",".$DB->quote($RSSarticles[$i]["source"])
                                                .") ";
    					if ($i != 1) $DB->sql .= ",";
    				}	
    				$DB->execute($DB->sql);
    				$DB->execute($xmlfeeds_setLastId,
                                        $DB->quote($RSSarticles[1]["id"]),
                                        $DB->quote($lastmodifiedRSS),
                                        $DB->escape($pfid)
                                );
    			}
            }
		}
		if ($title == "")
		{
			$title = $RSSarticles[0]["title"];
			$DB->execute($xmlfeeds_setTitle,$DB->quote($title),$DB->escape($pfid));
		}
	}
	$DB->freeResults();
?>