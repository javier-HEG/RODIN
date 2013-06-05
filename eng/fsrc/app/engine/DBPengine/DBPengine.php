<?php

	# STW Engine abstract
	#
	# Mai 2011
	# Fabio.fr.Ricci@hesge.ch  
	# HEG 

$filename="SRCengine.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) { include_once("$updir$filename");break;}

$filename="gen/u/arc/ARC2.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) { include_once("$updir$filename");break;}

$filename="fsrc/app/u/FRIutilities.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) { include_once("$updir$filename");break;}
	
$filename="../../../../../$DORINSEGMENT/app/u/FRIutilities.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) { include_once("$updir$filename");break;}
	

	




abstract class DBPengine extends SRCengine
{

	function __construct() 
	#########################
	{
		parent::__construct();
		//$this->setStores();
		$this->setWordbinding('DBP');
    $this->currentclassname='DBPengine';

		//print "<br> STWengine<hr>"; var_dump($this);print "<hr>";
		
	} //constructor 
	
	
	
	protected function setStores()
	{
		global $ARCCONFIG;
		global $DBPEDIA_PREFIX;
		global $DBPEDIA_SPARQL_ENDPOINT;
		
		/* // not used - own implementation instead
		$localArcConfig = $ARCCONFIG;
		$localArcConfig['store_name'] = 'dbpedia';
		$this->set_dbpedia_store(ARC2::getStore($localArcConfig));
		
		if (!$this->get_dbpedia_store()->isSetUp()) {
		  $this->get_dbpedia_store()->setUp();
		}
			
		$remoteArcConfig = $ARCCONFIG;
		$remoteArcConfig['remote_store_endpoint'] = $DBPEDIA_SPARQL_ENDPOINT;
		$this->set_dbpedia_remote_store(ARC2::getRemoteStore($remoteArcConfig));
		*/
	}
	
	
	protected function testDataSourceResponsiveness($user)
	#######################################################
	#
	# Test if datasource is online by receiving a small 
	# response inside the current timeout value
	#
	# returns: the $user value itsself back if the datasource was found to be online
	#          or a timout warning
	#          or a not-available warning
	{
		$ok=true;
		$timout=false;
		$$error=false;
		$result=$this->testDBPediaResponse();
		
		if ($result=='ok')
			$RESPONSE=
					"<user>"
					.$user
					."</user>";
		else if ($result=='nohost'
		||$result=='') 
		{
			$TXT=lg('ontodatasourceerror');
			$RESPONSE=
					"<error>"
					."Error initializing data source - Data source may be offline or not responding ..."
					."</error>";
		}
		else if ($timeout)
		{
			$TXT=lg('ontodatasourcetimedout');
			$RESPONSE=
					"<error>"
					."$TXT $timeout seconds"
					."</error>";
		}
		
		return $RESPONSE;
	}
	
	
	
	
	protected function preprocess_refine($terms,$wordbinding='DBP',$lang='en',$mode='web')
	{
		global $DEFAULT_MAX_REFINE_RESULTS;
		
		if($this->getSrcDebug()) print "<br>preprocess_refine($terms,$wordbinding,$lang);";

		list($node,$label)=$this->extract_DBPnode($terms);
		####################################################################
		if ($node)
		{
			$RESULTS_P=new SRCEngineResult($terms); #donotchange it is a link
		}
		####################################################################
		else {
			$candidate_query_terms_str = group_contigous_words($terms,$lang);
			if($this->getSrcDebug()) 
			{
				print "<br>group_contigous_words: (($candidate_query_terms_str))";
			}
			
			if ($lang=='en') {
				list($english_candidate_compounds_str, $english_candidate_compounds_str_raw) =
					$this->get_english_candidate_compounds($candidate_query_terms_str);
			} else {
				$english_candidate_compounds_str = $candidate_query_terms_str;
				$english_candidate_compounds_str_raw = '';
			}
				
			if($this->getSrcDebug()) print "<br>english_candidate_compounds_str=($english_candidate_compounds_str)";
			
			$stop_cleaned_words_str= cleanup_stopwords_str($english_candidate_compounds_str);
			if($this->getSrcDebug()) print "<br>stop_cleaned_words_str=($stop_cleaned_words_str)";
				
			if($this->getSrcDebug()) 
				print "<br>preprocess_refine returning (".$stop_cleaned_words_str.")";	
      
				$RESULTS_P = new SRCEngineResult(trim($stop_cleaned_words_str), $english_candidate_compounds_str_raw);
		}
		return $RESULTS_P;
	}
		
  
  /**
  * Formats $term as in DBpedia, first letter capital.
	*/
	protected function formatAsInThesaurus($term) {
		return ucfirst(strtolower($term));
	}

	
	
	
	private function testDBPediaResponse()
	#######################################
	#
	# Check DBPedia and return an error code
	# 
	# ok 					if a triple could be called
	# honost 			if host does not respond
	# timeoutFSRC if CURL Timeout occurred
	# timeoutDS   if Datasource responded with a timout message
	{
		
		$SPARQL_QUERY=urlencode("SELECT ?r ?rx { ?r ?p ?rx. } LIMIT 1");
		
		$endpointResults=get_dbpediaendpoint_results($SPARQL_QUERY);
		
		$res='';
		
		IF (is_array($endpointResults) 
			&& is_array($endpointResults['results']) 
			&&count($endpointResults['results'])>0)
			$res='ok';
		else if (strstr($endpointResults['results'],"Operation timed out after(.*)milliseconds with 0 bytes received"))
			$res='timeoutFSRC'; #SRC did not receive anything		
			
		else if (strstr($endpointResults['results'],"Operation timed out"))
			$res='timeoutDS'; #Datasource returned a timeout		
		else if (strstr($endpointResults['results'],"Could not resolve host"))
			$res='nohost';		
			
		if($this->getSrcDebug())
		{
			print "<br>ENDPOINTRESULTS: (((";  var_dump($endpointResults);    print "))) <br><br>"; fontprint("res=$res",'red');
		}
		return $res;
	}
	
