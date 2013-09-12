<?php

$filename="$RODINSEGMENT/app/u/FRIutilities.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
{
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}
}
#######################################

class RODIN_DB {
	private $DB_HOST;
	private $DB_UNAME;
	private $DB_PWORD;
	private $DB_DB;

	public $DBconn;

	public function __construct($DB='rodin')
	{
		$DEBUG=0;
		
		global $RODINSEGMENT;
		global $RODINDB_HOST;
		global $RODINDB_DBNAME;
		global $RODINDB_USERNAME;
		global $RODINDB_PASSWD;
		global $RODINPOSHDB_DBNAME;
		global $RODINPOSHDB_HOST;
		global $RODINPOSHDB_USERNAME;
		global $RODINPOSHDB_PASSWD;
		global $CAN_ACCESS_ADMIN_VAR;

		#print "FRIdbUtilities: RODINSEGMENT=$RODINSEGMENT, RODINPOSHDB_DBNAME=$RODINPOSHDB_DBNAME";

		switch ($DB) {
			case 'posh':
				$this->DB_HOST 	= $RODINPOSHDB_HOST;
				$this->DB_DB 		=	$RODINPOSHDB_DBNAME;
				$this->DB_UNAME =	$RODINPOSHDB_USERNAME;
				$this->DB_PWORD =	$RODINPOSHDB_PASSWD;
				break;
			default: // Also for 'rodin'
				$this->DB_HOST 	=	$RODINDB_HOST;
				$this->DB_DB 		=	$RODINDB_DBNAME;
				$this->DB_UNAME =	$RODINDB_USERNAME;
				$this->DB_PWORD =	$RODINDB_PASSWD;
				break;
		}
		$errors="";
		if ($CAN_ACCESS_ADMIN_VAR) {
			try {
				$DBconn = mysqli_connect($this->DB_HOST,$this->DB_UNAME,$this->DB_PWORD,$this->DB_DB) or $errors = $errors . "Could not connect to database.\n";
				$this->DBconn=$DBconn;
				if ($DEBUG) print "<br>RODIN_DB called with ({$this->DB_HOST},{$this->DB_UNAME},{$this->DB_PWORD},{$this->DB_DB}) returns: ({$DBconn})";
				if ($DEBUG && $DBconn==null) print "<br>ERROR: ZERO CONNECTION";
			}
			catch (Exception $e)
			{
				inform_bad_db($e);
			}
		}
	}

	//'mysqli://benutzerasswort@server/datenbank'
	public function close()
	{
		global $CAN_ACCESS_ADMIN_VAR;
		if ($CAN_ACCESS_ADMIN_VAR) // otherwise we have no db yet  (installing)
			@mysqli_close($this->DBconn);
	}
} // DB









function dblog($dbloglevel,$widget,$message)
####################################################
#
# Writes message in db for debugging purposes
#
{
	global $DB_GENERAL_LOG_LEVEL;

	if ($dbloglevel <= $DB_GENERAL_LOG_LEVEL)
	{

		try {
			$DB = new RODIN_DB('rodin');
			$DBconn=$DB->DBconn;

			$Q=<<<EOQ
INSERT into dblog_client
(`widget`,`message`)
values ('$widget','$message')
EOQ;

			$resultset = mysqli_query($DB->DBconn,$Q);
			if (mysqli_affected_rows()<1)
						throw(New Exception(mysqli_error($DB->DBconn)."<hr>Query:".$Q."<br><br>"));
			$DB->close();
		}
		catch (Exception $e)
		{
			inform_bad_db($e);
		}
	}
}




####################################################
####################################################
function store_widget_results(&$DecodedSearchresults)
####################################################
####################################################
#
# Stores results in DB
# returns the number of stored results
# FRI 28.11.2012
# STORING EACH RESULT ALSO IN SOLR !!
#
{
  //require_once('RodinResult/RodinSOLRResult.php');
	global $DBconn;
	global $FONTRED;
	$ERROR=false;
	$debug=0;
	//print "<br><b>store_widget_results</b><br><br>";
	//var_dump($DecodedSearchresults); echo "<hr />";
	//print "<hr />";

	try {
		$searchid = $DecodedSearchresults->searchid;
		$q = $searchid->q; //query
		$m = $searchid->m; // number of results
		$sid = $searchid->sid;
		$datasource = $searchid->datasource;
		
		if ($debug)
		{
			print "<br>store_widget_results:<br>";
			print "<br>sid=$sid";
			print "<br>datasource=$datasource";
		}

		$DB = new RODIN_DB();
		$DBconn=$DB->DBconn;

		// Is there ALREADY a result relative to sid in query?
		$countSidInSearchTable = "SELECT count(*) FROM SEARCH WHERE SEARCH.sid = '$sid';";
		$sid_ret = mysqli_query($DB->DBconn, $countSidInSearchTable);
		
		if ($sid_ret)
		{
			/* seek to row no. 0 */
		  mysqli_data_seek($sid_ret, 0);
	    /* fetch row */
	    $row = mysqli_fetch_row($sid_ret);
			$count_sid=$row[0];
			$sid_exists = ($count_sid == 1);
			//$sid_exists = (mysql_result($sid_ret,0) == 1);
		}
		if ($sid_exists) {} // Do nothing, results are already in DB
		else { // Insert query results in the DB 
			$dbq = addslashes($q);
			
			$insertSearchInDb = "INSERT INTO SEARCH (`sid`,`query`) values('$sid','$dbq');";
		
			if ($debug)
				print "<br />sid+ datasource + q = $sid + $datasource + $q<br />DB_SEARCH_INSERT: $insertSearchInDb<br>";

			$qresultDB_SEARCH_INSERT = mysqli_query($DB->DBconn,$insertSearchInDb);
			
			if (mysqli_affected_rows()<1)
				throw(New Exception(mysqli_error($DB->DBconn)."<hr>Query:".$insertSearchInDb."<br><br>"));
		}
		
		$DB_RESULT_INSERT = "INSERT INTO result (`sid`,`datasource`,`xpointer`,`node`,`follow`,`attribute`,`type`,`value`,`url`,`visible`) ";
		$DB_RESULT_INSERT .= "values ('$sid','$datasource','-1','nor','cr','nor','integer','$m', '','false')";
	
		$result = $DecodedSearchresults->result;
		$cnt = count($result);
		$elements = false;
		
		if ($m>0) { // non empty results
			for($i=0;$i<$cnt;$i++) { // for each result element
				$elements=true;
				$xpointer=$result[$i]->xpointer;
				$row=$result[$i]->row;

				//print "<br />xpointer=$xpointer, row=($row)";

				if ($row)
				foreach ($row as $num=>$ext_otv) {
					$attr   =	$ext_otv[0];
					$type		=	$ext_otv[1];
					$value	=	cleanup($ext_otv[2]);
					$url		=	cleanup($ext_otv[3]);
					$visible	=	$ext_otv[4];
					$visible	=	$visible==1?'true':'false';
					$node		=	$ext_otv[5];
					$follow		=	$ext_otv[6];

					$DB_RESULT_INSERT.=", ('$sid','$datasource','$xpointer','$node','$follow','$attr','$type','$value', '$url',$visible)";

          if (!solr_store_result($sid,$datasource,$xpointer,$node,$follow,$attr,$type,$value,$url,$visible))
          {
            fontprint ("ERROR STORING RESULT IN SOLR",'red');
          }

					//print "<br />&nbsp;&nbsp;&nbsp; $attr is a $type with value $value and url $url and visible=$visible";
				}
			}

			if ($elements) {
				$DB_RESULT_INSERT.=';'; // end of query if query nonnempty
				
				if ($debug) {
					print "<hr />";
					print "DB_RESULT_INSERT:<br>$DB_RESULT_INSERT<hr>";
				}

				$qresultDB_RESULT_INSERT = mysqli_query($DB->DBconn,$DB_RESULT_INSERT);

				if (mysqli_affected_rows()<1)
					throw(New Exception(mysqli_error($DB->DBconn)."<hr>Query:".$DB_RESULT_INSERT."<br><br>"));
			}
		}
	} catch (Exception $e) {
		$ERROR=1; // return value: 0 results
		inform_exc($e);
	}
	
	$DB->close();
	
	if ($debug)
		exit;

	if ($ERROR)
		$res = -1;
	else
		$res = $cnt + 1;

	return $res;
} // store_widget_results



####################################################
####################################################
function collect_resultattributes(&$DecodedSearchresults)
####################################################
####################################################
#
# Stores results in DB
# returns the number of stored results
#
{
	global $DBconn;
	$ERROR=false;
	$debug=0;
	//print "<br><b>collect_resultattributes</b><br><br>";
	//var_dump($DecodedSearchresults); echo "<hr />";
	//print "<hr />";
	$searchid= $DecodedSearchresults->searchid;
	$q=$searchid->q;  //query
	$m=$searchid->m; // number of elems in object
	$sid=$searchid->sid;
	$datasource=$searchid->datasource;
	$displaytype='c'; //c=collapsed, e=exploded
	$elements=false;

	if ($m>0) // non empty results
	{
		//print "<br>m: $m ";
		$result=$DecodedSearchresults->result;
		$cnt=count($result);

		for($i=0;$i<$cnt;$i++) // for each result element
		{
			$elements=true;
			$xpointer=$result[$i]->xpointer;
			$row=$result[$i]->row;

			if ($row)
			foreach ($row as $num=>$ext_otv)
			{
				$attr	=	$ext_otv[0];
				$type	=	$ext_otv[1];
				$visible=	$ext_otv[4];
				$node	=	$ext_otv[5];
				if ($visible)
				{
					//print "<br>$attr->$displaytype";
					$ATTRIBUTES{$attr} = $displaytype;

				}
			}
		}
	} //m

	return $ATTRIBUTES;
} // collect_resultattributes

/**
 * Gets the result count from the record with XPointer -1.
 */
function get_result_count($sid, $datasource) {
	$resultCount = 0;

	try {
		$DB = new RODIN_DB();
		$DBconn=$DB->DBconn;
			
		// Get the result count from xPointer -1
		$query = "SELECT value FROM result WHERE sid = '$sid' AND datasource='$datasource' AND xpointer='-1' LIMIT 0 , 1";
		$resultset = mysqli_query($DB->DBconn,$query);
		
		if ($resultset) {
			$row = mysqli_fetch_assoc($resultset);
			$resultCount = $row['value'];
		}
		
		$DB->close();
	} catch (Exception $e) {
		$ERROR=true;
		inform_exc($e);
	}

	return $resultCount;
}

/**
 * Builds an associative array of EATV results.
 *  - The key being the root of each result document xPointer.
 */
function build_result_EATV_array($sid, $datasource) {
	$result = array();
	
	try {
		$DB = new RODIN_DB();
		$DBconn = $DB->DBconn;
		
		// Construct the row again
		$query = "SELECT * FROM result WHERE sid = '$sid' AND datasource='$datasource'";
		$resultset= mysqli_query($DB->DBconn, $query);
			
		if ($resultset)	{
			while ($row = mysqli_fetch_assoc($resultset)) {
				$xpointer = $row['xpointer'];
					
				if ($xpointer > -1) {
					$attribute = $row['attribute'];
					$type = $row['type'];
					$value = redirty($row['value']);
					$url = $row['url'];
					$visible = $row['visible'];
					$node = $row['node'];
					$follow = $row['follow'];

					$contains_q = ($visible && contains_query_parts($value,$q));

					if ($visible) {
						$contains_q = contains_query_parts($value,$q);
					} else {
						$contains_q = 0;
					}

					$eatv_record = new EATV($xpointer, $node, $follow, $attribute, $type, $value, $url, $visible, $contains_q);
						
					$xlevel = compute_XPointerLevel($xpointer);
					$xroot = computeXPointerRoot($xpointer);
						
					if (isset($result[$xroot])) {
						$result[$xroot][] = $eatv_record;
					} else {
						$result[$xroot] = array($eatv_record);
					}
				}
			}
		}
		
		$DB->close();
	} catch (Exception $e) {
		$ERROR=true;
		inform_exc($e);
	}
	
	return $result;
}

/**
 * Uniformed HTML rendering of search results for a single datasource.
 * 
 * If $render: - "all", print all visible result lines.
 *             - "token", print only the visible result lines containing part of the query.
 *             - "min", print only the first visible result lines containing part of the query,
 *               or in case there isn't any, print the title.
 * If $format: - "xml", render as XML and return.
 * 
 * @author Fabio Ricci
 * @deprecated
 */
