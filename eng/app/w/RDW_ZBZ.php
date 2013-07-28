<?php
	#########################################################################
	#
	# WIDGET <Widgetname>
	# AUTHOR Fabio Ricci / HEG, Tel: 076-5281961 / fabio.fr.ricci@hesge.ch
	# DATE 	 10.08.2009
	#
	# PURPOSE: 	Visualize Results of old hand made maps in RDW_widget mode
	#          	The cover areas of each result is visualized on google maps
	#			in RDW_full mode
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

	# AJAX includes:
	#
	# Put all the WIDGET SPECIFIC ajax code here (pls. rename):
	#
	$MY_AJAX_FILE = make_ajax_widget_filename(/*overriding filename if needed*/);
	#
	# Put all the GENERIC ajax code in ../u/RODINutilities.js.php
	#
	##############################################



	// The following is the link to the resource:
	$ZBZMquerysegment="http://opac.nebis.ch/F?local_base=zbz&con_lng=GER&func=find-c&ccl_term=wft=cm+and+wrd=";
	##############################################


	##############################################
	##############################################
	#
	# This will print the html header with a Title
	#

	$EXTRAINCLUDES = "<script src=\"../u/json2.js\" type=\"text/javascript\"></script>"
    				 . "\n<script src=\"http://www.google.com/jsapi?key=$GOOGLEAPIKEY\" type=\"text/javascript\"></script>";


	print_htmlheader("ZBZ hand made maps", $MY_AJAX_FILE, $EXTRAINCLUDES);
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
	<input name="q" type="text" value="$qx" title='$title' class="localSearch" onchange="$SEARCHSUBMITACTION">
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
	##############################################



	// Button ask (DEFAULT)
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
		global $ZBZMquerysegment;
		global $RDW_REQUEST;
		global $datasource;
		
		# The followin globals here all registered querystring parameters:
		foreach ($RDW_REQUEST as $querystringparam => $d) eval( "global \${$querystringparam};" );

		// Returns true if you want to allow chaining / further computations
		$res=true;

		$tags=explode(' ',($q));

		$URL = $ZBZMquerysegment.urlencode($q);
		$htmlContent = get_file_content($URL);
		
		// Because of the SSO we need to detect the redirect URLs and load them.
		if (strstr($htmlContent,"PDS SSO")) {
			$pattern = '/\surl\s=\s\'.*url=(?P<url>.*)\'/i';
			preg_match($pattern, $htmlContent, $matches);
			
			$htmlContent = get_file_content($matches['url']);
			
			if (strstr($htmlContent,"PDS SSO")) {
				print "&nbsp;".lg('datasourcenotavailable');
				print make_uncache_javascript_code('exit from ZBZ');
				exit;
			}
		}

		// Scrap the HTML code for results
		$html = str_get_html($htmlContent);
		$resultsFoundInHTML = extractNbOfResultsFromHTML($html, $m);

		// Load each Result into DB
		$sr = new SR;
		$searchid = new SEARCHID;
		$searchid->sid = $sid;
		$searchid->m = 0; // possibly not yet correct!
		$searchid->q = $q;
		$searchid->datasource = $datasource;
		$sr->searchid = $searchid;
		$sr->result = array();
			
		$i=0;
		foreach($resultsFoundInHTML as $R) {
			if ($i > $m - 1) break; // user specified $m -> MORE EFFICIENT: RESTRICT RESULTS FROM GET
			$i++;

			$localresult = new RESULT;
			$localresult->xpointer="$i";
			$localresult->row= array();
			$localresult->row[]= array('MainRes','string','URL',($R->mainobj),true,true, 'ZBZMR','cr');
			$sr->result[]=$localresult;

			$localresult = new RESULT;
			$localresult->xpointer="$i.".$x++;
			$localresult->row[]= array('Author','string', $R->author, ($R->author_href),true, 'ZBZMR','cr');
			$sr->result[]=$localresult;

			$localresult = new RESULT;
			$localresult->xpointer="$i.".$x++;
			$localresult->row[]= array('Title','string',$R->title, '',true, 'ZBZMR','cr');
			$sr->result[]=$localresult;
			$localresult = new RESULT;
			$localresult->xpointer="$i.".$x++;
			$localresult->row[]= array('MapScale','string',$R->mapscale, '',true, 'ZBZMR','cr');
			$sr->result[]=$localresult;
			$localresult = new RESULT;
			$localresult->xpointer="$i.".$x++;
			$localresult->row[]= array('Impressum','string',$R->impressum, '',true, 'ZBZMR','cr');
			$sr->result[]=$localresult;
			$localresult = new RESULT;
			$localresult->xpointer="$i.".$x++;
			$localresult->row[]= array('Extent','string',$R->extent, '',true, 'ZBZMR','cr');
			$sr->result[]=$localresult;
			$localresult = new RESULT;
			$localresult->xpointer="$i.".$x++;
			$localresult->row[]= array('TotalExtent','string',$R->totalextent, '',true, 'ZBZMR','cr');
			$sr->result[]=$localresult;
			$localresult = new RESULT;
			$localresult->xpointer="$i.".$x++;
			$localresult->row[]= array('BibAdvice','string',$R->bibladvice, '',true, 'ZBZMR','cr');
			$sr->result[]=$localresult;
			$localresult = new RESULT;
			$localresult->xpointer="$i.".$x++;
			$localresult->row[]= array('Comment','string',$R->comment, '',true, 'ZBZMR','cr');
			$sr->result[]=$localresult;
			$localresult = new RESULT;
			$localresult->xpointer="$i.".$x++;
			$localresult->row[]= array('Library','string',$R->library, $R->library_href,true, 'ZBZMR','cr');
			$sr->result[]=$localresult;
			$localresult = new RESULT;
			$localresult->xpointer="$i.".$x++;
			$localresult->row[]= array('Sysnumber','string',$R->sysnr, '',true, 'ZBZMR','cr');
			$sr->result[]=$localresult;
			$localresult = new RESULT;
			$localresult->xpointer="$i.".$x++;
			$localresult->row[]= array('MapCoord','georect',$R->maprect, '',true, 'ZBZMR','cr');
			$sr->result[]=$localresult;
			$localresult = new RESULT;
			$localresult->xpointer="$i.".$x++;
			$localresult->row[]= array('FirstmapTHN','base64html',$R->firstmap_thumbnail, '',true, 'ZBZMR','cr');

			//foreach element in MAPS
			if (is_array($R->mapuri))
			foreach($R->mapuri as $MAPINFO) {
				list($maptitle,$mapurl)=$MAPINFO;
				$localresult->row[]= array('MapURI','string',$maptitle, $mapurl, true, 'ZBZMR','cr');
			}

			$sr->result[]=$localresult;
		}

		$searchid->m = $i; // Correct Number of results


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
		global $RDW_REQUEST;
		global $datasource;
    global $slrq;
		global $render;
		# The followin globals here all registered querystring parameters:
		foreach ($RDW_REQUEST as $querystringparam => $d) eval( "global \${$querystringparam};" );
		$res=true;

		// ADD HERE CODE TO RENDER THE STORED RESULTS in mode "RDW_widget"
		// Remember to add "global" Statements to see some important vars
		// Returns true if you want to allow chaining / further computations

		// render_googlebooksresults(RDW_widget);

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
		global $RDW_REQUEST;
		# The followin globals here all registered querystring parameters:
		foreach ($RDW_REQUEST as $querystringparam => $d) eval( "global \${$querystringparam};" );
		$res=true;

		// ADD HERE CODE TO RENDER THE STORED RESULTS in mode "RDW_full"
		// Remember to add "global" Statements to see some important vars
		// Returns true if you want to allow chaining / further computations

		$WANT_RENDERBUTTONS=false;


		print_zbz_results_on_googlemaps($sid,$w,$h);


		return $res;


	} // DEFINITION_RDW_SHOWRESULT_WIDGET()
	##############################################



	##############################################
	##############################################
	function WANT_RENDERBUTTONS()
	##############################################
	##############################################
	{
		if ($_GET['show']==RDW_full)
				return false;

		else
			return true;
	}






	########################################################################################
	########################################################################################
	#
	# Specific functions to this WIDGET:
	#



	#
	# Use values together with js.php file:
	#
	include_once("./RDW_ZBZ.inc.php");



    ##############################################
    ##############################################
 	function print_zbz_results_on_googlemaps($sid,$w,$h)
    ##############################################
    ##############################################
	{

		global $GOOGLEAPIKEY;
		global $datasource;

		global $RODINUTILITIES_GEN_URL;
		global 	$POLY_COLOR_FILL_RED,
				$POLY_COLOR_FILL_GREEN,
				$POLY_COLOR_FILL_BLUE,
				$NAVI_COLOR_FILL_BLUE; // See .js.php ajax file
     	$POLY_COLOR_FILL_OPACITY='.1';


		global $RDW_REQUEST;
		# The followin globals here all registered querystring parameters:
		foreach ($RDW_REQUEST as $querystringparam => $d) eval( "global \${$querystringparam};" );

		//correction:
		$w=max(600,$w); // at least 600...
		$h=max(400,$h);

		//print "<br> w,h = $w,$h";

		$EVTLGOOGLEMAPSAPIULOAD="onunload=\"GUnload()\"";

		// DEMO: User can display several "worlds" where to map ZBZ things
		$gag=$_REQUEST['gag'];
		switch( $gag )
		{
			case(''): $GOOGLEMAP='G_SATELLITE_MAP'; //G_PHYSICAL_MAP
								break;
			case('3d'): $GOOGLEMAP='G_SATELLITE_3D_MAP'; //G_PHYSICAL_MAP
								break;
			case('moon'): $GOOGLEMAP='G_MOON_VISIBLE_MAP'; //G_PHYSICAL_MAP
								break;
			case('mars'): $GOOGLEMAP='G_MARS_VISIBLE_MAP'; //G_PHYSICAL_MAP
								break;
			case('sky'): $GOOGLEMAP='G_SKY_VISIBLE_MAP'; //G_PHYSICAL_MAP
								break;
		}



    	$QUERY=<<<EOQ
select xpointer, attribute, value, url from result
where sid='$sid'
and datasource='$datasource'
and attribute in ('xpointer','MapCoord','FirstmapTHN','MapURI','Title','MainRes','Impressum','Author')
order by xpointer asc;
EOQ;

 	    try {
	      $DB = new RODIN_DB();
	      $DBconn = $DB->DBconn;
	      $resultset = mysqli_query($DB->DBconn,$QUERY);

	      $DB->close();
	    }
	    catch (Exception $e)
	    {
	      inform_bad_db($e);
	    }

      //Get a part of result set:
      // feth also Attribute=Title
      // For the same xpointer value!!!
      $old_xpointer='';
  	  while ($row = mysqli_fetch_assoc($resultset))
			{
					$xpointer =$row['xpointer']; //must be always the same for get record data
					$xlevel=compute_XPointerLevel($xpointer);

					if ($xlevel == 1)
					{
						if ($xpointer<>$old_xpointer)
						{ //begin new data set from universal db result store
							//fontprint("<br>SWITCH XPOINTER: $xpointer",'red');
					$state='BEGINDATASET';
							$old_xpointer=$xpointer;
							$INFO = new GEOMASHUPINFO;
							$INFO->maps=array();
							$INFO->url=$url;
							$MESHUP[]=$INFO;
						}
					}
          //read generic data set
          $attribute =$row['attribute'];
					$value =$row['value'];
          $url =$row['url'];

          if ($attribute=='MapURI')
          {
            $INFO->maps[]=(array($value,$url));
            //print "<br>MAPS (".(count($INFO->maps)).") Elemente: adding array($value,$url)";
            //print "<br> Xpointer: $xpointer: Adding Karte ($url,$value)";
            //$INFO->url=$url;
            //$INFO->mapname=$value; // Multiple values
          }
          else if ($attribute=='MainRes')
          {
            $INFO->url_details=$url;
          }
          else if ($attribute=='Author')
          {
   					$INFO->author=html_entity_decode($value);
          }
          else if ($attribute=='FirstmapTHN')
          {
   					$INFO->FirstmapTHN=$value;
          }
          else if ($attribute=='Impressum')
          {
   					$INFO->impressum=html_entity_decode($value);
          }
          else if ($attribute=='Title')
          {
   					$INFO->title=html_entity_decode($value);
          }
          else
           if ($attribute=='MapCoord')
          {
           	// use $INFO and complete it
            $arr = explode('+',$value);
            //print "<br>split ($value) = (".$arr[0].") AND (".$arr[1].")";
            $INFO->coord_latlon_min=$arr[0];
            $INFO->coord_latlon_max=$arr[1];
	          //print "<br>x min=".$INFO->coord_latlon_min.", max=".$INFO->coord_latlon_max;
          }
          //print "<br>y min=".$INFO->coord_latlon_min.", max=".$INFO->coord_latlon_max;

      } // while



      $CENTERINFO = compute_geomapcenter($MESHUP); // returns a LatLng value for setCenter '22, 44'

      //debug
  $NAVIHELP=<<<EOF

<table border=0 cellpadding=0 cellspacing=1>
EOF;




  $i=-1;
  if (count($MESHUP)>0)
	foreach ( $MESHUP as $mu )
  {
  		$i++;
      $letter=chr($i + ord('A'));
      $title = $mu->title;
      $impressum = $mu->impressum;
      $author = $mu->author;
      $FirstmapTHN = $mu->FirstmapTHN;

      $NUMOFMAPS =count($MAPS=$mu->maps);
      if ($NUMOFMAPS > 1)
      	$NUMOFMAPS="<a href='' title='$NUMOFMAPS maps found'>($NUMOFMAPS)</a>";
      else $NUMOFMAPS='';

      $url = $mu->url;
      $url_details = $mu->url_details;
      $coord_latlon_min = $mu->coord_latlon_min;
      $coord_latlon_max = $mu->coord_latlon_max;
      list ($lat_min,$lng_min) = explode(',',$mu->coord_latlon_min);
      list ($lat_max,$lng_max) = explode(',',$mu->coord_latlon_max);

      $coord_latlon_cnt = (($lat_min+ $lat_max)/2).", ".(($lng_min+$lng_max) / 2);

 			//print "<hr>";
      //print "<br>$letter: coord_latlon_min=($coord_latlon_min),  coord_latlon_max=($coord_latlon_max)<br>";
      //if ($coord_latlon_min == '') print "NULL";
      //print "Chart url: $url";
      //      fontprint( "<br>($lat_min,$lng_min) x($lat_max,$lng_max) = $coord_latlon_cnt",'red');

      $limited_title=str_limit($title,30);

      // rechter Bereich Navi-Help show one navi result
      #####################################################
      #####################################################
      #####################################################

      $HREF_ACTIVATE_ZOOM="<a href='' title='Zoom and show this on the map: \"$title\"' onclick=\"return showMarkerText(zbzm_mark_$i,$i);\">";
      $HREF_ACTIVATE="<a href='' title='Show this on the map: \"$title\"' onclick=\"return showMarkerText(zbzm_mark_$i,-1);\">";
      $HREF_MAP="<a href='$url' title='Show this map on new page' target='_blank'>";
      $HREF_DETAILS="<a href='$url_details' title='Show more details on a new page' target=\"_blank\">";
      $HREF_MARK="<a href='' onclick=\"poly_toggle($i,'$POLY_COLOR_FILL_GREEN','$POLY_COLOR_FILL_OPACITY','$POLY_COLOR_FILL_RED','.5',true);\" "
      						."title='Mark the map area red and brings it to the top'><img width=14 height=14 border=0 src='$RODINUTILITIES_GEN_URL/images/augerot.gif' /></a>";

      //Adjust bitmap dimensions:
      $max_navi_img_width=30;
      $max_navi_img_height=45; //provisorisch

	    $navi_img_width=0;
	    $navi_img_height=0;
	    if ($max_navi_img_width > 0 || $max_navi_img_height >0 )
	    {
	      $result = getIdealDimensions($FirstmapTHN, $max_navi_img_width, $max_navi_img_height);
	      $navi_img_width = $result[0];
	      $navi_img_height = $result[1];
	    }

      //NAVIHELP Text:
      $NAVIHELP.=<<<EOS


			<tr>
      	<td valign="center" align="center" bgcolor='#FF8080'>
        	$HREF_ACTIVATE_ZOOM<h3><font style='color:black;'>$letter</font></h3>
				</td>
        <td  onmouseover="self.bgcolor='#eeeeff'" onmouseout="self.bgcolor='white'" >
        		<table id='navi_$i' border=0 cellpadding=0 cellspacing=1>
      				<tr >
              	<td width=270 nowrap>
                  $HREF_ACTIVATE
                   <b>$limited_title</b>
                  </a>
                 </td>
                 <td>
                 $HREF_DETAILS
                 <font style='font-size:-3;'>more</font>
                 </a>
                </td>
              </tr>
         			<tr onclick="return showMarkerText(zbzm_mark_$i,$i);" onmouseover="transfer_mouseover($i)" onmouseout="transfer_mouseout($i)" onclick="poly_toggle($i,'$POLY_COLOR_FILL_GREEN','$POLY_COLOR_FILL_OPACITY','$POLY_COLOR_FILL_RED','.5',true);">
              	<td colspan=2>
                	<table cellpadding=0 cellspacing=1>
                  	<tr>
                    	<td valign=top>
                  $HREF_MAP
                    <img src='$FirstmapTHN' width=$navi_img_width height=$navi_img_height border=0/>
                  </a><br>
                  $NUMOFMAPS
                  		</td>
                 		 <td>
                 	   <table cellpadding=0 cellspacing=1>
	                        <tr>
	                          <td valign='top'> Author: </td><td><b>$author</b> </td>
	                        </tr>
	                        <tr>
	                          <td valign='top'> Impressum: </td><td><b>$impressum</b> </td>
	                        </tr>
	                   </table>
	              		</td>
                  </tr>
                  </table>
                </td>
              </tr>
         	</table>
        </td>
      </tr>
EOS;
      #####################################################
      #####################################################
      #####################################################
	} // foreach


		$MAPCOMMANDS=<<<EOC
    	<table cellpadding=0 cellspacing=0>
      <tr><td width=10/><td valign=top align=left>
      	<a href='' onclick="return zoomoutmap();" title='Zoomout to see all maps'>
      		<img src="$RODINUTILITIES_GEN_URL/images/zoomout2.jpg" width="30px" height="30px" border=0>
      	</a>
      </td>
      <td width=10/>
      <td valign=top align=left>
      	<label id='syslabel' />
      </td>
      </tr>
      </table>

EOC;
      // Begin googlemaps rendering:
    	$div="$sid.$datasource";
		$wp=320; //wpanel for mashup
		$header_h = 60; // height of the header area.
			$NAVIHELP.= "</table>";
		$h1=$h + 10;
		$h2=$h - $header_h - 30; //abzgl. header, abzgl. zoomout
		$h3=$h1 + 30;
		$wp2=$wp - 6;
		$h4 = $h + 10;
		$w1 = $w - $wp;

print <<<EOT

  	<div id="wpanel_pre" style="padding:5px; left:0px;top:0px;height:20px;width:{$wp2}px;background:white" >
			Results to Query: <strong>$q</strong>
    </div>
  	<div id="wpanel" style="position:relative;padding:5px; left:0px;top:0px;height:{$h}px;width:{$wp}px;overflow:scroll;" >
    $NAVIHELP
    </div>
    <div id='$div_pre' style="position:relative;left:310px; top:-{$h3}px; height:30px; width:{$w}px; background:white;">
    $MAPCOMMANDS
    </div>
    <div id='$div' style="position:relative;left:{$wp2}px; top:-{$h3}px; height:{$h2}px; width:{$w}px;">
    </div>
  	<script src="../u/json2.js" type="text/javascript"></script>
    <script src="http://www.google.com/jsapi?key={$GOOGLEAPIKEY}" type="text/javascript">
  	</script>



    <script language="Javascript" type="text/javascript">
    google.load('maps', '2');
    zbzm_polygon_toggle = [];
    zbzm_polygon_style = [];
    zbzm_polygon_title = [];
    zbzm_polygon = [];
    zbzm_marker_on=false;


  	cencer_lat=$CENTERINFO->center_lat;
	cencer_lng=$CENTERINFO->center_lng;
	gmaps_w = $w1;
	gmaps_h = $h4;


  	function initialize()
	  {
      //global:cencer_lat,cencer_lng,gmaps_w,gmaps_h
      bounds = new GLatLngBounds();

      if (GBrowserIsCompatible())
      {
      var extpoints = [];
      map = new GMap2(document.getElementById('$div'), {size:new GSize(gmaps_w,gmaps_h)});
      map.setCenter(new google.maps.LatLng( center_lat, center_lng), 8);
      map.setUIToDefault();

      map.addControl(new GScaleControl(300));
      map.enableDoubleClickZoom();
      map.enableScrollWheelZoom();
      //map.setMapType(G_PHYSICAL_MAP); //force this repr.
			map.setMapType($GOOGLEMAP); //force this repr.
			//G_SATELLITE_3D_MAP
			//G_SATELLITE_MAP
			//G_MOON_VISIBLE_MAP
			//G_MARS_VISIBLE_MAP
			//G_SKY_VISIBLE_MAP
			//More under http://code.google.com/intl/de-DE/apis/maps/documentation/reference.html#GMapType.G_SATELLITE_MAP


      //map.clearOverlays();
			// Erstellt ein Basissymbol f�r alle Markierungen, das den Schatten, die Symbolabmessungen usw. angibtvar
	    var baseIcon = new GIcon();
	    baseIcon.shadow = "http://www.google.com/mapfiles/shadow50.png";
	    baseIcon.iconSize = new GSize(20, 34);
	    baseIcon.shadowSize = new GSize(37, 34);
	    baseIcon.iconAnchor = new GPoint(9, 34);
	    baseIcon.infoWindowAnchor = new GPoint(9, 2);
	    baseIcon.infoShadowAnchor = new GPoint(18, 25);
    } //Browser
    else
    { alert('Please change browser to see this mashup representation');
      exit();
    }


	// the following belongs to initialize!!!





	   function getPolyLatLngs(i, sw, ne)
	   {  //Make a rectangle from sw->ne repr
		  //Inverting x y
		  var sw_x=sw.y;
		  var sw_y=sw.x;
		  var ne_x=ne.y;
		  var ne_y=ne.x;
		  var nw_x=sw.y;
		  var nw_y=ne.x;
		  var se_x=ne.y;
		  var se_y=sw.x;

		  var polyLatLngs = [];
		  var polyPixel;
		  var polyLatLng;
		  //GLog.write(i+" --Adding "+sw_x+","+sw_y);
		  polyLatLng = new GLatLng(sw_x,sw_y);
				polyLatLngs.push(polyLatLng);
		  //GLog.write(i+" --Adding "+nw_x+","+nw_y);
		  polyLatLng = new GLatLng(nw_x,nw_y);
		  polyLatLngs.push(polyLatLng);
		  //GLog.write(i+" --Adding "+ne_x+","+ne_y);
				polyLatLng = new GLatLng(ne_x,ne_y);
		  polyLatLngs.push(polyLatLng);
		  //GLog.write(i+" --Adding "+se_x+","+se_y);
		  polyLatLng = new GLatLng(se_x,se_y);
		  polyLatLngs.push(polyLatLng);
		  polyLatLng = new GLatLng(sw_x,sw_y);
				polyLatLngs.push(polyLatLng);
		  return polyLatLngs;
		}


		function createMarker(point, letter, txt_arr)
		{
			// Erstellt ein Symbol mit einem Buchstaben f�r diesen Punkt mithilfe der Symbolklasse
		  var letteredIcon = new GIcon(baseIcon);
		  letteredIcon.image = "http://www.google.com/mapfiles/marker" + letter + ".png";
		  markerOptions = { icon:letteredIcon,width:500 };
		  var marker = new GMarker(point, markerOptions);
		  marker.info_window_content = txt_arr;
		  marker.bindInfoWindowTabs(txt_arr);
		  GEvent.addListener(marker, "click", function() {
				zbzm_marker_on=true;
				//GLog.write("click click (zbzm_marker_on: " + zbzm_marker_on+")");
			  });
		  GEvent.addListener(marker, "infowindowbeforeclose", function() {
				//GLog.write("infowindowclose reset (zbzm_marker_on: " + zbzm_marker_on+")");
			   zbzm_marker_on=false;
		  });

		  return marker;
		}



		var n=-1;
EOT;


  // Gmaps Events: "mouseover", "mouseout", "mouseup" and "mousedown"

  $i=-1;
  if ($MESHUP)
	foreach ( $MESHUP as $mu )
  ###########################
  ###########################
  ###########################
  #Construct NAVIHELP
  ###########################
  ###########################
  ###########################
  {
  		$i++;
      $title = $mu->title;
      $url = $mu->url;
      $MAPS=$mu->maps;
      $impressum = $mu->impressum;
      $author = str_limit($mu->author,max(30,count($MAPS)*12));  // More room if multiple maps

      $FirstmapTHN = $mu->FirstmapTHN;
      list ($lat_min,$lng_min) = explode(',',$mu->coord_latlon_min);
      list ($lat_max,$lng_max) = explode(',',$mu->coord_latlon_max);
      $coord_latlon_min = $mu->coord_latlon_min;
      $coord_latlon_max = $mu->coord_latlon_max;

      $coord_latlon_cnt = (($lat_min+ $lat_max)/2).", ".(($lng_min+$lng_max) / 2);
      $limited_title=str_limit($title,35);


      //Ideal thumbnail dimensions:
      $navi_img_width=0;
	    $navi_img_height=0;
	    if ($max_navi_img_width > 0 || $max_navi_img_height >0 )
	    {
	      $result = getIdealDimensions($FirstmapTHN, $max_navi_img_width, $max_navi_img_height);
	      $navi_img_width = $result[0];
	      $navi_img_height = $result[1];
	    }
      // MARKER-TEXT:
      $HREF_DETAILS="<a href='$url_details' title='Show more details on a new page' target='_blank'>";


		//quote alles was ' ist
    //$txt2 = str_replace("'","\\'",$txt2);
    // Prepare the possibly multiple Infotab elements

      $MERKER_TABTEXT="var marker_alternatives=new Array;\n";
      $m=-1; $mm=0;

      $MARKER_WIDTH = max(250,count($MAPS) * 81); // necessary for google maps marker GInfoWindowTab label sizes on several maps

//      foreach($MAPS as $MAPS_INFO)
//      { $m++; $mm++;
//       	list($map_name,$map_url) = $MAPS_INFO;
//        $limited_map_name=str_limit($map_name,9,true);
        $limited_map_name=str_limit($limited_title,9,true);
//	      $HREF_MAP="<a href='$map_url' title='Show map on new page' target='_blank'>";
	      $txt = "<table cellpadding=0 cellspacing=0>"
	        ."<tr >"
	          ."<td width=$MARKER_WIDTH nowrap>"
//	            .$HREF_MAP
	             ."<b>$limited_title</b>"
	            ."</a>"
	           ."</td>"
	           ."<td>&nbsp;&nbsp;"
	           .$HREF_DETAILS
	           ."<font style='font-size:-3;'>more</font>"
	           ."</a>"
	          ."</td>"
	        ."</tr>"
          ."<tr >"
	          ."<td colspan=2> "
//             .$HREF_MAP
//             .$map_name
             ."</a>"
	          ."</td>"
	        ."</tr>"

	        ."<tr>"
	          ."<td colspan=2>"
	            ."<table cellpadding=0>"
	              ."<tr>"
	                ."<td>"
//	            .$HREF_MAP
	              ."<img src='$FirstmapTHN' width=$navi_img_width height=$navi_img_height border=0/>"
	            ."</a>"
	                ."</td>"
	               ."<td>"
	               ."<table cellpadding=0>"
	                    ."<tr>"
	                      ."<td valign='top' width='100'> Author: </td><td><b>$author</b> </td>"
	                    ."</tr>"
	                    ."<tr>"
	                      ."<td valign='top'> Impressum: </td><td><b>$impressum</b> </td>"
	                    ."</tr>"
	               ."</table>"
	              ."</td>"
	            ."</tr>"
	            ."</table>"
	          ."</td>"
	      ."</tr>"
	  ."</table>";
//				$MERKER_TABTEXT.="\n"."marker_alternatives[$m]= new GInfoWindowTab(\"$limited_map_name\",\"$txt\");  ";
				$MERKER_TABTEXT.="\n"."marker_alternatives[0]= new GInfoWindowTab(\"$limited_title\",\"$txt\");  ";
//      } // for $MAPS

      
print <<<EOMU
      n++;
      var letter = String.fromCharCode("A".charCodeAt(0) + $i);
      var point = new GLatLng($coord_latlon_min);

      $MERKER_TABTEXT

      zbzm_mark_$i = createMarker(point, letter, marker_alternatives);
      zbzm_polygon_title[n]="$title";
      map.addOverlay(zbzm_mark_$i);

      function handle_overlay_click_$i(overlaylatlng) {
      	zbzm_marker_on=true;
	      //GLog.write("zbzm_marker_on: " + zbzm_marker_on);
	      //GLog.write("You clicked overlay $i $title at overlaylatlng " + overlaylatlng);
        zbzm_mark_$i.openInfoWindowTabs(zbzm_mark_$i.info_window_content);
	    }

      function handle_overlay_mouseover_$i(overlaylatlng) {
      	if (!zbzm_marker_on)
        {
	        //GLog.write("overlay $i $title (zbzm_marker_on: " + zbzm_marker_on+")");
	        poly_mark_mouseover($i,'$POLY_COLOR_FILL_BLUE','.2',false);
	        navi_mark_mouseover($i);
          document.getElementById('syslabel').innerHTML ='$title';

        }
      }
      function handle_overlay_mouseout_$i(overlaylatlng) {
      	if (!zbzm_marker_on)
        {
          document.getElementById('syslabel').innerHTML ='';
  	      //GLog.write("leave overlay $i $title (zbzm_marker_on: " + zbzm_marker_on+")");
	        poly_mark_mouseout($i);
	        navi_mark_mouseout($i);
				}
	    }



      //var boundaries = new GLatLngBounds( new GLatLng($coord_latlon_min), new GLatLng($coord_latlon_max));
      //var oldmap = new GGroundOverlay("$url", boundaries, {clickable:true});
      //map.addOverlay(oldmap);

      var SW= new GLatLng($coord_latlon_min);
      var NE= new GLatLng($coord_latlon_max);
      bounds.extend(SW); // 4 fitMap
      bounds.extend(NE); // 4 fitMap
      polyLatLngs = getPolyLatLngs(n, SW, NE);

      var zbzm_poly_$i = new GPolygon(polyLatLngs, '#000000', 1, 1.0, '$POLY_COLOR_FILL_GREEN', $POLY_COLOR_FILL_OPACITY);
      // the following are global:
      zbzm_polygon_toggle[$i]=false;
      zbzm_polygon_style[$i]=['$POLY_COLOR_FILL_GREEN','$POLY_COLOR_FILL_OPACITY'];
      zbzm_polygon[$i]=zbzm_poly_$i;

      //GLog.write(n+": Polygon.isHidden():"+zbzm_poly_$i.isHidden());
      //GLog.write(n+": Polygon.getArea():"+zbzm_poly_$i.getArea());
      //GLog.write(n+": Polygon.getBounds():"+zbzm_poly_$i.getBounds());

      GEvent.addListener(zbzm_poly_$i, "click", handle_overlay_click_$i);
      GEvent.addListener(zbzm_poly_$i, "mouseover", handle_overlay_mouseover_$i);
      GEvent.addListener(zbzm_poly_$i, "mouseout", handle_overlay_mouseout_$i);




      map.addOverlay(zbzm_poly_$i);


EOMU;

  } // mu loop

print <<<EOT

       //GLog.write("set fit zoom: "+map.getBoundsZoomLevel(bounds));
       //GLog.write("Set fit center in: "+bounds.getCenter());
	     map.setZoom(map.getBoundsZoomLevel(bounds));
	     map.setCenter(bounds.getCenter());

    } //initialize




  	google.setOnLoadCallback(initialize);
    </script>

EOT;


    ##########################################
    } // print_zbz_results_on_googlemaps
    #####################












	########################################################################################
	########################################################################################
	#
	# Specific functions to this WIDGET:
	#
	########################################################################################
	########################################################################################


  class GEOMASHUPINFO
  {
	public $MAPS;
    public $title;
    public $url;
    public $impressum;
    public $author;
    public $url_details;
    public $FirstmapTHN;
    public $maps;
    public $coord_latlon_min;
    public $coord_latlon_max;
  }



  function compute_geomapcenter($MESHUP)
  ######################################
  #
  # returns a LatLng value for google.maps.setCenter: '22, 44'
  # as Struct GEOMAP_DIM
  {

    $max_lat= -9999;
    $max_lng= -9999;
    $min_lat=  9999;
    $min_lng=  9999;
    $R = new GEOMAP_DIM;

    if(count($MESHUP)>0)
    foreach( $MESHUP as $geomashupinfo )
    {
     $max_latlng = explode(',', $geomashupinfo->coord_latlon_max);
     $min_latlng = explode(',', $geomashupinfo->coord_latlon_min);

     $R->max_lat=max($R->max_lat, $max_latlng[0]);
     $R->max_lng=max($R->max_lng, $max_latlng[1]);
     $R->min_lat=min($R->max_lat, $min_latlng[0]);
     $R->min_lng=min($R->max_lng, $min_latlng[1]);
    }

    $R->center_lat= ($R->min_lat + $R->max_lat ) / 2;
    $R->center_lng= ($R->min_lng + $R->max_lng ) / 2;

    return $R;

  } //compute_geomapcenter

  class GEOMAP_DIM
  {
 		 public $max_lat;
     public $max_lng;
     public $min_lat;
     public $min_lng;
     public $center_lat;
     public $center_lng;
  }



  /**
   * @deprecated
   */
	function scanResult($ZBZM_STRING) {
		//print "<hr><b>scanResult:</b><br>(".($ZBZM_STRING).")<hr>";exit;

		$PATTERN_ZBZM = "/^<td class=td1 id=centered valign=top><A HREF=(.*)>(.*)<\/A><\/td>/";
		$res=preg_match($PATTERN_ZBZM,$ZBZM_STRING,$treffer);
		if ($res)
		{
			$HREF=$treffer[1];
			//foreach($treffer as $lineno=>$t) {
			//	print "\n\n<br>$lineno: <b>TREFFER</b>: (".($t).")";}

		}
		//print "<br> HREF: $HREF";
		//open and show HREF

		$html = get_file_content($HREF);


		//print "<br>html= ((($html)))";


		if ($WANTTOSEEHTMLSOURCE) print "<hr>Original Data from ZBZ:<br> $html";

		$R = parse_ZBZMresult($html); // ein ZBZMRESULT



    	$R->mainobj=$HREF;
    return $R;
	} // scanResult

