<?php
include_once("stopwords.php");


$filename="app/root.php";
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	{if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}}

$filename="gen/u/simplehtmldom/simple_html_dom.php";
#######################################
$max=10;
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
{ ;
	if (file_exists("$updir$filename")) 
	{ include_once("$updir$filename");break;}
}
#######################################

//include RODIN utilities
$filename="$RODINSEGMENT/app/u/FRIutilities.php";
#######################################
$max=10;
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
{ ;
	if (file_exists("$updir$filename")) 
	{ include_once("$updir$filename");break;}
}
#######################################



//print "<br>RODINSEGMENT: $RODINSEGMENT "//;

$filenamex="app/sroot.php";
#######################################
$max=10;
//print "<br>FRIutilities: try to require $filenamex at cwd=".getcwd()."<br>";
for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
{ 
	//print "<br>try to require $updir$filenamex";
	if (file_exists("$updir$filenamex")) 
	{
		//print "<br>REQUIRE $updir$filenamex";
		require_once("$updir$filenamex"); break;
	}
}

//if ($ROOT) print "<br>root geladen";
//else print "<br>NO ROOT?<br>";
#######################################

$FONTRED = "<font style=\"color:red;\">";

switch ($RODINSEGMENT)
{
	case('eng')	:	$COLOR_RODINSEGMENT="#006600";break;
	case('st')	:	$COLOR_RODINSEGMENT="#660000";break;
	case('p')		:	$COLOR_RODINSEGMENT="#000066";break;
	case('d')		:	$COLOR_RODINSEGMENT="#336699";break;
	case('x')		:	$COLOR_RODINSEGMENT="#DD6600";break;
	default			: $COLOR_RODINSEGMENT="#000000";
}

$COLOR_ORANGE	="#FF6600";
$COLOR_GRAY		="#aaaaaa";


$SONTWHITE="<font style=\"color:#eeeeee;\">";
$STYLEFONTRESULT=" style=\"color:black;font-size:normal\" ";
$FONTRESULT="<font $STYLEFONTRESULT>";
$FONTRESULTURL="<font style=\"color:blue;font-size:normal;font-weight:bold\">";
$STYLEFONTLABEL =" style=\"color:grey;font-size:normal;font-weight:bold\" ";
$STYLEFONTGREY =" style=\"color:#bbbbbb;font-size:normal;font-weight:bold\" ";
$FONTLABEL ="<font $STYLEFONTLABEL>";
$FONTGREY ="<font $STYLEFONTGREY>";
$ENDFONTLABEL="</font>";
$ENDFONTRESULT="</font>";
$ENDFONTRESULTURL=$ENDFONTRESULT;
$fontVERSION = "<font style=\"font-size:x-small;\">";

function cleanup_4url($tokens)
{
	$tokens = str_replace("  ",' ',trim($tokens));
	$tokens = str_replace(" ",'+',trim($tokens));
	return $tokens;
}


function dirtydown_viki_tokens($tokens)
{
	$tokens=trim($tokens);
	$tokens = str_replace(' ','_',$tokens);
	return $tokens;
}
	


/*
 * inject a value for TAG inside XMLDOC
 * and returs the changed XDMDOC
 * It works on a text basis.
 */
function xml_inject($XMLDOC, $TAG, $VALUE)
{
  $PATTERN="/<$TAG>(.*)<\/$TAG>/";
  $SUBSTITUTION="<$TAG>$VALUE</$TAG>";
  $XMLDOC= preg_replace($PATTERN,$SUBSTITUTION,$XMLDOC);
  return $XMLDOC;
}


function xml_extract($XMLDOC,$TAG)
{
  $PATTERN="/<$TAG>(.*)<\/$TAG>/";
     if (preg_match($PATTERN,$XMLDOC,$match))
     {
       $VALUE = $match[1];
     }
   return $VALUE;
}







function collect_pipe_elements($pipeTERMS_xml) 
##############################################
{
	if ($pipeTERMS_xml)
	{
		$namespaces = $pipeTERMS_xml->getDocNamespaces(true);
		$namespaces[]=''; // even the empty space (for fulltext items)
		
		foreach($pipeTERMS_xml->children($namespaces{'sparql'}) as $obj=>$val)
		{
			//print "<br>CHILD: (((".$obj."=>".$val.")))";
			$arr=array();
			
			if ($obj=='results')
			{	//print "<br>Results:";
				foreach($val->children() as $o=>$v)
				{
					//print "<br>CHILD: (((".$o."=>".$v.")))";
					if ($o=='result')
					{	//print "<br>Result:";
						foreach($v->children() as $or=>$vr)
						{
							if ($or=='binding')	
							{
								//print "<br>CHILD: (((".$or."=>".$vr->uri.")))";
								$arr[]=	$vr->uri;		
							}
						}
					}
				}
			}
		}
	}	
	
	return $arr;
}





