<?php
	$rows = $DB->select(FETCH_ARRAY,$install_getRssModules);
	foreach ($rows as $row)
	{
		$var=$row["variables"];
		preg_match('/pfid=(\d+)/',$var,$matches);
		if (count($matches)>0)
		{
			$fid=$matches[1];
			$DB->execute($install_updateFidField,$fid,$row["user_id"],$row["profile_id"],$row["uniq"]);
		}
	}
?>