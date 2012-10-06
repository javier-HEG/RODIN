<pre><?php
/*
 * Demo visualization of STW for RODIN
 *
 * Copyright 2011 HTW Chur.
 */

include_once('config.inc.php');

/*
$needle = "/php/i";
$haystack = "PHP is the web scripting language of choice.";

$needle = '/Central and Eastern European Countries/';
$needle = '/CEEC \(Central and Eastern European Countries\)/';
$haystack = 'CEEC (Central and Eastern European Countries)';

//if (preg_match("/php/i", "PHP is the web scripting language of choice.")) {
echo $needle . PHP_EOL;
echo $haystack . PHP_EOL;
if (preg_match($needle, $haystack)) {
    echo "A match was found." . PHP_EOL;
} else {
    echo "A match was not found." . PHP_EOL;
}

exit;
*/

/*
$needle = 'Central and Eastern European Countries';
// $needle = 'CEEC \(Central and Eastern European Countries\)';
// $needle = 'CEEC \(Central and Eastern European Countries\)';
// $needle = 'CEEC \SCentral and Eastern European Countries\S';
$needle = 'CEEC (Central and Eastern European Countries)';

$sparql = '
SELECT ?s ?o WHERE {
    { ?s <http://www.w3.org/2000/01/rdf-schema#label> ?o . }
    UNION
    {?s <http://www.w3.org/2004/02/skos/core#prefLabel> ?o . }
    UNION
    {?s <http://www.w3.org/2004/02/skos/core#altLabel> ?o . }
    FILTER (lang(?o) = "en")
    # substring regex
#    FILTER regex(str(?o), "'.$store->escape($needle).'", "i" )
    # exact regex
    FILTER regex(str(?o), "^'.$store->escape($needle).'+$", "i" )
    # exact string match
#    FILTER (?o = "'.$needle.'")
}';
echo $sparql . PHP_EOL;
$store->sparql($sparql, true);
*/

// $store->node_info('http://zbw.eu/stw/descriptor/19077-5');
$store->node_info('http://dbpedia.org/resource/Tax');
$store->node_info('http://zbw.eu/stw/descriptor/11547-6');

?></pre>