function print_endpoint_results($endpointResults)
{
	global $DBPEDIA_BASE;
	if (count($endpointResults)) 
	{
		$COLUMN_NAMES = $endpointResults['column_names'];
		if (count($endpointResults['results']))
		{
			$RESULTS = array_reverse($endpointResults['results']);
			
			$start=1;
			print "<table border='1'>";
			foreach($RESULTS as $zeile)
			{
				if ($start)
				{
					$start=0;
					print "<tr>";
					foreach($COLUMN_NAMES as $name)
						print "<td align='middle'><i><b>$name</b></i></td>";
					print "</tr>";
				}
				
				if (1)
				{
				print "<tr>";
				foreach($zeile as $term=>$word)
				{
					$href_dbpedia_category="<a href=\"$DBPEDIA_BASE/page/Category:".($word)."\" title='As DBPedia category' target=_blank>C</a>";	
					$href_dbpedia_normal="<a href=\"$DBPEDIA_BASE/page/".($word)."\" title='As DBPedia resource' target=_blank>R</a>";	
					$val= "<br><i><b>'$word'</b></i>: $href_dbpedia_category/$href_dbpedia_normal:<br>";
					
					print "<td nowrap>$val</td>";
				}
				print "</tr>";
			
				}
			}
			print "</table>";
		}
		else print "NO RESULTS TO PRINT";
	}
	else print "NO endpointResults TO PRINT";
}




/**
 * Gets the result of the SPARQL query as a PHP vector.
 */
function get_dbpediaendpoint_results($query, $SubtractFromValue=null) {
	global $DBPEDIA_SPARQL_ENDPOINT, $DBPEDIATIMEOUT_MSEC, $FSRC_CURL_TIMEOUT_SEC;
	global $SRCDEBUG, $VERBOSE;
  $RESULT=NULL;

	// could still add 'should-sponge' parameter
	$parameters = array('default-graph-uri' => 'http://dbpedia.org', 'debug' => 'on',
		'query' => urldecode($query), 'format' => 'rdf/xml', 'timeout' => $DBPEDIATIMEOUT_MSEC);
	// needs '&amp;' to be replaced with '&' to work
	$parametersString = str_replace(
			array('+', '%7E', '%2F', '&amp;'), 
			array('%20', '~', '/', '&'),
			http_build_query($parameters));
	
	$url = $DBPEDIA_SPARQL_ENDPOINT . '?' . $parametersString;
	
	if ($SRCDEBUG) print '<p>SPARQL query URL : ' . $url . '</p>';
	
	$options = array(CURLOPT_HTTPHEADER => array('Accept:application/sparql-results+xml'));
	$SparqlEndpointXmlResults = parametrizable_curl($url, array(), $options);
		
	if ($SparqlEndpointXmlResults) {
		
		if ($SRCDEBUG) print "<br>SparqlEndpointXmlResults: ($SparqlEndpointXmlResults)";
		
		if (!strstr($SparqlEndpointXmlResults,"syntax error")
		  && !strstr($SparqlEndpointXmlResults,"Could not resolve host"))
		{
			$endpoint_sxml_result = simplexml_load_string($SparqlEndpointXmlResults);
			
			if ($endpoint_sxml_result!=null)
			{
				$namespaces = $endpoint_sxml_result->getDocNamespaces(true);
				$namespaces[]=''; // even the empty space (for fulltext items)
		
				//GET COLUMN_NAMES (Spaltennamen)
				foreach($endpoint_sxml_result->children() as $obj=>$val)
				{
					if ($val->getName()=='head')
					{
						foreach($val->children() as $var)
						{
							$COLUMN_NAMES[]= $var->attributes(); 
						}
					} // head -> collumnames
				} // foreach
		
				// GET RESULTS:
				foreach($endpoint_sxml_result->children() as $obj=>$val)
				{
					if ($val->getName()=='results')
					{
						#print "<br>Results:<hr>";
						foreach($val->children() as $res)
						{	
							$ZEILE=array(); //clear
							foreach($res->children() as $res_binding)
							{	
								$COLUMN_Name=$res_binding->attributes();
								foreach($res_binding->children() as $uri)
								{	
									$COLUMN_Name=trim($res_binding->attributes());
									#print "<br>set $COLUMN_Name = $uri";
									if (is_array($SubtractFromValue))
									{
										
										if (preg_match("/disambiguation/",$uri))
											$uri=''; //discard;
										else
										
										foreach($SubtractFromValue as $deletethistext)
											$uri=str_replace($deletethistext,'',$uri);	
									}
									if (trim($uri)) // nur wenn was drin
										$ZEILE{$COLUMN_Name} = str_replace($SubtractFromValue,'',$uri);							
								}
							}
							$RESULT[]=$ZEILE;
							//print "<hr> verwendete ZEILE: <br>";var_dump($ZEILE);
						}
					}
				} // foreach
			} // !null
		} // no error 
		else 
		{
			if ($SRCDEBUG)
				fontprint("<br>ERROR in QUERY:<br>".show_xml_string(urldecode($query)),'red');
			$RESULT=$SparqlEndpointXmlResults;
		}
	} // get_file_content 
	else 
	{
		if ($SRCDEBUG)
			fontprint('<p>No response to parametrizable_curl() : ' . $url . '</p>', 'red');
			
		$RESULT = $SparqlEndpointXmlResults;
	}
	return array(
		'column_names'=>$COLUMN_NAMES,
		'results'=>$RESULT
	);
}












function Select_Random_Indices($source_array, $count = 1)
{
    if($count > 0)
    {
        if($count == 1)
        {
            $result = array(array_rand($source_array, $count));
        }
        else
        {
            $result = array_rand($source_array, $count);
        }
    }
    else
    {
        $result = array();
    }

    return $result;
}


