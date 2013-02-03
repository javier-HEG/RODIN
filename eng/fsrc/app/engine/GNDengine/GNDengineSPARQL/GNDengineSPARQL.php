<?php

	# GND engine using SPARQL
	#
	# Jan 2013
	# fabio.ricci@ggaweb.ch  
	# HEG 
	#
	# PROBLEM: GND is not in SKOS format
	# but still has a thesaurus shape (more general, related)
	# LABELS ARE NOT MARKED in the respective LANGUAGE!
	# THIS MAKES THINGS DIFFICULT: HOW DO YOU RECOGNIZE LANGUAGE IN LABELS?
	# 
	# Todo: insert language recogniser for output
	#

$THISFILE=__FILE__;
$THISCLASSNAME = basename(dirname($THISFILE)); // 
$BASECLASSNAME = basename(dirname(dirname($THISFILE))); // 

#Automatically load upper class
$filename="$BASECLASSNAME.php"; $max=10;
#######################################
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}


	
/*
 * SPARQL engine
 */
	
class GNDengineSPARQL extends GNDengine
{
	
	function __construct() 
	#########################
	{
		parent::__construct();
		$this->currentclassname='GNDengineSPARQL';

		$this->setWordbinding('GND');
	
		$this->solr_collection = ''; // this is a sparql engine
		
	} //GNDengineSPARQL 
	
	

	
	/**
	 * SPARQL implementation of the function, since the data is held
	 * in our server, this function will "succeed" in case there is data
	 */
	protected function testDataSourceResponsiveness($user) 
  {
    $counttriples=count_ARC_triples($this->get_store());
		if ($counttriples)
	    $response="<user>$user</user>";
		else
			$response="<error>LOCAL TRIPLE STORE connection for this ontology seems to be empty!</error>";
    return $response;
  }
	
	
	
	
	
	
	protected function refine_method($term,$action,$lang)
	############################################################
  # Find Terms related to $action 
	{ 
    global $RODINSEGMENT;
		$METHODNAME=$this->myFNameDebug(__FUNCTION__,__FILE__,__LINE__);
		/* Terms in GND have to be considered lower case ! */
		/* Try to make them like this to SQL-Match them */
		if ($this->getSrcDebug()) print "<br>$METHODNAME($term)...";
		
		############################################
		list($node,$label) = $this->extractDescriptor($term);
		############################################
		
		if ($node) # Request is on a node (label) exactely
		{
     list($labels,$descriptors) =  $this->exec_gnd_node_sparql($this->get_store(),$action,$node,$lang,$lang);
		} # node
		###########################################
		else //text
		{
      
      $term= $this->formatAsInThesaurus($term);
			// ----- Search for Labels in STW SKOS Store ------
			list($labels,$descriptors) =  $this->exec_gnd_sparql($this->get_store(),$action,$term,'X',$lang,$lang);
			//if (count($descriptors)==0)
			//list($labels,$descriptors) =  $this->exec_gnd_sparql($this->get_store(),$action,$term,'XX',$lang,$lang);
	
		}  //text	
    // 
    // 
		############################################################
		if (count($labels))
		{
			for($i=0;$i<count($labels);$i++)
			{
				$label=$labels[$i];
				$skos_terms		{($label)}= 100;
				$skos_concepts{($label)}= $descriptors[$i];
			} 
		}
		
		if ($this->getVerbose())
		{
			if (count($skos_terms))
			{
				print "<br>".count($skos_terms)." Terms found!";
				foreach($skos_terms as $label=>$Rank) 	
				{
						print "<br> $action of ($term) --> <b>$label</b>";
				}
			}
			
			if ($this->getSrcDebug()) 
			{	print "<br><br><b>$METHODNAME($term) </b><br>returning ".count($skos_terms)." $action TERMS for ($term):";
				if (count($skos_terms))
				{	$d=0;
					foreach($skos_terms as $te=>$Rank)
						print "<br>".$d++.": $te";
				}
			}
		} // text
    
    
		return array($skos_terms,$skos_concepts); // for each action
	
	} // 
		
		
	
	
	private function exec_gnd_sparql(&$store,$verb,$term,$SearchType,$lang_in,$lang_out)
	##################################
	# 
	# Computes the query to SKOS $verb
	# $verb= related, broader, narrowe
	# returns pair (results, descriptors)
	# example: exec_skos_sparql($store, 'related' ,$term);
	#
	{
		
		if ($this->getSrcDebug())
		{	
			print "<br><br><br>EXEC GND SPARQL $verb,($term),$SearchType,$lang_in,$lang_out <br>";
			$cnt_triples=count_ARC_triples($store);
			print "<br>STORE ($cnt_triples triples): <br>";
			var_dump($store);
		}
		
	$QUERY=<<<EOQ
	prefix gnd2:  <http://d-nb.info/standards/elementset/gnd#>
	select ?x ?d1 ?d2
EOQ;
	
		/*
		 * Issue a query searching for triples involving either a preferred or an alternative input name
		 * and output all names, preferred (in one language!!!) and alternatives (in other languages)
		 */
		switch( $SearchType ) // exact match
		{
			//********************
			case ('X'): // exact match
			//********************
			switch($verb)
			{
				case 'broader':
					$QUERY.=<<<EOQ
	{
		 {
		 	?d2 	gnd2:preferredNameForTheCorporateBody  '$term' .
			?d2 	gnd2:hierarchicalSuperiorOfTheCorporateBody	?d1 .
			?d1 	gnd2:variantNameForTheCorporateBody 	?x .
		 }
		 UNION
		 {
		 	?d2 	gnd2:preferredNameForTheCorporateBody  '$term' .
			?d2 	gnd2:hierarchicalSuperiorOfTheCorporateBody	?d1 .
			?d1 	gnd2:preferredNameForTheCorporateBody 	?x .
		 }
		 UNION
		 {
		 	?d2 	gnd2:variantNameForTheCorporateBody   '$term' .
			?d2 	gnd2:hierarchicalSuperiorOfTheCorporateBody	?d1 .
			?d1 	gnd2:preferredNameForTheCorporateBody  ?x .
		 }UNION
		 {
		 	?d2 	gnd2:variantNameForTheCorporateBody   '$term' .
			?d2 	gnd2:hierarchicalSuperiorOfTheCorporateBody	?d1 .
			?d1 	gnd2:variantNameForTheCorporateBody  ?x .
		 }
	}
EOQ;
					break;
				case 'narrower': // same as broader but inverse (d1/d2)
				$QUERY.=<<<EOQ
	{
		 {
		 	?d2 	gnd2:preferredNameForTheCorporateBody  '$term' .
			?d1 	gnd2:hierarchicalSuperiorOfTheCorporateBody	?d2 .
			?d1 	gnd2:preferredNameForTheCorporateBody 	?x .
		 }
		 UNION
		 {
		 	?d2 	gnd2:variantNameForTheCorporateBody  '$term' .
			?d1 	gnd2:hierarchicalSuperiorOfTheCorporateBody	?d2 .
			?d1 	gnd2:preferredNameForTheCorporateBody 	?x .
		 }
		 UNION
		 {
		 	?d2 	gnd2:preferredNameForTheCorporateBody   '$term' .
			?d1 	gnd2:hierarchicalSuperiorOfTheCorporateBody	?d2 .
			?d1 	gnd2:variantNameForTheCorporateBody  ?x .
		 }
		 UNION
		 		{
		 	?d2 	gnd2:variantNameForTheCorporateBody   '$term' .
			?d1 	gnd2:hierarchicalSuperiorOfTheCorporateBody	?d2 .
			?d1 	gnd2:variantNameForTheCorporateBody  ?x .
		 }
	}
EOQ;
					break;
				case 'related':
				/*
				 * Construct a collecting service using
				 * gnd2:succeedingCorporateBody and/or
				 * gnd2:precedingCorporateBody
				 * in a ring - eliminate doubles
				 * $
				 */	
				$concept = $this->collect_gnd_related($store,$SearchType,$term);
				list($concept,$result) = $this->add_labels($store,$concept,$lang_out);
				$QUERY=''; // non need anymore for a QUERY here.
					break;	
			} // switch ($verb)

			
					break;
		//********************
		case ('XX'):
		//********************
			switch($verb)
			{
				case 'broader':
				$QUERY.=<<<EOQ
	{
		 	?d2 gnd2:preferredNameForTheCorporateBody  ?p .
			FILTER regex(?p, "$term", "i") .
		 	?d2 gnd2:hierarchicalSuperiorOfTheCorporateBody	?d1 .
			?d1 gnd2:variantNameForTheCorporateBody 	?x .
	}
	UNION
	{
		 	?d2 gnd2:variantNameForTheCorporateBody  ?p .
			FILTER regex(?p, "$term", "i") .
		 	?d2 gnd2:hierarchicalSuperiorOfTheCorporateBody	?d1 .
			?d1 gnd2:variantNameForTheCorporateBody 	?x .
	}
EOQ;
			break;
			case 'narrower':
			$QUERY.=<<<EOQ
	{
			?d2 gnd2:preferredNameForTheCorporateBody  ?p .
		 	FILTER regex(?p, "$term", "i") .
			?d1 gnd2:hierarchicalSuperiorOfTheCorporateBody	?d2 .
			?d1 gnd2:preferredNameForTheCorporateBody 	?x .
	}
	UNION
	{
			?d2 gnd2:variantNameForTheCorporateBody  ?p .
		 	FILTER regex(?p, "$term", "i") .
			?d1 gnd2:hierarchicalSuperiorOfTheCorporateBody	?d2 .
			?d1 gnd2:preferredNameForTheCorporateBody 	?x .
	}
EOQ;
				break;
				case 'related':
				/*
				 * Construct a collecting service using
				 * gnd2:succeedingCorporateBody and/or
				 * gnd2:precedingCorporateBody
				 * in a ring - eliminate doubles
				 * $
				 */	
				$concept = $this->collect_gnd_related($store,$SearchType,$term);
				list($concept,$result) = $this->add_labels($store,$concept,$lang_out);
				
				$QUERY=''; // non need anymore for a QUERY here.
				break;			
			} // switch( $verb )

			break;
		} //switch
		
		if ($this->getSrcDebug())
			print "<br><br>exec_gnd_sparql($SearchType) <br>QUERY:<br>".str_replace("\n","<br>",htmlentities($QUERY))."<br>";
			
		// [Javier] I needed to restart the DB connexion
		//$store->closeDBCon();
		//$store->createDBCon();
	
		
		if ($QUERY)
		{
			$result=null;
			$concept=null;
			if (($rows = $store->query($QUERY, 'rows'))) 
			{
				if ($this->getSrcDebug()) 
				{
					print "ROWS: <br>"; var_dump($rows);
				}
						
				$candidate_label_lang='';
				foreach($rows as $row) 	
				{
					$candidate_label=GNDengine::cleanupLabel($row['x']); // YES WE MUST DO IT HERE SO: GND CACHES <expressions> in labels
					if ($lang_out) $candidate_label_lang=detect_language($candidate_label);
					if ($this->getSrcDebug()) 
					{
						print "<br> Lang=".$row['x lang']." for ".$candidate_label." (lang_out=$lang_out)";
						print "<br> DESC for (label) = ".$row['d2']; //FRI: Pickup concept descr for survista call
						print "<br> DESC1 = ".$row['d1']; //FRI: Pickup concept descr for survista call
					}
					//if ($row['x lang']==$lang_out) 
					//Add if really new (not contained or not subsumed)
					if (
								 (!$lang_out || $lang_out==$candidate_label_lang)  							// check language
							&& (!is_array($result) || !subsumed_in($result,$candidate_label)) // check subsumption
							) // onli if really different:
					{
						$result[]= $candidate_label;
						$concept[]= $row['d1'];
						if ($this->getSrcDebug()) print " TAKE LABEL(".$candidate_label.")";
					}
					else {
						if ($this->getSrcDebug()) 
						{
							print "<br> DISCARD RESULT: $candidate_label DESC: (".$row['d1'].") since either not expected language=$candidate_label_lang ($lang_out) or because subsumed in: "; var_dump($result);
						}
					}
				}
			} else {
				if ($this->getSrcDebug())
				{
					print "<br>NO RESULTS FROM QUERY<br>";
					var_dump($result);
				}
			}
			
		} // $QUERY
		
		
		return array($result,$concept);
	} //exec_gnd_sparql
		
	
	
	
	
