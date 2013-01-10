<html>
<head>
  
  <link rel="stylesheet" type="text/css" href="../../../app/css/rodin.css" />
  
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

<title>SKOS/SKOSXL-Navigator</title> 
</head>
<body>

<?php

//siehe http://arc.semsol.org/docs/v2/getting_started

include("../sroot.php");
//print "qua: ".getcwd();
include_once("../../gen/u/arc/ARC2.php");

include_once("../../../app/u/FRIutilities.php");
include_once("FRIutilities.php");
include_once("SOLR_SKOS_partial_evaluator/SKOS_SOLR_partial_evaluator_resources.php");


// SKOSXL $desc='http://lod.gesis.org/thesoz/concept/10036765'
$storenames= array('gesis_thesoz', 'zbw_stw', 'bnf_rameau', 'loc_sh');
$storenames_str=implode(', ',$storenames);


$limit      = $_GET['limit']; if (!$limit) $limit=0;
$storename  = $_GET['storename']; 
$thislink   = $_SERVER['PHP_SELF'];
$term       = $_REQUEST['term'];
$desc       = $_REQUEST['desc'];
$L          = $_REQUEST['L'];
$KOSTYPE='?';


if ($storename=='')
 {
  print "Your have to provide a name for your store ... ;-)";
 }
 

$DESC_DEFAULT_RAMEAU    ="http://data.bnf.fr/ark:/12148/cb11943532d";
$DESC_DEFAULT_STW       ="http://zbw.eu/stw/descriptor/11684-3";
$DESC_DEFAULT_SOZ       ="http://lod.gesis.org/thesoz/concept/10036770";
$DESC_DEFAULT_LOC       ="http://id.loc.gov/authorities/subjects/sh85038232";
$DESC_DEFAULT_GND    		="???";


$RAMEAU_URL_DATA_MODEL  ="http://data.bnf.fr/semanticweb";
$STW_URL_DATA_MODEL     ="http://zbw.eu/stw/versions/latest/about";
$THESOZ_URL_DATA_MODEL  ="http://www.semantic-web-journal.net/sites/default/files/swj279_2.pdf";
$LOC_URL_DATA_MODEL     ="http://id.loc.gov/descriptions/";
$GND_URL_DATA_MODEL     ="http://www.dnb.de/EN/Service/DigitaleDienste/LinkedData/linkeddata_node.html";



$ALLTRIPLES_URL="$WEBROOT$RODINROOT/$RODINSEGMENT/fsrc/app/u/store_show_all_triples.php";
$STORE_TRIPLES_RAMEAU    ="$ALLTRIPLES_URL?limit=1000&storename=bnf_rameau";
$STORE_TRIPLES_STW       ="$ALLTRIPLES_URL?limit=1000&storename=zbw_stw";
$STORE_TRIPLES_SOZ       ="$ALLTRIPLES_URL?limit=1000&storename=gesis_thesoz";
$STORE_TRIPLES_LOC       ="$ALLTRIPLES_URL?limit=1000&storename=loc_sh";
$STORE_TRIPLES_GND    ="$ALLTRIPLES_URL?limit=1000&storename=dnb_gnd";




