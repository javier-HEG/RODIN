<?php


	#########################################################################
	#
	# WIDGET <Widgetname>
	# AUTHOR Fabio Ricci / HEG, Tel: 076-5281961 / fabio.fr.ricci@hesge.ch
	# DATE 	 10.08.2009
	#
	# PURPOSE: 
	#
	#
	# HACKS
	# SPECIAL REMARKS
	#########################################################################
	

	// IMPORTANT!!! CHANGE URL transmission TO POST in order to hide infos !!!!
	
	

	##############################################
	#
	# Some preliminary stuff: 
	#
	include_once("../u/RodinWidgetBase.php");
	##############################################
	

	##############################################
	##############################################
	#
	# This will print the html header with a Title
	#
	print_htmlheader("GOOGLEBOOKS RODIN WIDGET");
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
	$q=$_REQUEST['q'];
	$htmldef=<<<EOH
		<input class="localSearch" name="q" type="text" value="$q" title='$title' onchange="$SEARCHSUBMITACTION">
EOH;
	add_search_control('q',$q,'',$htmldef,2);
	##############################################


	// Number of results m (DEFAULT)
	##############################################
	$title=lg("titleWidgetMaxResults");
	$m=$_REQUEST['m']; if(!$m) $m=$DEFAULT_M;
	$htmldef=<<<EOH
		<input class="localMaxResults" name="m" type="text" value="$m" title='$title'/>
EOH;
	add_search_control('m',$m,20,$htmldef,2);
	##############################################
	
	
	// Number of results m (DEFAULT)
	##############################################
	$title="Select if you want to search only full books";
	$m=$_REQUEST['f'];
	$htmldef=<<<EOH
		<input type="checkbox" name="f" $CHECKED_f value="$f" title="$title"> 