		//$descriptors= 
		private function collect_gnd_related(&$store,$SearchType,$term)
		{
			$labels=$descriptors=array();
			//Search for all descriptors matching $term:
			$descriptors=$this->get_gnd_descriptors($store,$SearchType,$term,'');

			return collect_gnd_related2($store,$descriptors);
		} // collect_gnd_related
	
	
	
	
	
		/**
		 * Returns enhanced descriptors according to GND related
		 */
		public function collect_gnd_related2(&$store,&$descriptors)
		{
			
			$labels=array();
			if ($this->getSrcDebug()) 
			{
				print "<br>collect_gnd_related_desc called with descriptors:<br>"; var_dump($descriptors);
			}
			
			foreach($descriptors as $desc)
			{
				$new_descriptors=$this->collect_gnd_related_desc($store,$desc,$descriptors);
				//Check unique???
				if ($this->getSrcDebug()) 
				{
					$outputable_descriptor=htmlentities($desc);
					print "<br>collect_gnd_related_desc ($outputable_descriptor) returns: <br>"; var_dump($new_descriptors);
				}
				
				$descriptors = merge_uniquely($descriptors,$new_descriptors);
			} // foreach
			
			
			if ($this->getSrcDebug()) 
			{
				print "<br>collect_gnd_related_desc ($outputable_descriptor) returns: <br>"; var_dump($descriptors);
			}
			
			return $descriptors;
		} // collect_gnd_related2
	
	
	
	
	