/**
* @deprecated
*/
	class ZBZMRESULT {

		public $title;
		public $mapdata;
    public $mapscale;
    public $maprect;
		public $impressum;
		public $extent;
		public $bibladvice;
		public $comment;
		public $totalextent;
		public $library;
		public $library_href;
    public $othercharts;
		public $author;
		public $author_href;
		public $mapuri;
    public $firstmap_thumbnail;
		public $sysnr;
    public $mainobj;
	} // ZBZMRESULT








	function parse_ZBZMresult($html)
	#################################
	{
		$R = new ZBZMRESULT;

    define($multiplevalues,true);
    define($singlevalue,true);
 		$html = str_get_html($html);

    // Extract thumbnail
    $R->firstmap_thumbnail = get_html_thumbnail_pic( $html->find('table',6) ); // search in 6th table for the thumbnail

  	// Parse Titel
		$table = $html->find('table',7); // at position 7 comes the data table

		$R->title = clean_html( get_html_value('Titel',singlevalue,true,$table) );
		//fontprint("<br> Titelxxx: ((".($R->title)."))",'red');

		// Parse Kartendaten
		$mapdata = get_html_value('Kartendaten',singlevalue,false,$table);
		//fontprint("<br> mapdata: ((".$mapdata."))",'green');
    $mapinfo=split_map_info($mapdata);
    $R->mapscale=$mapinfo[0];
    $R->maprect=polyLatLog($mapinfo[1]);

		//fontprint("<br> mapscale: ((".$R->mapscale."))",'blue');
		//fontprint("<br> maprect: ((".$R->maprect."))",'blue');
    // See GPolygon (http://code.google.com/intl/de-CH/apis/maps/documentation/reference.html#GPolygon)

		// Parse impressum
		$R->impressum = clean_html( get_html_value('Impressum',singlevalue,true,$table) );
		$R->impressum = trim(str_replace(",","",str_replace("&nbsp;","",($R->impressum))));
		//fontprint("<br> impressum: ((".$R->impressum."))",'red');

		// Parse Umfang
		$R->extent = clean_html( get_html_value('Umfang',singlevalue,false,$table) );
		//fontprint("<br> extent: ((".$R->extent."))",'green');

		// Parse BiblNachweis
		$R->bibladvice = clean_html( get_html_value('Bibl. Nachweis',singlevalue,false,$table) );
		//fontprint("<br> bibladvice: ((".$R->bibladvice."))",'red');

		// Parse Notiz
		$R->comment = clean_html( get_html_value('Notiz',singlevalue,false,$table));
		//fontprint("<br> comment: ((".$R->comment."))",'red');

		// Parse Gesamtbestand
		$R->totalextent = clean_html( get_html_value('Gesamtbestand',singlevalue,false,$table) );
		//fontprint("<br> totalextent: ((".$R->totalextent."))",'blue');

		// Parse Bibliothek
		$R->library = clean_html(get_html_value('Bibliothek',singlevalue,false,$table));
		//fontprint("<br> library: ((".$R->library."))",'red');

		$R->library_href = get_html_value('Bibliothek',singlevalue,true,$table,true);
		//fontprint("<br> library_href: ((".$R->library_href."))",'red');

		// Parse SchlagwZBKarten
		$R->othercharts = get_html_value('Schlagw. ZB-Karten',singlevalue,true,$table);
		//fontprint("<br> othercharts: ((".$R->othercharts."))",'red');
		// ACHTUNG: Hier werden mehrere Zeilen ausgegeben und zwar als LISTE!!!
		// XXXXXXXXXXXXXXXXXXXXXX Dieser Punkt muss weiter ausgebaut werden!

		// Parse Autor
		$R->author = clean_html( get_html_value('Autor\/-in',singlevalue,true,$table) );
		$R->author_href = get_html_value('Autor\/-in',singlevalue,true,$table,true);
		//fontprint("<br> author: ((".$R->author."))",'red');
		//fontprint("<br> author_href: ((".$R->author_href."))",'red');
		$R->author_href=str_replace("javascript:open_window(\"","",trim($R->author_href)); // eliminate the javascript call
    $R->author_href=str_replace("\");","",$R->author_href);


		// Parse DigiObject(s) of current Result:
    $MAPS = get_html_zbzmaps($table);
    //foreach($MAPS as $mapinfo)
    //{ list($maptitle,$mapurl) = $mapinfo;
    //  fontprint("<br> $maptitle: $mapurl",'green');}
		$R->mapuri = $MAPS;

    // Parse Systemnr
		$R->sysnr = get_html_value('Systemnr',singlevalue,false,$table);
		//fontprint("<br> sysnr: ((".$R->sysnr."))",'green');

		return $R;
	}





  function open_and_confirm_zbz_manuscript($mapuri)
  #####################################################
  #
  # Return the url of the map (open the map and klick the link)
  #
  {

     $HTML = get_file_content($mapuri);
     $Pattern = "/a href=\"http:\/\/(.+?)\"><img src=\"http/";

    $res = preg_match($Pattern,$HTML,$match);
    if ($res)
    {
 			//print "<br>open_and_confirm_zbz_manuscript:";
      $i=-1;
    	foreach($match as $m)
      { $i++;
      	//print "<br>$i: m=".$m;
      }
    }
     return "http://".$match[1];
  }



 function get_html_value($token,$multiple,$in_href,&$table,$href=false)
	##################################
  #
  # Extract values wich matches $token in &$table
  # &$table is a simple html object
  #
  # if $multiple then search will take all objects and
  # return an array instead of a single value.
  #
	{
		if($table)
		{
			$TRs = $table->find('tr');
			$tr_num=-1;
			foreach($TRs as $TR)
			{
				$tr_num++;
				$td_num=-1;
				foreach($TR->find('td') as $td)
				{
					$td_num++;
					$TEXT = trim($td->plaintext);
					if (preg_match("/$token/",$TEXT))
					{
						//print "<br> found $token:";
						if ($in_href) // extract from href
						{
							$AHREF = $TR->find('a',0);
							if ($href)
								$value=$AHREF->href;
							else
								$value=$AHREF->plaintext;
						} // if
						else
						{
							$TD2=$TR->find('td',1);
							$value=$TD2->plaintext;
						}
						$stop=true;
						break;
					} // if
					if ($stop) break;
				} // for
				if ($stop) break;
			} // for
				$RES = (trim($value));
		}
		return     $RES;
	} //get_html_value



  function get_html_thumbnail_pic(&$table)
  #########################################
  {
    $AHREF = $table->find('img',0);
     $value="http://opac.nebis.ch".$AHREF->src;
    	$RES = (trim($value));
      if (!$RES) // leer??
      $RES= "http://www.google.de/options/icons/localmobile.gif";

		return     $RES;
  } //get_html_thumbnail_pic





	function get_html_zbzmaps(&$table)
	##################################
  #
  # Extract map values wich matches $token in &$table
  # &$table is a simple html object
  #
  # if $multiple then search will take all objects and
  # returns an array [(maptitle,mapurl_confirmed,thumbnail_url),...] instead of a single value.
  #
	{ $maptoken='Digitales Objekt'; //From this row repeat until not $excl_token
    $excl_token= 'Systemnr.';

		$TRs = $table->find('tr');
		$tr_num=-1;
		foreach($TRs as $TR)
		{
			$tr_num++;
			$td_num=-1;
			//foreach($TR->find('td') as $td)
      $td = $TR->find('td',0); // alway in first ele
      {
				$td_num++;
				$TEXT = trim($td->plaintext);
        $fetch=false;
        if ($REPEAT)
        {
        	if (preg_match("/$excl_token/",$TEXT))
	        {
            $stop=true;
            $REPEAT=false;
            break;
          }
          else
            $fetch=true;
        } // REPEAT
        else //(!$REPEAT)
        {
        	if (preg_match("/$maptoken/",$TEXT))
	        {
	          $REPEAT=true;
            $fetch=true;

	        } // if
        } // !$REPEAT
        #############################
				if ($fetch)
        {   $fetch=false;
        		$AHREF = $TR->find('a',0);
            $maptitle=trim($AHREF->plaintext);
	          $mapurl=str_replace("javascript:open_window(\"","",trim($AHREF->href)); // eliminate the javascript call
            $mapurl=str_replace("\");","",$mapurl);
            $mapurl_confirmed=open_and_confirm_zbz_manuscript($mapurl);

	          $RESULT[]=array($maptitle,$mapurl_confirmed);
	          //print "<br> <b>$tr_num/$td_num</b> fetch  (trim($maptitle),trim($mapurl)";
        }
        if ($stop) break;
			} // first td ele
      if ($stop) break;
		} // for

		return $RESULT;
	} //get_html_zbzmaps


	/**
	 * Calculates the "ideal" dimensions for the map icon. That is the
	 * maximum width and height for the icon to remain within the boundaries
	 * set by the $maxImageWidth and $maxImageHeight parameteres.
	 *
	 * @param unknown_type $imageUrl
	 * @param unknown_type $maxImageWidth
	 * @param unknown_type $maxImageHeight
	 * @return an array (width, height)
	 */
	function getIdealDimensions($imageUrl, $maxImageWidth, $maxImageHeight) {
		/*list($imageWidth, $imageHeight, $type, $attr) = getimagesize($imageUrl);

		$imageAspect = $imageWidth/$imageHeight;
		$maxImageAspect = $maxImageWidth/$maxImageHeight;

		if ($imageAspect > $maxImageAspect) { //the image is wider
			$imageWidth = $maxImageWidth;
			$imageHeight = round($imageWidth / $imageAspect);
		} else { // the image is narrower
			$imageHeight = $maxImageHeight;
			$imageWidth = round($imageHeight * $imageAspect);
		}

		return array($imageWidth, $imageHeight);*/
		return array(35, 35);
	}
	
	
	/*********************************************************************************
	 * HTML Scraping functions
	 ******************************************************************************* */
	
	/**
	 * Extracts the results from the HTML DOM object in parameter.
	 * It returns an empty table if no results.
	 * 
	 * @param DOM object $html  
	 */
	function extractNbOfResultsFromHTML($html, $m) {
		$results = array();

		$resultTable = $html->find('table[id=resultlist]', 0);
		
		if ($resultTable) {
			$resultRows = $resultTable->find('tr');

			foreach($resultRows as $row) {
				if (count($results) >= $m)
				break;
					
				if (!$row->class) {
					if (stripos($row->find('td', 2), 'Kartenmaterial') !== false) {
						$results[] = scanResultFromTRow($row);
						$nbOfResultsSaved++;
					}
				}
			}
		}
		
		return $results;
	}
	
	/**
	 * Will return a ZBZSingleResult object from a result's TR.
	 * 
	 * The map's description is given in a different page, which can be
	 * reached following the link on the "Title" column (#5). The link
	 * is set using javascript.
	 * 
	 * @param DOM object $row
	 */
	function scanResultFromTRow($row) {
		$holdingLinkTd = $row->find('td', 4);
		$script = $holdingLinkTd->find('script', 0)->innertext;
		preg_match('/<A HREF=(?P<url>.*)>\d+/', $script, $matches);
		
		$url = $matches['url'];
		$singleResult = new ZBZSingleResult($url);
		
    	return $singleResult;
	}
	
	/**
	 * A single map from ZBZ.
	 * 
	 * @author Javier Belmonte
	 */
	class ZBZSingleResult {
    	public $mainobj;
		public $title;
		public $author;
		public $author_href;
		public $comment;
		public $impressum;
    	public $firstmap_thumbnail;
		public $mapuri;
		public $mapdata;
	    public $mapscale;
    	public $maprect;
		public $totalextent;
		public $library;
		public $library_href;
		public $sysnr;
		public $extent;
		public $bibladvice;
    	public $othercharts;
		
		function __construct($url) {
			$htmlContent = get_file_content($url);
			$html = str_get_html($htmlContent);

			$tableWithDescriptions = $html->find('table', 5);
			
			// MAINOBJ : Points to the URL with this map's description
			$this->mainobj = $url;
			// TITLE : Extract from the link in the first row
			$titleArray = explode('[Kartenmaterial]', $this->scanDataInTableRow($tableWithDescriptions, 'Titel'));
			$this->title = trim($titleArray[0]); 
			$patterns = array('{<span class=text3 id=normalb>}', '{</span>}');
			$this->title = clean_html(preg_replace($patterns, '', $this->title));
			// AUTHOR : Extract from the row named 'Autor/-in' or 'Autor/-in (p.)' 
			$this->author = $this->scanDataInTableRow($tableWithDescriptions, 'Autor/-in');
			$this->author_href = $this->scanFirstLinkInTableRow($tableWithDescriptions, 'Autor/-in');
			// COMMENT : Extract from the row named 'Notiz'
			$this->comment = $this->scanDataInTableRow($tableWithDescriptions, 'Notiz');
			// IMPRESSUM
			$this->impressum = $this->scanDataInTableRow($tableWithDescriptions, 'Impressum');
			$this->impressum = trim(str_replace(",","",str_replace("&nbsp;","",($this->impressum))));
			// THUMBNAIL (can't find the thumbnails)
			$this->firstmap_thumbnail = 'http://www.google.de/options/icons/localmobile.gif';
			// MAP URI
			$this->mapuri = get_html_zbzmaps($tableWithDescriptions);
			// MAP DATA, SCALE & RECT
			$this->mapdata = $this->scanDataInTableRow($tableWithDescriptions, 'Kartendaten');
			$this->scanMapInfo($this->mapdata);
			// EXTENT & TOTAL EXTENT
			$this->extent = $this->scanDataInTableRow($tableWithDescriptions, 'Umfang');
			$this->totalextent = $this->scanDataInTableRow($tableWithDescriptions, 'Gesamtbestand');
			// LIBRARY
			$this->bibladvice = $this->scanDataInTableRow($tableWithDescriptions, 'Bibl. Nachweis');
			$this->library = $this->scanDataInTableRow($tableWithDescriptions, 'Bibliothek');
			$this->library_href = $this->scanFirstLinkInTableRow($tableWithDescriptions, 'Bibliothek');
			// OTHER
	    	$this->sysnr = $this->scanDataInTableRow($tableWithDescriptions, 'Systemnr');
	    	$this->othercharts = $this->scanDataInTableRow($tableWithDescriptions, 'Schlagw. ZB-Karten');
		}
		
		/**
		 * @param DOMObject $table
		 * @param string $name
		 */
		private function scanFirstLinkInTableRow($table, $name) {
			$link = '';
			$record = false;
			
			foreach ($table->children as $child) {
				if ($child->tag == 'tr') {
					$rowName = trim($child->find('td', 0)->innertext);
					if ($rowName != '&nbsp;') {
						if (strripos($rowName, $name) === false)
							$record = false;
						else
							$record = true;
					}
					
					if ($record) {
						if ($child->find('td',1)->children) {
							foreach ($child->find('td a') as $linkPiece) {
								$href = $linkPiece->href;
								$match = preg_match('/javascript:open_window\("(?P<realLink>.*)"\);/', $href, $matches);
								$link = $match ? $matches['realLink'] : $href;
								
								if ($link != '')
									return $link;
							}
						}
					}
				}
			}
			
			return $link;
		}
		
		/**
		 * @param DOMObject $table
		 * @param string $name
		 */
		private function scanDataInTableRow($table, $name, $separator = '; ') {
			$data = "";
			$record = false;
			
			foreach ($table->children as $child) {
				if ($child->tag == 'tr') {
					$rowName = trim($child->find('td', 0)->innertext);
					if ($rowName != '&nbsp;') {
						if (strripos($rowName, $name) === false)
							$record = false;
						else
							$record = true;
					}
					
					if ($record) {
						if ($child->find('td',1)->children) {
							foreach ($child->find('td', 1)->children as $dataPiece) {
								if (trim($dataPiece->innertext) != '' && trim($dataPiece->innertext) != '&nbsp;')
									$data .= $dataPiece->innertext . $separator;
							}
						} else {
							$rowData = trim($child->find('td', 1)->innertext);
							$data .= $rowData . $separator;
						}
					}
				}
			}
			
			$data = substr($data, 0, - strlen($separator));

			return $data;
		}
		
		/**
		 * Sets scale and rect info from the string in the description table
		 * @param string $info
		 */
		private function scanMapInfo($info) {
			$scaleSubPattern = '(\d:(\d+(,|\s)?)+|Versch. Massstäbe|Nicht massstabgetreu)';
			$scalePattern = "(Scale|\[Ca\.)?(?P<scaleInfo>$scaleSubPattern)(\])?";
			$interSubPattern = '(&nbsp;|&nbsp;.*&nbsp;)';
			$coordinatesSubPattern = '\(.*\)';
			$pattern = "/$scalePattern((?P<other>$interSubPattern)(?P<rectInfo>$coordinatesSubPattern))?/";
			
			preg_match($pattern, $info, $matches);
			
			$this->mapscale = $matches['scaleInfo'];
			$this->maprect = $this->coordsToSW2NEDiagonal($matches['rectInfo']);
		}
		
		/**
		 * Transforms the coordinate string recovered from the description table
		 * into a south-west to north-east diagonal of Google JavaScript API compatible
		 * coordinates.
		 * 
		 * @param string $coords
		 */
		private function coordsToSW2NEDiagonal($coords) {
			$pattern = "/(?P<h1>E|W)\b(?P<h1Deg>.+)°((?P<h1Min>.+)(&rsquo;|ʹ))?((?P<h1Sec>.+)\")?"
				. "-+(?P<h2>E|W)\b(?P<h2Deg>.+)°((?P<h2Min>.+)(&rsquo;|ʹ))?((?P<h2Sec>.+)\")?"
				. "\/\s?(?P<v1>N|S)\b(?P<v1Deg>.+)°((?P<v1Min>.+)(&rsquo;|ʹ))?((?P<v1Sec>.+)\")?"
				. "-+(?P<v2>N|S)\b(?P<v2Deg>.+)°((?P<v2Min>.+)(&rsquo;|ʹ))?((?P<v2Sec>.+)\")?/";
				
			preg_match($pattern, $coords, $matches);

			// ZBZ Map coordinates are always given:
			// - The horizontal (h) ones from W to E
			// - The vertical (v) ones from N to S
			$westBoundary = $this->toGoogleLatLong($matches['h1'], intval($matches['h1Deg']),
				intval($matches['h1Min']), intval($matches['h1Sec']));
			$eastBoundary = $this->toGoogleLatLong($matches['h2'], intval($matches['h2Deg']),
				intval($matches['h2Min']), intval($matches['h2Sec']));
			$northBoundary = $this->toGoogleLatLong($matches['v1'], intval($matches['v1Deg']),
				intval($matches['v1Min']), intval($matches['v1Sec']));
			$southBoundary = $this->toGoogleLatLong($matches['v2'], intval($matches['v2Deg']),
				intval($matches['v2Min']), intval($matches['v2Sec']));
			
			$sWCorner = $southBoundary . ', ' . $westBoundary;
			$nECorner = $northBoundary . ', ' . $eastBoundary; 
			
	     	$sW2NEDiagonal = "$sWCorner + $nECorner";
	     	
	     	return $sW2NEDiagonal;
		}
		
		/**
		 * The Google Maps JavaScript API considers latitudes to lie between -90 and +90 degrees,
		 * and longitudes to lie between -180 and +180 degrees. No use for the "E/W/N/S" usually
		 * used instead of negative values.
		 * 
		 * @param string $direction
		 * @param float $degrees
		 * @param float $minutes
		 * @param float $seconds
		 */
		private function toGoogleLatLong($direction, $degrees, $minutes, $seconds) {
			$degreesOnly = $degrees + $minutes/60 + $seconds/3600;
			
			switch ($direction) {
				case 'W':
				case 'S':
					return -$degreesOnly;
					break;
				default:
					return $degreesOnly;
			}
		}
	}
	
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