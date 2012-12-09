<html>
<head>
<SCRIPT TYPE="text/javascript">
<!--
function submitenter(myfield,e)
{
var keycode;
if (window.event) keycode = window.event.keyCode;
else if (e) keycode = e.which;
else return true;

if (keycode == 13)
   {
   myfield.form.submit();
   return false;
   }
else
   return true;
}
//-->
</SCRIPT>

<title>Fabio's SKOS-Navigator</title> 
</head>
<body>

<?php

//siehe http://arc.semsol.org/docs/v2/getting_started

include("../sroot.php");
$PATH2U="../../gen/u";
include_once("$PATH2U/arc/ARC2.php");



print "<h2> Fabio's SKOS-Navigator</h2>";
print "Geladen:";
//print "<br><a href='$SKOS_DBPEDIA_FILE' title='File einsehen im anderen Tab' target='_blank'>DBPEDIA STW</a>";
print "<br><a href='$SKOS_ZBW_FILE' title='File einsehen im anderen Tab' target='_blank'>SCHWEIZERISCHE NATIONALBANK STW (Standard Thesaurus Wirtschaft)</a><br><br>";

$SUBMITONENTER=" onKeyPress=\"return submitenter(this,event)\"";

print <<<EOP
<form name='f'>
	<b><i>Dein Term: </i></b><input type="text" name='term' value='$term' title='Gib ein oder mehrer W�rter und bestaetige mit ENTER' $SUBMITONENTER>
	&nbsp;<b><i>die Sprache(n): </i></b><input type="text" size="4" name='L' value='$L' title='Gib eine - en - oder mehrere Sprachbezeichnungen - en,de - ein' $SUBMITONENTER>  	
	
	
</form>
EOP;



//exec_sparql_explore('dbpediastw');
exec_sparql_explore('zbw');






function exec_sparql_explore($storename)
##################################
# 
# Computes the query to SKOS $verb
# $verb= related, broader, narrower
{
	global $ARCCONFIG;
	$LOCALARCCONFIG = $ARCCONFIG;
	$LOCALARCCONFIG{'store_name'} = $storename;
	
	var_dump($LOCALARCCONFIG);
	
	$store = ARC2::getStore($LOCALARCCONFIG);
	if (!$store->isSetUp()) {
	  $store->setUp();
	}
	
	$QUERY=<<<EOQ
	select *  
	where
		 {
			?s 	?p  ?o.
		 } 
EOQ;

	
	print "<br>QUERY: <br>$QUERY<br><br>";
	
	if ($rows = $store->query($QUERY, 'rows')) 
	{
		$i=0;
		print "<table border=1>";
		foreach($rows as $row) 	
		{	$i++; 
    
    if ($i>200) break;
    
    print "<hr>";
    var_dump($row);
    
			print "<tr>";
			print "<td>$i";
			print "</td>";
			print "<td nowrap>";
			print $row['s'];
			print "</td>";
			print "<td nowrap>";
			print $row['p'];
			print "</td>";
			print "<td nowrap>";
			print $row['o'];
      if ($row["o lang"])
        print " (".$row["o lang"].")";
			print "</td>";
			print "</tr>";
		}
		print "<table>";
	}
	else print "?";
}



function make_browse_link($term)
################################
{
	global $thislink, $L;

	$LINK=<<<EOL
	<a href="#" onClick="window.open('$thislink?term=$term&L='+f.L.value,'_self')" title="Klick um im aktuellen SKOS repository nach '$term' zu browsen... ">$term</a>
EOL;
	return $LINK;
}





?>
</body>
</html>