function render_widget_results($sid, $datasource, $slrq, $mode=RDW_widget, $render='all', $format='html') {
	global $FONTRESULT, $ENDFONTRESULT;
	global $APP_ID, $WIDGET_ID, $TAB_DB_ID, $USER_ID;
	global $WIDGET_SEARCH_MAX;
	global $COLOR_WIDGET_RESULT_SEPARATION, $COLOR_WIDGET_MARKRESULT, $COLOR_WIDGET_MARKRESULT2;
	global $COLOR_WIDGET_UNMARKRESULT, $COLOR_WIDGET_RESULT_BG;
	global $_w, $nosrc, $m;

	$datasourcename = $datasource;

	$saved_userprefs_for_this_widget_application = get_prefs($USER_ID, $REQ_APP_ID, $datasource);
	$attributes_str = get_attribute_displays_str($saved_userprefs_for_this_widget_application);
	$attribute_display = make_atributes_prefs_assoc($attributes_str);

	$q = get_query($sid);

	if ($q == null) {
		// React as if no results had been found, although it is the
		// search which has not been registered
		print "<p id='widget_user_warn' class='widgetResultCount'>" . lg("lblGotNoResults") . "</p>";
	} else {
		$numberOfResults = get_result_count($sid, $datasource);
			
		// Print number of results if necessary
		if ($format == 'html') {
			if ($numberOfResults == 0) {
				print "<p class='widgetResultCount'>" . lg("lblGotNoResults") . "</p>";
			} else {
				if (intval($numberOfResults) > 1) {
					print "<p class='widgetResultCount' id='$numberOfRecordsPElementId'>" . lg("lblNumberOfResultsFound", $numberOfResults) . "</p>";
				} else {
					print "<p class='widgetResultCount' id='$numberOfRecordsPElementId'>" . lg("lblOneResultFound") . "</p>";
				}

				if ($debug) fontprint("sid = $sid","#eeeeee");
			}
		}

		$result = build_result_EATV_array($sid, $datasource);
			
		$HTML_ROW = array();
		$rowcnt = -1;
		$at_least_one_line_to_show = false;

		$nonRenderableAttributes = array('MainRes', 'MapURI', 'postingdate');
			
		// Parse result items
		$rowcnt= -1;
		$res_number= -1;
	  
		foreach ($result as $resultitem) {
			$want_result=true;
			$res_number++;
			$OLD_LEVEL1_XPOINTER='';
			$at_least_one_line_to_show=false;
			$ersatz_eatv=null;
			$last_eatv=null;
				
			if ($resultitem)
			foreach($resultitem as $eatv) { // Achtung: Markiert wenn enth. Query part!
				$eatv->toshow=false;

				if ($eatv->visible && trim($eatv->value)<>'' && $eatv->attribute<>'nor') {
					$rowcnt++;
					 
					if ($render<>'all'
					&&  $eatv->visible
					&&  (!is_in_bag($eatv->attribute, $nonRenderableAttributes))
					&&  (strtolower($eatv->attribute)==strtolower('Title')
					|| 	strtolower($eatv->attribute)==strtolower('Summary')
					|| 	strtolower($eatv->attribute)==strtolower('Description')
					|| 	trim($eatv->attribute)<>'nor') // egal eigentlich???
					&&  (!$ersatz_eatv)) {

						$ersatz_eatv = $eatv;
					}

					if ($render=='token' && $eatv->contains_query_parts) { // shortcut: SHOW ONLY THIS ATTRIBUTE!!
						$eatv->toshow=true;
					} elseif ($render=='all' && $eatv->attribute <> 'nor') {
						$eatv->toshow=true;
					} elseif ($render=='min'
					&& (!$at_least_one_line_to_show)
					&& $eatv->contains_query_parts) { // show only the first line
						$eatv->toshow=true;
					}

					if ($eatv->toshow)
					$at_least_one_line_to_show = true;
				}
			}

			if ($render<>'all') {
				if (!$at_least_one_line_to_show ) {
					if (trim($ersatz_eatv->attribute) && trim ($ersatz_eatv->value)) {
						$ersatz_eatv->toshow = true;
						$want_result = true;
					} else
					$want_result = false;
				}
			} else if ($eatv->attribute=='nor') {
				$want_result=false;
			}

			if ($want_result) {
				$wanted_results[]=$resultitem;
			}
		}

		$rowcnt= -1;
		$rescnt= -1;
		 
		if (count($wanted_results))
		foreach ($wanted_results as $resultitem) {
			$rescnt++;
			$OLD_LEVEL1_XPOINTER='';

			$forcount= -1;
			$mainlink='';

			if ($resultitem)
			foreach($resultitem as $eatv) {
				$forcount++;
					
				if ($forcount==0) {
					$mainlink=$eatv->url;
					$HTML_ROW[] = array('newcell',$mainlink);
				}
					
				$rowcnt++;
				$LEFT_PART=$RIGHT_PART='';

				$XLEVEL=compute_XPointerLevel($eatv->xpointer);

				$zeige=false;
					
				if ($eatv->toshow) {
					$LEFT_PART = $eatv->attribute;
					$CLEAN_RIGHT_PART = $eatv->value; // for semantic refinement

					if ($render<>'all') {
						if($eatv->type<>'url' )	{
							$eatv->url=$mainlink;
							$eatv->type='forced_url';
						}
					}

					if ($eatv->type=='url')	{
						$RIGHT_PART = "<a href=\"".($eatv->url)."\" title='Click to open the document in new tab:"
						. "$url' target=\"_blank\">".($eatv->value)."</a>";
					} else if ($eatv->type=='forced_url') {
						$RIGHT_PART = "<a href=\"".($eatv->url)."\" title='Click to reach the document in new tab:"
						. "$url' target='_blank'>".$eatv->value."</a>";
					} else if (strtolower($eatv->type)=='string' ||
					strtolower($eatv->type)=='num'	    ||
					strtolower($eatv->type)=='georect'	 ||
					strtolower($eatv->type)=='integer'	   ||
					strtolower($eatv->type)=='date'	    ||
					strtolower($eatv->type)=='yyyy') { // print normally or inside a url

						$LIMITED_URL=$eatv->value;
							
						if($eatv->url) {
							$RIGHT_PART =<<<EOR
									<span class='defaulthref'>
										<a href="{$eatv->url}"
											title='Click to open the result in new tab: {$eatv->url}'
											target="_blank">
											$FONTRESULTURL{$LIMITED_URL}$ENDFONTRESULTURL
										</a>
									</span>
EOR;
											$CLEAN_RIGHT_PART = $eatv->value; // for sem refinement
						} else { // print normal
							$RIGHT_PART =  cleanup_for_html( ( $eatv->value ) );
						}
					} else if (strtolower($eatv->type)=='img') {
						$RIGHT_PART =  "<img src=\"".cleanup_for_html( $eatv->url )."\">";
					} else if (strtolower($eatv->type)=='base64html') {
						$RIGHT_PART =  base64_decode( cleanup_for_html ( $eatv->url ) );
					} else {
						$RIGHT_PART =  "System error: Unknown type(".$eatv->type.")";
					}

					$CR = true;
					if ($rownum>1) {
						$old_eatv = $HTML_ROW[$rownum - 1][3]; // old eatv

						if ($render=='all' && $old_eatv->follow == 'bl'		// only render==all: if the old row had a bl, join the right value. nothing more
						&& 	$old_eatv->node == $eatv->node) {	// the previous row should be joined with this right part.
							$PREVIUS_RIGHT_PART=$HTML_ROW[$rownum - 1][2];
							$PREVIUS_RIGHT_PART.=" ".$RIGHT_PART;
							$HTML_ROW[$rownum - 1][2]=$PREVIUS_RIGHT_PART; // insert in the old row the value with a blank
							$CR = false;
						}
					}

					if ($CR) { // Normal insertion (if nonempty)
						if ($RIGHT_PART) {
							$HTML_ROW[] = array($XLEVEL, $LEFT_PART, $RIGHT_PART, $CLEAN_RIGHT_PART, $eatv, $attribute_display{$eatv->attr} );
							$rownum++;
						}
					}
				}
			}
		}
			
		$HTML_ROW[] = array('endcell','');

		if ($format=='xml') {
			##############################
			##############################
			##############################
			// XML Rendering
			##############################
			##############################
			##############################

			$RESPONSE=<<<EOR
					<datasourcename>$datasource</datasourcename>
					<numberofresults>$numberofresults</numberofresults>
					<response>
EOR;

			$rowcnt= -1;
			$rescnt= -1;

			foreach ($HTML_ROW as $SINGLE_ROW) {
				$l=0;$rowcnt++;

				$IDTABLE="$datasource.$rowcnt";
				$eatv = $SINGLE_ROW[4];
				$DISPLAY = $SINGLE_ROW[5];

				if ($SINGLE_ROW[0] == 'newcell' || $SINGLE_ROW[0] == 'endcell') {
					// new cell
					if ($rowcnt>0 && $SINGLE_ROW[0]=='newcell') {
						$RESPONSE.='
								</document>
								<document>';
					} else if ($SINGLE_ROW[0]=='newcell') {
						$RESPONSE.='
								<document>';
					} else if ($SINGLE_ROW[0]=='endcell') {
						$RESPONSE.='
								</document>';
					}

				} else {
					// cell details
					$XLEVEL=$SINGLE_ROW[$l++];
					$LEFT_PART=$SINGLE_ROW[$l++];
					$RIGHT_PART=$SINGLE_ROW[$l++];
					$CLEAN_RIGHT_PART=$SINGLE_ROW[$l++];
					$eatv=$SINGLE_ROW[$l++];
					$type=$eatv->type;

					$coded_value=base64_encode($RIGHT_PART);

					$RESPONSE.=<<<EOR
							<record>
								<name>$LEFT_PART</name>
								<type>$type</type>
								<value><![CDATA[$coded_value]]> </value>
							</record>
EOR;
				}
			}

			$RESPONSE.=<<<EOR
					</response>
EOR;

		} else if ($format=='html') {
			##############################
			##############################
			##############################
			// HTML Rendering
			##############################
			##############################
			##############################

			print<<<EOP
					<table class="widgetResultsListTable">
EOP;
			$rowcnt= -1;
			$rescnt= -1;

			foreach ($HTML_ROW as $SINGLE_HTML_ROW) {
				$l=0;$rowcnt++;

				$IDTABLE="$datasource.$rowcnt";
				$eatv = $SINGLE_HTML_ROW[4];
				$DISPLAY = $SINGLE_HTML_ROW[5];

				if ($SINGLE_HTML_ROW[0] == 'newcell' || $SINGLE_HTML_ROW[0] == 'endcell') {
					// New cell
					$rescnt++;
					$IDSEM="$datasource.sem.$rescnt";
					$IDRES="$datasource.res.$rescnt";

					$AHREF='';

					if ($SINGLE_HTML_ROW[1]) {
						// is there a url to put on the table to clickopeninnewtab?
						//URL existent? => put it in table cell
						$TABLE_OPTS=<<<EOT
								style="display:block;height:100%"
EOT;
						$TABLEOPTS_PLUS=<<<EOT
								onmouseover="setbgcolor('{$IDTABLE}_1','$COLOR_WIDGET_MARKRESULT');"
								onmouseout="setbgcolor('{$IDTABLE}_1','$COLOR_WIDGET_UNMARKRESULT');"
								onclick="window.open('{$SINGLE_HTML_ROW[1]}','_blank')"
								title='Click to expand result in Widget: {$SINGLE_HTML_ROW[1]}'
								style="display:block;"
EOT;
						$TABLEOPTS_MINUS=<<<EOT
								onmouseover="setbgcolor('{$IDTABLE}_1','$COLOR_WIDGET_MARKRESULT');"
								onmouseout="setbgcolor('{$IDTABLE}_1','$COLOR_WIDGET_UNMARKRESULT');"
								onclick="window.open('{$SINGLE_HTML_ROW[1]}','_blank')"
								title='Click to collapse result in Widget: {$SINGLE_HTML_ROW[1]}'
								style="display:block;"
EOT;
						$LEFTBAR_OPTS=<<<EOT
								onmouseover="setbgcolor('{$IDTABLE}_2','$COLOR_WIDGET_MARKRESULT');"
								onmouseout="setbgcolor('{$IDTABLE}_2','$COLOR_WIDGET_UNMARKRESULT');"
								onclick="window.open('{$SINGLE_HTML_ROW[1]}','_blank');return false;"
								title='Click to open result in new tab: {$SINGLE_HTML_ROW[1]}'
								style=""
EOT;
					} else {
						$TABLEOPTS='';
					}

					if ($rowcnt>0 || $SINGLE_HTML_ROW[1]=='endcell') {
						if ($render<>'min')
						$SEPARATOR="<tr height=1>"
						. "<td colspan=3><hr style=\"color:$COLOR_WIDGET_RESULT_SEPARATION;height: 1px; border-spacing: 1px;\"></td>"
						. "</tr>";
						else
						$SEPARATOR="";

						print <<<EOP
										</table>
									</td>
									$SEMANTIC_TD
								</tr>
								$SEPARATOR
EOP;
					}
					// start new table for all results in it:
					//design a left bar to expand/collapse/open result
					if( $SINGLE_HTML_ROW[0] == 'newcell') {
						$RESULTBAR_TD = make_vertical_documentbar_td($IDTABLE,$SINGLE_HTML_ROW[1],($rescnt + 1),"$COLOR_WIDGET_MARKRESULT2","$COLOR_WIDGET_UNMARKRESULT");
						print <<<EOP
								<tr>$RESULTBAR_TD
									<td>
										<table class="widgetResultsDocumentTable">
EOP;
					}
				} else {
					// Cell details
					$XLEVEL=$SINGLE_HTML_ROW[$l++];
					$LEFT_PART=$SINGLE_HTML_ROW[$l++];
					$RIGHT_PART=$SINGLE_HTML_ROW[$l++];
					$CLEAN_RIGHT_PART= $SINGLE_HTML_ROW[$l++];

					if (!$nosrc) {
						if (strlen($CLEAN_RIGHT_PART) > 1024) {
							// we truncate the text and send a string slightly longer than 1024
							$base64_RIGHT_PART=base64_encode(substr($CLEAN_RIGHT_PART, 0, 1024) . "XXX");
						} else {
							$base64_RIGHT_PART=base64_encode($CLEAN_RIGHT_PART);
						}

						$base64_q=base64_encode($q);
						$toggle=!$toggle;
						$IDRESITEM="$datasource.$rescnt.$rowcnt";
						$qs_params.="&recordcnt=".$rowcnt."&xpointer=".$eatv->xpointer."&quickvector=".$base64_RIGHT_PART."&quickq=".$base64_q."&sid=".$sid."&maxdur=".$WIDGET_SEARCH_MAX."&c=s";
						//NUR das item!!!
						$newtab_name="refine_".cleanup_datasource_name($datasourcename); // hier name eintragen?

						if ($eatv->type != 'url') {
							$SEMlink = "var cParent = (typeof parent.isIndexConnected == 'undefined') ? window.opener : parent; cParent.rodin_zen_filter('$base64_RIGHT_PART', '$base64_q', document.getElementById('spotlight-box-$IDRESITEM'));";
						} else {
							$SEMlink = '';
						}
						$REFTITLE = lg("titleLaunchZenFilter");
					} else {
						$SEMlink='';
					}

					print design_html_tr_record($XLEVEL,$IDRES, $IDRESITEM,
					$CLEAN_RIGHT_PART, $SEMlink, $REFTITLE, $LEFT_PART, $RIGHT_PART,
					$DISPLAY, $IDTABLE, $toggle, $_w, $render);
				}
			}
		}
	}

	if ($format=='xml') {
		return $RESPONSE;
	} else {
		return $ERROR;
	}
}



function cleanup_datasource_name($datasourcename)
{
	//Entferne RDW. oder auch RDW.BAR.
	$PATTERN[]="/RDW\.(\w+)\.(.*)/";
	$ERSETZUNG[]='$2';
	$PATTERN[]="/RDW_(.*)/";
	$ERSETZUNG[]='$1';
	$datasource_cleaned=preg_replace($PATTERN,$ERSETZUNG,$datasourcename);
	return $datasource_cleaned;
}