function Select_Random_Entries($source_array, $count = 1)
{
    $result = array();
    $index_array = Select_Random_Indices($source_array, $count);

    foreach($index_array as $index)
    {
        $result[$index] = $source_array[$index];
    }

    return $result;
} 






function cleanup_commas($terms)
{
	$terms=trim($terms);
	$terms=str_replace(',,',  ',' ,  $terms); // delete ,,
	if (substr($terms,0,1)==',') $terms =substr($terms,0,strlen($terms));  // delete , at the beginning 
	if (substr($terms,strlen($terms) - 1,1)==',') $terms =substr($terms,0,strlen($terms) - 1);  // delete , at the end
		return $terms;
}




function cleanup_comma_in_descr_label($label)
{
	$label=str_replace(',','',$label);
	return $label;
}



function is_singleton($chunk)
###############################
{
	return count(($chunk))==1;
}



/*
 * return true iif $ele is contained in one element in vector or ele is containing one element in vector
 */ 
function subsumed_in($vector,$ele)
{
	$subsumed=false;
	$ele=strtolower(trim($ele));
	
	if (!is_array($vector)) ;
	else if (count($vector))
	{
		foreach($vector as $v)	
		{
			$v = strtolower(trim($v));
			$subsumed = strstr($v,$ele) || strstr($ele,$v);
			if ($subsumed) break;
		}
	}
	
	return $subsumed;
}


/*
 * returns an array_merge of
 * @param $vector1
 * @param $vector2
 * by checking to have unique values
 */ 
function merge_uniquely($vector1,$vector2)
{
	$vector=array();
	if (!is_array($vector1) and !is_array($vector2)) ;
	else if (is_array($vector1))
	{
		if (!is_array($vector2))
			$vector=$vector1;
		else {
			$both_vectors=true; 
		} // is_array($vector2)
	} // is_array($vector1)
	else if (is_array($vector2))
	{
		if (!is_array($vector1))
			$vector=$vector2;
		else {
			$both_vectors=true; 
		} // is_array($vector1)
	} // is_array($vector2)
	
	if ($both_vectors)
	//Check insertion
	{
		$vector=array_merge($vector1,$vector2);
		$vector=array_unique($vector);
	}
	
	return $vector;
}




function gen_sequences($terms)
# $terms = array of terms
# Out of $terms=(a b c d) returns:
# (a b c d e)
# ((a b c) (c b d))
# ((a b)(b c)(c d))
#
#
{
	global $VERBOSE;
	global $SRCDEBUG;
	
	if ($SRCDEBUG) print "<br>gen_sequences($terms)";
	$max=count($terms);
	$seqs[] = $terms;
	
	for($n=$max - 1; $n>0; $n--)
	{
		$seq = gen_sequence($terms,$max,$n); //Array of lists
		$seqs = array_merge($seqs,$seq);
	}
	
	return $seqs;
}


function gen_sequence($terms,$max,$n)
# $terms = array of terms
#
# Out of $terms=(a b c d, 4, 3)
# returns $seq= ((a b c)(b c d))
# Out of $terms=(a b c d, 4, 2)
# returns $seq= ((a b)(b c)(c d))
# etc.
{	
	global $SRCDEBUG;
	
	if ($SRCDEBUG) print "<br>gen_sequence(".implode(' ',$terms).",max=$max,n=$n)";
	for($i=0; $i<=$max - $n; $i++)
	{
		$seq[]= array_slice($terms,$i,$n);
		if ($SRCDEBUG) print "<br>$i, ".($n)." : ".implode(' ',array_slice($terms,$i,$n));
	}	
	
	if ($SRCDEBUG) print "<br>END OF gen_sequence(".implode(' ',$terms).",$max,$n)";
	
	return $seq;
}



function array_flatten($a,$f=array()){
  if(!$a||!is_array($a))return '';
  foreach($a as $k=>$v){
    if(is_array($v))$f=array_flatten($v,$f);
    else $f[$k]=$v;
  }
  return $f;
}


function trim_array($arr)
{
	if (count($arr))
	{
		foreach($arr as $x)
		{
			if (is_array($x))
			{
				$newx=array();
				foreach($x as $elem)
				{
					if (trim($elem))
						$newx[]=$elem;
				}
				$arrcleaned[]=$newx;
			}
			else
			if(trim($x))
				$arrcleaned[]=$x;
		}	
	}
	else $arrcleaned=$arr;
	return $arrcleaned;
	
}



function cleanup_array_delete($arr)
{
	if (count($arr))
	{
		foreach($arr as $x)
		{
			if (is_array($x))
			{
				$newx=array();
				foreach($x as $elem)
				{
					if (trim($elem))
						$newx[]=$elem;
				}
				$arrcleaned[]=$newx;
			}
		}	
	}
	else $arrcleaned = $arr;
	return $arrcleaned;	
}



/**
 * Returns an array of the form (cleanterm, rawterm), sorted by
 * the ramification degree of terms.
 */