		//$descriptors = 
		/**
		 * Returns vector of descriptors beeing in the same classification ring
		 * using precedingCorporateBody and succeedingCorporateBody
		 * this method uses a controlled recursion to navigate among the predicates
		 * 
		 * @param &$store the ARC2 store to be used
		 * @param $desc the descriptor to be used
		 * @param &$collected_descriptors the already collected descriptors (not to repeate, recursion control)
		 */ 
		private function collect_gnd_related_desc(&$store,$desc,&$collected_descriptors)
		{
			$labels=$descriptors=array();
			if ($this->getSrcDebug()) 
				print "<br>collect_gnd_related_desc ($desc) ENTRY<hr>";
			
			$p_descriptors=$this->collect_gnd_rel_desc($store,$desc,'precedingCorporateBody',$collected_descriptors);
			$s_descriptors=$this->collect_gnd_rel_desc($store,$desc,'succeedingCorporateBody',$collected_descriptors);
			$sth_new=false;
			//Did we found sth new?
			$sth_new = (is_array($p_descriptors) && count($p_descriptors))
							|| (is_array($s_descriptors) && count($s_descriptors));

			if ($sth_new)
			{
				$new_descriptors				=	merge_uniquely(	$p_descriptors,				  $s_descriptors	 );
				$collected_descriptors 	= merge_uniquely(	$collected_descriptors, $new_descriptors ); //recursion control
				
				if ($this->getSrcDebug()) 
				{
					print "<br>collect_gnd_related_desc ($desc) STH NEW ".count($new_descriptors)." new desc: ";
					foreach($new_descriptors as $nd) print "<br>NEW DESC $nd";
				}	
				
				
			 	//foreach found descriptor continue search recursively
				foreach($new_descriptors as $dx) 
				{
					$related_dx = $this->collect_gnd_related_desc($store,$dx,$collected_descriptors);
					$descriptors = merge_uniquely($new_descriptors,$related_dx);
				}
			}
			
			
			if ($this->getSrcDebug()) 
			{
				print "<br>collect_gnd_related_desc ($desc) EXITING with: ";
				foreach($descriptors as $nd) print "<br>FOUND DESC $nd";
			}	
			
			return $descriptors;
		} // collect_gnd_related_desc
		

		
		/*
		 * Query the $store in order to find new descriptors in the classification of $desc
		 * @param $store
		 * @param $desc Descriptor
		 * @param $rel one of (precedingCorporateBody,succeedingCorporateBody)
		 * @param &$collected_descrs Vector containing already collected values (not to collect in this call)
		 */
		private function collect_gnd_rel_desc(&$store,$desc,$rel,&$collected_descrs)
		{
			$labels=$descriptors=array();
			$QUERY=<<<EOQ
	prefix gnd2:  <http://d-nb.info/standards/elementset/gnd#>
	select ?d
	{
	 <$desc> gnd2:$rel ?d.
	}
EOQ;
			
			if ($this->getSrcDebug())
				print "<br><br>collect_gnd_rel_desc($desc,$rel) <br>QUERY:<br>".str_replace("\n","<br>",htmlentities($QUERY))."<br>";
		

			if (($rows = $store->query($QUERY, 'rows'))) 
			{
					if ($this->getSrcDebug()) 
					{
						print "collect_gnd_pred_desc ($desc) ROWS: <br>"; var_dump($rows);
					}
				foreach($rows as $row) 	
					{
						if ($this->getSrcDebug()) 
						{
							print " descriptor=".$row['d']; //FRI: Pickup concept descr for survista call
						}
						$candidate_descriptors[]= $row['d'];
					}
			}
			
			
			//Check whether we are about to returns nothing new... 
			if (is_array($collected_descrs) && count($collected_descrs) && count($candidate_descriptors))
			{
				foreach($candidate_descriptors as $d)
				{
					//add only if not already in $collected_descrs
					if (!in_array($d, $collected_descrs))
					$descriptors[]=$d;
				}
			}
			else // nothing to check -> returns $candidate_descriptors
				$descriptors = $candidate_descriptors;
			
			
			if ($this->getSrcDebug()) 
			{
				print "<br>collect_gnd_rel_desc($desc,$rel) returns: <br>"; var_dump($descriptors);
			}
			
			return $descriptors;
		} // collect_gnd_rel_desc
		
		
		
		
		