/**
 * Used by the GoogleBooks and ViatImages widgets, which don't use
 * the stantdard widget rendering.
 */
function make_semlink_td($SEMlink,$Text,$qs_params) {
	global $ZEN_FILTER_ICON;

	$titlesem="$Text";
	$semarrow_size=15;
	$semcode=<<<EOS
		<td id='$IDSEM'	width='$semarrow_size' valign='top'
			title='$titlesem' onclick="$SEMlink;">
			
			<a 	href='' onclick="return false" target='_blank' title='$titlesem'>
				<img src='$ZEN_FILTER_ICON' border=0 width=$semarrow_size height=$semarrow_size/>
			</a>
		</td>
EOS;
	return $semcode;
}






function get_xml_widget_response($sid,$wid,$slrq,$render)
############################################
{
	$WIDGETINFO = collect_widget_infos($wid);
	$url = $WIDGETINFO[0]['url'];
	$path_parts=pathinfo($url);
	$datasource=$path_parts['filename'];


	//print "Calling render_widget_results($sid,$datasource,0,'token','xml')";


	if (!$datasource)
		$RESPONSE="
<error>No data source found for wid=$wid</error>
";
	else
		$RESPONSE=render_widget_results($sid,$datasource,$slrq,0,$render,'xml');

/*
	$RESPONSE=<<<EOT
	  <response>
	    <document>
	      <record>
	        <name>(test1(the record=attribute name))</name>
	        <type>(test1(type definition for this record))</type>
	        <value>
<![CDATA[(test1(value for this record))     ]]>
        </value>
	     </record>
	   	</document>
	    <document>
	      <record>
	        <name>(test2(the record=attribute name))</name>
	        <type>(test2(type definition for this record))</type>
	        <value>
<![CDATA[(test2(value for this record))     ]]>
        </value>
	     </record>
	   	</document>
   </response>
EOT;
	*/

	return $RESPONSE;
}







/*
 * Checks if RODIN's components are alive and are connected
 * returns true (in case RODIN might work) or a message to display 
 * DB 
 * SOLR
 */
function rodin_service_diagnostics()
{
	//print "rodin_service_diagnostics ...";
  include_once("SOLRinterface/solr_interface.php");
  $ok=true;
  $now=date("d.m.Y H:i:s");
  global $USER;
  global $RODINSEGMENT;
  global $RODINIMAGESURL;
	global $RESULTS_STORE_METHOD;
  
  $old_ERROR_REPORTING = error_reporting(NULL);
  
  $noofproblems=0;
  $USERNAME = $_SESSION['longname'];
  $SERVER = $_SERVER['SERVER_NAME'];

	$IS_HEGDMZ=(strstr($SERVER,'195.176.237.62'));
	$IS_WEBDMS=(strstr($SERVER,'82.192.234.100'));

  $icon="<img src=\"$RODINIMAGESURL/icon_working.gif\"></img>&nbsp;";
  $prefix="<table border=0>"
     ."<tr><td>$icon</td><td><b>RODIN</b> diagnostics self check at <b>$now</b> user \"<b>$USERNAME</b>\" (<b>$USER</b>)"
     ." on Server \"<b>$SERVER</b>\" segment \"<b>$RODINSEGMENT</b>\":</td></tr>"
          ;
  
  $title_contact="Click to send an email message to this person";
  $title_mantis="Click to issue a mantis task on this issue";

  
  if ($RODINSEGMENT<>'eng')
  {
  //Test Internet connection
  $internet_ok = check_internetconn();
  if (!$internet_ok)
  {
    //print " problem INTERNET";
    $LOCALREASON='Internet access';
    $MALFUNCTION_REASON.=$MALFUNCTION_REASON?', ':'';
    $MALFUNCTION_REASON.=$LOCALREASON;
    $noofproblems++;
    $message.="<tr height=15/>"
            ."<tr><td/><td><span class=\"error\">$LOCALREASON</span> from inside server $SERVER seems to fail</td></tr>"
            ."<tr><td/><td><span class=\"errorexplanation\">(This means, that almost no widget will provide you with search results)</span></td></tr>";
  }
  
	  if ($RESULTS_STORE_METHOD == 'solr') {
	    //Test SOLR connectability
	    $SOLR_COLLECTIONS=array(  'rodin_result',
	                              'rodin_search',
	                              'cached_rodin_widget_responsed',
	                              'cached_rodin_src_response',
	                              'zbw_stw',
	                              'gesis_thesoz'  );
	    
	    list($problemtext,$solr_connected) = test_solr_connected($SOLR_COLLECTIONS);
	    if (!$solr_connected)
	    {
	      $LOCALREASON='SOLR connectivity';
	      $MALFUNCTION_REASON.=$MALFUNCTION_REASON?', ':'';
	      $MALFUNCTION_REASON.=$LOCALREASON;
	      $noofproblems++;
	      $message.="<tr height=15/>"
	              ."<tr><td/><td><span class=\"error\">$LOCALREASON</span> seems to fail - is SOLR <b>running</b> on this server?</td></tr>"
	              //."<tr><td/><td>$problemtext</td></tr>"
	              ."<tr><td/><td><span class=\"errorexplanation\">(This is fatal to RODIN: no search possible)</span></td></tr>";
	    }
	}
    //Test SOLR http accessability:
    list($problemtext,$solr_http_queryable) = solr_collection_http_access();
    if (!$solr_http_queryable)
    {
      $LOCALREASON='SOLR local http access';
      $MALFUNCTION_REASON.=$MALFUNCTION_REASON?', ':'';
      $MALFUNCTION_REASON.=$LOCALREASON;
      $noofproblems++;
      $message.="<tr height=15/>"
              ."<tr><td/><td><span class=\"error\">$LOCALREASON</span> seems to fail</td></tr>"
              ."<tr><td/><td><span class=\"errorexplanation\">(This is fatal to RODIN: no search possible)</span></td></tr>";
    }
  }
  
  
  if ($noofproblems>0) // add icon and address
  {
     $subject="Malfunction ($MALFUNCTION_REASON) RODIN/$RODINSEGMENT/$USERNAME on Server $SERVER_IP at $now";

    if (!$internet_ok) 
    {
      $EVTL_CENTRE_INFO = "<tr><td><a class=\"admicontact\" title=\"$title_contact\" href=\"mailto:heginfo@hesge.ch?subject=$subject\">HEG - Info (HES)</a></td><td> tel. +41-22-3881777 </td></tr>";
    }
    
    $suffix="<tr height=15/>"
          ."<tr><td/><td>Please retry later or contact one of the following persons: </td></tr>"
          ."<tr><td/><td><table>"
          .$EVTL_CENTRE_INFO
          .($IS_HEGDMZ?"<tr><td><a class=\"admicontact\" title=\"$title_contact\" href=\"mailto:eliane.blumer@hesge.ch?subject=$subject\">Eliane Blumer</a></td><td> tel. +41-22-3881850 </td></tr>":'')
          .($IS_HEGDMZ?"<tr><td><a class=\"admicontact\" title=\"$title_contact\" href=\"mailto:javier.belmonte@hesge.ch?subject=$subject\">Javier Belmonte</a></td><td> tel. +41-22-3881796 </td></tr>":'')
          .($IS_HEGDMZ?"<tr><td><a class=\"admicontact\" title=\"$title_contact\" href=\"mailto:fabio.ricci@ggaweb.ch?subject=$subject\">Fabio Ricci</a></td><td> tel. +41-76-5281961 </td></tr>":'')
          .($IS_WEBDMS?"<tr><td><a class=\"admicontact\" title=\"$title_contact\" href=\"mailto:webdms@ggaweb.ch?subject=$subject\">WebDMS GmbH - Fabio Ricci</a></td><td> tel. +41-76-5281961 </td></tr>":'')
            ."</table></td></tr>"
          ."<tr><td/><td>or <a class=\"admicontact\" target=\"blank\" title=\"$title_mantis\" href=\"$WEBROOT/mantis/bug_report_advanced_page.php?summary=$subject\">issue a mantis task</a></td></tr>"
          ."<tr height=15/>"
          ."<tr><td colspan=2><a class=\"admicontact\" onclick=\"javascript:window.location.reload()\" title=\"click to reload RODIN\" href=\"#\">Retry</a></td></tr>"
          ."</table>"
          ;
    
    
    
    
    $message=$prefix.$message.$suffix;
    
  }

  error_reporting($old_ERROR_REPORTING);

  return array($message,$noofproblems==0,$noofproblems);
}









/**
 * Returns the level of the element denoted by $xpointer
 * Example: "2" -> 1
 *          "3.1" -> 2
 *          "1.1.2" -> 3
 */
function compute_XPointerLevel($xpointer) {
	return count(explode('.',$xpointer));
}

/**
 * Returns the root of the given $xpointer.
 */
function computeXPointerRoot($xpointer) {
	$exploded = explode('.', $xpointer);
	
	if (count($exploded) == 0) {
		return $xpointer;
	} else {
		return $exploded[0];
	}
}


/**
 * Produces the TR element for the HTML export of the search results.
 */
function design_html_tr_record($XLEVEL, $IDRES, $IDROW,
	$CLEAN_RIGHT_PART, $SEMlink, $TITLEREFINE, &$LEFT_PART, &$RIGHT_PART,
	$DISPLAY, $IDTABLE, $toggle, $_w, $render) {
		
	global $FONTRESULT;
	global $ENDFONTRESULT;
	global $RODINUTILITIES_GEN_URL, $RODINIMAGESURL;
	global $ZEN_FILTER_ICON, $ZEN_ICON_WIDTH, $ZEN_ICON_HEIGHT;
	global $COLOR_PAGE_BACKGROUND, $COLOR_WIDGET_RESULT_SEPARATION;
	global $COLOR_WIDGET_MARKRESULT, $COLOR_WIDGET_MARKRESULT2, $COLOR_WIDGET_UNMARKRESULT;

	$_w -= 30;		//html record are shown approx. 30 pix to the right

	if (preg_match("/href/",$RIGHT_PART)) {
			$sdom = str_get_html($RIGHT_PART); //simplehtml
			$A = $sdom->find('a');
			$RIGHT_PART_MINIMIZED=$RIGHT_PART;
			// extract href-label and shorten it
			$RIGHT_PART_MINI=trim(cleanup_for_html($A[0]->innerText()));
			//$RIGHT_PART_MINI='Link';
			$RIGHT_PART_MINIMIZED = "<a href=".$A[0]->href." target=_blank>$RIGHT_PART_MINI</a>";
	}
	else
		$RIGHT_PART_MINIMIZED = cleanup_for_html($RIGHT_PART); // prepare minimized result text

	$icon_size=12;
	$icon_size_wx=2;
	$icon_size_hx=12;

	//$PADDINGLEFT = max(($XLEVEL - 1),0) * 10; //Tiefe aus Struktur - wir wollen aber verzichten zu guensten Lesbarkeit
	$PADDINGLEFT = 3;


	$ICONCOLLAPSE	="$RODINUTILITIES_GEN_URL/images/white.PNG";
	$ICONEXPAND		="$RODINUTILITIES_GEN_URL/images/white.PNG";
	$ICONNOSORT		="$RODINUTILITIES_GEN_URL/images/icon_nosort.png";
	$ICONSORTUP		="$RODINUTILITIES_GEN_URL/images/icon_sortup.png";
	$ICONSORTDOWN	="$RODINUTILITIES_GEN_URL/images/icon_sortdown.png";
	$TITLESORTUP	="Sort on $LEFT_PART ascending";
	$TITLESORTDOWN="Sort on $LEFT_PART descending";
	$TITLENOSORT	="Do not sort on $LEFT_PART";
	$TITLEEXPAND	="Expand $LEFT_PART result item";
	$TITLECOLLAPSE="Collapse $LEFT_PART result item";
	$ICONREFINE		=$ZEN_FILTER_ICON;


	if ($DISPLAY=='c')
	{
		$TITLESTART = $TITLECOLLAPSE;
		$ICONSTART = $ICONCOLLAPSE;
	}
	else if ($DISPLAY=='e')
	{
		$TITLESTART = $TITLEEXPAND;
		$ICONSTART = $ICONEXPAND;
	}

	if ($SEMlink<>'')
	{
	$REFINE="<a id='zen$IDROW'
		class='zenlink'
		href='#' ".<<<EOR
		onclick="$SEMlink"
		title='$TITLEREFINE'
	><img id='{$IDRES}_XX'
			src='$ICONREFINE'
			border=0
			width="$ZEN_ICON_WIDTH"
			height="$ZEN_ICON_HEIGHT" /></a>
EOR;
	}
	else $REFINE="<img id='{$IDRES}_XX'
		src='$RODINIMAGESURL/white.PNG'
		border=0
		width=$ZEN_ICON_WIDTH
		height=$ZEN_ICON_HEIGHT />";

	if ($render=='min') $SPACENOWRAP="white-space:nowrap;";
	$TITLE=" title='$RIGHT_PART_MINI' ";

	/*
	if ($toggle && false) //FRI-Neue Anforderung RS 20100630
	{
		$RESULTSTYLE=" bgcolor=#eeeeee";
		$RESULTSTYLE_TD=" style=\"background-color:#eeeeee;padding-left:{$PADDINGLEFT}px\"";
		$HIDDENSTYLE_TD=" style=\"background-color:#eeeeee;visibility:hidden;\"";
	}
	else //Das zieht immer:
	*/

	{
		$RESULTSTYLE=" bgcolor=$COLOR_WIDGET_RESULT_BG";
		$RESULTSTYLE_TD=" style=\"background-color:$COLOR_WIDGET_RESULT_BG;padding-left:{$PADDINGLEFT}px;$SPACENOWRAP\"";
		$HIDDENSTYLE_TD=" style=\"background-color:$COLOR_WIDGET_RESULT_BG;visibility:hidden;word-wrap:normal;$SPACENOWRAP\"";
	}
	//$padding="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";

	$RIGHT_PART_ENHANCED=enhance_bc_divs($CLEAN_RIGHT_PART);

	// [Javier] Here is where the code for the spotlight empty divs is added.
	$HTML=<<<EOH
		<tr>
			<td align="left" valign="top"> {$REFINE} </td>
			<td colspan="2" valign=top $RESULTSTYLE_TD width="$_w">
			<div class="spotlightbox" style="visibility:hidden;" id="spotlight-box-$IDROW" title=""></div>
			{$FONTRESULT}{$RIGHT_PART_ENHANCED}{$ENDFONTRESULT}</td>
		</tr>
EOH;

	return $HTML;
}






	/**
	 * Converts a result term vector into a vector of divs with each div a term
	 * Supposes each term contains a word (no html controls)
	 * @deprecated done by the Result Object itself
	 */
	function enhance_bc_divs($words) {
		$div_class = "bcx-deakt";
		$words_arr = explodeX(Array(".",  "!",  " ",  "?",  ";",  ",",  ":",  "-",  "+",  "/"), $words);
		
		$enhancedWords = array();
		foreach($words_arr as $w) {
			$title = lg("lblActionsOnWord");
			$enhancedWords[]="<lable class=\"$div_class\" "
							."onmouseover=\"if(this.className=='bcx-deakt') this.className='bcx-selected'\" "
							."onmouseout=\"if(this.className=='bcx-selected') this.className='bcx-deakt'\" "
							."title=\"$title\">$w</lable>";
		}

		return implode(' ', $enhancedWords);
	}



	function make_vertical_documentbar_td($IDRES,$RESlink,$recordcnt,$colorOver,$colorOut) {
		global $RODINUTILITIES_GEN_URL;

		$title = lg("titleOpenResult");

		$icon_size=15;
		$code=<<<EOS
			<td id='$IDRES' valign='top' align='right' width=$icon_size title='$title'
				onclick="window.open('$RESlink','_blank');return false;"
				onmouseover="setbgcolor('$IDRES','$colorOver');"
				onmouseout="setbgcolor('$IDRES','$colorOut');">
				
				<span class='recordcnt'> $recordcnt&nbsp; </span>
			</td>
EOS;

		return $code;
	}
















