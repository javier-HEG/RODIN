<?php

/*
 * LOC data store configuration for Rodin RDF visualization
 *
 * Copyright 2011 HTW Chur.
 */

ini_set('max_execution_time', 600);

// main configuration file
include_once('../survista/config.inc.php');

// classes
include_once(SURVISTA_PATH . 'RdfGraph.php');

#######################################
#
# RODIN
#
#$config['store_name'] = 'zbw';
#######################################
// ARC config
$config = array();
$config['max_errors'] = 100;
$config['db_name'] = 'rodin_loc';
$config['db_user'] = 'rodin';
$config['db_pwd'] = '9XMMhxvnDupwwKUc';
$config['store_name'] = 'arc_rodin_loc';

$store = new SurvistaRdfGraph();

/* Labelling */

$store->lang = "en"; // only english
// set which relations point to labels:
$store->setPreferredLabelProperty('http://www.w3.org/2004/02/skos/core#prefLabel');
$store->addAlternativeLabelPropterty('http://www.w3.org/2004/02/skos/core#prefLabel');
$store->addAlternativeLabelPropterty('http://www.w3.org/2004/02/skos/core#altLabel');

// some additional labels
$store->manual_labels['http://www.w3.org/2004/02/skos/core#broader'] = 'broader';
$store->manual_labels['http://www.w3.org/2004/02/skos/core#narrower'] = 'narrower';
$store->manual_labels['http://www.w3.org/2004/02/skos/core#related'] = 'related';

/* Node filter */

// whitelist
$store->node_filter_rules = array(
    'http://id.loc.gov/authorities' => true, // TODO: use http://id.loc.gov/authorities/ to not have http://id.loc.gov/authorities#?
);
$store->node_filter_default = false;

/* Edge filter */

// whitelist
$store->edge_filter_rules = array(
    'http://www.w3.org/2004/02/skos/core#broader' => true,
    'http://www.w3.org/2004/02/skos/core#narrower' => true,
    'http://www.w3.org/2004/02/skos/core#related' => true,
//  'http://www.w3.org/2004/02/skos/core#inScheme' => true, // too many hits possible
);
$store->edge_filter_default = false;

$store->hierarchyProperties[] = 'http://www.w3.org/2004/02/skos/core#narrower';
$store->hierarchyProperties[] = 'http://www.w3.org/2004/02/skos/core#broader';
$store->hierarchyPropertiesDirections['http://www.w3.org/2004/02/skos/core#narrower'] = 1; // outgoing
$store->hierarchyPropertiesDirections['http://www.w3.org/2004/02/skos/core#broader'] = -1; // incoming

$store->connectArcStore($config);
?>