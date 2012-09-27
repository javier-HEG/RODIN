<?php
// In case this script was called outside of a widget
include_once('RodinWidgetBase.php');

$STATEMACHINE_DEBUG = $_GET['smdebug'];
if ($STATEMACHINE_DEBUG)
	$headerAreaHeight+=450;

$USER_ID					= $_REQUEST['pid'];
$CLONEDFROM_APPID			= $_REQUEST['cloned_from_app_id'];

//print "USERID=$USER_ID";
$RDW_POST					=(count($_POST)>0);
$RDW_SAVE_PREFS				=(is_a_value($_REQUEST['save']));
$RDW_RERENDER				=(is_a_value($_REQUEST['rerender']));
$RDW_DELETE_PREFS			=(is_a_value($_REQUEST['delete']));
$RDW_DOWNLOAD				=(is_a_value($_REQUEST['download']));
$RDW_STORERESULTS 			= is_a_value($_GET['sr']); // Request should be handled as a POST !
$RDW_GENERICAJAXREQUEST		=(is_a_value($_REQUEST['ajax'])|| $RDW_POST || $RDW_SAVE_PREFS || $RDW_DELETE_PREFS ||
$RDW_GENERICAJAXREQUEST); // respond with a number or 0
	//if ($RDW_POST) $STATEMACHINE_DEBUG=0; //in post mode we answer as php server to an ajax client=> reduce verbosity

#######################################################################################
// RDODIN WIDGET STATE MACHINE
// Used as running decision after widget def
// and together with RodinWidgetBase.php
//print "<br>\nRODIN WIDGET STATE MACHINE\n";
evalRDW_REQUEST(); // get all (needed) parameter from Querystring
#######################################################################################


//Decide witch procedure to be run

/* USE STATE MACHINE:
define ($RDW_DISPLAYHEADER,			0); // --> DEFINITION_RDW_DISPLAYHEADER
define ($RDW_DISPLAYSEARCHCONTROLS,	1); // --> DEFINITION_RDW_DISPLAYSEARCHCONTROLS
define ($RDW_COLLECTRESULTS,		2); // --> DEFINITION_RDW_COLLECTRESULTS
define ($RDW_STORERESULTS,			3); // --> DEFINITION_RDW_STORERESULTS
define ($RDW_SHOWRESULT_WIDGET,		4); // --> DEFINITION_RDW_SHOWRESULT_WIDGET
define ($RDW_SHOWRESULT_FULL,		5); // --> DEFINITION_RDW_SHOWRESULT_FULL
*/

if (! $RDW_GENERICAJAXREQUEST )
{
	$RDW_DISPLAYHEADER 			= true; // always
	$RDW_DISPLAYSEARCHCONTROLS 	= true; // always
	$RDW_COLLECTRESULTS 		= ($_GET['go']>0 && (!$RDW_RERENDER)); //action 'go'=1 (fri_rodin_metasearch()) =2 (direkt aus Wid)
	$RDW_SHOWRESULT_WIDGET 		= ($_GET['show']==RDW_widget);
	$RDW_SHOWRESULT_FULL 		= ($_GET['show']==RDW_full);
	$QUERYEXPRESS 				= is_a_value($_GET['qe']);
	if ($QUERYEXPRESS) {$q=$_GET['qe']; $qe=''; $_GET['qe']=''; $_GET['q']=$q;}
	if ($QUERYEXPRESS && !$RDW_RERENDER &&!$RDW_SHOWRESULT_WIDGET && !$RDW_SHOWRESULT_FULL)
		$RDW_COLLECTRESULTS=true;
}

$skin						= $_GET['skin'];

$SHOWING = $RDW_SHOWRESULT_WIDGET || $RDW_SHOWRESULT_FULL;


if ($RDW_POST)
	$RDW_STORERESULTS = (is_a_value($_GET['sr']) && is_a_value($_GET['_p']));

if ($RDW_SAVE_PREFS)
{
	$cnt = RDW_REGISTER_WIDGET_USER_PREFS('save');
	if ($STATEMACHINE_DEBUG) print "<br>RDW_REGISTER_WIDGET_USER_PREFS saved $cnt line(s)";
}
else if ($RDW_DELETE_PREFS)
{
	$D_APP_ID=$_GET['app_id'];
	$cnt = RDW_REGISTER_WIDGET_USER_PREFS('delete',$D_APP_ID);
	if ($STATEMACHINE_DEBUG) print "<br>RDW_REGISTER_WIDGET_USER_PREFS saved $cnt line(s)";
}



if ($RDW_DOWNLOAD)
{
	DEFINITION_RDW_DOWNLOAD();
}