function addDoubleClickProtection($RIGHT_PART,$IDTABLE)
#########################################################
{

	// do we have a URL?
	if (preg_match("/http:/",$RIGHT_PART))
	{
		$cr="\n";
		// Deactivate onmouseover and onclick of the block cell
		// smarter actions may be taken ...
		$DEACTIVATE_TABLE_CLICK_CODE=<<<EOD
		var x=document.getElementById('$IDTABLE');
		if (x) {
			var old=x.onclick;
			x.onclick='alert()';
			x.onmouseover='';
			x.title='Automatic tab opening has been deactivated by using a link. Pls. reload results';
		}

EOD;

		$RIGHT_PART= str_replace('check_disable_activediv_INCOMPLETE',$DEACTIVATE_TABLE_CLICK_CODE,$RIGHT_PART);


	} // URL

	return $RIGHT_PART;
} // addDoubleClickProtection





$u_umlaut=chr(195).chr(131).chr(194).chr(188);

function cleanup($input)
########################
#
# Cleaning up strings
# Used before db store
#
{
	global $u_umlaut;
	//Aus RSS FEED ECONOMY SUISSE:
	$input = str_replace($u_umlaut,'ue',$input);



	/*
	if(preg_match("/Aufgaben(.+)/",$input,$match))
	{
		$str=$match[0];
		$maxlen= strlen($str);

		print "<br>Untersuche $maxlen ".$str."<br>";
		for($x=0; $x < $maxlen ; $x++)
		{
			$c = substr($str,$x,1);
			print "<br>".$c." ".ord($c);
		}

	}
	*/
	$input = trim($input);
	# SPEEDUP: Replace the following str_replace
	# lines by an appropriate ereg_replace expression:
	$input = addslashes($input);
	$input = str_replace('"',' ',$input);
	$input = str_replace("\n",' ',$input);
	$input = htmlentities($input);
	$input = mysqli_real_escape_string($input);
	return $input;
}



function cleanup_for_html($input)
########################
#
# Cleaning up strings
# Used before db store
#
{

	//print "<br>cleanup_for_html((($input)))...";
	$input = trim(($input));

	if (0 && preg_match("/(dell&#039;*Auditorium Parco)/",($input),$match))
	{

		print "<br><b>BINGOOOOO</b> HOCHKOMMA - (((".$match[0]."))) len=".strlen($match[0]);
		print "<br><b>BINGOOOOO</b> HOCHKOMMA - (((".$match[1]."))) len=".strlen($match[1]);
		print "<br><b>BINGOOOOO</b> HOCHKOMMA - (((".$match[2]."))) len=".strlen($match[2]);
		print "<br><b>BINGOOOOO</b> HOCHKOMMA - (((".$match[3]."))) len=".strlen($match[3]);
		$ANA=$match[0];

		$max=strlen($ANA);
		for($i=0;$i<$max;$i++)
		{
			$char=$ANA[$i];
			print "<br>$i: ($char)";

		}
	}
	$input = str_replace("'"," ",$input);
	$input = str_replace("&#039;"," ",$input); //Einfaches Hochkomma aber kodiert
	$input = str_replace('"'," ",$input);
	$input = str_replace("\\"," ",$input);
	$input = str_replace("\n"," ",$input);
	$input = str_replace("\t"," ",$input);
	$input = str_replace("<i>","",$input);
	$input = str_replace("</i>","",$input);
	$input = urldecode($input);

	//print "<br>cleanup_for_html liefert: ((($input)))";

	//$input = mysql_escape_string($input);
	//$input = EscapeShellCmd($input);
	//print "<br>CLEANED==>((($input)))<br><br>";
	return $input;
}





function redirty($str)
########################
#
# inverse to cleanup()
#
{
	 $str = html_entity_decode($str);
	 return $str;
}


function cleanup_strangecharacgers($text)
{

	$text = str_replace ("&auml;",	"�", $text);
	$text = str_replace ("&auml;",	"�", $text);
	$text = str_replace ("&ouml;",	"�", $text);
	$text = str_replace ("&Ouml;", 	"�", $text);
	$text = str_replace ('&uuml;', 	"�", $text);
	$text = str_replace ("&Uuml;", 	"�", $text);
	$text = str_replace ("&szlig;", "�", $text);
	return $text;
}




function get_logger_records($sid)
{
	$logger_records=array();
	try {
		$DB = new RODIN_DB();
		$DBconn=$DB->DBconn;
	
		$Q=<<<EOQ
SELECT * FROM `logger` 
where sid='$sid' 
ORDER BY `id`  ASC
EOQ;

		//print "<br>$Q<br>";

		$resultset = mysqli_query($DB->DBconn,$Q);
		$DB->close();
		if ($resultset)
	  while ($row = mysqli_fetch_assoc($resultset))
	  {
			$logger_records[]=$row;
		}

	}
	catch (Exception $e)
	{
		inform_bad_db($e);
	}
	return $logger_records;
} // get_logger_records




function db_get_topqueries($TOP)
{
	try {
		$DB = new RODIN_DB();
		$DBconn=$DB->DBconn;

    $Q=<<<EOQ
SELECT sid, query
FROM search
ORDER BY sid DESC
LIMIT $TOP
EOQ;

    $resultset = mysqli_query($DB->DBconn,$Q);
		$DB->close();

    while ($row = mysqli_fetch_assoc($resultset))
    {
         $sid =$row['sid'];
         $query =$row['query'];
         //print "<br> HOP $query $sid ";
         if ($sid)
			$SEARCH[]=array($sid,$query);
 		}

	}
	catch (Exception $e)
	{
		inform_bad_db($e);
	}
	return $SEARCH;
}









function fetch_record($Q,$DBID='rodin')
##########################
{
	$resultset=null;
	try {
		$DB = new RODIN_DB($DBID);
		$DBconn = $DB->DBconn;

		$ret = mysqli_query($DBconn,$Q);

		if ($ret!=null)
			$resultset = mysqli_fetch_assoc($ret);
		
		$DB->close();
	}
	catch (Exception $e)
	{
		inform_bad_db($e);
	}
	
	return $resultset;

} // fetch_record



function fetch_records($Q,$DBID='rodin')
##########################
{
	$resultset=null;
	try {
		$DB = new RODIN_DB($DBID);
		$DBconn = $DB->DBconn;

		$ret = mysqli_query($DBconn,$Q);
	
		if ($ret!=null)
		{
			while ($record = mysqli_fetch_assoc($ret))
					$records[] = $record;
			;
		}
		$DB->close();
	}
	catch (Exception $e)
	{
		inform_bad_db($e);
	}
	return $records;
} // fetch_records




function get_query($sid)
/**
 * Returns the query made for a particular SID, or null
 * if no search was made with such ID.
 */
{
  global $RESULTS_STORE_METHOD;
  switch($RESULTS_STORE_METHOD)
  {
    case 'mysql':
          get_query_DB($sid);
          break;
    case 'solr':
          get_query_SOLR($sid);
          break;
  }
}


/**
 * Returns the query made for a particular SID, or null
 * if no search was made with such ID.
 */


function get_query_SOLR($sid)
{

  global $SOLR_RODIN_CONFIG;
  global $USER;
  
  if (!$USER) print "System error: get_query_SOLR() USER is null!";
  
  $allResults = array();

  $solr_user= $SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['user'];
  $solr_host= $SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['host']; //=$HOST;
  $solr_port= $SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['port']; //=$SOLR_PORT;
  $solr_path= $SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['path']; //='/solr/rodin_result/';

  #Fetch result from SOLR
  $solr_select= "http://$solr_host:$solr_port$solr_path".'select?';

  try {

   //$solr_result_query_url=$solr_select."wt=xml&q=sid:$sid&wdatasource:$datasource&qt=/lucid&req_type=main&user=$solr_user&role=DEFAULT";
    $solr_result_query_url=$solr_select
        ."wt=xml"
        ."&q=sid:$sid%20user:$USER"
        ."&rows=1"
        ."&fl=query"
        ."&omitHeader=true"
        ;
    $filecontent=file_get_contents($solr_result_query_url);
    $solr_sxml= simplexml_load_string($filecontent);
    #print "<hr>SOLR_QUERY: <a href='$solr_result_query_url' target='_blank'>$solr_result_query_url</a><br>";
    #print "<hr>SOLR_CONTENT: <br>(((".htmlentities($filecontent).")))";
    #print "<hr>SOLR_RESULT: <br>"; var_dump($solr_sxml);

    $DOCS = $solr_sxml->xpath('/response/result/doc/str'); //find the doc list
    $Query= $DOCS[0];
    #print "<br> Query: $Query";

    return $Query;
  }

  catch (Exception $e) {
		print "<br>get_query_SOLR EXCEPTION! $e";
	}
}






/**
 * Returns the query made for a particular SID, or null
 * if no search was made with such ID.
 */
function get_query_DB($sid) {
	$res = null;
	
	try {
		$DB = new RODIN_DB();
		$DBconn=$DB->DBconn;

		// Check if there is a search with such SID
		$query = "SELECT query FROM SEARCH WHERE SEARCH.sid = '$sid'";
		$sid_ret = mysqli_query($DB->DBconn,$query);
		
		if ($sid_ret!=null) {
			$numrows = mysqli_num_rows($sid_ret);
			
			if ($numrows>0)
			{
				 /* seek to row no. 0 */
  		  mysqli_data_seek($numrows, 0);
		    /* fetch row */
		    $row = mysqli_fetch_row($numrows);
				$res=$row[0];
				mysqli_free_result($numrows);
				//$res = mysql_result($sid_ret, 0);
			}
		}
		
		$DB->close();
	} catch (Exception $e) {
		inform_bad_db($e);
	}
	
	return $res;
}




/**
 * lookup in posh db
 * returns for each matching widgets the inforecord 
 */
function get_matching_widget_records($widgetslabels)
{
	$wds = explode(',',$widgetslabels);
	foreach ($wds as $labelsegment)
	{
		$WWS.=$WWS?' OR ':'';
		$WWS.= 'url like "%'.$labelsegment.'%"';
	}
	
	$Q=<<<EOQ
SELECT id, url, name 
FROM dir_item 
WHERE $WWS 
EOQ;

	$records = get_posh_records($Q);
	
	return $records;	
} // get_matching_widget_records



/**
 * Searches in the DB for SRC's which matches $as_service 
 * returns a triple ($ok,$records,$errortxt)
 * 
 * Author: Fabio Ricci for HEG
 * fabio.ricci@semweb.ch
 * 
 * @param $srcsources - string: comma separated SRC name (segments)
 * @param $userid - int: id of the user to which the src should correspond
 * @param $as_service - string: name corresponding to the services the SRC should be allowed to 
 * Allowed service names are:
 * UsedAsThesaurus 				(allow__UsedAsThesaurus)
 * UsedForAutocomplete 		(allow__UsedForAutocomplete)
 * UsedForSubjects 				(allow__UsedForSubjects)
 * UsedForLODRdfExpansion (allow__UsedForLODRdfExpansion)
 *  
 */
function get_matching_SRC_records($srcsources, $userid, $as_service='')
{
	$DEBUG=0;
	$errortxt='';
	$ok=
		//Servicename correct?
	$servicename_correct = 
		(	$as_service=='UsedAsThesaurus'
		||$as_service=='UsedForAutocomplete'
		||$as_service=='UsedForSubjects'
		||$as_service=='UsedForLODRdfExpansion'
		);
	
	if ($servicename_correct)
	{
		$ids=array();
		$records = array();
		if (trim($srcsources) && $userid)
		{
			$wds = explode(',',$srcsources);
			foreach ($wds as $labelsegment)
			{
				$WWS.=$WWS?' OR ':'';
				$WWS.= 'Name like "%'.$labelsegment.'%"';
			}
$Q=<<<EOQ
SELECT * 
FROM src_interface 
WHERE ($WWS) 
AND forRODINuser = $userid 
AND $as_service = 1
EOQ;
			$records = get_rodin_records($Q);
		}
	} // $servicename_correct
	else {
		$errortxt="as_service ($as_service) unknown - possible values are: [UsedAsThesaurus, UsedForAutocomplete, UsedForSubjects, UsedForLODRdfExpansion]";
		$ok=false;
	}	
	
	if ($DEBUG) {
		print "<br> get_matching_SRC_records($srcsources, $userid, $as_service) => (($Q)) ".count($records). ' records returned <br><br>';
		var_dump($records);
	}
	return array($ok,$records,$errortxt);	
} // get_matching_SRC_records