function weight_and_order_refine_results($endpointResults) {
	global $SRCDEBUG, $VERBOSE;
	
	$DEGREE = array();
	
	if (count($endpointResults)) {
		if (count($endpointResults['results']))
		{
			$epresults=$endpointResults['results'];
			if (is_array($epresults) && count($epresults)>0)
				$RESULTS = array_reverse($endpointResults['results']);
			else 
				$RESULTS = array();
			$COLUMN_NAMES = $endpointResults['column_names'];
		
			foreach($RESULTS as $ZEILE)
			{
				if(count($ZEILE)==1) //hier der Term erstmal
				{
					foreach($ZEILE as $name=>$val)
					$DEGREE{$val} = 1;
				}
			}	// foreach
			
			//if ($SRCDEBUG) foreach ($DEGREE as $t=>$d) print "<br>DEGREE $t=>$d";
			//count in/out degree each term
			foreach($RESULTS as $ZEILE)
			{
				if(count($ZEILE)>1) //ZÔøΩhle je Term die Haeufigkeit
				{
					foreach($ZEILE as $name=>$val)
					{	
						if ($name==$COLUMN_NAMES[0]) //Siehe Query: in erster Spalte immer der refine term, in zweiter der indegreeterm
						{
							//if ($SRCDEBUG)  print "{$name}=>".$val." DEGREE($val)=".$DEGREE{$val} ;
							$DEGREE{$val}= $DEGREE{$val} + 1;
							//if ($SRCDEBUG)  print " -> DEGREE($val)=".$DEGREE{$val} ;
						}
					}
				}
			}	// foreach
			
			//if ($SRCDEBUG) foreach ($DEGREE as $t=>$d) print "<br>Degree term $t=>$d";
			
			//make sorted vector
			arsort($DEGREE);
			if ($VERBOSE) print "<hr><b>SORTED</b> results (the will be fused at higher level and sorted again):<br>";
			if ($VERBOSE) foreach ($DEGREE as $t=>$d) print "<br>$d: $t";
			if ($VERBOSE) print "<hr>";
			
			//truncate up to $m "best" elements
			//array_splice($DEGREE,$m);
			
		} // count
	} // count 
	return $DEGREE;
}






function wikipedia_disambiguate($term,$lang)
#############################################
# Returns vector of disambiguated terms
{		
	global $VERBOSE;
	global $WIKIPEDIASEARCH;
	$disambiguated_terms=array();
	
	$URL=$WIKIPEDIASEARCH."&search=$term&language=$lang";
	if ($VERBOSE) {
		print "<br>WIKIPEDIASEARCH=$WIKIPEDIASEARCH ...";
		print "<br>wikipedia_disambiguate($term)...";
	}
	$URL = cleanup_4url($URL);
	if ($VERBOSE) print "Disambiguating call: <a href='$URL' title='click2open in new tab' target='_blank'>$URL</a>";
	
	$options = array(
		CURLOPT_USERAGENT => 'User-Agent: RODIN (http://www.e-lib.ch/en/Offers/RODIN)',
		CURLOPT_HTTPHEADER => array('Accept:text/xml')
	);
	
	$f = parametrizable_curl($URL, array(), $options);
	
	//$f = get_file_content($URL,false);

	if ($VERBOSE) print "<br><br>ECCO COSA DA LA URL su ($term): ($URL): (((" . var_export($f, true) . ")))";
	
	if ($f)
	{
		
		$sXML= new SimpleXMLElement($f);
	
		$C=$sXML->children();
		$section=$C[1];
		$ITEMS=$section->children();
		
		foreach($ITEMS as $Item)
		$disambiguated_terms[] = str_replace(" ","_",$Item->Text);
	}
	
	/*if (!count($disambiguated_terms))
		$disambiguated_terms=array($term);*/
	if ($SRCDEBUG || $VERBOSE)
	{
		print "<br>returning: disambiguated terms ($term):";
		foreach($disambiguated_terms as $dt)
			print "<br>possibleterm: <b>$dt</b>";
	}
	return $disambiguated_terms;
}


/**
 * @param unknown_type $text
 * TODO replace with the wordProcessingTools
 */
function clean_puntuation($text) {
	//remove all characters that are not letters or numbers or underscore
	$cleanText= preg_replace('/[^\p{L}\p{N}_]+/u',' ',$text);
	//remove multiple spaces that might result from previous operation
	$cleanText= str_replace('/\s+/',' ',$cleanText);
	
	return trim($cleanText);
}




function render_skos_cleaned($rowss,$thema)
#############################
{
	global $SRCDEBUG;
	
	if (count($rowss)==0) {
		if ($SRCDEBUG)
		print "<br>KEINE Predikate gefunden zu $disambiguated_term!<br>";
	}
	else
	
	{
		$result="<table border=1 bgcolor=#bbeebb>";

		foreach($rowss as $predicate=>$rows)
		{
			$result.="<tr><td colspan=1000 bgcolor=#aaaaee>$predicate</td></tr>";

			if (is_array($rows))
			foreach($rows as $row) 
			{
				$cnt++;
				$result.="<tr>";
				foreach ($row as $value)
				{	
					$result.="<td>$value</td>";
				}
				$result.="</tr>";
			} // for rows as $row
		}
		$result.='</table>';
	}	

	
	print <<<EOP

	<hr>RESULTS $thema:
	
	$result
	
EOP;

}




