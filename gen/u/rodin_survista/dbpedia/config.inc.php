<?php

/*
 * DBPedia data store configuration for Rodin RDF visualization
 *
 * Copyright 2011 HTW Chur.
 */

ini_set('max_execution_time', 600);

// main configuration file
include_once('../survista/config.inc.php');

//print "<br>[stw/config.inc] Included survista/config.inc.php";

// classes
include_once(SURVISTA_PATH . 'RdfGraph.php');



$store = new SurvistaRdfGraph();

$store->lang = $lang;

// set which relations point to labels: only rdfs:label
$store->setPreferredLabelProperty('http://www.w3.org/2004/02/skos/core#prefLabel');
$store->addAlternativeLabelPropterty('http://www.w3.org/2000/01/rdf-schema#label');
//$store->reverseLabelProperties();

/* Node filter */

// whitelist
$store->node_filter_rules = array(
    'http://dbpedia.org/resource/Category:' => true
    //'http://dbpedia.org/resource/' => true
);
$store->node_filter_default = false;

/* Edge filter */

// whitelist
$store->edge_filter_rules = array(
    'http://purl.org/dc/terms/subject' => true,
    'http://dbpedia.org/ontology/wikiPageRedirects' => true,
	'http://www.w3.org/2004/02/skos/core#broader' => true,
    'http://www.w3.org/2004/02/skos/core#narrower' => true,
    'http://www.w3.org/2004/02/skos/core#related' => true,
    'http://purl.org/dc/terms/isReplacedBy' => true
);
$store->edge_filter_default = false;

// some additional labels
$store->manual_labels['http://purl.org/dc/terms/subject'] = 'is subject of';
$store->manual_labels['http://dbpedia.org/ontology/wikiPageRedirects'] = 'redirects from';
$store->manual_labels['http://www.w3.org/2004/02/skos/core#broader'] = lg("lblSkosBroaderProperty");
$store->manual_labels['http://www.w3.org/2004/02/skos/core#narrower'] = lg("lblSkosNarrowerProperty");
$store->manual_labels['http://www.w3.org/2004/02/skos/core#related'] = lg("lblSkosRelatedProperty");
$store->manual_labels['http://purl.org/dc/terms/isReplacedBy'] = 'is replaced by';

// edges directions
$store->hierarchyProperties[] = 'http://purl.org/dc/terms/subject';
$store->hierarchyProperties[] = 'http://dbpedia.org/ontology/wikiPageRedirects';
$store->hierarchyPropertiesDirections['http://purl.org/dc/terms/subject'] = 1; // outgoing
$store->hierarchyPropertiesDirections['http://dbpedia.org/ontology/wikiPageRedirects'] = -1; // incoming

$store->hierarchyProperties[] = 'http://www.w3.org/2004/02/skos/core#narrower';
$store->hierarchyProperties[] = 'http://www.w3.org/2004/02/skos/core#broader';
$store->hierarchyPropertiesDirections['http://www.w3.org/2004/02/skos/core#narrower'] = 1; // outgoing
$store->hierarchyPropertiesDirections['http://www.w3.org/2004/02/skos/core#broader'] = -1; // incoming

#######################################
#
# RODIN
#
if ($USE_LOCAL_DBPEDIA) {
	$config['db_name'] = $LOCAL_DBPEDIA_DB_NAME;
	$config['store_name'] = $LOCAL_DBPEDIA_ARC_NAME;
	
	$store->connectArcStore($config);
	$store->checkSetup();
} else {
	global $PROXY_NAME, $PROXY_PORT;
	if ($PROXY_NAME != '') {
		$config['proxy_host'] = $PROXY_NAME;
		$config['proxy_port'] = $PROXY_PORT;
	}
	
	$config['store_name'] = '';
	$config['remote_store_endpoint'] = $DBPEDIA_SPARQL_ENDPOINT;
	
	$store->connectRemoteArcStore($config);
}
#######################################



?>