if ($STATEMACHINE_DEBUG)
{
	print "<hr>";

	print "<br>RDW_RERENDER: $RDW_RERENDER";
	print "<br>RDW_SAVE_PREFS: $RDW_SAVE_PREFS";
	print "<br>RDW_POST: $RDW_POST";
	print "<br>RDW_GENERICAJAXREQUEST: $RDW_GENERICAJAXREQUEST";
	print "<br>RDW_DOWNLOAD: $RDW_DOWNLOAD";
	print "<br>RDW_DISPLAYHEADER: $RDW_DISPLAYHEADER";
	print "<br>RDW_DISPLAYSEARCHCONTROLS: $RDW_DISPLAYSEARCHCONTROLS";
	print "<br>RDW_COLLECTRESULTS: $RDW_COLLECTRESULTS";
	print "<br>RDW_STORERESULTS: $RDW_STORERESULTS";
	print "<br>RDW_SHOWRESULT_WIDGET: $RDW_SHOWRESULT_WIDGET";
	print "<br>RDW_SHOWRESULT_FULL: $RDW_SHOWRESULT_FULL";
	print "<br>SHOWING: $SHOWING";
	print "<br>QUERYEXPRESS: $QUERYEXPRESS";
	print "<br>SKIN: $skin";
	print "<hr>";
//exit;
}


//------------------------------------------------------------

if ($STATEMACHINE_DEBUG && !RDW_GENERICAJAXREQUEST)
{
	$qs_params=makeRDW_qs_params();
	$selfredirect ="$thisSCRIPT?$qs_params";
	print "<br>selfredirect: $selfredirect";
}


// following must be available, therefore it must be placed here
$widgetresultdivid="results_".uniqid();

##########################################
##########################################
##########################################
$res=true;
// Execution of the Widget Segments
if ($res && $RDW_DISPLAYHEADER)
	$res = DEFINITION_RDW_DISPLAYHEADER();

if ($res && $RDW_DISPLAYSEARCHCONTROLS)
	$res = RDW_DISPLAYSEARCHCONTROLS_EPI();

if ($res && $RDW_COLLECTRESULTS)
	$res = RDW_COLLECTRESULTS_EPI();

if ($res && $RDW_STORERESULTS)
	$res = RDW_STORERESULTS_EPI();

if ($res && $RDW_SHOWRESULT_WIDGET)
	$res = RDW_SHOWRESULT_WIDGET_EPI();

else if ($res && $RDW_SHOWRESULT_FULL)
	$res = RDW_SHOWRESULT_FULL_EPI();

##########################################
##########################################
##########################################




