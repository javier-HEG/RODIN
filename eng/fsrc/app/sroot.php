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
			include_once("$updir$filename");
//			print "<br>[sroot.php] root.php was included.";
			break;
		}
	}
	
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

	
	$HOST = $_ENV["HTTP_HOST"];
	$DEFAULT_MAX_REFINE_RESULTS=10;
	$MAX_DISAMBIGUATED_TERMS=5;
	$TERM_SEPARATOR=',';
		
	$MAX_SRC_EXEC_TIME_LIMIT = 20; //SEC
	
	$UPATH="$RODINROOT/$RODINSEGMENT/fsrc/app/u";
	$PATH2U="../../../../fsrc/gen/u";
	$PATH2RU="../../../../../gen/u"; //Utilities from RODIN app
	$PATH2app="../../../app"; //Utilities from RODIN app

	
	#ARC DB & Default configuration
	$ARCDB_DBNAME = getA('ARCDB_DBNAME');
	$ARCDB_USERNAME = getA('ARCDB_USERNAME');
	$ARCDB_USERPASS = getA('ARCDB_USERPASS');
	$SRCDB_DBHOST = getA('ARCDB_DBHOST');
					
	$ARCCONFIG = array(
		/* db */
		'db_name' => $ARCDB_DBNAME,
		'db_user' => $ARCDB_USERNAME,
		'db_pwd'  => $ARCDB_USERPASS,
	
		/* store */
		'store_name' => '', // must be set
	
		/* stop after 100 errors */
		'max_errors' => 100,
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
?>