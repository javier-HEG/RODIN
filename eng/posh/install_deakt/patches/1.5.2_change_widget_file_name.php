<?php
	//Update widget type>U for widget that open an external file in a frame
	$DB->execute("UPDATE dir_item SET typ='U' WHERE SUBSTR(url,0,10)!='../modules' ");
	
	// Update widget file name
	$DB->getResults("SELECT url,id FROM dir_item WHERE typ in ('I') AND SUBSTR(url,0,17)='../modules/module' ");
	while ($row=$DB->fetch(0))
	{
		$widgetUrl=replace(".php?",".php",$row["url"]);
		if (is_file($widgetUrl))
			copy($widgetUrl,"../modules/module".$row["id"].".php?");
	}
	$DB->freeResults();
	
	// Update widget param file name
	$DB->getResults("SELECT url,id FROM dir_item WHERE typ in ('I') AND SUBSTR(url,0,17)='../modules/module' ");
	while ($row=$DB->fetch(0))
	{
		$widgetUrl=replace(".php?","_param.xml",$row["url"]);
		if (is_file($widgetUrl))
			copy($widgetUrl,"../modules/module".$row["id"]."_param.xml");
	}
	$DB->freeResults();
	
	//Update url in dir_item ??????????????
	
	// Create param file name for IFRAME URL widget
	$DB->getResults("SELECT url,id FROM dir_item WHERE typ='U' ");
	while ($row=$DB->fetch(0))
	{
		copy("../modules/template_param.xml","../modules/module".$row["id"]."_param.xml");
	}
	$DB->freeResults();
?>