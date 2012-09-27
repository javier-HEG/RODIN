<?php

	# STW Engine 1
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


	
	
	
class STWengine1 extends STWengine
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
		$METHODNAME=$this->myFNameDebug(__FUNCTION__);
		/* Terms in STW are First letter capital */
		/* Try to make them like this to SQL-Match them */
		$term = strtolower($term);
		$term = strtoupper(substr($term,0,1)).substr($term,1,strlen($term)-1);
		
		if ($this->getVerbose()) 
		{
			$href_dbpedia_category="<a href=\"{$this->dbpedia_base}/page/Category:".dirtydown_viki_tokens($term)."\" title='As DBPedia category' target=_blank>Category</a>";	
			$href_dbpedia_normal="<a href=\"{$this->dbpedia_base}/page/".dirtydown_viki_tokens($term)."\" title='As DBPedia resource' target=_blank>Resource</a>";	
			print "<br>find_dbpedia <b>$action</b> to <i><b>'$term'</b></i>: $href_dbpedia_category/$href_dbpedia_normal:<br>";
		}
	
		$RESULT=$this->get_zbw_skos_terms($term,$lang,$action);
		
		if ($this->getSrcDebug()) 
		{
			print "<br><br><b>find_zbwdbpedia_ {$action} terms of ($term)  with respect to $lang</b> returning :";
			foreach($RESULT as $te=>$Rank)
				print "<br>$te=>$Rank";
		}
			
		return $RESULT;	
	
	} // find_dbpedia_terms
	
	
	
	
	
	
	
	private function get_zbw_skos_terms($resourceTerm,$lang,$action)
	############################################################
	/*
	 * action = related|broader|narrower 
	 */
	{
		#################################
		#
		# Sammle die related terms aus den bereits
		# besorgten Objekten in $rowss
		#
		global $DBPEDIA_PREFIX;
		
		$ok=true;
		#################################
		
		if ($this->getSrcDebug()) print "<br>get_zbw_skos_terms($resourceTerm)...";
		
		$QUERY_ZBWDBPEDIA=<<<EOSQ
		SELECT ?zbw_descr
		{
			{
				?zbw_descr <http://www.w3.org/2004/02/skos/core#exactMatch> <http://dbpedia.org/resource/$resourceTerm> .
			}
			UNION
			{
				?zbw_descr <http://www.w3.org/2004/02/skos/core#closeMatch> <http://dbpedia.org/resource/$resourceTerm> .
			}
		}
EOSQ;
		if ($this->getSrcDebug()) 
		{
			print "<br><b>QUERY_ZBWDBPEDIA (language=$lang)</b>:<br>".show_xml_string($QUERY_ZBWDBPEDIA);
		}
		
		//Annahme: $zbw_descr ist gesetzt
		$rows = $this->get_zbwdbpedia_store()->query($QUERY_ZBWDBPEDIA, 'rows');
		if ($errs = $this->get_zbwdbpedia_store()->getErrors()) 
		{
			$ok=false;
			if ($this->getSrcDebug()) 
			{
				print "get_zbw_skos_terms():<br> Problems on Query: <br>".show_xml_string($QUERY_ZBWDBPEDIA)."<br>:<br>";
				foreach($errs as $err)
				fontprint ("<br>".$err.' ','red');
			}
		}
		
		if (count($rows))
		foreach($rows as $row) 	
			$zbw_descriptors[]= trim($row['zbw_descr']);
			
			
			
		if ($this->getVerbose())
		{	
			print "<br>Gefundene DESKRIPTOREN fuer <b>$resourceTerm</b>:";
			if (count($zbw_descriptors))
			foreach($zbw_descriptors as $zbw_descr)			
				print "<br>&nbsp;&nbsp;&nbsp;$zbw_descr";
			print "<br>";
		}	
		
		
		$skos_terms=$this->get_zbw_skos_terms_x($zbw_descriptors,$resourceTerm,$action,$lang);
			
		if ($this->getSrcDebug()) 
		{	print "<br><br><b>get_dbpedia_skos_terms($term) </b> returning :";
			foreach($skos_terms as $te=>$Rank)
				print "<br>$te";
		}
		
		return $skos_terms; // je action
	} //get_dbpedia_skos_terms
	





	private function get_zbw_skos_terms_x($zbw_descriptors,$resourceTerm,$skosproperty,$language='en')
	############################################################
	#
	# Liefert dbpedia SKOS broader terms (array)
	# Setzt voraus, dass in folgenden ARC stores folgende Files geladen sind:
	#
	# zbwdbpedia -> $PATH2U/data/SKOS/dbpedia_stw.rdf
	# zbw 		 -> "$PATH2U/data/SKOS/stw.rdf"
	#
	# Siehe http://zbw.eu/stw/versions/latest/about f�r Details
	#
	# Achtung: Die Dateien m�ssen als Western Windows Latin 1 gespeichert werden!
	# Bitte darauf achten -> Datei editieren und speichern-als mit richtiger Kodierung
	# Auch unter Windows
	#
	{		
		if ($this->getSrcDebug()) print "<br><b>get_dbpedia_skos_broader_terms($resourceTerm)...</b>";
	//		?desc <http://www.w3.org/2004/02/skos/core#exactMatch> <http://dbpedia.org/resource/$resourceTerm> .
		
		// ----- Suche nun nach Labels in ZBW SKOS Store ------
		if ($zbw_descriptors)
		foreach($zbw_descriptors as $zbw_descr)
		{
			
			if ($this->getSrcDebug())
				print "get_zbw_skos_{$skosproperty}_terms zu DESKRIPTOR:<b> $zbw_descr </b>:<br><br>";
		
		$QUERY_ZBW_PREFERRED=<<<EOSQ
		SELECT  ?label
		WHERE
		{
			<$zbw_descr> <http://www.w3.org/2004/02/skos/core#$skosproperty>  ?broaderdescr.
			?broaderdescr <http://www.w3.org/2004/02/skos/core#prefLabel> ?label.
		}
EOSQ;
		$QUERY_ZBW_ALTERNATIVE=<<<EOSQ
		SELECT  ?label
		WHERE
		{
			<$zbw_descr> <http://www.w3.org/2004/02/skos/core#$skosproperty>  ?broaderdescr.
			?broaderdescr <http://www.w3.org/2004/02/skos/core#altLabel> ?label.
		}
EOSQ;

			$broader_prefLabels = $this->exec_ARCstore($this->get_zbw_store(), $QUERY_ZBW_PREFERRED, $language, 'label', 'get_zbw_skos_broader_terms()');
			$broader_altlabels = $this->exec_ARCstore($this->get_zbw_store(), $QUERY_ZBW_ALTERNATIVE, $language, 'label', 'get_zbw_skos_broader_terms()');
			
		} // each descriptor
	
		// ADD RANKED:
		$results=array();
		if (count($broader_prefLabels))
		foreach($broader_prefLabels as $prefLabel) 	
		{
			if (strtolower(trim($resourceTerm))<>strtolower(trim($prefLabel)))
				$results{($prefLabel)}=100;
		}
		if (count($broader_altlabels))
		foreach($broader_altlabels as $altLabel) 	
		{
			if (strtolower(trim($resourceTerm))<>strtolower(trim($altLabel)))
				$results{cleanup_ZBW($altLabel)}=10;
		}
		
		
		if ($this->getVerbose())
		{
			if (count($results))
			foreach($results as $label=>$Rank) 	
			{
				
					print "<br> $skosproperty of ($resourceTerm) --> <b>$label</b>";
			}
		}
		
		return $results;
	} //get_zbw_skos_broader_terms
	
	
private function exec_ARCstore($store, $query, $language, $attribute, $warncontext)
	{
		// Liefer mono spalte array mit $attribute nach $language
		if ($this->getSrcDebug()) 
		{
			print "<br>QUERY: $query<br>";
		}
		
		$rows = $store->query($query, 'rows');
		if ($errs = $store->getErrors())
		{
			$ok=false;
			if ($this->getSrcDebug()) 
			{
				print "$warncontext:<br> Problems on Query: <br>$query<br>:<br>";
				foreach($errs as $err)
				fontprint ("<br>".$err.' ','red');
			}
		}
		if ($this->getSrcDebug()) 
		{
			print "<br><b>QUERY (language=$language)</b>:<br>".show_xml_string(($query));
			print "<hr>RESULTS<br>";
			var_dump($rows);
			print "<hr>";
		}
		
		//In case language is set, use it to restrict the search
		foreach($rows as $row) 	
		{	
			if ($language=='undefined' || $row['label lang'] == $language) 
				$arr[]=cleanup_ZBW(trim($row[$attribute]));
		}
		
		return $arr;
	}
		
	
	
	
} // class STWengine1



?>