##########################################
##########################################
##########################################
function RDW_DISPLAYSEARCHCONTROLS_EPI()
##########################################
##########################################
##########################################
{
	global $STATEMACHINE_DEBUG;
	global $RDW_DISPLAYSEARCHCONTROLS;
	global $RODINUTILITIES_GEN_URL;
	global $SEARCHcontrol;
	global $SEARCHFILTERcontrol;
	global $QS_VALUE;
	global $DBUSERPREF;
	global $WEBROOT, $thisSCRIPT;
	global $remoteuser, $datasource;
	global $HOST, $FORMNAME;
	global $_w;
	global $APP_ID;
	global $USER_ID;
	global $CLONEDFROM_APPID;
	global $SHOWING;
	global $FRAMENAME;
	global $render;
	
	global $RDW_REQUEST;
	foreach ($RDW_REQUEST as $querystringparam => $defaultvalue)
		eval( "global \${$querystringparam};" );

	if ($STATEMACHINE_DEBUG && $RDW_DISPLAYSEARCHCONTROLS)
		print "<br>DEFINITION_RDW_DISPLAYSEARCHCONTROLS";

	$oldsid=$sid;
	$sid = check_recompute($sid,$datasource,$remoteuser);
	if ($sid <> $oldsid)
	{
		$QS_VALUE{'sid'}=$sid; //register
	}
	$res = DEFINITION_RDW_DISPLAYSEARCHCONTROLS();  // exec user definitions if any


	//Display form and default search control:
	print <<<EOP

<form name="$FORMNAME" method="get"  action="$WEBROOT$thisSCRIPT">
<table border=0 cellspacing=0 cellpadding=0 width="100%">
EOP;

	#######################
	#
	# Generate and display specified control fields
	#
	$old_linenr=-999;
	$linenr=-1;
	$firstline=1;
	$T='';
	$GENERATED_CONTROL = array();
	foreach ( $SEARCHcontrol as $CONTROL)
	{
		list($HTMLCONTROL,$linenr,$name,$value) = $CONTROL;

		if (0) print "<br>$linenr: ".urlencode($HTMLCONTROL);

		//print "<br> generating control $name...";
		if ($linenr <> $old_linenr)
		{
			if($firstline)
			{
				$firstline=false;
				$T.="\n<tr><td name=\"localSearchTable\" valign=\"top\" align=\"left\">";
			}
			else
				$T.="\n</td></tr>\n<tr><td>";
			 $old_linenr = $linenr;
		}

		// display control:

		$T.= "\n".$HTMLCONTROL;
		$GENERATED_CONTROL{$name}=1;
	} // foreach $SEARCHcontrol
	$T.="\n</td>";

	print $T;








$RENDERBUTTONS=true; // default;
//BLENDE DISPLAY CONTROL EIN (ZOOMS)
if (function_exists('WANT_RENDERBUTTONS'))
{
	$RENDERBUTTONS= WANT_RENDERBUTTONS();
}

//print "<br> RENDERBUTTONS: $RENDERBUTTONS";

if ($RENDERBUTTONS)
{

	//	print " RENDER1 ";
	##############
	if ($SHOWING)
	##############
	{
		$zoom_button_width = 20;
		$zoom_button_height = 24;
		
		$title1 = "";
		$title1_sel = "";
		$title2 = "";
		$title2_sel = "";
		$title3 = "";
		$title3_sel = "";
		
		$B1_ICON_NORMAL = "$RODINUTILITIES_GEN_URL/images/white.PNG";
		$B1_ICON_SELECTED = "$RODINUTILITIES_GEN_URL/images/white.PNG";
		$B1_ICON_HOVER = "$RODINUTILITIES_GEN_URL/images/white.PNG";
		$B2_ICON_NORMAL = "$RODINUTILITIES_GEN_URL/images/white.PNG";
		$B2_ICON_SELECTED = "$RODINUTILITIES_GEN_URL/images/white.PNG";
		$B2_ICON_HOVER = "$RODINUTILITIES_GEN_URL/images/white.PNG";
		$B3_ICON_NORMAL = "$RODINUTILITIES_GEN_URL/images/white.PNG";
		$B3_ICON_SELECTED = "$RODINUTILITIES_GEN_URL/images/white.PNG";
		$B3_ICON_HOVER = "$RODINUTILITIES_GEN_URL/images/white.PNG";

		$B1_ID = $FRAMENAME."_B_min";
		$B2_ID = $FRAMENAME."_B_token";
		$B3_ID = $FRAMENAME."_B_all";

		switch($render) {
			########################
			case "min":
				$ZOOM_HTML=<<<EOH
					<img src="$B1_ICON_SELECTED" width="$zoom_button_width" height="$zoom_button_height" border=0 title='$title1_sel'>
					<a ID=$B2_ID href='#' onClick="reload_frame('$FRAMENAME','token')" target='_self' title='$title2'>
						<img src="$B2_ICON_NORMAL" width="$zoom_button_width" height="$zoom_button_height" border=0
							onMouseover='src="$B2_ICON_HOVER"' onMouseout='src="$B2_ICON_NORMAL"'>
					</a>
					<a ID=$B3_ID href='#' onClick="reload_frame('$FRAMENAME','all')" target='_self' title='$title3'>
						<img src="$B3_ICON_NORMAL" width="$zoom_button_width" height="$zoom_button_height" border=0
							onMouseover='src="$B3_ICON_HOVER"' onMouseout='src="$B3_ICON_NORMAL"'>
					</a>
EOH;
				break;
				
			########################
			case "token":
				$ZOOM_HTML=<<<EOH
					<a ID=$B1_ID href='#' onClick="reload_frame('$FRAMENAME','min')" target='_self' title='$title1'>
						<img src="$B1_ICON_NORMAL" width="$zoom_button_width" height="$zoom_button_height" border=0
							onMouseover='src="$B1_ICON_HOVER"' onMouseout='src="$B1_ICON_NORMAL"'>
					</a>
					<img src="$B2_ICON_SELECTED" width="$zoom_button_width" height="$zoom_button_height" border=0 title="$title2_sel">
					<a ID=$B3_ID href='#' onClick="reload_frame('$FRAMENAME','all')" target='_self' title='$title3'>
						<img src="$B3_ICON_NORMAL" width="$zoom_button_width" height="$zoom_button_height" border=0
							onMouseover='src="$B3_ICON_HOVER"' onMouseout='src="$B3_ICON_NORMAL"'>
					</a>
EOH;
				break;

			########################
			case "all":
				$ZOOM_HTML=<<<EOH
					<a ID=$B1_ID href='#' onClick="reload_frame('$FRAMENAME','min')" target='_self' title='$title1'>
						<img src="$B1_ICON_NORMAL" width="$zoom_button_width" height="$zoom_button_height" border=0
							onMouseover='src="$B1_ICON_HOVER"' onMouseout='src="$B1_ICON_NORMAL"'>
					</a>
					<a ID=$B2_ID href='#' onClick="reload_frame('$FRAMENAME','token')" target='_self' title='$title2'>
						<img src="$B2_ICON_NORMAL" width="$zoom_button_width" height="$zoom_button_height" border=0
							onMouseover='src="$B2_ICON_HOVER"' onMouseout='src="$B2_ICON_NORMAL"'></a>
					<img src="$B3_ICON_SELECTED" width="$zoom_button_width" height="$zoom_button_height" border=0 title="$title3_sel">
EOH;
				break;
		}
		
		$ZOOMFILTER = "<td>$ZOOM_HTML</td>";
	} // SHOWING
} // RENDERBUTTONS
// ---------------------------------------


	#####################################
	if (function_exists('DEFINITION_RDW_SEARCH_FILTER')) // in RODINWidget
	#####################################
	{
		$resDEFINITION_RDW_SEARCH_FILTER = DEFINITION_RDW_SEARCH_FILTER(); // add search filter controls
		$WANTFILTER=true;
		/* If the user has not defined this function, he/she does not want any special search filter
		   making the following useless */

	}
	#####################################
	#
	# Insert SEARCH FILTER CONTROL
	# As last elem (toggles div on/off)
	#
	# Construct the framework to display search filter controls
	# defined in the rodin widgets.
	#
	#####################################


	if ($WANTFILTER)
	{
		//Incase $CLONEDFROM_APPID is set, use the same as the cloning brother
		//in order to get the PREFS, then store the prefs for this widget
		//using the current app_id
		if (is_a_value($CLONEDFROM_APPID))
		$REQ_APP_ID=$CLONEDFROM_APPID;
		else
		$REQ_APP_ID=$APP_ID;

		$saved_userprefs_for_this_widget_application= get_prefs($USER_ID,$REQ_APP_ID,$datasource);

		if ($STATEMACHINE_DEBUG)
		print "saved_userprefs_for_this_widget_application($QUERY_GET): $saved_userprefs_for_this_widget_application";



		if ($saved_userprefs_for_this_widget_application)
		{
			$PREFS=explode("&", $saved_userprefs_for_this_widget_application);
			foreach($PREFS as $P)
			{
				list($paramname,$paramvalue)=explode('=',$P);
				$DBUSERPREF{$paramname}=$paramvalue;

				if ($paramvalue <> '') $SEARCHPARAMSEXIST=true;
			}
		} //saved_userprefs_for_this_widget_application



		#Prepare for displaying

		$filterdivid="{$datasource}_{$APP_ID}_filter";
		$filterimgdivid="imgicon_".uniqid();
		
		$FILTERDIV = make_filter_div($filterdivid,$_w);
		
		if ($SEARCHPARAMSEXIST) {
			$ICONOPEN = "$RODINUTILITIES_GEN_URL/images/famfamfam/cog.png";
			$TITLEOPEN = lg("lblPrefsOpen");
		} else {
			$ICONOPEN = "$RODINUTILITIES_GEN_URL/images/famfamfam/cog_error.png";
			$TITLEOPEN = lg("lblPrefsLoadFailed");
		}
		

		################################################
		# Show Attribute settings Checkboxes whether collapsed or expanded
		# Here are shown

		$attributes_str = get_attribute_displays_str($saved_userprefs_for_this_widget_application);
		$ad_pairs = explode(',',$attributes_str);

		$i=-1;
		$ATTR= "<tr><td colspan=33>Change display prefs (collapsed) for each attribute:</td></tr>";
		
		foreach ($ad_pairs as $X)
		{
			$i++;
			$pair=explode(':',$X);
			$attr=$pair[0];
			$display=$pair[1];
			if ($attr)
			{
				$CHECKED = ($display=='c'?'checked':'');
				$TITLE=" title='Check if you wish to see the result item \"$attr\" ($display) collapsed, uncheck if you wish to see the result item \"$attr\" expanded at the next load' ";
				$OPTIONS=" $CHECKED name='ad_$attr' $TITLE ";
				$ATTR .=<<<EOO
					<tr><td colspan=3>
						<INPUT TYPE=CHECKBOX $OPTIONS>$attr</option>
					</td></tr>
EOO;
			}
		} // foreach;


		$SAVEACTION="get_and_send_form_x_params(document.$FORMNAME,'http://{$HOST}{$thisSCRIPT}','ajax=1&save=1&prefsuser=$USER_ID&app_id=$APP_ID', '$OKACTION','save');";

		if (is_a_value($CLONEDFROM_APPID)) {
			// duplicate the loaded PREFS from the cloning widget in DB for further use
			$QUERY_DUPLICATE="
			INSERT INTO userRDWprefs
			(SELECT prefsuser,datasource,'$APP_ID',queryprefs
				FROM userRDWprefs
				WHERE application_id='$REQ_APP_ID'
				AND prefsuser='$USER_ID'
				AND datasource='$datasource')";
				
			try {
				$DB = new RODIN_DB();
				$ret = mysql_query($QUERY_DUPLICATE, $DB->DBconn);
				$DB->close();
			}
			catch (Exception $e) {
				$ERROR=1; // return value: 0 results
				inform_exc($e);
			}
		} // copy&store the PREFS from the cloning widget

		print<<<EOP
			<td align="left" valign="top" name="searchPreferences">
				<img class="openPreferencesButton" id="$filterimgdivid" src="$ICONOPEN" title="$TITLEOPEN"
					onclick="var prefsDiv = document.getElementById('$filterdivid');
							 var openPrefsButton = document.getElementById('$filterimgdivid');

							 prefsDiv.style.visibility='visible';
							 prefsDiv.style.display='block';

							 openPrefsButton.style.visibility='hidden';"/>
			</td>
		</tr>
	</table>
	<div style="display: none; height: 0px;">$ZOOMFILTER</div>
EOP;



		#####################################
		print $FILTERDIV;

		print "<table cellpadding=0 cellspacing=0 border=0>";
			
		if ($resDEFINITION_RDW_SEARCH_FILTER) // generates the controls as it should
		{
			#######################
			#
			# Generate specified search filter control fields
			# and set their DB values from $DBUSERPREF
			#
			$old_linenr=-999;
			$linenr=-1;
			$firstline=1;
			$T='';
			foreach ( $SEARCHFILTERcontrol as $CONTROL)
			{
				list($HTMLCONTROL,$linenr,$name,$value) = $CONTROL;
				#
				# replace value='' with value='DBUSERPREF{$name}'
				# replace value='oldvalue' with value='DBUSERPREF{$name}'
				#
				$HTMLCONTROL_DB_VALUES = inject_current_filter_value($HTMLCONTROL,$name);

				if ($linenr <> $old_linenr)
				{
					if($firstline)
					{
						$firstline=false;
						$T.="\n<tr><td valign=top align=left>";
					}
					else
					$T.="\n</td></tr>\n<tr><td valign=top align=left>";
					$old_linenr = $linenr;
				}

				// display control:

				$T.= "\n".$HTMLCONTROL_DB_VALUES;
				$GENERATED_CONTROL{$name}=1;
			} // foreach $SEARCHcontrol
			$T.="\n</td>";
			print $T;
		}
		#####################################
		print "\n</table>";


		$closeLabel = lg("lblPrefsClose");
		$closeTitle = lg("lblPrefsCloseTitle");
		$buttonCloseAction = 'javascript: var x=document.getElementById(\'' . $filterdivid . '\'); x.style.visibility=\'hidden\'; x.style.display=\'none\'; ';
		$buttonCloseAction .= 'var openPrefsButton = document.getElementById(\'' . $filterimgdivid . '\'); openPrefsButton.style.visibility=\'visible\';';

		$saveLabel = lg("lblPrefsSave");
		$saveTitle = lg("lblPrefsSaveTitle");
		$savedOkLabel = lg("lblPrefsSavedOk");
		$saveButtonId = "save_button_" . uniqid();
		$saveOkAction = "javascript: x=document.getElementById(\'$saveButtonId\').value=\'$savedOkLabel\'";
		$buttonSaveAction = "javascript: get_and_send_form_x_params(document.$FORMNAME,'http://{$HOST}{$thisSCRIPT}','ajax=1&save=1&prefsuser=$USER_ID&app_id=$APP_ID', '$saveOkAction','save');";

		print '<p style="text-align: right; margin-bottom: 0px; margin-top: 6px;">';
		print '<input type="button" onclick="' . $buttonCloseAction . '" value="' . $closeLabel . '" />';
		print '<input type="button" id="' . $saveButtonId . '" value="' . $saveLabel . '" onclick="' . $buttonSaveAction . '" onmouseout="this.value=\'' . $saveLabel . '\'" />';
		print '</p>';

		print "\n</div></div>";

	} // WANTFILTER
	else
		print '</table><div style="display: none; height: 0px;">' . $ZOOMFILTER . '</div>';
	#####################################





	#######################
	#
	# Generate hidden fields to hold values
	# but only if not already there as controls

	$T='';
	foreach ($RDW_REQUEST as $querystringparam => $defaultvalue)
	{
		$name=$querystringparam;

		if (! ($GENERATED_CONTROL{$name}))
		{


			if (is_a_value($QS_VALUE{$querystringparam}))
				$value = $QS_VALUE{$querystringparam};
			else
			{

				// eval local value
				eval("\$value = \$$name;");
				//print "<br>extra evaluated value for \$$name: ($value)";
			}

		//print "<br> set hidden if not alreay control: $name = $value";


		$T.=<<<EOT
		 \n<input type="hidden" name="$name" value="$value" />
EOT;
		} // if ! $GENERATED_CONTROL{$name}
	}
	print $T;
	$govalue=RDW_COLLECTRESULTS;
	$T=<<<EOT
		\n<input type="hidden" name="go" value="$govalue" />
		\n<input type="hidden" name="p" value="$p" />
		\n<input type="hidden" name="app_id" value="$APP_ID" />
EOT;

	print $T;
	print "\n</form>";

	return $res;
} // RDW_DISPLAYSEARCHCONTROLS_EPI















