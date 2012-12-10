<?php
include_once("../u/RodinWidgetBase.php");
require_once '../u/RodinResult/RodinResultManager.php';

print_htmlheader("ALEXANDRIA RODIN WIDGET");

global $SEARCHSUBMITACTION;

$searchsource_uri = "$WEBROOT$RODINU/xmlsearch.php";

$datadir = "$RODINDATADIR/alexsg";
$basedatadir = "$RODINBASEDATADIR/alexsg";

$widget_icon_width = 55;
$widget_icon_height = 20;

$ALEX_SG_URL = array('alexpub_en_MCM.xml' => 'http://www.alexandria.unisg.ch/EXPORT/XML/Publikationen/de/MCM.xml',
	'alexproj_en_MCM.xml' => 'http://www.alexandria.unisg.ch/EXPORT/XML/Projekte/en/MCM.xml');


##############################################
# HTML SEARCH CONTROLS:
##############################################

// Query input : q (rodin internal query tag)
##############################################
$title=lg("titleWidgetTypeSearch");
$htmldef=<<<EOH
	<input class="localSearch" name="q" type="text" value="$qx" title='$title' onchange="$SEARCHSUBMITACTION">
EOH;
add_search_control('q',$qx,'$q',$htmldef,1);
##############################################

// Number of results : m (default)
##############################################
$title=lg("titleWidgetMaxResults");
$m = $_REQUEST['m']; if(!$m) $m=20;
$htmldef=<<<EOH
	<input class="localMaxResults" name="m" type="text" value="$m" title='$title'/>
EOH;
add_search_control('m',$m,20,$htmldef,1);
##############################################

// Search Button : ask (default)
##############################################
$title=lg("titleWidgetButtonAsk");
$label=lg("labelWidgetButtonAsk");
$htmldef=<<<EOH
	<input name="ask" class="localSearchButton" type="button" onclick="$SEARCHSUBMITACTION" value="$label" title='$title'/>
EOH;
add_search_control('ask','','',$htmldef,1);
##############################################



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

	##############################################
	# Each filter param is prefixed by "x"
	# You have to provide a real name also
	##############################################
	# Site xcc (in rerodoc  real name: cc)
	# Please insert value=''
	##############################################
	
	// Defint some style for controls
	global $_w;
	$w=$_w - 15; // fix width in accordance to Widget desired width
	
	$STYLE =<<<EOS
		style="min-width: {$w}px; max-width: {$w}px; width : {$w}px;";
EOS;

	
	
	##############################################
	# Publications or projects and even here an update on them
	##############################################
	$title="Please select to restrict the search";
	$xp=$_REQUEST['xp'];
	
	if (!$xp) $PUBBLICATIONS_CHECKED = ' checked ';
	
	$htmldef=<<<EOH
		</td><td><table><tr><td valign=top>See:</td><td nowrap valign=top>
	    <input type="radio" name="xp" value="publications"> Publications <br>
	    <input type="radio" name="xp" value="projects"> Projects <br>
	    <input type="radio" name="xp" value="both"> Both
		</td>
