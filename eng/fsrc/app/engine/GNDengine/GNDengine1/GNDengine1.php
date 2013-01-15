<?php

	# STW Engine 2 (improvement of STW engine 1)
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


	
/*
 * SPARQL engine
 */
	
class GNDengine1 extends GNDengine
{
	
	function __construct() 
	#########################
	{
		parent::__construct();
		$this->currentclassname='GNDengine1';

		$this->setWordbinding('GND');
	
		$this->solr_collection = ''; // this is a sparql engine
		
	} //GNDengine1 
	
	

	
	
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
  # Find Terme related to $action 
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
			if ($this->getVerbose()) print "<br>Checking RODINSEGMENT $RODINSEGMENT";
			
		}  //text	
    // 
    // 
		############################################################
		if (count($labels))
		{
			for($i=0;$i<count($labels);$i++)
			{
				$label=$labels[$i];
				$skos_terms		{cleanup_ZBW($label)}= 100;
				$skos_concepts{cleanup_ZBW($label)}= $descriptors[$i];
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
			{	print "<br><br><b>$METHODNAME($term) </b> returning :";
				if (count($skos_terms))
				foreach($skos_terms as $te=>$Rank)
					print "<br>$te";
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
	select ?x ?d1 
EOQ;
	
		
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
			?d1 	gnd2:preferredNameForTheCorporateBody 	?x .
		 }
		 UNION
		{
		 	?d2 	gnd2:variantNameForTheCorporateBody   '$term' .
			?d2 	gnd2:hierarchicalSuperiorOfTheCorporateBody	?d1 .
			?d1 	gnd2:preferredNameForTheCorporateBody  ?x .
		 }
	}
EOQ;
					break;
				case 'narrover': // same as broader but inverse (d1/d2)
				$QUERY.=<<<EOQ
	{
		 {
		 	?d2 	gnd2:preferredNameForTheCorporateBody  '$term' .
			?d1 	gnd2:hierarchicalSuperiorOfTheCorporateBody	?d2 .
			?d1 	gnd2:preferredNameForTheCorporateBody 	?x .
		 }
		 UNION
		{
		 	?d2 	gnd2:variantNameForTheCorporateBody   '$term' .
			?d1 	gnd2:hierarchicalSuperiorOfTheCorporateBody	?d2 .
			?d1 	gnd2:preferredNameForTheCorporateBody  ?x .
		 }
	}
EOQ;
					break;
				case 'narrover':
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
			?d2 gnd2:preferredNameForTheCorporateBody  ?x .
			?d2 gnd2:hierarchicalSuperiorOfTheCorporateBody	?d1 .
			?d2 gnd2:preferredNameForTheCorporateBody 	?p .
		 	FILTER regex(?p, "$term", "i") .
	}
EOQ;
			break;
			case 'narrower':
			$QUERY.=<<<EOQ
	{
			?d2 gnd2:preferredNameForTheCorporateBody  ?x .
			?d2 gnd2:hierarchicalSuperiorOfTheCorporateBody	?d1 .
			?d2 gnd2:preferredNameForTheCorporateBody 	?p .
		 	FILTER regex(?p, "$term", "i") .
	}
EOQ;
				break;
				case 'narrower':
					
				//Special case: Collect preceeding/succeeding units...
				
				break;			
			} // switch( $verb )

			break;
		} //switch
		
		if ($this->getSrcDebug())
			print "<br><br>exec_gnd_sparql($SearchType) QUERY:<br>".str_replace("\n","<br>",htmlentities($QUERY))."<br>";
			
		// [Javier] I needed to restart the DB connexion
		//$store->closeDBCon();
		//$store->createDBCon();
	
	
		$result=array();
		if (($rows = $store->query($QUERY, 'rows'))) 
		{
			if ($this->getSrcDebug()) 
			{
				print "ROWS: <br>"; var_dump($rows);
			}
					
			
			foreach($rows as $row) 	
			{
				if ($this->getSrcDebug()) 
				{
					print "<br> Lang=".$row['x lang']." for ".$row['x']." (lang_out=$lang_out)";
					print " concept=".$row['d1']; //FRI: Pickup concept descr for survista call
					print " concept=".$row['d2']; //FRI: Pickup concept descr for survista call
				}
				if ($row['x lang']==$lang_out) 
				{
					$result[]= $row['x'];
					$concept[]= $row['d1'];
				}
			}
		} else {
			if ($this->getSrcDebug())
			print "<br>NO RESULTS FROM QUERY<br>";
		}
		
		return array($result,$concept);
	} //exec_gnd_sparql
		
	
	
	
	
	
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
		
	
	
	
} // class GNDengine1



?>