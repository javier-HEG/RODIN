<?php

/*
 * Proxy class to use ARC PHP.
 * The triples in the store are filtered and only certain resources are
 * taken as nodes, others as edges. Nodes are assigned to classes or individuals.
 * Edges are assigned to object or hierarchy relations.
 *
 * Copyright 2011 HTW Chur.
 */

class SurvistaRdfGraph {

    // holds the ARC2 store
    var $arc_store;
    // holds ARC2 errors
    var $arc_errs;
    // holds ARC2 results
    var $arc_rows;
    // holds the ARC2 parser
    var $arc_parser;
    // uris which connect resources to labels
    var $label_properties;
    var $preferred_label_property;
    var $manual_labels = array(
        'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' => 'hasType',
        'http://www.w3.org/2000/01/rdf-schema#subClassOf' => 'subclassOf'
    );
    // use this language
    var $lang;
    // nodes
    var $nodes;
    var $ignored_nodes;
    // filter rules: 'substring' => allow?
    var $node_filter_rules = array(
        'http://www.w3.org/2002/07/owl#Ontology' => false,
        'http://www.w3.org/2002/07/owl#ObjectProperty' => false,
        'http://www.w3.org/2002/07/owl#topObjectProperty' => false,
        'http://www.w3.org/2002/07/owl#AsymmetricProperty' => false,
        'http://www.w3.org/2002/07/owl#IrreflexiveProperty' => false,
        'http://www.w3.org/2002/07/owl#TransitiveProperty' => false,
        'http://www.w3.org/2002/07/owl#Class' => false,
        'http://www.w3.org/1999/02/22-rdf-syntax-ns#nil' => false,
        'http://www.w3.org/2002/07/owl#SymmetricProperty' => false,
        'http://www.w3.org/2002/07/owl#Restriction' => false,
        'http://www.w3.org/2002/07/owl#Thing' => false,
        'http://www.w3.org/2002/07/owl#AllDisjointClasses' => false,
    );
    var $node_filter_default = true; // true: allow, false: deny
    // edges
    var $edges;
    var $ignored_edges;
    var $edge_filter_rules = array(
        'http://www.w3.org/1999/02/22-rdf-syntax-ns#first' => false,
        'http://www.w3.org/1999/02/22-rdf-syntax-ns#rest' => false,
        'http://www.w3.org/1999/02/22-rdf-syntax-ns#value' => false,
        'http://www.w3.org/2000/01/rdf-schema#comment' => false,
        'http://www.w3.org/2000/01/rdf-schema#domain' => false,
        'http://www.w3.org/2000/01/rdf-schema#label' => false,
        'http://www.w3.org/2000/01/rdf-schema#subPropertyOf' => false,
        'http://www.w3.org/2000/01/rdf-schema#seeAlso' => false,
        'http://www.w3.org/2000/01/rdf-schema#range' => false,
        'http://www.w3.org/2002/07/owl#allValuesFrom' => false,
        'http://www.w3.org/2002/07/owl#complementOf' => false,
        'http://www.w3.org/2002/07/owl#deprecated' => false,
        'http://www.w3.org/2002/07/owl#disjointWith' => false,
        'http://www.w3.org/2002/07/owl#equivalentClass' => false,
        'http://www.w3.org/2002/07/owl#equivalentProperty' => false,
        'http://www.w3.org/2002/07/owl#hasValue' => false,
        'http://www.w3.org/2002/07/owl#intersectionOf' => false,
        'http://www.w3.org/2002/07/owl#inverseOf' => false,
        'http://www.w3.org/2002/07/owl#members' => false,
        'http://www.w3.org/2002/07/owl#oneOf' => false,
        'http://www.w3.org/2002/07/owl#onProperty' => false,
        'http://www.w3.org/2002/07/owl#someValuesFrom' => false,
        'http://www.w3.org/2002/07/owl#unionOf' => false,
        'http://www.w3.org/2002/07/owl#versionInfo' => false,
    );
    var $edge_filter_default = true;
    var $hierarchyProperties = array(
        'http://www.w3.org/1999/02/22-rdf-syntax-ns#type',
        'http://www.w3.org/2000/01/rdf-schema#subClassOf'
    );
    var $hierarchyPropertiesDirections = array(
        'http://www.w3.org/1999/02/22-rdf-syntax-ns#type' => 1, // outgoing
        'http://www.w3.org/2000/01/rdf-schema#subClassOf' => 1
    );
    var $labelCleaner = null;