##########################################
##########################################
##########################################
function RDW_COLLECTRESULTS_EPI()
##########################################
##########################################
##########################################
{
	global $STATEMACHINE_DEBUG;
	global $RDW_COLLECTRESULTS;
	global $thisSCRIPT;
	global $WEBROOT;
	global $datasource;
	global $NEED_PHP_INTERNET_ACCESS;
		global $q, $qe;
	global $USER_ID;
	global $APP_ID;
	global $CLONEDFROM_APPID;
	global $attributes_str;
	global $headerAreaHeight;
	global $widgetresultdivid;
	global $_w, $_h;

	// Include global parameters with exactly the same names
	// as the RDW_REQUEST parameters
	global $RDW_REQUEST;
	foreach ($RDW_REQUEST as $querystringparam => $defaultvalue) {
		//print "<br>RDW_REQUEST $querystringparam=>$defaultvalue";
		eval( "global \${$querystringparam};" );
	}

	##################################
	#
	# Retrieve Userprefs from DB
	#


	// In case $CLONEDFROM_APPID is set, we have to use the same ID
	// as the cloning brother, otherwise it will possibly be empty,
	// since this widget instance was already created.
	if (isset($CLONEDFROM_APPID))
			$REQ_APP_ID=$CLONEDFROM_APPID;
	else
			$REQ_APP_ID=$APP_ID;

	$saved_userprefs_for_this_widget_application= get_prefs($USER_ID,$REQ_APP_ID,$datasource);
	if($saved_userprefs_for_this_widget_application)
		$PREFS="&$saved_userprefs_for_this_widget_application";

	// Load user preferences as PHP variables and in the _REQUEST variables
	$userprefstatement = explode("&",$saved_userprefs_for_this_widget_application);
	foreach ($userprefstatement as $x) {
		list($name,$value) = explode('=',$x);
		if ($value) {
			//print "<br> Will eval: "."\${$name}=$value;";
			if (is_a_value($value) && !is_integer($value))
				$value = "'$value'";
			
			eval( "\${$name} = $value;" );
			$_REQUEST[$name] = $value;
			$RDW_REQUEST{$name} = $value;
		}
	}

	if ($STATEMACHINE_DEBUG && $RDW_COLLECTRESULTS)
		print "<br>DEFINITION_RDW_COLLECTRESULTS";

	$qs_params = makeRDW_qs_params();

	$selfredirect ="$WEBROOT$thisSCRIPT?$qs_params";

	if ($STATEMACHINE_DEBUG) {
		print "<br>makeRDW_qs_params: $qs_params";
		print "<br>selfredirect: $selfredirect";
		print "<br>WEBROOT: $WEBROOT";
	}


	################################################
	#
	# Output "Receiving results from source" message
	#

	$_w = max($_w,$minwidth);
	$_h = max($_h,300);
	$res = true;
	
	print make_widget_div($widgetresultdivid.'_xx', $_h, $headerAreaHeight);

	$ok_to_continue = true;
	if ($NEED_PHP_INTERNET_ACCESS)
	{
		if (!check_internetconn())
		{
			fontprint('<B><FONT COLOR=red>No internet or bad internet connection - please try again later</b></FONT>','red');
			$res=false;
			print "</div>";
			$ok_to_continue = false;
		}
	}

	if ($ok_to_continue) {
		if ($STATEMACHINE_DEBUG && $RDW_COLLECTRESULTS )
			print "<br>After DEFINITION_RDW_COLLECTRESULTS, redirect to: $selfredirect";

		print "<p class='widgetResultCount'>" . lg("lblCollectingResults") . "</p>";

		if ($qe)
			$q=$qe;

		$sr = DEFINITION_RDW_COLLECTRESULTS($selfredirect);
		
		// $sr can also be a false value
		if ($sr === false)
			$res = false;
		else if (is_object($sr))
			store_widget_results($sr);

		$ATTRIBUTESDISPLAYDEFS = collect_resultattributes($sr);

		// Save parameters (attributes?) set in the widget's preferences
		if (count($ATTRIBUTESDISPLAYDEFS)) {
			$attributes_str = get_attribute_displays_str($saved_userprefs_for_this_widget_application);
			
			if ($attributes_str <> '') {
				$queryprefs = $saved_userprefs_for_this_widget_application;
			} else {
				$queryprefs = $saved_userprefs_for_this_widget_application;
			}

			if (!$queryprefs) {
				$queryprefs = $saved_userprefs_for_this_widget_application;
			}

			foreach ($ATTRIBUTESDISPLAYDEFS as $attrname=>$displayoption) {
				if ($attrname) {
					//print "<br>take $attrname=>$displayoption";
					$value = 'c'; // default

					if ($value = $existing_display_option{$attrname}) {
						$used{$attrname}=1;
					} else {
						$value = $displayoption;
					}
					
					if ($new_attributedisplays) {
						$new_attributedisplays.=',';
					}

					$new_attributedisplays .= $attrname . ":" . $value;
				}
			}

			//incase the saved where more then the current:
			if (count($existing_display_option)) {
				foreach($existing_display_option as $attrname=>$displayoption)
				{
					if (!$used{$attrname}) {
						if ($new_attributedisplays) {
							$new_attributedisplays.=',';
						}

						$new_attributedisplays .= $attrname . ":" . $displayoption;
					}
				}
			}

			// Insert into prefd "&attributes=$new_attributedisplays"
			$queryprefs .= "&attributes=" . $new_attributedisplays;
			$queryprefs = str_replace('&&', '&', $queryprefs);

			try {
				$DB = new RODIN_DB();
				$DBconn = $DB->DBconn;

				$QUERY_UPDATE="
					UPDATE userRDWprefs SET
						prefsuser  ='$USER_ID' ,
						datasource = '$datasource' ,
						application_id = '$APP_ID',
						queryprefs = '$queryprefs'
					WHERE
						prefsuser = '$USER_ID' AND
						datasource = '$datasource' AND
						application_id = '$APP_ID';";

					$ret = mysql_query($QUERY_UPDATE);
				} catch (Exception $e) {
					$ERROR=1; // return value: 0 results
					inform_exc($e);
				}

				$DB->close();
		}

		print "</div> VICHANGO";

		// This variable tells if the widget should continue to the 
		// next state, in that case it redirects itself.
		if ($res) {
			print inject_javascript(
				"fri_redirect('$datasource','$selfredirect','$_x','_self'); // using frame id in $_x"
			);
		}
	} // internet connection

	return $res;
} // RDW_COLLECTRESULTS_EPI








