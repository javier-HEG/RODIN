<?php

/*
 * Class to generate a directed graph out of RDF statments.
 * A rdf statement (triple) occures once and is directed.
 * The graph can be transformed to json to use with JIT
 *
 * Copyright 2011 HTW Chur.
 */

include("../../../../$rodinsegment/fsrc/app/u/FRIutilities.php");

class GraphMaker {

    // collect unique nodes and edges
    var $nodes = array();           // uri => true
    var $edges = array();           // uri => true
    // lookup edge uri by id
    var $edgesToUri = array();      // id => uri
    var $depth0 = array();          // initual uris
    var $refOut = array();          // of uris
    var $outD = array();            // depth
    var $outs = array();            // refOut connects to
    var $refIn = array();           // of uris
    var $inD = array();             // depth
    var $ins = array();             // connect to refIn
    var $graph;                     // object for the structure to encode in json
    var $o;                         // rdf data
    var $allRelations = false;      // switch for fanIn/fanOut
    var $includeBetweens = true;    // switch for "half" depths
    // directed unique triples, adjacence matrix
    var $x = array();               // array: node id (from) => array: node id (to) => array: edge id => true
    var $maxRel = 1;                // maximum of sum of relations between two nodes. -> lineWidth [1..7]

    function __construct($store) {
        $this->graph = new GraphStructure();
        $this->o = $store;
    }

    function addOuts($fromUri, $depth = 1) {
        $this->nodes[$fromUri] = true;
        $outs = $this->o->fanOut($fromUri, $this->allRelations);
        $this->add($outs);
        $this->refOut[] = $fromUri;
        $this->outD[] = $depth;
        $this->outs[] = $outs;
    }

    function addIns($toUri, $depth = 1) {
        $this->nodes[$toUri] = true;
        $ins = $this->o->fanIn($toUri, $this->allRelations);
        $this->add($ins);
        $this->refIn[] = $toUri;
        $this->inD[] = $depth;
        $this->ins[] = $ins;
    }

    /**
     * Queries adjancent nodes upto depth 1 or 2
     *
     * @param <type> $uri uri of the starting node at depth 0
     * @param <type> $depth how far from the starting node
     */
    function prepare($uri, $depth = 1) {
        // depth 0      1       2
        // Pathogen     virus   dengue
        // depth 0: only uri
        $this->depth0[] = $uri;

        // depth 1
        $this->addOuts($uri);
        $this->addIns($uri);

        if ($depth == 1 && $this->includeBetweens) {
            // connections between nodes at depth 1
            $this->addBetweensAtDepth($depth);
        }

        if ($depth == 2) {
            // connections from nodes at depth 1 to others than the starting node
            foreach ($this->nodes as $uri1 => $id) {
                if ($uri1 != $uri) {
                    $this->addOuts($uri1, $depth);
                    $this->addIns($uri1, $depth);
                }
            }

            if ($this->includeBetweens) {
                // connections between nodes at depth 2
                $this->addBetweensAtDepth($depth);
            }
        }
    }

    /**
     * Adds connections between nodes at $depth
     *
     * @param <type> $depth
     */
    private function addBetweensAtDepth($depth) {
        $nodes = $this->nodesAtDepth($depth);
        if ($depth == 2) {
            // remove nodes from depth 1
            $nodes = array_diff($nodes, $this->nodesAtDepth(1));
        }

        foreach ($nodes as $fromUri) {
            $betweens = $this->o->fanOut($fromUri, $this->allRelations);
            $outs = array(); // allowed betweens
            foreach ($betweens as $connects) {
                // add to others or self
                if ($connects[0] == $fromUri || in_array($connects[0], $nodes)) {
                    $outs[] = array($connects[0], $connects[1]);
                }
            }
            if (count($outs) > 0) {
                $this->add($outs);
                $this->refOut[] = $fromUri;
                $this->outD[] = $depth;
                $this->outs[] = $outs;
            }
        }
    }