    var $label_cache = array();
    var $query_count = 0;

    function __construct() {
        $this->label_properties = array(
            'http://www.w3.org/2000/01/rdf-schema#label',
            'http://www.w3.org/2004/02/skos/core#prefLabel'
        );
        $this->preferred_label_property = $this->label_properties[1];
        $this->lang = "en";
    }

    /**
     * setup and/or connects to the ARC store with the rdf data
     *
     * @param <type> $config ARC config
     * @param <type> $reset resets the store
     */
    function connectArcStore($config, $reset = false) {
        $this->arc_store = ARC2::getStore($config);
        if (!$this->arc_store->isSetUp()) {
            $this->arc_store->setUp();
        }

        // once
        if ($reset) {
            $this->reset();
        }
        $this->dumpArcStoreErrors();
    }
    
	/**
     * Connects to a remotely SPARQL endpoint
     *
     * @param <type> $config ARC config
     */
    function connectRemoteArcStore($config) {
    	$this->arc_store = ARC2::getRemoteStore($config);
    	$this->dumpArcStoreErrors();
    }

    function reset() {
        $this->arc_store->reset();
    }

    function load($uri) {
        $this->arc_store->query('LOAD <' . $uri . '>');
        $this->dumpArcStoreErrors();
    }

    function dumpArcStoreErrors() {
        $errs = $this->arc_store->getErrors();
        if (count($errs) > 0) {
            echo "<pre>ERROR in ARC2 Store:" . PHP_EOL;
            html_out($errs);
            echo "</pre>" . PHP_EOL;
            exit;
        }
    }

    function dumpArcParserErrors() {
        $errs = $this->arc_parser->getErrors();
        if (count($errs) > 0) {
            echo "<pre>ERROR in ARC2 Parser:" . PHP_EOL;
            html_out($errs);
            echo "</pre>" . PHP_EOL;
            exit;
        }
    }

    function queryStore($query) {
//        $this->query_count++;
//        error_log($this->query_count . "\t" . $query);
        return $this->arc_store->query($query, 'rows');
    }

    /**
     * runs a sparql query and returns rows
     * 
     * @param <type> $query sparql query
     * @param <type> $verbose
     * @return <type> array of rows
     */
    function sparql($query, $verbose = false) {
//    	$this->arc_rows = $this->arc_store->query($query, 'rows');
        $this->arc_rows = $this->queryStore($query);
//        html_out($query);
//        var_dump($this->arc_rows);
        if (is_array($this->arc_rows)) {
            if ($verbose) {
                print_r($this->arc_rows);
            }
            return $this->arc_rows;
        } else {
            html_out($query);
            $this->dumpArcStoreErrors();
        }
    }

    /**
     * runs a sparql query and echoes results for html output
     * @param type $query 
     */
    function sparqlResults($query) {
        $results = $this->sparql($query);
        echo htmlspecialchars(var_export($results, true));
    }

    function setPreferredLabelProperty($prop) {
        $this->preferred_label_property = $prop;
    }

    function addAlternativeLabelPropterty($prop) {
        if (!in_array($prop, $this->label_properties)) {
            $this->label_properties[] = $prop;
        }
    }

    function reverseLabelProperties() {
        $this->label_properties = array_reverse($this->label_properties);
    }

    /**
     * returns an array with this resource's preferred label(s) if available
     */
    function pref_labels($res_uri) {
        if (array_key_exists($res_uri, $this->label_cache)) {
            return $this->label_cache[$res_uri];
        }
        $lang = "";
        if (!empty($this->lang)) {
            $lang = 'FILTER (lang(?o) = "' . $this->lang . '")';
        }
        $sparql = "
# pref_labels
SELECT ?o WHERE {
    <" . $res_uri . "> <" . $this->preferred_label_property . "> ?o .
    " . $lang . "
}
";
        $rows = $this->sparql($sparql);
        $retval = array();
        foreach ($rows as $row) {
            $retval[] = $row['o'];
        }
        $this->label_cache[$res_uri] = $retval;
        return $retval;
    }