function RDW_REGISTER_WIDGET_USER_PREFS($action,$d_app_id='')
##########################################
#
# Processing the request of saving all
# params (whose name begins with "x")
# for the currrent datasource/application:_id/prefsuser
# this request is sent by an AJAX call get_and_send_form_x_params()
{
	global $STATEMACHINE_DEBUG;
	global $thisSCRIPT;
	global $datasource;
	global $DBconn;
	global $USER_ID;

	//print "RDW_REGISTER_WIDGET_USER_PREFS: ($action,$d_app_id)";

    foreach($_REQUEST as $name=>$value)
	{

		//print "<br> _REQUEST $name=>$value";
		// collect only the params whose names begins with "x"
		if (substr($name,0,1) == 'x')
		{
			if (is_array($value))
			{	$array_seg='';
				foreach ($value as $v)
				{
					if ($array_seg) $array_seg.=",";
					$array_seg.= $v;
				}

				if ($XPARAMS) $XPARAMS.="&";
				$XPARAMS.="$name=$array_seg"; // xz=value1,value2,value3
		  }
			else
			{
				if ($XPARAMS) $XPARAMS.="&";
				$XPARAMS.="$name=$value";
			}
		}
		else if ($name=='app_id')
			$APP_ID=$value;
		else if ($name=='attributes')
			$XPARAMS.="$name=$value";

	}

	$prefsuser=$_REQUEST{'prefsuser'};
	$reload=$_REQUEST{'reload'};

	//print "<br>$XPARAMS ";
	$queryprefs=$XPARAMS;

	$QUERY_INSERT="
		INSERT into userRDWprefs (`prefsuser`,`datasource`,`application_id`,`queryprefs`)
		value ('$prefsuser','$datasource','$APP_ID','$queryprefs')";
	$QUERY_CHECK="
		SELECT count(*) as CNT
		FROM userRDWprefs
		WHERE
		prefsuser = '$prefsuser' AND
		datasource = '$datasource' AND
		application_id = '$APP_ID';";
	$QUERY_UPDATE="
	UPDATE userRDWprefs SET
		prefsuser  ='$prefsuser' ,
		datasource = '$datasource' ,
		application_id = '$APP_ID',
		queryprefs = '$queryprefs'
	WHERE
		prefsuser = '$prefsuser' AND
		datasource = '$datasource' AND
		application_id = '$APP_ID';";
	$QUERY_DELETE="
		DELETE
		FROM userRDWprefs
		WHERE
		application_id = '$d_app_id';";

	if (1)
	{
		try {

			$DB = new RODIN_DB();
			$DBconn=$DB->DBconn; // needed?

			if ($action=='save')
			{
				$ret = mysql_query($QUERY_CHECK);
				if ($ret!=null)
				{

						//print "QUERY_CHECK: $QUERY_CHECK<br>";

						$REC= mysql_fetch_assoc($ret);
				}

				if ($REC['CNT'] > 0 && !$reload) // bereits eintrag da: UPDATE wenn nicht reload
				{

					//print "<br>TRY $QUERY_UPDATE";
					$qresult = mysql_query($QUERY_UPDATE);
					// ohne meldung
					$affected_rows=1;
				}
				else // INSERT
				{
					//print "<br>TRY $QUERY_INSERT";
					$qresult = mysql_query($QUERY_INSERT);
					if (($affected_rows= mysql_affected_rows())<1)
						throw(New Exception(mysql_error($DB->DBconn)."<hr>Query:".$QUERY_INSERT."<br><br>"));
				}
			} // save
			else if ($action=='delete')
			{
				print "<br>TRY $QUERY_DELETE";

				$qresult = mysql_query($QUERY_DELETE);
				$affected_rows=1;
			} // delete
		}
		catch (Exception $e)
		{
			$ERROR=1; // return value: 0 results
			inform_exc($e);

		}
		$DB->close();
	}

	print $affected_rows; // for http req ajax
	return $affected_rows;
} // RDW_REGISTER_WIDGET_USER_PREFS












