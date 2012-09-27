<?php
	//update variables in users modules
	$rows = $DB->select(FETCH_ARRAY,$install_getRssModules);
	foreach ($rows as $row){
		$oldvar=$row["variables"];
		if (strpos($oldvar,"pfid=")===false){
			if (preg_match("/rssurl=([a-zA-Z0-9%.\-:\/,_]+)/i",$oldvar,$parts)){
				$rss=urldecode($parts[1]);
				$newvar="";
				$DB->getResults($install_getIdOfFeed,$DB->quote($rss));
				if ($DB->nbResults()==1){
					$row2=$DB->fetch(0);
					$rssId=$row2["id"];
					$DB->freeResults();
				} else {
					$DB->freeResults();
					$DB->execute($install_initFeed,$DB->quote($rss));
					$rssId=$DB->getId();
				}
				$newvar=$oldvar."&pfid=".$rssId;
			}
			if ($newvar!=""){
				$DB->execute($install_setRssVars,$DB->quote($newvar),$row["user_id"],$row["profile_id"],$row["uniq"]);
			}
		}
	}
	//update variables in default pages
	$rows = $DB->select(FETCH_ARRAY,$install_getRssInPages);
	foreach ($rows as $row){
		$oldvar=$row["variables"];
		if (strpos($oldvar,"pfid=")===false){
			if (preg_match("/rssurl=([a-zA-Z0-9%.\-:\/,_]+)/i",$oldvar,$parts)){
				$rss=urldecode($parts[1]);
				$newvar="";
				$DB->getResults($install_getIdOfFeed,$DB->quote($rss));
				if ($DB->nbResults()==1){
					$row2=$DB->fetch(0);
					$rssId=$row2["id"];
					$DB->freeResults();
				} else {
					$DB->freeResults();
					$DB->execute($install_initFeed,$DB->quote($rss));
					$rssId=$DB->getId();
				}
				$newvar=$oldvar."&pfid=".$rssId;
			}
			if ($newvar!=""){
				$DB->execute($install_setRssVarInPages,$DB->quote($newvar),$row["page_id"],$row["uniq"]);
			}
		}
	}
	//update variables in directory modules
	$rows2 = $DB->select(FETCH_ARRAY,$install_getRssDirModules);
	foreach ($rows2 as $row){
		$oldvar=$row["defvar"];
		if (strpos($oldvar,"pfid=")===false && !(strpos($oldvar,"rssurl=")===false)){
			if (preg_match("/rssurl=([a-zA-Z0-9%.\-:\/,_]+)/i",$oldvar,$parts)){
				$rss=urldecode($parts[1]);
				$newvar="";
				$DB->getResults($install_getIdOfFeed,$DB->quote($rss));
				if ($DB->nbResults()==1){
					$row2=$DB->fetch(0);
					$rssId=$row2["id"];
					$DB->freeResults();
				} else {
					$DB->freeResults();
					$DB->execute($install_initFeed,$DB->quote($rss));
					$rssId=$DB->getId();
				}
				$newvar=$oldvar."&pfid=".$rssId;
			}
			if ($newvar!=""){
				$DB->execute($install_setRssVarInDir,$DB->quote($newvar),$row["id"]);
			}
		}
	}
?>