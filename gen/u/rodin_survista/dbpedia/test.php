<pre><?php
/*
 * Simple tests for DBPedia for Rodin
 *
 * Copyright 2011 HTW Chur.
 */

include_once('../survista/rodinLoader.inc.php');
include_once('config.inc.php');
include_once(SURVISTA_PATH. 'test.inc.php');

var_dump($config);

// Data

$sparql = "
SELECT * WHERE {
    GRAPH ?g { ?s ?p ?o . }
}
LIMIT 10
";
$testLoading = false;
if ($testLoading && isset($_GET['reset'])) {
    $store->reset();
    a("reset store", count($store->sparql($sparql)) == 0);

    // load
    $store->load('file:../data/stw.rdf', $config);
    $store->load('file:../data/dbpedia_stw.rdf', $config);
    a("load success", count($store->sparql($sparql)) > 0);
} else {
    a("data available", count($store->sparql($sparql)) > 0);
}


// Label
$fixture = array(
    'non_existing' => 'http://dbpedia.org/resource/Intellgencia',
    'with_labels' => 'http://dbpedia.org/resource/Intelligence',
    'with_preferred_labels' => 'http://dbpedia.org/resource/Category:Time' //,
    //'without_label' => 'http://zbw.eu/stw/descriptor/11066-11234567890'
);

a('non existing uri', count($store->pref_labels($fixture['non_existing'])) == 0);
a('with labels', count($store->labels($fixture['with_labels'])) > 0);
a('with preferred labels', count($store->pref_labels($fixture['with_preferred_labels'])) > 0);
//a('without label', $store->label_or_uri($fixture['without_label']) == $fixture['without_label']); // returns uri

// cache
if (SURVISTA_CACHES) {
    a('cache dir exists', is_dir(SURVISTA_CACHE_PATH));

    $hash1 = cacheHash('abÄcd');
    $hash2 = cacheHash('abäcd');
    a('cache umlaut', $hash1 == $hash2);

    $hash1 = cacheHash(array('a', 'b', 'Ä'));
    $hash2 = cacheHash(array('ä', 'b', 'a'));
    a('cache array', $hash1 == $hash2);

    $hash1 = cacheHash('a');
    $hash2 = cacheHash(array('A'));
    a('cache string', $hash1 == $hash2);

    $hash1 = cacheHash(getCacheKey('a', 'b'));
    $hash2 = cacheHash(getCacheKey(array('A'), 'b'));
    a('cache key', $hash1 == $hash2);
}
?></pre>