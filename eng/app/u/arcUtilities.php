<?php

/**
 * arcUtilities.php ARC2 Utilities
 * Author: Fabio Ricci
 * For HEG
 * Email: fabio.ricci@ggaweb.ch
 * Tel: +41-76-5281961
 */

/*The following can be used as mechanism for carrying complex data inside triples using base64 coding: */
$TOBECODED64 = array('dc:description','rodin:abbreviation');

$filenamex="app/root.php"; $max=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}

//Include ARC2 LOCAL STORE INFOS
$filename="gen/u/arc/ARC2.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}

//Include UTILITIES
$filename="u/FRIdbUtilities.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}
#######################################


	/**
	 * prints the triples from $storename using QUERY
	 * or all triples from store rodin
	 * @param $storename
	 * @param $QUERY
	 */
	function get_triples_as_html_table
												(	&$RDFenhancement,
													$show_list3pagelinks,
													$QUERY='',
													$TITLE='All triples',
													$tableclass='tripletable')
	{
		global $RODINUTILITIES_GEN_URL;
		$IMG3P_ICON = "$RODINUTILITIES_GEN_URL/images/arrow_link.png";
		$IMG3P="<img src='$IMG3P_ICON' width='15'>";
		$RDFenhancementCLASS = get_class($RDFenhancement);
		$QUERY=$RDFenhancementCLASS::$NAMESPACES_PREFIX." select ?s ?p ?o { ?s ?p ?o . }";
		$TITLE_TPAGELINK="Click to open rodin w3s triple page in new tab";
		$HTML='';
		//print "<br>QUERY:<br>".htmlentities($QUERY);
		if ($rows = $RDFenhancementCLASS::$store->query($QUERY, 'rows')) 
		{
			$i=0;
			
			$HTML.= "<table  border='1' class='$tableclass'>";
			$HTML.= "<tr><td colspan='4'><b>$TITLE</b></td></tr>";
			foreach($rows as $row) 	
			{	$i++;
			
				$subject	= prettyprintURI($row['s'],$RDFenhancementCLASS::$NAMESPACES) ;
				$predicate= prettyprintURI($row['p'],$RDFenhancementCLASS::$NAMESPACES) ;
				
				$object		= prettyprintURI($row['o'],$RDFenhancementCLASS::$NAMESPACES) ;
				$object   = strstr($row['o'],'http://')
										?"<a href='".$row['o']."' target='_blank' title='click to open url in a new tab'>$object</a>"
										:$object;
			
				if ($show_list3pagelinks)
				{
					//Compute link for subject
					
					//print "<br>CHECKING strstr($subject,{$RDFenhancementCLASS::$ownnamespacename}) ...";
					
					$TPAGELINK_S=	strstr($subject,$RDFenhancementCLASS::$ownnamespacename)
												?	correct_rodin_url(  $row['s'], $RDFenhancementCLASS::$NAMESPACES  )
												:'';
					$TPAGELINK_P=	strstr($predicate,$RDFenhancementCLASS::$ownnamespacename)
												?	correct_rodin_url(  $row['p'], $RDFenhancementCLASS::$NAMESPACES  )
												:'';
					$TPAGELINK_O=	strstr($object,$RDFenhancementCLASS::$ownnamespacename)
												?	correct_rodin_url(  $row['o'], $RDFenhancementCLASS::$NAMESPACES  )
												:'';
														
					$LINK_S=$LINK_P=$LINK_O='';
					$POINTERTRIPLEPAGE_S = $POINTERTRIPLEPAGE_P = $POINTERTRIPLEPAGE_O = '';
					if ($TPAGELINK_S)
					{
						$LINK_S=<<<EOX
							onclick="window.open('$TPAGELINK_S','_blank')"
							title='$TITLE_TPAGELINK'
							style="cursor:pointer"
EOX;
						$POINTERTRIPLEPAGE_S=	" $IMG3P ";
					}
					
					if ($TPAGELINK_P)
					{
						$LINK_P=<<<EOX
							onclick="window.open('$TPAGELINK_S','_blank')"
							title='$TITLE_TPAGELINK'
							style="cursor:pointer"
EOX;
						$POINTERTRIPLEPAGE_P=	" $IMG3P ";
					}
					
					if ($TPAGELINK_O)
					{
						$LINK_O=<<<EOX
							onclick="window.open('$TPAGELINK_S','_blank')"
							title='$TITLE_TPAGELINK'
							style="cursor:pointer"
EOX;
						$POINTERTRIPLEPAGE_O=	" $IMG3P ";
					}
					
				
					$POINTERTRIPLEPAGE_O=	$TPAGELINK_O
																?" <a href='$TPAGELINK_O' target='_blank' title='$TITLE_TPAGELINK'>$IMG3P</a>"
																:'';						
				} 
			
				$HTML.= "<tr>";
				$HTML.= "<td align='right'>$i";
				$HTML.= "</td>";
				$HTML.= "<td nowrap $LINK_S>";
				$HTML.= $subject.$POINTERTRIPLEPAGE_S;
				$HTML.= "</td>";
				$HTML.= "<td nowrap $LINK_P>";
				$HTML.= $predicate.$POINTERTRIPLEPAGE_P;
				$HTML.= "</td>";
				$HTML.= "<td nowrap $LINK_O>";
				$HTML.= $object.$POINTERTRIPLEPAGE_O;
				$HTML.= "</td>";
				$HTML.= "</tr>";
			}
			$HTML.= "<table>";
		}
		return $HTML;
	} // print_triples
			

			
			
			
			
			
			
			
		
		function decode_64_literal($p,$literal,&$TOBECODED64)
		{
			if (count($TOBECODED64))			
			foreach($TOBECODED64 as $pname)
			{
				if ($p==$pname)
				{
					$literal=base64_decode($literal);
					break;
				}
			}
			return $literal;
		}
		
		
		
		
		/**
		 * Guarantees the triples navigation
		 * 
		 * @param $pagetitle
		 * @param text $uid - the univeral id used to search for triples
		 * @param $ext - indicates whether the call is coming from an extrnal aqeuired rodin pool like europeana
		 */
		function print_triplespage($uid,$pagetitle,$namespace_short='')
		{
			$nix = null;
			$RDFenhancement = new RodinRDFResult($nix,$nix,$nix,$nix);
			if (!$namespace_short)
					$namespace_short=RodinRDFResult::$ownnamespacename;
			$word_id = $namespace_short.':'.$uid;
			print get_triples_as_navi_page(	$RDFenhancement,
																			$word_id,
																			$pagetitle,
																			$tableclass='triplepagetable');
		} // print_triplespage
		
		
	/**
	 * return a text with the triples from $storename using QUERY
	 * or all triples from store rodin
	 * @param $storename
	 * @param $rodin_uid_short an expr like "rodin:blabla" with ns short!
	 */
	function get_triples_as_navi_page(	&$RDFenhancement,
																			$word_id,
																			$pagetitle,
																			$tableclass='triplepagetable')
	{
		global $RODINLOGO;
		global $URL_MANTIS;
		global $W3SLABHOMEPAGEURL;
		$RDFenhancementCLASS = get_class($RDFenhancement);
		$GRAPH=$RDFenhancementCLASS::$importGraph;
		
		//$SORTLITERALS = array('foaf:name', 'rodin:id','dbo:Place','dbo:birthDate','dbo:deathDate','rodin:profession','dc:description','dc:isReferencedBy','dc:creator');
		list($ns_short,$short_uid)=explode(':',$word_id);
		$w_full=correct_rodin_url(  get_full_url($word_id,$RDFenhancementCLASS::$NAMESPACES), $RDFenhancementCLASS::$NAMESPACES  );
		$w_target=(strstr($word_id,'rodin:')||strstr($word_id,'rodin_e:'))?"":"target='_blank'";
		$WORDID_HREF="<a class='about' href='$w_full' $w_target><small>$ns_short:</small>$short_uid</a>";
		
		//print "<br>get_triples_as_navi_page getting triples for '$word_id' (about: $about)";
		
		$p_o 				 = get_entity_infos($RDFenhancement,$word_id);
		$s_p_inverse = get_entity_infos_inverse($RDFenhancement,$word_id);
		//list($p_o,$rest_p_o) = sort_entity_infos($p_o,$SORTLITERALS);
		
		if (count($p_o))
		{
			$entity_isa_arr=$p_o{'rdf:type'};
			if (count($entity_isa_arr))
			{
				foreach($entity_isa_arr as $eisa_obj)
				{
					list($eisa,$ns,$eisa_full,$eisa_isliteral,$eisa_lang) = $eisa_obj;
					
					if ($entitytypes_expr) $entitytypes_expr.=", ";
					$entitytypes_expr.="<a href='$eisa_full' target='_blank'>$eisa</a>";
				}
				$EVTL_ENTITY_TYPE_DEF=" of type $entitytypes_expr";
			}
$TRIPLEPAGE=<<<EOP
		<table id='triplepageframe' width='100%' height='100%' cellpadding="0" border=0>
		<tr height="5">
			<td align='left' valign='top'>
				<span class='triplepagei'>$pagetitle</span>
			</td>
			<td align='right' valign='top'>
			<span class='triplepage'>$URL_MANTIS</span>
			</td>
		</tr>
		<tr height='20'>
		<td valign='top'>
    <div id="header" >
			  <h2 id="title">About $WORDID_HREF</h2>
		  	<div class="page-resource-uri" title="An entity is connotated by direct (and inverse) properties obtained traversing the graph centerd on the subject '$word_id'">
			    An entity $EVTL_ENTITY_TYPE_DEF 
			    from graph <a href="$GRAPH" target='_blank'>$GRAPH</a>
		    </div>
		</div> <!-- header -->
		</td>
		<td valign='top' align='right'>
			<div id='headerlogo'>
				<table>
				<tr>
				<td>
					<a href='$W3SLABHOMEPAGEURL' title="Click to go back to RODIN's W3S LAB homepage">
						<img src='$RODINLOGO' width='80'>
					</a>
				</td>
				</tr>
					<td>
					</td>
				</tr>
				</table>
			</div>
		</td>
		</tr>
		
		<tr>
		<td  valign='top' colspan="2" >
		<div id="content" class="$tableclass">
				<table class="description $tableclass" width="100%" cellpadding="1" cellspacing="0">
EOP;

	$i=0;
	foreach($p_o as $p=>$ooo)
	{
		$i++; $even = !($i%2); 
		$bgclass=$even?'even':'odd';
		//print "<br>even($i): class $class";
		//Skip some
		if ($i==1) // only once
		{
				$TRIPLEPAGE.=<<<EOP
		<tr height="10">
			<td colspan='2' title="Direct properties are gathered by traversing the graph from the subject '$word_id'">
				Properties:
			</td>
		</tr>
		<tr>
		<td colspan="2">
		   <hr class='triplepage'>
		</td>
		</tr>
EOP;
		}
			
				if ($p<>'rdf:type')
		{
			
			$p_full=correct_rodin_url(  get_full_url($p,$RDFenhancementCLASS::$NAMESPACES), $RDFenhancementCLASS::$NAMESPACES  );
			$pns=get_ns($p);
			$pshort=str_replace("$pns:",'',$p);
			$class="literal";
			$p_target=(strstr($p,'rodin:')||strstr($p,'rodin_e'))?"":"target='_blank'";
			//show section on all same content:
			$P_HREF="<a href='$p_full' $p_target><small>$pns:</small>$pshort</a>";
$TRIPLEPAGE.=<<<EOP
	<tr class='$bgclass'><td valign='top' class='property' style='white-space:nowrap'>
	<span class="predicate">$P_HREF</span></td>
	<td><ul class='rodinpedia'>
EOP;
			foreach($ooo as $obj)
			{
				list($o,$ns,$o_full,$o_is_literal,$o_lang) = $obj;
		
				if ($o_is_literal)
				{
					$O_HREF=decode_64_literal($p,$o,$RDFenhancementCLASS::$TOBECODED64);
					
					//Check url -> make clickable:
					$O_HREF   = strstr($o,'http://')
										?"<a href='".$o."' target='_blank' title='click to open url in a new tab'>$O_HREF</a>"
										:$O_HREF;
				} else {
					$o_full = correct_rodin_url($o_full,$RDFenhancementCLASS::$NAMESPACES);
					$o_short=str_replace("$ns:",'',$o);
					$o_target=(strstr($o,'rodin:')||strstr($o,'rodin_e:'))?'':"target='_blank'";
					//print "<br>o=$o o_target=$o_target";
					$O_HREF="<a href='$o_full' $o_target><small>$ns:</small>$o_short</a>";
				} 
				$TRIPLEPAGE.=<<<EOP
	<li><span class="object">$O_HREF</span></li>
EOP;
			}
		} // skip
	} // foreach($p_o as $p=>$ooo)

	if (count($s_p_inverse))
	foreach($s_p_inverse as $p=>$sss)
	{
		$i++; $even = !($i%2); 
		$bgclass=$even?'even':'odd';
		
		if (!$inverted_header_shown)
		{
			$inverted_header_shown=true; //do only once
				$TRIPLEPAGE.=<<<EOP
	<tr height='20'/>
	<tr><td colspan='2' title="Inverse properties are gathered by traversing the graph toward the subject '$word_id'">
		Inverse properties:
    <hr class='triplepage'>
	</td>
	</tr>
EOP;
		} //do only once $inverted_header_shown
		
		//print "<br>even($i): class $class";
		//Skip some
		$p_full=correct_rodin_url(  get_full_url($p,$RDFenhancementCLASS::$NAMESPACES), $RDFenhancementCLASS::$NAMESPACES  );
		$pns=get_ns($p);
		$pshort=str_replace("$pns:",'',$p);
		$class="literal";
		$p_target=(strstr($p,'rodin:')||strstr($p,'rodin_e:'))?"":"target='_blank'";
		//show section on all same content:
		$P_HREF="<a href='$p_full' $p_target>inv(<small>$pns:</small>{$pshort})</a>";
$TRIPLEPAGE.=<<<EOP
	<tr class='$bgclass'><td valign='top' class='property' style='white-space:nowrap'>
	<span class="predicate">$P_HREF</span></td><td><ul class='rodinpedia'>
EOP;
		foreach($sss as $obj)
		{
			list($s,$ns,$s_full,$_,$_) = $obj;
			
			
			$s_full = correct_rodin_url($s_full,$RDFenhancementCLASS::$NAMESPACES);
			$s_short=str_replace("$ns:",'',$s);
			$s_target=(strstr($s,'rodin:')||strstr($s,'rodin_e:'))?'':"target='_blank'";
			//print "<br>o=$o o_target=$o_target";
			$S_HREF="<a href='$s_full' $s_target><small>$ns:</small>$s_short</a>";
			
			$TRIPLEPAGE.=<<<EOP
	<li><span class="object">$S_HREF</span></li>
EOP;
			}
	} // foreach($s_p_inverse as $p=>$sss)
		
		
$TRIPLEPAGE.=<<<EOP
	</ul></td></tr>
EOP;

				//Construct/shor inverses:
				//
	
$TRIPLEPAGE.=<<<EOP
      </table>
    </div> <!--  #content -->
   </td>
  </tr>
  </table>
 
EOP;
		} // count($p_o)
else print "<br>Sorry: No further information found on $word_id";
		
		
		return $TRIPLEPAGE;
	} // get_triples_as_navi_page	
		
		
		
		
		
	
	/**
	 * in case the url is rodin: (the coresponding long for rodin)
	 * substitute the last word /bla with /?token=bla
	 */
	function correct_rodin_url($url,&$NAMESPACES)
	{
			
		//print "<br>correct_rodin_url ($url) returning ...";
		//var_dump($NAMESPACES);
			
		
		if (strstr($url,$NAMESPACES{'rodin'}))
		{
			$path_parts=pathinfo($url);
			$filename=$path_parts['filename'];
			$dirname=$path_parts['dirname'];
			
			//in case rodin house address matches ... correct:
			if (strstr($dirname,$NAMESPACES{'rodin_e'}))
				$delta_rodin= str_replace($NAMESPACES{'rodin_e'},'',$dirname);
			if (!$delta_rodin && strstr($dirname,$NAMESPACES{'rodin'}))
			$delta_rodin= str_replace($NAMESPACES{'rodin'},'',$dirname);
			
			if ($delta_rodin)
			{
				//print "<br>correct_rodin_url($url) (delta=($delta_rodin)) filename=$filename dirname=$dirname";
				
				//Attach delta to filename
				$dirname=str_replace($delta_rodin,'',$dirname);
				$filename=$delta_rodin.'/'.$filename; //Assume always '/'
				//print "<br>corrected: filename=$filename dirname=$dirname";
			}
			$url=$dirname.'?token='.$filename;
		}
		//
		return $url;
	}
	
	
	
	
	
	function get_entity_infos_inverse(	&$RDFenhancement,
																			$subject_uid_short )
	{
		return get_entity_infos(	$RDFenhancement,
															$subject_uid_short,
															$direct = false );
	} // get_entity_infos_indirect
	
		
	/**
	 * Query the local store $store
	 * Returns a vector of array($o,$ns,$o_full,$o_is_literal,$o_lang) depending from $direct
	 * corresponding to subject_uid_short as subject
	 * @param RodinRDFResult& $RDFenhancement obj containing rdf store information 
	 * @param string $subject_uid_short ID to be use to find triples
	 * @param bool $direct flag indicating whether the direct or the inverse relationshould be computed
	 * 
	 * In case of $direct=true returns triples ($subject_uid_short ?p ?o)
	 * In case of $direct=false returns triples (?s ?p $subject_uid_short)
	 */
	function get_entity_infos(	&$RDFenhancement,
															$subject_uid_short,
															$direct = true)
	{
		if (!$subject_uid_short) {fontprint("System error: subject_uid_short is empty!",'red');}
		//else fontprint("subject_uid_short: ($subject_uid_short)",'green');
		$RDFenhancementCLASS = get_class($RDFenhancement);
		
		$p_o=array();
		//Retrieve triple information on author
		$QUERY 	= $direct
						?$RDFenhancementCLASS::$NAMESPACES_PREFIX . ' select ?p ?o { '.$subject_uid_short.' ?p ?o . }'
						:$RDFenhancementCLASS::$NAMESPACES_PREFIX . ' select ?s ?p { ?s ?p '.$subject_uid_short.' . }';
		
//		print "<br>get_entity_infos ($subject_uid_short) ... <b>".htmlentities($QUERY).'<b>';
		if ($rows = $RDFenhancementCLASS::$store->query($QUERY, 'rows')) 
		{			
				$i=0;
				foreach($rows as $row) 	
				{	$i++;
					$p_full=$row['p'];
					$p = separate_namespace($RDFenhancementCLASS::$NAMESPACES,$p_full,':',false);
				
					if($direct)
					{
						$o_full=$row['o'];
						$o = separate_namespace($RDFenhancementCLASS::$NAMESPACES,$o_full,':',false);
						$o_is_literal=($o==$o_full);
						$o_lang=$row['o_lang']; // if any
						$ns=$o_is_literal?'':get_ns($o);
						//Prepare obj to be stored under $p:
						$obj=array($o,$ns,$o_full,$o_is_literal,$o_lang);
					} 
					else 
					{
						$s_full=$row['s'];
						$s = separate_namespace($RDFenhancementCLASS::$NAMESPACES,$s_full,':',false);
						$ns=get_ns($s);
						//Prepare obj to be stored under $p:
						$obj=array($s,$ns,$s_full,false,null);
					} // inverse
					
					//store each triple inside a value
					//on collision make an array and adds elements:
										
					if (!is_array($p_o[$p]))
					{
						$p_o[$p]=array($obj);
					}
					else {
						$old_content=$p_o[$p];
						$old_content[]=$obj;
						$p_o{$p}=$old_content;
					}
				}
			}
	//var_dump($p_o);
	return $p_o;

	} // get_entity_infos
	
			
	function 	get_ns($o)
	{
		$ns=substr($o,0,strpos($o,':'));
		
		return $ns;
	}
	
	
	function get_full_url($semwebtoken,&$NAMESPACES)
	{
		
		list($ns,$token) = explode(':',$semwebtoken);
		$full_url=$NAMESPACES{$ns}.$token;
		
		//print "<br>get_full_url($semwebtoken) returning $full_url";
		
		return $full_url;
	}
		
		
			
		
	function prettyprintURI($URIexpr,&$namespaces)
	{
	  $SEP='';
	
	  list($left,$right,$literal) = analyzeURI($URIexpr,$namespaces);
	  
		
		
		
	  if ($left)
	  {
	    $left= separate_namespace($namespaces,$left.$SEP,':',true);
	  }  
	  
	  //print "<br>prettyprintURI($URIexpr)=($left,$right,$literal)";
	  // <span class='urigray'>$left</span><span class='uribold'>$right</span> <span class='uriliteral'>$literal</span>

  return <<< EOR
 $left<span class='uribold'>$right</span> <span class='uriliteral'>$literal</span>
EOR;
	}


	
	
	/**
	 * separate an expression into namespace and term
	 * in case of href=true, namespace is a href which leads to its definition page
	 * returns the term without the namespace
	 */
	function separate_namespace(&$namespaces,$term,$sep=':',$href=true)
	{
	  //OPTIMIZE
	  //print "<br><br>separate_namespace ... Test substitution on <b>$term</b>";
	  if (!(strstr($term,'http://')))
	  {
	    //print "<br>Exit: Term '<b>$term</b>' already done";
	    return $term;
	  }
	  else
	  {  
	    foreach($namespaces as $ns=>$expr)
	    {
	      $expr2=str_replace("/","\/",$expr);
	      $pattern="/$expr2(.*)/";
	
	      //print "<br>Pattern $pattern ";
	
	      if (preg_match($pattern,$term,$match))
	      {
					$exprH=htmlentities($expr);
					
		      $ns_title_span_expl=$href
		      									 ?"<a class=\"urigray\" href='#' target=\"_blank\" onclick=\"open_ns('$expr2');return false;\" title=\"Click to explore namespace definition\n$exprH\n(if existing) in a new tab\">$ns</a>"
														 :$ns;
		      //ATTENTION: GERMAN UMLAUTS ... NOT HANDLED HERE?
	
	        $matched=1;
	        $nakedterm=$match[1]; //cut first "/"
	        if ($nakedterm[0]=='/')
	            $nakedterm=substr($nakedterm,1);
	
	        $returnterm=$ns_title_span_expl.$sep.$nakedterm;
	        //print " YES ";
	        break;
	      }
	    }
	
	    if (!$matched) 
	    {
	      $returnterm=$term;
	      //print " ERROR applying $pattern -> $returnterm<br>";
	    } 
	    //else print " SUCCESS: $term -> $returnterm<br>";
	  }
	  return $returnterm;
	}

	
		
	/**
	 * Returns a separation in left/right expression
	 * list($left,$right,$literal)
	 * 
	 * where $right is the actual token (or literal)
	 * and left the rest of the expression
	 */
	function analyzeURI($URIexpr,&$namespaces)
	{
	
	  $literal='';
		foreach($namespaces as $ns=>$ns_uri)
		{
			if (strstr($URIexpr,$ns_uri))
			{
				$left=$ns_uri;
				$right=str_replace($ns_uri,'',$URIexpr);
				break;
			}
		}
		if (!$left)
		{ // literal
	    $literal=utf8_decode($URIexpr);
	    $right='';
	    $left='';
	  } 
		
		
		//print "<br>LEFTRIGHT: ($left)($right)";
		if ($left)	
	  {
	    $left= separate_namespace($namespaces,$left.$SEP,':');
	  }  
				
		//print "<br>analyzeURI ($URIexpr) ==> ($left)($right)($literal)";
	
	  return array($left,$right,$literal);
	}	
		
	/**
	 * Returns a separation in left/right expression
	 * list($left,$right,$literal)
	 * 
	 * where $right is the actual token (or literal)
	 * and left the rest of the expression
	 */
	function analyzeURI_old($URIexpr,&$namespaces)
	{
	  $literal='';
	  list($left,$right) = splitrn($URIexpr,$SEP='#',1);
	  if (!$right)
	    list($left,$right) = splitrn($URIexpr,$SEP='/',1);
	  if (! $right  
	       ||   is_numeric($right) )
	    list($left,$right) = splitrn($URIexpr,$SEP='/',2);
	  if (!$right)
	  { // literal
	    $literal=utf8_decode($URIexpr);
	    $right='';
	    $left='';
	  } 
		
		print "<br>LEFTRIGHT: ($left)($right)";
		if ($left)	
	  {
	    $left= separate_namespace($namespaces,$left.$SEP,':');
	  }  
				
		print "<br>analyzeURI ($URIexpr) ==> ($left)($right)($literal)";
	
	  return array($left,$right,$literal);
	}	
		
		

		
	
	/**
	 * Compute related subjects to subject inside a given spqrql source
	 * using
	 * 
	 * PREFIX dc: <http://purl.org/dc/elements/1.1/>
	 * select ?srelated {
	 *   ?s ?_ "$subject" .
	 *   ?s dc:subject ?srelated .
	 * } limit 10
	 *
	 * @param string $subject
	 * @param string $src_name
	 * @param string $sds_sparql_endpoint
	 * @param string $sds_sparql_endpoint_params
	 * @param $integer $limit
	 *  
	 */	
	function get_related_subjects_from_sparql_endpoint(	$subject,
																											$src_name,
																											$sds_sparql_endpoint,
																											$sds_sparql_endpoint_params,
																											$limit=5 )
	{
		$debug=0;
		$subject_arr = array();
		$cache_id="relatedsubjects.$src_name.$subject.$limit";
		
		$sparqlquery=<<<EOQ
PREFIX dc: <http://purl.org/dc/elements/1.1/>

select ?srelated
{
  ?s ?_ "$subject" .
  ?s dc:subject ?srelated .
 } limit $limit		
EOQ;

		if ($debug)
		print "<br>get_related_subjects_from_sparql_endpoint:";

		$url_endpoint= $sds_sparql_endpoint
								 	.'?query='.urlencode($sparqlquery)
									.'&'.$sds_sparql_endpoint_params;
		
		//cache_src_response($cache_id,$xml_src_content)
    //Logger::logAction(25, array('from'=>'get_related_subjects_from_sparql_endpoint','msg'=>'Started with cacheid:'.$cache_id));
    list($xmlCached_content,
            $creationtimestamp,
            $age_in_sec,
            $max_age_in_sec, // this is always set!! even if no data
            $expiring_in_sec) = get_cached_src_response($cache_id);

    if (! sparql_cached_content_quality_control($xmlCached_content,$src_name,$age_in_sec))
    { // ask service and rebuild cache
       
      $timestamp=date("d.m.Y H:i:s");
      $timestamp0=time();
      $age_in_sec=0;
      $expiring_in_sec=$max_age_in_sec;
      if ($debug) {
      	print "<br><br>CALLING ".$url_endpoint."<br><br>";
      }
			
			$xml_content=get_file_content($url_endpoint);
		
			cache_src_response($cache_id,$xml_content);
			$xmlCached_content = $xml_content;
		}

		//scan/open $xmlCached_content for later use
		//print "<br>XML GOT FROM $src_name:<br>".htmlentities($xmlCached_content);
		$sxmldom=simplexml_load_string($xmlCached_content,'SimpleXMLElement', LIBXML_NOCDATA);
		foreach($sxmldom->results->result as $result)
		{
			$rel_sub=trim($related_sub=$result->binding->literal."");
			if ($rel_sub<>'')
					$subject_arr[]=$result->binding->literal."";		
		}
		
		if ($debug)
		{
			$subject_arr_noof=count($subject_arr);
			print "<br>RETURNING $subject_arr_noof SPARQL related subjects to $subject from $src_name:";
			foreach($subject_arr as $sub)
			{
				print "<br>$sub";
			}
		}
		return $subject_arr;
	}	// get_related_subjects_from_sparql_endpoint
		
		
		
		
		
		
	/**
	 * Compute related subjects to subject inside a given spqrql source
	 * using
	 * 
	 * PREFIX dc: <http://purl.org/dc/elements/1.1/>
	 * select ?srelated {
	 *   ?s ?_ "$subject" .
	 *   ?s dc:subject ?srelated .
	 * } limit 10
	 *
	 * @param string $subject
	 * @param string $src_name
	 * @param string $sds_sparql_endpoint
	 * @param string $sds_sparql_endpoint_params
	 * @param $integer $limit
	 *  
	 */	
	function get_triples_on_subject_from_sparql_endpoint(	$subject,
																												$src_name,
																												$sds_sparql_endpoint,
																												$sds_sparql_endpoint_params,
																												&$NAMESPACES,
																												$limit=150 )
	{
		$debug=0;
		$subject_arr = array();
		$cache_id="expansion.$src_name.$subject.$limit";
		
		$dce_ns_url=$NAMESPACES{'dce'}
;		
		$sparqlquery=<<<EOQ
PREFIX dce: <$dce_ns_url>

select distinct ?s ?p ?o
{
  ?s dce:subject "$subject" .
  ?s ?p ?o 
 } limit $limit		
EOQ;

		if ($debug)
		print "<br>get_triples_on_subject_from_sparql_endpoint:<br><br>query:<br>".(str_replace("\n","<br>",htmlentities($sparqlquery)))."<br><br>";

		$url_endpoint= $sds_sparql_endpoint
								 	.'?query='.urlencode($sparqlquery)
									.'&'.$sds_sparql_endpoint_params;
		
		//cache_src_response($cache_id,$xml_src_content)
    //Logger::logAction(25, array('from'=>'get_related_subjects_from_sparql_endpoint','msg'=>'Started with cacheid:'.$cache_id));
    list($xmlCached_content,
            $creationtimestamp,
            $age_in_sec,
            $max_age_in_sec, // this is always set!! even if no data
            $expiring_in_sec) = get_cached_src_response($cache_id);

    if (! sparql_cached_content_quality_control($xmlCached_content,$src_name,$age_in_sec))
    { // ask service and rebuild cache
       
      $timestamp=date("d.m.Y H:i:s");
      $timestamp0=time();
      $age_in_sec=0;
      $expiring_in_sec=$max_age_in_sec;
      if ($debug) {
      	print "<br><br>CALLING ".$url_endpoint."<br><br>";
      }
			
			$xml_content=get_file_content($url_endpoint);
		
			cache_src_response($cache_id,$xml_content);
			$xmlCached_content = $xml_content;
		}

		//scan/open $xmlCached_content for later use
		if ($debug) print "<br>XML GOT FROM $src_name:<br>".htmlentities($xmlCached_content);
		$sxmldom=simplexml_load_string($xmlCached_content,'SimpleXMLElement', LIBXML_NOCDATA);
		foreach($sxmldom->results->result as $result)
		{
			//print "<hr>";
			
			$s=$result->binding->uri.'';
			$s = separate_namespace($NAMESPACES,$s,':',false);
			//print "<br><br>s:<br>$s";
			
			
			$p=$result->binding[1]->uri.'';
			$p = separate_namespace($NAMESPACES,$p,':',false);
			//print "<br><br>p:<br>$p";
						
			$o_literal= $result->binding[2]->literal.'';
			$o_uri= $result->binding[2]->uri.'';
			$o_bnode= $result->binding[2]->bnode.'';
			
			if ($o_uri)
				$o_uri = separate_namespace($NAMESPACES,$o_uri,':',false);
			
			$o=$o_literal
				?l($o_literal) //Use special quote function
				:($o_uri
				 ?$o_uri
				 :$o_bnode);
					 
			//print "<br><br>o: ($o_literal,$o_uri,$o_bnode)<br>";
			
			$triples[]=array($s,$p,$o);	
		}
		
		if ($debug)
		{
			$triples_noof=count($triples);
			print "<br>RETURNING $triples_noof SPARQL triples to $subject from $src_name:";
			foreach($triples as $triple)
			{
				list($$s,$p,$o)= $triple;
				print "<br><b>TRIPLE</b>: $s $p $o";
			}
		}
		return $triples;
	}	// get_triples_on_subject_from_sparql_endpoint
			
		
		
		
		
		
		
		
		
		
	/**
	 * State using either cache age or content
	 * whether the cache content should be considered valid
	 */
	function sparql_cached_content_quality_control($xmlcontent,$src_name,$age_in_sec)
	{
		$ok = trim($xmlcontent) <> '';

		//Special case empty xml object		
		if ($ok && strstr(strtolower($src_name),'europeana'))
		{
			//recompute only after 4 hours
			$tolerated_age_sec=4*3600;
			$ok = ($age_in_sec < $tolerated_age_sec);
			//$sxmldom=simplexml_load_string($xmlcontent,'SimpleXMLElement', LIBXML_NOCDATA);
			//$ok = count($sxmldom->results->result)>0; // there are/where results
		}//europeana	
		 
		return $ok;
	}
		
		
		
		
 	function cleanup4literal($str)
	{
		$str = str_replace("'","\\'",$str);
		return $str;
	}
	
	
	/**
	 * Returns an s string value: subject_uid used by the first triple having value
	 */
	function get_triple_subject($o,$literal,&$RDFresult)
	{
		$RDFenhancementCLASS=get_class($RDFresult);
		$store=$RDFenhancementCLASS::$store;
		$QUERY = $RDFenhancementCLASS::$NAMESPACES_PREFIX . " select ?s { ?s $o \"$literal\" . } limit 1";
		$s = null;
		//print "<br>get_triple_subject ($literal) ... <b>".str_replace("\n","<br>",htmlentities($QUERY)).'<b>';
		if ($rows = $RDFenhancementCLASS::$store->query($QUERY, 'rows')) 
		{			
			$row=$rows[0];
			$s_full=$row['s'];
			$s = separate_namespace($RDFenhancementCLASS::$NAMESPACES,$s_full,':',false);
		}
		
		//print "<br>get_triple_subject returns ($literal)=>($s)";
		return $s;
 	} // get_triple_subject
	

	/**
	 * returns "$str"
	 */
	function l($str)
	{
		return '"'.addslashes($str).'"';
	}

	function l_inverse($str)
	{
		return str_replace('"','',$str);
	}

//$REFERER=get_referer(); // global
$ARCUTILITIES=1;
?>