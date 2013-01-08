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


	
	
	
class STWengine2 extends STWengine
{
	
	function __construct() 
	#########################
	{
		parent::__construct();
		$this->currentclassname='STWengine2';

		$this->setWordbinding('STW');
		//print "<br> STWengine2<hr>"; var_dump($this->get_zbwdbpedia_store());print "<hr>";
	} //STWengine1 
	
	

	
	
	
	
	protected function refine_method($term,$action,$lang)
	############################################################
  # Find Terme related to $action 
	{ 
    global $RODINSEGMENT;
		$METHODNAME=$this->myFNameDebug(__FUNCTION__,__FILE__,__LINE__);
		/* Terms in STW are First letter capital */
		/* Try to make them like this to SQL-Match them */
		if ($this->getSrcDebug()) print "<br>$METHODNAME($term)...";
		
		############################################
		list($node,$label) = $this->extractDescriptor($term);
		############################################
		
		if ($node) # Request is on a node (label) exactely
		{
     list($labels,$descriptors) =  $this->exec_skos_node_sparql($this->get_store(),$action,$node,$lang,$lang);
		} # node
		###########################################
		else //text
		{
      
      $term= $this->formatAsInThesaurus($term);
			// ----- Search for Labels in STW SKOS Store ------
			list($labels,$descriptors) =  $this->exec_skos_sparql($this->get_store(),$action,$term,'X',$lang,$lang);
				
      
			if ($this->getVerbose()) print "<br>Checking RODINSEGMENT $RODINSEGMENT";
			#8.8.2011: REQUIREMENT RENE SCHNEIDER: On /st/ and on /x/ no XXL			
// 			if ($RODINSEGMENT<>'st' && $RODINSEGMENT <>'x' )
			#21.11.2011: Fabio Ricci: XX may be performed if X gave nothing...
			{
				$skos_terms=array();
				if (count($labels)==0)
				{
					if ($this->getVerbose()) print "<br><b>NO $action terms</b> to ($term) found directly in Ontology! <b>Loosening down search to pattern matching XX</b><br>";
					$SearchType='XX';			
					list($labels,$descriptors) =  $this->exec_skos_sparql($this->get_store(),$action,$term,'XX',$lang,$lang);
        }
				
				if ($RODINSEGMENT<>'st' && $RODINSEGMENT <>'x'  &&  0)
				{
          
					if (count($labels)==0)
					{
						if ($this->getVerbose()) print "<br><b>NO $action terms</b> to ($term) found directly in Ontology! <b>Loosening down search to pattern matching XXL</b><br>";
								list($labels,$descriptors) =  $this->exec_skos_sparql($this->get_store(),$action,$term,'XXL',$lang,$lang);
					}
          
				}
			}
			
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
		
		
	
	
	private function exec_skos_sparql($store,$verb,$term,$SearchType,$lang_in,$lang_out)
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
			print "<br><br><br>EXEC SKOS SPARQL<br>";
			print "<br>STORE: <br>";
			var_dump($store);
		}
		
		switch( $SearchType ) // exact match
		{
			case ('X'): // exact match
	$QUERY=<<<EOQ
	
	prefix skos:  <http://www.w3.org/2004/02/skos/core#>
	
	select ?x ?d1 
	where
	{
		 {
			?d1 	skos:prefLabel '$term' .
			?d1 	skos:$verb 		?d2 .
			?d2 	skos:prefLabel 	?x .
		 }
		 UNION
		{
			?d1 	skos:altLabel  '$term' .
			?d1 	skos:$verb 			?d2 .
			?d2 	skos:prefLabel 	 ?x.
		 }
	}
EOQ;
		break;

		case ('XX'):
		
			$QUERY=<<<EOQ
	
	prefix skos:  <http://www.w3.org/2004/02/skos/core#>
	
	select ?x ?d1
	where
	{
		 {
			FILTER regex(?p, "$term", "i") 
			?d2 	skos:prefLabel ?x .
			?d1 	skos:$verb 		?d2 .
			?d1 	skos:prefLabel ?p .
		 }
	}
EOQ;
		break;
		case ('XXL'):

			$QUERY=<<<EOQ
	
	prefix skos:  <http://www.w3.org/2004/02/skos/core#>
	
	select ?x ?d1
	where
	{
		 {
			?d1 	skos:prefLabel ?p  .
			?d1 	skos:$verb 		?d2 .
			?d2 	skos:prefLabel 	?x .
			FILTER regex(?p, "$term", "i")
		 }
		 UNION
		{
			?d1 	skos:altLabel ?p  .
			?d1 	skos:$verb 		?d2 .
			?d2 	skos:prefLabel ?x	 .
			FILTER regex(?p, "$term", "i")
		 }
	}
EOQ;
		} //switch
		
		if ($this->getSrcDebug())
			print "<br><br>exec_skos_sparql($SearchType): $QUERY <br>";
			
		// [Javier] I needed to restart the DB connexion
		$store->closeDBCon();
		$store->createDBCon();
		
		if ($this->getSrcDebug())
			print "<br><br>FINISHED reloading the store<br>";
		
		$result=array();
		if ($rows = $store->query($QUERY, 'rows')) 
		{
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
	} //exec_skos_sparql
		
	
	
	
	
	
		private function exec_skos_node_sparql($store,$verb,$descriptor,$lang_in,$lang_out)
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
			print "<br>STORE: <br>";
			var_dump($store);
		}

		if (preg_match("/descriptor\/(\d*)-(\d*)/",$descriptor,$match))
		{
			$num=$match[1].'-'.$match[2];
			$dprefix=" prefix desc:  <http://zbw.eu/stw/descriptor/> ";
		} else
		if (preg_match("/thsys\/(\d*)/",$descriptor,$match))
		{
			$num=$match[1];
			$dprefix=" prefix desc:  <http://zbw.eu/stw/thsys/> ";
		}
		$dterm="desc:$num";
		
	
	$QUERY=<<<EOQ
	
	prefix skos:  <http://www.w3.org/2004/02/skos/core#>
	$dprefix
	
	select ?x ?d1
	where
	{
		 {
			$dterm   	skos:$verb 		?d1 .
			?d1 	skos:prefLabel 	?x .
		 }
		 UNION
		{
			$dterm 	skos:$verb 			?d1 .
			?d1 	skos:prefLabel 	 ?x.
		 }
		}
EOQ;
		
		
		if ($this->getSrcDebug())
			print "<br><br>exec_skos_node_sparql($SearchType): $QUERY <br>";
			
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
	} //exec_skos_node_sparql
		
	
	
	
	
	
		
		
	
		
	
	
	
	
	
	
	
	
} // class STWengine1



?>