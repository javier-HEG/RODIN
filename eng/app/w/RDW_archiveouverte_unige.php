<?php
include_once("../u/RodinWidgetBase.php");

		##############################################
		##############################################
		print_htmlheader("ARCHIVEOUVERTE-UNIGE RODIN WIDGET");
		##############################################
		##############################################
	
	
		global $SEARCHSUBMITACTION;
/*
The URL provided in your message may be reduced to the following:
http://doc.rero.ch/search?c=GENEVE&p=Economy&rg=10&of=xm
*/		
$searchsource_baseurl="http://doc.rero.ch/search";		
$widget_icon_width=55;
$widget_icon_height=20;

		##############################################
		# HTML SEARCH CONTROLS:  
		##############################################

		// add_search_control($nale,$leftlable, $rightlable, $defaultvalueQS,$htmldef,$pos)

print "<br> WIDGET IN BAU ...";
	
			
		// QUERY TAG: q (rodin internal query tag)
		##############################################
		$title=lg("titleWidgetTypeSearch");
		$q=$_REQUEST['q'];
		if (!$q) $q=$_REQUEST['qe'];
			$htmldef=<<<EOH
			<input class="localSearch" name="q" type="text" value="$q" title='$title' onchange="$SEARCHSUBMITACTION">
EOH;
		add_search_control('q',$q,'$q',$htmldef,1);
		##############################################



		// Number of results m (default)
		##############################################
		$title=lg("titleWidgetMaxResults");
		$m=$_REQUEST['m']; if(!$m) $m=20;
		$htmldef=<<<EOH
			<input class="localMaxResults" name="m" type="text" value="$m" title='$title'/>
EOH;
		add_search_control('m',$m,20,$htmldef,1);
		##############################################
		
		if (0)
		{
		// Button ask (default)
		##############################################
		$title=lg("titleWidgetButtonAsk");
		$label=lg("labelWidgetButtonAsk");
		$htmldef=<<<EOH
			<input name="ask" class="localSearchButton" type="button" onclick="$SEARCHSUBMITACTION" value="$label" title='$title'/>
EOH;
		add_search_control('ask','','',$htmldef,1);
		##############################################
		}





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
	
	// Defint some style for controls
	global $_w;
	$w=$_w - 15; // fix width in accordance to Widget desired width
	  
	$PREFS_STYLE =<<<EOS
	style="min-width: {$w}px; max-width: {$w}px; width : {$w}px;";
EOS;

	
	$title="Insert here the class name coming from the focusonsearchbox of doc.redo.ch";
	$xcc=$_REQUEST['xcc'];
	if ($xcc=='')
		$xcc=$ANY;
	
	$htmldef=<<<EOH
	ReroDOC Class:
	<input name="xcc" type='text' size=$SEARCHFILTER_TEXT_SIZE title="$title" value='' $STYLE>		
EOH;

	$n= add_searchfilter_control('xcc','cc',$xcc,"'$xcc'",$htmldef,1);
	
	
	##############################################
	
	
	
	
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










	##############################################
	##############################################
	function DEFINITION_RDW_COLLECTRESULTS($chaining_url='')
	##############################################
	##############################################
	{	
		$res=true;

		##########################################
		#
		# Get all params global
		#
		global $datasource;
		global $searchsource_baseurl;
		global $REALNAME;
		global $RDW_REQUEST;
		foreach ($RDW_REQUEST as $querystringparam => $d) eval( "global \${$querystringparam};" );
		
		foreach($REALNAME as $rodin_name=>$needed_name)
		{
			//print "<br>REALNAME:  $rodin_name=>$needed_name";
			
			if ("${$rodin_name}" <> '') // only if value defined
				$FILTER_SECTION.="&$needed_name=${$rodin_name}";
		}
		
		$res = true;
		
		//print "<br>XXXX FILTER: $FILTER_SECTION";
		
		//http://doc.rero.ch/search?c=GENEVE&p=Economy&rg=10&of=xd (XML DUBLIN CORE)
 
		$parameters="p=$q"
					."&rg=$m"
					.$FILTER_SECTION
					."&of=xd";
					
		$url="$searchsource_baseurl?$parameters";
		//print "<br>url: $url";
	
		$xml = get_file_content($url);


$XMLNS1=<<<EOX
xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
EOX;



$XMLNS2=<<<EOX
xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd"
EOX;


		// WORKAROUND: Cleanup rdf & ns info (not compat with simplexml)
		$xml=str_replace("<dc:","<",$xml);
		$xml=str_replace("/dc:","/",$xml);
		
		$xml=str_replace($XMLNS1,"",$xml);
		$xml=str_replace($XMLNS2,"",$xml);
		
		
		
		$sxml= new SimpleXMLElement($xml);


	
		//Test: parse dublin core simple:
		foreach ($sxml->dc as $DC)
		{
			foreach($DC as $attrname=>$attrvalue)
			{
			
				//print "<br>$attrname=>$attrvalue";
			
			}	
			
		}
		//process results so, that the are storable in rodin

		if (count($sxml->dc)>0)
		{
				
				$sr = new SR;
					$searchid = new SEARCHID;
					$searchid->sid=$sid;
					$searchid->m=$m; // not yet correct!
					$searchid->q=$q;
					$searchid->datasource=$datasource;
				$sr->searchid = $searchid;
				$sr->result   = array();
				$i=0;

			foreach($sxml->dc as $DC) //grasp one result set and store arrtibutes
			{
				//print "<br> neues DC...";
				if ($i > $m - 1) break; // user specified $m -> MORE EFFICIENT: RESTRICT RESULTS FROM GET
				

				$fatherresult=$localresult = new RESULT;
				$localresult->xpointer=$i++;
				$localresult->row[]= array('','string','', '',false, '','cr'); // level 1 trenner
				$sr->result[]=$localresult;
				
				$j=0;			
				foreach($DC as $attrname=>$attrvalue)
				{
					//print "<br> <b>$attrname</b>=>$attrvalue";

					$j++;
					$node='record';
					$localresult = new RESULT;
					$localresult->xpointer="$i.$j"; // following entries are one position indented INVERSE!!
					
				
					if (preg_match("/http:/",$attrvalue))
					{
						$localresult->row[]= array($attrname,'string',$attrvalue, $attrvalue, true, $node,'cr');
					
						//use this url to enrich main result (level 1)
						$fatherresult->row[0][3]=$attrvalue; // add url to unique main result
					}
					else if (preg_match("/date/",$attrname))
						$localresult->row[]= array($attrname,'date',$attrvalue, '',true, $node,'cr');
					else
						$localresult->row[]= array($attrname,'string',$attrvalue, '',true, $node,'cr');
	
					$sr->result[]=$localresult;
				}
				$j++;
				$localresult = new RESULT;
				$localresult->xpointer="$i.$j"; // following entries are one position indented


				$sr->result[]=$localresult;
			}
		} // count > 0

	
	
		$res= store_widget_results($sr);
		return $res;

} // DEFINITION_RDW_COLLECTRESULTS
	##############################################

	






	
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
		global $slrq;
		global $datasource;
		

		render_widget_results($sid,$datasource,$slrq);

		
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
    global $slrq;
		
		render_widget_results($sid,$datasource,$slrq,RDW_full);
		return true; 
	
	} // DEFINITION_RDW_SHOWRESULT_WIDGET





##################################################
##################################################
# Decide what to run:
include_once("../u/RodinWidgetSMachine.php");
##################################################
##################################################

?>

