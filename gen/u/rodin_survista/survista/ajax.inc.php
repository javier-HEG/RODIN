<?php

/*
 * Survista ajax controller that searches in the rdf data for the value specified by
 * GET param "n". If no matches are found it searches for the value specified
 * by the GET param "a".
 *
 * Returns nodes and edges for depth 1
 */

include('config.inc.php');
include('GraphStructure.php');
include('GraphMaker.php');

$needle = trim(@$_GET['n']);
$alt = trim(@$_GET['a']);
$uri = trim(@$_GET['u']);

// sleep(3);

if (!empty($needle)) {
    // search for $needle
    $results = $store->search($needle);

    // if no hits: search for alternative
    if (count($results) == 0 && !empty($alt)) {
        $results = $store->search($alt);
        $needle = $alt;
    }

    // if present: take the first result
    if (count($results) > 0) {

        $cacheContent = getCacheContent($result, $store->lang);
//        error_log('ajax cache hit needle? ' . ($cacheContent !== false));

        if ($cacheContent === false) {
            $maker = new GraphMaker($store);
            foreach ($results as $uri) {
                // depth 1
                $maker->prepare($uri);
            }
            $maker->createGraph();
            $data = $maker->toJson();
            setCacheContent($result, $store->lang, $data);
            echo $data;
        } else {
            echo $cacheContent;
        }
//        if (isset($_GET['t'])) {
//            echo $maker->termsToJson();
//        } else {
//            echo $maker->toJson();
//        }
    } else {
        // empty result
        echo "{}";
    }
} else if (!empty($uri)) {
    // return adjacencies for uri
    // TODO: check uri exists
    if (true || $store->isResource($uri)) {
        $cacheContent = getCacheContent($uri, $store->lang);
//        error_log('ajax cache hit uri? ' . ($cacheContent !== false));
        if ($cacheContent === false) {
            $maker = new GraphMaker($store);
            $maker->prepare($uri);
            $maker->createGraph();
            $data = $maker->toJson();
            setCacheContent($uri, $store->lang, $data);
            echo $data;
        } else {
            echo $cacheContent;
        }
    } else {
        $error = 'Resource given by parameter u not found';
        echo '{err:' . json_encode($error) . '}';
    }
} else {
    $error = 'No parameter for n given';
    echo '{err:' . json_encode($error) . '}';
}
?>