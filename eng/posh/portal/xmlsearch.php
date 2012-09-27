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
# Searched modules list
#
# !! be careful, this file must be saved under uft8 format, and display an e accentuated here : é
# ***************************************

$motcle=(isset($_GET["searchtxt"]))?$_GET["searchtxt"]:exit();
$page=(isset($_GET["p"]))?$_GET["p"]:exit();
$nb_searchSecured=0;
$nb_search=0;
$nb_searchSecured1=0;
$nb_search2=0;

$folder="";
$not_access=0;
$pagename="portal/xmlsearch.php";
//includes
require_once('includes.php');
require_once('../includes/xml.inc.php');

$file=new xmlFile();
$file->header("results");

if (strlen($motcle)<3) {
	echo "<error>1</error>";
}
else {
	//gets each word of the string
	$retItemName = array(); 
	$retItemTag = array();
	//contains the id of the widget found with name match to avoid dulicates with tag match
	$retNameId = array();
	$retNameIdGroup = array();
	$mot = explode(",",$motcle);
	for ($i=0;$i<sizeof($mot);$i++)
	{
		  if (strlen($mot[$i])>=3) {
			  if (sizeof($retItemName)==0) {
				  //dir_item name match
				  array_push($retItemName, " AND ( dir_item.name LIKE '%".$mot[$i]."%'");  
				  //dir_item.tag match
				  array_push($retItemTag, " AND ( label_simplified LIKE '%".$mot[$i]."%'");	
			  }
			  else {
				  //dir_item name match
				  array_push($retItemName, " OR dir_item.name LIKE '%".$mot[$i]."%'");  
				  //dir_item.tag match
				  array_push($retItemTag, " OR label_simplified LIKE '%".$mot[$i]."%'");	
			  }
		  }
	}	  
		
//if total length of the search > 3 but none of the tag's length are > 3 => exit
if (sizeof($retItemName)<1) {
	echo "<error>1</error>";
	exit(1);
}
								
	if ($page<1000)
	{
		//if security is managed at group level
		if (__useGroup && $_SESSION["user_id"])
		{		
			$reqGroupName = "SELECT DISTINCT dir_item.id, dir_item.name , dir_item.icon
							FROM search_index, search_keyword, dir_item, dir_cat_item, users_group_category_map, users_group_map 
							WHERE status = 'O'
							AND dir_item.id=search_index.item_id 
							AND search_keyword.id=kw_id 
							AND dir_item.id=dir_cat_item.item_id 
							AND dir_cat_item.category_id=users_group_category_map.category_id 
							AND users_group_category_map.group_id=users_group_map.group_id 
							AND users_group_map.user_id=".$_SESSION['user_id'];
							
			$reqGroupTag = $reqGroupName;
								
			//completes the query
			for ($j=0;$j<sizeof($retItemName);$j++)
			{	
				$reqGroupName.= $retItemName[$j];
			}
			$reqGroupName.= ')';
			
			$DB->getResults($reqGroupName);
			$nb_searchSecured1 = $DB->nbResults();
		
			if ($nb_searchSecured1>0) {
				while ($row = $DB->fetch(0))
				{
                    $itemicon=$row["icon"];
                    $itemid=$row["id"];
                    $itemname=$row["name"];
                    if ($itemicon=="") { $itemicon="box0_".$itemid; }
                    
					array_push($retNameIdGroup,$row["id"]);
					echo "<item>";
					echo "<id>".$itemid."</id>";
					echo "<name><![CDATA[".$itemname."]]></name>";
					echo "<secured>1</secured>";
					echo "<icon><![CDATA[".$itemicon."]]></icon>";
					echo "</item>";
				}
			}	
			$DB->freeResults();
		
			//if less than 10 results, search on tag's name
			if ($nb_searchSecured1<10) {
				for ($a=0;$a<sizeof($retItemTag);$a++)
				{	
					$reqGroupTag.= $retItemTag[$a];
				}
				$reqGroupTag.= ')';
				
				$DB->getResults($reqGroupTag);
				$nb_searchSecured2 = $DB->nbResults();
				
				if ($nb_searchSecured2>0) {
					while ($row = $DB->fetch(0))
					{
						//verify that if the widget already matches with name (to avoid double display)
						if (!in_array($row["id"], $retNameIdGroup)) {
                            $itemid=$row["id"];
                            $itemname=$row["name"];
                            $itemicon=$row["icon"];
                            if ($itemicon=="") { $itemicon="box0_".$itemid; }
                        
							echo "<item>";
							echo "<id>".$itemid."</id>";
							echo "<name><![CDATA[".$itemname."]]></name>";
							echo "<secured>0</secured>";
							echo "<icon><![CDATA[".$itemicon."]]></icon>";
							echo "</item>";
						}
					}
				}
				$DB->freeResults();	
			}
		$nb_searchSecured = $nb_searchSecured1 + $nb_searchSecured2;
		}
					
		$reqNoGroupName = " SELECT DISTINCT dir_item.id,dir_item.name, dir_item.icon
							FROM dir_item, dir_cat_item, dir_category
							WHERE STATUS = 'O'
							AND secured = 0
							AND dir_item.id = dir_cat_item.item_id
							AND dir_cat_item.category_id = dir_category.id";
							
		$reqNoGroupTag = "  SELECT DISTINCT dir_item.id,dir_item.name, dir_item.icon
							FROM search_index, search_keyword, dir_item, dir_cat_item, dir_category
							WHERE STATUS = 'O'
							AND secured = 0
							AND dir_item.id = dir_cat_item.item_id
							AND dir_cat_item.category_id = dir_category.id
							AND search_index.item_id = dir_item.id
							AND search_index.kw_id = search_keyword.id";

		for ($j=0;$j<sizeof($retItemName);$j++)
		{	
			$reqNoGroupName.= $retItemName[$j];
		}
		$reqNoGroupName.= ')';

		//search on widget's name
		$DB->getResults($reqNoGroupName);
		$nb_search = $DB->nbResults();
			
		//total NbResults (Group Name/Tag + Public Name)
		echo "<nbres1>".($nb_search+$nb_searchSecured)."</nbres1>";
		if ($nb_search>0) {
			while ($row = $DB->fetch(0))
			{
                $itemid=$row["id"];
                $itemname=$row["name"];
                $itemicon=$row["icon"];
                if ($itemicon=="") { $itemicon="box0_".$itemid; }
            
				array_push($retNameId,$row["id"]);
				echo "<item>";
				echo "<id>".$itemid."</id>";
				echo "<name><![CDATA[".$itemname."]]></name>";
				echo "<secured>0</secured>";
				echo "<icon><![CDATA[".$itemicon."]]></icon>";
				echo "</item>";
			}
		}
		$DB->freeResults();
		
		//if less than 10 results, search on widget's tag
		if ($nb_search<10)
		{
			for ($a=0;$a<sizeof($retItemTag);$a++)
			{	
				$reqNoGroupTag.= $retItemTag[$a];
			}
			$reqNoGroupTag.= ')';
			
			$DB->getResults($reqNoGroupTag);
			$nb_search2 = $DB->nbResults();
			
			if ($nb_search2>0) {
				while ($row = $DB->fetch(0))
				{
					if (!in_array($row["id"], $retNameId))
					{
                        $itemid=$row["id"];
                        $itemname=$row["name"];
                        $itemicon=$row["icon"];
                        if ($itemicon=="") { $itemicon="box0_".$itemid; }
                        
						echo "<item>";
						echo "<id>".$itemid."</id>";
						echo "<name><![CDATA[".$itemname."]]></name>";
						echo "<secured>0</secured>";
						echo "<icon><![CDATA[".$itemicon."]]></icon>";
						echo "</item>";
					}
				}
			}
			$DB->freeResults();
		}
	}
}
$file->footer("results");
$DB->close();
?>