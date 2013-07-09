<?php
include_once("../u/RodinWidgetBase.php");
//include_once("$DOCROOT/JavaBridge/java/Java.inc");
		##############################################
		##############################################
		print_htmlheader("RERODOC RODIN WIDGET");
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

		// QUERY TAG: q (rodin internal query tag)
		##############################################
		$title=lg("titleWidgetTypeSearch");
		if ($WANT_WIDGET_SEARCH)
		{
			if (!$q) $q=$_REQUEST['qe'];
				$htmldef=<<<EOH
				<input class="localSearch" name="q" type="text" value="$qx" title='$title' onchange="$SEARCHSUBMITACTION">
EOH;
		add_search_control('q',$qx,'$q',$htmldef,2);
		##############################################



		// Number of results m (default)
		##############################################
		$title=lg("titleWidgetMaxResults");
		$m=$_REQUEST['m']; if(!$m) $m=$DEFAULT_M;
		$htmldef=<<<EOH
			<input class="localMaxResults" name="m" type="text" value="$m" title='$title'/>
EOH;
		add_search_control('m',$m,20,$htmldef,2);
		##############################################


		// Button ask (default)
		##############################################
		$title=lg("titleWidgetButtonAsk");
		$label=lg("labelWidgetButtonAsk");
		$htmldef=<<<EOH
			<input name="ask" class="localSearchButton" type="button" onclick="$SEARCHSUBMITACTION" value="$label" title='$title'/>
EOH;
		add_search_control('ask','','',$htmldef,2);
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

	// Defint some style for controls
	global $_w;
	$w=$_w - 15; // fix width in accordance to Widget desired width




	$title="Insert here the class name coming from the focusonsearchbox of doc.redo.ch";
	$xcc=$_REQUEST['xc'];
	if ($xcc=='')
		$xcc=$ANY;

	$htmldef=<<<EOH
	ReroDOC Class:
	<input name="xc" type='text' size=$SEARCHFILTER_TEXT_SIZE title="$title" value='' $STYLE>
EOH;

	$n= add_searchfilter_control('xc','c',$xc,"'$xc'",$htmldef,1);


	##############################################


		##############################################
		#
		# Show dublin core or marc ?
		#
		##############################################
		$title="Please select to personalize Widget output";
		$xp=$_REQUEST['xout'];
		$htmldef=<<<EOH
	<br>Please select to optimize output:<br>
    <input type="radio" name="xout" value="xmldc"> xml dublin core <br>
    <input type="radio" name="xout" value="xmlm"> xml marc <br>
    <input type="radio" name="xout" value="both"> Both

EOH;

		add_searchfilter_control('xout','out',$xout,'$xout',$htmldef,2);


		// SET DEFAULT PREFS IN DB
		// in  order to enable direct use of the widget
		// without setting any preference
		register_default_prefs("xc=&xout=xmldc");



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
	#(timeout)
	$NEED_PHP_INTERNET_ACCESS=true;



	##############################################
	##############################################
	function DEFINITION_RDW_COLLECTRESULTS($chaining_url='')
	##############################################
	##############################################
	{


		$res=true;
		$debug=false;


		##########################################
		#
		# Info should be collected with rich xml marc
		# and translated to dublin core (more readable)
		#
		##########################################
		#
		# Get all params global
		#
		global $datasource;
		global $searchsource_baseurl;
		global $REALNAME;
		global $RDW_REQUEST;
		global $MARC_TO_DC_UNQUALIFIED_XSL;
		global $q;
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

 		//print "<br>q=".$_GET['qe'];

		$parameters="p=".urlencode($q)
					."&rg=$m"
					.$FILTER_SECTION
					;

		$urldc="$searchsource_baseurl?$parameters"."&of=xd"; // xml dublin core
		//print "<br>urldc: $urldc";

		$urlxm="$searchsource_baseurl?$parameters"."&of=xm"; // xml mark (more details)
		//print "<br>urlxm: $urlxm";


		if ($xout=='xmldc')
		{
			$show_dc=true;
			$show_m=false;
		}
		else
		if ($xout=='xmlm')
		{
			$want_mark=1;

			$show_dc=false;
			$show_m=true;
		}
		else
		{
			$show_dc=true;
			$show_m=true;
		}

		###################################################
		# TRANSFORM FROM MARC TO DUBLINCORE UNQUALIFIED:

		$sXmlFile = $urlxm; // The xml file
		$sXslFile = $MARC_TO_DC_UNQUALIFIED_XSL; // The xsl file

//		$filename="/Users/vicho/Desktop/debugging.txt";
//		$h=fopen($filename,"w");
//		fwrite($h, "RDW_rerodoc.php ::: original XML URL : $sXmlFile \n");
//		fwrite($h, "RDW_rerodoc.php ::: XSL file : $sXslFile \n");
//		if (file_exists($sXslFile)) {
//			fwrite($h, "FRIutilities.php ::: XSL file exists. \n");
//		} else {
//			fwrite($h, "FRIutilities.php ::: XSL file can't be found [$sXslFile]. \n");
//		}
//		fclose($h);

		$debug=0;

		$xmlString = get_file_content($sXmlFile);

		$xml = xsl_local_transform($xmlString,$sXslFile);

		if ($xml)
		{
			$sxml= new SimpleXMLElement($xml);

			$xml_marc = get_file_content($urlxm);


			//print "<hr>xml_marc:<br>(((".urlencode($xml_marc).")))";


			$sxmlm= new SimpleXMLElement($xml_marc);

			$namespaces = $sxml->getDocNamespaces(true);
			$namespaces[]=''; // even the empty space (for fulltext items)

			//STORE the mark records for use inside the dublin core records
			foreach($sxmlm->record as $MARC) //grasp one result set and store arrtibutes
				$MARCRECORDS[]=&$MARC;


			//$namespace=$namespaces{'rdf'};
			$sr = new SR;
				$searchid = new SEARCHID;
				$searchid->sid=$sid;
				$searchid->m=$m; // not yet correct!
				$searchid->q=$q;
				$searchid->datasource=$datasource;
			$sr->searchid = $searchid;
			$sr->result   = array();

			if (count($sxml->children($namespaces{'rdf'}))>0)
			{

					$i=0;

				foreach($sxml->children($namespaces{'rdf'}) as $DC) //grasp one result set and store arrtibutes
				{
					//print "<br> neues DC...: ";
					if ($i > $m - 1) break; // user specified $m -> MORE EFFICIENT: RESTRICT RESULTS FROM GET


					$fatherresult=$localresult = new RESULT;
					$localresult->xpointer=$i++;
					$localresult->row[]= array('','string','', '',false && $show_dc, '','cr'); // level 1 trenner
					$sr->result[]=$localresult;

					$j=0;
					//at this point only without rdf namespace:
					//foreach ($namespaces as $prefix=>$namespace)

					foreach($DC->children($namespaces{'dc'}) as $attrname=>$attrvalue)
					{
						//print "<br> <b>: ($prefix::$namespace) $attrname</b>=>$attrvalue";

						$j++;
						$node='dc-record';
						$localresult = new RESULT;
						$localresult->xpointer="$i.$j"; // following entries are one position indented INVERSE!!


						if (preg_match("/http:/",$attrvalue))
						{
							$localresult->row[]= array($attrname,'string',$attrvalue, $attrvalue, true && $show_dc, $node,'cr');

							//use this url to enrich main result (level 1)
							$fatherresult->row[0][3]=$attrvalue; // add url to unique main result
						}
						else if (preg_match("/date/",$attrname))
							$localresult->row[]= array($attrname,'date',$attrvalue, '',true && $show_dc, $node,'cr');
						else
							$localresult->row[]= array($attrname,'string',$attrvalue, '',true && $show_dc, $node,'cr');

						$sr->result[]=$localresult;
					}
					$j++;
					$localresult = new RESULT;
					$localresult->xpointer="$i.$j"; // following entries are one position indented


					$sr->result[]=$localresult;


					######################
					// PROCESS MARC PART
					######################
					if ($want_mark)
					{
						$MARC = $MARCRECORDS[$i - 1];

						$j=0;
						foreach($MARC as $fieldname=>$fieldvalue)
						{
							$j++;

							$fieldparams = get_xml_params($fieldvalue->asXML());
							/*
							print "\n<br> mark <b>$fieldname</b> fieldvalue params:"
									." tag=".$fieldparams['tag']
									." ind1=".$fieldparams['ind1']
									." ind2=".$fieldparams['ind2']
									." Subfields:";
							*/
							$tag=$fieldparams['tag'];
							$ind1=$fieldparams['ind1'];
							$ind2=$fieldparams['ind2'];
							if ($ind2=="><subfield") $ind2=''; // smal bug but notime

							$name="field_$tag";
							$value="ind1=$ind1, ind2=$ind2"; // test

							$node=$tag;
							$localresult = new RESULT;
							$localresult->xpointer="$i.$j"; // following entries are one position indented INVERSE!!
							$localresult->row[]= array($name,'string',$value, '',true && $show_m, $node,'cr');
							$sr->result[]=$localresult;

							// show only subfield (default)

							$CC = $fieldvalue->children();
							if (count($CC) > 0)
							{

								$k=0;
								foreach($CC as $subfieldname=>$subfieldvalue)
								{
									$k++;
									$subfieldparams = get_xml_params($subfieldvalue->asXML());
									//print "\n\t<br> $subfieldname (code=".$subfieldparams['code'].")=>$subfieldvalue";

									$subfield_code=$subfieldparams['code'];
									$localresult = new RESULT;
									$localresult->xpointer="$i.$j.$k"; // following entries are one position indented INVERSE!!
									$name="subfield_$subfield_code";
									$value=$subfieldvalue;


									if (preg_match("/http:/",$value))
									{
										$localresult->row[]= array($name,'string',$value, $value, true && $show_m, $node,'cr');

										//use this url to enrich main result (level 1)
										//$fatherresult->row[0][3]=$value; // add url to unique main result
									}
									else if (preg_match("/date/",$name))
										$localresult->row[]= array($name,'date',$value, '',true && $show_m, $node,'cr');
									else
										$localresult->row[]= array($name,'string',$value, '',true && $show_m, $node,'cr');

									$sr->result[]=$localresult;
								}
							} // $CC = $fieldvalue->children();
						} // MARC
					} // $want_mark

				} // each $sxml->dc

				$searchid->m = $i; // Coorect Number of results

			} // count > 0

		} // xml
		else $sr=null;



		return $sr;

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
		global $datasource;
		global $render;
    global $slrq;


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
    global $slrq;
		global $render;

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