##########################################
##########################################
##########################################
function RDW_STORERESULTS_EPI()
##########################################
##########################################
##########################################
{
	global $STATEMACHINE_DEBUG;

	$res = DEFINITION_RDW_STORERESULTS();

	return $res;

}













##########################################
##########################################
##########################################
function RDW_SHOWRESULT_WIDGET_EPI()
##########################################
##########################################
##########################################
{
	global $STATEMACHINE_DEBUG;
	global $datasource;
	global $headerAreaHeight;
	global $minwidth;
		global $widgetresultdivid;
	global $attributes_str;
	global $APP_ID;
	global $_w, $_h;

	global $RDW_REQUEST;
	foreach ($RDW_REQUEST as $querystringparam => $defaultvalue) eval( "global \${$querystringparam};" );

	if ($STATEMACHINE_DEBUG) print "<br>DEFINITION_RDW_SHOWRESULT_WIDGET";


	//Correction minwidth
	$_w = max($_w,$minwidth);
	$_h = max($_h, 300);

	print make_widget_div($widgetresultdivid, $_h, $headerAreaHeight);

	$res = DEFINITION_RDW_SHOWRESULT_WIDGET($_w, $_h);

	print "</div>";

	########################
	#
	# Used to unblock rodin's dark search protection
	#
	if ($uncache)
		print make_uncache_javascript_code('FRI: Uncache from $datasource');


	adapt_widgetsareas_on_openclose_widgetmenu();

	return $res;

} // RDW_SHOWRESULT_WIDGET_EPI