function get_POSH_user_info($USER_ID ='')
####################################################
{
	global $CAN_ACCESS_ADMIN_VAR;
	
	$DEBUG=0; 
	
	if ($CAN_ACCESS_ADMIN_VAR)
	{
		if ($USER_ID)
			$WHERE=" id = $USER_ID";
		else
			$WHERE=" lastconnect_date = (select max(lastconnect_date) from users) ";
		
		$Q=<<<EOQ
SELECT id,long_name,username,positext,negatext FROM users WHERE $WHERE
EOQ;

 		return fri_fetch_assoc($Q);
	}
	else
		return 'installing...';
}


/**
 * 
 */
function get_resonance_texts_from_poshdb($USER_ID)
{
	if (($user_values=get_POSH_user_info($USER_ID)))
	{
		$positext = $user_values['positext'];
		$negatext = $user_values['negatext'];
	}
	return array($positext,$negatext);
} // get_resonance_textsfrom_poshdb






function fri_get_last_user()
##################################
#
# returns the row with $tab_id
#
{

	$Q=<<<EOQ
SELECT id
FROM users
WHERE lastconnect_date = (
SELECT max( lastconnect_date )
FROM users )
EOQ;

	$U = fri_fetch_assoc($Q);
	$USERID=$U['id'];

	return $USERID;
}






function fri_get_dir_infos($url)
##################################
#
# returns the row of dir_item with $tab_id
#
{
	//cleanup url

	$url = str_replace(".rodin","%",$url);
	$url = str_replace(".php","%",$url);

   $Q=<<<EOQ
SELECT id, url,defvar,name,description,typ,status,format,height,minwidth,sizable,website,editor_id,editor_id,creation_date,lastmodif_date,notation,voter_nb,updated,nbusers,sorting,lang,usereader,autorefresh,views,icon,l10n
FROM dir_item
WHERE url like '%$url?'
EOQ;

	return fri_fetch_assoc($Q);
}





function fri_get_page_infos()
##################################
#
# returns the row with $tab_id
#
{
    $Q=<<<EOQ
SELECT name,type,param,nbcol,showtype,npnb,style,modulealign,position,controls
FROM pages
WHERE id=1
EOQ;


  //print "$Q returns: ".$Q['nbcol'];
	return fri_fetch_assoc($Q);
}


function fri_save_in_phpsession($user_id)
##################################
{
	$_SESSION['rodin_user_id'] = $user_id;

	//var_dump($_SESSION);
	//print "SAVING in SESSION: rodin_user_id=(".$_SESSION['rodin_user_id'].")";
}
function fri_get_phpsession_rodin_user_id()
##################################
{
	//print "GETTING out of SESSION: rodin_user_id=(".$_SESSION['rodin_user_id'].")";
	return $_SESSION['rodin_user_id'];
}



function fri_get_tab_id($TABNAME,$user_id)
##################################
{
	$tab_id= -1; // default

    $Q=<<<EOQ
SELECT id
FROM profile
WHERE name='$TABNAME' AND user_id=$user_id
EOQ;

	$row = fri_fetch_assoc($Q);
	$tab_id =$row['id'];

	if ($DEBUG) print "<hr>fri_get_tab_id($TABNAME,$user_id) ($Q) returns tab_id=$tab_id";

	return $tab_id;
}



function fri_get_users_id()
##################################
{
    $Q=<<<EOQ
SELECT * FROM `users` WHERE typ='I'
EOQ;

		try {
			$DB = new RODIN_DB('posh');
			$DBconn=$DB->DBconn;
			$resultset = mysqli_query($DB->DBconn,$Q);
			$DB->close();

			while ($row = mysqli_fetch_assoc($resultset))
			{
				$users[]= array('id'=>$row['id'],'long_name'=>$row['long_name'],'username'=>$row['username']);
			}

	}
	catch (Exception $e)
	{
		inform_bad_db($e);
	}

	return $users;
}


function fri_get_user_info($user_id)
##################################
{
    $Q=<<<EOQ
SELECT * FROM `users` WHERE typ='I' AND id=$user_id
EOQ;

		try {
			$DB = new RODIN_DB('posh');
			$DBconn=$DB->DBconn;
			if ($DBconn)
			{
				if (!$resultset = mysqli_query($DB->DBconn,$Q))
					fontprint("Problem beim oeffnen der posh DB ",'red');
				$DB->close();

				if ($row = mysqli_fetch_assoc($resultset))
				{
					$users= array('id'=>$row['id'],'long_name'=>$row['long_name'],'username'=>$row['username']);
				}
			}
	}
	catch (Exception $e)
	{
		inform_bad_db($e);
	}

	return $users;
}


function db_logger_delete($sid)
{
	$DBQUERY=<<<EOQ
	DELETE from 
	`logger`
	WHERE sid='$sid';
EOQ;

	$DB = new RODIN_DB('rodin');
	$DBconn=$DB->DBconn;
	$urset = mysqli_query($DB->DBconn,$DBQUERY);
	$affected=mysqli_affected_rows($DB->DBconn);

	return $affected;
} // db_logger_delete


function dblogger($timestamp_prog,$segment,$userid,$username,$sid,$action,$info,$msg)
{
	$DEBUG=0;
	global $RODINDB_HOST;
	if ($RODINDB_HOST) // in some cases is is not set
	{ 
	$msg = addslashes($msg);
	
	if (!$userid) $userid=-1;
	if (!$username) $username='undef'; //this comes when called from an SRC over web - in this case no session is set with user and userid... should be revisited with the new RODIN architecture
	
	
	$DBQUERY=<<<EOQ
	insert into 
	`logger`(`timestamp_prog`,`sid`,`segment`,`userid`,`username`,`action`,`msg`) 
	values	('$timestamp_prog','$sid','$segment',$userid,'$username',$action,'$msg');
EOQ;

	$DB = new RODIN_DB('rodin');
	$DBconn=$DB->DBconn;
	if (!$DBconn)
		print "<br>dblogger(): No Database connected";
	else 
	{
		$urset = mysqli_query($DB->DBconn,$DBQUERY);
		$affected=mysqli_affected_rows($DB->DBconn);
		if ($affected < 0)
		{
			fontprint("<br>dblogger: Problem (affected=$affected) inserting into 'logger' ...<br>(($DBQUERY)))",'red');
		}
		else {
			if ($DEBUG)
				fontprint("<br>dblogger SUCCESS inserting ... <br>(($DBQUERY)))",'green');
		}
	}
	} // $RODINDB_HOST
} // dblogger




function fri_fetch_assoc($Q)
##################################
#
# returns the row with $tab_id
#
{
	try {
			$DB = new RODIN_DB('posh');
			$DBconn=$DB->DBconn;

			if ($DBconn)
			{
				$resultset = mysqli_query($DBconn,$Q);
				$DB->close();
				if($resultset)
				$row = mysqli_fetch_assoc($resultset);
			}
			//print "fri_fetch_assoc: $Q";

	}
	catch (Exception $e)
	{
		inform_bad_db($e." on Q=$Q");
	}

	//print "fri_fetch_assoc returns : "; var_dump($row);

	return $row;
}


function get_posh_records($query)
{
	try {
			$DB = new RODIN_DB('posh');
			$DBconn=$DB->DBconn;
			$resultset = mysqli_query($DB->DBconn,$query);
			$DB->close();

		  if ($resultset)
				while ($row = mysqli_fetch_assoc($resultset))
					$rows[]=$row;

	}
	catch (Exception $e)
	{
		inform_bad_db($e);
	}

	return $rows;
} // get_posh_records






function get_rodin_records($query)
{
	try {
			$DB = new RODIN_DB('rodin');
			$DBconn=$DB->DBconn;
			$resultset = mysqli_query($DB->DBconn,$query);
			$DB->close();

		  if ($resultset)
				while ($row = mysqli_fetch_assoc($resultset))
					$rows[]=$row;

	}
	catch (Exception $e)
	{
		inform_bad_db($e);
	}

	return $rows;
} // get_rodin_records







function fri_get_relevant_widget_info($tab_id,$user_id)
##################################
{

	$query=<<<EOQ
SELECT i.*
FROM module m, dir_item i
where
m.item_id = i.id
and m.profile_id = $tab_id
and m.user_id=$user_id
EOQ;

		try {
			$DB = new RODIN_DB('posh');
			$DBconn=$DB->DBconn;
			$resultset = mysqli_query($DB->DBconn,$query);
			$DB->close();

		  if ($resultset)
				while ($row = mysqli_fetch_assoc($resultset))
				{
					$widgestinfos[]= array('id'=>$row['id'],'url'=>$row['url'],'name'=>$row['name'],'description'=>$row['description']);
				}

	}
	catch (Exception $e)
	{
		inform_bad_db($e);
	}

	return $widgestinfos;
}





function collect_tags($datasource)
##################################
{
	// replace last extenstion token (currently: ".rodin" - may change) to ".php?"
	$datasource_cleaned=preg_replace("/(.*)\.(\w+)$/","$1",$datasource);


	$query=<<<EOQ
SELECT k.label_simplified
from search_keyword k, search_index s, dir_item d
where k.id = s.kw_id
and s.item_id = d.id
AND d.url like "$datasource_cleaned%"
EOQ;
		try {
			$DB = new RODIN_DB('posh');
			$DBconn=$DB->DBconn;
			$resultset = mysqli_query($DB->DBconn,$query);
			$DB->close();

			while ($row = mysqli_fetch_assoc($resultset))
			{
				if ($tags) $tags.=", ";
				$tags.= $row['label_simplified'];
			}

	}
	catch (Exception $e)
	{
		inform_bad_db($e);
	}

	return $tags;
}





function collect_widget_infos($widget_id)
##################################
{
	// replace last extenstion token (currently: ".rodin" - may change) to ".php?"
	$datasource_cleaned=preg_replace("/(.*)\.(\w+)$/","$1",$datasource);


	$query=<<<EOQ
SELECT i.*
FROM module m, dir_item i
where
m.item_id = i.id
and m.item_id = $widget_id
EOQ;
		try {
			$DB = new RODIN_DB('posh');
			$DBconn=$DB->DBconn;
			$resultset = mysqli_query($DB->DBconn,$query);
			$DB->close();

			if ($resultset)
			while ($row = mysqli_fetch_assoc($resultset))
			{
					$widgestinfos[]= array('id'=>$row['id'],'url'=>$row['url'],'name'=>$row['name'],'description'=>$row['description']);
			}

	}
	catch (Exception $e)
	{
		inform_bad_db($e);
	}

	return $widgestinfos;
}


function eraseTagCloud($USER)
##############################
#
# Erases the searches of USER
#
{
  global $RESULTS_STORE_METHOD;
  switch($RESULTS_STORE_METHOD)
  {
    case 'mysql':
          eraseTagCloud_DB($USER);
          break;
    case 'solr':
          eraseTagCloud_SOLR($USER);
  }
}



/**
 * Erases the searches of USER
 *
 * @param string $USER the id of the user making the request
 */
function eraseTagCloud_SOLR($USER) {

  global $SOLR_RODIN_CONFIG;

  $elements=0;
  $solr_user= $SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['user'];
  $solr_host= $SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['host']; //=$HOST;
  $solr_port= $SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['port']; //=$SOLR_PORT;
  $solr_path= $SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['path']; //='/solr/rodin_result/';

  #delete result from SOLR

  $solr_delete= "http://$solr_host:$solr_port$solr_path"."update?stream.body=<delete><query>user:$USER</query></delete>&commit=true";

  try {

   //$solr_result_query_url=$solr_select."wt=xml&q=sid:$sid&wdatasource:$datasource&qt=/lucid&req_type=main&user=$solr_user&role=DEFAULT";
    
    $filecontent=file_get_contents($solr_delete);
    $solr_sxml= simplexml_load_string($filecontent);
    #print "<hr>SOLR_QUERY: <a href='$solr_delete' target='_blank'>".htmlentities($solr_delete)."</a><br>";
    #print "<hr>SOLR_CONTENT: <br>(((".htmlentities($filecontent).")))";
    #print "<hr>SOLR_RESULT: <br>"; var_dump($solr_sxml);

  }

  catch (Exception $e) {
		print "<br>get_query_SOLR EXCEPTION! $e";
	}
}





function eraseTagCloud_DB($USER)
##############################
#
# Erases the searches of USER
#
{
	//CAN BE OPTIMIZED !!!
	//ADD A NEW FIELD USER INTO TABLE search

	$query=<<<EOQ
DELETE
FROM search
WHERE sid like "%.$USER"
EOQ;

	try {
		$DB = new RODIN_DB('rodin');
		$DBconn=$DB->DBconn;
		$resultset = mysqli_query($DB->DBconn,$query);
		$DB->close();
	}
	catch (Exception $e)
	{
		inform_bad_db($e);
	}

	// NOW ERASE also results
	$query=<<<EOQ
DELETE
FROM result
WHERE sid like "%.$USER"
EOQ;

	try {
		$DB = new RODIN_DB('rodin');
		$DBconn=$DB->DBconn;
		$resultset = mysqli_query($DB->DBconn);
		$DB->close();
	}
	catch (Exception $e)
	{
		inform_bad_db($e);
	}






}






/**
 * Returns a vector of the queries launched by a user.
 * 
 * @param string $USER the id of the user making the request
 */
function collect_queries_tag($seg,$user,$sid='') {

   global $RESULTS_STORE_METHOD;
    switch($RESULTS_STORE_METHOD)
    {
      case 'mysql':
            return collect_queries_tag_DB($user);
            break;
      case 'solr':
        		return collect_queries_tag_SOLR($seg,$user,$sid);
    }
}


/**
 * Returns a vector of the queries launched by a user.
 *
 * @param string $seg the rodinsegment
 * @param string $user the user id of the user making the request
 * @param string $sid the sid of a given query (in this case the method returns a single string (not a vector))
 * 
 */