function render_rowss($rowss,$thema)
#############################
{
	if (count($rowss)==0) print "<br>KEINE Predikate gefunden zu $disambiguated_term!<br>";
	else
	
	{
		$result="<table border=1 bgcolor=#bbeebb>";
		if (count($rowss))
		foreach($rowss as $predicate=>$rows)
		{
			$result.="<tr><td colspan=1000 bgcolor=#aaaaee>$predicate</td></tr>";
			if (is_array($rows))
			foreach($rows as $row) 
			{
				$cnt++;
				$result.="<tr>";
				if (is_array($row))
				foreach ($row as $name=>$value)
				{	
					if ($name=='o')
					{
						if (preg_match("/http/",$value)) 
						$value="<a href=\"$value\" target=_blank title='click to open in new tab'>$value</a>";
						$result.="<td nowrap>$name: $value</td>";		
					}
				}
				$result.="</tr>";
			} // for rows as $row
		}
		$result.='</table>';
	}	

	
	print <<<EOP

	<hr>RESULTS $thema:
	
	$result
	
EOP;

}








function extract_objects($rows)
/*
 * From rows coming from ARC2 (difining triples)
 * 
 */
{
	$objects=array();	
	if (is_array($rows) && count($rows))
	{
		foreach($rows as $row)
		{
			$value=$row['o'];	
			if (preg_match("/resource\/Category\:(.*)/",$value,$m))
			{
				$value=$m[1];
			}
			$value=urldecode($value);
			//else print " NO MATCH CATEGORY in $value";
			$objects[]=$value;	
		}
	}
	return $objects;
}




function clean_dbpedia_results($rowss,$maxChoice=5)
###################################################
#
# Pro Kategorie: MaxChoice Terme random wÔøΩhlen 
#
#
{
	if (count($rowss))
	{
		foreach($rowss as $predicate=>$rows)
		{
			if (is_array($rows))
			foreach($rows as $row) 
			{
				foreach ($row as $name=>$value)
				{	
					if ($name=='o')
					{
						$pathinfo=pathinfo($value);
						$value=$pathinfo['filename'];
						$value=str_replace('_',' ',$value);
						$value=str_replace('Category:','',$value); //falls da
						$arr[]=$value;
					}
				}
				$rowarr[]=$arr;$arr='';
			} // foreach($rows 
			//Shuffle and add the first n elements
			shuffle($rowarr);
			$maxChoice=min($maxChoice,count($rowarr)); //nur soviele
			for($i=0;$i<$maxChoice;$i++)
				$predarr{$predicate}[]=$rowarr[$i];
			$rowarr='';
		} //foreach($rowss
	}
	
	return $predarr;
} //clean_dbpedia_results






function no_suggest_words($text, $lang)
##################################
# Return this vector
{
	global $SRCDEBUG;
	
	
	$RESULT=array(
		'ok' => $ok,
		'results' => $text,
	'explanation' => 'Coming soon'
	);
	
	return $RESULT;

} // 






function group_contigous_words($text)
######################################
#
# Contigous words are quoted words
# This procedure substitutes the qoutes with _
# assuming there is a term in the ontology mything this.
# works mainly for dbpedia
#
{
	global $SRCDEBUG;
	$max_runs=strlen($text);
	$i=0;
	while ( preg_match("/\'/",$text) && $i<$max_runs) //quote found?
	{	$i++;
		if($SRCDEBUG) print "<br> group_contigous_words: hochkomma detected";
		$old_text=$text;
		$text = preg_replace("/(.*)'(.*)\s(.*)'(.*)/","$1$2_$3$4",$text); //hochkomma weg und _ rein
		if($SRCDEBUG) print "<br> $old_text ==> $text";
	}
	

	$i=0;
	while ( preg_match("/\"/",$text) && $i<$max_runs) // double quote found?
	{	$i++;
		if($SRCDEBUG) print "<br> group_contigous_words: ganzefuesschen detected";
		$old_text=$text;
		$text = preg_replace("/(.*)\"(.*)\s(.*)\"(.*)/","$1$2_$3$4",$text); //hochkomma weg und _ rein
		if($SRCDEBUG) print "<br> $old_text ==> $text";
	}
	
			
	return $text;
}



function filter_doubles_str($words_str)
{
	global $SRCDEBUG;
	if($SRCDEBUG) print "<br> filter_doubles_str: ($words_str)";
	
	$words= explode(' ',$words_str);
	$single_words=array();
	foreach($words as $word)
	{
		if (!in_array($word,$single_words))	
			$single_words[]=$word;
	}	

	$single_words_str = implode(' ',$single_words);
	if($SRCDEBUG) print "<br> filter_doubles_str returning: ($single_words_str)";
	
	return $single_words_str;
}





function JAVASCRIPT_ENTER_CODE()
{
return <<<EOR
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
EOR;
}


