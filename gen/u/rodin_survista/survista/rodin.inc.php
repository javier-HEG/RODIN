<?php
/*
 * Survista Html controller that allows to search in the rdf data
 * and visualizes the result as a JIT force directed graph
 *
 * Copyright 2011 HTW Chur.
 */
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
    <head>
        <title>SUrvista Rdf VISualizaTion Application</title>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!--[if IE]><script language="javascript" type="text/javascript" src="../vendor/excanvas.js"></script><![endif]-->
        <script type="text/javascript" src="../vendor/jquery-1.4.4.js"></script>
        <script type="text/javascript" src="../vendor/jit-yc.js"></script>
        <script type="text/javascript" src="../survista/RdfGraph.js"></script>
        <style type="text/css">
#rdfgraph {
	/*border: 1px solid;*/
	float: center;
	display: none;
	width: 100%;
	height: 100%;
}

.tip {
	color: #111;
	/* width: 139px; */
	background-color: white;
	border: 1px solid #ccc;
	/*-moz-box-shadow: #555 2px 2px 8px;
	-webkit-box-shadow: #555 2px 2px 8px;
	-o-box-shadow: #555 2px 2px 8px;
	box-shadow: #555 2px 2px 8px;
	opacity: 0.9;
	filter: alpha(opacity = 90);*/
	font-size: 10px;
	font-family: Verdana, Geneva, Arial, Helvetica, sans-serif;
	padding: 7px;
}

.contextMenu {
	/*-moz-box-shadow: #555 2px 2px 8px;
	-webkit-box-shadow: #555 2px 2px 8px;
	-o-box-shadow: #555 2px 2px 8px;
	box-shadow: #555 2px 2px 8px;*/
	
	/*opacity: 0.9;
	filter: alpha(opacity = 90);*/
	
	background-color: <?php echo $COLOR_PAGE_BACKGROUND;?>;
	border: solid 1px #CCC;
	color: black;
	width: 180px;
	font-size: 12px;
	font-family: "Arial";
	padding: 3px;
	
	overflow-y: auto;
}

.contextMenu ul {
	padding: 0px;
	margin: 0px;
}

.contextMenu h1 {
	font-size: 12px;
	font-weight: bold;
	padding: 1px 2px 6px 2px;
	margin: 2px;
}

.contextMenu a {
	color: #333;
	text-decoration: none;
	display: block;
	background-position: 6px center;
	background-repeat: no-repeat;
	outline: none;
	font-size: 12px;
	display: block;
	line-height: normal;
	height: auto;
	padding: 1px 2px 1px 28px;
	background-position: 6px center;
	background-repeat: no-repeat;
}

.contextMenu li {
	list-style: none;
	padding: 0px;
	margin: 0px;
	line-height: auto;
	height: auto;
}

.contextMenu a:hover {
	color: black;
	background-color: <?php echo $COLOR_PAGE_BACKGROUND2; ?>;
}

.contextMenu li.addToBreadcrumb a {
	background-image: url('<?php echo $RODINIMAGESWEB; ?>/add-to-breadcrumb.png');
}

.contextMenu li.exploreOntoFacets a {
	background-image: url('<?php echo $RODINIMAGESWEB; ?>/magnifier-onto-small.png');
}

</style>
    </head>
    <body>
        <?php
        include('graphStructure.php');
        include('graphMaker.php');

        $allRelations = false;

        $maker = new GraphMaker($store);
        $maker->allRelations = $allRelations;
        // set includeBetweens to true to add "0.5" to depth
        // $maker->includeBetweens = false;
        // search
        
        $result = array();
        $centerNode = '';
        $myCenterNode = '';
        
        $tryToReloadData = false;
        
        if (isset($_GET['q'])) {
            $needle = $_GET['q'];
            $result = $store->search(utf8_decode($needle));
            
            //$filename="/Users/vicho/Desktop/debugging.txt";
			//$h=fopen($filename,"a");
			//fwrite($h,"rodin.inc.php ::: needle : $needle\n");
			//fclose($h);
        } else if (isset($_GET['r'])) {
            $uris = $_GET['r'];

            // separate by newline, remove unnecessary chars
            function cleanUris($in) {
                $retval = array();
                $values = explode("\n", $in);
                foreach ($values as $value) {
                    $value = trim($value);
                    $badChr = array("\r");
                    $goodChr = array("");
                    $value = str_replace($badChr, $goodChr, $value);
                    if (!empty($value)) {
                        $retval[] = $value;
                    }
                }
                return $retval;
            }

            $userUris = array_unique(cleanUris($uris));
            
            // use user label for center node
            if (count($userUris) == 1 && isset($_GET['ul'])) {
            	$centerNode = '
htw.sii.survista.graph.centerUri = \'' . $userUris[0] . '\';
htw.sii.survista.graph.centerLabel = ' . json_encode($_GET['ul']) . ';
';
                
                $myCenterNode = '
parent.window.survistaCenterUri = ' . json_encode($userUris[0]) . ';
parent.window.survistaCenterLabel = ' . json_encode($_GET['ul']) . ';
';
            }
            
            
            $result = $userUris;
        } else {
        	$tryToReloadData = true;
        }
        
        ?>
        <div id="rdfgraph"></div>
        <div id="txtLang" style="visibility:hidden;"><?PHP echo $store->lang; ?></div>
        <div id="rodinSegment" style="visibility:hidden;"><?PHP echo $rodinsegment; ?></div>
        <pre style="width: 48%; max-width: 48%; float: right;"><?php
        
//      $filename="/Users/vicho/Desktop/debugging.txt";
//		$h=fopen($filename,"a");
//		fwrite($h,"survista/vis.inc.php ::: created the div txtLang : $store->lang\n");
//		fwrite($h,"rodin.inc.php ::: count(result) : " . count($result) . "\n");
//		fclose($h);
        
        if ($tryToReloadData) {
        	echo '
<script type="text/javascript">
	window.onload = function() {
    	document.getElementById(\'rdfgraph\').style.display = \'block\';

    	if (parent.window.survistaCenterLabel != "") {
    		htw.sii.survista.graph.centerUri = parent.window.survistaCenterUri;
			htw.sii.survista.graph.centerLabel = parent.window.survistaCenterLabel;
        }
    	
    	htw.sii.survista.graph.init(\'rdfgraph\');
	};
</script>';
        } else if (count($result) > 0) {

            // check for cache
            $cacheContent = getCacheContent($result, $store->lang);
//            error_log('vis cache hit? ' . ($cacheContent !== false));

            // graph of all results
            foreach ($result as $uri) {
                // textual representation
                //$store->printGraph($uri, $allRelations);
                //echo PHP_EOL;

                // graph
                if ($cacheContent === false) {
                    if (@ $_GET['d'] == '2') {
                        $maker->prepare($uri, 2);
                    } else {
                        // depth 1
                        $maker->prepare($uri);
                    }
                }
            }

            if ($cacheContent === false) {
                $maker->createGraph();
                $data = $maker->toJson();
                setCacheContent($result, $store->lang, $data);
            } else {
                $data = $cacheContent;
            }

            echo '
<script type="text/javascript">
	parent.window.survistaData = ' . $data . ';
	' . $myCenterNode . '
	
	window.onload = function() {
	    document.getElementById(\'rdfgraph\').style.display = \'block\';
	    ' . $centerNode . '
	    htw.sii.survista.graph.init(\'rdfgraph\');
	    
	    $(document).bind("contextmenu",function(e){
              return false;
       	});
	};
</script>';
        }
        
        ?></pre>
    </body>
</html>