function collect_queries_tag_SOLR($seg,$user,$sid='') {

  global $SOLR_RODIN_CONFIG;
  $queries = array();

  $solr_user= $SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['user'];
  $solr_host= $SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['host']; //=$HOST;
  $solr_port= $SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['port']; //=$SOLR_PORT;
  $solr_path= $SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['path']; //='/solr/rodin_result/';

  #Fetch result from SOLR
  $solr_select= "http://$solr_host:$solr_port$solr_path".'select?';

  try {

		//$evtl_sid=$sid?"sid:$sid%20":'';
		$evtl_sid=$sid?"$sid%20":'';
    $solr_result_query_url=$solr_select
        ."wt=xml"
        ."&q=user:$user%20seg:$seg%20"
        .$evtl_sid
        ."&fl=query"
        ."&omitHeader=true"
        ."&rows=100000"
        ;
    $filecontent=file_get_contents($solr_result_query_url);
    $solr_sxml= simplexml_load_string($filecontent);
//    print "<hr>collect_queries_tag_SOLR: <a href='$solr_result_query_url' target='_blank'>$solr_result_query_url</a><br>";
//    print "<hr>SOLR_CONTENT: <br>(((".htmlentities($filecontent).")))";
//    print "<hr>SOLR_RESULT: <br>"; var_dump($solr_sxml);

    $DOCS = $solr_sxml->xpath('/response/result/doc'); //find the doc list
    foreach($DOCS as $DOC)
    {
      $queries[]= $DOC->str.'';
    }

		if ($sid) // we need just ONE
			$queries=$queries[0];


    return $queries;
  }

  catch (Exception $e) {
		print "<br>get_query_SOLR EXCEPTION! $e";
	}
}




/**
 * Returns a vector of the queries launched by a user.
 *
 * @param string $USER the id of the user making the request
 */
function collect_queries_tag_DB($USER) {
 	// TODO Can be optimized by adding a "user" column to the table
	$queries = array();

	try {
		$DB = new RODIN_DB('rodin');
		$DBconn=$DB->DBconn;
		$resultset = mysqli_query($DB->DBconn,"SELECT Query FROM search WHERE sid like '%.$USER' ORDER BY sid DESC");
		$DB->close();

		while ($row = mysqli_fetch_assoc($resultset)) {
			$queries[] = $row['Query'];
		}
	} catch (Exception $e) {
		inform_bad_db($e);
	}

	return $queries;
}





function contains_query_parts($value,$query)
#################################
#################################
{
	//print "<br>contains_query_parts(<b>$value</b>,$query)";

	if ($query=='') return 0;
	else
	{
		$a = preg_split("/[\s,]+/", $query);

		foreach($a as $query_part)
		{
			if ($query_part<>'' && preg_match("/$query_part/i",strtolower($value)))
			{
				//print " <br>(($value)) <b>contains_query_parts (($query_part))</b>";
				return 1;
			}
		}
		//print " <br>contains_query_parts returning FALSE</b>";

		return 0;
	}
}





function get_attribute_displays_str($prefs)
#################################
#################################
{
	if (preg_match("/attributes=([\w,:]*)&/",$prefs,$match))
	{
		//foreach ($match as $m=>$v) print "<br>match $m=>$v";
		$attributes_str=($match[1]);
	} // match
	else if (preg_match("/&attributes=([\w,:]*)$/",$prefs,$match))
	{
		//foreach ($match as $m=>$v) print "<br>match $m=>$v";
		$attributes_str=($match[1]);
	} // match

	//print "<br>get_attribute_displays_str($prefs) returns:";
	//print "<br>(($attributes_str))";

	return trim($attributes_str);
}

/**
 * Returns a string with the prefs of a widgetinstance
 * In case $REQ_APP_ID is not set (-1) - $widget_id must be set!
 * Searches and (if found) returns the prefs
 * of the first widget instance inside the tab "javaserver" for $USER_ID
 */
function get_prefs($USER_ID,$REQ_APP_ID,$datasource,$widget_id=-1)
{
	$DEBUG=0;
	//FETCH PREFS FROM DB
	
	//In case $REQ_APP_ID is not set, try to discover a widget 
	//For datasource inside a tab named "javaserver"
	if($REQ_APP_ID == -1)
	{
		$tab_id = fri_get_tab_id($TABNAME='javaserver', $USER_ID);
		$widget_uniq = fri_get_widget_uniq_in_tab($tab_id,$widget_id);
		$REQ_APP_ID= $USER_ID.':'.$tab_id.':'.$widget_uniq; // partial!! We do NOT have the WIDGET UNIQUE ID
		if ($DEBUG) print "<br>get_prefs: REQ_APP_ID $REQ_APP_ID ";
	}
		
	$QUERY_GET="
		SELECT queryprefs
		FROM userRDWprefs
		WHERE
		prefsuser = '$USER_ID' AND
		datasource like '$datasource%' 
		AND application_id = '$REQ_APP_ID' ;";

	if ($DEBUG) print "<br>get_prefs($USER_ID,$REQ_APP_ID,$datasource): ($QUERY_GET)";
	$REC=fetch_record($QUERY_GET);

	$prefs= $REC['queryprefs'];
	
	return $prefs;
}


function fri_get_widget_uniq_in_tab($tab_id,$widget_id)
{
	$Q_SELECT=<<<EOQ
SELECT uniq
FROM `module`
WHERE profile_id = $tab_id
AND item_id = $widget_id
EOQ;
	$REC = fri_fetch_assoc($Q_SELECT);
	
	return $REC['uniq'];
} // fri_get_widget_uniq_in_tab




function make_atributes_prefs_assoc($attributes_str)
######################################################
{
	$V=array();
	$ad_pairs = explode(',',$attributes_str);
	foreach ($ad_pairs as $X)
	{
		$pair=explode(':',$X);
		$attr=$pair[0];
		$display=$pair[1];
		$V{$attr}=$display;
	}
	return $V;
}



function register_SRC_REFINE_INTERFACES($USER_ID)
######################################################
#
# Returns a javascript text:
# to be executed setting SRC_REFINE_INTERFACES
# at the main level (index_connected)

{
		//FETCH PREFS FROM DB
			$QUERY_GET="
				SELECT *
				FROM src_interface
				WHERE forRODINuser=$USER_ID
					AND UsedAsThesaurus=1
			";
			$DB = new RODIN_DB('rodin');
			$DBconn=$DB->DBconn;
			//print "<br>$QUERY_GET";
			$resultset = mysqli_query($DBconn,$QUERY_GET);
			$DB->close();
			$start=1;
			while ($REC = mysqli_fetch_assoc($resultset))
			{
				$cnt++;
				$AuthUser	=$REC['AuthUser'];
				$AuthPasswd	=$REC['AuthPasswd'];
				$ID			=$REC['ID'];
				$Protocol	=$REC['Protocol'];
				$Server		=$REC['Server'];
				$Port		=$REC['Port'];
				$Path_Start	=$REC['Path_Start'];
				$Path_Refine=$REC['Path_Refine'];
				$Path_Test	=$REC['Path_Test'];
				$Servlet_Start=$REC['Servlet_Start'];
				$Servlet_Refine=$REC['Servlet_Refine'];
				$Servlet_Test=$REC['Servlet_Test'];
				$Name		=$REC['Name'];
				$SERVICE_URL_REFINE_MODE = $REC['mode'];

				if (preg_match("/(\d+)/",$Servletname,$match))
				{
					$ENGINE=$match[0];
				}

				$SERVICE_URL_BASE="$Protocol://$Server";
				if (is_a_value($Port)) $SERVICE_URL_BASE.=":$Port";
				$SERVICE_URL_START="$SERVICE_URL_BASE/$Path_Start/$Servlet_Start";
				$SERVICE_URL_REFINE="$SERVICE_URL_BASE/$Path_Refine/$Servlet_Refine";
				if ($Servlet_Test)
				{
					$SERVICE_URL_TEST="$SERVICE_URL_BASE/$Path_Test/$Servlet_Test";
					$SERVICE_URL_TEST_INJ = "refine_interface_def['test']='$SERVICE_URL_TEST';";
				}

				if ($start)
				{
					$start=0;
					$AJAX.="
					SRC_REFINE_INTERFACE_TEST='$SERVICE_URL_TEST';
					SRC_REFINE_INTERFACE = new Array();
					";
				}

				$AJAX.="
					refine_interface_def=new Object;
					SRC_REFINE_INTERFACE.push(refine_interface_def);
					refine_interface_def['id']='$ID';
					refine_interface_def['name']='$Name';
					refine_interface_def['engine']='$ENGINE';
					refine_interface_def['url']='$SERVICE_URL_REFINE';
					refine_interface_def['mode']='$SERVICE_URL_REFINE_MODE';
					$SERVICE_URL_TEST_INJ
					refine_interface_def['initialized']=false;
					";
			}
			if ($cnt==0)
				$AJAX.="
					NOSRC=true;
					";
			else
				$AJAX.="
					NOSRC=false;
					";


			return ($AJAX);
}


/**
 * Returns Records actevated for UsedForSubjects iff temporarily_used
 */
function get_active_THESAURI_expansion_sources($USER_ID)
{
	return initialize_SRC_MODULES( $USER_ID , ' AND UsedForSubjects=1 AND temporarily_used=1');
}


/**
 * Returns Records actevated for UsedAsThesaurus iff temporarily_used
 */
function get_active_THESAURI_sources($USER_ID)
{
	return initialize_SRC_MODULES( $USER_ID , ' AND UsedAsThesaurus=1 AND temporarily_used=1');
}
		
/**
 * Returns Records actevated for UsedForSubjects
 * NB: temporarily_used is NOT asked here
 */
		function get_active_LOD_expansion_sources($USER_ID)
{
	//print "<br>Calling LOD sources for $USER_ID:";
	
	return initialize_SRC_MODULES( $USER_ID , ' AND UsedForLODRdfExpansion=1 ');
}

/**
 * Returns Records actevated for autocomplete iff temporarily_used
 */
function get_active_SRC_autocomplete_sources($USER_ID)
{
	$DEBUG=0;
	//print "<br>get_active_SRC_autocomplete_sources for user id $USER_ID:";
	$SRCM= initialize_SRC_MODULES( $USER_ID , ' AND UsedForAutocomplete=1 ');
	if($DEBUG) {print "<br>get_active_SRC_autocomplete_sources($USER_ID):  "; var_dump($SRCM);}
	return $SRCM;
}



/**
 * in case $CONDITION is set, it must be something like
 * " AND column=value "
 */
function initialize_SRC_MODULES( $USER_ID, $CONDITION='' )
######################################################
#
# Returns a javascript text:
# to be executed
# at the main level (index_connected)
{
		$DEBUG=0;
		$LOCALCONDITION=" AND UsedAsThesaurus=1 ";
		$LOCALCONDITION=$CONDITION?$CONDITION:$LOCALCONDITION;
		if (!$USER_ID) fontprint("initialize_SRC_MODULES called with empty USER",'red');
		$AJAX='';
		//FETCH PREFS FROM DB
			$QUERY_GET="
				SELECT *
				FROM src_interface
				WHERE	forRODINuser=$USER_ID
					$LOCALCONDITION
				ORDER BY POS ASC
			";
			
			if ($DEBUG) print "<br>initialize_SRC_MODULES($USER_ID, $CONDITION) = ($QUERY_GET)";
			$DB = new RODIN_DB('rodin');
			$DBconn=$DB->DBconn;
			//print "<br>$QUERY_GET";
			$resultset = mysqli_query($DB->DBconn,$QUERY_GET);
			$DB->close();
			$noOfSRC=0;
			if ($resultset)
			{
			while ($REC = mysqli_fetch_assoc($resultset))
			{
				$noOfSRC++;
				$Name				=$REC['Name'];
				$ID					=$REC['ID'];

				/*
				$AuthUser		=$REC['AuthUser'];
				$AuthPasswd	=$REC['AuthPasswd'];
				$Protocol		=$REC['Protocol'];
				$Server			=$REC['Server'];
				$Port				=$REC['Port'];
				$Path_Start	=$REC['Path_Start'];
				$Path_Refine=$REC['Path_Refine'];
				$sparql_endpoint=$REC['sparql_endpoint'];
				$comment=$REC['comment'];
				$Servlet_Start	=$REC['Servlet_Start'];
				$Servlet_Refine	=$REC['Servlet_Refine'];
				*/
				$RECS[]=$REC;
				//print "<br>Servletname:$Servletname";

				/*
				$SERVICE_URL_BASE="$Protocol://$Server";
				if (is_a_value($Port)) $SERVICE_URL_BASE.=":$Port";
				$SERVICE_URL_START.="/$Path_Start/$Servlet_Start";
				$SERVICE_URL_REFINE.="/$Path_Refine/$Servlet_Refine";
				*/
				$Temporarily_used=$REC['temporarily_used']?$REC['temporarily_used']:0;
				$SRC_REFINE_INTERFACE_SPECS[$ID]=array($Name,$Temporarily_used);
			} // while
			$AJAX.="fri_initialize_src();
";
			}
			$RES_OBJ = array(
					'ajax_init_src_code' => $AJAX,
					'src_interface_specs' => $SRC_REFINE_INTERFACE_SPECS,
					'records' 					=>   $RECS
			);

			return ($RES_OBJ);
}







/**
 * Returns an array with:
 * - [0] the URL for the SRC with the $SERVICE_ID,
 * - [1] the username and [2] password for HTTP authentification
 * 
 * @param unknown_type $Type
 * @param unknown_type $USER_ID
 * @param unknown_type $SERVICE_ID
 */
function get_service_url($Type,$USER_ID,$SERVICE_ID,$PRINT_IT=0) {
	// Get information from the DB
	$QUERY_GET = "SELECT * FROM src_interface WHERE forRODINuser=$USER_ID"
		. " AND ID=$SERVICE_ID";

	if ($PRINT_IT) print "<br>get_service_url($Type,$USER_ID,$SERVICE_NAME)<br>";

	if ($SERVICE_NAME) $QUERY_GET .= " AND Name='$SERVICE_NAME' ";

	if ($PRINT_IT) print "QUERY_GET: <br>$QUERY_GET<br>";

	$REC = fetch_record($QUERY_GET);

	$AuthUser = $REC['AuthUser'];
	$AuthPasswd = $REC['AuthPasswd'];
	$AuthUser = $REC['AuthUser'];
	$Protocol = $REC['Protocol'];
	$Server = $REC['Server'];
	$Port = $REC['Port'];
	$Path_Start = $REC['Path_Start'];
	$Path_Refine = $REC['Path_Refine'];
	$Path_Test = $REC['Path_Test'];
	$Servlet_Start = $REC['Servlet_Start'];
	$Servlet_Refine = $REC['Servlet_Refine'];
	$Servlet_Test = $REC['Servlet_Test'];

	$SERVICE_URL = array();
	$SERVICE_URL[0] = "$Protocol://$Server";
	
	if (is_a_value($Port)) $SERVICE_URL[0] .= ":" . $Port;

	if (strtolower($Type)=='start')
		$SERVICE_URL[0].="$Path_Start/$Servlet_Start";
	else if (strtolower($Type)=='refine')
		$SERVICE_URL[0].="$Path_Refine/$Servlet_Refine";
	else if (strtolower($Type)=='test')
		$SERVICE_URL[0].="$Path_Test/$Servlet_Test";


	if ($AuthUser)
	{
		$SERVICE_URL[]=$AuthUser;
		$SERVICE_URL[]=$AuthPasswd;
	}


	if ($PRINT_IT) {
		print "<br>get_service_url returns:<br>";
		var_dump($SERVICE_URL);
		print "<br>";
	}
	return $SERVICE_URL;
}