EOH;

	add_searchfilter_control('xp','p',$xp,'$xp',$htmldef,1);

	##############################################
	$title="Select the institute you are interested in";
	
	$MCM_TITLE="title= 'TBD'";
	$ACA_TITLE="title= 'Institut fuer Accounting, Controlling und Auditing'";
	$ARC_TITLE="title= 'Asia Research Center'";
	$EUR_TITLE="title= 'Institut fuer Europaeisches und Internationales Wirtschaftsrecht'";
	$FAA_TITLE="title= 'Forschungsinstitut fuer Arbeit und Arbeitsrecht'";
	$FBM_TITLE="title= 'Forschungsstelle fuer Business Metrics'";
	$FCI_TITLE="title= 'Forschungsstelle fuer Customer Insight'";
	$FEW_TITLE="title= 'Forschungsinstitut fuer Empirischische Oekonomie und Wirtschaftspolitik'";
	$FGN_TITLE="title= 'Forschungsgemeineschaft fuer Nationaloekonomie'";
	$FIM_TITLE="title= 'Forschungsstelle fuer Internationales Management'";
	$FWR_TITLE="title= 'Forschungsstelle fuer Wirtschaftsgeografie und Raumordnung'";
	$IDT_TITLE="title= 'Institut fuer Oeffentliche Dienstleistungen'";
	$IFB_TITLE="title= 'Institut fuer Betriebswirtschaft'";
	$IFF_TITLE="title= 'Institut fuer Finanzwissenschaft und Finanzrecht'";
	$IFM_TITLE="title= 'Institut fuer Marketing'";
	$IFPM_TITLE="title= 'Institut fuer Fuehrung und Personalmanagement'";
	$IORCF_TITLE="title= 'Institut fuer Accounting, Controlling und Auditing'";
	$IRM_TITLE="title= 'Institute for Retail Management'";
	$IVW_TITLE="title= 'Institut fuer Versicherungswirtschaft'";
	$IWE_TITLE="title= 'Institut fuer Wirtschaftsethik'";
	$KMU_TITLE="title= 'Schweizerisches Institut fuer Klein- und mIttelunternehmen'";
	$MCM_TITLE="title= 'DBD'";
	$LOGM_TITLE="title= 'Lehrstuhl fuer Logistikmanagement'";
	$OPSY_TITLE="title= 'Lehrstuhl fuer Organisationspsychologie'";
	$SBF_TITLE="title= 'Schweizerisches Institut fuer Banken und Finanzen'";
	$SEW_TITLE="title= 'Swiss Institute for Empirical Economic Research'";
	$SiAW_TITLE="title= 'Schweizerisches Institut fuer Aussenwirtschaft'";
	
	//selection is done per injection at abstract widget level
	$htmldef=<<<EOH
	<td valign=top align=right width=120>Institutes:</td><td>
	<select multiple name="xi" size="5" title="Select the institute in whose publications you are interested in">
		<option value="all" >(All)</option>
		<option value="ACA" $ACA_TITLE>ACA</option>
		<option value="ARC" $ARC_TITLE>ARC</option>
		<option value="EUR" $EUR_TITLE>EUR</option>
		<option value="FAA" $FAA_TITLE>FAA</option>
		<option value="FBM" $FBM_TITLE>FBM</option>
		<option value="FCI" $FCI_TITLE>FCI</option>
		<option value="FEW" $FEW_TITLE>FEW</option>
		<option value="FGN" $FGN_TITLE>FGN</option>
		<option value="FIM" $FIM_TITLE>FIM</option>
		<option value="FWR" $FWR_TITLE>FWR</option>
		<option value="IDT" $IDT_TITLE>IDT</option>
		<option value="IFB" $IFB_TITLE>IFB</option>
		<option value="IFF" $IFF_TITLE>IFF</option>
		<option value="IFM" $IFM_TITLE>IFM</option>
		<option value="IFPM" $IFPM_TITLE>IFPM</option>
		<option value="IORCF" $IORCF_TITLE>IORCF</option>
		<option value="IRM" $IRM_TITLE>IRM</option>
		<option value="IVW" $IVW_TITLE>IVW</option>
		<option value="IWE" $IWE_TITLE>IWE</option>
		<option value="KMU" $KMU_TITLE>KMU</option>
		<option value="MCM" $MCM_TITLE>MCM</option>
		<option value="LOGM" $LOGM_TITLE>LOGM</option>
		<option value="OPSY" $OPSY_TITLE>OPSY</option>
		<option value="SBF" $SBF_TITLE>SBF</option>
		<option value="SEW" $SEW_TITLE>SEW</option>
		<option value="SIAW" $SIAW_TITLE>SIAW</option>
	</select></td></tr></table>
