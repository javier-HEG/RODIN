<?php
	#############################################
	#
	# Fabio Ricci (fabio.ricci@semweb.ch) for HEG
	# WARNING - this search is obsolete
	# it should be replaced by SOLR
	#
	$DEBUG=2;
	include_once("FRIutilities.php");
	
	//z.b. ?q=blabla&m=10&source=			


	##########################################
	#
	# Returns max m xml results inside an xml 
	#

	$EMPTYCOLLECTION="<empty></empty>";


	$source=$_REQUEST['source']; 
	$q=($_REQUEST['q']); 
	$m=$_REQUEST['m']; if (!$m) $m=10;
	$h=$_REQUEST['h']; // how: may contains 'and' or 'or'
	if (!$h) $h='or';
	$inattr=$_REQUEST['inattr']; 
	
	//extract info for node=creator and param=institute:
	$institutes=$_REQUEST['creator_par_institute']; 
	
	if ($institutes)
	$creator_par_institute=explode(',',$institutes); 
	
	//print "creator_par_institute=($creator_par_institute)<br>";
	
	
	$uri="$RODINDATAURI/$source";
  $url="$WEBROOT$uri";

	//print "<br>suche $m Ergebnisse nach $q in $url";

		
	$data_to_send="";
	
	
	//$C = getcontent_authenticated($url);	
	//if ($DEBUG) print "<br> xmlsearc(): get content $url";
	$XMLcontent =	file_get_contents($url,false);
	//if ($DEBUG) print "<br>XMICONTENT: ((($XMLcontent)))";
	
	
	if ($XMLcontent<>'')
	{	
		$sxml = simplexml_load_string($XMLcontent);
		$namespaces = $sxml->getDocNamespaces(true);
		$namespaces[]=''; // even the empty space (for fulltext items)
		//if ($DEBUG) var_dump($XMLcontent);
		
		$cntres=0;
		$qq = explode(' ',($q)); // in case there were several token in query
	
		//search for q inside 
	
		// find out the uppermost result tag inside the collection...
		// instead of publication
		
		$firsttagname	=sxml_get_toptagname($sxml);
		$resulttagname	=sxml_get_secondtagname($sxml);
	
		$candidateresults = $sxml->xpath("/$firsttagname/$resulttagname"); // all objects with this xpaths
		//var_dump($publications);
		
		$cntitems=count($candidateresults);
		
		//print "There are $cntitems Elements and q=$q<br>";
	} 
	$cnt_candidateresults=0;
	foreach( $candidateresults as $sxmlele )
	{			
		//if ($DEBUG) print "<br>$cnt_candidateresults $h($q)";
		$selected=false;
		$cnt_candidateresults++;
		if ($cntres == $m) break;
		else
		{
			//print "<hr>bearbeite Result nr. $cnt_candidateresults";
			$xmlele=$sxmlele[0]->asXML();
			//if ($DEBUG) print "<br>xmlele= ($xmlele)";
			
			$found=false;
			$selected=true;	// suppose	
			#########################################################################					
			//match institute in creator parameter!
			// Find a creator in institute:
			if ($institutes)
			{
				$selected=false;
				foreach ($namespaces as $prefix=>$namespace)
				foreach( $CCC= $sxmlele->children($namespace) as $attrname=>$attrvalue)
				{
					if ($attrname=='creator')
					{
						$creator_params = get_xml_params($attrvalue->asXML());
						if ($creator_params{'institute'})
						foreach($creator_par_institute as $institute_name)
						{
							//print "<br> check param (".$creator_params['institute'].") with institute ($institute_name) ";
						
							if ($creator_params['institute'] == $institute_name)
							{
								//print "<br><br><br> YES $institute_name at $cnt_candidateresults !!! because of ".$creator_params['value']."<br><br><br>";
								$selected = true;
								break;
							}
						}
						if ($selected) break;
					}
					if ($selected) break;
				}		
			} // institutes
			#########################################################################					
				
				
			//if ($selected)
			{
	
				if ($h == 'or')
				{	
					foreach($qq as $token)
					// match tokens in qq
					{
						//print "<br>Teste: $token im $cnt_candidateresults Element ... ";
						if (preg_match("/$token/i",$xmlele))
						{
							$cntres++;
							$RESULT[]=$xmlele;
							//print "<br><b>or FOUND $token</b> im $cnt_candidateresults Result !<br>";
							$found=true;
						} // match!
						if ($found) break;
					}// cc
				}
				else if ($h == 'and')
				{
					$and=true;
					foreach($qq as $token)
					// match all tokens in qq
					{
						if (!$and) break;
						else
						{
							$res = preg_match("/$token/i",$xmlele);
							$and = $and && $res;
						}
					
					}// cc
					if ($and) 
					{
						//print "<b>and FOUND</b> im $cnt_candidateresults Result !";
						$RESULT[]=$xmlele;
						$cntres++;
					}
				} // for qq
				
			} // selected
			
		} // $m reached
	} // for

	

header ("content-type: text/xml");
print  "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
			."<!--RODIN XML Search (HEG)-->"
			."<!--fabio.ricci@semweb.ch -->"
			."<!--$cntres Results to Query: $h($q) i=$institutes -->"
			."<$firsttagname>"
			;
	
	if ($RESULT)
	foreach ( $RESULT as $XMLR )
	{
	
		print "\n  ".$XMLR;
	}
	print "\n</$firsttagname>";
	
	
	
	
	#####################################
	
	

	
?>