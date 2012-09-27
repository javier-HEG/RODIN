<?php

	# DBP Engine 1
	#
	# Mai 2011
	# Fabio.fr.Ricci@hesge.ch  
	# HEG 

$THISFILE=__FILE__;
$THISCLASSNAME = basename(dirname($THISFILE)); // 
$BASECLASSNAME = basename(dirname(dirname($THISFILE))); // 

#Automatically load upper class
$filename="$BASECLASSNAME.php"; $max=10;
#######################################
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}


	
	
	
	
class DBPengine1 extends DBPengine 
{
	
	private $dbpedia_base;
	
	function __construct() 
	#########################
	{
		parent::__construct();
		
		global $DBPEDIA_BASE;
		
		$this->dbpedia_base=$DBPEDIA_BASE;
		$this->setWordbinding('DBP');
		//print "<br> STWengine1<hr>"; var_dump($this->get_zbwdbpedia_store());print "<hr>";
	} //STWengine1 
	
	

	
	
	
	
	protected function refine_method($term,$action,$lang)
	############################################################
	/*
	 * Finde Terme zu $action (lade wenn notw. Triples in den store)
	 */
	{
		$METHODNAME=$this->myFNameDebug(__FUNCTION__,__FILE__,__LINE__);
				
		$rowss = $this->get_dbpedia_local_triples($term,$lang);
		//render_rowss($rowss,"dbpedia zu $disambiguated_term");

		$found_terms=$this->get_dbpedia_skos_terms($term,$rowss,$lang,$action);

		if (count($found_terms))
		{
			shuffle($found_terms);
			foreach($found_terms as $label) 	
			{
					$out_terms{$label}=100;
			} 
		}
		
		
		if ($this->verbose)
		{
			print "<br><b>$METHODNAME($term,$action) </b> returning $action terms: ";
			if (count($found_terms))
			foreach ($out_terms as $found_term)
				print "<br>$found_term";	
		}
		
		return $out_terms;	
		
	
	} // find_dbpedia_terms
	
	
	function get_dbpedia_skos_terms($term,$rowss,$language,$action)
	/*
	 * action = related|broader|narrower 
	 * 
	 * 
	 * 
	 */
	{
		
		#################################
		#
		# Sammle die related terms aus den bereits
		# besorgten Objekten in $rowss
		#
		$METHODNAME=$this->myFNameDebug(__FUNCTION__,__FILE__,__LINE__);
		$ok=true;
		#################################
		
		if ($this->getSrcDebug()) print "<br>get_dbpedia_skos_terms(<b>$action</b> of $term)...";
		
		# Gibt es eine skos:subject Cetegory, die genau so heisst wie $term?
		
		$SKOS_SUBJECTS=$rowss{'dcterms:subject'};
		if ($this->getSrcDebug()) {
			print "<br> SKOS_SUBJECTS: ".count($SKOS_SUBJECTS)." items<br>";
			var_dump($SKOS_SUBJECTS);
			print "<br><br>";
		}
		
		
		if (is_array($SKOS_SUBJECTS) && count($SKOS_SUBJECTS))
		foreach($SKOS_SUBJECTS as $subject)
		{
			$sterm=$subject['o'];
			if ($this->getSrcDebug()) print "<br> Evaluating /$term/i in dbpedia term <b>$sterm</b>";
			if (preg_match("/$term/i",$sterm))
			{
				$SKOS_SELF=$term;
				break;
			}
			else $skosSubjectDelta[]=$subject;
		}
			
		
		//if ($SKOS_SELF || 1 )
		#### Open SKOS Self Category
		{
			switch($action)
			{
				case('broader'):
					//get broader and narrower
					$skos_terms=$this->get_dbpedia_skos_broader_terms($this->get_dbpedia_store(),"Category:$term",$term,$lang);
					break;
				case('narrower'):
					$skos_terms=$this->get_dbpedia_skos_narrower_terms("Category:$term",$term,$lang);
					break;
				case('related'):	
					//get related (nimm die �brigen skos:subject ohne Term selbs)
					$skos_terms=$this->extract_objects($skosSubjectDelta);
					break;
			} // switch
		}	
		
		
		
		if ($this->getSrcDebug()) print "<br><br><b>$METHODNAME($action,$term) </b> returning ".count($skos_terms)." Terms<br>";
		
		return $skos_terms; // je action
	} //get_dbpedia_skos_terms

	
		
		
	protected function get_dbpedia_local_triples($term,$lang='en')
	#############################################
	{
		$METHODNAME=$this->myFNameDebug(__FUNCTION__,__FILE__,__LINE__);
				
		#################################
		#
		# Aendere den Store auf dbpedia
		# Anname: Term ist ein dbpedia-Term
		#
		
		$ok=true;
		#################################
		if ($this->getSrcDebug()) print "<br>$METHODNAME($term)...";
		
			
			
		$ok = $this->evtl_load_dbpedia_doc_into_store($this->get_dbpedia_store(),$term,$language,$lang);
		if ($ok)
		{
			$predicates[]= 'dcterms:subject';
			$predicates[]= 'dbpedia2:disambiguates';
			$predicates[]= 'owl:sameAs';
			$predicates[]= 'dbprop:redirect';
		}
			
		if (count($predicates))
		foreach ($predicates as $predicate) {
			$rowss{$predicate} = $this->simple_query_store($this->get_dbpedia_store(),$predicate,$term,$lang);
			//print "<br><b>:::$predicate</b> : " . count($rowss[$predicate]) . '<br>';
		}
	
		if ($this->getSrcDebug()) print "returning $rowss";
		
		return $rowss;
	} //get_dbpedia_triples2_remoteStore
		
	
	
	
	protected function evtl_load_dbpedia_doc_into_store($store,$term,$lang)
	##############################################
	{
		$METHODNAME=$this->myFNameDebug(__FUNCTION__,__FILE__,__LINE__);
				
		global $DBPEDIA_PREFIX;
		global $DBPEDIA_BASE;
		$ok=true; // Anname;
	
	
		if ($this->getSrcDebug()) print "<br>evtl_load_dbpedia_doc_into_store($term)...";
	
		#################################
		#################################
		#################################
		//Check $term already in store?
		//Es gibt ein Label zum Term
	
		$QUERY_CHECK_TRIPLES="
		$DBPEDIA_PREFIX
		SELECT ?o
		{
			:$term rdfs:label ?o.
		} LIMIT 1";
		
		//print "<br>QUERY_CHECK_TRIPLES:<br>$QUERY_CHECK_TRIPLES<br>";
		$documentURL="$DBPEDIA_BASE/data/$term.rdf";
		$documentURL2="$DBPEDIA_BASE/page/$term";
		$documentHREF="<a href='$documentURL2' target=_blank>$documentURL2</a>";
			
		$rows = $store->query($QUERY_CHECK_TRIPLES, 'rows');
		if (count($rows)==0)
		{	
			if ($this->getSrcDebug()) 
			{
				fontprint("Adding document $documentHREF ($documentURL) to local store",'red');
			
				print "<hr>";
				//var_dump($store);
				print "<hr>";
			}
			$rs= $store->query("LOAD <$documentURL>");
			if ($errs = $this->get_dbpedia_store()->getErrors()) {
				$ok=false;
				if ($this->getSrcDebug()) 
				{
					print "evtl_load_dbpedia_doc_into_store($documentURL):<br>";
					foreach($errs as $err)
					fontprint ("<br>".$err.' ','red');
				}
			}
			
			if ($this->getSrcDebug())
			{
				$duration = $rs['query_time'];
				$added_triples = $rs['result']['t_count'];
				$load_time = $rs['result']['load_time'];
				$index_update_time = $rs['result']['index_update_time'];
				
				print "<hr>
						duration: $duration
				<br>added_triples: $added_triples
				<br>load_time: $load_time
				<br>index_update_time: $index_update_time
				<br>";
			}
		}	
		else
		{
			if ($this->getSrcDebug()) fontprint ("(Dokument $documentHREF bereits in LocalStore)",'#44aa44');	
		}
			
		#################################
		#################################
		#################################
		if ($this->getSrcDebug()) print "<br>evtl_load_dbpedia_doc_into_store returning $ok";
	
		return $ok;
	}
	
	
	
	
function simple_query_store($store,$predicate,$term,$lang)
#############################################
#
#
{
	$METHODNAME=$this->myFNameDebug(__FUNCTION__,__FILE__,__LINE__);
	global $DBPEDIA_PREFIX; 
	
	if ($this->getSrcDebug()) 
		{	
			print "$METHODNAME(<b>predicate=$predicate</b>)";
		}
	
	
	if ($predicate=='dbpedia2:disambiguates')
	{
		$termDisambiguation=$term."_%28disambiguation%29";
		$QUERY="
			$DBPEDIA_PREFIX
			SELECT ?o
			{
				 :$termDisambiguation $predicate ?o.
			}";
	}
	else if (preg_match("/is (.+) of/",$predicate,$match))
	//INVERSE RELATION (MUST BE REMOTE)
	{
		$predicate=$match[1];
		
		$QUERY="
			$DBPEDIA_PREFIX
			SELECT ?o
			{
			 	?o $predicate <http://dbpedia.org/resource/$term> .
			}";
	}
	else
	{
	 	$QUERY="
			$DBPEDIA_PREFIX
			SELECT ?o 
			{
				 $term $predicate ?o.
			}";
		}
	
		$QUERY.="
		 LIMIT 20";
	
		if ($this->getSrcDebug()) 
		{	
			print "<br>$METHODNAME:<br>STORE:<br>"; var_dump($store);
			print "<br>QUERY:<br>".fontprint(show_xml_string($QUERY),'green')."<br>";
		}
		$result = $store->query($QUERY, 'rows');
		
		if ($this->getSrcDebug()) 
		{	
			if ($errs = $store->getErrors()) {
					print "$METHODNAME(QUERY ERROR):<br>";
					foreach($errs as $err)
					fontprint ("<br>".$err.' ','red');
			}
			print "<br>$METHODNAME:<br>RESULT:<br>"; var_dump($result);
		}
		
		
		
		
		return $result;
}
	
	
	
	
	
	
	protected function get_dbpedia_skos_broader_terms($store,$skosCategoryTerm,$term,$language='en')
	#############################################
	#
	# Liefert dbpedia SKOS broader terms (array)
	#
	#
	{
		$METHODNAME=$this->myFNameDebug(__FUNCTION__,__FILE__,__LINE__);
		$ok=true;
		#################################
		
		if ($this->getSrcDebug()) print "<br><b>get_dbpedia_skos_broader_terms($skosCategoryTerm,$term)...</b>";
	
		
		$ok = $this->evtl_load_dbpedia_doc_into_store($store,$skosCategoryTerm,$language);
		if ($ok)
		{
			$predicates[]= 'skos:broader';
		}
			
		if (count($predicates))
		foreach ($predicates as $predicate)
			$rows = $this->simple_query_store($store,$predicate,$skosCategoryTerm,$lang);
		
			$termvector = extract_objects($rows);
			
			if ($this->getSrcDebug())
			{
				print "<br>$METHODNAME BROADER terms:<br>";
				foreach($termvector as $termx) print "<br>Term: $termx";
				//var_dump($termvector);
				print "<br>";
			}
			
		return $termvector;
	} //get_dbpedia_skos_broader_terms


	
	
	
	protected function get_dbpedia_skos_narrower_terms($skosCategoryTerm,$term,$language='en')
	#############################################
	#
	# Liefert dbpedia broader und narrower
	#
	#
	{
		$METHODNAME=$this->myFNameDebug(__FUNCTION__,__FILE__,__LINE__);
				
		#################################
		#
		# Aendere den Store auf dbpedia
		# Anname: Term ist ein dbpedia-Term
		#
		
		if ($this->getSrcDebug()) {
			print "<br>$METHODNAME(".($skosCategoryTerm).",".$term.")...<br>";
		}
		
		#################################
		#################################
		#
		# Search remote for inverse
		# of skos:broader
		#
		# ADD THESE ON local store?
		#
		#################################
		#################################
		$rpredicates=array();
		
		if ($this->get_dbpedia_remote_store())
		{
			$rpredicates[]= 'is skos:broader of'; //Die sind noch in dbpedia store (ausssen!!!)
			//$rpredicates[]= 'skos:broader';
		}
		else {
			if ($this->getSrcDebug())
			print "ACHTUNG: PROBLEM BEIM REMOTE STORE";
		}
		
		foreach ($rpredicates as $rpredicate)
			$rows = $this->simple_query_store($this->get_dbpedia_remote_store(),$rpredicate,$skosCategoryTerm,$lang);
		
		$termvector = extract_objects($rows);
			
		if ($this->getSrcDebug())
			{
				print "<br>$METHODNAME: NARROWER terms:<br>";
				foreach($termvector as $termx) print "<br>Term: $termx";
				//var_dump($termvector);
				print "<br>";
			}
		return $termvector;
	} //get_dbpedia_skos_narrower_terms
		
	
	
	
	
	
	protected function extract_objects($rows)
	/*
	 * From rows coming from ARC2 (difining triples)
	 * 
	 */
	{
		$objects=array();	
		if (is_array($rows) && count($rows))
		{
			foreach($rows as $row)
			{
				$value=$row['o'];	
				if (preg_match("/resource\/Category\:(.*)/",$value,$m))
				{
					$value=$m[1];
				}
				$value=urldecode($value);
				//else print " NO MATCH CATEGORY in $value";
				$objects[]=$value;	
			}
		}
		return $objects;
	}
	
	
	
	
} // class DBPengine1



?>