EOH;

	add_searchfilter_control('xi','i',$xi,'$xi',$htmldef,1);
	
	##############################################
	// Publications xml file (english):
	$pubxmlfile='alexpub_en_MCM.xml';
	// Project xml file (english):
	$prxmlfile='alexproj_en_MCM.xml';
	
	$AXSG_BUTTON_ID="dwnlb_".uniqid();
	$UPDATELABELID = "upd_".uniqid();
	$UPDATELABELID2 = "upd_".uniqid();
	$BUTTON_BACKGROUNDCOLOR_GREEN="#33aa33";
	$BUTTON_BACKGROUNDCOLOR_RED="#aa3333";
	
	$dwnl_params="ajax=1&download=1&url[]=$pubxmlfile&url[]=$prxmlfile";
	$OKACTION="document.$FORMNAME.$AXSG_BUTTON_ID.style.bgColor=$BUTTON_BACKGROUNDCOLOR_GREEN";
	$BTN_STYLE_WIDTH="min-width: {$w}px; max-width: {$w}px; width : {$w}px;";
	$DWN_STYLE_red="style=\"background-color:$BUTTON_BACKGROUNDCOLOR_RED;color:white;$BTN_STYLE_WIDTH\"";
	$DWN_STYLE_green="style=\"background-color:$BUTTON_BACKGROUNDCOLOR_GREEN;color:white;$BTN_STYLE_WIDTH\"";
	$DWN_TITEL="Update your Alexandrias catalogue";
	$LABELTEXT="Last catalogue update:";
	$LABELSTYLE="style=\"color:#999999\"";
	$LABELSTYLEINVISIBLE="style=\"color:#eeeEee\""; // like the background of the div on wich itis displayed
	
	#######################################
	// How old is the archive on which the Widget relies?
	$min_mtime = get_alex_sg_lastupdate();
	$last_update_date = date ("d.m.Y H:i:s", $min_mtime);
	$delta_ms = time() - $min_mtime;
	$delta_days=round($delta_ms / (60 * 60 * 24),0);
	//print "DAYS OLD: $delta_days";
	// older than 3 days? red
	if ($delta_days>=3)
	{
		$DAYSOLD="$delta_days days old";
		$DWN_STYLE_X= $DWN_STYLE_red;
	}
	else
	{
	 	$DAYSOLD="Up to date";
		$DWN_STYLE_X= $DWN_STYLE_green;
	}
	
	$htmldef=<<<EOH
		</td><td colspan=2>
		<br>
		<label $LABELSTYLE>$LABELTEXT</label>&nbsp;
		<label name="$UPDATELABELID" id="$UPDATELABELID">$last_update_date</label>
		<br>
		<label $LABELSTYLEINVISIBLE>$LABELTEXT</label>&nbsp;
		<label name="$UPDATELABELID2" id="$UPDATELABELID2">$DAYSOLD</label>
		<br><input name="$AXSG_BUTTON_ID" type="button"
		 onclick="if (confirm('All catalogues of Alexandria UNI S.Gallen are now downloaded (ca. 5sec time)'))  {this.style.disabled=true;var dateupd = performRequest('$thisSCRIPT', '$dwnl_params'); this.style.background='$BUTTON_BACKGROUNDCOLOR_GREEN';} this.style.disabled=false;document.getElementById('$UPDATELABELID').innerHTML=dateupd;document.getElementById('$UPDATELABELID2').innerHTML='Up to date'; return false;"
		 value=" Update catalogue "
		 title='$DWN_TITEL'
		 $DWN_STYLE_X/>
		<br><br>
EOH;

	##############################################
	// Insert as filter element but dont begin with x =>  dont participate to filter prefs
	add_searchfilter_control('update','update','','',$htmldef,3);

	// Load the default preferences to enable direct use
	// of the widget (without setting any preference).
	register_default_prefs("xi=all&xp=publications");

	return true;
}
##############################################




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

	return $res;
} // DEFINITION_RDW_DISPLAYSEARCHCONTROLS
##############################################