		protected function extract_DBPnode($term)
	####################################
	#
	# Returns list() if node matches one of:
	# http://dbpedia.org/resource/Category:blabla (altLabel)
	# http://dbpedia.org/resource/blabla (altLabel)
	#
	#
	{
		$ok= false;
		$node=$label=0;
		$altLabelExpr="\((.*)\)";
		$p1="http://dbpedia.org/resource/Category:";
		$p2="http://dbpedia.org/resource/";
		$PATTERN1="/^http:\/\/dbpedia\.org\/resource\/Category:(.*) \((.*)\)/";
		$PATTERN2="/^http:\/\/dbpedia\.org\/resource\/(.*) \((.*)\)/";
		
		if($this->getSrcDebug()) print "<br>Try to match $PATTERN1 or $PATTERN2 in($term)";
		
		if (preg_match($PATTERN1,$term,$match))
		{
			$ok= true;
			$node		="$p1".$match[1];
			$label	=$match[2];
			if($this->getSrcDebug()) print "<br>Matched node=($node) label=($label)";
		}
		else if (preg_match($PATTERN2,$term,$match))
		{
			$ok= true;
			$node		="$p2".$match[1];
			$label	=$match[2];
			if($this->getSrcDebug()) print "<br>Matched node=($node) label=($label)";
		}
		else if($this->getSrcDebug()) print "<br><b>NO match for direct DBPedia node </b>($term)<br>";
		return array($node,$label);		
		
	} // extract_DBPnode
	
	
	protected function validateTermInOntology($term, $lang = 'en') {
		if($this->getSrcDebug()) {
			print "<br>DPengine3->validateTermInOntology($term,$lang='en') ...";
		}
		
		$foundTerm = $this->checkterm_in_dbpedia($term,'X',$lang);
		
    if($this->getSrcDebug()) {
			print "\n<br>checkterm_in_dbpedia($term): ";var_dump($foundTerm);
		}
		
		if ($foundTerm != '') {
			return array(str_replace('_', ' ', $foundTerm), base64_encode('http://dbpedia.org/resource/Category:' . $foundTerm));
		} else {
			return false;
		}
		
	}
	
	
	private function checkterm_in_dbpedia($term, $SearchType='X', $lang) {
		$term = $this->suggest_dbpedia_term($term, $lang, 1);
		return $term; 
	}

	
	private function suggest_dbpedia_term($term, $lang, $maxterms = 1) {
		global $TERM_SEPARATOR;
		
		$suggested_term = $this->scrap_wikipedia_suggestion($term);
		
    if($this->getSrcDebug()) {
			print "<br>DPengine->suggest_dbpedia_term($term) ...= ($suggested_term)";
			print "<br>DPengine->suggest_dbpedia_term redirected_term=$redirected_term";
		}
    
		// Enough already? Disambiguate useless
		// $redirected_term = get_dbpedia_redirect($suggested_term,$lang);
		
		if (!$redirected_term) {
			$redirected_term = $suggested_term;
		}
		
    if($this->getSrcDebug()) {
			print "\n<br>DPengine->suggest_dbpedia_term redirected_term=$redirected_term";
		}
    
		// Disambiguate and keep the first $maxterms results only
		$disambiguated_terms = wikipedia_disambiguate($redirected_term, $lang);
    
    if($this->getSrcDebug()) {
			print "\n<br>DPengine->suggest_dbpedia_term disambiguated_terms: "; var_dump($disambiguated_terms);
		}
    
		$disambiguated_terms = array_slice($disambiguated_terms, 0, $maxterms);

    if($this->getSrcDebug()) {
			print "\n<br>DPengine->suggest_dbpedia_term disambiguated_terms: "; var_dump($disambiguated_terms);
		}
			
		$res_disambiguated_terms = implode($TERM_SEPARATOR, $disambiguated_terms);
		if (trim($res_disambiguated_terms) == '')
		$res_disambiguated_terms=$term;
    
    if($this->getSrcDebug()) {
			print "\n<br>DPengine->suggest_dbpedia_term res_disambiguated_terms: ($res_disambiguated_terms)";
		}

		$res_disambiguated_terms = cleanup_commas($res_disambiguated_terms);
    
    if($this->getSrcDebug()) {
			print "\n<br>DPengine->suggest_dbpedia_term cleaned up commas: res_disambiguated_terms: ($res_disambiguated_terms)";
		}
    
		
		if ($SRCDEBUG) {
			print "<br><b>find_dbpedia_terms($term)</b>";
			print "<hr>$term -> suggested:<em>$suggested_term</em> -> redirected:<em>$redirected_term</em> ->disambiguated:<em>$res_disambiguated_terms</em><hr>"; 
			//print "<hr>Aus $term -> suggested:$suggested_term -> first disambiguated:$disambiguated_term<hr>"; 
			print "<br><b>All disambiguated terms:";
			foreach($disambiguated_terms as $disambiguated_term)
				print "<br><a href='http://dbpedia.org/resource/$disambiguated_term' target=_blank>http://dbpedia.org/resource/$disambiguated_term</a>";
			print "<br><b>$maxterms choosen and returned terms:";
			foreach($partial_disambiguated_terms as $disambiguated_term)
				print "<br><a href='http://dbpedia.org/resource/$disambiguated_term' target=_blank>http://dbpedia.org/resource/$disambiguated_term</a>";
		}	
		
		return $res_disambiguated_terms;	
	}
	
