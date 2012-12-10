<?php

/* ********************************************************************************
 * The EconBiz API follows a RESTful API design. The base URL is
 * - http://metamat.zbw-kiel.de/v1/
 * 
 * @author Javier Belmonte
 ******************************************************************************** */

include_once("../u/RodinWidgetBase.php");
require_once('../u/RodinResult/RodinResultManager.php');

global $SEARCHSUBMITACTION;

// Since widgets are loaded inside an iFrame, they need
// a HTML header.
print_htmlheader("EconBiz RODIN Widget");

$searchsource_baseurl="http://metamat.zbw-kiel.de/v1/";		


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
 * 
 * This function is used to create the form for the widget preferences.
 * 
 * @author Javier Belmonte <javier.belmonte@hesge.ch>
 */
function NOT_IMPLEMENTED_YET() {
//	DEFINITION_RDW_SEARCH_FILTER() {
	// Fix width in accordance to Widget preferred widht
	global $_w;
	$w = $_w - 15;
	
	// Define the style for the controls
	$PREFS_STYLE =<<<EOS
	style="min-width: {$w}px; max-width: {$w}px; width : {$w}px;";
EOS;
	
	// Choosing the record type of interest
	$title="Articles";
	$publications=$_REQUEST['xArticles'];
	$htmldef="<p><input type=\"checkbox\" name=\"xArticles\" value=\"articles\" />$title</p>";

	//add_searchfilter_control($name,$realname,$value,$defaultvalueQS,$htmldef,$pos)
	add_searchfilter_control('xArticles','xArticles',$publications,'articles',$htmldef,1);
	
	// Set default preferences in the DB
	register_default_prefs("xArticles=articles");
	
	$title="Books";
	$publications=$_REQUEST['xBooks'];
	$htmldef="<p><input type=\"checkbox\" name=\"xBooks\" value=\"books\" />$title</p>";

	//add_searchfilter_control($name,$realname,$value,$defaultvalueQS,$htmldef,$pos)
	add_searchfilter_control('xBooks','xBooks',$publications,'books',$htmldef,1);
	
	// Set default preferences in the DB
	register_default_prefs("xBooks=books");
		
	return true;		
}		 	

/** 
 * 
 * This function was used to diplay a header containing a logo for the service.
 * 
 * @deprecated
 */
function DEFINITION_RDW_DISPLAYHEADER()
{
	return true;
}

/**
 * 
 * ... ?
 */
