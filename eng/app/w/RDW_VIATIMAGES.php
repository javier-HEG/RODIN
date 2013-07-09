<?php
	#########################################################################
	#
	# WIDGET <Widgetname>
	# AUTHOR Fabio Ricci / HEG, Tel: 076-5281961 / fabio.fr.ricci@hesge.ch
	# DATE 	 1.12.2009
	#
	# PURPOSE: 	Visualize Results of VIATIMAGE (VIATICALP)
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
	# Put all the GENERIC ajax code in ../u/RODINutilities.js.php
	#
	##############################################

	// The following is the link to the resource:
	$viatimage_service_url="http://www3.unil.ch/viatimages/index.php?module=searchadv&projet=viaticalpes&action=search";
	$viatimage_basis_url="http://www3.unil.ch/viatimages/";
	##############################################


	##############################################
	##############################################
	#
	# This will print the html header with a Title
	#


	print_htmlheader("VIATICALP");
	##############################################
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

		//No problem here:
		return true;

	} // DEFINITION_RDW_DISPLAYHEADER
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
		global $SEARCHFILTER_TEXT_SIZE;
		global $m;
	##############################################


	##############################################
	#
	# Each filter param is prefixed by "x"
	# You have to provide a real name also
	##############################################

	// Defint some style for controls
	global $_w;
	$w=$_w - 15; // fix width in accordance to Widget desired width

	$PREFS_STYLE =<<<EOS
	style="min-width:{$w}px;max-width:{$w}px; width:{$w}px;text-align:center";
EOS;
		$PREFS_STYLE =<<<EOS
	style="text-align:center";
EOS;

		$title="Enter year (e.g. 1800)";
		$xfrom=$_REQUEST['xfrom'];
		if ($xfrom=='')
			$xfrom="1800";

		$htmldef=<<<EOH
		from:
		<input name="xfrom" type='text' size=4 title="$title" value='$xfrom' $PREFS_STYLE>
EOH;
		$n= add_searchfilter_control('xfrom','from',$xfrom,"'$xfrom'",$htmldef,1);

		$title="Enter year (e.g. 2010)";
		$xto=$_REQUEST['xto'];
		if ($xto=='')
			$xto="2010";


		$htmldef=<<<EOH
		to:
		<input name="xto" type='text' size=4 title="$title" value='$xto' $PREFS_STYLE>
