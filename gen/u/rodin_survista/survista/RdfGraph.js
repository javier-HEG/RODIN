/**
 * @name Survista Rdf Graph
 * @version version 1.0
 * @author Daniel Streiff <daniel.streiff@htwchur.ch>
 * @fileoverview
 * Visualizes RDF GraphStructure as jit ForceDirected
 *
 * Copyright 2011 HTW Chur.
 */

/**
 * implement spike edge type
 */
$jit.ForceDirected.Plot.EdgeTypes.implement({
    'spike': {
        'render': function(adj, canvas) {
            var from = adj.nodeFrom.pos.getc(true),
            to = adj.nodeTo.pos.getc(true),
            dim = adj.getData('dim'),
            direction = adj.data.$direction,
            inv = (direction && direction.length>1 && direction[0] != adj.nodeFrom.id);
            var ctx = canvas.getCtx();
            // invert edge direction
            if (inv || adj.getData('swap')) {
                var tmp = from;
                from = to;
                to = tmp;
            }
            var vect = new $jit.Complex(to.x - from.x, to.y - from.y);
            vect.$scale(dim / vect.norm());
            var intermediatePoint = new $jit.Complex(from.x, from.y),
            normal = new $jit.Complex(-vect.y / 2, vect.x / 2),
            v1 = intermediatePoint.add(normal),
            v2 = intermediatePoint.$add(normal.$scale(-1));
            ctx.beginPath();
            ctx.moveTo(from.x, from.y);
            ctx.lineTo(to.x, to.y);
            ctx.stroke();
            ctx.beginPath();
            ctx.moveTo(v1.x, v1.y);
            ctx.lineTo(v2.x, v2.y);
            ctx.lineTo(to.x, to.y);
            ctx.closePath();
            ctx.fill();
        },
        'contains': function(adj, pos) {
            var from = adj.nodeFrom.pos.getc(true),
            to = adj.nodeTo.pos.getc(true);
            return this.edgeHelper.arrow.contains(from, to, pos, this.edge.epsilon);
        }
    }
});

/**
 * Namespace
 */
var htw = {
    sii: {
        survista: {}
    }
};

/**
 * Module
 */
