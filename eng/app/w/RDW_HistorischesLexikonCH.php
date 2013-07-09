<?php
	#########################################################################
	#
	# WIDGET <Widgetname>
	# AUTHOR Fabio Ricci / HEG, Tel: 076-5281961 / fabio.fr.ricci@hesge.ch
	# DATE 	 1.12.2009
	#
	# PURPOSE: 	Visualize Results of HISTORISCHES LEXIKON DER SCHWEIZ
	#
	# HACKS
	# SPECIAL REMARKS
	#########################################################################


	// IMPORTANT!!! CHANGE URL transmission TO POST in order to hide infos !!!!



	##############################################
	#
	# Some preliminary stuff:
	# PHP includes:
	include_once("../u/RodinWidgetBase.php");
	include_once "$DOCROOT/$RODINUTILITIES_GEN_URL/simplehtmldom/simple_html_dom.php";
	#
	# AJAX includes:
	#
	# Put all the WIDGET SPECIFIC ajax code here (pls. rename):
	#
	# $MY_AJAX_FILE = make_ajax_widget_filename(/*overriding filename if needed*/);
	#
	##############################################

	// The following is the link to the resource:
	$url_basesegment="http://www.hls-dhs-dss.ch";
	##############################################


	##############################################
	##############################################
	#
	# This will print the html header with a Title
	#


	print_htmlheader("Historisches Lexikon der Schweiz");
	##############################################
	##############################################



	############################################################################################
	#
	# SPECIFY HTML SEARCH CONTROLS
	#
	# Add here all the specs for html control
	# to be used in the search
	# Follow the example and do not possibly
	# alter the DEFAULT elements
	#
	# add_search_control($name,$value,$defaultvalueQS,$htmldef,$pos)
	#
	#
	# REMARKS:
	# If you add new controls, then automatically
	# hidden fiels and php querystring $_REQUEST's
	# will be added in order to ensure the widget
	# gets this parameters during running
	#
	############################################################################################

	// add_search_control($nale,$leftlable, $rightlable, $defaultvalueQS,$htmldef,$pos)

	// QUERY TAG: q (DEFAULT)
	##############################################
	$title=lg("titleWidgetTypeSearch");
	
	if ($WANT_WIDGET_SEARCH)
	{
		$htmldef=<<<EOH
		<input class="localSearch" name="q" type="text" value="$qx" title='$title' onchange="$SEARCHSUBMITACTION">
EOH;
	add_search_control('q',$qx,'',$htmldef,2);
	##############################################


	// Number of results m (DEFAULT)
	##############################################
	$title=lg("titleWidgetMaxResults");
	$m=$_REQUEST['m']; if(!$m) $m=$DEFAULT_M;
	$htmldef=<<<EOH
		<input class="localMaxResults" name="m" type="text" value="$m" title='$title'/>
EOH;
	add_search_control('m',$m,20,$htmldef,2);
	}
	##############################################




	// Button ask (DEFAULT)
	##############################################
	$title=lg("titleWidgetButtonAsk");
	$label=lg("labelWidgetButtonAsk");
	$htmldef=<<<EOH
		<input name="ask" class="localSearchButton" type="button" onclick="$SEARCHSUBMITACTION" value="$label" title='$title'/>