    /**
     * returns an array with this resource's label(s) if available
     */
    function labels($res_uri) {
        if (array_key_exists($res_uri, $this->manual_labels)) {
            return array($this->manual_labels[$res_uri]);
        }
        $lang = "";
        if (!empty($this->lang)) {
            $lang = 'FILTER (lang(?o) = "' . $this->lang . '")';
        }
        
        $rows = array();
        foreach ($this->label_properties as $prop) {
            $propPart = "<" . $res_uri . "> <" . $prop . "> ?o . " . PHP_EOL;
            $sparql = " SELECT ?o WHERE { { $propPart } " . $lang . " } ";
            $rows = array_merge($rows, $this->sparql($sparql));
        }
        
        $retval = array();
        foreach ($rows as $row) {
            $retval[] = $this->cleanLabel($row['o']);
        }
        
        return array_unique($retval);
    }    
    
    
    
    
    /**
     * returns an array with this resource's label(s) if available
     * @deprecated
     */
    function labelsOld($res_uri) {
    	if (array_key_exists($res_uri, $this->manual_labels)) {
            return array($this->manual_labels[$res_uri]);
        }
        $lang = "";
        if (!empty($this->lang)) {
            $lang = 'FILTER (lang(?o) = "' . $this->lang . '")';
        }
        $propParts = array();
        foreach ($this->label_properties as $prop) {
            $propParts[] = "<" . $res_uri . "> <" . $prop . "> ?o . " . PHP_EOL;
        }
        $sparql = "
# labels
SELECT ?o WHERE {
    {
    " . implode("} UNION {", $propParts) . "
    }
    " . $lang . "
}
";
        $rows = $this->sparql($sparql);
        $retval = array();
        foreach ($rows as $row) {
            $retval[] = utf8_encode($this->cleanLabel($row['o']));
        }
        return array_unique($retval);
    }

    /**
     * returns the (first or preferred) label of res_uri or the uri
     * TODO: if no label in lang found, search for label without @lang
     *
     * @param <type> $res_uri uri of the resource (node or edge)
     * @return <type>  first label of the resource if available or $res_uri
     */
    function label_or_uri($res_uri) {
        $prefered = $this->pref_labels($res_uri);
        if (count($prefered) > 0) {
            return $this->cleanLabel($prefered[0]);
        }
        $labels = $this->labels($res_uri);
        if (count($labels) > 0) {
            return $this->cleanLabel($labels[0]);
        }
        return $res_uri;
    }

    function labels_or_uri($res_uri) {
        $labels = $this->labels($res_uri);
        $pref_labels = $this->pref_labels($res_uri);
        $labels = array_unique(array_merge($labels, $pref_labels));
        if (count($labels) > 0) {
            return $labels;
        }
        return array($res_uri);
    }

    function cleanLabel($in) {
        if ($this->labelCleaner) {
            return trim($this->labelCleaner->cleanup($in));
        } else {
            return $in;
        }
    }

    function filterNodes($nodes) {
        $this->nodes = array();
        $this->ignored_nodes = array();
        $this->applyFilterRules($nodes, $this->node_filter_rules, $this->nodes, $this->ignored_nodes, $this->node_filter_default);
    }

    function allowedNode($uri) {
        return $this->allowedByRule($uri, $this->node_filter_rules, $this->node_filter_default);
    }

    function allowedEdge($uri) {
        return $this->allowedByRule($uri, $this->edge_filter_rules, $this->edge_filter_default);
    }

    function filterEdges($edges) {
        $this->edges = array();
        $this->ignored_edges = array();
        $this->applyFilterRules($edges, $this->edge_filter_rules, $this->edges, $this->ignored_edges, $this->edge_filter_default);
    }

    function applyFilterRules($values, & $rules, & $allowed, & $denied, $default) {
        foreach ($values as $value) {
            if ($this->allowedByRule($value, $rules, $default)) {
                $allowed[] = $value;
            } else {
                $denied[] = $value;
            }
        }
    }

    function allowedByRule($value, & $rules, & $default) {
//        echo "value: " . $value;
        foreach ($rules as $key => $action) {
            if ($this->contains($value, $key)) {
//                echo ", key: " . $key . ", action: " . $action . PHP_EOL;
                return $action;
            }
        }
//        echo ", default: " . $default . PHP_EOL;
        return $default;
    }

    function contains($haystack, $needle) {
        return (strpos($haystack, $needle) !== false);
    }