function cleanup_ZBW($oldtxt)
{
	global $SRCDEBUG;
	
	if ($SRCDEBUG)
	{
		print "<br>cleanup_ZBW: $oldtxt --> ";
	}
	$txt=$oldtxt;
	//Rausfiltern von kryptischen Informationen
	//wie V.11.22.33 blabla	
	$PATTERNS[]="/^(\w)\.(\d+)\.(\d+)\.(\d+)\.(\d+)\b(.*)/";
	$PATTERNS[]="/^(\w)\.(\d+)\.(\d+)\.(\d+)\b(.*)/";
	$PATTERNS[]="/^(\w)\.(\d+)\.(\d+)\b(.*)/";
	$PATTERNS[]="/^(\w)\.(\d+)\b(.*)/";
	$SUBSTITUTIONS[]="$6";
	$SUBSTITUTIONS[]="$5";
	$SUBSTITUTIONS[]="$4";
	$SUBSTITUTIONS[]="$3";


	$txt=preg_replace($PATTERNS,$SUBSTITUTIONS,$txt);
	$txt=str_replace(","," ",$txt);
	
	$strarr=strsplt($txt);
	//REPLACE EACH UMLAUT
	foreach($strarr as $char)
	{
		if ($SRCDEBUG) print "<br>$char = ".ord($char)." chr: ".chr(ord($char));
		//replace ä with ae
		switch (ord($char))
		{
			case 228: // ä ==> ae
				$newtxt.='ae';
				break;
				
			case 196: // Ä ==> ae
				$newtxt.='Ae';
				break;
				
				
			case 246: // ö ==> oe
				$newtxt.='oe';
				break;
	
			case 214: // Ö ==> oe
				$newtxt.='Oe';
				break;
				
			case 220: // Ü ==> ue
				$newtxt.='Ue';
				break;
					
			case 252: // ü ==> ue
				$newtxt.='ue';
				break;
				
			case 223: // Schafes S ==> ss
				$newtxt.='ss';
				break;
					
		default: 
				$newtxt.=$char;
		}
	}
	$txt=$newtxt;
	
	if ($SRCDEBUG)
	{
		print $txt;
	}
	
	return 	trim($txt);
}




function ZBW_dirtydown($terms)
{
	
	$terms=str_replace('Ae',chr((196)),$terms);
	$terms=str_replace('ae',chr((228)),$terms);
	$terms=str_replace('Oe',chr((214)),$terms);
	$terms=str_replace('oe',chr((246)),$terms);
	$terms=str_replace('Ue',chr((220)),$terms);
	$terms=str_replace('ue',chr((252)),$terms);
	//$terms=str_replace('ss',chr((223)),$terms);

	return $terms;	
}











function strsplt($thetext,$num=1)
{
	if (!$num)
	{
		$num=1;
	}
	$arr=array();
	$x=floor(strlen($thetext)/$num);
	while ($i<=$x)
	{
		$y=substr($thetext,$j,$num);
		if ($y)
		{
			array_push($arr,$y);
		}
		$i++;
		$j=$j+$num;
	}
	return $arr;
}



function show_xml_string($txt)
{
	$txt=str_replace('<',"&lt;",$txt);
	$txt=str_replace('>',"&gt;",$txt);
	return $txt;
}










/*
 * Prints an information and
 * returns the tasks to be processed
 */