	/**
	 * Checks in Wikipedia if the given $query corresponds to the name
	 * of an article. If not, then Wikipedia usually proposes a 'Did you mean?'
	 * when a search is made with the $query, the proposition is returned
	 * instead of the $query.
	 */
	private function scrap_wikipedia_suggestion($query) {
		global $SRCDEBUG;
		global $WIKIPEDIASEARCH2;
		global $WIKIPEDIABASEURL;
			
		if ($SRCDEBUG) {
			print "<br>scrap_wikipedia_suggestion in $WIKIPEDIABASEURL/$query ...";
		}
			
		if (url_exists("$WIKIPEDIABASEURL/$query")) {
			$suggestion = $query;
		} else {
			$WIKIPEDIARESPONSE = get_file_content($WIKIPEDIASEARCH2 . "&search=$query");
			$w = str_get_html($WIKIPEDIARESPONSE);

			//Was liegt an: Page found oder didyoumean?
			$a = $w->find('a[title="Special:Search"]');

			if (count($a) == 0)
			$suggestion = $query;
			else
			$suggestion = $a[0]->plaintext;
		}
			
		$suggestion = ucfirst($suggestion);
			
		if ($SRCDEBUG) {
			print "returning: $suggestion";
		}

		return $suggestion;
	}


	/**
	 * @deprecated ? Its single use in suggest_dbpedia_term is commented
	 */
	private function get_dbpedia_redirect($term,$language='en')
#############################################
#
# Returns the redirect link in English from dbpedia
#
#
{
	global $PATH2U;
	include_once("$PATH2U/arc/ARC2.php");
	global $SRCDEBUG;
$SRCDEBUG=1;

	global $ARCCONFIG;
	global $DBPEDIA_SPARQL_ENDPOINT;
	$LOCALCONFIG=$ARCCONFIG;
	$LOCALCONFIG{'remote_store_endpoint'}=$DBPEDIA_SPARQL_ENDPOINT;
	$LOCALCONFIG{'sparqlscript_default_endpoint'}=$DBPEDIA_SPARQL_ENDPOINT;
	$LOCALCONFIG{'sparqlscript_max_operations'}=100;
	$LOCALCONFIG{'sparqlscript_max_queries'}=10;
	
	$ssp = ARC2::getSPARQLScriptProcessor($LOCALCONFIG);	
	if($SRCDEBUG)
	print "<br>get_dbpedia_redirect($term)...";

	//print "<br>ssp:<br>";
	//var_dump($ssp);
	//print "<br>end of ssp:<br>";
	

	
	//Zuerst oeffne wikipedia mit Term
	//Dann gewinne Resource-Name nach Disambiguation
	
	$scr = '
  ENDPOINT <http://dbpedia.org/sparql>
	$rows1 = SELECT * WHERE 
					{<http://dbpedia.org/resource/'.$term.'> <http://dbpedia.org/property/redirect> ?object.}
';

	
	if($SRCDEBUG)
		print "<br>get_dbpedia_redirect QUERY: <br>".show_xml_string($scr);
	
	$ssp->processScript($scr);
	$rowss[]= $ssp->env['vars']['rows1']['value'];

	//Der erste Eintrag:
	$redirect = $ssp->env['vars']['rows1']['value'][0]['object'];
	$path_elemts=pathinfo($redirect);
	$redirect=$path_elemts['filename'];
	//print "get_dbpedia_redirect: $redirect";
	
	if (!$redirect) $redirect=$term;
	
	if($SRCDEBUG) print "returning $redirect";
	
	
	return trim($redirect);
} //get_dbpedia_triples
	
} // class DBPengine



?>