    /**
     * echos textual representation (labels) of the uri's given by keys or values in $uris
     *
     * @param <type> $uris the array with uris
     * @param <type> $byKey use keys or values of $uris
     */
    function printUris($uris, $byKey = true) {
        if (!is_array($uris)) {
            $uris = array($uris);
        }
        $count = 1;
        foreach ($uris as $key => $value) {
            if ($byKey) {
                $var = $key;
            } else {
                $var = $value;
            }
            echo $count . "\t";
            $labels = $this->labels($var);
            if (count($labels) > 0) {
                echo htmlspecialchars(implode(" | ", $labels));
            }
            echo "\t(" . htmlspecialchars($var) . ")" . PHP_EOL;
            $count++;
        }
    }

    function uri_and_labels($var) {
        $labels = $this->labels($var);
        if (count($labels) > 0) {
            echo htmlspecialchars(implode(" | ", $labels));
        }
        echo "\t(" . htmlspecialchars($var) . ")" . PHP_EOL;
    }

    function printUrisByKey($uris) {
        $this->printUris($uris);
    }

    function printUrisByValue($uris) {
        $this->printUris($uris, false);
    }

    /**
     * echos pairs of node and edge connecting to and connected from $uri
     *
     * @param <type> $uri of the node
     * @param <type> $all include all resouces returned from the rdf data
     */
    function printGraph($uri, $all = false) {
        $out = "";

        $out .= "'" . $this->label_or_uri($uri) . "' (" . $uri . ")" . PHP_EOL;

        $nodes = $this->fanOut($uri, $all);
        if (count($nodes) > 0)
            $out .= "- connects:" . PHP_EOL;
        foreach ($nodes as $node) {
            $out .= "-- via '" . $this->label_or_uri($node[1]) . "' to '" . $this->label_or_uri($node[0]) . "' (" . $node[0] . ")" . PHP_EOL;
        }

        $nodes = $this->fanIn($uri, $all);
        if (count($nodes) > 0)
            $out .= "- connected from:" . PHP_EOL;
        foreach ($nodes as $node) {
            $out .= "-- '" . $this->label_or_uri($node[0]) . "' (" . $node[0] . ") via '" . $this->label_or_uri($node[1]) . "'" . PHP_EOL;
        }
        echo htmlspecialchars($out);
    }

    /**
     * returns arrays with pairs of node uri and edge uri res_uri connects to
     *
     * @param <type> $res_uri uri of the node
     * @param <type> $all include all resouces returned from the rdf data
     * @return <type> id => array( node uri, edge uri )
     */
    function fanOut($res_uri, $all = false) {
        $connects = array();
        $query = "
SELECT ?p ?o WHERE {
    <" . $res_uri . "> ?p ?o .
    FILTER (isURI(?o))
}";
//        $rows = $this->arc_store->query($query, 'rows');
        $rows = $this->queryStore($query);
        if ($rows != FALSE) {
            foreach ($rows as $row) {
                if ($all || ($this->allowedEdge($row['p']) && $this->allowedNode($row['o']))) {
                    $connects[] = array($row['o'], $row['p']);
                }
            }
        }
//        if ($this->includeDomainRange) {
//            // add domain range relations too
//            $query = "SELECT ?p ?o WHERE {
//                ?p <http://www.w3.org/2000/01/rdf-schema#domain> <" . $res_uri . "> .
//                ?p <http://www.w3.org/2000/01/rdf-schema#range> ?o .
//            }";
//            $rows = $this->arc_store->query($query, 'rows');
//            if ($rows != FALSE) {
//                foreach ($rows as $row) {
//                    if ($all || array_key_exists($row['o'], $this->node_uris)) {
//                        $connects[] = array($row['o'], $row['p']);
//                    }
//                }
//            }
//        }
        return $connects;
    }