##############################################
##############################################
function DEFINITION_RDW_DOWNLOAD()
##############################################
##############################################
#
# Reaction to an AJAX request:
# Performs the download and returns the date
#
{
	global $basedatadir;
	global $ALEX_SG_URL;
	date_default_timezone_set('Europe/Rome');
	$docs = $_REQUEST['url'];

	foreach ($docs as $docname)
	{
		if ($stop) break;
		else {
			$url=$ALEX_SG_URL{$docname};
			$xml = get_file_content($url);
			// save onto $datadir
			$filename="$basedatadir/$docname";
			//print "<br>try to update $filename";
			try {
				$f = fopen($filename,'w');

				if ($f) {
					$cnt=fwrite($f,$xml);
					
					if ($cnt==0) {
						print "SYSTEM ERROR: COULD NOT WRITE ON $filename";
						$res=0;
						$stop=true;
						break;
					}

					fclose($f);
					$res=1;
				}
			} catch (Exception $e) {
				print $e;
				$stop=true;
				$res=0;
			}
		}
	}

	$now=date("d.m.Y H:i:s");

	if ($res)
		$ret=$now;
	else
		$ret='Save error';
		
	print $ret;
} //DEFINITION_RDW_DOWNLOAD


#The following tells the widget state machine to check
#once for internet connection and warn if no one found
#(timeout) before collecting results
$NEED_PHP_INTERNET_ACCESS=false;


/**
 * Method called from RodinWidgetSMachine.RDW_COLLECTRESULTS_EPI() to collect
 * results from the source and save them to the database. It used to return a
 * table with an old structure for results but now returns the number of results
 * found only.
 * 
 * @param string $chaining_url
 */