EOH;
	add_search_control('ask','','',$htmldef,2);
	##############################################








	##############################################
	##############################################
	function DEFINITION_RDW_DISPLAYHEADER()
	##############################################
	##############################################
	#
	# Define the LOGO img to be displayed:
	#
	{
		$widget_icon_width=20;
		$widget_icon_height=20;
		/*
		$T=<<<EOT
		\n<img src="../images/zbz-logo.gif" width=$widget_icon_width height=$widget_icon_height border=0/>
EOT;
		print $T;
		*/
		//No problem here:
		return true;

	} // DEFINITION_RDW_DISPLAYHEADER
	##############################################







	##############################################
	##############################################
	function DEFINITION_RDW_DISPLAYSEARCHCONTROLS()
	##############################################
	##############################################
	{

		return true;

	}




	##############################################
	##############################################
	function DEFINITION_RDW_SEARCH_FILTER()
	##############################################
	##############################################
	{
		global $SEARCHFILTER_TEXT_SIZE;
		global $RODINUTILITIES_GEN_URL;
		global $FORMNAME;
		global $thisSCRIPT;
		global $RDW_REQUEST;
		# The followin globals here all registered querystring parameters:
		foreach ($RDW_REQUEST as $querystringparam => $d) eval( "global \${$querystringparam};" );
		##############################################
		$res=true;

		// ADD HERE ADDITIONAL STUFF TO BE EXECUTED DURING DISPLAYSEARCHCONTROLS
		// Remember to add "global" Statements to see some important vars
		// Returns true if you want to allow chaining / further computations


		$title="Select the kind of search you are interested in";

		//selection is done per injection at abstract widget level
		$htmldef=<<<EOH
		</tr><tr height=5></tr><tr>
		<td valign="top" align="right" > Searchtype:
		<select name="xs" size="1" title="Select the searchtype you are interested in">
			<option value="Articles" disabled title='Currently disabled'>articles</option>
			<option value="Fulltext" >fulltext</option>
		</select></td></tr>
EOH;
		add_searchfilter_control('xs','s',$xs,'$xs',$htmldef,1);


/*
fulltext title&text:
&searchcont=1

fulltext nur title:
&searchcont=2
*/

		//selection is done per injection at abstract widget level
		$htmldef=<<<EOH
		</tr><tr height=5></tr><tr>
		<td valign="top" align="right" > Search
		<select name="xsc" size="1" title="Select where to search for">
			<option value=1 >in title and in text</option>
			<option value=2 >only in title</option>
		</select></td></tr>
EOH;
		add_searchfilter_control('xsc','sc',$xd,'$xsc',$htmldef,2);




		$title="Show result Nr.";
		if (!$xc) $xc=1;
		$htmldef=<<<EOH
		</tr><tr height=5></tr><tr>
		<td valign="top" align="right" > Visualize results from record number:
			<input name="xc" type='text' size=1 title="$title" value='$xc' $STYLE>
		</td></tr>
EOH;
		$n= add_searchfilter_control('xc','c',$xc,'$xc',$htmldef,4);



		// SET DEFAULT PREFS IN DB
		// in  order to enable direct use of the widget
		// without setting any preference
		register_default_prefs("xs[]=Fulltext&xsc[]=1&xc=1");


		return $res;

	} // DEFINITION_RDW_DISPLAYSEARCHCONTROLS
	##############################################


	#The following tells the widget state machine to check
	#once for internet connection and warn if no one found
	#(timeout) before collecting results
	$NEED_PHP_INTERNET_ACCESS=true;
	$NEED_AJAX_INTERNET_ACCESS=false;



	##############################################
	##############################################
	function DEFINITION_RDW_COLLECTRESULTS($chaining_url='')
	##############################################
	##############################################
	{
		global $datadir;
		global $url_basesegment;
		global $datasource;
		global $REALNAME;
		global $RDW_REQUEST;
		foreach ($RDW_REQUEST as $querystringparam => $d) eval( "global \${$querystringparam};" );
		# The followin globals here all registered querystring parameters:
		foreach ($RDW_REQUEST as $querystringparam => $d) eval( "global \${$querystringparam};" );
		$res=true;

		foreach($REALNAME as $rodin_name=>$needed_name)
		{
			//print "<br>REALNAME:  $rodin_name=>$needed_name";
			if ("${$rodin_name}" <> '' && $rodin_name <> "xi") // only if value defined
				$FILTER_SECTION.="&$needed_name=${$rodin_name}";

				if ($rodin_name == 'xs')
				{
					$searchtype=strtolower($xs); // catch search filter value from RDW_REQUEST
					//print "<br> xs=$xs";
				}
		}		##############################################
		# post:
		# http://www.hls-dhs-dss.ch/index.php?
		#		 searchtype=articles|fulltext|nouveaux
		#		&searchstring=Tell
		#		&searchstart=1
		#		&dateletter=01/12/2009
		#		&searchft=simple
		#		&curlg=d
		#		&process=now
		#		&searchlang=d
		#

		//Setup http Post call
		$data=	"searchtype=$searchtype"
					."&searchstring=".urlencode($q)
					."&searchcont=$xsc"
					."&searchstart=$xc"
					."&dateletter="
					."&searchft=simple"
					."&curlg=d"
					."&process=now"
					."&searchlang=d";

		//print "<br>Calling ($url_basesegment/index.php) with posted data (<b>$data</b>)<br>";

		$cc = new cURL();
		$POST_RESULT = $cc->post("$url_basesegment/index.php",$data);

		//print "<br>POST_RESULT FOUND: (((".htmlentities($POST_RESULT).")))"; exit;

	 	$html = str_get_html($POST_RESULT);

		foreach( $html->find('div') as $DIV )
		{

			if ($DIV->id == 'result')
			{
				$DIVRESULT=$DIV;
				break;
			}
		}

		// $DIVRESULT liegt vor

		// Suche nach erster Div mit class=searchres
		// packe die Elemente in Array $DIV_SEARCHRES

		$i=0;
		if ($DIVRESULT) // Datensatz gefunden
		foreach ($DIVRESULT->find('div') as $DIV)
		{

			if ($DIV->class == 'searchres')
			{
				$DIV_SEARCHRES[]=$DIV;
				$i++;
				if ($i>=$m) break; //stop - not that many results needed
			}
		}

		$num_results=$i;
		//print "<br> $i results (m=$m)!! ";




		//Load into Struture for storing
		if ($num_results)
		{
			$sr = new SR;
				$searchid = new SEARCHID;
				$searchid->sid=$sid;
				$searchid->m=$num_results;
				$searchid->q=$q;
				$searchid->datasource=$datasource;
			$sr->searchid = $searchid;
			$sr->result   = array();
			$i=0;
		}

		if (count($DIV_SEARCHRES))
		foreach ($DIV_SEARCHRES as $SEARCHRES)
		{
			$x=0;
			// selektiere Titel und HREF

			$A = $SEARCHRES->find('a',0);
			$TITLE=htmlentities( $A->innertext );
			$URL=$url_basesegment."/".str_replace("')","",str_replace("javascript:OpenWindow('","",$A->href));
			//print "<br>TITLE: $TITLE";
			//print "<br>URL: $URL";


			$SEARCHDESC = $SEARCHRES->find('div[class=searchresdesc]',0);
			$INNERTEXT=$SEARCHDESC->plaintext;

			//print "<br>\nINNERTEXT: ((($INNERTEXT)))";

			if (preg_match("/(\d\d)\/(\d\d)\/(\d\d\d\d)/",$INNERTEXT,$PTS))
			{
				$DATE= $PTS[1].".".$PTS[2].".".$PTS[3];
				//print "<hr>DATE: $DATE";
			}

			// <span class="highlight">Mayer</span>
			$ABSTRACT=trim(substr($INNERTEXT,0,strlen($INNERTEXT))); // Date skippen

			$CLEANED_ABSTRACT=str_replace("'","",$ABSTRACT);
			$CLEANED_ABSTRACT=str_replace("\"","",$CLEANED_ABSTRACT);
			$CLEANED_ABSTRACT=str_replace("\n"," ",$CLEANED_ABSTRACT);

			//print "<br>\nSEARCHDESC  ABSTRACT: (((".$ABSTRACT.")))";
			//print "<br>\nSEARCHDESC  CLEANED_ABSTRACT: (((".$CLEANED_ABSTRACT.")))";
			$SEARCHRESPROP = $SEARCHRES->find('div[class=searchresprop]',0);
			$ZEICHEN=$SEARCHRESPROP->find('b',0)->innertext;
			$AUTOR=$SEARCHRESPROP->find('b',1)->innertext;

			//Subtrahiere noch von ABSTRACT die enthaltenen Zeichen und Autor
			$CLEANED_ABSTRACT=str_replace("Zeichen: $ZEICHEN"," ",$CLEANED_ABSTRACT);
			$CLEANED_ABSTRACT=str_replace("Autor: $AUTOR"," ",$CLEANED_ABSTRACT);
			$CLEANED_ABSTRACT=htmlentities($CLEANED_ABSTRACT);


			/*
			print "<br>  SEARCHRESPROP: (((".$SEARCHRESPROP.")))";
			print "<br>  CLEANED_ABSTRACT: (((".($CLEANED_ABSTRACT).")))";
			print "<br>  TITLE: (((".$TITLE.")))";
			print "<br>  ZEICHEN: (((".$ZEICHEN.")))";
			print "<br>  AUTOR: (((".$AUTOR.")))";
			print "<br>SEARCHRES : ".$SEARCHRES;
			*/

			$localresult = new RESULT;
			$localresult->xpointer="$i";
			$localresult->row[]= array('MainRes','string','','',false, 'HLCH','cr');
			$sr->result[]=$localresult;

			$localresult = new RESULT;
			$localresult->xpointer="$i.".$x++;
			$localresult->row[]= array('Title','string', $TITLE, ($URL),true, 'HLCH','cr');
			$sr->result[]=$localresult;

			$localresult = new RESULT;
			$localresult->xpointer="$i.".$x++;
			$localresult->row[]= array('Author','string', $AUTOR, ($URL),true, 'HLCH','cr');
			$sr->result[]=$localresult;
			$localresult = new RESULT;
			$localresult->xpointer="$i.".$x++;
			$localresult->row[]= array('Date','string', $DATE, '',true, 'HLCH','cr');
			$sr->result[]=$localresult;

			$localresult = new RESULT;
			$localresult->xpointer="$i.".$x++;
			$localresult->row[]= array('Reference','string', $ZEICHEN, '',true, 'HLCH','cr');
			$sr->result[]=$localresult;

			$localresult = new RESULT;
			$localresult->xpointer="$i.".$x++;
			$localresult->row[]= array('Abstract','string', $CLEANED_ABSTRACT, '',true, 'HLCH','cr');
			$sr->result[]=$localresult;

			$i++;
	} // foreach SEARCHRES
	//exit;

	return $sr;

	} // DEFINITION_RDW_COLLECTRESULTS()
	##############################################













	##############################################
	##############################################
	function DEFINITION_RDW_STORERESULTS()
	##############################################
	##############################################
	{

		// not used here
		return true;

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
		return $res;

	} // DEFINITION_RDW_SHOWRESULT_WIDGET()
	##############################################







	##############################################
	##############################################
	function DEFINITION_RDW_SHOWRESULT_FULL($w,$h)
	##############################################
	##############################################
	{
		global $sid;
		global $datasource;
    global $slrq;
		$res=true;

		// ADD HERE CODE TO RENDER THE STORED RESULTS in mode "RDW_full"
		// Remember to add "global" Statements to see some important vars
		// Returns true if you want to allow chaining / further computations

		render_widget_results($sid,$datasource,$slrq);


		return $res;


	} // DEFINITION_RDW_SHOWRESULT_WIDGET()
	##############################################




print <<<EOT
</body>
</html>
EOT;




	##################################################
	##################################################
	#
	# RUN WIDGET STATE MACHINE:
	#
	include_once("../u/RodinWidgetSMachine.php");
	##################################################
	##################################################
?>