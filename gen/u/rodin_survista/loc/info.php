<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <title>Info - LOC - SUrvista Rdf VISualizaTion Application</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <pre><?php
/*
 * Summary of some informations about LOC
 *
 * Copyright 2011 HTW Chur.
 */

include_once('config.inc.php');

// $store->config();

$uri = "http://id.loc.gov/authorities/sh85015272#concept"; // Boeing 707 (Jet transports)
//$uri = "http://id.loc.gov/authorities/sh85026423#concept"; // Civilization | Civilisation
//$uri = "http://id.loc.gov/authorities/sh99005029#concept"; // Civilization | --Culture
//$uri = "http://id.loc.gov/authorities#generalSubdivision"; // ~3500 narrower

/* query labels */

// $store->printUrisByValue($uri);

/* raw querying a single resource: */

// $store->node_info($uri);
// $store->node_info('http://en.wikipedia.org/wiki/Trogen');
// $store->node_info('http://dbpedia.org/resource/Trogen,_Switzerland');
// $store->node_info('http://dbpedia.org/ontology/principalArea');

/* query adjacencies */

// $store->printGraph($uri);

/* query allowed and denied nodes (costly!) */
// $store->node_stats();

/* query allowed and denied edges (costly!) */
// $store->edge_stats();

/* General information (applicable to any rdf data) */

// echo $store->config();
// $store->stats();

/* print some examples for all allowed edges */
// $store->edgeUsage();
// $store->anEdgeUsage("http://www.w3.org/2004/02/skos/core#inScheme");
$sparql = "
SELECT DISTINCT ?scheme WHERE { ?x <http://www.w3.org/2004/02/skos/core#inScheme> ?scheme }
";
//$store->sparqlResults($sparql, true);
// -> 18 schemes, i.e. http://id.loc.gov/authorities#meetings
// $store->node_info("http://id.loc.gov/authorities#meetings"); // Meetings Name Concept Scheme
// Meetings: http://id.loc.gov/authorities/sh93010136#concept // Super Bowl--Records
// $store->node_info("http://id.loc.gov/authorities/sh93010136#concept"); // Super Bowl--Records

/* calculate degree of each node (costly!) */
// $store->fans();

/* top concepts */

$sparql = "
SELECT ?s WHERE {
{
    ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2004/02/skos/core#Concept> .
    OPTIONAL { ?s <http://www.w3.org/2004/02/skos/core#broader> ?b }
}
FILTER (!BOUND(?b))
}
LIMIT 100000
OFFSET 200000";
// -> count: 11506 => 211506 concepts without broader, i.e. http://id.loc.gov/authorities/sh85015280#concept
// $store->node_info("http://id.loc.gov/authorities/sh85015280#concept"); // Boel family

$sparql = "
SELECT ?s WHERE {
{
    ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2004/02/skos/core#Concept> .
    ?s <http://www.w3.org/2004/02/skos/core#narrower> ?n . 
    OPTIONAL { ?s <http://www.w3.org/2004/02/skos/core#broader> ?b }
}
FILTER (!BOUND(?b))
}
LIMIT 200000
OFFSET 0";
// -> count: 106183 concepts with narrower but no broader, i.e. http://id.loc.gov/authorities/sh85114415#concept
// $store->node_info("http://id.loc.gov/authorities/sh85114415#concept"); // Rivers--Utah

$sparql = "
SELECT ?s WHERE {
{
    ?s <http://www.w3.org/1999/02/22-rdf-syntax-ns#type> <http://www.w3.org/2004/02/skos/core#Concept> .
    OPTIONAL { ?s <http://www.w3.org/2004/02/skos/core#narrower> ?n }
}
FILTER (!BOUND(?n))
}
LIMIT 100000
OFFSET 300000";
// -> count: 46719 => 346719 concepts with no narrower, i.e. http://id.loc.gov/authorities/sh85015272#concept
//$store->node_info("http://id.loc.gov/authorities/sh85015272#concept"); // Boeing 707 (Jet transports)
//$results = $store->sparql($sparql);
//echo count($results);
//echo htmlspecialchars(var_export($results[0], true));
//echo htmlspecialchars(var_export($results, true));

/* concepts: */

// - http://id.loc.gov/authorities#*
// - http://id.loc.gov/authorities/*
$sparql = '
SELECT ?s ?o WHERE {
    {
    ?s <http://www.w3.org/2000/01/rdf-schema#label> ?o . 
    } UNION {
    ?s <http://www.w3.org/2004/02/skos/core#prefLabel> ?o . 
    } UNION {
    ?s <http://www.w3.org/2004/02/skos/core#altLabel> ?o . 
    }
    FILTER (lang(?o) = "en")
    FILTER regex(?o, "^--", "i" ) # --*
#    FILTER regex(?o, "--", "i" ) # *--*
#    FILTER regex(?o, "^Plains--", "i" ) # --*
}
LIMIT 100000
OFFSET 0
';
//$store->sparqlResults($sparql, true);

// --* -> count: 1132, i.e. http://id.loc.gov/authorities/sh2003007741#concept
//$store->node_info("http://id.loc.gov/authorities/sh2003007741#concept"); // --Abandonment of nests. Use as a topical subdivision under individual animals and groups of animals.
// *--* -> count: 183572, i.e. http://id.loc.gov/authorities/sh85102605#concept
//$store->node_info("http://id.loc.gov/authorities/sh85102605#concept"); // Plains--Australia. In schema http://id.loc.gov/authorities#topicalTerms
?></pre>
    </body>
</html>