function DEFINITION_RDW_COLLECTRESULTS($chaining_url = '') {
	global $datadir;
	global $datasource;
	global $searchsource_uri;
	global $REALNAME;
	global $RDW_REQUEST;

	foreach ($RDW_REQUEST as $querystringparam => $d) {
		eval( "global \${$querystringparam};" );
	}

	foreach ($REALNAME as $rodin_name => $needed_name) {
		if ("$rodin_name" != '' && $rodin_name != "xi") {
			$FILTER_SECTION .= "&$needed_name=${$rodin_name}";
		}

		if ($rodin_name == 'xp') {
			$xp = $xp;
		} else if ($rodin_name == 'xi') {
			$xi = $_REQUEST['xi'];

			if (!strstr($xi, 'all')) {
				$FILTER_SECTION.="&creator_par_institute=$xi";
			}
		}
	}
	
	// When $xi is either not defined or 'all', then search is done
	// everywhere, thus the empty value
	if (!strstr($FILTER_SECTION, "creator_par_institute"))
		$FILTER_SECTION.="&creator_par_institute=";
	
	$base = "alexsg";
	switch ($xp) {
		case'publications':
			$sourceToLook['publications'] = "$base/alexpub_en_MCM.xml";
			break;
		case'projects':
			$sourceToLook['projects'] = "$base/alexproj_en_MCM.xml";
			break;
		case'both':
		default:
			$sourceToLook['publications']="$base/alexpub_en_MCM.xml";
			$sourceToLook['projects']="$base/alexproj_en_MCM.xml";
			break;
	}

	// Search in every record in $sourceToLook (i.e. projects and/or publications)
	$xmlByMatter = array();
	$resultCountByMatter = array();
	foreach ($sourceToLook as $matter => $xmlsource) {
		$parameters = "q=" . urlencode($q) . "&m=$m" . "&source=$xmlsource" . $FILTER_SECTION;
		$url = "$searchsource_uri?$parameters";
		$xmlContent = get_file_content($url);
		$xml = str_get_html($xmlContent);
			
		if (!$xml) {
			echo "<br>Problem loading XML ($xmlContent) (exit)\n";

			foreach(libxml_get_errors() as $error) {
				echo "\t", $error->message;
			}

			exit;
		}

		$xmlByMatter[$matter] = $xml;
		$resultCountByMatter[$matter] = count($xml->find('publications, projects', 0)->children());
	}

	// Build the results objects
	$allResults = array();
	
	// Compute how many to take from each source
	// NB. Destroys $resultCountByMatter
	$resultsToTakePerMatter = array();
	while (count($resultCountByMatter) > 0) {
		$resultsStillToBeTaken = $m-array_sum($resultsToTakePerMatter);
		$minCountKey = array_search(min($resultCountByMatter), $resultCountByMatter);
		
		$resultsToTakePerMatter[$minCountKey] = min($resultCountByMatter[$minCountKey], floor($resultsStillToBeTaken/count($resultCountByMatter)));
		unset($resultCountByMatter[$minCountKey]);
	}
	
	foreach($xmlByMatter as $matter => $xml) {
		switch ($xml->find('publications, projects', 0)->tag) {
			case 'publications':
				// Other types include: Präsentation
				$typesAsArticle = array('Konferenzpapier', 'Artikel (wissenschaftliche Zeitschrift)', 'Artikel (Zeitung etc.)', 'Arbeitspapier');
				$typesAsBook = array('Buchkapitel', 'Buch');
					
				$i = 0;
				foreach ($xml->find('publication') as $publication) {
					$type = $publication->getElementByTagName('dc:type')->innertext;

					if (in_array($type, $typesAsArticle)) {
						// Create an article result object
						$singleResult = RodinResultManager::buildRodinResultByType(RodinResultManager::RESULT_TYPE_ARTICLE);

						// General fields
						$singleResult->setTitle($publication->getElementByTagName('dc:title')->innertext);
						$singleResult->setUrlPage($publication->getElementByTagName('dc:source')->innertext);
						$singleResult->setDate($publication->getElementByTagName('dcterms:date')->innertext);

						$authorArray = array();
						$authorElements = $publication->find('dc:creator');
						if (count($authorElements) > 0) {
							foreach ($authorElements as $author) {
								// Put first name first, remove ','
								$authorArray[] = implode(' ', array_reverse(explode(',', $author->innertext)));
							}
						}
						$singleResult->setAuthors(implode(', ', $authorArray));

						// Article specific fields
						// $singleResult->setProperty('abstract', $entry->getElementByTagName('summary')->innertext);

						$categoryArray = array();
						$categoryElements = $publication->find('dc:subject');
						if (count($categoryElements) > 0) {
							foreach ($categoryElements as $category) {
								if ($category->innertext != '')
								$categoryArray[] = $category->innertext;
							}
						}
						$singleResult->setProperty('keywords', implode(', ', $categoryArray));

						// Add single result to table
						$allResults[] = $singleResult;
					} else if (in_array($type, $typesAsBook)) {
						// Create an article result object
						$singleResult = RodinResultManager::buildRodinResultByType(RodinResultManager::RESULT_TYPE_BOOK);

						// General fields
						$singleResult->setTitle($publication->getElementByTagName('dc:title')->innertext);
						$singleResult->setUrlPage($publication->getElementByTagName('dc:source')->innertext);
						$singleResult->setDate($publication->getElementByTagName('dcterms:date')->innertext);

						$authorArray = array();
						$authorElements = $publication->find('dc:creator');
						if (count($authorElements) > 0) {
							foreach ($authorElements as $author) {
								// Put first name first, remove ','
								$authorArray[] = implode(' ', array_reverse(explode(',', $author->innertext)));
							}
						}
						$singleResult->setAuthors(implode(', ', $authorArray));

						// Book specific fields
						$citation = $publication->find('dcterms:bibliographicCitation', 0)->innertext;
						$isbn10Re = '\d-\d{3}-\d{5}-\d|X';
						$isbn13ReA = '\d{3}-\d-\d{3}-\d{5}-\d';
						$isbn13ReB = '\d{3}-\d-\d{4}-\d{4}-\d';
						if (preg_match("/($isbn10Re)|($isbn13ReA)|($isbn13ReB)/", $citation, $matches)) {
								$singleResult->setProperty('isbn', $matches[0]);
								
								$publisherRe = "/:\s([\p{L}|\s]+)\,\s\d{4}\.\s-\sISBN\s{$singleResult->getProperty('isbn')}/u";
								if (preg_match($publisherRe, $citation, $matches)) {
									$singleResult->setProperty('publisher', $matches[1]);
								}
						}
						
						$categoryArray = array();
						$categoryElements = $publication->find('dc:subject');
						if (count($categoryElements) > 0) {
							foreach ($categoryElements as $category) {
								if ($category->innertext != '')
								$categoryArray[] = $category->innertext;
							}
						}
						$singleResult->setProperty('subjects', implode(', ', $categoryArray));

						// Add single result to table
						$allResults[] = $singleResult;
					} else {
						// Create a basic result object otherwise
						$singleResult = RodinResultManager::buildRodinResultByType();

						// General fields
						$singleResult->setTitle($publication->getElementByTagName('dc:title')->innertext);
						$singleResult->setUrlPage($publication->getElementByTagName('dc:source')->innertext);
						$singleResult->setDate($publication->getElementByTagName('startOfProject')->innertext);

						$authorArray = array();
						$authorElements = $publication->find('dc:creator');
						if (count($authorElements) > 0) {
							foreach ($authorElements as $author) {
								// Put first name first, remove ','
								$authorArray[] = implode(' ', array_reverse(explode(',', $author->innertext)));
							}
						}
						$singleResult->setAuthors(implode(', ', $authorArray));

						// Add single result to table
						$allResults[] = $singleResult;
					}
						
					$i++;
					if ($i >= $resultsToTakePerMatter[$matter]) {
						break;
					}
				}
				break;
			case 'projects':
				$i = 0;
				foreach ($xml->find('project') as $project) {
					// Create the result object
					$singleResult = RodinResultManager::buildRodinResultByType();

					// General fields
					$singleResult->setTitle($project->getElementByTagName('dc:title')->innertext);
					$singleResult->setUrlPage($project->getElementByTagName('dc:source')->innertext);
					$singleResult->setDate($project->getElementByTagName('startOfProject')->innertext);

					$authorArray = array();
					$authorElements = $project->find('dc:creator');
					if (count($authorElements) > 0) {
						foreach ($authorElements as $author) {
							// Put first name first, remove ','
							$authorArray[] = implode(' ', array_reverse(explode(',', $author->innertext)));
						}
					}
					$singleResult->setAuthors(implode(', ', $authorArray));

					// Add single result to table
					$allResults[] = $singleResult;
						
					$i++;
					if ($i >= $resultsToTakePerMatter[$matter]) {
						break;
					}
				}
				break;
		}
	}

	// Save search to DB
	RodinResultManager::saveRodinSearch($sid, $q);
	
	// Save all articles found to DB
	RodinResultManager::saveRodinResults($allResults, $sid, $datasource);
	
	return count($allResults);
}