    /**
     * returns arrays with pairs of node uri and edge uri res_uri is connected from
     *
     * @param <type> $res_uri uri of the node
     * @param <type> $all include all resouces returned from the rdf data
     * @return <type> id => array( node uri, edge uri )
     */
    function fanIn($res_uri, $all = false) {
        $connects = array();
        $query = "
SELECT ?s ?p WHERE {
    ?s ?p <" . $res_uri . "> .
    FILTER (isURI(?s))
}";
//        $rows = $this->arc_store->query($query, 'rows');
        $rows = $this->queryStore($query);
        if ($rows != FALSE) {
            foreach ($rows as $row) {
                if ($all || ($this->allowedEdge($row['p']) && $this->allowedNode($row['s']))) {
                    $connects[] = array($row['s'], $row['p']);
                }
            }
        }
//        if ($this->includeDomainRange) {
//            // add domain range relations too
//            $query = "SELECT ?p ?o WHERE {
//                ?p <http://www.w3.org/2000/01/rdf-schema#range> <" . $res_uri . "> .
//                ?p <http://www.w3.org/2000/01/rdf-schema#domain> ?o .
//            }";
//            $rows = $this->arc_store->query($query, 'rows');
//            if ($rows != FALSE) {
//                foreach ($rows as $row) {
//                    if ($all || array_key_exists($row['o'], $this->node_uris)) {
//                        $connects[] = array($row['o'], $row['p']);
//                    }
//                }
//            }
//        }
        return $connects;
    }

    /**
     * Returns true if $uri is an object relation
     *
     * @param <type> $uri the uri to check
     * @return <type> true if $uri is an object relation
     */
    function isObjectRelation($uri) {
        return!in_array($uri, $this->hierarchyProperties);
    }

