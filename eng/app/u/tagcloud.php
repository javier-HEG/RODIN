<?php

/*
@wordCloud
Author: Derek Harvey
Website: www.lotsofcode.com

@Description
PHP Tag Cloud Class, a nice and simple way to create a php tag cloud, a database and non-database solution.
*/

class wordCloud
{
	var $wordsArray = array();

	/*
	* PHP 5 Constructor
	*
	* @param array $words
	* @return void
	*/

	function __construct($words = false)
	{
	if ($words !== false && is_array($words))
	{
	foreach ($words as $key => $value)
	{
	$this->addWord($value);
	}
	}
	}

	/*
	* PHP 4 Constructor
	*
	* @param array $words
	* @return void
	*/

	function wordCloud($words = false)
	{
	$this->__construct($words);
	}

	/*
	* Assign word to array
	*
	* @param string $word
	* @return string
	*/

	function addWord($word, $value = 1)
	{
	$word = strtolower($word);
	if (array_key_exists($word, $this->wordsArray))
	$this->wordsArray[$word] += $value;
	else
	$this->wordsArray[$word] = $value;

	return $this->wordsArray[$word];
	}

	/*
	* Shuffle associated names in array
	*/

	function shuffleCloud()
	{
		{
			$keys = array_keys($this->wordsArray);
		
			shuffle($keys);
		
			if (count($keys) && is_array($keys))
			{
			$tmpArray = $this->wordsArray;
			$this->wordsArray = array();
			foreach ($keys as $key => $value)
			$this->wordsArray[$value] = $tmpArray[$value];
			}
		}
	}

	/*
	* Calculate size of words array
	*/

	function getCloudSize()
	{
	return array_sum($this->wordsArray);
	}

	/*
	* Get the class range using a percentage
	*
	* @returns int $class
	*/

	function getClassFromPercent($percent)
	{
	if ($percent >= 99)
	$class = 1;
	else if ($percent >= 70)
	$class = 2;
	else if ($percent >= 60)
	$class = 3;
	else if ($percent >= 50)
	$class = 4;
	else if ($percent >= 40)
	$class = 5;
	else if ($percent >= 30)
	$class = 6;
	else if ($percent >= 20)
	$class = 7;
	else if ($percent >= 10)
	$class = 8;
	else if ($percent >= 5)
	$class = 9;
	else
	$class = 0;

	return $class;
	}

	/*
	* Create the HTML code for each word and apply font size.
	* For $returnType==href: starts RODIN search on tag!
	* @returns string $spans
	*/

	function showCloud($returnType = "html")
	#########################################
	{
    print "ddddd"; 
		global $USER_ID;
    if (!$USER_ID) $USER_ID=$_REQUEST['pid'];
    //print "USER_ID=$USER_ID";
		if($returnType == 'href')
		{
			include_once("FRIdbUtilities.php");
			global $USER_ID;
			$tab_id= fri_get_tab_id("UNIVERSE",$USER_ID);
			$page_info = fri_get_page_infos($tab_id);
		}
		if (is_array($this->wordsArray) && count($this->wordsArray))
		{
		$this->shuffleCloud();
		
		$this->max = max($this->wordsArray);
		}
			if ($returnType == "href_connected")
				$ret="<table cellspacing='0' cellpadding='0' border='0'><tr><td >";
			if (is_array($this->wordsArray) && count($this->wordsArray))
		{
			$ret.= ($returnType == "html" ? "" : ($returnType == "array" ? array() : "\n<form action=''>"));
			foreach ($this->wordsArray as $word => $popularity)
			{
				if (trim($word) <> '')
				{
					$sizeRange = $this->getClassFromPercent(($popularity / $this->max) * 100);
					if ($returnType == "array")
					{
						$ret[$word]['word'] = $word;
						$ret[$word]['sizeRange'] = $sizeRange;
						if ($currentColour)
							$ret[$word]['randomColour'] = $currentColour;
					}
					else if ($returnType == "html")
					{
						$ret .= "<span class='word size{$sizeRange}'> {$word} </span>";
												
					}
					
					################################
					################################
					else if ($returnType == "href")
					################################
					################################
					{
						$hreftitle="Search in current tab for \"$word\" again";
						$USER_ID = $_REQUEST['pid'];
						$m=10; //Default: 10 results each query
						$db_tab_id=fri_get_tab_id("UNIVERSE",$USER_ID); //print " tab_id($USER_ID): ($tab_id)";
						$ACTION=<<<EOA
	parent.\$p.app.tabs.open(0);
	fri_parallel_metasearch("$word",document.getElementById("rodinsearch_m").value,-1,-1,{$USER_ID},true,true,parent.\$p)
	$p.app.tabs.sel=0;
EOA;
						$ret .= "\n<a href='#' onclick='$ACTION' title='$hreftitle'><span class='word size{$sizeRange}'> $word </span></a>";
					}					
					################################
					################################
					else if ($returnType == "href_connected") //call from main page on top -> always the current tab
					################################
					################################
					{
						$word_stripped=stripslashes($word);
						$word_tag=cleanup4Tooltip($word);
						$hreftitle="Search in current tab for \"$word_tag\" again";
						$USER_ID = $_REQUEST['pid'];
						$m=10; //Default: 10 results each query
						$db_tab_id=fri_get_tab_id("UNIVERSE",$USER_ID); //print " tab_id($USER_ID): ($tab_id)";
						
						$ACTION=<<<EOA
var w=cleanupAJAXstring("$word_tag");
parent.bc_clearBreadcrumbIfNeeded(w);
parent.setMetaSearchInputText(w);
EOA;
//
// still some problems calling rodin directly from 
//
// fri_parallel_metasearch(w,-1,-1,-1,{$USER_ID},true,true,parent.\$p) 
//
						$COLOR=$this->toggle_tagCloudColor($COLOR);
						$ret .= "\n<a href='#' onclick='$ACTION' title='$hreftitle'><span class='word size{$sizeRange}'><font style=\"color:$COLOR;\"> $word_stripped </font></span></a>";
					}
				} // trim word
			} //foreach
			
			if ($returnType == "href_connected")
				$ret.="</td></tr></table>";
			
			if ($returnType == "href") $ret.="\n</form>";
			return $ret;
			}
		}
		
		
		function toggle_tagCloudColor($COLOR)
		{
			global $COLOR1_TAGCLOUD, $COLOR2_TAGCLOUD;
			$RET_COLOR=$COLOR1_TAGCLOUD;
			if ($COLOR == $COLOR1_TAGCLOUD)
				$RET_COLOR=$COLOR2_TAGCLOUD;
			else 
				$RET_COLOR=$COLOR1_TAGCLOUD;
			return $RET_COLOR;
		}
		
		
	} // class wordCloud

?>