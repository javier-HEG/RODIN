<?php

/* ********************************************************************************
 * This widget is implemented using the SRU-Service offered by Swissbib, more
 * information at: http://www.swissbib.org/wiki/index.php?title=SRU
 * 
 * A basic search interface can be accessed at: http://sru.swissbib.ch/SRW/search/
 *
 * The definition of the MARC21 Bibliographic Data Format, maintained by the
 * Library of Congres: http://www.loc.gov/marc/bibliographic/
 *
 * @author Fabio Ricci
 * @author Javier Belmonte, reimplementation 11.09.2012 
 ******************************************************************************** */

include_once("../u/RodinWidgetBase.php");
include_once '../u/RodinResult/RodinResultManager.php';
include_once "$DOCROOT/$RODINUTILITIES_GEN_URL/simplehtmldom/simple_html_dom.php";

global $SEARCHSUBMITACTION;

$NEED_PHP_INTERNET_ACCESS = true;

// Since widgets are loaded inside an iFrame, they need a HTML header.
print_htmlheader("SWISSBIB RODIN WIDGET");
	
$searchsource_baseurl = "http://sru.swissbib.ch/SRW/search/";
$swissbib_permalink_baseurl = "http://www.swissbib.ch/TouchPoint/perma.do?v=nose&l=en&q=35="; // Add MARC field 035
$swissbib_record_url = "http://www.swissbib.ch/TouchPoint/start.do?Language=de&View=nose&Query=35="; // ."%22".<marc035>."%22" 

/* ********************************************************************************
 * Generate the HTML search input fields and controls
 ******************************************************************************* */

// - Query (Rodin default is 'q')
$title=lg("titleWidgetTypeSearch");
$htmldef=<<<EOH
	<input class="localSearch" name="q" type="text" value="$qx" title='$title' onchange="$SEARCHSUBMITACTION">
EOH;
add_search_control('q',$qx,'$q',$htmldef,1);

// - Number of results (Rodin default is 'm')
$title=lg("titleWidgetMaxResults");
$m=$_REQUEST['m']; if(!$m) $m=20;
$htmldef=<<<EOH
	<input class="localMaxResults" name="m" type="text" value="$m" title='$title'/>
EOH;
add_search_control('m',$m,20,$htmldef,1);

// - Search button (Rodin default is 'ask') 
$title=lg("titleWidgetButtonAsk");
$label=lg("labelWidgetButtonAsk");
$htmldef=<<<EOH
	<input name="ask" class="localSearchButton" type="button" onclick="$SEARCHSUBMITACTION" value="$label" title='$title'/>
EOH;
add_search_control('ask','','',$htmldef,1);


/* ********************************************************************************
 * Widget functions
 ******************************************************************************* */	 	

/** 
 * This function was used to diplay a header containing a logo for the service.
 *
 * @deprecated
 */
function DEFINITION_RDW_DISPLAYHEADER() {
	return true;
}

/**
 * ... ?
 */
function DEFINITION_RDW_DISPLAYSEARCHCONTROLS() {
	return true;
}

/**
 * Method called from RodinWidgetSMachine.RDW_COLLECTRESULTS_EPI() to collect
 * results from the source and save them to the database. It used to return a
 * table with an old structure for results but now returns the number of results
 * found only.
 * 
 * @param string $chaining_url
 */