##########################################
##########################################
##########################################
function RDW_SHOWRESULT_FULL_EPI()
##########################################
##########################################
##########################################
{
	global $minwidth;
	global $STATEMACHINE_DEBUG;
	global $RDW_REQUEST;
	global $widgetresultdivid;
	global $headerAreaHeight;
	global $_w, $_h;

	foreach ($RDW_REQUEST as $querystringparam => $defaultvalue) eval( "global \${$querystringparam};" );
	if ($STATEMACHINE_DEBUG) print "<br>DEFINITION_RDW_SHOWRESULT_FULL";

	//Correction minwidth
	$_w = max($_w,$minwidth);
	$_h = max($_h,300);

	print make_widget_div($widgetresultdivid, $_h, $headerAreaHeight);

	$res = DEFINITION_RDW_SHOWRESULT_FULL($_w, $_h);

	print "\n</div>";
	return $res;

} // RDW_SHOWRESULT_WIDGET_EPI






function inject_current_filter_value($HTMLCONTROL,$name)
########################################################
#
# Used to put in userprefs current values from $DBUSERPREF
# relating to $name
#
# Returns the injected or original $HTMLCONTROL
#
# SERVED HTML CONTROLS:
# input (text radio checkbox)
# textarea
# select
{
	global $DBUSERPREF;

	$names=explode(",",$DBUSERPREF{$name});
	//foreach($names as $xname) print "<br>xname: $xname";

	if (count($names) == 1) $names=null;

	// foreach ($DBUSERPREF as $X=>$Y) print "<br>DBUSERPREF ($X=>$Y)";

	// search alway for the empty case:
	// <input name="xq" size=17 type="text" value="" 	...>
	// <input name="xq" size=17 type="text" value= 		...>
	// <input name="xq" size=17 type="text" value='' 	...>
	$replace=true;

	$sdom = str_get_html($HTMLCONTROL); //simplehtml

	$TEXTAREA = $sdom->find('textarea');
	$INPUT = $sdom->find('input');
	$SELECT = $sdom->find('select');

	foreach ($TEXTAREA as $AR)
		if ($AR->name==$name)
			$AR->innertext=$DBUSERPREF{$name};

	foreach ($INPUT as $IN)
		if ($IN->name==$name)
		{
			if ($IN->type == 'text')
				$IN->value=$DBUSERPREF{$name};
			else if ($IN->type == 'radio')
				$IN->checked= ($IN->value == $DBUSERPREF{$name});
			else if ($IN->type == 'checkbox')
				$IN->checked= (preg_match("/".$IN->value."/",$DBUSERPREF{$name})==true);
		}


	foreach ($SELECT as $SEL)
		if ($SEL->name==$name)
			foreach($SEL->find('option') as $OP)
				$OP->selected=false;

	foreach ($SELECT as $SEL)
		if ($SEL->name==$name)
		{
			$selcnt=-1;
			foreach($SEL->find('option') as $OP)
			{	$selcnt++;
				if ($names)
				{
					foreach($names as $xname)
					{
						// set only positive case, because of multiple values!
						if ($OP->value==$xname) $OP->selected=true;
					}
				}
				else
					$OP->selected=($OP->value==$DBUSERPREF{$name});

			}
		}






	//print "<br> returning html:<br> ((\n".($sdom->save())."))";

	return $sdom->save();
} // inject_current_filter_value






################################################
if (!$RDW_POST && !$RDW_SAVE_PREFS)
print <<<EOP
</body>
</html>
EOP;

?>