function print_info($storename,$txt,$elsetxt='',$want_statistics=false)
{
  global $ARCCONFIG;
  $LOCALCONFIG=$ARCCONFIG;
  $LOCALCONFIG{'store_name'}=$storename;
  $store = ARC2::getStore($LOCALCONFIG);
  if (!$store->isSetUp()) {
     $store->setUp();
  }
  $num_triples_before=count_ARC_triples($store);
  $num_triples_before_formatted=number_format($num_triples_before, 0, '.', "'");
  //Print an info on loaded tasks:
  $LOAD_TASK = get_ARC_load_tasks_from_DB($storename,0,$want_statistics);
  $CNT = count($LOAD_TASK);
  
  if ($CNT)
  {
    $i=0;
    print "<br><u>TRIPLE STORE '<b>$storename</b>' (currently $num_triples_before_formatted triples)</u>:";
    print "<br>The following <b>$CNT triple load tasks</b> $txt";
    print "<table>";
    foreach ($LOAD_TASK as $arr)
    { 
      list($storename,$filepath,$statistics,$timestamp_statistics) = $arr;
      if ($want_statistics)
      {
        print "<tr><td valign='top' align='right'>".($i++).":</td><td valign='top' ><b>$storename</b> $filepath</td>"
              ."<td/><td><i><b>$timestamp_statistics</b><br>$statistics</i></td></tr>";
      } 
      else
      {
        print "<tr><td align='right'>".($i++).":</td><td><b>$storename</b>: $filepath</td></tr>";
      }  
    } // foreach
    print "</table>";
  } else 
  {
    if ($elsetxt<>'')
      print $elsetxt;
  }  
  return $LOAD_TASK;
 } 


  function delete_each_triple_load_task($storename='')
  {
    $EVTL_STORENAME=$storename==''?'':"AND storename='$storename'";
    $EVTL_STORENAME_TXT=$storename==''?'':"in store '$storename'";
    global $ARCDB_DBNAME, $ARCDB_USERNAME, $ARCDB_USERPASS, $ARCDB_DBHOST;
    //print "DB: $ARCDB_DBNAME, $ARCDB_USERNAME, $ARCDB_USERPASS, $ARCDB_DBHOST";
    $DBconn = mysql_connect($ARCDB_DBHOST,$ARCDB_USERNAME,$ARCDB_USERPASS) or $errors = $errors . "Could not connect to database.\n";
		@mysql_select_db($ARCDB_DBNAME);
    
    $Q=<<<EOQ
    DELETE FROM $ARCDB_DBNAME.`triplefiles_toload` 
    WHERE true
    $EVTL_STORENAME
EOQ;
    
    $resultset = mysql_query($Q);
		if (($numofrows=mysql_affected_rows())<1)
			throw(New Exception(mysql_error($DBconn)."<hr>Problem deleting triple load tasks for storename '$storename' - Query:".$Q."<br><br>"));
    mysql_close($DBconn);
    
    print "<br>$numofrows triple load tasks DELETED $EVTL_STORENAME_TXT !";
    
   
    return true;
    
  } // delete_each_triple_load_task


  function store_statistics_for_this_loaded_triple_file($statistics,$filepath,$storename)
  {
    global $ARCDB_DBNAME, $ARCDB_USERNAME, $ARCDB_USERPASS, $ARCDB_DBHOST;
    //print "DB: $ARCDB_DBNAME, $ARCDB_USERNAME, $ARCDB_USERPASS, $ARCDB_DBHOST";
    $DBconn = mysql_connect($ARCDB_DBHOST,$ARCDB_USERNAME,$ARCDB_USERPASS) or $errors = $errors . "Could not connect to database.\n";
		@mysql_select_db($ARCDB_DBNAME);
    
    $statistics=mysql_real_escape_string($statistics);
    
    
    
    $Q=<<<EOQ
    UPDATE $ARCDB_DBNAME.`triplefiles_toload` 
    SET `statistics` = '$statistics', 
     `timestamp_statistics` = NOW() 
    WHERE `storename` = '$storename' 
    AND `filepath` = '$filepath' 
EOQ;
    
    $resultset = mysql_query($Q);
		if (($numofrows=mysql_affected_rows())<>1)
			throw(New Exception(mysql_error($DBconn)."<hr>Not Just one row (=$numofrows)in update - Query:".$Q."<br><br>"));
    mysql_close($DBconn);
    return true;
    
  } // store_statistics_for_this_loaded_triple_file
  
  
 
  

  /*
   * Returns a vector of list($storename,$filepath)
   * In case $want_statistics is set, only the done tasks are returned
   */
  function get_ARC_load_tasks_from_DB($storename='',$maxfiles=0,$want_statistics=false)
  {
    $EVTL_STORENAME=$storename==''?'':"AND storename='$storename'";
    $EVTL_LIMIT=$maxfiles>0?"LIMIT $maxfiles":'';
    $EVTL_STATISTICS=$want_statistics?"AND statistics<>''":"AND statistics=''";
    $EVTL_SORT=$want_statistics?'ORDER BY timestamp_statistics ASC':'';
    global $ARCDB_DBNAME, $ARCDB_USERNAME, $ARCDB_USERPASS, $ARCDB_DBHOST;
    //print "DB: $ARCDB_DBNAME, $ARCDB_USERNAME, $ARCDB_USERPASS, $ARCDB_DBHOST";
    $DBconn = mysql_connect($ARCDB_DBHOST,$ARCDB_USERNAME,$ARCDB_USERPASS) or $errors = $errors . "Could not connect to database.\n";
		@mysql_select_db($ARCDB_DBNAME);
    
    $Q=<<<EOQ
    SELECT * 
    FROM $ARCDB_DBNAME.`triplefiles_toload`
    WHERE true
    $EVTL_STATISTICS
    $EVTL_STORENAME
    $EVTL_SORT
    $EVTL_LIMIT
    ; 
EOQ;
    
    $resultset = mysql_query($Q);
    
    //print "$Q<br><br>";var_dump($resultset);
    
    while ($row = mysql_fetch_assoc($resultset)) {
			$TASKS[] = array($row['storename'],$row['filepath'], $row['statistics'], $row['timestamp_statistics']);
		}
    mysql_close($DBconn);

    //print "<br>TASKS: ";var_dump($TASKS);
    
    return $TASKS;
  } // get_load_tasks


  /*
   * Register storename and filepath in table todo
   */
  function register_triples_file($storename,$filepath)
  {
    global $ARCDB_DBNAME, $ARCDB_USERNAME, $ARCDB_USERPASS, $ARCDB_DBHOST;
    $DBconn = mysql_connect($ARCDB_DBHOST,$ARCDB_USERNAME,$ARCDB_USERPASS) or $errors = $errors . "Could not connect to database.\n";
		@mysql_select_db($ARCDB_DBNAME);
    
    $Q=<<<EOQ
    INSERT INTO $SRCDB_DBNAME.`triplefiles_toload` 
          (`filepath`,`storename`) 
    VALUE ('$filepath','$storename'); 
EOQ;
    
    $resultset = mysql_query($Q);
		if (($numofrows=mysql_affected_rows())<1)
			throw(New Exception(mysql_error($DBconn)."<hr>Query:".$Q."<br><br>"));
    mysql_close($DBconn);
    return $numofrows;
  } // register_triples_file

   
  
  
  /*
   * Loads the triples into store and returns the statistics
   */
  function load_triplefile_into_ARC_store(&$store,$storename,$obj)
  {
    $num_triples_before=count_ARC_triples($store);
    $num_triples_before_formatted=number_format($num_triples_before, 0, '.', "'");

    Logger::logAction(26, array('msg'=>'importing file: '.$obj.' into storename '.$storename." ($num_triples_before_formatted triples)"));

    //We need on the server at HEG to enhance php execution time limit, 
    //since this server is slowlee and need more time than the local power macs
    set_time_limit ( 1000000 ); // 250h -> Feature in 5.3.0 deprecated, in 5.4.0 deleted - but useful right now
    $rs=NULL;
    $repetitions=0;
    $added_triples=0;
    $MAXREPETITIONS=5;
    while($added_triples == 0 && $repetitions < $MAXREPETITIONS)
    {
      $rs= $store->query("LOAD <$obj>");
      $added_triples = intval($rs['result']['t_count']);
      $repetitions++;
      if (($errs = $store->getErrors())) {

        foreach($errs as $err)
        print "<br>ARC ERROR: $err";
      }
    }
    
		$duration = $rs['query_time'];
		//$added_triples = $rs['result']['t_count'];
		$load_time = $rs['result']['load_time'];

    $num_triples_before_formatted=number_format($num_triples_before, 0, '.', "'");

    $added_triples_formatted=number_format($added_triples, 0, '.', "'");

    
		print " <hr>Loaded file <b>$obj</b> into <b>$storename</b>
            <br>duration: $duration sec
            <br>load_time: $load_time sec
            <br>added_triples: $added_triples_formatted
            <br>";
    
    if ($added_triples==0) 
        $statistics=null;
    else
    { 
      $num_triples_after=count_ARC_triples($store);
      $num_triples_after_formatted=number_format($num_triples_after, 0, '.', "'");
      
      $triples_delta=$num_triples_after - $num_triples_before;
      $EVTL_DELTA=" (delta triples=$triples_delta)";
        
      $REPS=($repetitions>1)?" ($repetitions repetitions)":"";
      $ESITO=($added_triples>0)
                  ?"$added_triples triples$EVTL_DELTA$REPS"
                  :"<b><font style='color:red'>No triples ($added_triples_formatted) added after $repetitions repetitions</font></b>";
      $statistics="Triple file processed: $ESITO, duration: $duration sec, load_time: $load_time sec - total triples after processing: $num_triples_after_formatted";
      Logger::logAction(26, array('msg'=>$statistics));
    }
    //Avoid updating statistics if no triples added...
    
    
    return $statistics;
  }

  
  
  
  
  
