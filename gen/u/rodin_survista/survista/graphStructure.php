<?php

/*
 * Class for json encode a graph structure
 *
 * Example:
 * {
 * n(odes): [
 *  "class0", "instance1", "instance2"
 * ],
 * c(lasses): [0],
 * i(individuals): [1, 2],
 * m(atches): [0, 1],
 * r(relations): [
 *  [], [2], [1]
 * ],
 * h(hierarchical relations): [
 *  [], [0], [0]
 * ],
 * e(dges): [
 *  "is a", "connects", "connects directly"
 * ],
 * rt(ypes): [
 *  [], [1], [2]
 * ],
 * ht(ypes): [
 *  [], [0], [0]
 * ],
 * u(ris): [
 *  'uri1', 'uri2'
 * ],
 * d(ata): {
 *  m(axEdges): 1
 * }
 * }
 *
 * Copyright 2011 HTW Chur.
 */

class GraphStructure {

    var $n = array();   // id => label of node
    var $nc = array();  // id => clean labels of nodes - For RODIN
    var $c = array();   // ids of nodes that are classes
    var $i = array();   // ids of nodes that are individuals
    var $m = array();   // ids of nodes that matched the search query and get highlighted
    var $r = array();   // id of node => ids of nodes connected (to), object properties
    var $h = array();   // id of node => ids of nodes connected (to), hierarchical properties
    var $e = array();   // id => label of edge
    var $ed = array();  // id => direction of edge (1: outgoing, 0: both, -1: incoming)
    var $rt = array();  // id of node => ids of edges (via), for associating labels of edges
    var $ht = array();  // id of node => ids of edges (via)
    var $u = array();   // uris of nodes
    var $d = array();   // associative array for additional data

}

?>