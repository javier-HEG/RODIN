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

<title>Fabio's skos_query_solr </title> 
</head>
<body>

<?php

include("../sroot.php");
include_once("$PATH2app/u/SOLRinterface/solr_interface.php");



$thislink	=$_SERVER['PHP_SELF'];
$term			=$_REQUEST['term'];
$desc			=$_REQUEST['desc'];
$L				=$_REQUEST['L'];
if (!$L) $L='de';
$LANGUAGES=explode(',',$L);



if (!$term) $term="Wachstumsbranche";
//if (!$desc) $desc="id:stw_thsys/70324";


if ($desc)
{
  list($noofres,$document) = get_resource_SOLR('id',trim($desc));
//  print "ECCOLO:";
//  var_dump($document);
}
  
else if ($term)  
list($noofres,$document) = get_stw_node_SOLR($term);



print "<h2> Fabio's STW SKOS-Navigator</h2>";
print "Geladen:";
//print "<br><a href='$SKOS_DBPEDIA_FILE' title='File einsehen im anderen Tab' target='_blank'>DBPEDIA STW</a>";
print "<br><a href='$SKOS_ZBW_FILE' title='File einsehen im anderen Tab' target='_blank'>SCHWEIZERISCHE NATIONALBANK STW (Standard Thesaurus Wirtschaft)</a><br><br>";

$SUBMITONENTER=" onKeyPress=\"return submitenter(this,event)\"";

print <<<EOP
<form name='f'>
	<b><i>Dein Term: </i></b><input type="text" name='term' value='$term' title='Gib ein oder mehrer W�rter und bestaetige mit ENTER' $SUBMITONENTER>
	&nbsp;<b><i>die Sprache(n): </i></b><input type="text" size="4" name='L' value='$L' title='Gib eine - en - oder mehrere Sprachbezeichnungen - en,de - ein' $SUBMITONENTER>  	
	<br><b><i>Deskriptor: </i></b><input size="50" type="text" name='desc' value='$desc' title='Gib einen Deskriptor ein und bestaetige mit ENTER' $SUBMITONENTER>
	
</form>
EOP;


if (is_array($document))
{
  print "<br>$noofres Fetched resources for descriptor $desc / Searchterm '$term':<br><br><table>";
  foreach($document as $pair)
  {
    $FONT=$FONTEND='';
    list($p,$o)=$pair;
    if (strstr($p,'abel'))
    {
      $FONT="<font style='color:green'>";
      $FONTEND="</font>";
    }    
    if ($p=='id')
      print "<tr height='20'/>";
    print "<tr><td>$p</td><td> <b>$FONT".make_browse_link($o)."$FONTEND</b></td></tr>";
  }
  print "</table>";

} 










function get_resource_SOLR($field,$desc)
#############################
# 
# returns the resource from the descritpor
#
{
	#USE SOLR COLLECTION 'src_zbw_stw':
  $solr_collection='zbw_stw';
  if (($SOLRCLIENT = init_SOLRCLIENT($solr_collection,'solr_index_skos_namespaces system error init SOLRCLIENT')))
  {
    // get a select query instance
    $query = $SOLRCLIENT->createSelect();
    //$query->setQuery(("id%3A$desc"));
    $query->createFilterQuery($field)->setQuery($desc);
    
    // set start and rows param (comparable to SQL limit) using fluent interface
    $query->setStart(0)->setRows(500); // one should be enough

    // set fields to fetch (this overrides the default setting 'all fields')
    // $query->setFields(array('id','name','price'));

    // sort the results by price ascending
    //$query->addSort('price', Solarium_Query_Select::SORT_ASC);

    // this executes the query and returns the result
    $resultset = $SOLRCLIENT->select($query);
    $noofresults=$resultset->getNumFound();
    if ($noofresults > 1) print "Descriptor Query returns more than one element ($noofresults)";
    //var_dump($resultset);
    // display the total number of documents found by solr
    $returndocument=array();
    // show documents using the resultset iterator
    foreach ($resultset as $document) 
    {
      // the documents are also iterable, to get all fields
        foreach($document AS $field => $value)
        {
            // this converts multivalue fields to a comma-separated string
            if(is_array($value)) $value = implode(', ', $value);
            $returndocument[]=array($field,$value);
        }
    }
    
  }
	return $returndocument;
}






function get_stw_node_SOLR($term)
{
  #USE SOLR COLLECTION 'src_zbw_stw':
  $solr_collection='zbw_stw';
  if (($SOLRCLIENT = init_SOLRCLIENT($solr_collection,'solr_index_skos_namespaces system error init SOLRCLIENT')))
  {
    // get a select query instance
    $query = $SOLRCLIENT->createSelect();
    $query->setQuery(($term));
//    $query->createFilterQuery('id')->setQuery($desc);
    
    // set start and rows param (comparable to SQL limit) using fluent interface
    $query->setStart(0)->setRows(500); // one should be enaugh

    // set fields to fetch (this overrides the default setting 'all fields')
    // $query->setFields(array('id','name','price'));

    // sort the results by price ascending
    //$query->addSort('price', Solarium_Query_Select::SORT_ASC);

    // this executes the query and returns the result
    $resultset = $SOLRCLIENT->select($query);
    $noofresults=$resultset->getNumFound();
    print "$noofresults Results found";
    //var_dump($resultset);
    // display the total number of documents found by solr
    $returndocument=array();
    // show documents using the resultset iterator
    foreach ($resultset as $document) 
    {
      // the documents are also iterable, to get all fields
        foreach($document AS $field => $value)
        {
            // this converts multivalue fields to a comma-separated string
            if(is_array($value)) $value = implode(', ', $value);
            $returndocument[]=array($field,$value);
        }
    }
    
  }
	return array($noofresults,$returndocument);
}





function make_browse_link($term)
################################
{
	global $thislink, $L;

  if (strstr($term," "))
  {
    $termarr=explode(',',$term);
    
    foreach($termarr as $t)
    {
      $sl_t = makesimple_link($t);
      $browsable_term.=$browsable_term?' ':'';
      $browsable_term.=$sl_t;
    } 
  }
  else $browsable_term=makesimple_link($term);
  
	
	return $browsable_term;
}


function makesimple_link($term)
{
  $LINK=<<<EOL
	<a href="#" onClick="window.open('$thislink?term=$term&L='+f.L.value,'_self')" title="Klick um im aktuellen SKOS repository nach '$term' zu browsen... ">$term</a>
EOL;
	return $LINK;
}




?>
</body>
</html>