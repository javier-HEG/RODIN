<?php
#############################################
#
# HEG Geneve
# Fabio Ricci Tel. +41(76)5281961
#
# Main Vars File
# 18.1.2010
#


	// Import root.php
	// print "<br>SEARCHING app/root.php from ".getcwd();
	$filename="app/root.php";
	#######################################
	$max=10;
	for ($x=1,$updir='';$x<=$max;$x++,$updir.="../") {
//		print "<br>[sroot.php] - - Looking for $updir$filename ... ($x) from ".getcwd();
		if (file_exists("$updir$filename")) {
//			print "<br>[sroot.php] - - Root file $updir$filename exists.";
//			print "<br>[sroot.php] Trying to include root.php";
			include_once("$updir$filename"); $rootincluded=true;
//      print "<br>root.php: RODINSEGMENT: $RODINSEGMENT";
//      print "<br>root.php: RODIN: $RODIN";
//			print "<br>[sroot.php] root.php was included.";
			break;
		}
	}
  if (!$rootincluded) print "<br>sroot.php: could not load root.php !!!";
	
	$DBPEDIA_PREFIX="PREFIX owl: <http://www.w3.org/2002/07/owl#>
		PREFIX xsd: <http://www.w3.org/2001/XMLSchema#>
		PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
		PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
		PREFIX foaf: <http://xmlns.com/foaf/0.1/>
		PREFIX dc: <http://purl.org/dc/elements/1.1/>
		PREFIX : <http://dbpedia.org/resource/>
		PREFIX dbpedia2: <http://dbpedia.org/property/>
		PREFIX dbpedia: <http://dbpedia.org/>
		PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
	";
	
	$DBPEDIA_BASE="http://dbpedia.org";
	$WIKIPEDIABASE="http://en.wikipedia.org";
	$DBPEDIA_SPARQL_ENDPOINT="$DBPEDIA_BASE/sparql";
	$WIKIPEDIABASEURL="http://en.wikipedia.org/wiki";
	$WIKIPEDIASEARCH="$WIKIPEDIABASE/w/api.php?action=opensearch&format=xml";
	$WIKIPEDIASEARCH2="$WIKIPEDIABASE/w/index.php?";

	
	$HOST = $_SERVER["SERVER_NAME"];
	//$DEFAULT_MAX_REFINE_RESULTS=10; -> IS IN root.php
	$MAX_DISAMBIGUATED_TERMS=5;
	$TERM_SEPARATOR=',';
		
	$MAX_SRC_EXEC_TIME_LIMIT = 20; //SEC
	
	$APPPATH="$RODINROOT/$RODINSEGMENT/app";
	$UPATH1="$RODINROOT/$RODINSEGMENT/app/u";
	$UPATH="$RODINROOT/$RODINSEGMENT/fsrc/app/u";
	$PATH2U="../../../../fsrc/gen/u";
	$PATH2RU="../../../../../gen/u"; //Utilities from RODIN app
	$PATH2app="../../../app"; //Utilities from RODIN app

	
	#ARC DB & Default configuration
	$ARCDB_DBNAME = getA('ARCDB_DBNAME');
	$ARCDB_USERNAME = getA('ARCDB_USERNAME');
	$ARCDB_USERPASS = getA('ARCDB_USERPASS');
	$ARCDB_DBHOST = getA('ARCDB_DBHOST');
	$GRAPHVIZ_PATH= getA('GRAPHVIZ_PATH');
	$GRAPHVIZ_TMP_PATH= getA('GRAPHVIZ_TMP_PATH');
					
	$ARCCONFIG = array(
		/* db */
		'db_name' => $ARCDB_DBNAME,
		'db_user' => $ARCDB_USERNAME,
		'db_pwd'  => $ARCDB_USERPASS,
	
		/* store */
		'store_name' => '', // must be set
		/* stop after 100 errors */
		'max_errors' => 100,
		
		/* path to dot ?*/
	  'graphviz_path' => $GRAPHVIZ_PATH,
	  /* tmp dir (default: '/tmp/') */
	  'graphviz_temp' => $GRAPHVIZ_TMP_PATH,
	  /* pre-defined namespace prefixes (optional) */
	  'ns' => array(
	  					'foaf' => 'http://xmlns.com/foaf/0.1/'
						),
						
		'arcUtilities' => "$DOCROOT$UPATH1/arcUtilities.php",
	);
	
	#SRC DB
	$SRCDB_DBNAME				=getA('SRCDB_DBNAME');
	$SRCDB_USERNAME			=getA('SRCDB_USERNAME');
	$SRCDB_USERPASS			=getA('SRCDB_USERPASS');
	$SRCDB_DBHOST				=getA('SRCDB_DBHOST');
	
	#DERIPIPES Host
	$DERIPIPE_ENGINE		=getA('DERIPIPE_ENGINE');
	$DERIPIPE_RELATED		=getA('DERIPIPE_RELATED');
	$DERIPIPE_BROADER		=getA('DERIPIPE_BROADER');
	$DERIPIPE_NARROWER	=getA('DERIPIPE_NARROWER');


	

	$DOCROOT=$_SERVER['DOCUMENT_ROOT'];
	$BASESERVERROOT=str_replace("/htdocs","",$DOCROOT);

	$CURL_COOCKIEDIR="$DOCROOT$RODINROOT/$RODINSEGMENT/fsrc/gen/u/tmp";

	$SKOS_DBPEDIA_FILE="$PATH2U/data/SKOS/dbpedia_stw.rdf";
	$SKOS_ZBW_FILE="$PATH2U/data/SKOS/stw.rdf";
  
	
	//These namespaces will be transferred into the DB ARC
	//and will be made available to each SRC engine
	
  //REDUNDANCY with same setting inside SRC engines
  $zbw_stw_namespaces=array(
                  
               'stw'=> 'http://zbw.eu/stw/' ,
                'cc'=> 'http://creativecommons.org/ns#' ,
                'dc'=> 'http://purl.org/dc/terms/' ,
               'gbv'=> 'http://purl.org/ontology/gbv/' ,
               'owl'=> 'http://www.w3.org/2002/07/owl#' ,
               'rdf'=> 'http://www.w3.org/1999/02/22-rdf-syntax-ns#' ,
              'rdfs'=> 'http://www.w3.org/2000/01/rdf-schema#' ,
              'skos'=> 'http://www.w3.org/2004/02/skos/core#' ,
               'xsd'=> 'http://www.w3.org/2001/XMLSchema#' ,
            'zbwext'=> 'http://zbw.eu/namespaces/zbw-extensions/' 
          
);
  
  $gesis_thesoz_namespaces=array(
         'thesozext'=> 'http://lod.gesis.org/thesoz/ext/' ,
            'thesoz'=> 'http://lod.gesis.org/thesoz/' ,
                'cc'=> 'http://creativecommons.org/ns#' ,
                'dc'=> 'http://purl.org/dc/terms/' ,
              'skos'=> 'http://www.w3.org/2004/02/skos/core#' ,
            'skosxl'=> 'http://www.w3.org/2008/05/skos-xl#' ,
               'rdf'=> 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
              'rdfs'=> 'http://www.w3.org/2000/01/rdf-schema#' ,
               'owl'=> 'http://www.w3.org/2002/07/owl#' ,
               'prv'=> 'http://purl.org/net/provenance/ns#' ,
              'void'=> 'http://rdfs.org/ns/void#' ,
               'xsd'=> 'http://www.w3.org/2001/XMLSchema#' 
);

  $bnf_rameau_namespaces=array(
              'bnfc'=> 'http://catalogue.bnf.fr/',
               'bnf'=> 'http://data.bnf.fr/' ,
     'rameau_stitch'=> 'http://stitch.cs.vu.nl/vocabularies/rameau/ark:/' ,
                'cc'=> 'http://creativecommons.org/ns#' ,
                'dc'=> 'http://purl.org/dc/terms/' ,
              'foaf'=> 'http://xmlns.com/foaf/spec/' ,
               'gbv'=> 'http://purl.org/ontology/gbv/' ,
               'owl'=> 'http://www.w3.org/2002/07/owl#' ,
              'owlf'=> 'http://www.w3.org/TR/owl-features#' ,
               'rdf'=> 'http://www.w3.org/1999/02/22-rdf-syntax-ns#' ,
               'ore'=> 'http://www.openarchives.org/ore/1.0/rdfxml/' ,
              'rdfs'=> 'http://www.w3.org/2000/01/rdf-schema#' ,
              'skos'=> 'http://www.w3.org/2004/02/skos/core#' ,
               'xsd'=> 'http://www.w3.org/2001/XMLSchema#' ,
            'zbwext'=> 'http://zbw.eu/namespaces/zbw-extensions/' ,
'RDAgroup2elements' => 'http://rdvocab.info/uri/schema/FRBRentitiesRDA/' , 
 'RDArelationships' => 'http://rdvocab.info/RDARelationshipsWEMI/' ,
          'RDVocab' => 'http://RDVocab.info/Elements/',
           'deweyi' => 'http://dewey.info/',
              'gnd' => 'http://d-nb.info/gnd/'
);
  
  
  $loc_sh_namespaces=array(
      
           'locas' => 'http://id.loc.gov/authorities/subjects/',
            'loca' => 'http://id.loc.gov/authorities/',
              'rdf'=> 'http://www.w3.org/1999/02/22-rdf-syntax-ns#' ,
          'madsrdf'=> 'http://www.loc.gov/mads/rdf/v1#' ,
               'ri'=> 'http://id.loc.gov/ontologies/RecordInfo#' ,
              'owl'=> 'http://www.w3.org/2002/07/owl#' ,
             'skos'=> 'http://www.w3.org/2004/02/skos/core#' ,
           'skosxl'=> 'http://www.w3.org/2008/05/skos-xl#' ,
               'cs'=> 'http://purl.org/vocab/changeset/schema#' ,
              'owl'=> 'http://www.w3.org/2002/07/owl#' ,
             'rdfs'=> 'http://www.w3.org/2000/01/rdf-schema#' 
);
?>