EOH;
		$n= add_searchfilter_control('xto','to',$xto,"'$xto'",$htmldef,1);


		// SET DEFAULT PREFS IN DB
		// in  order to enable direct use of the widget
		// without setting any preference
		register_default_prefs("xfrom=1800&xto=2010");

	return true;

}// DEFINITION_RDW_SEARCH_FILTER









	#The following tells the widget state machine to check
	#once for internet connection and warn if no one found
	#(timeout) before collecting results
	$NEED_PHP_INTERNET_ACCESS=true;
	$NEED_AJAX_INTERNET_ACCESS=true;



	##############################################
	##############################################
	function DEFINITION_RDW_COLLECTRESULTS($chaining_url='')
	##############################################
	##############################################
	{
		global $DBB_basesegment;
		global $RDW_REQUEST;
		global $datasource;
		global $REALNAME;
		global $viatimage_service_url;
		global $viatimage_basis_url;

		# The followin globals here all registered querystring parameters:
		foreach ($RDW_REQUEST as $querystringparam => $d) eval( "global \${$querystringparam};" );
		$res=true;

		foreach($REALNAME as $rodin_name=>$needed_name)
		{
			//print "<br>REALNAME:  $rodin_name=>$needed_name";
			if ("${$rodin_name}") // only if value defined
				$FILTER_SECTION.="&$needed_name=${$rodin_name}";

				if ($rodin_name == 'xfrom')
				{
					$von=$xfrom; // catch search filter value from RDW_REQUEST
					//print "<br> xfrom=$xfrom";
				} else
				if ($rodin_name == 'xto')
				{
					$bis=$xto; // catch search filter value from RDW_REQUEST
					//print "<br> xto=$xto";
				}
		}



		$data=
		 "&crit0[]=&crit0[]=0&crit0[]=$q&crit0[]=ALL"
		."&crit1[]=AND&crit1[]=1&crit1[]=&crit1[]=ALL&crit2[]=AND"
		."&crit2[]=2&crit2[]=&crit2[]=ALL&crit3[]=AND"
		."&crit3[]=3&crit3[]=&crit3[]=ALL&crit4[]=AND"
		."&crit4[]=4&crit4[]=&crit4[]=ALL"
		."&dateMode=from&txtStartDate=$von&txtEndDate=$bis"
		."&siecle_voyage=&siecle_edition=&biblio=&map_type=&emplacement=&composer=all&planche=all&couleurs=all&texteAssoc=all#result";

		$url=$viatimage_service_url.$data;

		$RESULT = get_file_content($url,false);

		/*
		print "<br>RESULT ($url) <hr>FOUND: (((<br>".($RESULT).")))<br>";
		$filename="/Users/fabio/Downloads/output_dbb.xml";

		$h=fopen($filename,"w");
		fwrite($h,$RESULT);
		fclose($h);
		print "<br>See (($filename)) for content of webpage";
		*/

		$html = str_get_html($RESULT);
		$IMAGELIST = $html->find('div[class=imagethumb]');
		//print "<br>RESULTLISTBODY  (((".($RESULTLISTBODY).")))";

		$num_results=0;
		if ($IMAGELIST) // Datensatz gefunden
		foreach ($IMAGELIST as $IMAGEDIV)
		{
			if ($num_results >= $m) break;
			else
			{
				$a0=$IMAGEDIV->find('a',0);
				$link=$IMAGEDIV->find('a',1);
				$img=$IMAGEDIV->find('img',0);

				$datarecord_url=$viatimage_basis_url.$link->href;
				$img_url=$img->src;
				$title=$img->alt;

				/*
				print "<hr>";
				//print "<br><br><b>a0:</b><br>";print $a0->title ."   <br><b>href:</b>". $a0->href;
				print "<br><br><b>link:</b><br>";print $link->title ."   <br><b>href:</b>". $link->href;
				print "<br><br><b>img:</b><br>";print $img->src ."   <br><b>alt</b>". $img->alt;
				print "<br><br><b>datarecord:</b><br><a href='$datarecord_url' target='_blank' title='klick to see'>$datarecord_url</a>";
				print "<br><br><b>title:</b><br>";print $title;
				*/
				$VIARESULT[]=array(
									$title,
									$img_url,
									$datarecord_url
								   );

				$num_results++;
			}
		}

		$sr = new SR;
			$searchid = new SEARCHID;
			$searchid->sid=$sid;
			$searchid->m=$num_results;
			$searchid->q=$q;
			$searchid->datasource=$datasource;
		$sr->searchid = $searchid;
		$sr->result   = array();

		//Load into Structure for storing
		if ($num_results)
		{
			$i=0;
		}

	$i=0;
	// Extract and store each record the document fields:
	if (count($VIARESULT))
	foreach( $VIARESULT as $VIA)
	{
		list($title, $img_url, $datarecord_url)= $VIA;
		/*
		print "<ht><br>title= $title";
		print "<br>img_url= $img_url";
		print "<br>datarecord_url= $datarecord_url";
		*/
		$pos=0; $x=0;

		$fatherresult=$localresult = new RESULT;
		$localresult->xpointer="$i";
		$localresult->row[]= array('MainRes','string','', base64_encode($datarecord_url), false, 'VIAT','cr');
		$sr->result[]=$localresult;

		$localresult = new RESULT;
		$localresult->xpointer="$i";
		$localresult->row[]= array('Title','string', '', base64_encode($title) ,true, 'VIAT','cr');
		$sr->result[]=$localresult;

		$localresult = new RESULT;
		$localresult->xpointer="$i";
		$localresult->row[]= array('IMG','string', '', base64_encode($img_url) ,true, 'VIAT','cr');
		$sr->result[]=$localresult;

		$i++;
	}


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
		global $render;

		render_viatimage_sresults(RDW_widget,$w,$h);

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
		global $render;


		render_viatimage_sresults(RDW_full,$w,$h);
		return $res;

	} // DEFINITION_RDW_SHOWRESULT_WIDGET()
	##############################################





	function render_viatimage_sresults($mode,$w,$h)
	##############################################
	{
		global $RODINUTILITIES_GEN_URL;
		global $thisSCRIPT;
		global $datasource;
		global $WIDGET_ID;
		global $APP_ID;
		global $SRC_INTERFACE_BASE_URL;
		global $WIDGET_SEARCH_MAX;
		global 	$COLOR_PAGE_BACKGROUND,
		$COLOR_WIDGET_UNMARKRESULT,
		$COLOR_WIDGET_RESULT_SEPARATION;
		global $nosrc;



		$datasourcename = $datasource;

		global $RDW_REQUEST;
		foreach ($RDW_REQUEST as $querystringparam => $defaultvalue) eval( "global \${$querystringparam};" );

		if ($mode==RDW_widget)
		{
			$viatimagewidth=70;
			$viatimageheight=70;
		}
		else if ($mode==RDW_full)
		{
			$viatimagewidth=200;
			$viatimageheight=200;
			// to be adjusted:
			$imgSideSize = 200;
 		}

		//fontprint("<br>sid=$sid, q=$q, qe=$qe, m=$m, uncache=$uncache",'blue');

		$Query="select * from dir_item where url like \"%$thisSCRIPT%\";";
		$widgetData = fetch_record($Query,'posh');

		$headerAreaHeight=60; // header area height
		$widgetVertScrollDelta=10; //on the right this take some pixels
		$widgetInnerWidth = $_w;
		$widgetInnerWidthPics = $widgetInnerWidth - $widgetVertScrollDelta;
		$widgetInnerTableAreaWidth = $widgetInnerWidthPics - 8;
		$widgetInnerHeight = $_h;
		$widgetInnerHeightPics = $widgetInnerHeight - $headerAreaHeight;
		$txtseqRODINattrnames = array( 	'MainRes'	=>'base64html',
										'IMG'		=>'base64html',
										'Title' 	=>'base64html'   );

		if (!($RESULT = getRODINdata($sid,"System error: No sid given to Widget - could not retrieve results",$txtseqRODINattrnames)))
		{


		} // !getRODINdata



		// in $RESULT are the collected GBOOKINFOs:
		print <<<EOT
<table cellpadding=0 cellspacing=0 border=0 width="100%">
EOT;
	//Adjust bitmap dimensions: Take from viatimagebooks!

		$i=-1;
		$ClickTitle="Click to see this Viaticalpes result in a new browser tab";
		foreach ($RESULT as $INFO)
		{
			$i++;
			//OPTI: CACHE bauen

			$viatimage_pic_url=$INFO{'IMG'};

			/*
			print "<hr>";
			print "<br>IMG:".$INFO{'IMG'};
			print "<br>Title:".$INFO{'Title'};
			print "<br>DataRecordURL:".$INFO{'MainRes'};
			*/


			if ($viatimagewidth>0 and $viatimageheight>0)
			{
				$IMGDIMS=" width=$viatimagewidth height=$viatimageheight ";
				$TD2IMG_DIM=" width=$viatimagewidth ";
			}
			else
				$IMGDIMS="";

			$IMGHREF="<img src='$viatimage_pic_url' $IMGDIMS border=0>";
		 	$IMGREF="<a href='".$INFO{'MainRes'}."' target=_blank title='$ClickTitle'>$IMGHREF</a>";

		 	$IDSEM="$datasource.sem.$i";
			$base64_q=base64_encode($q);

			$qs_params="user_id=$USER_ID"
						."&sid=$sid"
						."&datasource=$datasource"
						."&app_id=$APP_ID"
						."&c=s" // synch call
						."&maxdur=$WIDGET_SEARCH_MAX"
						."&sid=$sid"
						."&quickvector=".$wordw64
						."&quickq=".$base64_q;
			$newtab_name="Refine $datasourcename result ITEM";

//DA LAVORARE: PRESI DA utilities:

			if (!$nosrc) {
				$CLEAN_RIGHT_PART=cleanup_strangecharacgers($INFO{'Title'});
				$base64_RIGHT_PART=base64_encode($CLEAN_RIGHT_PART);
				$base64_q=base64_encode($q);
				//$qs_params.="&quickvector=".$base64_RIGHT_PART."&quickq=".$base64_q;
				$qs_params="&recordcnt=&xpointer=&quickvector=".$base64_RIGHT_PART."&quickq=".$base64_q."&sid=".$sid."&maxdur=".$WIDGET_SEARCH_MAX."&c=s";
				//NUR das item!!!
				$newtab_name="refine_".cleanup_datasource_name($datasourcename);
				$SEMlink = "parent.rodin_zen_filter('$base64_RIGHT_PART', '$base64_q', document.getElementById('spotlight-box-$IDSEM'))";
				$SEMANTIC_TD = make_semlink_td($SEMlink,lg("titleLaunchZenFilter"),"$qs_params"); /* DT */
			}

//DA LAVORARE: PRESI DA utilities:
			//[Javier] Add the Spotlight box div
			$spotlightBox = '<div class="spotlightbox" style="visibility:hidden; margin-bottom: 2px; width:95%;" id="spotlight-box-'. $IDSEM . '"></div>';

			print "\n<tr height=\"1\"><td colspan=4><hr style=\"height:1px;color:$COLOR_WIDGET_RESULT_SEPARATION;height: 1px; border-spacing: 1px\"/>$spotlightBox</td></tr><tr><td $TD2IMG_DIM valign=\"top\" align=\"center\">".($IMGREF)."</td>"
					."\n<td>".$SEMANTIC_TD."</td>"
					 ."\n<td valign='top' id='gbr$i' onclick=\"window.open('".$INFO{'MainRes'}."','_blank')\"
					 	bgcolor=\"$COLOR_WIDGET_UNMARKRESULT\"
						onmouseover=\"setbgcolor('gbr$i','$COLOR_WIDGET_MARKRESULT');\"
					 	onmouseout=\"setbgcolor('gbr$i','$COLOR_WIDGET_UNMARKRESULT');\" title=\"$ClickTitle\">"
						 ."\n<table cellpadding=0 cellspacing=0 style=\"display:block\">"
					 	."\n<tr><td  valign='top'colspan=2>{$FONTRESULT}".$INFO{'Title'}."{$ENDFONTRESULT}</td></tr>"
					."</table></td>"
					."</tr>"
					."<tr height=\"3\"/><td colspan=3></td></tr>";

		} // foreach

		print<<<EOT
</table>
EOT;

	} // definition_rdw_showresult_widget()
	##############################################







	########################################################################################
	########################################################################################
	#
	# Specific functions to this WIDGET:
	#