		/**
		 * SEARCH FUNCTION
		 * returns descriptors matching $text (as main or alt label)
		 * @param $store the ARC2 store to be used
		 * @param $SearchType X or XX
		 * @param $text the text to be used in the search
		 */ 
		private function get_gnd_descriptors(&$store,$SearchType,$text)
		{
			$labels=$descriptors=array();
			
			switch($SearchType)
			{
				case 'X':
					$QUERY=<<<EOQ
					prefix gnd2:  <http://d-nb.info/standards/elementset/gnd#>
					select ?d
					{
						{
						 ?d gnd2:preferredNameForTheCorporateBody '$text' .
						}
						UNION
						{
						 ?d gnd2:variantNameForTheCorporateBody '$text' .
						}
					}
EOQ;
					break;
				case 'XX':
					$QUERY=<<<EOQ
					prefix gnd2:  <http://d-nb.info/standards/elementset/gnd#>
					select ?x ?d
					{
						{
						 ?d gnd2:preferredNameForTheCorporateBody ?x .
						}
						UNION
						{
						 ?d gnd2:variantNameForTheCorporateBody ?x .
						}
						FILTER regex(?x,"$text","i") .
					}
EOQ;
					break;
			} // switch
			
			if ($this->getSrcDebug()) 
			{
				print "<br>get_gnd_descriptors ($text) <br>QUERY: <br>".str_replace("\n","<br>",htmlentities($QUERY));
			}	
			
			if (($rows = $store->query($QUERY, 'rows'))) 
			{
					if ($this->getSrcDebug()) 
					{
						print "get_gnd_descriptors ($SearchType,$text,$lang_out) ROWS: <br>"; var_dump($rows);
					}
					foreach($rows as $row) 	
					{
						if ($this->getSrcDebug()) 
						{
							print " descriptor: ".$row['d']; //FRI: Pickup concept descr for survista call
						}
						$descriptors[]= $row['d'];
					}
			}
			
			return $descriptors;
		} // get_gnd_descriptors
		
		
		
		
		
		
		/**
		 * SEARCH FUNCTION
		 * returns descriptors matching $text (as main or alt label)
		 * DO NOT DELETE THIS METHOD: Used in SOLR partial eval!
		 * @param $store the ARC2 store to be used
		 * @param $descriptor The descriptor to be used
		 */ 
		public function get_gnd_narrower_descriptors(&$store,$descriptor)
		{
			$descriptors=array();
			
			$QUERY.=<<<EOQ
	prefix gnd2:  <http://d-nb.info/standards/elementset/gnd#>
	select ?n
	{
			?n gnd2:hierarchicalSuperiorOfTheCorporateBody	<$descriptor> .
	}
EOQ;
			
			if ($this->getSrcDebug()) 
			{
				$outputable_desc=htmlentities($descriptor);
				print "<br>get_gnd_narrower_descriptors($outputable_desc) <br>QUERY: <br>".str_replace("\n","<br>",htmlentities($QUERY));
			}	
			
			if (($rows = $store->query($QUERY, 'rows'))) 
			{
					if ($this->getSrcDebug()) 
					{
						print "get_gnd_narrower_descriptors ($outputable_desc) ROWS: <br>"; var_dump($rows);
					}
					foreach($rows as $row) 	
					{
						if ($this->getSrcDebug()) 
						{
							print " descriptor: ".$row['n']; //FRI: Pickup concept descr for survista call
						}
						$descriptors[]= $row['n'];
					}
			}
			
			return $descriptors;
		} // get_gnd_narrower_descriptors
		
		
		
		
		
				
		
		
		
		
	/*
	 * Compute labels for $lang_out
	 * returns a pair of aligned arrays (labels, desc)
	 * list($labels,$descr)=
	 */
	 private function add_labels(&$store,&$descriptors,$lang_out='')
	 {
	 	if ($this->getSrcDebug()) 
		{
			print "<hr>add_labels called with lang_out=$lang_out and with descriptors: <br>"; var_dump($descriptors);
		}
		
		 	$labels=$new_descriptors=array();
	 		foreach($descriptors as $desc)
			{
	 	
			$QUERY=<<<EOQ
	prefix gnd2:  <http://d-nb.info/standards/elementset/gnd#>
	select ?x
	{
		{
			<$desc> gnd2:preferredNameForTheCorporateBody  ?x .
		}
		UNION
		{
			<$desc> gnd2:variantNameForTheCorporateBody  ?x .
		}
	}
EOQ;
				if ($this->getSrcDebug()) 
					print "<br><br>add_labels QUERY: <br>".str_replace("\n","<br>",htmlentities($QUERY));
				

				if (($rows = $store->query($QUERY, 'rows'))) 
				{
					if ($this->getSrcDebug()) 
					{
						print "<br>add_labels ($desc) ROWS: <br>"; var_dump($rows);
					}
					
					$candidate_label_lang='';
					foreach($rows as $row) 	
					{
						$candidate_label=$this->cleanupLabel($row['x']); // YES WE MUST DO IT HERE SO: GND CACHES <expressions> in labels
						if ($lang_out) $candidate_label_lang = detect_language($candidate_label);
						
						if ($this->getSrcDebug()) 
						{
							if ($lang_out)
							{ //Check lang info in data:
								if (!$row['x lang']) print "<br>!!!! NO LANG INFO for ".$candidate_label;
								print "<br> Lang=".$row['x lang']." for ".$candidate_label." (lang_out=$lang_out)";
							}
							print "<br>label($desc) = ".$candidate_label; //FRI: Pickup concept descr for survista call
						}
						//In case a lang-out is set
						//compute language and see if it coresponds
						if ($lang_out=='' || ($candidate_label_lang==$lang_out))
						{
							if ($candidate_label)
							{
								$labels[]= $candidate_label;
								$new_descriptors[]=$desc;
								if ($this->getSrcDebug()) 
								print " TAKE LABEL! ";
							}
						}
						else 
						{
							if ($this->getSrcDebug()) 
								print " SKIP LABEL! ";
						}	
					} // foreach ($rows)
				}
				else {
					if ($this->getSrcDebug()) 
						print "<br>add_labels ($desc) NO LABLES ROWS !!! ";
				}
			} // foreach($descriptors as $desc)
			
			//Return aligned pair ov vectors:
			return array($new_descriptors,$labels);	 			
	 		
	 } // add_labels
	
	
		
		
		
	
		private function exec_gnd_node_sparql($store,$verb,$descriptor,$lang_in,$lang_out)
	##################################
	# 
	# Computes the query to GND  $verb
	# $verb= related, broader, narrowe
	# returns pair (results, descriptors)
	# example: exec_skos_sparql($store, 'related' ,$term);
	#
	{
		
	if ($this->getSrcDebug())
		{	
			print "<br>STORE: <br>";
			var_dump($store);
		}

$QUERY=<<<EOQ
	prefix gnd2:  <http://d-nb.info/standards/elementset/gnd#>
	select ?x ?d1
EOQ;

	switch($verb)
	{
		case 'broader': 
		$QUERY.=<<<EOQ
		{
			?$descriptor gnd2:hierarchicalSuperiorOfTheCorporateBody	?d1 .
			?d1 gnd2:preferredNameForTheCorporateBody ?x .
	  }
EOQ;
			break;
		case 'narrower': 
		$QUERY.=<<<EOQ
		{
			?d1 gnd2:hierarchicalSuperiorOfTheCorporateBody	?$descriptor.
			?d1 gnd2:preferredNameForTheCorporateBody ?x .
	  }
EOQ;
			break;
		case 'related': 
			//Here we need an undefined loop ... collecting the precedings and succedings pieces
			
			
			break;
	} // switch


		
		
		if ($this->getSrcDebug())
			print "<br><br>exec_gnd_node_sparql($SearchType): $QUERY <br>";
			
		// [Javier] I needed to restart the DB connexion
		$store->closeDBCon();
		$store->createDBCon();
		
		$result=array();
		if ($rows = $store->query($QUERY, 'rows')) 
		{
			foreach($rows as $row) 	
			{
				if ($this->getSrcDebug()) 
				{
					print "<br> Lang=".$row['x lang']." for ".$row['x']." (lang_out=$lang_out)";
					print " concept=".$row['d1']; //FRI: Pickup concept descr for survista call
				}
				
				if ($row['x lang']==$lang_out) 
				{
					$result[]= $row['x'];
					$concept[]= $row['d1'];
				}
			}
		}
		return array($result,$concept);
	} //exec_gnd_node_sparql
		
	
	
	
} // class GNDengineSPARQL



?>