function count_ARC_triples(&$store)
##################################
# 
# Computes the query to SKOS $verb
# $verb= related, broader, narrower
{
	
	$QUERY=<<<EOQ
	SELECT (COUNT(*) AS ?no) { ?s ?p ?o  }
EOQ;

	$result=array();
	if ($rows = $store->query($QUERY, 'rows')) 
	{
    //var_dump($rows);
    $no = $rows[0]['no'];
    
 	}
  
  //print "<br> count_ARC_triples returning $no triples in store ";
  
	return $no;
}

  


	/*
   * Returns a vector of list($storename,$filepath)
   * In case $want_statistics is set, only the done tasks are returned
   */
  function get_namespaces_from_DB()
  {
    global $ARCDB_DBNAME, $ARCDB_USERNAME, $ARCDB_USERPASS, $ARCDB_DBHOST;
    //print "DB: $ARCDB_DBNAME, $ARCDB_USERNAME, $ARCDB_USERPASS, $ARCDB_DBHOST";
    $DBconn = mysql_connect($ARCDB_DBHOST,$ARCDB_USERNAME,$ARCDB_USERPASS) or $errors = $errors . "Could not connect to database.\n";
		@mysql_select_db($ARCDB_DBNAME);
    
    $Q=<<<EOQ
    SELECT * 
    FROM $ARCDB_DBNAME.`semweb_namespaces`
    ; 
EOQ;
    
    $resultset = mysql_query($Q);
    
    while ($row = mysql_fetch_assoc($resultset)) {
    	$ns_name=$row['ns_name'];
    	$ns_url=$row['ns_url'];
			$NAMESPACES{$ns_name}=$ns_url;
		}
    if ($DBconn) mysql_close($DBconn);
		else print getBacktrace();
		
		if(0)
		{
			print "<br>Namespaces:<br>";
			foreach($NAMESPACES as $ns_name=>$ns_url)
				print "<br><b>$ns_name</b>: ".htmlentities($ns_url);
		}
		
    return $NAMESPACES;
  } // get_namespaces_from_DB







$FRIUTILITIES=1;


/**
 * Getting backtrace
 *
 * @param int $ignore ignore calls
 *
 * @return string
 */
 function getBacktrace($ignore = 2)
{
    $trace = '';
    foreach (debug_backtrace() as $k => $v) {
        if ($k < $ignore) {
            continue;
        }

        array_walk($v['args'], function (&$item, $key) {
            $item = var_export($item, true);
        });

        $trace .= '<br>#' . ($k - $ignore) . ' ' . $v['file'] . '(' . $v['line'] . '): ' . (isset($v['class']) ? $v['class'] . '->' : '') . $v['function'] . '(' . implode(', ', $v['args']) . ')' . "\n";
    }

    return $trace;
} 




?>