function getRODINdata($sid,$nosidwarning,$txtseqRODINattrnames)
#################################
#
#
# $sid: The sid
# $nosidwarning: The warning text if sid is not valid
# $txtseqRODINattrnames: Attributenames in table result - quoted and with commas
# 						 For instance: 'BookUrl'=>,'BookImgUrl','Booktitle','PublishedYear','Title','PageCount'
/*

txtseqRODINattrnames = Array (
							attrname => url // what you need here
							attrname => base64html // what you need here
							attrname => value // what you need here
						)

*/
{
		global $datasource;
		$RESULT=null;

		if (ckeck_sid($sid,$nosidwarning))
		{
			// construct list of attrnames plus 'xpointer'
			foreach($txtseqRODINattrnames as $attr=>$X)
			{
				if ($attributes)
					$attributes.=',';
				$attributes.="'$attr'";
			}

			$RESULT = array();
			$QUERY=<<<EOQ
select distinct xpointer, attribute, value, url from result
where sid='$sid' and datasource='$datasource'
and attribute in ($attributes)
order by xpointer asc;
EOQ;

			//print "<br><b>QUERY:</b><br>$QUERY<br>";
			try {
			  $DB = new RODIN_DB();
			  $DBconn=$DB->DBconn;
			  $resultset = mysql_query($QUERY);

			  $DB->close();
			}
			catch (Exception $e)
			{
			  inform_bad_db($e);
			}

		  //Get a part of result set:
		  // feth also Attribute=Title
		  // All values for the same xpointer value!!!
			$old_xpointer='';
			$i==-1;

			//else
			{	$numres= -1;
				while ($row = mysql_fetch_assoc($resultset))
				{
				  $i++;
				  $numres++;
				  $xpointer =$row['xpointer'];

				  if ($xpointer<>$old_xpointer)
				  { //begin new data set from universal db result store
					$old_xpointer=$xpointer;


					if ($i>1) // not the first time
					{
						$RESULT[]=$INFO; //collect results
					}
					$INFO = array(); //prepare for next run
				  } // if

				  //read generic data set
				  $attribute =$row['attribute'];
				  $value =$row['value'];
				  $url =$row['url'];

				  /*
				  print "<hr>xpointer=$xpointer";
				  print "<br>attribute=$attribute";
  				  print "<br>value=$value";
  				  print "<br>url=$url";
				  */

				  if ($wanted_value=$txtseqRODINattrnames{$attribute}) //url, base64html, etc,
				  {
					if ('url'==$wanted_value)
					{
						$INFO{$attribute}=$url;
						//print "<br>trovato $attribute: ".$INFO{$attribute};
					} // url
					elseif ('value'==$wanted_value)
					{
						$INFO{$attribute}=$value;
						//print "<br>trovato $attribute: ".$INFO{$attribute};
					} // value
					elseif ('base64html'==$wanted_value)
					{
						$INFO{$attribute}=base64_decode($url);
						//print "<br>trovato e decodificato base64html $attribute: ".$INFO{$attribute};
					} // value
				  }
			  } // while

		  	if($numres<=0)
			{
				print "<p id=\"widget_user_warn\" class=\"widgetResultCount\">" . lg("lblGotNoResults") . "</p>";
			}
			else
			    $RESULT[]=$INFO; //collect last results
		} // while
	 }


	//fontprint("<br>FRI: getRODINdata liefert ".count($RESULT)." Ergebnisse",'blue');
	//var_dump($RESULT);


	return $RESULT;

} // getRODINdata










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