    /**
     * case insensitive search for substring $needle
     * @param <type> $needle 
     */
    function search($needle) {
        $needle = trim($needle);
        $needle = $this->escape($needle);
        $lang = "";
        if (!empty($this->lang)) {
            $lang = 'FILTER (lang(?o) = "' . $this->lang . '")';
        }

        $propParts = array();
        foreach ($this->label_properties as $prop) {
            $propParts[] = "?s <" . $prop . "> ?o . " . PHP_EOL;
        }
        $sparql = "
# search
SELECT ?s WHERE {
    {
    " . implode("} UNION {", $propParts) . "
    }
    " . $lang . "
    FILTER regex(?o, \"^" . $needle . "+$\", \"i\" )
#    FILTER regex(?o, \"" . $needle . "\", \"i\" )
}
";

        $nodes = array();
        $rows = $this->sparql($sparql);
        foreach ($rows as $row) {
            if (!in_array($row['s'], $nodes)) {
                $nodes[] = $row['s'];
            }
        }
        $this->filterNodes($nodes);
        return $this->nodes;
    }

    // (costly) stats
    function numberOfTriples() {
        $sparql = "
SELECT * WHERE {
    GRAPH ?g { ?s ?p ?o . }
}
";
        return count($this->sparql($sparql));
    }

    function nodes() {
// Doesn't work with dbpedia resources...
//        $sparql = "
//SELECT DISTINCT ?o WHERE {
//    {
//    ?s ?p ?o .
//    } UNION {
//    ?o ?p ?s .
//    }
//    FILTER (isURI(?o))
//}
//";
        $nodes = array();
// subjects
        $sparql = "
SELECT DISTINCT ?s WHERE {
    ?s ?p ?o .
    FILTER (isURI(?s))
}
";
        $rows = $this->sparql($sparql);
        foreach ($rows as $row) {
            if (!in_array($row['s'], $nodes)) {
                $nodes[] = $row['s'];
            }
        }

// objects
        $sparql = "
SELECT DISTINCT ?o WHERE {
    ?s ?p ?o .
    FILTER (isURI(?o))
}
";
        $rows = $this->sparql($sparql);
        foreach ($rows as $row) {
            if (!in_array($row['o'], $nodes)) {
                $nodes[] = $row['o'];
            }
        }

        $this->filterNodes($nodes);
        return $this->nodes;
    }

    function edges() {
        $sparql = "
SELECT DISTINCT ?p WHERE {
    ?s ?p ?o .
}
";
        $edges = array();
        $rows = $this->sparql($sparql);
        foreach ($rows as $row) {
            if (!in_array($row['p'], $edges)) {
                $edges[] = $row['p'];
            }
        }
        $this->filterEdges($edges);
        return $this->edges;
    }

    function node_stats() {
        $nodes = $this->nodes();
        echo "number of nodes: " . count($nodes) . PHP_EOL;
        echo "nodes: " . PHP_EOL;
        $this->printUrisByValue($nodes);
        $nodes = $this->ignored_nodes;
        echo "number of ignored nodes: " . count($nodes) . PHP_EOL;
        echo "ignored nodes: " . PHP_EOL;
        $this->printUrisByValue($nodes);
    }

    function edge_stats() {
        $edges = $this->edges();
        echo "number of edges: " . count($edges) . PHP_EOL;
        echo "edges: " . PHP_EOL;
        $this->printUrisByValue($edges);
        $edges = $this->ignored_edges;
        echo "number of ignored edges: " . count($edges) . PHP_EOL;
        echo "ignored edges: " . PHP_EOL;
        $this->printUrisByValue($edges);
    }

    function stats() {
        echo "STATS:" . PHP_EOL;

        echo "number of triples: " . $this->numberOfTriples() . PHP_EOL;

        // $this->node_stats();
        // $this->edge_stats();
    }

    function edgeUsage($limit = 100, $offset = 0) {
        $this->edges();
        for ($i = $offset; $i < min($offset + $limit, count($this->edges)); $i++) {
            $this->anEdgeUsage($this->edges[$i]);
        }
    }

    function anEdgeUsage($uri, $limit = 10, $offset = 0) {
        echo $uri . PHP_EOL;
        $sparql = "
  SELECT ?s ?o WHERE {
  ?s <" . $uri . "> ?o .
  }
  LIMIT " . $limit . "
  OFFSET " . $offset . "
";
        $this->sparql($sparql, true);
    }

    function fans() {
        $i = 1;
        $max = 0;
        $nodes = $this->nodes();
        foreach ($nodes as $node) {
            $out = $this->fanOut($node);
            $in = $this->fanIn($node);
            $sum = count($out) + count($in);
            $max = max($max, $sum);
            echo $i . "\t" . $sum . "\t" . $this->label_or_uri($node) . PHP_EOL;
            $i++;
        }
        echo "max: " . $max . PHP_EOL;
    }

    function config() {
        echo "CONFIG:" . PHP_EOL;
        echo "lang: " . $this->lang . PHP_EOL;
        echo "pref label property:\n\t" . $this->preferred_label_property . PHP_EOL;
        echo "label properties:\n\t" . implode("\n\t", $this->label_properties) . PHP_EOL;
        echo "manual labels:\n\t" . implode("\n\t", $this->manual_labels) . PHP_EOL;
        echo "node filter rules:" . PHP_EOL;
        $this->dumpFilterRules($this->node_filter_rules);
        echo "default node filter: " . $this->actionLabel($this->node_filter_default) . PHP_EOL;
        echo "edge filter rules:" . PHP_EOL;
        $this->dumpFilterRules($this->edge_filter_rules);
        echo "default edge filter: " . $this->actionLabel($this->edge_filter_default) . PHP_EOL;
    }

    function checkSetup() {
        // check arc store setup and access
        // - wrong db credentials (db, user, pass): Warning: mysql_connect(): Access denied for user ...
        // - wrong store name: will automatically be set up, so
        // $this->arc_store->isSetUp();
        // always returns true
        // - content
        $sparql = "
SELECT * WHERE {
    GRAPH ?g { ?s ?p ?o . }
}
LIMIT 1
";
        if (count($this->sparql($sparql)) == 0) {
            echo "ERROR in RdfGraph->checkSetup(): store is empty!";
            exit();
        }
        return true;
    }

    function actionLabel($action) {
        if ($action) {
            return "allow";
        } else {
            return "deny";
        }
    }

    function dumpFilterRules($rules) {
        foreach ($rules as $substring => $action) {
            echo "\t";
            echo $this->actionLabel($action) . "\t";
            echo $substring . PHP_EOL;
        }
    }

    // TODO: escape and escape regexp
    function escape($in) {
        $out = $in;
        $badChar = array('\\', '"', "(", ")");
        $goodChar = array('', '\\"', "[(]", "[)]");
        $out = str_replace($badChar, $goodChar, $out);
        return $out;
    }

    function tescapeSearch() {
        for ($i = 0; $i < 256; $i++) {
            echo $i . "\t[" . chr($i) . "]" . PHP_EOL;
            echo "result:\t" . count($this->search(chr($i))) . PHP_EOL;
        }
    }

    function node_info($uri) {
        // subject
        $sparql = "
SELECT * WHERE {
    <" . $uri . "> ?p ?o .
}
";
        html_out($sparql);
        $this->sparql($sparql, true);
        // object
        $sparql = "
SELECT * WHERE {
    ?s ?p <" . $uri . "> .
}
";
        html_out($sparql);
        $this->sparql($sparql, true);
    }

}

?>