htw.sii.survista.graph = {
    container: '',      // html element
    id_prefix: 'g1_',   // unique nodes for multiple fds
    data: {},           // json data from ajax
    jitData: [],        // jit Adjacencies
    fd: {},             // jit ForceDirected
    maxLineWidth: 7,    // edge contains width: 7
    lang: {
        lbl_individual: 'Individual',
        lbl_class: 'Class',
        lbl_resource: 'Resource'
    },
    highlightColor: '#669933', // edges and nodes
    hierarchyRelationColor: '#999999',
    objectRelationColor: '#cccccc',
    classColor: '#cccccc',
    individualColor: '#cccccc',
    matchColor: '#99ccff', // matching the query
    ajaxUrl: 'ajax.php',
    noResult: '<div>no result</div>',
    inputFieldSelector: '#txtSearch',
    centerUri: '', // uri of the node main node
    centerLabel: '', // alternative label for the main node
    /**
     * Sets up module functions, converts data to jit format, loads jit
     *
     * @param {string} container The html element id to inject the graph into
     * @param {object} data The GraphStructre object in JSON
     * @param {string} prefix Prefix for nodes
     */
    //init: function(container, data, prefix) {
    init: function(container, prefix) {
    	var data = parent.window.survistaData;
    	
    	var el, that = this, iterations;

        if (data.d) {

            this.container = container;
            this.data = data;

            if (prefix) {
                this.id_prefix = prefix;
            }

            /* functions depending on module data */
            this.edgeLabel = function(id) {
                return data.e[id][0];
            };
            this.edgeLabels = function(id) {
                return data.e[id];
            };
            this.edgeDirection = function(id) {
                return data.ed[id];
            };
            this.nodeLabel = function(id) {
                return data.n[id][0];
            };
            this.nodeLabels = function(id) {
                return data.n[id];
            };
            this.nodeCleanLabels = function(id) { // For RODIN
            	return data.nc[id];
            };
            this.nodeUri = function(id) {
                return data.u[id];
            };
            if (data.d && data.d['m'] && data.d['m'] > this.maxLineWidth) {
                // normalize
                var stepsPerNum = this.maxLineWidth / data.d['m'];
                this.edgeLineWidth = function (num) {
                    return num * stepsPerNum;
                };
            } else {
                // use num, [1..7]
                this.edgeLineWidth = function (num) {
                    return num;
                };
            }
            this.nodeId = function(id) {
                return this.id_prefix + id;
            };
            el = document.getElementById(this.container);
            this.startBusy = function() {
                el.style.cursor = 'wait';
            }
            this.stopBusy = function() {
                el.style.cursor = 'default';
            };

            this.adjustedIterations = function (count) {
                var minI = 25, maxI = 150, maxN = 70; // maxN: count > maxN => i = minI
                return Math.max(minI, (maxN - count) * (maxI - minI)/maxN + minI);
            }
            iterations = this.adjustedIterations(data.n.length);

            this.initJit(this.container, iterations);
            this.startBusy();
            this.makeJitData();
            this.fd.loadJSON(this.jitData);
            this.fd.computeIncremental({
                iter: 40,
                property: 'end',
                onComplete: function() {
                    that.stopBusy();
                    that.fd.animate({
                        modes: ['linear'],
                        // transition: $jit.Trans.Elastic.easeOut,
                        transition: $jit.Trans.linear,
                        duration: 100
                    });
                }
            });
        } else {
            // empty result, no match found
            el = document.getElementById(container);
            el.innerHTML = this.noResult;

        }
    },
    /**
     * New callback function showing navigation tips instead of
     * concept related information.
     *
     * @param tip {} The element to inject the html
     * @param node {} The node which was returned by the event
     */
    showNavigationTips: function (tip, node) {
    	var html;
    	if (node.nodeFrom) {
    		// edge
            var edgeFrom = node.nodeFrom;
            var edgeTo = node.nodeTo;
            var el = node.data.toTypes[0];
            var elLabel = this.edgeLabel(el);
            html = '<p style=\"text-align:center; margin: 0px;\">"'
            	+ edgeFrom.name + '"<br/><i>' + elLabel + '</i><br />"'
            	+ edgeTo.name + '"</p>';
        } else {
        	// node
            //html = '<b>' + node.name + '</b> (' + node.data.nodeType + ')<br />';
        	html = '<b>' + node.name + '</b><br />';
            html += parent.lg("lblDoubleClickToExplore") + '<br />';
            html += parent.lg("lblRightClickForOptions") + '<br />';
        }
        tip.innerHTML = html;
    },
    /**
     * Callback function for onshow events. Also works for edges
     *
     * @param tip {} The element to inject the html
     * @param node {} The node which was returned by the event
     */
    showTips: function (tip, node) {
        var html,
        i,
        el,
        inv,
        direction,
        edgeTo,
        edgeFrom,
        list,
        that = this,
        types;
        if (node.nodeFrom) {
            // edge
            html = "",
            edgeFrom = node.nodeFrom,
            edgeTo = node.nodeTo,
            list = [],
            direction = node.data.$direction,
            inv = (direction && direction.length > 1 && direction[0] != edgeFrom.id);
            if (inv) {
                // swap
                edgeFrom = node.nodeTo;
                edgeTo = node.nodeFrom;
            }
            for (i in node.data.toTypes) {
                if (node.data.toTypes.hasOwnProperty(i)) {
                    el = node.data.toTypes[i];
                    list.push("'" + edgeFrom.name + "' " + this.edgeLabel(el) + " '" + edgeTo.name + "'");
                }
            }
            for (i in node.data.fromTypes) {
                if (node.data.fromTypes.hasOwnProperty(i)) {
                    el = node.data.fromTypes[i];
                    list.push("'" + edgeTo.name + "' " + this.edgeLabel(el) + " '" + edgeFrom.name + "'");
                }
            }
            html = html + list.join("<br />");
        } else {
            // node
            html = "<b>" + node.name + "</b><br />";
            // + 'Type: ' + node.data.nodeType;

            // outgoing edges
            list = [];
            node.eachAdjacency(function(adj) {
                direction = adj.data.$direction,
                inv = (direction && direction.length > 1 && direction[0] != node.id);
                if (inv) {
                    // swap
                    types = adj.data.fromTypes;
                } else {
                    types = adj.data.toTypes;
                }
                for (i in types) {
                    if (types.hasOwnProperty(i)) {
                        el = types[i];
                        list.push(" * '" + that.edgeLabel(el) + "' '" + adj.nodeTo.name + "'");
                    }
                }
            });
            if (list.length > 0) {
                html = html + "<br />outgoing relations:<br />"
                html = html + list.join("<br />");
                html = html + "<br />";
            }

            // incoming edges
            // TODO: dry
            list = [];
            node.eachAdjacency(function(adj) {
                direction = adj.data.$direction,
                inv = (direction && direction.length > 1 && direction[0] != node.id);
                if (!inv) {
                    // swap
                    types = adj.data.fromTypes;
                } else {
                    types = adj.data.toTypes;
                }
                for (i in types) {
                    if (types.hasOwnProperty(i)) {
                        el = types[i];
                        list.push(" '" + adj.nodeTo.name + "' '" + that.edgeLabel(el) + "' *");
                    }
                }
            });
            if (list.length > 0) {
                html = html + "<br />incoming relations:<br />"
                html = html + list.join("<br />");
            }
        }
        tip.innerHTML = html;
    },
    /**
     * Callback function for onMouseEnter events. Highlights outgoing edges
     *
     * @param fd {ForceDirected} The jit graph
     * @param node {} The node which was returned by the event
     */
    highlightEdges: function (fd, node) {
        var changed = false;
        node.eachAdjacency(function(adj) {
            // from matches or bidirectional
            if (adj.data.$direction[0] == node.id || adj.data.fromTypes.length > 0) {
                changed = true;
                if (adj.data.$color) {
                    // save if present
                    adj.data.oldColor = adj.data.$color;
                }
                adj.data.$color = '#669933';
            }
        });
        //trigger animation to final styles
        if (changed) {
            fd.fx.animate({
                modes: [
                'edge-property:lineWidth'
                ],
                duration: 500
            });
        }
    },
    /**
     * Callback function for onMouseEnter events. Highlights outgoing edges and
     * connected nodes
     *
     * @param fd {ForceDirected} The jit graph
     * @param node {} The node which was returned by the event
     * @param that {htw.sii.survista.graph} the graph module
     */
    highlightEdgesAndNodes: function (fd, node, that) {
        var changed = false,
        hNode; // node to be highlighted
        node.eachAdjacency(function(adj) {
            // from matches or bidirectional
            if (adj.data.$direction[0] == node.id || adj.data.fromTypes.length > 0) {
                changed = true;
                // the edge
                if (adj.data.$color) {
                    // save if present
                    adj.data.oldColor = adj.data.$color;
                }
                adj.data.$color = that.highlightColor;
                // the node
                if (adj.nodeFrom.id == node.id) {
                    hNode = adj.nodeTo;
                } else {
                    hNode = adj.nodeFrom;
                }
                if (hNode.data.$color) {
                    // save if present
                    hNode.data.oldColor = hNode.data.$color;
                }
                hNode.data.$color = that.highlightColor;
            }
        });
        //trigger animation to final styles
        if (changed) {
            fd.fx.animate({
                modes: [
                'edge-property:lineWidth'
                ],
                duration: 500
            });
        }
    },
    /**
     * Callback function for onMouseLeave events. Resets highlighted edges and
     * connected nodes
     *
     * @param fd {ForceDirected} The jit graph
     * @param node {} The node which was returned by the event
     */
    dehighlightEdgesAndNodes: function (fd, node) {
        var changed = false,
        hNode; // highlighted node
        node.eachAdjacency(function(adj) {
            // from matches or bidirectional
            if (adj.data.$direction[0] == node.id || adj.data.fromTypes.length > 0) {
                changed = true;
                // the edge
                if (adj.data.oldColor) {
                    // reset to previous if present
                    adj.data.$color = adj.data.oldColor;
                    delete adj.data.oldColor;
                } else {
                    // reset to default
                    delete adj.data.$color;
                }
                // the node
                if (adj.nodeFrom.id == node.id) {
                    hNode = adj.nodeTo;
                } else {
                    hNode = adj.nodeFrom;
                }
                if (hNode.data.oldColor) {
                    // reset to previous if present
                    hNode.data.$color = hNode.data.oldColor;
                    delete hNode.data.oldColor;
                } else {
                    // reset to default
                    delete hNode.data.$color;
                }
            }
        });
        //trigger animation to final styles
        if (changed) {
            fd.fx.animate({
                modes: [
                'edge-property:lineWidth'
                ],
                duration: 500
            });
        }
    },
    /**
     * Callback function for onMouseLeave events. Resets highlighted edges
     *
     * @param fd {ForceDirected} The jit graph
     * @param node {} The node which was returned by the event
     */
    dehighlightEdges: function (fd, node) {
        var changed = false;
        node.eachAdjacency(function(adj) {
            // from matches or bidirectional
            if (adj.data.$direction[0] == node.id || adj.data.fromTypes.length > 0) {
                changed = true;
                if (adj.data.oldColor) {
                    // reset to previous if present
                    adj.data.$color = adj.data.oldColor;
                    delete adj.data.oldColor;
                } else {
                    // reset to default
                    delete adj.data.$color;
                }
            }
        });
        //trigger animation to final styles
        if (changed) {
            fd.fx.animate({
                modes: [
                'edge-property:lineWidth'
                ],
                duration: 500
            });
        }
    },
    /**
     * Initializes the jit force directed graph and "mixes out" Tips.onMouseMove
     * to support also edges
     *
     * @param container {string} id of the visualization container
     * @param iterations {int} number of iterations for the FD algorithm
     */
    initJit: function(container, iterations) {
        var fd, // make visible in nested closures
        that = this;
        
        var rodinsegment = $('#rodinSegment').text();

        fd = new $jit.ForceDirected({
            //id of the visualization container
            injectInto: container,
            //Enable zooming and panning
            //by scrolling and DnD
            Navigation: {
                enable: true,
                //Enable panning events only if we're dragging the empty
                //canvas (and not a node).
                panning: 'avoid nodes',
                zooming: 20 //zoom speed. higher is more sensible
            },
            // Change node and edge styles such as
            // color and width.
            // These properties are also set per node
            // with dollar prefixed data-properties in the
            // JSON structure.
            Node: {
                overridable: true,
                type: 'circle',
                color: that.classColor, // default: class
                dim: 10
            },
            Edge: {
                overridable: true,
                color: that.objectRelationColor, // object relation
                lineWidth: 1,
//                dim: 5,
//                type: 'spike' // needs edge.data.$direction = [nodeFrom, nodeTo] to work
                type: 'line'
            },
            // Native canvas text styling
            // type 'Native' allows mouse over events to work right on nodes and edges!
            Label: {
                type: 'Native', //Native or HTML
                size: 10,
                color: '#000000',
                textBaseline: 'top'
            },
            //Add Tips
            Tips: {
                enable: true,
                onShow: this.showNavigationTips,
                edgeLabel: this.edgeLabel
            },
            // Add node events
            Events: {
                enable: true,
                enableForEdges: true,
                //Change cursor style when hovering a node
                onMouseEnter: function(node) {
                    if(node.nodeFrom) {
                    // edge
                    } else {
                        fd.canvas.getElement().style.cursor = 'move';
//                        that.highlightEdgesAndNodes(fd, node, that);
                    }
                },
                onMouseLeave: function(node) {
                    if (node.nodeFrom) {
                    // edge
                    } else {
                        fd.canvas.getElement().style.cursor = '';
//                        that.dehighlightEdgesAndNodes(fd, node);
                    }
                },
                //Update node positions when dragged
                onDragMove: function(node, eventInfo, e) {
                    if(node.nodeFrom) {
                    // edge
                    } else {
                        // node
                        var pos = eventInfo.getPos();
                        node.pos.setc(pos.x, pos.y);
                        fd.plot();
                    }
                },
                //Implement the same handler for touchscreens
                onTouchMove: function(node, eventInfo, e) {
                    $jit.util.event.stop(e); //stop default touchmove event
                    this.onDragMove(node, eventInfo, e);
                },
                // show context menu with labels
                onRightClick: function(node, eventInfo, e) {
                    var ctxm, pos, row, td, a;
                    if (node.id) {
                        $jit.util.event.stop(e);
                        // hide tip
                        var tip = document.getElementById('_tooltip');
                        tip.style.display = 'none';

                        //parent.bc_add_breadcrumb_unique(node.data.cleanLabels[0],'survista');
                        
                        // get or create context menu once
                        ctxm = $(".contextMenu");
                        if(ctxm.length == 0) {
                            ctxm = $('<div />');
                            ctxm.attr('id', '_contextMenu');
                            ctxm.addClass('contextMenu');
                            ctxm.css('position', 'absolute');
                            ctxm.css('zIndex', 13000);
                            $('body').append(ctxm);
                        }
                        pos = $jit.util.event.getPos(e);
                        ctxm.css('top', pos.y - 10 + 'px');
                        ctxm.css('left', pos.x - 10 + 'px');
                        ctxm.css('height', 'auto');
                        ctxm.empty();
                        for (i in node.data.labels) {
                            if (node.data.labels.hasOwnProperty(i)) {
                            	el = node.data.labels[i];
                            	elClean = node.data.cleanLabels[i];
                            	
                            	// label
                            	var ul = $('<ul />');
                            	
                                var b = $('<h1 />');
                                b.html(el);
                                ul.append(b);
                                
                                // a) add to breadcrumb
                                var li = $('<li />');
                                li.addClass('addToBreadcrumb');

                                var a = $('<a />');
                                a.html(parent.lg("lblSurvistaAddToBreadcrumb"));
                                a.attr('href', '#');
                                (function (el) {
                                	a.click(function() {
                                		parent.bc_add_breadcrumb_unique(el,'survista');
                                		ctxm.hide();
                                	});
                                })(el);
                                
                                li.append(a);
                                ul.append(li);
                                
                                // b) explore in ontofacets
                                li = $('<li />');
                                li.addClass('exploreOntoFacets');
                                
                                a = $('<a />');
                                a.html(parent.lg("lblSurvistaExploreOntoFacets"));
                                a.attr('href', '#');                                
                                (function (el) {
                                	a.click( function() {
                                		parent.fb_set_node_ontofacet(el);
                                		parent.setLanguageForOntofacets($('#txtLang').text());
                                		parent.launch_fri_onto_metasearch(el, 0, 0, 0, 0, 0, 0, parent.$p);
                                		ctxm.hide();
                                		return false;
                                	});
                                })(el);
                                
                                li.append(a);
                                ul.append(li);
                                
                                ctxm.append(ul);
                            }
                        }
                        
                        ctxm.show();
                        
                        var canvas = $("#rdfgraph-canvaswidget");
                        
                        // Move context menu up if it overflows the given frame 
                        var bottomLine = ctxm.offset().top + ctxm.height();
                        if (bottomLine > canvas.offset().top + canvas.height()) {
                        	var fromTop = canvas.height() - ctxm.height();

                        	if (fromTop > 0) {
                        		// Simply move it
                        		ctxm.css('top', fromTop - 10);
                        	} else {
                        		// Needs resizing
                        		ctxm.css('top', 3);
                        		ctxm.css('height', canvas.height() - 13);
                        	}
                        	
                        }
                        
                        // Move context menu right if it overflows the given frame 
                        var rightLine = ctxm.offset().left + ctxm.width();
                        if (rightLine > canvas.width()) {
                        	var fromLeft = canvas.width() - ctxm.width();
                        	ctxm.css('left', fromLeft - 10);
                        }
                        
                        // Set the action on mouse leaving the menu
						ctxm.mouseleave(function(){
							ctxm.hide();
						});
                        
                        $(document).unbind('click');

                        setTimeout( function() { // Delay for Mozilla
                            $(document).click( function() {
                                $(document).unbind('click');
                                ctxm.hide();
                                return false;
                            });
                        }, 0);
                    }
                }
            },
            //Number of iterations for the FD algorithm
            iterations: iterations,
            //Edge length
            levelDistance: 100,
            // Add text to the labels. This method is only triggered
            // on label creation and only for DOM labels (not native canvas ones).
            onCreateLabel: function(domElement, node){
                domElement.innerHTML = node.name;
                var style = domElement.style;
                style.fontSize = "0.8em";
                style.color = "#000000";
            },
            // Change node styles when DOM labels are placed
            // or moved.
            onPlaceLabel: function(domElement, node){
                var style = domElement.style;
                var left = parseInt(style.left);
                var top = parseInt(style.top);
                var w = domElement.offsetWidth;
                style.left = (left - w / 2) + 'px';
                style.top = (top + 10) + 'px';
                style.display = '';
            }
        });

        /* overwrite Tips.onMouseMove to also return edges */
        fd.Classes.Tips.prototype.onMouseMove = function(e, win, opt) {
            var $ = $jit.util;

            if(this.dom && this.isLabel(e, win)) {
                this.setTooltipPosition($.event.getPos(e, win));
            }
            if(!this.dom) {
                var node = opt.getNode();

                // HTW
                if(!node) {
                    // get edge
                    var edge = opt.getEdge();
                    if (edge) {
                        // always, first time or difference
                        if(this.config.force || !this.edge || this.edge != edge) {
                            this.edge = edge;
                        }
                        node = edge;
                    }
                }

                if(!node && !edge) {
                    // Finish, no tip
                    this.hide(true);
                    return;
                }

                if(this.config.force || !this.node || this.node != node) {
                    this.node = node;
                    this.config.onShow(this.tip, node, opt.getContains());
                }
                this.setTooltipPosition($.event.getPos(e, win));
            }
        };

        /* overwrite Layouts.ForceDirected.getOptions to keep more distance between nodes */
        fd.getOptions = function(random) {
            var s = this.canvas.getSize();
            var w = s.width, h = s.height;
            //count nodes
            var count = 0;
            this.graph.eachNode(function(n) {
                count++;
            });
            var k2 = w * h / count * 40, k = Math.sqrt(k2);
            var l = this.config.levelDistance;

            return {
                width: w,
                height: h,
                tstart: w * 0.1,
                nodef: function(x) {
                    return k2 / (x || 1);
                },
                edgef: function(x) {
                    return /* x * x / k; */ k * (x - l);
                }
            };
        };

        /* add double click */
        // the pos function from MouseEventsManager
        var getPos = function(e) {
            var canvas = fd.canvas,
            s = canvas.getSize(),
            p = canvas.getPos(),
            ox = canvas.translateOffsetX,
            oy = canvas.translateOffsetY,
            sx = canvas.scaleOffsetX,
            sy = canvas.scaleOffsetY,
            pos = $jit.util.event.getPos(e);
            return {
                x: (pos.x - p.x - s.width/2 - ox) * 1/sx,
                y: (pos.y - p.y - s.height/2 - oy) * 1/sy
            };
        };

        // the event
        $jit.util.addEvent(fd.canvas.getElement(), 'dblclick', function(e) {
            var pos = getPos(e),
            geom,
            found;
            fd.graph.eachNode(function(node) {
                geom = fd.fx.nodeTypes[node.getData('type')];
                if (geom.contains.call(fd.fx, node, pos)) {
                    found = node;
                }
            });
            //            if (found && !found.data.match) {
            if (found) {
                fd.canvas.getElement().style.cursor = 'wait';
                that.addByUri(found.data.uri);
            }
        });

        this.fd = fd; // write back to module
    },
    /**
     * Converts source data (GraphStructure) into jit data format
     */
    makeJitData: function () {
        var i,              // array index
        el,                 // array element
        data = this.data,   // source data
        jitData = [];       // converted source data

        // add adjacencies
        for (i in data.n) {
            if (data.n.hasOwnProperty(i)) {
                el = this.nodeLabel(i);
                var nodeUri = this.nodeUri(i);
                if (nodeUri == this.centerUri && el != this.centerLabel) {
                	el = this.centerLabel + " (" + el + ")";
                }
                jitData.push({
                    adjacencies: [],
                    data: {
                        uri: nodeUri,
                        labels: this.nodeLabels(i),
                        cleanLabels: this.nodeCleanLabels(i), // For RODIN
                        match: false,
                        nodeType : htw.sii.survista.graph.lang.lbl_resource
                    },
                    id: this.nodeId(i),
                    name: el
                })
            }
        }

        // style individuals gray
        for (i in data.i) {
            if (data.i.hasOwnProperty(i)) {
                el = data.i[i];
                if (!jitData[el].data['$color']) {
                    // exept first node
                    jitData[el].data['$color'] = this.individualColor;
                }
            }
        }

        // add relations: object (default)
        this.addRelations(data.r, jitData, {}, data.rt);
        // add relations: hierarchy (black)
        this.addRelations(data.h, jitData, {
            "$color" : this.hierarchyRelationColor,
            "$type" : "spike",
            "$dim" : 7
        }, data.ht);

        // add node types
        for (i in data.c) {
            if (data.c.hasOwnProperty(i)) {
                el = data.c[i];
                jitData[el].data['nodeType'] = htw.sii.survista.graph.lang.lbl_class;
            }
        }
        for (i in data.i) {
            if (data.i.hasOwnProperty(i)) {
                el = data.i[i];
                jitData[el].data['nodeType'] = htw.sii.survista.graph.lang.lbl_individual;
            }
        }

        // style matches
        for (i in data.m) {
            if (data.m.hasOwnProperty(i)) {
                el = data.m[i];
                jitData[el].data['$color'] = this.matchColor;
                jitData[el].data['match'] = true;
            }
        }

        // save
        this.jitData = jitData;
    },
    /**
     * Returns the types of outgoing relations
     *
     * @param fromIndex {number} The index of the from node
     * @param toId {number} The id of the to node
     * @param allRelations {array} The outgoing relations
     * @param allTypes {array} The types of relations
     * @return {array} The types of outgoing relations
     */
    relationTypes: function (fromIndex, toId, allRelations, allTypes) {
        var toRelations = allRelations[fromIndex],
        i,  // toRelations array index
        el, // toRelations array element (index of node)
        types = [];
        for (i in toRelations) {
            if (toRelations.hasOwnProperty(i)) {
                el = toRelations[i];
                if (el == toId) {
                    types.push(allTypes[fromIndex][i]);
                }
            }
        }
        return types;
    },
    /**
     * Adds jit adjacencies with extra data
     *
     * @param relations {array} The ids of the relations
     * @param jitData {object} Where to add the adjacencies to
     * @param edgeData {object} The data (color) for the edge
     * @param relData {object} Data for the relations
     */
    addRelations: function (relations, jitData, edgeData, relData) {
        var i, // array index
        el, // array element
        toIndex, // 2nd array index
        toId, // 2nd array element (to node)
        eData, // edgeData copied and enriched
        x,
        y;
        for (i in relations) {
            if (relations.hasOwnProperty(i)) {
                el = relations[i];
                for (toIndex in el) {
                    if (el.hasOwnProperty(toIndex)) {
                        toId = el[toIndex];
                        eData = {};
                        // copy
                        if (edgeData['$color']) {
                            eData['$color'] = edgeData['$color'];
                        }
                        if (edgeData['$type']) {
                            eData['$type'] = edgeData['$type'];
                        }
                        if (edgeData['$dim']) {
                            eData['$dim'] = edgeData['$dim'];
                        }
                        // only the first adj per pair will be kept
                        // nodeFrom and nodeTo will contain all relations (in both directions) each!
                        // add all types of connections between the two nodes:
                        // from [x] to
                        x = this.relationTypes(i, toId, relations, relData);
                        eData['toTypes'] = x;
                        // to [y] from
                        y = this.relationTypes(toId, i, relations, relData);
                        eData['fromTypes'] = y;
                        eData['$lineWidth'] = this.edgeLineWidth(x.length + y.length);
                        // from to
                        eData['$direction'] = [this.nodeId(i), this.nodeId(toId)];
                        if (this.edgeDirection(x) == -1) {
                            // swap
                            eData['$swap'] = true;
                        } else {
                            eData['$swap'] = false;
                        }
                        //                        // STW
                        //                        if ($.inArray(2, x) > -1 || $.inArray(2, y) > -1) {
                        //                            eData['$alpha'] = 0.3;
                        //                        }
                        //                        if ($.inArray(0, x) > -1) {
                        //                            eData['$type'] = 'arrow';
                        //                        }
                        jitData[i].adjacencies.push({
                            nodeTo: this.nodeId(toId),
                            data: eData
                        })
                    }
                }
            }
        }
    },
    /**
     * Queries a graph for the resource specified by uri
     *
     * @param uri {string} The uri to search for
     */
    addByUri: function (uri) {
//    	alert("Adding URI : " + uri + " using lang : " + document.getElementById('txtLang').textContent);
        var that = this;
        that.startBusy();
        $.ajax({
            url: that.ajaxUrl,
            data: {
                u: uri,
                rodinsegment: $('#rodinSegment').text(),
                l: $('#txtLang').text(),
                l10n: parent.__lang
            },
            success: function(data) {
                that.stopBusy();
                parent.window.survistaData = data;
                
                // Set center record to empty
                parent.window.survistaCenterUri = '';
                parent.window.survistaCenterLabel = '';
                
                that.addMore();
            },
            dataType: "json"
        });
    },
    /**
     * Replaces the visualization by a new graph
     *
     * @param {object} data The GraphStructre object in JSON
     */
    addMore: function() {
    	$("#" + this.container).empty();
        this.init(this.container);
    }
};