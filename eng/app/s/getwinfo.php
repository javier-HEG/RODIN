<?php
###########################################
#
# SERVICE getwinfo
# returns an xml object with informations on rodin widgets
# 1. user informations 2. Widget informations

	require_once("../u/FRIdbUtilities.php");

	$user_id= $_REQUEST['user']; 


$OUT="<rodin_winfo>
";
	$user_info=fri_get_user_info($user_id);
	//tab-id UNIVERSE rausziehen
	$tab_id= fri_get_tab_id("UNIVERSE",$user_id);
	$winfos = fri_get_relevant_widget_info($tab_id,$user_id);
	//print "<hr> <b>WIDGETINFOS zu TAB UNIVERSE ($tab_id) bzgwl. User ($user_id - {$user_info['username']}{$user_info['long_name']}):</b>";
 
$OUT.=<<<EOT
  <userid>{$user_info['id']}</userid>
  <useremail><![CDATA[  {$user_info['long_name']}   ]]></useremail >
  <useremail><![CDATA[  {$user_info['username']}   ]]></useremail >
 <widgets>
 
EOT;
  if (count($winfos)>0)
	{
		foreach ($winfos as $winfo)
		{
			$uri = clean_w_url($winfo['url']);
			
			$tags = collect_tags($winfo['url']);
			/*
			print "<br> ID: ".$winfo['id'];
			print "<br> url: ".$winfo['url'];
			print "<br> name: ".$winfo['name'];
			print "<br> description: ".$winfo['description'];
			print "<br> tags: ".$tags;
		*/
			$OUT .=<<<EOT
		<widget>  
			<uri>{$uri}</uri>
			<tags>{$tags}</tags>
			<id>{$winfo['id']}</id>	
			<desc><![CDATA[ {$winfo['description']} ]]></desc > 
		</widget>
	
EOT;
		}
	}
	else print "no widget ?";


$now=date("D M j G:i:s T Y");
$OUT.=<<<EOT
 </widgets>
</rodin_winfo>
EOT;




header ("content-type: text/xml");
print "<?xml version=\"1.0\" encoding=\"UTF-8\"?>";
print <<<EOX
<!-- RODIN XML service getwinfo (HEG)-->
<!-- fabio.fr.ricci@hesge.ch -->
<!-- $now -->
<!-- Informations on loaded widgets in TAB UNIVERSE for user $user_id-->
$OUT
EOX;





function clean_w_url($url)
{

	$cleaned=$url;
	if (preg_match("/(.*)\?&/",$url,$match))
	{
		//foreach ($match as $m) print "<br />1 match ".$m;
		$cleaned=$match[1];
	}
	else if (preg_match("/(.*)\?/",$url,$match))
	{
		//foreach ($match as $m) print "<br />2 match ".$m;
		$cleaned=$match[1];
	}
	
	//print "<br />returning $cleaned";
	return $cleaned;
}



?>