$namespaces=get_namespaces_from_DB();


  switch ($storename)
  {
  	
		
    case 'dnb_gnd': 
          if (!$L) $L='de';
          $KOSTYPE='SKOS'; 
          //$namespaces=$bnf_rameau_namespaces;
          if (!$term && !$desc) $desc=$DESC_DEFAULT_GND; 
          $DEFAULT_MODEL_URL = $GND_URL_DATA_MODEL;
		  		$DEFAULT_STORE_TRIPLES = $STORE_TRIPLES_GND;
          break;
		
    case 'bnf_rameau': 
          if (!$L) $L='fr';
          $KOSTYPE='SKOS'; 
          //$namespaces=$bnf_rameau_namespaces;
          if (!$term && !$desc) $desc=$DESC_DEFAULT_RAMEAU; 
          $DEFAULT_MODEL_URL = $RAMEAU_URL_DATA_MODEL;
		  		$DEFAULT_STORE_TRIPLES = $STORE_TRIPLES_RAMEAU;
          break;
    case 'zbw_stw': 
          if (!$L) $L='de';
          $KOSTYPE='SKOS'; 
          //$namespaces=$zbw_stw_namespaces;
          if (!$term && !$desc) $desc=$DESC_DEFAULT_STW; 
          $DEFAULT_MODEL_URL = $STW_URL_DATA_MODEL;
		  		$DEFAULT_STORE_TRIPLES = $STORE_TRIPLES_STW;
          break;
    case 'gesis_thesoz': 
          if (!$L) $L='de';
          $KOSTYPE='SKOSXL'; 
          //$namespaces=$gesis_thesoz_namespaces;
          if (!$term && !$desc) $desc=$DESC_DEFAULT_SOZ; 
		  $DEFAULT_STORE_TRIPLES = $STORE_TRIPLES_SOZ;
          $DEFAULT_MODEL_URL = $THESOZ_URL_DATA_MODEL;
          break;
    case 'loc_sh': 
          if (!$L) $L='en';
          $KOSTYPE='SKOS'; 
 		  		$DEFAULT_MODEL_URL = $LOC_URL_DATA_MODEL;
 		  		$DEFAULT_STORE_TRIPLES = $STORE_TRIPLES_LOC;
          //$namespaces=$loc_sh_namespaces;
          if (!$term && !$desc) $desc=$DESC_DEFAULT_LOC; 
          break;
  }

$LANGUAGES=explode(',',$L);
$LOCALCONFIG=$ARCCONFIG;
$LOCALCONFIG{'store_name'}=$storename;
$store = ARC2::getStore($LOCALCONFIG);


if ($desc<>'')
{
    $p_o_resources[] = $KOSTYPE=='SKOS'
          ?get_skos_resource(trim($desc),$store)
          :get_skosxl_resource(trim($desc),$store);
  
}
  
else if ($term<>'')  
{
  
  $desc_list=$KOSTYPE=='SKOS'
            ?get_skos_desc_list(trim($term),$store)
            :get_skosxl_desc_list(trim($term),$store);
  
  if ($KOSTYPE=='SKOS')
  {
    foreach($desc_list as $desc)
    {  
       $p_o_resources[] = get_skos_resource(trim($desc),$store);
    }
  }
  else 
  if ($KOSTYPE=='SKOSXL')
  {
  	if (count($desc_list))
    foreach($desc_list as $desc)
    {  
       $p_o_resources[] = get_skosxl_resource(trim($desc),$store);
    }
  }
  
}


print "<h2> SKOS-Navigator in triple store '$storename' ($KOSTYPE)</h2>";

