<?php

include_once '../sroot.php';
include_once '../../../gen/u/arc/ARC2.php';
include_once '../../../../gen/u/simplehtmldom/simple_html_dom.php';
include_once '../u/FRIutilities.php';
include_once '../u/FRIdbUtilities.php';

global $ARCCONFIG;
global $DBPEDIA_SPARQL_ENDPOINT;
global $DBPEDIA_PREFIX;

$dbPediaARCConfig=$ARCCONFIG;
$dbPediaARCConfig['remote_store_endpoint']=$DBPEDIA_SPARQL_ENDPOINT;

$store = ARC2::getRemoteStore($dbPediaARCConfig);

$query = <<<EOF
PREFIX : <http://dbpedia.org/resource/>
PREFIX owl: <http://www.w3.org/2002/07/owl#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX dcterms: <http://purl.org/dc/terms/>

SELECT * WHERE {
	:Leipzig rdfs:label ?label .
	OPTIONAL { ?meta owl:annotatedSource :Leipzig . } .
}
EOF;

//	?meta owl:annotatedSource :Parsauni .	
//	OPTIONAL { ?meta dcterms:modified ?date . } .

$query = <<<EOF
PREFIX dc: <http://purl.org/dc/terms/>
SELECT * WHERE {
	?uri dc:modified ?mtime .
	FILTER ( ?mtime >= "2010-06-10T20:23:34Z"^^xsd:dateTime )
}
ORDER BY ASC(?mtime) LIMIT 5000
EOF;


print "Query is : $query <br />";

$rows = $store->query($query, 'rows');

if ($errors = $store->getErrors()) {
	foreach($errors as $error) {
		print "SPARQL Query ERROR : $error <br />";
	}
}

$rowsCount = count($rows);

print "Obtained $rowsCount rows! <br />";

foreach($rows as $row) {
	$uri = $row['label'];
	$modif = $row['meta'];
	
	print "R::: $uri // $modif <br />";
}

?>