function DEFINITION_RDW_DISPLAYSEARCHCONTROLS()
{
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
	global $RDW_REQUEST;
	
	foreach ($RDW_REQUEST as $querystringparam => $d)
		eval( "global \${$querystringparam};" );
	
	$qTokens = explode(',', trim($q, ' ,'));
	
	$parameters = array();
	$parameters['q'] = implode('+OR+', $qTokens) . ' (type:article OR type:book)';
	$parameters['size'] = $m;
						
	$options = array(CURLOPT_HTTPHEADER => array('Accept:application/json'));

	$jsonString = parametrizable_curl($searchsource_baseurl . 'search', $parameters, $options);
	
	$jsonInfo = json_decode($jsonString, true);

	// Parse JSON result and build results
	$allResults = array();
	
	// TODO Check status is 200
	// TODO Implement iterative access to results, batches of size 10

	if ($jsonInfo['hits']['total'] > 0) {
		foreach ($jsonInfo['hits']['hits'] as $record) {
			// Get result data from record
			$options = array(CURLOPT_HTTPHEADER => array('Accept:application/json'));
			$jsonRecordString = parametrizable_curl($searchsource_baseurl . 'record/' . $record['id'], array(), $options);
			$jsonRecordResult = json_decode($jsonRecordString, true);
			$jsonRecordInfo = $jsonRecordResult['record'];
			
			switch ($jsonRecordInfo['type']) {
				case 'article':
					// Create the result object
					$singleResult = RodinResultManager::buildRodinResultByType(RodinResultManager::RESULT_TYPE_ARTICLE);
		
					// General fields
					$singleResult->setTitle($jsonRecordInfo['title']);
					$singleResult->setDate($jsonRecordInfo['econbiz_created']);
					
					if (isset($jsonRecordInfo['source_url'])) {
						$singleResult->setUrlPage($jsonRecordInfo['source_url']);
					} else if (isset($jsonRecordInfo['identifier_url'])) {
						$singleResult->setUrlPage($jsonRecordInfo['identifier_url'][0]);
					} 
					
					$authorArray = array();
					$authorFieldNames = array('creator', 'person', 'contributor');
					foreach ($authorFieldNames as $fieldName) {
						$authorElements = $jsonRecordInfo[$fieldName];
						
						if (count($authorElements) > 0) {
							foreach ($authorElements as $author) {
								// Put first name first, remove ','
								$authorArray[] = implode(' ', array_reverse(explode(',', $author)));
							}
							
							break;
						}
					}
					$singleResult->setAuthors(implode(', ', $authorArray));
						
					// Article specific fields
					// TODO Check if it's possible to have multiple abstracts
					$singleResult->setProperty('abstract', strip_tags($jsonRecordInfo['abstract'][0]));
					
					if (isset($jsonRecordInfo['subject']) && is_array($jsonRecordInfo['subject'])) {
						$singleResult->setProperty('keywords', implode(', ', $jsonRecordInfo['subject']));
					}
					break;
				case 'book':
					// Create the result object
					$singleResult = RodinResultManager::buildRodinResultByType(RodinResultManager::RESULT_TYPE_BOOK);
		
					// General fields
					$singleResult->setTitle($jsonRecordInfo['title']);
					$singleResult->setDate($jsonRecordInfo['econbiz_created']);
					
					if (isset($jsonRecordInfo['source_url'])) {
						$singleResult->setUrlPage($jsonRecordInfo['source_url']);
					} else if (isset($jsonRecordInfo['identifier_url'])) {
						$singleResult->setUrlPage($jsonRecordInfo['identifier_url'][0]);
					} 
					
					$authorArray = array();
					$authorFieldNames = array('creator', 'person', 'contributor');
					foreach ($authorFieldNames as $fieldName) {
						$authorElements = $jsonRecordInfo[$fieldName];
						
						if (count($authorElements) > 0) {
							foreach ($authorElements as $author) {
								// Put first name first, remove ','
								$authorArray[] = implode(' ', array_reverse(explode(',', $author)));
							}
							
							break;
						}
					}
					$singleResult->setAuthors(implode(', ', $authorArray));
						
					// Book specific fields
					$singleResult->setProperty('description', strip_tags($jsonRecordInfo['abstract'][0]));
					
					if (isset($jsonRecordInfo['subject']) && is_array($jsonRecordInfo['subject'])) {
						$singleResult->setProperty('subjects', implode(', ', $jsonRecordInfo['subject']));
					}
					break;
				default:
					// Create a dummmy result object
					$singleResult = RodinResultManager::buildRodinResultByType(RodinResultManager::RESULT_TYPE_BASIC);
					break;
			}
			

			// Add single result to table
			$allResults[] = $singleResult;
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
 * to print the HTML code corresponding to results. The caller method already
 * creates the necessary DIV for all the results.
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
function decodeCategory($category) {
	switch ($category) {	
		case 'stat.AP': return 'Statistics (Applications)';
		case 'stat.CO': return 'Statistics (Computation)';
		case 'stat.ML': return 'Statistics (Machine Learning)';
		case 'stat.ME': return 'Statistics (Methodology)';
		case 'stat.TH': return 'Statistics (Theory)';
		case 'q-bio.BM': return 'Quantitative Biology (Biomolecules)';
		case 'q-bio.CB': return 'Quantitative Biology (Cell Behavior)';
		case 'q-bio.GN': return 'Quantitative Biology (Genomics)';
		case 'q-bio.MN': return 'Quantitative Biology (Molecular Networks)';
		case 'q-bio.NC': return 'Quantitative Biology (Neurons and Cognition)';
		case 'q-bio.OT': return 'Quantitative Biology (Other)';
		case 'q-bio.PE': return 'Quantitative Biology (Populations and Evolution)';
		case 'q-bio.QM': return 'Quantitative Biology (Quantitative Methods)';
		case 'q-bio.SC': return 'Quantitative Biology (Subcellular Processes)';
		case 'q-bio.TO': return 'Quantitative Biology (Tissues and Organs)';
		case 'cs.AR': return 'Computer Science (Architecture)';
		case 'cs.AI': return 'Computer Science (Artificial Intelligence)';
		case 'cs.CL': return 'Computer Science (Computation and Language)';
		case 'cs.CC': return 'Computer Science (Computational Complexity)';
		case 'cs.CE': return 'Computer Science (Computational Engineering; Finance; and Science)';
		case 'cs.CG': return 'Computer Science (Computational Geometry)';
		case 'cs.GT': return 'Computer Science (Computer Science and Game Theory)';
		case 'cs.CV': return 'Computer Science (Computer Vision and Pattern Recognition)';
		case 'cs.CY': return 'Computer Science (Computers and Society)';
		case 'cs.CR': return 'Computer Science (Cryptography and Security)';
		case 'cs.DS': return 'Computer Science (Data Structures and Algorithms)';
		case 'cs.DB': return 'Computer Science (Databases)';
		case 'cs.DL': return 'Computer Science (Digital Libraries)';
		case 'cs.DM': return 'Computer Science (Discrete Mathematics)';
		case 'cs.DC': return 'Computer Science (Distributed; Parallel; and Cluster Computing)';
		case 'cs.GL': return 'Computer Science (General Literature)';
		case 'cs.GR': return 'Computer Science (Graphics)';
		case 'cs.HC': return 'Computer Science (Human-Computer Interaction)';
		case 'cs.IR': return 'Computer Science (Information Retrieval)';
		case 'cs.IT': return 'Computer Science (Information Theory)';
		case 'cs.LG': return 'Computer Science (Learning)';
		case 'cs.LO': return 'Computer Science (Logic in Computer Science)';
		case 'cs.MS': return 'Computer Science (Mathematical Software)';
		case 'cs.MA': return 'Computer Science (Multiagent Systems)';
		case 'cs.MM': return 'Computer Science (Multimedia)';
		case 'cs.NI': return 'Computer Science (Networking and Internet Architecture)';
		case 'cs.NE': return 'Computer Science (Neural and Evolutionary Computing)';
		case 'cs.NA': return 'Computer Science (Numerical Analysis)';
		case 'cs.OS': return 'Computer Science (Operating Systems)';
		case 'cs.OH': return 'Computer Science (Other)';
		case 'cs.PF': return 'Computer Science (Performance)';
		case 'cs.PL': return 'Computer Science (Programming Languages)';
		case 'cs.RO': return 'Computer Science (Robotics)';
		case 'cs.SE': return 'Computer Science (Software Engineering)';
		case 'cs.SD': return 'Computer Science (Sound)';
		case 'cs.SC': return 'Computer Science (Symbolic Computation)';
		case 'nlin.AO': return 'Nonlinear Sciences (Adaptation and Self-Organizing Systems)';
		case 'nlin.CG': return 'Nonlinear Sciences (Cellular Automata and Lattice Gases)';
		case 'nlin.CD': return 'Nonlinear Sciences (Chaotic Dynamics)';
		case 'nlin.SI': return 'Nonlinear Sciences (Exactly Solvable and Integrable Systems)';
		case 'nlin.PS': return 'Nonlinear Sciences (Pattern Formation and Solitons)';
		case 'math.AG': return 'Mathematics (Algebraic Geometry)';
		case 'math.AT': return 'Mathematics (Algebraic Topology)';
		case 'math.AP': return 'Mathematics (Analysis of PDEs)';
		case 'math.CT': return 'Mathematics (Category Theory)';
		case 'math.CA': return 'Mathematics (Classical Analysis and ODEs)';
		case 'math.CO': return 'Mathematics (Combinatorics)';
		case 'math.AC': return 'Mathematics (Commutative Algebra)';
		case 'math.CV': return 'Mathematics (Complex Variables)';
		case 'math.DG': return 'Mathematics (Differential Geometry)';
		case 'math.DS': return 'Mathematics (Dynamical Systems)';
		case 'math.FA': return 'Mathematics (Functional Analysis)';
		case 'math.GM': return 'Mathematics (General Mathematics)';
		case 'math.GN': return 'Mathematics (General Topology)';
		case 'math.GT': return 'Mathematics (Geometric Topology)';
		case 'math.GR': return 'Mathematics (Group Theory)';
		case 'math.HO': return 'Mathematics (History and Overview)';
		case 'math.IT': return 'Mathematics (Information Theory)';
		case 'math.KT': return 'Mathematics (K-Theory and Homology)';
		case 'math.LO': return 'Mathematics (Logic)';
		case 'math.MP': return 'Mathematics (Mathematical Physics)';
		case 'math.MG': return 'Mathematics (Metric Geometry)';
		case 'math.NT': return 'Mathematics (Number Theory)';
		case 'math.NA': return 'Mathematics (Numerical Analysis)';
		case 'math.OA': return 'Mathematics (Operator Algebras)';
		case 'math.OC': return 'Mathematics (Optimization and Control)';
		case 'math.PR': return 'Mathematics (Probability)';
		case 'math.QA': return 'Mathematics (Quantum Algebra)';
		case 'math.RT': return 'Mathematics (Representation Theory)';
		case 'math.RA': return 'Mathematics (Rings and Algebras)';
		case 'math.SP': return 'Mathematics (Spectral Theory)';
		case 'math.ST': return 'Mathematics (Statistics)';
		case 'math.SG': return 'Mathematics (Symplectic Geometry)';
		case 'astro-ph': return 'Astrophysics';
		case 'cond-mat.dis-nn': return 'Physics (Disordered Systems and Neural Networks)';
		case 'cond-mat.mes-hall': return 'Physics (Mesoscopic Systems and Quantum Hall Effect)';
		case 'cond-mat.mtrl-sci': return 'Physics (Materials Science)';
		case 'cond-mat.other': return 'Physics (Other)';
		case 'cond-mat.sof': return 'Physics (Soft Condensed Matter)';
		case 'cond-mat.stat-mech': return 'Physics (Statistical Mechanics)';
		case 'cond-mat.str-el': return 'Physics (Strongly Correlated Electrons)';
		case 'cond-mat.supr-con': return 'Physics (Superconductivity)';
		case 'gr-qc': return 'General Relativity and Quantum Cosmology';
		case 'hep-ex': return 'High Energy Physics (Experiment)';
		case 'hep-lat': return 'High Energy Physics (Lattice)';
		case 'hep-ph': return 'High Energy Physics (Phenomenology)';
		case 'hep-th': return 'High Energy Physics (Theory)';
		case 'math-ph': return 'Mathematical Physics';
		case 'nucl-ex': return 'Nuclear Experiment';
		case 'nucl-th': return 'Nuclear Theory';
		case 'physics.acc-ph': return 'Physics (Accelerator Physics)';
		case 'physics.ao-ph': return 'Physics (Atmospheric and Oceanic Physics)';
		case 'physics.atom-ph': return 'Physics (Atomic Physics)';
		case 'physics.atm-clus': return 'Physics (Atomic and Molecular Clusters)';
		case 'physics.bio-ph': return 'Physics (Biological Physics)';
		case 'physics.chem-ph': return 'Physics (Chemical Physics)';
		case 'physics.class-ph': return 'Physics (Classical Physics)';
		case 'physics.comp-ph': return 'Physics (Computational Physics)';
		case 'physics.data-an': return 'Physics (Data Analysis; Statistics and Probability)';
		case 'physics.flu-dyn': return 'Physics (Fluid Dynamics)';
		case 'physics.gen-ph': return 'Physics (General Physics)';
		case 'physics.geo-ph': return 'Physics (Geophysics)';
		case 'physics.hist-ph': return 'Physics (History of Physics)';
		case 'physics.ins-det': return 'Physics (Instrumentation and Detectors)';
		case 'physics.med-ph': return 'Physics (Medical Physics)';
		case 'physics.optics': return 'Physics (Optics)';
		case 'physics.ed-ph': return 'Physics (Physics Education)';
		case 'physics.soc-ph': return 'Physics (Physics and Society)';
		case 'physics.plasm-ph': return 'Physics (Plasma Physics)';
		case 'physics.pop-ph': return 'Physics (Popular Physics)';
		case 'physics.space-ph': return 'Physics (Space Physics)';
		case 'quant-ph': return 'Quantum Physics';
		default: return '';
	}
}


/* ********************************************************************************
 * Decide which function the state machine is on.
 ******************************************************************************* */

include_once("../u/RodinWidgetSMachine.php");

?>