    private function nodesAtDepth($depth) {
        $nodes = array();
        $count = 0;
        foreach ($this->outD as $d) {
            if ($d == $depth) {
                // add nodes
                foreach ($this->outs[$count] as $connects) {
                    if (!in_array($connects[0], $this->depth0)) {
                        // exepct first nodes
                        $nodes[$connects[0]] = true;
                    }
                }
            }
            $count++;
        }
        $count = 0;
        foreach ($this->inD as $d) {
            if ($d == $depth) {
                // add nodes
                foreach ($this->ins[$count] as $connects) {
                    if (!in_array($connects[0], $this->depth0)) {
                        // exepct first nodes
                        $nodes[$connects[0]] = true;
                    }
                }
            }
            $count++;
        }
        return array_keys($nodes);
    }

    /**
     * Collect unique uris of nodes and edges
     *
     * @param <type> $adds array( node uri, edge uri)
     */
    private function add($adds) {
        foreach ($adds as $add) {
            $this->nodes[$add[0]] = true;
            $this->edges[$add[1]] = true;
        }
    }

    /**
     * Transforms array (uri => true) to array of labels
     */
    function combine() {
        // nodes
        $count = 0;
        foreach ($this->nodes as $uri => $bool) {
            // save id
            $this->nodes[$uri] = $count;
            $this->graph->n[] = $this->o->labels_or_uri($uri);
            $this->graph->nc[] = $this->cleanLabels($this->o->labels_or_uri($uri)); // For RODIN
            $this->graph->u[] = $uri;
//            if ($this->o->resourceIsClass($uri)) {
//                $this->graph->c[] = $count;
//            }
//            if ($this->o->resourceIsIndividual($uri)) {
//                $this->graph->i[] = $count;
//            }
            $count++;
        }
        // labels
        $count = 0;
        foreach ($this->edges as $uri => $bool) {
            // save id
            $this->edges[$uri] = $count;
            $this->edgesToUri[$count] = $uri;
            $this->graph->e[] = $this->o->labels_or_uri($uri);
            if ($this->o->isObjectRelation($uri)) {
                $this->graph->ed[] = 0; // both
            } else {
                // hierarchy property direction
                $this->graph->ed[] = $this->o->hierarchyPropertiesDirections[$uri];
            }

            $count++;
        }
        // matches
        foreach ($this->depth0 as $matchUri) {
            $this->graph->m[] = $this->nodes[$matchUri];
        }
    }

    function createGraph() {
        // collect unique nodes and edges
        $this->combine();
//        print_r($this->nodes);
//        print_r($this->graph->n);
//        print_r($this->edges);
//        print_r($this->graph->e);
        // iterate over outs/ins again and make adjacense matrix
        $this->makeX();
//        print_r($this->x);
        // add hierarchical/relations to graph
        $this->addRelations();
        // calculate max relations
        $this->maxEdges();
    }

    /**
     * Returns the number of relations between node x and y
     *
     * @param <type> $x id of node x
     * @param <type> $y id of node y
     * @return <type> number of relations between x and y
     */
    function countXY($x, $y) {
        if (isset($this->x[$x]) && isset($this->x[$x][$y])) {
            return count($this->x[$x][$y]);
        } else {
            return 0;
        }
    }

    /**
     * Calculates the maximum of relations between any two nodes
     */
    function maxEdges() {
        $countX = 0;
        foreach ($this->x as $fromNodes) {
            $countY = 0;
            foreach ($fromNodes as $toNodes) {
                $sum = $this->countXY($countX, $countY) + $this->countXY($countY, $countX);
                $this->maxRel = max($this->maxRel, $sum);
                $countY++;
            }
            $countX++;
        }
        $this->graph->d['m'] = $this->maxRel;
    }

    /**
     * Adds an object adjacency
     *
     * @param <type> $fromId
     * @param <type> $edgeId
     * @param <type> $toId
     */
    function addObjectRelation($fromId, $edgeId, $toId) {
        $this->graph->r[$fromId][] = $toId;
        $this->graph->rt[$fromId][] = $edgeId; // of type
    }

    /**
     * Adds an hierarchy adjacency
     *
     * @param <type> $fromId
     * @param <type> $edgeId
     * @param <type> $toId
     */
    function addHierarchyRelation($fromId, $edgeId, $toId) {
        $this->graph->h[$fromId][] = $toId;
        $this->graph->ht[$fromId][] = $edgeId; // of type
    }