/**
 * 
 * ... ?
 */	
function DEFINITION_RDW_STORERESULTS()
{
	return true; // nothing to do here
}

/**
 * Called from RodinWidgetSMachine.RDW_SHOWRESULT_WIDGET_EPI(), it is asked
 * to print the HTML code corresponding to results. The caller method already
 * creates the necessary DIV for all the results.
 */
function DEFINITION_RDW_SHOWRESULT_WIDGET($w,$h) {
	global $sid;
	global $datasource;
  global $slrq;
	global $render;
	
	RodinResultManager::renderAllResultsInWidget($sid, $datasource, $slrq, $render);
	
	return true; 
}

/**
 * Called from RodinWidgetSMachine.RDW_SHOWRESULT_FULL_EPI(), it is asked
 * to print the HTML code corresponding to results.
 */
function DEFINITION_RDW_SHOWRESULT_FULL($w,$h) {
	global $sid;
	global $datasource;
	global $slrq;
  
	RodinResultManager::renderAllResultsInOwnTab($sid,$datasource,$slrq);
	
	return true; 
}


/* ******************************************
 * Utility functions, widget dependent.
 ****************************************** */
function get_alex_sg_lastupdate() {
	global $datadir;
	global $ALEX_SG_URL;
	global $basedatadir;
	date_default_timezone_set('Europe/Rome');
	// read all files data and give the earlest data
	$min_mtime = time();
	$timestamp='test date';
	foreach($ALEX_SG_URL as $docname=>$url)
	{
		$filename="$basedatadir/$docname";

		if (file_exists($filename))
		{

			$mtime=filemtime($filename);
			if ($mtime < $min_mtime)
				$min_mtime = $mtime;
		}
	}

	return $min_mtime;
}


/* ********************************************************************************
 * Decide which function the state machine is on.
 ******************************************************************************* */

include_once("../u/RodinWidgetSMachine.php");

?>