$SUBMITONENTER=" onKeyPress=\"return submitenter(this,event)\"";
$onchange=" 
  var L = document.getElementById(\"L\");
  var term = document.getElementById(\"term\");
  var desc = document.getElementById(\"desc\");
  var source = document.getElementById(\"source\");
  var triples = document.getElementById(\"triples\");
    if (this.value==\"bnf_rameau\")
  {
    L.value=\"fr\";
    term.value=\"\";
    desc.value=\"$DESC_DEFAULT_RAMEAU\";
    source.href=\"$RAMEAU_URL_DATA_MODEL\";
    source.innerHTML=source.href;
	triples.href=\"$STORE_TRIPLES_RAMEAU\";
  }
  if (this.value==\"zbw_stw\")
  {
    L.value=\"de\";
    term.value=\"\";
    desc.value=\"$DESC_DEFAULT_STW\";
    source.href=\"$STW_URL_DATA_MODEL\";
    source.innerHTML=source.href;
    triples.href=\"$STORE_TRIPLES_STW\";
  }
  if (this.value==\"gesis_thesoz\")
  {
    L.value=\"de\";
    term.value=\"\";
    desc.value=\"$DESC_DEFAULT_SOZ\";
    source.href=\"$THESOZ_URL_DATA_MODEL\";    
    source.innerHTML=source.href;
    triples.href=\"$STORE_TRIPLES_SOZ\";
  }
  if (this.value==\"loc_sh\")
  {
    L.value=\"en\";
    term.value=\"\";
    desc.value=\"$DESC_DEFAULT_LOC\";
    source.href=\"$LOC_URL_DATA_MODEL\";    
    source.innerHTML=source.href;
    triples.href=\"$STORE_TRIPLES_LOC\";
  }
";
$STORENAMES_OPTIONS=make_selection_control($storenames,'storename',$storename,$onchange);

print <<<EOP
<form name='f'>
	<b><i>Your Term: </i></b><input type="text" id='term' name='term' value='$term' title='Enter one or more words and press ENTER' $SUBMITONENTER>
	&nbsp;<b><i>the language(s): </i></b><input type="text" size="4" id= 'L' name='L' value='$L' title='Enter one or more language acronyms - en,de' $SUBMITONENTER> 
    $STORENAMES_OPTIONS
	<br><b><i>Descriptor: </i></b><input size="56" type="text" id='desc' name='desc' value='$desc' title='Enter a descriptor and press ENTER' $SUBMITONENTER>
  <br><input type='submit' value='(-: Find :-)' style="width:465px;">
  <br><a id='source' href='$DEFAULT_MODEL_URL' title='Click to open data model descritpion in new tab' target='_blank'>$DEFAULT_MODEL_URL</a> 
  &nbsp; 
  &nbsp; 
  <a id='triples' href='$DEFAULT_STORE_TRIPLES' title='Click to explore a limited number of triples inside the STORE in new tab' target='_blank'>SEE TRIPLES IN STORE</a>
  <br>
</form>
EOP;
//error_reporting(E_ALL);


if (count($p_o_resources))
{
  foreach($p_o_resources as $p_o_resource)
  {
    $desc=prettyprintURI($desc,$namespaces);
    print "Triples under <b>$KOSTYPE descriptor $desc</b>:<br><br><table>";
    $x=$o='';

    if (count($p_o_resource))
    foreach($p_o_resource as $arr)
    {

      //print "<hr>"; var_dump($arr);
      if ($KOSTYPE=='SKOS')
      {    
        list( $p, $o, $o_lang
           )=$arr;
        $p=prettyprintURI($p,$namespaces);
        $o=prettyprintURI($o,$namespaces);
      }
      else 
      if ($KOSTYPE=='SKOSXL')
      {
        list( $p,$p_lang,
              $o_tmp,$o_tmp_lang,
              $x, $x_lang,
              $o, $o_lang,
              $y, $y_lang, 
              $v, $v_lang
           )=$arr;
        $p=prettyprintURI($p,$namespaces);
        $o_tmp=prettyprintURI($o_tmp,$namespaces);
        $x=prettyprintURI($x,$namespaces);
        $o=prettyprintURI($o,$namespaces);
        $y=prettyprintURI($y,$namespaces);
        $v=prettyprintURI($v,$namespaces);
      }

      $e_p_lang=$e_o_tmp_lang=$e_x_lang=$e_o_lang=$e_y_lang=$e_v_lang='';
      if ($p_lang) $e_p_lang="@$p_lang";
      if ($o_tmp_lang) $e_o_tmp_lang="@$o_tmp_lang";
      if ($x_lang) $e_x_lang="@$x_lang";
      if ($o_lang) $e_o_lang="@$o_lang";
      if ($y_lang) $e_y_lang="@$y_lang";
      if ($v_lang) $e_v_lang="@$v_lang";


      $OPT="nowrap='nowrap'";
      print "<tr>
              <td $OPT>$p$p_lang</td>
              <td $OPT> <b>$o_tmp</b>$e_o_tmp_lang</td>
              <td $OPT> <b>$x</b>$e_x_lang</td>
              <td $OPT> <b>$o</b>$e_o_lang</td>
              <td $OPT> <b>$y</b>$e_y_lang</td>
              <td $OPT> <b>$v</b>$e_v_lang</td>
            </tr>";
    } // $p_o_resource
    print "</table>";
  } // $p_o_resources
  
} // $p_o_resources





/*
 * get 
 * prefLabel, 
 * altLabel, 
 * hiddenLabel?, 
 * related, 
 * broader, 
 * narrower, 
 * skos:relatedMatch -> link to other
 * skos:broadMatch 
 */
function get_resource_skosxl($desc,$store)
{
  $p_o_arr = get_resource_skosxl_core($desc,$store);
  
  return $p_o_arr;
}








function make_selection_control($values,$opname,$selectedvalue,$onchangeaction)
{
  $OP="<select name='$opname' onchange='$onchangeaction' title='Please select one $opname'>";
  
  foreach($values as $value)
  {
    $SELECTED= ($value==$selectedvalue)? ' selected ':'';
    $OP.="<option $SELECTED>$value</option>";
  }  
  
  $OP.="</select>";
  return $OP;
}




?>
</body>
</html>