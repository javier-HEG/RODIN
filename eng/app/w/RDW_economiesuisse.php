<?php
include_once("../u/RodinWidgetBase.php");

		##############################################
		##############################################
		print_htmlheader("ECONOMIESUISSE RODIN WIDGET");
		##############################################
		##############################################
	
	
		global $SEARCHSUBMITACTION;

$DELICIOUS_search_baseFEED="http://www.economiesuisse.ch/web/en/_layouts/srchrss.aspx";
//$DELICIOUS_search_baseFEED="http://www.economiesuisse.ch/web/de/seiten/Results.aspx";
$widget_icon_width=55;
$widget_icon_height=20;

		##############################################
		# HTML SEARCH CONTROLS:  
		##############################################

		// add_search_control($nale,$leftlable, $rightlable, $defaultvalueQS,$htmldef,$pos)

	
			
		// QUERY TAG: q (rodin internal query tag)
		##############################################
		$title=lg("titleWidgetTypeSearch");
if ($WANT_WIDGET_SEARCH)
{
	$htmldef=<<<EOH
			<input class="localSearch" name="q" type="text" value="$qx" title='$title' onchange="$SEARCHSUBMITACTION">
EOH;
		add_search_control('q',$qx,'$q',$htmldef,1);
		##############################################



		// Number of results m (default)
		##############################################
		$title=lg("titleWidgetMaxResults");
		$m=$_REQUEST['m']; if(!$m) $m=$DEFAULT_M;
		$htmldef=<<<EOH
			<input class="localMaxResults" name="m" type="text" value="$m" title='$title'/>
EOH;
		add_search_control('m',$m,20,$htmldef,1);
		##############################################
		
		
		// Button ask (default)
		##############################################
		$title=lg("titleWidgetButtonAsk");
		$label=lg("labelWidgetButtonAsk");
		$htmldef=<<<EOH
			<input name="ask" class="localSearchButton" type="button" onclick="$SEARCHSUBMITACTION" value="$label" title='$title'/>
EOH;
		add_search_control('ask','','',$htmldef,1);
	}
		##############################################
		





	##############################################
	##############################################
	function DEFINITION_RDW_SEARCH_FILTER()
	##############################################
	##############################################
	{	
		global $SEARCHFILTER_TEXT_SIZE;
	##############################################
	/*
		In case of rerodoc 
		filter operations are already
		coming inside the table 'focusonsearchbox'
		This table should be shown as is and then
		on click the cc parameter should be extracted.
		
		Here (demo) just a CLASS parameter

	##############################################
	*/
	
	
	##############################################
	#
	# Each filter param is prefixed by "x"
	# You have to provide a real name also
	##############################################
	# Site xcc (in rerodoc  real name: cc)
	# Please insert value=''
	#
	#
	##############################################
	
	// Define some style for controls
	global $_w;
	$w=$_w - 15; // fix width in accordance to Widget desired width
	  
	$PREFS_STYLE =<<<EOS
	style="min-width: {$w}px; max-width: {$w}px; width : {$w}px;";
EOS;

	
	$title="Insert here additional search words to be added to RODIN's search while performing your query";
	$xcc=$_REQUEST['xc'];
	if ($xcc=='')
		$xcc=$ANY;
	
	$htmldef=<<<EOH
	Additional search item:
	<input name="xc" type='text' size=$SEARCHFILTER_TEXT_SIZE title="$title" value='' $STYLE>		
EOH;

	$n= add_searchfilter_control('xc','c',$xc,"'$xc'",$htmldef,1);
	
	
	##############################################
	
	
	// SET DEFAULT PREFS IN DB
	// in  order to enable direct use of the widget
	// without setting any preference
	register_default_prefs("xc=");

	
	return true;	
		
}// DEFINITION_RDW_SEARCH_FILTER		
		 	

	


	
	
	
	
	##############################################
	##############################################
	function DEFINITION_RDW_DISPLAYHEADER()
	##############################################
	##############################################
	{
		//Widget Icon is displayed directly on title bar
		//Instead of name
		return true;
		
	} // DEFINITION_RDW_DISPLAYHEADER
	##############################################











	##############################################
	##############################################
	function DEFINITION_RDW_DISPLAYSEARCHCONTROLS()
	##############################################
	##############################################
	{
	
		$res=true;
		// ADD QUERY CONTROLS
		
		return $res;

	} // DEFINITION_RDW_DISPLAYSEARCHCONTROLS
	##############################################







	#The following tells the widget state machine to check 
	#once for internet connection and warn if no one found
	#(timeout) before collecting results
	$NEED_PHP_INTERNET_ACCESS=true;


	##############################################
	##############################################
	function DEFINITION_RDW_COLLECTRESULTS($chaining_url='')
	##############################################
	##############################################
	{	
		$res=true; // RSS FEDD TECHNIQUE

		##########################################
		#
		# Get all params global
		#
	
		global $datasource;
		global $DELICIOUS_search_baseFEED;
		global $REALNAME;
		global $DOCROOT;
		global $RODINUTILITIES_GEN_URL;
		global $RDW_REQUEST;
		global $RODINBASEDATADIR; // for testing from fixed file
		foreach ($RDW_REQUEST as $querystringparam => $d) eval( "global \${$querystringparam};" );
			
		include_once("$DOCROOT/$RODINUTILITIES_GEN_URL/magpierss-0.72/rss_fetch.inc"); //  = global "u"
		include_once "$DOCROOT/$RODINUTILITIES_GEN_URL/simplehtmldom/simple_html_dom.php";
		
		$EXTRAPARAMS_K='';
		foreach($REALNAME as $rodin_name=>$needed_name)
		{
			//print "<br>REALNAME:  $rodin_name=>$needed_name";
			
			if ($needed_name == 'c') //  print " YES cx:(${$rodin_name})"; // sonderfall c
					$EXTRAPARAMS_K="+${$rodin_name}";
			else
			if ("${$rodin_name}" <> '') // only if value defined
				$FILTER_SECTION.="&$needed_name=${$rodin_name}";
		}
		
		$res = true;
		$parameters="k=".urlblankencode(stripslashes($q))."$EXTRAPARAMS_K&count=$m" // no count restriction on economiesuisse!
					.$FILTER_SECTION
					;
					
		$feed="$DELICIOUS_search_baseFEED?$parameters";
		//print "<br>feed: $feed";
  		//$rss = fetch_rss( $feed );
	  	$rss = new fri_rss_reader($feed,$m);
		/*
	  	print "<br><br>Result (RSS): (((";
	  	var_dump($rss);
	  	print ")))<br><br><br>";
	  	exit;
	  	*/
	  	/*
	  	$rss = file_get_contents( $feed );
		$sx_rss = simplexml_load_string($rss);		
		if (!$sx_rss) {
				echo "<br>Problem loading XML ($rss) (exit)\n";
	
				foreach(libxml_get_errors() as $error) 
				{
						echo "\t", $error->message;
				}
				exit;
		}
	  	//echo "Channel Title: " . $rss->channel['title'] . "<p>";
	  	$namespaces = $sx_rss->getDocNamespaces(true);
		$namespaces[]=''; // even the empty space (for fulltext items)
		
		$cc=$sx_rss->children();
		foreach($cc as $a=>$v)
		{
			if ($a=='channel')	
			{
				$CHANNEL=$v[0];
				break;
			}
		}
		$cc=$CHANNEL->children();
		$rss_item=array();
		$i=0;
		foreach($cc as $a=>$v)
		{
			if ($a=='item')
			{
				$i++;
				if ($i>$m) {print "<br>$i >= $m Basta";break;}
				else
				{
					$item_attributes = array();
					foreach($v[0]->children() as $attr=>$val)
					{
						$item_attributes{$attr}=$val;
						//print "<br>local item $attr=>$val";
					}			
					$rss_item[]=$item_attributes;
				}
			}
	
		}		
		
		$n=count($rss_item);
		
	  	print "<br>CHANNEL:((($CHANNEL))) hast $n ITEMS!!!";
		foreach ($rss_item as $attributes)
		{
			print "<br>item ".$i++;
			foreach($attributes as $attr=>$val)
			print "<br>local item $attr=>$val"; 
			
		}
		*/
		
	  	
	  	
		$sr = new SR;
			$searchid = new SEARCHID;
			$searchid->sid=$sid;
			$searchid->m=$m; // not yet correct!
			$searchid->q=$q;
			$searchid->datasource=$datasource;
		$sr->searchid = $searchid;
		$sr->result   = array();
	
		//print "<br> Risultati:".$rss->n;
		
		
		
		if (count($rss->items)>0)
		{
					
			$i=0;
	
			foreach($rss->items as $item) //grasp one result set and store attributes
			{
				//print "<br>\n--------neues Item...";
				if ($i > $m - 1) break; // user specified $m -> MORE EFFICIENT: RESTRICT RESULTS FROM GET
				
				$fatherresult=$localresult = new RESULT;
				$localresult->xpointer=$i++;
				$localresult->row[]= array('','string','', '',false, '','cr'); // level 1 trenner
				$sr->result[]=$localresult;
				
				$j=0;			
				foreach($item as $attrn=>$attrv)
				{
					//print "<br>\n1: <b>$attrn</b>=>(($attrv))";
					
					$attrname=''; $attrvalue='';
					$j++;
					$node='rss-item';
					$localresult = new RESULT;
					$localresult->xpointer="$i.$j"; // following entries are one position indented INVERSE!!
					
					// special case (dc: or wfw: terms)
					if ($attrn=='dc' || $attrn=='wfw')
					{
						foreach($attrv as $attrname=>$attrvalue) ;
						//echo "\n<br>&nbsp;&nbsp;&nbsp;&nbsp;<b>$xx</b>=>$cc";
						// there is only one sub ele! 
					}
					else
					{
						$attrname=$attrn; $attrvalue=$attrv;
					}						
					
					
					if (preg_match("/summary/i",$attrname))
					{
						
						print "<hr>";
						
						print "<br> Summary: ".$attrvalue->asXML();
						print "<hr>";
						
						
							$htmls = str_get_html($attrvalue);
							$link= $htmls->find('a',0)->href;
							$summary= $htmls->find('span',1)->innertext;
							//print "<br>\nSUMMARY: ($summary) link=($link)";
							$attrvalue=$summary;		
					}
					else 
					if (preg_match("/description/",$attrname))
					{
						$XML_attrvalue = $attrvalue->asXML();
						if (preg_match("/CDATA/",$XML_attrvalue))
						{
							$xdesc = str_replace("<description><![CDATA[","",$XML_attrvalue);
							$xdesc = str_replace("]]>","",$xdesc);
						}
						$htmld = str_get_html($xdesc);
						$description=$htmld->find('span',1)->innertext;
						
							
						//print "<br>\nDESCRIPTION NODE: (".$xdesc.") link=($link)";						
						//print "<br>\nDESCRIPTION: ($description) link=($link)";						
						$attrvalue=$description;		
					}
	
					$attrvalue=ec_cleanup($attrvalue);
	
					//print "<br>\n2: <b>$attrname</b>=>(($attrvalue))";
	
					
					if (preg_match("/link/i",$attrname))
					{
						//print "<br> match link:   ".$attrvalue;
					
						$mainlink=cleanup_economySuisse_link($attrvalue);
						//use this url to enrich main result (level 1)
						$fatherresult->row[0][3]= $mainlink; // add url to unique main result
					}
	
					if (preg_match("/http/i",$attrvalue))
					{
					
						//print "<br> match http:   ".$attrvalue;
	
						if (!preg_match("/Postfach/",$attrvalue)) 
						// es kann naemlich ein fiesser EIntrag mit Firmeninfos auftreten, 
						//der ein unvolls. http AUsdruck hat, der hierein matcht !!!!!
							$link=cleanup_economySuisse_link($attrvalue);
					//use this url to enrich main result (level 1)
					}
					else $link='';
		
					
					//print "<br>STORE: $attrname=".$attrvalue;
					
					// insert data:
					if ($link<>'')
						$localresult->row[]= array($attrname,'url',($attrvalue), $link, true, $node,'cr');
					else if (preg_match("/date/i",$attrname))
						$localresult->row[]= array($attrname,'date',($attrvalue), '',true, $node,'cr');
					else
						$localresult->row[]= array($attrname,'string',html_entity_decode($attrvalue), '', true, $node,'cr');
					
					$link='';
					
					/*
					else
						$localresult->row[]= array($attrname,'string',$attrvalue, '',true, $node,'cr');
					*/
					$sr->result[]=$localresult;
				}
				$j++;
				$localresult = new RESULT;
				$localresult->xpointer="$i.$j"; // following entries are one position indented
	
	
				$sr->result[]=$localresult;
			}
			$searchid->m = $i; // Correct Number of results
			
		} // count > 0
	
		//exit;
		return $sr;
	
	} // DEFINITION_RDW_COLLECTRESULTS
	##############################################

	


  function ec_cleanup($str)
	#########################
	{
		$str=str_replace("?",'',$str);
		$str=str_replace("?\r\n",'',$str);
		$str=str_replace("\r\n",'',$str);
		$str=str_replace("  ",' ',$str);
		$str=strip_tags($str);
		$str=htmlentities($str);    
		return $str;
			
	}
	
	
	 


	function cleanup_economySuisse_link($link)
	{
		//print "<br>UNSAUBER: (($link))";

		if (preg_match("/(.*)\.(aspx|pdf)(.*)/",$link,$match))
		{
			$link=$match[1].".".$match[2];
			
			//print "<br>SAUBER: (($link))";
			
		}
	
		return str_replace(" ", "%20", $link); // weil namen auch blanks enhtalten k�nnen bei EconomySuisse
	
	}





	
	##############################################
	##############################################
	function DEFINITION_RDW_STORERESULTS()
	##############################################
	##############################################
	{
		
		return true; // nothing to do here
	
	} // DEFINITION_RDW_STORERESULTS











	##############################################
	##############################################
	function DEFINITION_RDW_SHOWRESULT_WIDGET($w,$h)
	##############################################
	##############################################
	{
		
		global $sid;
		global $datasource;
    global $slrq;
		global $render;
		
		render_widget_results($sid,$datasource,$slrq,RDW_widget,$render);

		
		return true; 
	
	} // DEFINITION_RDW_SHOWRESULT_WIDGET











	##############################################
	##############################################
	function DEFINITION_RDW_SHOWRESULT_FULL($w,$h)
	##############################################
	##############################################
	{
		global $sid;
		global $datasource;
		global $render;
    global $slrq;

		render_widget_results($sid,$datasource,$slrq,RDW_full,$render);
		return true; 
	
	} // DEFINITION_RDW_SHOWRESULT_WIDGET





##################################################
##################################################
# Decide what to run:
include_once("../u/RodinWidgetSMachine.php");
##################################################
##################################################

?>

