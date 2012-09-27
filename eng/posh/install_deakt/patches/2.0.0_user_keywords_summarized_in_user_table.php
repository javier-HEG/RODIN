<?php
	$install_getUsers="SELECT id FROM users";
	$rows = $DB->select(FETCH_ARRAY,$install_getUsers); 
	foreach ($rows as $row)
	{
		$install_getKeywordsForUser="SELECT label FROM network_keywords,search_keyword WHERE kw_id=search_keyword.id AND user_id=%u AND friend_id=0 ";
		$DB->getResults($install_getKeywordsForUser,$row["id"]);
		$userKeywords="";
		while ($row2=$DB->fetch(0))
		{
			$userKeywords.=",".$row2["label"];
		}
		$DB->freeResults();

		$install_updateKeywordsListForUser="UPDATE users SET keywords='%s' WHERE id=%u ";
		$DB->execute($install_updateKeywordsListForUser,substr($userKeywords,1),$row["id"]);
	}
?>