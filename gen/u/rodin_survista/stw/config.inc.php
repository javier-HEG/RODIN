<?php

/*
 * STW data store configuration for Rodin RDF visualization
 *
 * Copyright 2011 HTW Chur.
 */

ini_set('max_execution_time', 600);

// main configuration file
include_once('../survista/config.inc.php');

//print "<br>[stw/config.inc] Included survista/config.inc.php";

// classes
include_once(SURVISTA_PATH . 'RdfGraph.php');
include_once('LabelCleaner.php');

//print "<br>[stw/config.inc] Included RdfGraph.php & LabelCleaner.php";

#######################################
#
# RODIN
#
$config['store_name'] = 'zbw';
#######################################

$store = new SurvistaRdfGraph();

$store->lang = $lang;
// [Javier] I set http://www.w3.org/2004/02/skos/core#prefLabel to be the only label_property on RdfGraph.php
// [Javier] TODO This of course needs to be fixed, but my guess is that once we put ZBW and DBPEDIA_ZBW in the
//      same ARC STORE, things should run smoothly.

// set which relations point to labels
$store->setPreferredLabelProperty('http://www.w3.org/2004/02/skos/core#prefLabel');
$store->addAlternativeLabelPropterty('http://www.w3.org/2004/02/skos/core#altLabel');
$store->addAlternativeLabelPropterty('http://www.w3.org/2004/02/skos/core#prefLabel');
$store->reverseLabelProperties();

/* Node filter */

// whitelist
$store->node_filter_rules = array(
    'http://zbw.eu/stw/thsys/' => true,
    'http://zbw.eu/stw/descriptor/' => true,
//    'http://dbpedia.org/resource/' => true,
);
$store->node_filter_default = false;

/* Edge filter */

// whitelist
$store->edge_filter_rules = array(
    'http://www.w3.org/2004/02/skos/core#broader' => true,
    'http://www.w3.org/2004/02/skos/core#narrower' => true,
    'http://www.w3.org/2004/02/skos/core#related' => true,
    'http://purl.org/dc/terms/isReplacedBy' => true,
//    'http://www.w3.org/2004/02/skos/core#exactMatch' => true,
//    'http://www.w3.org/2004/02/skos/core#closeMatch' => true,
);
$store->edge_filter_default = false;

// STW specific post processing of labels
$store->labelCleaner = new LabelCleaner();

// some additional labels
$store->manual_labels['http://www.w3.org/2004/02/skos/core#broader'] = lg("lblSkosBroaderProperty");
$store->manual_labels['http://www.w3.org/2004/02/skos/core#narrower'] = lg("lblSkosNarrowerProperty");
$store->manual_labels['http://www.w3.org/2004/02/skos/core#related'] = lg("lblSkosRelatedProperty");
$store->manual_labels['http://purl.org/dc/terms/isReplacedBy'] = 'is replaced by';

$store->hierarchyProperties[] = 'http://www.w3.org/2004/02/skos/core#narrower';
$store->hierarchyProperties[] = 'http://www.w3.org/2004/02/skos/core#broader';
$store->hierarchyPropertiesDirections['http://www.w3.org/2004/02/skos/core#narrower'] = 1; // outgoing
$store->hierarchyPropertiesDirections['http://www.w3.org/2004/02/skos/core#broader'] = -1; // incoming

$store->connectArcStore($config);

$store->checkSetup();

?>