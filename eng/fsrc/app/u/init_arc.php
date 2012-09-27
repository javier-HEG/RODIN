<?php

//siehe http://arc.semsol.org/docs/v2/getting_started
include("../sroot.php");

$PATH2U="../../gen/u";
include_once("$PATH2U/arc/ARC2.php");




//ein paar errors ausgeben

/* LOAD will call the Web reader, which will call the
format detector, which in turn triggers the inclusion of an
appropriate parser, etc. until the triples end up in the store. */


$obj1="$PATH2U/data/SKOS/stw.rdf";
$obj2="$PATH2U/data/SKOS/dbpedia_stw.rdf";

$OBJECTS=array('zbw'=>$obj1,'zbwdbpedia'=>$obj2);
$OBJECTS=array('zbw'=>$obj1);
//$OBJECTS=array('zbwdbpedia'=>$obj2);
//$OBJECTS=array('zbw'=>$obj1);

foreach ($OBJECTS as $storename=>$obj)
{
	if ($storename)
	{
		
		$LOCALCONFIG=$ARCCONFIG;
		$LOCALCONFIG{'store_name'}=$storename;
		
		$store = ARC2::getStore($LOCALCONFIG);
		if (!$store->isSetUp()) {
		  $store->setUp();
		}
		print "<br><br>Loading $obj in store $storename <br>";
		$rs= $store->query("LOAD <$obj>");
		if ($errs = $store->getErrors()) {
		 
			foreach($errs as $err)
			print $err.' ';
	
		
		}
		$duration = $rs['query_time'];
		$added_triples = $rs['result']['t_count'];
		$load_time = $rs['result']['load_time'];
		$index_update_time = $rs['result']['index_update_time'];
		
		print "<hr>
					duration: $duration
			<br>added_triples: $added_triples
			<br>load_time: $load_time
			<br>index_update_time: $index_update_time
			<br>";
	} // $storename
}


?>