function check_correct_app_widget_urls($RODINROOT,$RODINSEGMENT)
#
# For each Widget in table (DB posh): update their url and icon path
# according to the current $RODINROOT/$RODINSEGMENT position
# of the installation
{
	global $WEBROOT,$DOCROOT,$RODINCACHE;
	try {
		$DB = new RODIN_DB('posh');
		$DBconn=$DB->DBconn;

		$Q_SELECT=<<<EOQ
SELECT id, url, website
FROM rodinposh_$RODINSEGMENT.dir_item;
EOQ;
		print "<u>Automatical Widget Update in POSH Database according to current root $RODINROOT/$RODINSEGMENT </u> ((($Q_SELECT))):<br>";
		$resultset= mysqli_query($DB->DBconn,$Q_SELECT);
		while ($row = mysqli_fetch_assoc($resultset))
		{
			$id						=$row['id'];
			$oldurl 			=$row['url'];
			$oldwebsite 	=$row['website'];
			$url					=evtl_adjust_root($oldurl,$RODINROOT,$RODINSEGMENT);
			$website			=evtl_adjust_root($oldwebsite,$RODINROOT,$RODINSEGMENT);
			$XMLcacheItem	="$WEBROOT$RODINCACHE/item_$id.xml";
			$XMLcacheItemW="$DOCROOT$RODINCACHE/item_$id.xml";

			##########################################
			print "<br>Controlling item_$id.xml ...";
			$chacheItemChanged=false;
			$sxml = simplexml_load_string(file_get_contents($XMLcacheItem));
			if ($sxml->url<>$url){
				fontprint( "<br>item_$id.xml:  (update from ".$sxml->url." to <b>$url</b>)", '#229');
				$sxml->url=$url;
				//fontprint( "<br>check$id.xml url:  (updated to ".$sxml->url.")", 'red');
				$chacheItemChanged=true;
			}
			if ($sxml->website<>$website){
				fontprint( "<br>item_$id.xml:  (update from ".$sxml->website." to <b>$website</b>)", '#229');
				$sxml->website=$website;
				//fontprint( "<br>check$id.xml wbsite:  (updated to ".$sxml->website.")", 'red');
				$chacheItemChanged=true;
			}
			if ($chacheItemChanged)
			{
				$h = fopen($XMLcacheItemW,'w');
				fwrite($h, $sxml->asXML());
				fclose($h);
				fontprint( "<br>item_$id.xml written to $XMLcacheItemW", 'black');
			}
			else fontprint( " (no change)", 'black');

			##########################################


			$URL_INFORM=$LOGO_INFORM='';
			$url_changed=($url<>$oldurl);
			$logo_changed=($website<>$oldwebsite);
			if ($url_changed)  $URL_INFORM ="<b>URL</b>  (update $oldurl to <b>$url</b>)<br>";
			if ($logo_changed) $LOGO_INFORM="<b>Logo</b> (update $oldwebsite to <b>$website</b>)<br>";

			if ($url_changed || $logo_changed)
			{
				$Q_UPDATE =<<<EOQ
	UPDATE rodinposh_$RODINSEGMENT.dir_item
	SET url = '$url',
	`website` = '$website'
	WHERE dir_item.id = $id
	LIMIT 1 ;
EOQ;
			if (0) print "<hr>$Q_UPDATE";
			$urset = mysqli_query($DB->DBconn,$Q_UPDATE);
			$affected=mysqli_affected_rows($DB->DBconn);
			if ($affected < 0)
			{
				throw(New Exception(mysqli_error($DB->DBconn)."<hr>Query:".$Q_UPDATE."<br><br>"));
			}
			else {
				fontprint( "<small><small>".$URL_INFORM
						.$LOGO_INFORM
						."</small></small><br>" ,'#229');
			}
		}
		}
		$DB->close();
	}
	catch (Exception $e)
	{
		inform_bad_db($e);
	}
} // check_correct_app_widget_urls






function evtl_adjust_urls($xmlItem,$url,$website)
{
	$sxml = simplexml_load_string($xmlItem);
	print "<br>".$sxml->name.":";
	print " url: ".$sxml->url;
	print " website: ".$sxml->website;

	$sxml->url->name = $url;
	$sxml->website->name = $website;
	return $sxml->asXML();
}







function evtl_adjust_root($url,$RODINROOT,$RODINSEGMENT)
{
	// ir� = /_/rodin/e/w/RDW_flickr.rodin?
	// url = /rodinposh/u/images/bibsonomy-logo.png'

	$pattern_widget="/\/w\/(.+)/"; // select widgetname
	$pattern_logo="/\/u\/images\/(.+)/"; // select widgetname

	if (preg_match($pattern_widget,$url,$match))
	{
		$token=$match[1];
		$new_url="$RODINROOT/$RODINSEGMENT/app/w/$token";
	}
	else
	if (preg_match($pattern_logo,$url,$match))
	{
		$token=$match[1];
		$new_url="$RODINROOT/gen/u/images/$token";
	}
	else $new_url=$url;


	return $new_url;
}





function check_correct_values_in_portaneo_configfiles(	$PROT,$HOST,$PORT,$RODINROOT,$RODINSEGMENT,
														$RODIN_APPNAME,$RODINADMIN_ADMIN_EMAILADDR,
														$RODINDB_DBNAME, $RODINDB_USERNAME, $RODINDB_PASSWD)
##############################################################################################################################
#
# Register the real location of this installation
# automatically into ../../posh/includes/config.js the line:
# var __APPNAME="RODIN";
# var __LOCALFOLDER="http:\/\/localhost\/rodin\/eng\/posh\/";
# var __SUPPORTEMAIL="fabio.ricci@ggaweb.ch";
# var __headmenu=new dArray({"id":"lab_sep10","seq":12,"type":"label","label":"lblYouareloggedinas","comment":lg(""),"clss":"","images":"","fct":"","anonymous":1,"connected":1,"admin":0,"position":"right","options":""},{"id":"link_name","seq":2,"type":"link","label":lg("%username%"),"comment":lg(""),"clss":"","images":"","fct":"$p.network.myprofile()","anonymous":0,"connected":1,"admin":1,"position":"right","options":""},{"id":"span_availability","seq":3,"type":"label","label":lg(""),"comment":lg(""),"clss":"","images":"","fct":"","anonymous":0,"connected":1,"admin":0,"position":"right","options":""},{"id":"lab_par1","seq":4,"type":"label","label":lg("("),"comment":lg(""),"clss":"","images":"","fct":"","anonymous":0,"connected":1,"admin":1,"position":"right","options":""},{"id":"link_logout","seq":5,"type":"link","label":lg("lblDisconnect"),"comment":lg("lblDisconnect"),"clss":"","images":"","fct":"$p.app.logout()","anonymous":0,"connected":1,"admin":1,"position":"right","options":""},{"id":"lab_par2","seq":6,"type":"label","label":lg(")"),"comment":lg(""),"clss":"","images":"","fct":"","anonymous":0,"connected":1,"admin":1,"position":"right","options":""},{"id":"link_save","seq":10,"type":"link","label":lg("lblSave"),"comment":lg("lblSavePage"),"clss":"b","images":"ico_menu_disk.gif","fct":"$p.app.connection.saveMenu()","anonymous":1,"connected":0,"admin":0,"position":"right","options":""},{"id":"showlogo_fri","seq":10,"type":"link","label":'Version Info',"comment":'',"images":"logo_portal.gif","fct":"window.open('../../app/release_infos.php','_blank')","anonymous":0,"connected":1,"admin":0,"position":"left","options":""},{"id":"link_login","seq":12,"type":"link","label":lg("lblConnect"),"comment":lg("lblConnect"),"clss":"b","images":"","fct":"$p.app.connection.menu()","anonymous":1,"connected":0,"admin":0,"position":"right","options":""},{"id":"lab_sep3","seq":39,"type":"label","label":lg("|"),"comment":lg(""),"clss":"","images":"","fct":"","anonymous":1,"connected":0,"admin":0,"position":"left","options":""});
# Returns true if $sth_changed
#
{
	global $POSHDOCROOT;
	$sth_changed=false;
print "<br><br><u>check_correct_values_in_portaneo_configfiles according to current root $RODINROOT/$RODINSEGMENT </u>:<br>";
##############################################################################################################################
#### File config.js in posh/includes
#### SET CURRENT VALUES
	$RODIN_APPNAME_VALUE="\"$RODIN_APPNAME\";";
	$PORTEXTRA=($PORT==80)?'':":$PORT";
	$POSH__LOCALFOLDER_VALUE="\"$PROT://$HOST$PORTEXTRA$RODINROOT/$RODINSEGMENT/posh/\";";
	$SUPPORT_EMAIL_VALUE="\"$RODINADMIN_ADMIN_EMAILADDR\";";
	//The following is a portaneo headmenu refining var which has been changed/adapted according to RODIN requirements
	//Hence, you have to be sure, that this var be set as below in the file config.js - otherweise some optical effects
	//could arise:
	$POSH__HEADMENU_VALUE=<<<EOHM
new Array({"id":"lab_sep10","seq":12,"type":"label","label":lg("lblYouareloggedinas"),"comment":lg(""),"clss":"","images":"","fct":"","anonymous":1,"connected":1,"admin":0,"position":"right","options":""},{"id":"link_name","seq":2,"type":"link","label":lg("%username%") + '<img src="../../../gen/u/images/famfamfam/cog.png">',"comment":lg("titleManageAccount"),"clss":"userAccountLink","images":"","fct":"\$p.network.myprofile()","anonymous":0,"connected":1,"admin":1,"position":"right","options":""},{"id":"span_availability","seq":3,"type":"label","label":lg(""),"comment":lg(""),"clss":"","images":"","fct":"","anonymous":0,"connected":1,"admin":0,"position":"right","options":""},{"id":"lab_par1","seq":4,"type":"label","label":lg("("),"comment":lg(""),"clss":"","images":"","fct":"","anonymous":0,"connected":1,"admin":1,"position":"right","options":""},{"id":"link_logout","seq":5,"type":"link","label":lg("lblDisconnect"),"comment":lg("lblDisconnect"),"clss":"","images":"","fct":"\$p.app.logout()","anonymous":0,"connected":1,"admin":1,"position":"right","options":""},{"id":"lab_par2","seq":6,"type":"label","label":lg(")"),"comment":lg(""),"clss":"","images":"","fct":"","anonymous":0,"connected":1,"admin":1,"position":"right","options":""},{"id":"link_save","seq":10,"type":"link","label":lg("lblSave"),"comment":lg("lblSavePage"),"clss":"b","images":"ico_menu_disk.gif","fct":"$p.app.connection.saveMenu()","anonymous":1,"connected":0,"admin":0,"position":"right","options":""},{"id":"showlogo_fri","seq":10,"type":"link","label":'',"comment":'',"images":"logo_portal.gif","fct":"window.open('../../app/release_infos.php','_blank')","anonymous":0,"connected":1,"admin":0,"position":"left","options":""},{"id":"link_login","seq":12,"type":"link","label":lg("lblConnect"),"comment":lg("lblConnect"),"clss":"b","images":"","fct":"\$p.app.connection.menu()","anonymous":1,"connected":0,"admin":0,"position":"right","options":""},{"id":"lab_sep3","seq":39,"type":"label","label":lg("|"),"comment":lg(""),"clss":"","images":"","fct":"","anonymous":1,"connected":0,"admin":0,"position":"left","options":""});
EOHM;
	$filename="config.js";
	$filetype='js';
	$filedir=$POSHDOCROOT."/includes";

	//appname
	$term1="var __APPNAME=";
	$term2=";";
	$pattern[]="/$term1(.*?)$term2/";
	$substitution[]=$term1.$RODIN_APPNAME_VALUE;

	//supportmail
	$term1="var __SUPPORTEMAIL=";
	$term2=";";
	$pattern[]="/$term1(.*?)$term2/";
	$substitution[]=$term1.$SUPPORT_EMAIL_VALUE;

	//localfolder assuming /posh/ root elem:
	$term1="var __LOCALFOLDER=";
	$term2=";";
	$pattern[]="/$term1(.*?)$term2/";

	$substitution[]=''.$term1.$POSH__LOCALFOLDER_VALUE;
	
	//hide configuration link in widgets
	$term1="var __showModuleConfigure=";
	$term2=";";
	$pattern[]="/$term1(.*?)$term2/";

	$substitution[]=''.$term1.'false';

	// headmenu - last line
	$term1="var __headmenu=";
	$pattern[]="/$term1(.*?);/";
	$substitution[]=$term1.$POSH__HEADMENU_VALUE;

	$changed = check_replace_in_file($filename, $filedir, $filetype, $pattern, $substitution, true);
	$sth_changed = $sth_changed || $changed;
		$pattern=null;
	$substitution=null;
##############################################################################################################################
#### File config.inc.php in posh/includes
####
#### define("__LOCALFOLDER","http://anubis.local:25834/_/rodin/eng/posh/");
#### define("__DB","rodinposh_eng");
#### define("__APPNAME","RODIN");
#### define("__SERVER","anubis.local");
#### define("__LOGIN","posh_rodin_user");
#### define("__PASS","fri4p");
#### define("__SUPPORTEMAIL","fabio.ricci@ggaweb.ch");
####
#### SET CURRENT VALUES

	$PORTEXTRA=($PORT==80)?'':":$PORT";
	$POSH__LOCALFOLDER_VALUE="$PROT:\/\/$HOST$PORTEXTRA\\$RODINROOT\/$RODINSEGMENT\/posh\/";

	$filename="config.inc.php";
	$filetype='php';
	$filedir=$POSHDOCROOT."/includes";

	//localfolder
	$preg = preg_set_php_inc_value('__LOCALFOLDER',$POSH__LOCALFOLDER_VALUE);
	$pattern[]		=$preg{'pattern'};
	$substitution[]	=$preg{'substitution'};

  	//DB
	$preg = preg_set_php_inc_value('__DB',$RODINDB_DBNAME);
	$pattern[]		=$preg{'pattern'};
	$substitution[]	=$preg{'substitution'};

	//APPNAME
	$preg = preg_set_php_inc_value('__APPNAME',$RODIN_APPNAME);
	$pattern[]		=$preg{'pattern'};
	$substitution[]	=$preg{'substitution'};

	//SERVER = mysql server
	$preg = preg_set_php_inc_value('__SERVER','localhost');
	$pattern[]		=$preg{'pattern'};
	$substitution[]	=$preg{'substitution'};

	//DB LOGIN
	$preg = preg_set_php_inc_value('__LOGIN',$RODINDB_USERNAME);
	$pattern[]		=$preg{'pattern'};
	$substitution[]	=$preg{'substitution'};

	//DB LOGIN PASSWD
	$preg = preg_set_php_inc_value('__PASS',$RODINDB_PASSWD);
	$pattern[]		=$preg{'pattern'};
	$substitution[]	=$preg{'substitution'};

	//supporteremail
	$preg = preg_set_php_inc_value('__SUPPORTEMAIL',$RODINADMIN_ADMIN_EMAILADDR);
	$pattern[]		=$preg{'pattern'};
	$substitution[]	=$preg{'substitution'};

	//notificationemail -> supporteremail
	$preg = preg_set_php_inc_value('__NOTIFICATIONEMAIL',$RODINADMIN_ADMIN_EMAILADDR);
	$pattern[]		=$preg{'pattern'};
	$substitution[]	=$preg{'substitution'};

	$changed = check_replace_in_file($filename, $filedir, $filetype, $pattern, $substitution, true);
	$sth_changed = $sth_changed || $changed;
	$pattern=null;
	$substitution=null;

	return $sth_changed;
}