function DEFINITION_RDW_COLLECTRESULTS($chaining_url='') {	
	global $datasource;
	global $searchsource_baseurl;
	global $swissbib_permalink_baseurl;
	global $REALNAME;
	global $RDW_REQUEST;
	
	foreach ($RDW_REQUEST as $querystringparam => $d)
		eval( "global \${$querystringparam};" );

	foreach ($REALNAME as $rodin_name=>$needed_name) {
		if ("${$rodin_name}" <> '') // only if value defined
			$FILTER_SECTION .= "&$needed_name=${$rodin_name}";
	}

	$url = $searchsource_baseurl . '?'
		. 'query=dc.anywhere+%3D+"' . urlencode($q) . '"'
		. '&version=1.1&operation=searchRetrieve&recordSchema=info%3Asrw%2Fschema%2F1%2Fmarcxml-v1.1'
		. '&maximumRecords=' . $m . '&startRecord=1&resultSetTTL=300&recordPacking=xml';
	
  
  
	//TomaNota::deEsto($_SERVER[PHP_SELF], "search url : $url");

	//$xml = get_file_content($url);
	$xml = get_cached_widget_response($url);
  $simpleXmlElement = str_get_html($xml);
	// Parse XML looking for results
	$allResults = array();

	$records = $simpleXmlElement->find('searchRetrieveResponse records', 0);

	for ($i = 0; $i < count($records->children); $i++) { 
		$record = $records->children($i);
		$recordData = $record->find('recordData srw_marc:record', 0);

		// Build single result with the right sub-class
		$formatRaw = getDataFromRecord($recordData, array('898' => 'a'));
		$format = decodeMarcFormat($formatRaw);
		$singleResult = RodinResultManager::buildRodinResultByType($format);

		// Set general fields
		$singleResult->setTitle(getDataFromRecord($recordData, array('245' => array('a', 'b'))));
		
		$controlNumber = getDataFromRecord($recordData, array('035' => 'a'));
		$singleResult->setUrlPage($swissbib_permalink_baseurl . urlencode('"' . $controlNumber . '"'));
    
		$date = getDataFromRecord($recordData, array('260' => 'c'));
		if (preg_match("/\d{4}/", $date, $match))
			$date = '01.01.' . $match[0];
		else
			$date = '';
		$singleResult->setDate($date);
		
		$singleResult->setAuthors(getDataFromRecord($recordData, array('*100' => array('D', 'a'), '*700' => array('D', 'a'))));

		// Result type specific fields
		switch ($format) {
			case RodinResultManager::RESULT_TYPE_BOOK:
				// Book specific fields: description, subjects, publisher, review, cover, isbn
				$singleResult->setProperty('subjects', getDataFromRecord($recordData, array('*650' => array('a', '*x'), '*691' => array('a', '*x'))));
				$singleResult->setProperty('publisher', getDataFromRecord($recordData, array('260' => array('a', 'b'))));
				$singleResult->setProperty('isbn', getDataFromRecord($recordData, array('020' => array('a'))));
				break;
			case RodinResultManager::RESULT_TYPE_ARTICLE:
				// Article specific fields: abstract, full-text, keywords, review, doi
				$singleResult->setProperty('keywords', getDataFromRecord($recordData, array('*650' => array('a', '*x'), '*691' => array('a', '*x'))));
				break;
			default:
				break;
		}

		//TomaNota::deEsto($_SERVER[PHP_SELF], "singleResult ($formatRaw) : $singleResult");

		$allResults[] = $singleResult;
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

/* ********************************************************************************
 * Utility functions, mainly widget independent.
 ******************************************************************************* */
function strip_cdata($string) {
	preg_match_all('/<!\[cdata\[(.*?)\]\]>/is', $string, $matches); 
	return str_replace($matches[0], $matches[1], $string); 
} 

/**
 * Returns a string representation of the datafields subfields
 * given in $fields that exist in $record. As a default, different
 * fields are comma separated and subfields space separated.
 * @param  [type] $record [description]
 * @param  Array $fields [description]
 * @return [type]
 */
function getDataFromRecord($record, $fields) {
	$textArray = array();
	foreach ($fields as $tag => $codes) {
		if (substr($tag, 0, 1) == '*') {
			$tag = substr($tag, 1);
			
			if ($datafields = $record->find('marc:datafield[tag=' . $tag . ']'))
				foreach ($datafields as $datafield)
					$textArray[] = getSubfieldFromData($datafield, $codes);
		} else {
			if ($record->find('marc:datafield[tag=' . $tag . ']')) {
				$datafield = $record->find('marc:datafield[tag=' . $tag . ']', 0);
				$textArray[] = getSubfieldFromData($datafield, $codes);
			}
		}
	}

	return implode(', ', $textArray);
}

/**
 * Returns a string representation of a datafield subfields.
 */
function getSubfieldFromData($data, $subfields) {
	if (!is_array($subfields))
		$subfields = array($subfields);

	$textArray = array();
	foreach ($subfields as $code) {
		if (substr($code, 0, 1) == '*') {
			$code = substr($code, 1);

			if ($fields = $data->find('marc:subfield[code=' . $code . ']'))
				foreach ($fields as $field)
					$textArray[] = strip_cdata($field->innertext);
		} else
			if ($data->find('marc:subfield[code=' . $code . ']'))
				$textArray[] = strip_cdata($data->find('marc:subfield[code=' . $code . ']', 0)->innertext);
	}

	return implode(' ', $textArray);
}

function decodeMarcFormat($format) {
	switch ($format) {
		#Book
		case 'BK000006000': return RodinResultManager::RESULT_TYPE_BOOK; // Buch auf Mikrofiche
		case 'BK010000000': return RodinResultManager::RESULT_TYPE_ARTICLE; // Artikel
		case 'BK040000000': return RodinResultManager::RESULT_TYPE_BOOK; // Buch
		case 'BK040100000': return RodinResultManager::RESULT_TYPE_BASIC; // Comic
		case 'BK040200000': return RodinResultManager::RESULT_TYPE_BASIC; // Dissertation/Masterarbeit
		case 'BK040300000': return RodinResultManager::RESULT_TYPE_BASIC; // Festschrift
		case 'BK040400000': return RodinResultManager::RESULT_TYPE_BASIC; // Gesetze und Verordnungen
		case 'BK040500000': return RodinResultManager::RESULT_TYPE_BOOK; // Tagungsband
		case 'BK040002001': return RodinResultManager::RESULT_TYPE_BOOK; // eBook: Datenträger
		case 'BK040002007': return RodinResultManager::RESULT_TYPE_BOOK; // eBook
		case 'BK060000000': return RodinResultManager::RESULT_TYPE_BASIC; // Dossier
		case 'BK070000000': return RodinResultManager::RESULT_TYPE_BASIC; // Teil eines Buches
		case 'BK080000000': return RodinResultManager::RESULT_TYPE_BASIC; // Manuskript
		#Computer File
		case 'CF010000000': return RodinResultManager::RESULT_TYPE_BASIC; // Datei
		case 'CF010002000': return RodinResultManager::RESULT_TYPE_BASIC; // Datei
		case 'CF010002001': return RodinResultManager::RESULT_TYPE_BASIC; // CD-ROM/DVD-ROM
		case 'CF010002002': return RodinResultManager::RESULT_TYPE_BASIC; // Diskette 3.5"
		case 'CF010002003': return RodinResultManager::RESULT_TYPE_BASIC; // Diskette 5.25"
		case 'CF010002005': return RodinResultManager::RESULT_TYPE_BASIC; // Magnetband
		case 'CF010002007': return RodinResultManager::RESULT_TYPE_BASIC; // Online Ressource
		#Continuing Resources
		case 'CR000006000': return RodinResultManager::RESULT_TYPE_BASIC; // Zeitung/Zeitschrift auf Mikrofiche
		case 'CR030000000': return RodinResultManager::RESULT_TYPE_ARTICLE; // Zeitschrift/Schriftenreihe
		case 'CR030100000': return RodinResultManager::RESULT_TYPE_ARTICLE; // Schriftenreihe
		case 'CR030200000': return RodinResultManager::RESULT_TYPE_BASIC; // Zeitung
		case 'CR030200001': return RodinResultManager::RESULT_TYPE_BASIC; // Zeitung: Datenträger
		case 'CR030200007': return RodinResultManager::RESULT_TYPE_BASIC; // Zeitung: online
		case 'CR030300000': return RodinResultManager::RESULT_TYPE_BOOK; // Zeitschrift
		case 'CR030302000': return RodinResultManager::RESULT_TYPE_BASIC; // eJournal
		case 'CR030302001': return RodinResultManager::RESULT_TYPE_BASIC; // eJournal: Datenträger
		case 'CR030302007': return RodinResultManager::RESULT_TYPE_BASIC; // eJournal
		case 'CR030400000': return RodinResultManager::RESULT_TYPE_BASIC; // Zeitschrift/Zeitung
		case 'CR030500000': return RodinResultManager::RESULT_TYPE_BASIC; // Zeitschrift: Dissertation/Masterarbeit
		case 'CR030600000': return RodinResultManager::RESULT_TYPE_BASIC; // Zeitschrift: Gesetze und Verordnungen
		case 'CR030700000': return RodinResultManager::RESULT_TYPE_BASIC; // Zeitschrift: Tagungsband
		case 'CR040000000': return RodinResultManager::RESULT_TYPE_BASIC; // Zeitschriften-Teil0
		case 'CR050000000': return RodinResultManager::RESULT_TYPE_BASIC; // Loseblatt-Ausgabe
		#Map
		case 'MP010001000': return RodinResultManager::RESULT_TYPE_BASIC; // Kartenmaterial
		case 'MP010001001': return RodinResultManager::RESULT_TYPE_BASIC; // Atlas
		case 'MP010001002': return RodinResultManager::RESULT_TYPE_BASIC; // Karte: Diagramm
		case 'MP010001003': return RodinResultManager::RESULT_TYPE_BASIC; // Karte
		case 'MP010001004': return RodinResultManager::RESULT_TYPE_BASIC; // Profil
		case 'MP010001005': return RodinResultManager::RESULT_TYPE_BASIC; // Relief
		case 'MP010001006': return RodinResultManager::RESULT_TYPE_BASIC; // Luftbild
		case 'MP010001007': return RodinResultManager::RESULT_TYPE_BASIC; // Schnitt
		case 'MP010001008': return RodinResultManager::RESULT_TYPE_BASIC; // Ansicht
		case 'MP010003001': return RodinResultManager::RESULT_TYPE_BASIC; // Globus
		#Music
		case 'MU010000000': return RodinResultManager::RESULT_TYPE_BASIC; // Noten
		case 'MU010100000': return RodinResultManager::RESULT_TYPE_BASIC; // Musik: Partitur
		case 'MU010200000': return RodinResultManager::RESULT_TYPE_BASIC; // Musik: Partiturauszug
		case 'MU010300000': return RodinResultManager::RESULT_TYPE_BASIC; // Musik: Particell
		case 'MU010400000': return RodinResultManager::RESULT_TYPE_BASIC; // Musiknoten
		case 'MU010006000': return RodinResultManager::RESULT_TYPE_BASIC; // Noten auf Mikrofiche
		case 'MU020000000': return RodinResultManager::RESULT_TYPE_BASIC; // Musik: Manuskript
		case 'MU030000000': return RodinResultManager::RESULT_TYPE_BASIC; // Töne/Sprache
		case 'MU030012000': return RodinResultManager::RESULT_TYPE_BASIC; // Hörbuch
		case 'MU030612001': return RodinResultManager::RESULT_TYPE_BASIC; // Hörbuch (CD)
		case 'MU030612002': return RodinResultManager::RESULT_TYPE_BASIC; // Schallplatte Ton
		case 'MU040000000': return RodinResultManager::RESULT_TYPE_BASIC; // Musik
		case 'MU040012000': return RodinResultManager::RESULT_TYPE_BASIC; // Musik (CD)
		case 'MU040612001': return RodinResultManager::RESULT_TYPE_BASIC; // Musik (CD/DVD)
		case 'MU040612002': return RodinResultManager::RESULT_TYPE_BASIC; // Schallplatte Musik
		#Mixed Materials
		case 'MX010000000': return RodinResultManager::RESULT_TYPE_BASIC; // Medienkombination
		#Visual Materials
		case 'VM010000000': return RodinResultManager::RESULT_TYPE_BASIC; // Film/Video
		case 'VM010005000': return RodinResultManager::RESULT_TYPE_BASIC; // Projektionsgrafik allgemein
		case 'VM010005001': return RodinResultManager::RESULT_TYPE_BASIC; // Diapositiv
		case 'VM010005002': return RodinResultManager::RESULT_TYPE_BASIC; // Transparent
		case 'VM010214001': return RodinResultManager::RESULT_TYPE_BASIC; // Video
		case 'VM010214002': return RodinResultManager::RESULT_TYPE_BASIC; // DVD-Video
		case 'VM010300000': return RodinResultManager::RESULT_TYPE_BASIC; // Film
		case 'VM010400000': return RodinResultManager::RESULT_TYPE_BASIC; // Lehrmittel
		case 'VM010500000': return RodinResultManager::RESULT_TYPE_BASIC; // Spiel
		case 'VM020000000': return RodinResultManager::RESULT_TYPE_BASIC; // Fotografie
		case 'VM020005002': return RodinResultManager::RESULT_TYPE_BASIC; // Arbeitstransparent
		case 'VM020005004': return RodinResultManager::RESULT_TYPE_BASIC; // Bild auf Spezialträger
		case 'VM020006000': return RodinResultManager::RESULT_TYPE_BASIC; // Bilder auf Mikrofiche
		case 'VM020007000': return RodinResultManager::RESULT_TYPE_BASIC; // Bilder und Poster
		case 'VM020007001': return RodinResultManager::RESULT_TYPE_BASIC; // Bildmaterial
		case 'VM030000000': return RodinResultManager::RESULT_TYPE_BASIC; // Medienkombination
		case 'VM050000000': return RodinResultManager::RESULT_TYPE_BASIC; // Gegenstand
		#Institutional Repositories
		case 'VM040000000': return RodinResultManager::RESULT_TYPE_BASIC; // Institutional Repository Daten
		default: return RodinResultManager::RESULT_TYPE_BASIC;
	}
}

/* ********************************************************************************
 * Decide which function the state machine is on.
 ******************************************************************************* */

include_once("../u/RodinWidgetSMachine.php");

?>
