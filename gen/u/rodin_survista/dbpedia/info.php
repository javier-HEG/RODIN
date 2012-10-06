<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <title>Info - DBpedia - SUrvista Rdf VISualizaTion Application</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <pre><?php
/*
 * Summary of some informations about DBpedia
 *
 * Copyright 2011 HTW Chur.
 */

include_once('config.inc.php');

$uri = "http://dbpedia.org/resource/Trogen";

// raw querying a single resource:
// $store->node_info($uri);
// $store->node_info('http://en.wikipedia.org/wiki/Trogen');
// $store->node_info('http://dbpedia.org/resource/Trogen,_Switzerland');
// $store->node_info('http://dbpedia.org/ontology/principalArea');


// query labels
// $store->printUrisByValue($uri);

// query adjacencies
// $store->printGraph($uri);

// query allowed and denied nodes (costly!)
// $store->node_stats();
// exit;

// query allowed and denied edges (costly!)
// $store->edge_stats();


/* General information (applicable to any rdf data) */

// echo $store->config();

// $store->stats();

// print some examples for all allowed edges
// $store->edgeUsage();

// calculate degree of each node (costly!)
$store->fans();

?></pre>
    </body>
</html>