function preg_set_php_inc_value($TOKEN,$NEWVALUE)
{
	define("COMMA",',');
	define("CLOSEEXPR",');');

	$term1='"'.$TOKEN.'"'.COMMA;
	$term2=";";
	$preg{'pattern'} 		="/$term1(.*?)$term2/";
	$preg{'substitution'}	=$term1.'"'.$NEWVALUE.'"'.CLOSEEXPR;

	return $preg;
}




function check_replace_in_file($filename, $filedir, $filetype, $patterns, $substitutions, $tellfile=false)
###############################################################################################
#
# Extension of preg_replace to the content of a file
#
# Operates the changes, writes the current content to the file and the oldfile to .bak
#
{
	$debug=0;
	//print "<br>check_replace_in_file($filename)";

	if ($debug)
	{
		print "<br><b>check_replace_in_file $filename:</b><br>"
				."Pattern ==> Substitution";
		if (count($patterns)==0)
		{
			print "<br>(CALLED WITH EMPTY REGEXPs - exit)<br>";
			return false;
		}
		else
		{
			for($i=0;$i<count($patterns);$i++)
			print "<br>".$patterns[$i]." ==> ".$substitutions[$i];
		}
	} // debug



	$filepath="$filedir/$filename";
	if (file_exists($filepath))
	{
		####################################################################

		$filecontent = file_get_contents($filepath,$filetype);
		$tellable_filecontent = tell_text($filecontent);

		if ($filetype=='php')
				$filecontent = strip_php($filecontent);

		if ($tellfile)
		{
			$len=strlen($filecontent);
			fontprint ("<hr>Old content of $filepath:<br>$len characters:<br>");
			fontprint ("<small><small>".$tellable_filecontent."</small></small><hr>",'#999');
		}

		$newFileContent = preg_replace($patterns,$substitutions,$filecontent);
		$sth_changed=($newFileContent!=NULL && $newFileContent!=$filecontent);

		$tellable_newfilecontent = tell_text($newFileContent);
		if ($filetype=='php')
			$newFileContent = unstrip_php($newFileContent);

		####################################################################
		if ($sth_changed)
		{
			print "$filename updated!";
			if ($tellfile)
			{
				fontprint("<hr>New content of $filename:<br>");
				fontprint("<small><small>$tellable_newfilecontent</small></small><hr>",'#229');
			}
			else print "<br>";

			if (copy($filepath, $filepath.'.bak'))
			{
				//chmod($config_js_filepath.'.bak',0646); // not permitted...
	    		$h=fopen($filepath,"w");
				if (!fwrite($h,$newFileContent))
					fontprint("<hr> ERROR IN WRITING CHANGES to $filepath !!! No changes done",'red');
				else
				{
					fclose($h);
					print "<br><b>CHANGES WERE NEEDED: </b> PREVIOUS CONFIG FILE SAVED UNDER $filepath.bak</b>";
				}
				print "<br><br><hr>";
			}
		} // patterns in file found
		else print "<b>No change needed</b> - you keep your old <b>$filename</b> file in $filedir<br><br>";
	} // file_exists
	else
	{
		fontprint("<br>System error check_replace_in_file($filename, $filedir): File not readable!",'red');
	}
	return $sth_changed;
}



##############################################
#
# Special Strip PHP TEXT
#
$__PHPBEGIN_STRIPPED='___php_begin___';
$__PHPEND_STRIPPED='___php_end___';
$__PHPBEGIN_UNSTRIPPED='<?php';
$__PHPEND_UNSTRIPPED='?>';
function strip_php($filecontent)
{
	global $__PHPBEGIN_UNSTRIPPED;
	global $__PHPEND_UNSTRIPPED;
	global $__PHPBEGIN_STRIPPED;
	global $__PHPEND_STRIPPED;
	$filecontent = $__PHPBEGIN_STRIPPED.substr($filecontent,5);
	$stripped_content=str_replace($__PHPEND_UNSTRIPPED,$__PHPEND_STRIPPED,$filecontent);
	return $stripped_content;
}

function unstrip_php($filecontent)
{
	global $__PHPBEGIN_UNSTRIPPED;
	global $__PHPEND_UNSTRIPPED;
	global $__PHPBEGIN_STRIPPED;
	global $__PHPEND_STRIPPED;
	$stripped_content=str_replace($__PHPBEGIN_STRIPPED,$__PHPBEGIN_UNSTRIPPED,$filecontent);
	$stripped_content=str_replace($__PHPEND_STRIPPED,$__PHPEND_UNSTRIPPED,$stripped_content);
	return $stripped_content;
}
##############################################





function tell_text($text)
{
	$tellable_text = str_replace("\n",'<br>',$text);
	return $tellable_text;
}



function check_corrent_fsrc_localpaths($PROT,$HOST,$PORT,$RODINROOT,$RODINSEGMENT)
#
# Updates the paths of the SRC engines marked as 'fsrc'
# according to the current $RODINROOT/$RODINSEGMENT path
#
# This is useful after an installation (or installation move)
#
{
	print "<br>check_corrent_fsrc_localpaths($PROT,$HOST,$PORT,$RODINROOT,$RODINSEGMENT)";
	$SQL_SRC_INTERFACES="SELECT * from src_interface where Type='fsrc'; ";

	try {
		$DB = new RODIN_DB('rodin');
		$DBconn=$DB->DBconn;

		print "<br><br><u>Automatical SRC Engine Update in RODIN SRC Database according to current root $RODINROOT/$RODINSEGMENT </u>:<br>";
		$resultset= mysqli_query($DB->DBconn,$SQL_SRC_INTERFACES);
		while ($row = mysqli_fetch_assoc($resultset))
		{
			$ID				=$row['ID'];
			$Name			=$row['Name'];
			$Protocol 		=$row['Protocol '];
			$Server 		=$row['Server'];
			$Port 			=$row['Port'];
			$Path_Start 	=$row['Path_Start'];
			$Path_Refine	=$row['Path_Refine'];
			$Path_Test		=$row['$Path_Test'];

			$NewProtocol	=$PROT;
			$NewServer 		=$HOST;
			$NewPort 		=$PORT==''?80:$PORT;
			$NewPath_Start 	=adjust_fsrc_path($Path_Start,$RODINROOT,$RODINSEGMENT);
			$NewPath_Refine	=adjust_fsrc_path($Path_Refine,$RODINROOT,$RODINSEGMENT);
			$NewPath_Test	=adjust_fsrc_path($Path_Test,$RODINROOT,$RODINSEGMENT);

			$Q_UPDATE =<<<EOQ
UPDATE rodin_$RODINSEGMENT.src_interface
SET
Protocol = '$NewProtocol',
Server = '$NewServer',
Port = $NewPort,
Path_Start = '$NewPath_Start',
Path_Refine = '$NewPath_Refine',
Path_Test = '$NewPath_Test'
WHERE src_interface.ID = $ID
LIMIT 1 ;
EOQ;
			if (0) print "<hr>$Q_UPDATE";
			$urset = mysqli_query($DB->DBconn,$Q_UPDATE);
			$affected=mysqli_affected_rows($DB->DBconn);
			if ($affected < 0)
			{
				throw(New Exception(mysqli_error($DB->DBconn)."<hr>Query:".$Q_UPDATE."<br><br>"));
			}
			else
			 {
				fontprint( "<small><small><b>$Name</b> (Service ID $ID):".
				 "<br><b>Protocol</b>  (from $Protocol to <b>$NewProtocol</b>)".
				 "<br><b>Port</b>  (from $Port to <b>$NewPort</b>)".
				 "<br><b>Server</b>  (from $Server to <b>$NewServer</b>)".
				 "<br><b>Path_Start</b>  (from $Path_Start to <b>$NewPath_Start</b>)".
				 "<br><b>Path_Refine</b>  (from $Path_Refine to <b>$NewPath_Refine</b>)".
				 "<br><b>Path_Test</b>  (from $Path_Test to <b>$NewPath_Test</b>)".

					  "<br></small></small><hr><br>",'#229');
			}
		}

		$DB->close();

	}
	catch (Exception $e)
	{
		inform_bad_db($e);
	}

}






function adjust_fsrc_path($Path,$NEWRODINROOT,$NEWRODINSEGMENT)
{
	// url = _/rodin/eng/fsrc/app/engines/engine_fr3
	print "<br>adjust_fsrc_path($Path,$NEWRODINROOT,$NEWRODINSEGMENT) ...";

	$pattern_fsrc="/\/(.*)\/fsrc\/app\/engine\/(.+)/"; // select engine name
	//print "<br>adjust_fsrc_path($Path,$RODINROOT,$RODINSEGMENT): <br>";
	if (preg_match($pattern_fsrc,$Path,$match))
	{
		$token=$match[2];
		$new_path="$NEWRODINROOT/$NEWRODINSEGMENT/fsrc/app/engine/$token";
	}
	else //Match old rodinposh era: /fsrc/x/e/SRC/engine_fr1
	{
		#print "<br> NOMATCH $pattern_fsrc in ($Path)";
		$pattern_fsrc="/fsrc\/(.*)\/e\/SRC\/(.*)/"; // select engine name

		if (preg_match($pattern_fsrc,$Path,$match))
		{
			$token=$match[2];
			$new_path="$NEWRODINROOT/$NEWRODINSEGMENT/fsrc/app/engine/$token";
			//print " YES MATCH FSRC OLD in ($Path) token=$token"
			//		."<br>NEW PATH= $new_path ";
		}
		else
		{
			#print "<br> NOMATCH $pattern_fsrc in ($Path)";
			$new_path=$Path;
		}
	}
	return $new_path;
}




$ACTION_CALM=	"<br>(This action takes place only once after each installation change - just reload or login please.)<br><br>";
function check_rodin_installation($PROT,$HOST,$PORT,$RODINROOT,$RODINSEGMENT)
#################################
{
	global $RODINPOSHDB_DBNAME, $RODINPOSHDB_USERNAME, $RODINPOSHDB_PASSWD, $RODIN_APPNAME, $RODINADMIN_ADMIN_EMAILADDR;
	global $ACTION_CALM, $RODINADMIN_LINK;
	global $FONTGREEN;

  print "<br>check_rodin_installation($RODINSEGMENT) RODINPOSHDB_DBNAME=$RODINPOSHDB_DBNAME ";

	print "<hr>${FONTGREEN}AUTOMATIC RODIN MAINTENANCE CHECK </font><br>";
	print "Checking your RODIN installation with regards to following parameters:"
	."<br><small><small>Any suggestions please to $RODINADMIN_LINK </small></small><hr>"
	."<table cellcadding=0 cellspacing=0>"
	."<tr><td>RODIN Host:</td><td> <b>$PROT://$HOST:$PORT</b></td></tr>".
	"<tr><td>RODIN Service Root: </td><td> <b>$RODINROOT/$RODINSEGMENT</b></td></tr>"
	."</table>";

	$sth_changed = check_correct_values_in_portaneo_configfiles( $PROT,$HOST,$PORT,$RODINROOT,$RODINSEGMENT,
																 $RODIN_APPNAME,$RODINADMIN_ADMIN_EMAILADDR,
																 $RODINPOSHDB_DBNAME, $RODINPOSHDB_USERNAME, $RODINPOSHDB_PASSWD);
	// if ($sth_changed) // always!
	{
		check_correct_app_widget_urls($RODINROOT,$RODINSEGMENT);
		check_corrent_fsrc_localpaths($PROT,$HOST,$PORT,$RODINROOT,$RODINSEGMENT);

	}
	print 	"<br>END OF AUTOMATIC RODIN MAINTENANCE CHECK <hr>"
			."<br>Please feel free to contact the $RODINADMIN_LINK for questions or <b>just login</b><br><br>";

}





#################################
#
# Exec part: compute RODIN user info
# debugUtils::callStack(Exception::getTrace());

	
		$POSH_user_info = get_POSH_user_info($USER);
	
		$rodinuseremail=$POSH_user_info['username'];
		$rodinuserid=$USER;
		$rodinuser=$POSH_user_info['long_name'];
	
	class debugUtils {
	    public static function callStack($stacktrace) {
	        print "<br>".str_repeat("=", 50) ."\n";
	        $i = 1;
	        foreach($stacktrace as $node) {
	            print "<br>$i. ".basename($node['file']) .":" .$node['function'] ."(" .$node['line'].")\n";
	            $i++;
	        }
	    } 
	}

?>