EOH;
	add_search_control('f',$f,'',$htmldef,2);
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



	####################################################

	##############################################
	##############################################
	function DEFINITION_RDW_DISPLAYHEADER()
	##############################################
	##############################################
	#
	# Define the LOGO img to be displayed:
	#
	{
		global $RODINUTILITIES_GEN_URL;
		$widget_icon_width=50;
		$widget_icon_height=20;
		
		$T=<<<EOT
		\n<img src="$RODINUTILITIES_GEN_URL/books_sm2.gif" width=$widget_icon_width height=$widget_icon_height border=0/>
EOT;
		print $T;
		
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
		global $RDW_REQUEST;
		# The followin globals here all registered querystring parameters:
		foreach ($RDW_REQUEST as $querystringparam => $d) eval( "global \${$querystringparam};" );
		$res=true;
		
		// ADD HERE ADDITIONAL STUFF TO BE EXECUTED DURING DISPLAYSEARCHCONTROLS
		// Remember to add "global" Statements to see some important vars
		// Returns true if you want to allow chaining / further computations    
		
		return $res;

	} // DEFINITION_RDW_DISPLAYSEARCHCONTROLS
	##############################################


	##############################################
	##############################################
	function DEFINITION_RDW_SEARCH_FILTER()
	##############################################
	##############################################
	{
	 	
		// Type of resource searched
		$title = lg("titleBibsonomyResourceType");
		$xr = $_REQUEST['xr'];

		if ($xr=='') {
			$xr='bookmark';
		}

		if ($xr=='bookmark') {
			$BOOCKMARK_SEL=' checked ';
		} else if ($xr=='bibtex') {
			$BIBTEX_SEL=' checked ';
		}

		$htmldef=<<<EOH
		Resource:</td><td>
		<select name="xr" size="1" title="$title" >
			<option value="bookmark" $BOOCKMARK_SEL>Bookmark</option>
			<option value="bibtex" $BIBTEX_SEL>Bibtex</option>
		</select>
EOH;

		add_searchfilter_control('xr','r',$xr,'$xr',$htmldef,1);

		// BibSonomy username
		$title = lg("titleBibsonomyUserRestrict");
		$xu = $_REQUEST['xu'];
		$htmldef=<<<EOH
		User:</td><td> <input name="xu" type="text" value="" title="$title" style="width: {$w}px;" />
EOH;

		add_searchfilter_control('xu','u',$xu,'$xu',$htmldef,2);
	
		// EXAMPLES OF OTHER KINDS OF HANDLET FILTER CONTROLS
		
		// TEST CHECKBOX  
		$title = "Please type a username only if you want to restrict the search";
		$xz = $_REQUEST['xz'];
		$htmldef = <<<EOH
			<p>
				<input type="checkbox" name="xz" value="salami"> Salami<br>
    			<input type="checkbox" name="xz" value="pilze"> Pilze<br>
    			<input type="checkbox" name="xz" value="sardellen"> Sardellen
			</p>
EOH;

		add_searchfilter_control('xz','z',$xz,'$xz',$htmldef,2);
			
		// TEST DARIO BBUTTON 
		$title = "Please type a username only if you want to restrict the search";
		$xe = $_REQUEST['xe'];
		$htmldef = <<<EOH
			<p>
   	 			<input type="radio" name="xe" value="Mastercard"> Mastercard<br>
   	 			<input type="radio" name="xe" value="Visa"> Visa<br>
    			<input type="radio" name="xe" value="AmericanExpress"> American Express
			</p>
EOH;

		add_searchfilter_control('xe','e',$xe,'$xe',$htmldef,2);
		
		// TEXT AREA
		$title="Please type a username only if you want to restrict the search";
		$xa=$_REQUEST['xa'];
		$htmldef=<<<EOH
			<p>Welche HTML-Elemente fallen Ihnen ein, und was bewirken sie:<br>
    			<textarea name="xa" cols="50" rows="10"></textarea>
  			</p>
EOH;

		add_searchfilter_control('xa','a',$xu,'$xa',$htmldef,2);
	
		
		// SET DEFAULT PREFS IN DB
		// in  order to enable direct use of the widget
		// without setting any preference
		register_default_prefs("xr=bookmark&xu=");
		
		return true; // dont forget

	}	



	##############################################
	##############################################
	function DEFINITION_RDW_COLLECTRESULTS($chaining_url='')
	##############################################
	##############################################
	{
		global $RDW_REQUEST;
		# The followin globals here all registered querystring parameters:
		foreach ($RDW_REQUEST as $querystringparam => $d) eval( "global \${$querystringparam};" );
		$res=true;
		
		// ADD HERE HOW THE WIDGET COLLECT RESULTS TO BE STORED IN DB
		// Remember to add "global" Statements to see some important vars
		// Returns true if you want to allow chaining / further computations    
		
		return $res;

	} // DEFINITION_RDW_COLLECTRESULTS()
	##############################################



	##############################################
	##############################################
	function DEFINITION_RDW_STORERESULTS()
	##############################################
	##############################################
	{
		global $RDW_REQUEST;
		# The followin globals here all registered querystring parameters:
		foreach ($RDW_REQUEST as $querystringparam => $d) eval( "global \${$querystringparam};" );
		$res=true;
		
		// ADD HERE CODE TO STORE IN DB THE COLLECTED RESULTS
		// Remember to add "global" Statements to see some important vars
		// Returns true if you want to allow chaining / further computations    
		
		return $res;

	} // DEFINITION_RDW_STORERESULTS







	##############################################
	##############################################
	function DEFINITION_RDW_SHOWRESULT_WIDGET($w,$h)
	##############################################
	##############################################
	{
		global $RDW_REQUEST;
		# The followin globals here all registered querystring parameters:
		foreach ($RDW_REQUEST as $querystringparam => $d) eval( "global \${$querystringparam};" );
		$res=true;
		
		// ADD HERE CODE TO RENDER THE STORED RESULTS in mode "RDW_widget"
		// Remember to add "global" Statements to see some important vars
		// Returns true if you want to allow chaining / further computations    

		// render_googlebooksresults(RDW_widget);

		
		return $res;
		
	} // DEFINITION_RDW_SHOWRESULT_WIDGET()
	##############################################
	
	
	
	
	
	
	
	##############################################
	##############################################
	function DEFINITION_RDW_SHOWRESULT_FULL($w,$h)
	##############################################
	##############################################
	{
		global $RDW_REQUEST;
		# The followin globals here all registered querystring parameters:
		foreach ($RDW_REQUEST as $querystringparam => $d) eval( "global \${$querystringparam};" );
		$res=true;
		
		// ADD HERE CODE TO RENDER THE STORED RESULTS in mode "RDW_full"
		// Remember to add "global" Statements to see some important vars
		// Returns true if you want to allow chaining / further computations    

		// render_googlebooksresults(RDW_full);

		
		return $res;
		
			
	} // DEFINITION_RDW_SHOWRESULT_WIDGET()
	##############################################
	
	

	

	########################################################################################
	########################################################################################
	#
	# Specific functions to this WIDGET:
	#
	
	
	########################################################################################
	########################################################################################
	
	
		
	
	##################################################
	##################################################
	#
	# RUN WIDGET STATE MACHINE:
	#
	include_once("../u/RodinWidgetSMachine.php");
	##################################################
	##################################################	

?>