    /**
     * Adds relations and types to the structure
     */
    function addRelations() {
        $fromCount = 0;
        foreach ($this->nodes as $from) {
            // add empty arrays anyways
            $this->graph->r[$fromCount] = array(); // object relation
            $this->graph->rt[$fromCount] = array(); // of type
            $this->graph->h[$fromCount] = array(); // hierarchical relation
            $this->graph->ht[$fromCount] = array(); // of type
            foreach ($this->nodes as $to) {
                if (isset($this->x[$from]) && isset($this->x[$from][$to])) {
                    foreach ($this->x[$from][$to] as $edgeId => $bool) {
                        $edgeUri = $this->edgesToUri[$edgeId];
                        if ($this->o->isObjectRelation($edgeUri)) {
                            $this->addObjectRelation($fromCount, $edgeId, $to);
                        } else {
                            $this->addHierarchyRelation($fromCount, $edgeId, $to);
                        }
                    }
                }
            }
            $fromCount++;
        }
//        print_r($this->graph->r);
//        print_r($this->graph->rt);
//        print_r($this->graph->h);
//        print_r($this->graph->ht);
    }

    /**
     * Generates the adjacence matrix
     */
    function makeX() {
        $count = 0;
        foreach ($this->outs as $outs) {
            $fromUri = $this->refOut[$count];
            foreach ($outs as $out) {
//                echo "from " . $fromUri . " via " . $out[1] . " to " . $out[0] . PHP_EOL;
//                echo "from " . $this->nodes[$fromUri] . " via " . $this->edges[$out[1]] . " to " . $this->nodes[$out[0]] . PHP_EOL;
                $fromId = $this->nodes[$fromUri];
                $toId = $this->nodes[$out[0]];
                $viaId = $this->edges[$out[1]];
//                echo "from " . $this->graph->n[$fromId] . " via " . $this->graph->e[$viaId] . " to " . $this->graph->n[$toId] . PHP_EOL;
                $this->addFromViaTo($fromId, $viaId, $toId);
            }
            $count++;
        }
        $count = 0;
        foreach ($this->ins as $ins) {
            $toUri = $this->refIn[$count];
            foreach ($ins as $in) {
//                echo "from " . $in[0] . " via " . $in[1] . " to " . $toUri . PHP_EOL;
//                echo "from " . $this->nodes[$in[0]] . " via " . $this->edges[$in[1]] . " to " . $this->nodes[$toUri] . PHP_EOL;
                $fromId = $this->nodes[$in[0]];
                $toId = $this->nodes[$toUri];
                $viaId = $this->edges[$in[1]];
//                echo "from " . $this->graph->n[$fromId] . " via " . $this->graph->e[$viaId] . " to " . $this->graph->n[$toId] . PHP_EOL;
                $this->addFromViaTo($fromId, $viaId, $toId);
            }
            $count++;
        }
    }

    /**
     * Adds an adjacency to the adjancence matrix
     *
     * @param <type> $fromId
     * @param <type> $viaId
     * @param <type> $toId
     */
    function addFromViaTo($fromId, $viaId, $toId) {
        if (!isset($this->x[$fromId])) {
            $this->x[$fromId] = array();
        }
        if (!isset($this->x[$fromId][$toId])) {
            $this->x[$fromId][$toId] = array();
        }
        // no duplicate triples/statements possible
        $this->x[$fromId][$toId][$viaId] = true;
    }

    /**
     * For RODIN, this function will clean an array of labels using
     * the functions predefined in FRIutilities.php and stopwords.php
     *
     * @param <type> $labels holds the array of labels
     */
    private function cleanLabels($labels) {
    	$clean = array();
    	
    	foreach ($labels as $label) {
    		$punctuationClean = clean_puntuation(html_entity_decode($label));
    		$clean[] = cleanup_stopwords_str($punctuationClean);
    	}
    	
    	return $clean;
    }
    
    /**
     * Returns the graph structure in JSON
     * @return <type> the graph structure in JSON
     */
    function toJson() {
        return json_encode($this->graph);
    }

    function termsToJson() {
        return json_encode(array_values($this->graph->n));
    }

}

?>