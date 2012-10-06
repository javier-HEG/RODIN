<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <title>Info - STW - SUrvista Rdf VISualizaTion Application</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    </head>
    <body>
        <pre><?php
/*
 * Summary of some informations about STW
 *
 * Copyright 2011 HTW Chur.
 */

include_once('config.inc.php');

$store->node_info("http://dbpedia.org/resource/Tax");


/* General information (applicable to any rdf data) */

// echo $store->config();
// costly function
if (false) {
    $store->stats();
} else {
    echo <<<TEXT

STATS:
number of triples: 110824
number of nodes: 6524
number of ignored nodes: 3167
number of edges: 4
number of ignored edges: 46

TEXT;
}

// print some examples for all allowed edges
// $store->edgeUsage();

/* Special Information (STW specific) */

// resources (subjects or objects) matching:
// - http://zbw.eu/stw/thsys/
// - http://zbw.eu/stw/descriptor/
// - http://dbpedia.org/resource/
if (false) {

    $needle = 'http://zbw.eu/stw/thsys/';
    $needle = 'http://zbw.eu/stw/descriptor/';
//    $needle = 'http://dbpedia.org/resource/';

    $res = array();
    //$sparql = "
    //SELECT DISTINCT ?o WHERE {
    //    {
    //    ?s ?p ?o .
    //    } UNION {
    //    ?o ?p ?s .
    //    }
    //    FILTER (isURI(?o))
    //    FILTER regex(?o, \"" . $needle . "\" )
    //}
    //";
    // subjects
    $sparql = "
      SELECT DISTINCT ?x WHERE {
      {
      ?x ?p ?o .
      }
      FILTER (isURI(?x))
      FILTER regex(?x, \"" . $needle . "\" )
      }
      ";
    $results = $store->sparql($sparql);
    foreach ($results as $row) {
        if (!in_array($row['x'], $res)) {
            $res[] = $row['x'];
        }
    }
    // objects
    $sparql = "
      SELECT DISTINCT ?x WHERE {
      {
      ?s ?p ?x .
      }
      FILTER (isURI(?x))
      FILTER regex(?x, \"" . $needle . "\" )
      }
      ";
    $results = $store->sparql($sparql);
    foreach ($results as $row) {
        if (!in_array($row['x'], $res)) {
            $res[] = $row['x'];
        }
    }

    $max = 0;
    foreach($res as $resource) {
    //    echo $store->label_or_uri($resource) . " (" . $resource . ")" . PHP_EOL;
    ////    echo $store->printGraph($row['o']);
        $labels = $store->labels($resource);
        echo $resource . "\t" . count($labels) . "\t" . implode(", ", $labels) . PHP_EOL;
        $max = max($max, count($labels));
    }
//    print_r($res);
    echo "number of nodes: " . count($res) . PHP_EOL;
    echo "max labels: " . $max . PHP_EOL;
} else {
    echo <<<TEXT
number of resources matching http://zbw.eu/stw/thsys/:       532
number of resources matching http://zbw.eu/stw/descriptor/: 5992

TEXT;
}


// resources having dbpedia matches (close, exact):
if (false) {
    $sparql = "
SELECT ?s ?o WHERE {
    ?s <http://www.w3.org/2004/02/skos/core#exactMatch> ?o
#    ?s <http://www.w3.org/2004/02/skos/core#closeMatch> ?o
}
";

    $results = $store->sparql($sparql);
    echo count($results) . PHP_EOL;
} else {

    echo <<<TEXT
number of triples with predicate core#exactMatch: 1359
number of triples with predicate core#closeMatch: 1967

TEXT;
}

/*
 * Disambiguation/Duplicates in STW
 * i.e.:
 * - Religion
 *  - Religion | Religion	(http://zbw.eu/stw/thsys/73388)
 *  - Religion | Mythologie | Mystik	(http://zbw.eu/stw/descriptor/15671-6)
 * - Militär
 *  - Militär | Militär	(http://zbw.eu/stw/thsys/70865)
 *  - Militär | Militär	(http://zbw.eu/stw/thsys/73363)
 *  - Militär | Wehrmacht | Verteidigungsstreitkräfte | Streitkräfte | Landesverteidigung | Heer | Bundeswehr | Armee	(http://zbw.eu/stw/descriptor/13412-5)
 * etc.
 *
 */

if (false) {

    function getLabels($labelRel, $lang) {
        global $store;
        $res = array();

        $sparql = "
SELECT * WHERE {
    {
    ?s <" . $labelRel . "> ?o .
    OPTIONAL {
    ?s <http://www.w3.org/2004/02/skos/core#inScheme> ?o2 .
    }
    }
    FILTER (lang(?o) = \"" . $lang . "\")
    FILTER (bound(?o2))
}
";
        $results = $store->sparql($sparql, true);
        foreach ($results as $row) {
            $label = mb_strtolower($row['o'], 'UTF8');
            $label = LabelCleaner::cleanup($label);
            $res[] = $label;
        }

        echo "labels for " . $labelRel . " in " . $lang . ":" . PHP_EOL;
        echo count($res) . PHP_EOL;
//print_r($res);
        return $res;
    }

    function more_than_once($array) {
        $retval = array();
        $counts = array_count_values($array);
        $max = 0;
        foreach ($counts as $key => $value) {
            $max = max($max, $value);
            if ($value > 1) {
//        echo $key . " found " . $value . " times" . PHP_EOL;
                $retval[] = $key;
            }
        }
//    echo "max: " . $max . PHP_EOL;
        return $retval;
    }

    $lang = "en";

    $prefLabels = getLabels("http://www.w3.org/2004/02/skos/core#prefLabel", $lang);
    $altLabels = getLabels("http://www.w3.org/2004/02/skos/core#altLabel", $lang);
//$prefLabels = array();
//$altLabels = array();

    $allLabels = array_merge($prefLabels, $altLabels);
////print_r($allLabels);
    $uniqueLabels = array_unique($allLabels);
////print_r($uniqueLabels);
    $duplicateLabels = more_than_once($allLabels);

    echo "all labels: " . count($allLabels) . PHP_EOL;
    echo "unique labels: " . count($uniqueLabels) . PHP_EOL;
    echo "duplicate labels: " . count($duplicateLabels) . PHP_EOL;

    foreach ($duplicateLabels as $l) {
        echo $l . PHP_EOL;
    }
} else {

    echo <<<TEXT

duplicates in en:
labels for http://www.w3.org/2004/02/skos/core#prefLabel: 6520
labels for http://www.w3.org/2004/02/skos/core#altLabel: 3037
all labels: 9558
unique labels: 9231
duplicate labels: 317

duplicates in de:
labels for http://www.w3.org/2004/02/skos/core#prefLabel: 6520
labels for http://www.w3.org/2004/02/skos/core#altLabel: 15028
all labels: 21549
unique labels: 21258
duplicate labels: 283

TEXT;
}



/* nodes without labels */

if (false) {

    $sparql = "
SELECT DISTINCT ?s ?o2 ?o3 WHERE {
{
?s <http://www.w3.org/2004/02/skos/core#inScheme> ?o .
OPTIONAL { ?s <http://www.w3.org/2004/02/skos/core#prefLabel> ?o2 . }
OPTIONAL { ?s <http://www.w3.org/2004/02/skos/core#altMatch> ?o3 . }
}
# exact
# FILTER (bound(?o2))
# close
# FILTER (bound(?o3))
# neither
FILTER (!bound(?o2) && !bound(?o3))
## only exact: same as exact
## FILTER (bound(?o2) && !bound(?o3))
## only close: same as close
## FILTER (!bound(?o2) && bound(?o3))
## both: 0
## FILTER (bound(?o2) && bound(?o3))
FILTER (isUri(?s))
}
";

    $result = $store->sparql($sparql, true);
    echo count($result) . PHP_EOL;

    foreach ($result as $res) {
//        $store->uri_and_labels($res['s']);
        $store->node_info($res['s']);
    }
} else {
    echo <<<TEXT

nodes without alt/pref label: 4

TEXT;
}





/* Concepts and dbpedia matches */

if (false) {

    $sparql = "
SELECT DISTINCT ?s ?o2 ?o3 WHERE {
{
?s <http://www.w3.org/2004/02/skos/core#inScheme> ?o .
OPTIONAL { ?s <http://www.w3.org/2004/02/skos/core#exactMatch> ?o2 . }
OPTIONAL { ?s <http://www.w3.org/2004/02/skos/core#closeMatch> ?o3 . }
}
# exact
# FILTER (bound(?o2))
# close
# FILTER (bound(?o3))
# neither
FILTER (!bound(?o2) && !bound(?o3))
## only exact: same as exact
## FILTER (bound(?o2) && !bound(?o3))
## only close: same as close
## FILTER (!bound(?o2) && bound(?o3))
## both: 0
## FILTER (bound(?o2) && bound(?o3))
FILTER (isUri(?s))
}
";

    $result = $store->sparql($sparql);
    echo count($result) . PHP_EOL;

    foreach ($result as $res) {
        $store->uri_and_labels($res['s']);
    }

// Audit (not having dbpedia matches)
// http://zbw.eu/stw/descriptor/12882-1
// "P.20.04  Fish and Fish Products"
// rdf:label: Fish and Fish Products
// http://zbw.eu/stw/thsys/73301
//$sparql = "
//SELECT * WHERE {
//    <http://zbw.eu/stw/thsys/73301> ?p ?o .
//}
//";
//$result = $store->sparql($sparql, true);
} else {
    echo <<<TEXT

resources and dbpedia matches
- resources inSchema: 6524
- resources with exactMatch: 1359
- resources with closeMatch: 1967
- resources without match: 3198

TEXT;
}


/*
 * dbpedia resources having matches:
 * - [http://dbpedia.org/resource/Tax] => 4
 * - [http://dbpedia.org/resource/Statistics] => 4
 * ...
 */


if (false) {

    $sparql = "
SELECT DISTINCT ?o ?s2 ?s3 WHERE {
{
?s ?p ?o .
OPTIONAL { ?s2 <http://www.w3.org/2004/02/skos/core#exactMatch> ?o . }
OPTIONAL { ?s3 <http://www.w3.org/2004/02/skos/core#closeMatch> ?o . }
}

# exact
# FILTER (bound(?o2))
# close
# FILTER (bound(?o3))
# neither
# FILTER (!bound(?o2) && !bound(?o3))
## only exact: same as exact
## FILTER (bound(?o2) && !bound(?o3))
## only close: same as close
## FILTER (!bound(?o2) && bound(?o3))
## both: 0
## FILTER (bound(?o2) && bound(?o3))
# FILTER (isUri(?o))
FILTER regex(?x, \"http://dbpedia.org/resource/\" )
}
";

    $sparql = "
SELECT ?dbpedia WHERE {
    {
    { ?e <http://www.w3.org/2004/02/skos/core#exactMatch> ?dbpedia }
    UNION
    { ?c <http://www.w3.org/2004/02/skos/core#closeMatch> ?dbpedia }
    }
#    OPTIONAL {?e2 <http://www.w3.org/2004/02/skos/core#exactMatch> ?dbpedia }
#    OPTIONAL {?c2 <http://www.w3.org/2004/02/skos/core#closeMatch> ?dbpedia }
}
GROUP BY str(?dbpedia)
"; // 3202

    $sparql = "
SELECT DISTINCT ?dbpedia WHERE {
    {
    { ?e <http://www.w3.org/2004/02/skos/core#exactMatch> ?dbpedia }
    UNION
    { ?c <http://www.w3.org/2004/02/skos/core#closeMatch> ?dbpedia }
    }
#    OPTIONAL {?e2 <http://www.w3.org/2004/02/skos/core#exactMatch> ?dbpedia }
#    OPTIONAL {?c2 <http://www.w3.org/2004/02/skos/core#closeMatch> ?dbpedia }
}
"; // 3106

    $sparql = "
SELECT ?dbpedia WHERE {
    {
    { ?e <http://www.w3.org/2004/02/skos/core#exactMatch> ?dbpedia }
    UNION
    { ?c <http://www.w3.org/2004/02/skos/core#closeMatch> ?dbpedia }
    }
}
"; // 3326
// all: 3326
// distinct: 3106
// both:
    $all = array();
    $res = array();
    $dup = array();
    $result = $store->sparql($sparql);

    foreach ($result as $row) {
        $all[] = $row['dbpedia'];
        if (!in_array($row['dbpedia'], $res)) {
            $res[] = $row['dbpedia'];
        } else {
            if (!in_array($row['dbpedia'], $dup)) {
                $dup[] = $row['dbpedia'];
            } else {
//        echo "dup dup: " . $row['dbpedia'] . PHP_EOL;
            }
        }
    }

    echo "dbpedia resources having at least 2 matches: " . count($dup) . PHP_EOL;
    $counts = array_count_values($all);
    arsort($counts);
    print_r($counts);

// $store->node_info("http://dbpedia.org/resource/Steel");
//    $store->node_info("http://dbpedia.org/resource/Tax");
    // number of uses
    $needle = "http://www.w3.org/2004/02/skos/core#exactMatch"; // -> 1359
    $needle = "http://www.w3.org/2004/02/skos/core#closeMatch"; // -> 1967
    // 3326


    $sparql = "
SELECT * WHERE {
    ?s <" . $needle . "> ?o .
}
";
//    $result = $store->sparql($sparql);
//    echo "uses of " . $needle . ": " . count($result) . PHP_EOL;
} else {
    echo <<<TEXT

dbpedia resources having at least 2 matches: 194

TEXT;
}


if (false) {
    $store->fans();
} else {
    echo PHP_EOL . "max fan: 210, i.e. Völker und Ethnien" . PHP_EOL;
}
?></pre>
    </body>
</html>