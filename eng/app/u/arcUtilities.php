<?php

/**
 * arcUtilities.php ARC2 Utilities
 * Author: Fabio Ricci
 * For HEG
 * Email: fabio.ricci@ggaweb.ch
 * Tel: +41-76-5281961
 */

/*The following can be used as mechanism for carrying complex data inside triples using base64 coding: */

$filename="$RODINSEGMENT/app/root.php"; $max=10;
//print "<br>arcUtilities: seeking $filename";
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
{
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}
}
//Include ARC2 LOCAL STORE INFOS
$filename="gen/u/arc/ARC2.php"; $maxretries=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}

//Include UTILITIES
$filename="$RODINSEGMENT/app/u/FRIdbUtilities.php"; $maxretries=10;
//print "<br>arcUtilities: seeking $filename";
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
{
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}
}
#######################################


			
				/**
	 * prints the triples from $storename using QUERY
	 * or all triples from store rodin
	 * @param 
	 * @param 
	 */
	function get_triples_as_html_table2
												(	&$STORE,
													$STORENAME,
													&$NAMESPACES,
													&$TOBECODED64,
													$ownnamespacename,
												  $added_triples,
													$show_list3pagelinks,
													$QUERY='',
													$TITLE='All triples',
													$tableclass='tripletable')
	{
		global $RODINUTILITIES_GEN_URL;
		global $PROT; // hhtp or https
		$IMG3P_ICON = "$RODINUTILITIES_GEN_URL/images/arrow_link.png";
		$IMG3P="<img src='$IMG3P_ICON' width='15'>";
		$QUERY=$NAMESPACES_PREFIX." select ?s ?p ?o { ?s ?p ?o . }";
		
		$TITLE_TPAGELINK="Click to open dbRODIN LoD Browser in new tab";
		$HTML='';
		//print "<br>QUERY:<br>".htmlentities($QUERY);
		if ($rows = $STORE->query($QUERY, 'rows')) 
		{
			$i=0;
			$TRIPLECOUNT = count($rows);
			$HTML.= "<table  border='1' class='$tableclass'>";
			$HTML.= "<tr><td colspan='4'><b>$added_triples added triples ($TRIPLECOUNT TRIPLES in STORE '$STORENAME') $TITLE</b></td></tr>";
			foreach($rows as $row) 	
			{	$i++;
			
				$subject	= prettyprintURI($row['s'],$NAMESPACES) ;
				$predicate= prettyprintURI($row['p'],$NAMESPACES) ;
				
				$predicatex= printURI($row['p'],$NAMESPACES) ;
				$object		= prettyprintURI(decode_64_literal($predicatex,$row['o'],$TOBECODED64),$NAMESPACES) ;
				$object   = substr($row['o'],0,4)==$PROT
										?"<a href='".$row['o']."' target='_blank' title='click to open url in a new tab'>$object</a>"
										:$object;
			
				if ($show_list3pagelinks)
				{
					//Compute link for subject
					
					//print "<br>CHECKING strstr($subject,{$ownnamespacename}) ...";
					
					$TPAGELINK_S=	strstr($subject,$ownnamespacename)
												?	correct_rodin_url(  $row['s'], $NAMESPACES  )
												:'';
					$TPAGELINK_P=	strstr($predicate,$ownnamespacename)
												?	correct_rodin_url(  $row['p'], $NAMESPACES  )
												:'';
					$TPAGELINK_O=	strstr($object,$ownnamespacename)
												?	correct_rodin_url(  $row['o'], $NAMESPACES  )
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
			$HTML.= "</table>";
		}
		return $HTML;
	} // get_triples_as_html_table2
			
			
			
			
			
		function timestamp_for_rdf_annotation()
		{
			//$now =  time() .'_'. str_pad(substr((float)microtime(), 2), 6, '0', STR_PAD_LEFT);
			list($microSec, $timeStamp) = explode(" ", microtime());
			$time4rdf=$timeStamp.'_'.($microSec*1000000);
			$now4humans= date('F jS, Y, H:i:', $timeStamp) . (date('s', $timeStamp) + $microSec);
			return array($time4rdf,$now4humans);
		}	
			
			
		
		
		function decode_64_literal($p,$literal,&$TOBECODED64)
		{
			if ($TOBECODED64{$p})	
			{
				if (is_array($literal))
				{
					$literal=$literal[0]; // assume
				}
				$literal=base64_decode($literal);
			}
			return str_replace('"','',$literal);
		}
		
		
		
		/**
		 * Extracts portions of $txt which are numbers or (en) stopwords
		 * @param $txt0 - The text to be cleaned
		 */
		function filter_as_subject($txt0,$pattern='')
		{
			//Do not want to see the following chars inside the term $txt0
			$pattern = $pattern<>''?$pattern:'/[0-9&!;\\\.\:\-_\[\]\(\)]+/';
			$replace = '';
			$txt= trim(preg_replace($pattern, $replace, $txt0));
			//print "<br>filter_as_subject($txt0)=>($txt)";
			$stopWords = get_stopwords();
			
			$newtxt='';
		
			if (is_numeric($txt))
			return $newtxt;
			
			if (strstr($txt,' '))
				$arr=preg_split("/[\;\:\. ]+/", $txt);
			else $arr=array($txt);
			
			foreach($arr as $microseg)
			{
				$microseg=trim($microseg);
				if ($microseg<>'')
				{
				  //print "<br>microseg ($microseg)";
					if (!is_numeric($microseg) 
					&& !is_date_segment($microseg) 
					&& !is_romanic_number($microseg)  
					&& !in_array($microseg, $stopWords))
					$newtxt.=' '.$microseg;
					//else print " discard! ";
				}
			}
			$newtxt = ltrim($newtxt);
			$newtxt = utf8_encode($newtxt);
			
			//print "<br>filter_as_subject($txt0)=>($txt)=>($newtxt)";
			return $newtxt;
		}
		
		
		
		
		
		/**
		 * 
		 */
		function tell_subjects(&$subjects,$TITLE)
		{
			global $RDFLOG;	
	 		if (is_array($subjects) && ($c=count($subjects)))
			{
				
				$RDFLOG.="<hr><i>$c $TITLE</i>";
				foreach($subjects as $sub)
					$RDFLOG.="<br>$sub";
				$RDFLOG.="<hr>";
			}
		}
		
		
		
			
		
		/**
		 * Flatten skos subjects to be used in a loop
		 * Sort output biggest subjects first
		 */
		function flatten_sort_skos_obj_to_array($skos_subjects_expansions)
		{
			$DEBUG=0;
			global $RDFLOG;
			//var_dump($skos_subjects_expansions);
			if ($DEBUG) $RDFLOG.="<hr><b>flatten_sort_skos_obj_to_array</b>";
			$flattened = array();
			if (is_array($skos_subjects_expansions) && count($skos_subjects_expansions))
			{
	 			foreach($skos_subjects_expansions as $subject=>$EXPANSIONS)
				{
					foreach($EXPANSIONS as $SKOS)
					{
						list($src_name,$srcuid,$src_data_fresh,$broaders,$narrowers,$related)=$SKOS;
					}
					if (is_array($broaders) && count($broaders))
					{
						foreach($broaders as $label)
						$flattened[]=$label;
					}
					if (is_array($narrowers) && count($narrowers))
					{
						foreach($narrowers as $label)
						$flattened[]=$label;
					}
					if (is_array($related) && count($related))
					{
						foreach($related as $label)
						$flattened[]=$label;
					}
				}
				
				usort($flattened,'sort_by_item_length');
				
				if ($DEBUG)
				{
					$RDFLOG.="<br>flatten_sort_skos_obj_to_array returns: ";
					foreach($flattened as $f) $RDFLOG.="<br>$f";
				}
			}
			return $flattened;
		} // flatten_sort_skos_obj_to_array
		
		
		
		function sort_by_item_length($a,$b)
		{
    		return strlen($b)-strlen($a);
		}	
		
		
		
		function tell_skos_subjects($skos_subjects, $TITLE)
		{
			global $RDFLOG;
	 		if (is_array($skos_subjects) && ($c=count($skos_subjects)))
			{
				
				$RDFLOG.="<hr><i>$c $TITLE</i>";
				foreach($skos_subjects as $s=>$EXPANSIONS)
				{
					
					foreach($EXPANSIONS as $SKOS)
					{
						list($src_name,$srcuid,$src_data_fresh,$broaders,$narrowers,$related)=$SKOS;
						$RDFLOG.= "<br><b>$src_name</b> ($srcuid).SKOS ($s):";
						
						if (($bc=is_array($broaders) * count($broaders))>0)
						{
							$RDFLOG.="<br><i>$bc Broaders:</i>";
							foreach($broaders as $b)
							$RDFLOG.="<br>&nbsp;&nbsp; $b";
						}
						
						if (($nc=is_array($narrowers) * count($narrowers))>0)
						{
							$RDFLOG.="<br><i>$nc Narrowers:</i>";
							foreach($narrowers as $n)
							$RDFLOG.="<br>&nbsp;&nbsp; $n";
						}
	
						if (($rc=is_array($related) * count($related))>0)
						{
							$RDFLOG.="<br><i>$rc Related:</i>";
							foreach($related as $r)
							$RDFLOG.="<br>&nbsp;&nbsp; $r";
						}
					}
				} // EXPANSIONS
				$RDFLOG.="<hr>";
			}
		}
		
		
	
		/**
		 * Guarantees the triples navigation
		 * 
		 * @param $pagetitle
		 * @param text $uid - the univeral id used to search for triples
		 * @param $ext - indicates whether the call is coming from an extrnal aqeuired rodin pool like europeana
		 */
		function display_interactive_lod_entity_triples($uid,$pagetitle,$namespace_short='rodin',$seeonly)
		{
			$DEBUG=0;
			global $RODINRDFSTORENAME;
			global $HOST;

			if($DEBUG)
			{			
				print "<br>display_interactive_lod_entity_triples calling new RDFprocessor(null, 2, $HOST)";
			}
			
			if (($rdfprocessor = new RDFprocessor(null, 2, $HOST)))
			{
				if (!$namespace_short)
						$namespace_short=RDFprocessor::$ownnamespacename;
				$word_id = $namespace_short.':'.$uid;
				
				if($DEBUG)
				{			
					print "<hr> store:<br>"; var_dump($rdfprocessor->store);
					print "<hr> NAMESPACES:<br>"; var_dump(RDFprocessor::$NAMESPACES);
					print "<hr> NAMESPACES_PREFIX:<br>".htmlentities(RDFprocessor::$NAMESPACES_PREFIX);
					print "<hr> TOBECODED64:<br>"; var_dump(RDFprocessor::$TOBECODED64);
					print "<hr> importGraph:<br>"; var_dump(RDFprocessor::$importGraph);
				}				
				
				print display_interactive_triples(	$st=$rdfprocessor->store,
																						$ns=RDFprocessor::$NAMESPACES,
																						$np=RDFprocessor::$NAMESPACES_PREFIX,
																					 	$tbc=RDFprocessor::$TOBECODED64,
																						$graph=RDFprocessor::$importGraph,
																						$seeonly,
																						$word_id,
																						$uid,
																						$pagetitle,
																						$namespace_short,
																						$tableclass='triplepagetable_'.$namespace_short);
			}
			else fontprint("<br>display_interactive_lod_entity_triples - error - no RDF store available","red"); 
		} // print_triplespage
	
	
	
	
	/**
	 * return a text with the triples from $storename using QUERY
	 * or all triples from store rodin
	 * 
	 * color on $namespace_short
	 * 
	 * @param $RDFenhancement
	 * @param $word_id
	 * @param $pagetitle
	 * @param $namespace_short
	 * @param $tableclass
	 */
	function display_interactive_triples(	&$store,
																				&$NAMESPACES,
																				$NAMESPACES_PREFIX,
																				&$TOBECODED64,
																				$GRAPH,
																				$seeonly, // limit to $p containing $seeonly
																				$word_id,
																				$token,
																				$pagetitle,
																				$namespace_short,
																				$tableclass='triplepagetable')
	{
		$DEBUG=0;
		global $RODINLOGO;
		global $PROT;
		global $URL_MANTIS;
		global $lodLABHOMEPAGEURL;
		global $RODINUTILITIES_GEN_URL;
		global $LINK2RODINSPARQLENDPOINT;
		global $RODINRDFSTORENAME;
		$LODENDPOINT_ICON = "$RODINUTILITIES_GEN_URL/images/arrow_link.png";
		$LODENDPOINT_LINK="<img src='$LODENDPOINT_ICON' width='15'>";
		$SPARQLQUERY 	= get_navi_sparql_query($word_id,$token,$NAMESPACES_PREFIX,true); 
		$LODENDPOINT_URL="$LINK2RODINSPARQLENDPOINT?storename=$RODINRDFSTORENAME&QUERY=".urlencode($SPARQLQUERY);
		$LODENDPOINT_TITLE="Click to open dbRODIN LoD SPARQL endpoint on \"$word_id\"";
    $LODENDPOINT_HREF="<a href='$LODENDPOINT_URL' target='_blank' title='$LODENDPOINT_TITLE'>$LODENDPOINT_LINK</a>";
		
		if($DEBUG)
		{
			print "<br><br><hr>display_interactive_triples";
			if(1)
			{print "<br>store: <br>";var_dump($store);print "<br>";}
		}
		
		
		//Descript what kind of entity we are displaying right now:
		switch($namespace_short)
		{
			case 'rodin_a': $METATYPED='<b>annotation</b> '; break;
			case 'rodin_e': $METATYPED='<b>imported</b> '; break;
			case 'rodin': $METATYPED=''; break;
			default: $METATYPED='?';
		}
		//Look and feel
		$TRIPLEPAGECLASS='triplepage-'.$namespace_short;
		
		
		//$SORTLITERALS = array('foaf:name', 'rodin:id','dbo:Place','dbo:birthDate','dbo:deathDate','rodin:profession','dc:description','dc:isReferencedBy','dc:creator');
	  list($ns_short,$short_uid)=explode(':',$word_id);
		$w_full=correct_rodin_url(  get_full_url($word_id,$NAMESPACES), $NAMESPACES  );
		$w_target=(strstr($word_id,'rodin:')||strstr($word_id,'rodin_e:')||strstr($word_id,'rodin_a:'))?"":"target='_blank'";
		$WORDID_HREF="<a class='about' href='$w_full' $w_target><small>$ns_short:</small>$short_uid</a>";
		
		//print "<br>get_triples_as_navi_page getting triples for '$word_id' (about: $about)";
		
		$p_o 				 = get_entity_infos2				($NAMESPACES,	$NAMESPACES_PREFIX, $store,	$word_id, $token);
		
		if (!strstr($token,'*') && $seeonly=='') // makes no sense: no entity identified
		{
			$s_p_inverse = get_entity_infos_inverse2($NAMESPACES,	$NAMESPACES_PREFIX,	$store,	$word_id, $token);
		}	
		
		
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
			
			if ($seeonly && !strstr($token,'*'))
			{
				$ENTITY_DESCRIPTION = "An {$METATYPED}entity $EVTL_ENTITY_TYPE_DEF ";
				$ENTITY_DESCRIPTION.="<br>Filtered view on '<b>$seeonly</b>'";
				$ADVICE=" (please start navigating by clicking on one displayed resource)";
			}
			else if (strstr($token,'*'))
			{
				$ENTITY_DESCRIPTION= "<br>LoD Filtered view on '<b>$token</b>'";
				$ADVICE=" (please start navigating by clicking on one displayed resource)";
			}
			else {
				$ENTITY_DESCRIPTION="An {$METATYPED}entity $EVTL_ENTITY_TYPE_DEF ";
			}
			
			
$TRIPLEPAGE=<<<EOP
		<table id='triplepageframe' width='100%' height='100%' cellpadding="0" border=0>
		<tr height="5">
			<td align='left' valign='top'>
				<span class='triplepagei'>$pagetitle</span>
			</td>
			<td align='right' valign='top'>
			<span class='$TRIPLEPAGECLASS'>$URL_MANTIS</span>
			</td>
		</tr>
		<tr height='20'>
		<td valign='top'>
    <div id="header" >
			  <h2 id="title">About $WORDID_HREF $LODENDPOINT_HREF</h2>
		  	<div class="page-resource-uri" title="An entity is connotated by direct (and inverse) properties obtained traversing the graph centerd on the subject '$word_id'">
			   $ENTITY_DESCRIPTION 
			    from graph <a href="$GRAPH" target='_blank'>$GRAPH</a> $ADVICE
		    </div>
		</div> <!-- header -->
		</td>
		<td valign='top' align='right'>
			<div id='headerlogo'>
				<table>
				<tr>
				<td>
					<!--a href='$lodLABHOMEPAGEURL' title="Click to go back to RODIN's lod LAB homepage"-->
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

	$i=1;
	foreach($p_o as $p=>$ooo)
	{
		//Display only if wished:
		if ($seeonly=='' || strstr($p,$seeonly))
		{
		
			$HTML_NUMOFOBJECTS='';
			$NUMOFOBJECTS=count($ooo);
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
		
			$i++;
			if ($p<>'rdf:type')
			{
				 
				if ($p=='rodin_a:delivered')
				{
					if ($NUMOFOBJECTS > 5)
					{
						$HTML_NUMOFOBJECTS="<span class='numofobjects'>($NUMOFOBJECTS)</span>";
					}
				}
				$even = !($i%2); 
				$bgclass=($even?'even':'odd').'_'.$namespace_short;
			
				$p_full=correct_rodin_url(  get_full_url($p,$NAMESPACES), $NAMESPACES  );
				$pns=get_ns($p);
				$pshort=str_replace("$pns:",'',$p);
				$class="literal";
				$p_target=(strstr($p,'rodin:')||strstr($p,'rodin_e')||strstr($p,'rodin_a'))?"":"target='_blank'";
				//show section on all same content:
				$P_HREF="<a href='$p_full' $p_target><small>$pns:</small>$pshort</a> $HTML_NUMOFOBJECTS";
$TRIPLEPAGE.=<<<EOP
	<tr class='$bgclass'><td valign='top' class='property' style='white-space:nowrap'>
	<span class="predicate">$P_HREF</span></td>
	<td><ul class='rodinpedia'>
EOP;
				foreach($ooo as $obj)
				{
					$O_TITLE='';
					list($o,$ns,$o_full,$o_is_literal,$o_lang) = $obj;
			
					if ($o_is_literal)
					{
						//is o a triple representation?
						if ($p=='rodin_a:delivered' && ($o_is_triple=(strstr($o,',')))) // contains commas
						{
							$O_TITLE=" title='This is a triple (list) itself - a kind of meta information on the triples delivered by this object' ";
							$O_CLASS='triple';
						}
						else 
							$O_CLASS='object';
						
						$O_HREF=decode_64_literal($p,$o,$TOBECODED64);
						//Check url -> make clickable:
						$O_HREF   = substr($row['o'],0,4)==$PROT
											?"<a href='".$o."' target='_blank' title='click to open url in a new tab'>$O_HREF</a>"
											:$O_HREF;
					} else {
						$o_full = correct_rodin_url($o_full,$NAMESPACES);
						$o_short=str_replace("$ns:",'',$o);
						$o_target=(strstr($o,'rodin:')||strstr($o,'rodin_e:')||strstr($o,'rodin_a:'))?'':"target='_blank'";
						//print "<br>o=$o o_target=$o_target";
						$O_HREF="<a href='$o_full' $o_target><small>$ns:</small>$o_short</a>";
					} 
					$TRIPLEPAGE.=<<<EOP
	<li><span class="$O_CLASS" $O_TITLE>$O_HREF</span></li>
EOP;
				}
			} // skip
		} // foreach($p_o as $p=>$ooo)
	} // seeonly
	if (count($s_p_inverse))
	foreach($s_p_inverse as $p=>$sss)
	{
				//Display only if wished:
		if ( (!strstr($token,'*')) && ($seeonly=='' || strstr($p,$seeonly)))
		{
			$i++; $even = !($i%2); 
			$bgclass=($even?'even':'odd').'_'.$namespace_short;
			
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
			$p_full=correct_rodin_url(  get_full_url($p,$NAMESPACES), $NAMESPACES  );
			$pns=get_ns($p);
			$pshort=str_replace("$pns:",'',$p);
			$class="literal";
			$p_target=(strstr($p,'rodin:')||strstr($p,'rodin_e:')||strstr($p,'rodin_a:'))?"":"target='_blank'";
			//show section on all same content:
			$P_HREF="<a href='$p_full' $p_target>inv(<small>$pns:</small>{$pshort})</a>";
	$TRIPLEPAGE.=<<<EOP
	<tr class='$bgclass'><td valign='top' class='property' style='white-space:nowrap'>
	<span class="predicate">$P_HREF</span></td><td><ul class='rodinpedia'>
EOP;
		foreach($sss as $obj)
		{
			list($s,$ns,$s_full,$_,$_) = $obj;
			
			
			$s_full = correct_rodin_url($s_full,$NAMESPACES);
			$s_short=str_replace("$ns:",'',$s);
			$s_target=(strstr($s,'rodin:')||strstr($s,'rodin_e:')||strstr($s,'rodin_a:'))?'':"target='_blank'";
			//print "<br>o=$o o_target=$o_target";
			$S_HREF="<a href='$s_full' $s_target><small>$ns:</small>$s_short</a>";
			
			$TRIPLEPAGE.=<<<EOP
	<li><span class="object">$S_HREF</span></li>
EOP;
				}
		} // foreach($s_p_inverse as $p=>$sss)
	} // seeonly
		
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
	} // display_interactive_triples	
		
	
		
		
		
	
	/**
	 * in case the url is rodin: (the coresponding long for rodin)
	 * substitute the last word /bla with /?token=bla
	 */
	function correct_rodin_url($url,&$NAMESPACES)
	{
		
		$DEBUG=0;
		global $HOST, $PORT;
		//print "<br>correct_rodin_url ($url) returning ...";
		//var_dump($NAMESPACES);
		
		if ($DEBUG) print "<hr>correct_rodin_url($url)";
		
		
		if (strstr($url,$NAMESPACES{'rodin'}))
		{
			$path_parts=pathinfo($url);
			$filename=$path_parts['filename'];
			$dirname=$path_parts['dirname'];
			if (substr($dirname,strlen($dirname)-1,1)<>'/') 
			$dirname.='/';
			
			//in case rodin house address matches ... correct:
			if ($DEBUG)
			{
				print "<br><br>dirname: $dirname";
				print "<br>filename: $filename";
				print "<br>rodin_a: ".$NAMESPACES{'rodin_a'};
			}
			if (strstr($dirname,$NAMESPACES{'rodin_e'}))
			{
				$delta_rodin= str_replace($NAMESPACES{'rodin_e'},'',$dirname);
			}
			else {
				// print "<br>TRY ELSE ($dirname)(".$NAMESPACES{'rodin_a'}.")";
				if (strstr($dirname,$NAMESPACES{'rodin_a'}))
					$delta_rodin= str_replace($NAMESPACES{'rodin_a'},'',$dirname);
				else if (strstr($dirname,$NAMESPACES{'rodin'}))
					$delta_rodin= str_replace($NAMESPACES{'rodin'},'',$dirname);
			}
			
			
			if ($delta_rodin)
			{
				// print "<br>correct_rodin_url($url) (delta=($delta_rodin))<br>filename=$filename<br>dirname=$dirname";
				
				//Attach delta to filename
				$dirname=str_replace($delta_rodin,'',$dirname);
				$filename=str_replace("//","/",$delta_rodin.'/'.$filename); //Assume always '/'
				// print "<br>corrected: filename=$filename dirname=$dirname";
			}
			$url=$dirname.'?token='.$filename;
		}

		//Add finally port to allow access:
		$url=str_replace($HOST,"$HOST:$PORT",$url);
		
		if ($DEBUG) print "<br>corrected url: $url";
		
		return $url;
	}
	
	
	
	
	function read_rodin_label($entity, &$store,&$NAMESPACES)
	{
		return lookup_firstvalue($entity,'rodin:label', $store, $NAMESPACES);
	}
	
	
	//Convenience function
	function exists_in_store($s,$p, &$store,&$NAMESPACES)
	{
		return (lookup_firstvalue($s,$p,$store,$NAMESPACES) == null);
	}
	
	
	
	
	/**
	 * retuns the value of 
	 */
	function lookup_firstvalue($s,$p, &$store,&$NAMESPACES)
	{
		$o=null;
		$QUERY = " select ?o { $s $p ?o . } limit 1";
		
		//print "<br>Query:<br>".str_replace("\n","<br>",htmlentities(urldecode($QUERY)));
		
		if ($rows = $store->query($QUERY, 'rows')) 
		{			
				foreach($rows as $row) 	
				{
					$o_full=$row['o'];
					$o = separate_namespace($NAMESPACES,$o_full,':',false);
					//print " O FOUND ";
					break;
				}
		}
		
		//print "<br>lookup_firstvalue($s,$p) returning ($o)";
		
		return $o;
	} // lookup_firstvalue
	


	function get_ARC_triples_for_viz(&$store,$searchterm,&$NAMESPACES)
	##################################
	# 
	# Computes the query to SKOS $verb
	# $verb= related, broader, narrower
	{
		if (!$searchterm)
		$QUERY=<<<EOQ
		select distinct ?s ?p ?o 
		{ ?s ?p ?o .}
EOQ;
		else {
			$rodin_a_ns_url =$NAMESPACES{'rodin_a'};
			
			$QUERY=<<<EOQ
PREFIX rodin_a: <$rodin_a_ns_url>
select ?s ?p ?o
{
	{
	  ?s rodin_a:search_term "$searchterm" .
	  ?s ?p ?o .
	}
	UNION
	{
	  ?sm1 rodin_a:search_term "$searchterm" .
	  ?sm1 ?pm1 ?om1 .
	  FILTER (?om1 = ?s ) .
	  ?s ?p ?o .
	}
	UNION
	{
	  ?sm2 rodin_a:search_term "$searchterm" .
	  ?sm2 ?pm2 ?om2 .
	  FILTER (?om2 = ?sm1 ) .
	  ?sm1 ?pm1 ?om1 .
	  FILTER (?om1=?s) .
	  ?s ?p ?o .
	}
}			
EOQ;
		}
	
		$result=array();
		$rows = $store->query($QUERY, 'rows');
		
		return $rows;
	}





	
	function get_ARC_triples(&$store)
	##################################
	# 
	# Computes the query to SKOS $verb
	# $verb= related, broader, narrower
	{
		
		$QUERY=<<<EOQ
		select ?s ?p ?o 
		{ ?s ?p ?o .}
EOQ;
	
		$result=array();
		$rows = $store->query($QUERY, 'rows');
		
		//var_dump($rows);
		
		return $rows;
	}
	
	
	
	
	function get_entity_infos_inverse2(	&$NAMESPACES,
																			$NAMESPACES_PREFIX,
																			&$store,
																			$subject_uid_short,
																			$token )
	{
		return get_entity_infos2(	&$NAMESPACES,
															$NAMESPACES_PREFIX,
															&$store,
															$subject_uid_short,
															$token,
															$direct = false );
	} // get_entity_infos_indirect
	
	
	
	
	
	
		/**
	 * Query the local store $store
	 * Returns a vector of array($o,$ns,$o_full,$o_is_literal,$o_lang) depending from $direct
	 * corresponding to subject_uid_short as subject
	 * @param RDFprocessor& $RDFenhancement obj containing rdf store information 
	 * @param string $subject_uid_short ID to be use to find triples
	 * @param bool $direct flag indicating whether the direct or the inverse relationshould be computed
	 * 
	 * In case of $direct=true returns pairs ($subject_uid_short ?p ?o)
	 * In case of $direct=false returns pairs (?s ?p $subject_uid_short)
	 */
	function get_entity_infos2(	&$NAMESPACES,
															$NAMESPACES_PREFIX,
															&$store,
															
															$subject_uid_short,
															$token,
															$direct = true)
	{
		$DEBUG=0;
		if (!$subject_uid_short) {fontprint("System error: subject_uid_short is empty!",'red');}
		//else fontprint("subject_uid_short: ($subject_uid_short)",'green');
		
		if (!$store)
		{
			print "<hr>get_entity_infos2 (direct=$direct): Store is empty!!!<br>";
			debug_print_backtrace();
		}
		
		$p_o=array();
		//Retrieve triple information on author
		//Recognize wild card and use FILTER if needed
		$QUERY 	= get_navi_sparql_query($subject_uid_short,$token,$NAMESPACES_PREFIX,$direct);
		if ($DEBUG)
		{
				print "<br><br><hr>get_entity_infos2 ($subject_uid_short) (direct=$direct) ... <b><br>".show_sparql_query($QUERY).'</b>';
		
				if(1)
				{print "<br>STORE: "; var_dump($store); print "<br><br>";}
		}
		if ($rows = $store->query($QUERY, 'rows')) 
		{			
				$i=0;
				foreach($rows as $row) 	
				{	$i++;
					$p_full=$row['p'];
					$p = separate_namespace($NAMESPACES,$p_full,':',false);
				
					if($direct)
					{
						$o_full=$row['o'];
						$o = separate_namespace($NAMESPACES,$o_full,':',false);
						$o_is_literal=($o==$o_full);
						$o_lang=$row['o_lang']; // if any
						$ns=$o_is_literal?'':get_ns($o);
						//Prepare obj to be stored under $p:
						$obj=array($o,$ns,$o_full,$o_is_literal,$o_lang);
						
						if ($DEBUG)
						{
							print "<br><b>p:</b>$p, <b>o:</b>$o";
						}
					} 
					else 
					{
						$s_full=$row['s'];
						$s = separate_namespace($NAMESPACES,$s_full,':',false);
						$ns=get_ns($s);
						//Prepare obj to be stored under $p:
						$obj=array($s,$ns,$s_full,false,null);
						if ($DEBUG)
						{
							print "<b>s:</b>$s, <br><b>p:</b>$p";
						}
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
				} // foreach
			}
	//var_dump($p_o);
	return $p_o;

	} // get_entity_infos2
	
	
	function get_navi_sparql_query($subject_uid_short,$token,$NAMESPACES_PREFIX,$outgoing)
	{
		
		if (strstr($subject_uid_short,$W='*')
		|| strstr($subject_uid_short,$W='%'))
			$QUERY= $outgoing
							? $NAMESPACES_PREFIX .	" \nselect ?p ?o \n{ ?s ?p ?o . FILTER Regex(?s =\"".str_replace($W,'',$token).'") . }'
							: $NAMESPACES_PREFIX .	" \nselect ?s ?p \n{ ?s ?p ?o . FILTER Regex(?o =\"".str_replace($W,'',$token).'") . }';
		else 
			$QUERY= $outgoing
							? $NAMESPACES_PREFIX .	" \nselect ?p ?o \n{ $subject_uid_short ?p ?o . }"
							: $NAMESPACES_PREFIX .	" \nselect ?s ?p \n{ ?s ?p $subject_uid_short . }";
							
		return $QUERY;
	} // get_navi_sparql_query
	
	
			
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
		$DEBUG=0;
	  $SEP=':';
	
		if ($DEBUG) print "<br>prettyprintURI(".htmlentities($URIexpr).")";
	
	  list($left,$right,$literal) = analyzeURI($URIexpr,$namespaces);
	  
		if ($DEBUG) print "<br>analyzeURI(): left=(".htmlentities($left).") right=(".htmlentities($right).") literal=(".htmlentities($literal).")";
		
	  // if ($left)
	  // {
	  // $left= separate_namespace($namespaces,$left.$SEP,':',true);
	  // }  
	  
	  //print "<br>prettyprintURI($URIexpr)=($left,$right,$literal)";
	  // <span class='urigray'>$left</span><span class='uribold'>$right</span> <span class='uriliteral'>$literal</span>

  return <<< EOR
 $left<span class='uribold'>$right</span> <span class='uriliteral'>$literal</span>
EOR;
	} // prettyprintURI

	
	
	
	

	function printURI($URIexpr,&$namespaces)
	{
	  $SEP='';
	
	  list($left,$right,$literal) = analyzeURI($URIexpr,$namespaces);
	  
	  if ($left) $left = separate_namespace($namespaces,$left.$SEP,':',false);
		
	 	return $left.$SEP.$right;
	}
	
	/**
	 * separate an expression into namespace and term
	 * in case of href=true, namespace is a href which leads to its definition page
	 * returns the term without the namespace
	 */
	function separate_namespace(&$namespaces,$term,$sep=':',$href=true)
	{
		$DEBUG=0;
		global $PORT;
		global $HOST;
		global $RODINSEGMENT;
	  //OPTIMIZE
	  if (!count($namespaces)) fontprint("<br>separate_namespaces(".htmlentities($term).") called with EMPTY namespaces",'red');
	  if ($DEBUG) print "<br><br>separate_namespace(".htmlentities($term).") ... Test substitution on <b>$term</b>";
	  if (!(strstr($term,'http')))
	  {
	    if ($DEBUG) print "<br>Exit: Term '<b>$term</b>' already done (no http(s))";
	    return $term;
	  }
	  else
	  {
	  	if ($DEBUG) fontprint("<br>separate_namespaces(".htmlentities($term).") ",'blue');
	    foreach($namespaces as $ns=>$expr)
	    {
	      $expr2=str_replace("/","\/",$expr);
	      $pattern="/$expr2(.*)/";
				
	      if ($DEBUG) print "<br>Pattern $pattern url $url";
	
	      if (preg_match($pattern,$term,$match))
	      {
	      	//Set the link to be used:
					$exprH=htmlentities($expr);
		      //ATTENTION: GERMAN UMLAUTS ... NOT HANDLED HERE?
	        $matched=1;
	        $nakedterm=$match[1]; //cut first "/"
	        
	        if($DEBUG) print "<br>YES nakedterm: $nakedterm";
	        if ($nakedterm[0]=='/')
	            $nakedterm=substr($nakedterm,1);

					$url=str_replace($HOST,"$HOST\\:$PORT",$expr2)."?token=$ns";
					$ns_title_span_expl=$href
		      									 ?"<a class=\"urigray\" href='#' target=\"_blank\" onclick=\"open_ns('$url');return false;\" title=\"Click to explore namespace definition\n$exprH\n(if existing) in a new tab\">$ns</a>"
														 :$ns;
	
	        $returnterm=$ns_title_span_expl.$sep.$nakedterm;

	        if ($DEBUG) print "<br> YES separated URL (href=$href): ($HOST:$PORT) $url <b>returnterm</b>(".htmlentities($returnterm).")";
	        break;
	      }
				else {
					if ($DEBUG)
						print "<br> NO ";
				} 
					
	    }
	
	    if (!$matched) 
	    {
	      $returnterm=$term;
	      if ($DEBUG) print " ERROR applying $pattern -> $returnterm<br>";
	    } 
	    else 
	    {
	    	if ($DEBUG) print " SUCCESS: $term -> $returnterm<br>";
			}
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
	    $left= separate_namespace($namespaces,$left.$SEP,':',true);
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
	function get_cached_related_subjects_from_sparql_endpoint(	$subject,
																															$src_name,
																															$sds_sparql_endpoint,
																															$sds_sparql_endpoint_params,
																															$cache_id,
																															&$NAMESPACES,
																															$lang,
																															$sid,
																															$limit=5 	)
	{
		$DEBUG=0;
		global $RDFLOG;
		
		$subject_arr = array();
		if (!$cache_id) $cache_id="relatedsubjects.$src_name.$subject.$lang.$limit";
		
		Logger::logAction(27, array('from'=>'get_cached_related_subjects_from_sparql_endpoint','msg'=>"Start with cache_id $cache_id"),$sid);
		$dce_ns_url=$NAMESPACES{'dce'};
		
		$sparqlquery=<<<EOQ
PREFIX dce: <$dce_ns_url>

select ?srelated
{
  ?s ?_ "$subject" .
  ?s dce:subject ?srelated .
	FILTER(?srelated != "$subject") .
} limit $limit
EOQ;

		//print "<br> get_cached_related_subjects_from_sparql_endpoint cache_id=$cache_id";

		if ($DEBUG)
		$RDFLOG.= "<br>get_cached_related_subjects_from_sparql_endpoint: cache_id=($cache_id";

		$url_endpoint= $sds_sparql_endpoint
								 	.'?query='.urlencode($sparqlquery)
									.'&'.$sds_sparql_endpoint_params;
		if ($DEBUG) {
      	$RDFLOG.= "<br><br>USING SOLR CACHED SOURCE for subject expansion<br>".str_replace("\n","<br>",htmlentities(urldecode($url_endpoint)))."<br><br>";
     }
		
		//cache_src_response($cache_id,$xml_src_content)
    //Logger::logAction(25, array('from'=>'get_cached_related_subjects_from_sparql_endpoint','msg'=>'Started with cacheid:'.$cache_id),$this->sid);
    list($xmlCached_content,
            $creationtimestamp,
            $age_in_sec,
            $max_age_in_sec, // this is always set!! even if no data
            $expiring_in_sec) = get_cached_src_response($cache_id);

    if (! sparql_cached_content_quality_control($xmlCached_content,$src_name,$age_in_sec,$max_age_in_sec))
    { // ask service and rebuild cache
       
      $timestamp=date("d.m.Y H:i:s");
      $timestamp0=time();
      $age_in_sec=0;
      $expiring_in_sec=$max_age_in_sec;
      
		 	if ($DEBUG) {
      	$RDFLOG.= htmlprint("<br><br>CALLING REMOTE SOURCE for subject expansion ".$url_endpoint."<br><br>",'red');
      }
			Logger::logAction(27, array('from'=>'get_cached_related_subjects_from_sparql_endpoint','msg'=>"Open $url_endpoint"),$sid);
			
			if ($DEBUG) $RDFLOG.="<br>Calling $url_endpoint";
			$xml_content=get_file_content($url_endpoint);
			
			if ($DEBUG) $RDFLOG.="<br>Getting: (((".htmlentities($xml_content)."))))";
			cache_src_response($cache_id,$xml_content);
			$xmlCached_content = $xml_content;
		}
		else {
			if ($DEBUG)
			{
      	$RDFLOG.= htmlprint("<br><br>USING CACHED SOURCE CONTENT for subject expansion ".$url_endpoint."<br><br>",'green');
			}
		}

		if ($DEBUG) $RDFLOG.="<br>NOW CHECKING VALID XML";
		$valid_xml=true;
		$xml_content_len=count($xmlCached_content);
		if (datasource_error($xmlCached_content,$src_name))
		{
			fontprint("<hr>ERROR FROM DATASOURCE $src_name called on subject ($subject) for related subjects:"
						."<br>Check for CACHE?<br>"
						."<br>QUERY:<br>"
						.str_replace("\n","<br>",htmlentities($sparqlquery)),'red')
						."<br><br>Using url:"
						."<br>$url_endpoint";
			$valid_xml=false;
	 		Logger::logAction(27, array('from'=>'get_cached_related_subjects_from_sparql_endpoint','msg'=>$msg="Invalid XML retrieved ($xml_content_len bytes)"),$sid);
			if ($DEBUG) $RDFLOG.="<br>$msr";
		}
		
		if ($valid_xml)
		{
			//scan/open $xmlCached_content for later use
			if ($DEBUG) $RDFLOG.="<br>VALID XML GOT FROM $src_name:"; //print "<br>XML GOT FROM $src_name:<br>".htmlentities($xmlCached_content);
			$xml_content_len = strlen($xmlCached_content);
			Logger::logAction(27, array('from'=>'get_cached_related_subjects_from_sparql_endpoint','msg'=>"Analyse content ($xml_content_len bytes)"),$sid);
			
			$sxmldom=simplexml_load_string($xmlCached_content,'SimpleXMLElement', LIBXML_NOCDATA);
			
			if ($DEBUG) 
			{
				 print "<hr>ARRAY subext $src_name on '$subject': count=".count($sxmldom->results->result)."<br>";var_dump($sxmldom->results->result);
				$RDFLOG.="<br> <hr>ARRAY subext $src_name on '$subject': count=".count($sxmldom->results->result)."<br> " .print_r($sxmldom->results->result);
			}
			
			if ($sxmldom->results->result)
			{
				foreach($sxmldom->results->result as $_=>$result)
				{
					$rel_sub=trim($related_sub=$result->binding->literal."");
					if ($rel_sub<>'')
					{
						$subject_candidate = $rel_sub;	
						//Add only iff filtered is once and in the same lang	
						if ($DEBUG) $RDFLOG.="<br>CONSIDER nth related subject ($subject_candidate)";
						insert_filtered_once($subject_candidate,$subject_arr,$lang);
					}
				} // foreach
			} else {if ($DEBUG) $RDFLOG.="<br>NO RESULT CHILDREN PARSED IN XML";}
			
			if ($DEBUG)
			{
				$subject_arr_noof=count($subject_arr);
				$RDFLOG.= "<br>RETURNING $subject_arr_noof SPARQL related subjects to $subject from $src_name:";
				foreach($subject_arr as $sub)
				{
					$RDFLOG.= "<br>$sub";
				}
			}
			
			Logger::logAction(27, array('from'=>'get_cached_related_subjects_from_sparql_endpoint','msg'=>"Exit"),$sid);
		} // valid xml
		else {if ($DEBUG) $RDFLOG.="<br>INVALID XML - NO RESULTS DELIVERED";}
		return $subject_arr;
	}	// get_cached_related_subjects_from_sparql_endpoint
		
		
		
		
		
		
		
	/**
	 * enrich the vector
	 * if the filtered version is new to $vector
	 * 
	 * @param text $candidate - a term
	 * @param array $vector - the vector to fill
	 * @param string $lang
	 * @param string $tolerated_lang
	 */
	function insert_filtered_once($candidate,&$vector,$lang,$tolerated_lang='')
	{
		global $RDFLOG;
		$DEBUG=0;
		global $sid; if(!$sid) fontprint('insert_filtered_once: sid not set globally','red');
		
		if ($DEBUG) $RDFLOG.="<br>insert_filtered_once($candidate):";
		if (!discard_subject_word_candidate($candidate))
		{
			//The following is the same as on a triple subject but allows minus sign as trade d'union (natural language)
			if (($subject=filter_as_subject($candidate,'/[0-9&!;\\\.\:_\[\]\(\)]+/')))
			{
				if ($DEBUG) $RDFLOG.="<br>insert_filtered_once() Considering subject '$subject' in language $lang";
				
				if (($langt=detectLanguageAndLog($subject,'insert_filtered_once',$sid))==$lang || ($tolerated_lang<>'' && $langt==$tolerated_lang))
				{
					if (!in_array($subject,$vector))
							$vector[]= $subject;
					//print "<br>TAKE($langt==$lang) SUBJ($subject):";
				 
					if($DEBUG)  
						$RDFLOG.= "<br>TAKE($langt==$lang) SUBJ($subject):";
					else {
						//fontprint("<br>DISC SUBJ($langt<>$lang) SUBJ($subject)",'red');
						if($DEBUG)  $RDFLOG.= htmlprint("<br>DISC SUBJ($langt<>$lang) SUBJ($subject)",'red');
					}
				} // lang
			}
		} else {if ($DEBUG) $RDFLOG.="<br>DISCARD ($candidate)";}
	} // insert_filtered_once
		
		
	/**
	 * Compute related subjects to subject inside a given spqrql source 
	 * and with the SAME document language as $lang
	 *
	 * Note: In this method firstly n documents are queried gathering their doc id
	 * afterwards, foreach of these docid, triples are queried from the remote LOD source
	 * the cache is build upon the subquery results
	 * 
	 * Only subjects and docs in the same language as $lang are imported!
	 * 
	 * @param string $subject
	 * @param string $src_name
	 * @param string $solr_cache_max_age_in_sec
	 * @param string $sds_sparql_endpoint
	 * @param string $sds_sparql_endpoint_params
	 * @param vector &$NAMESPACES
	 * @param string $lang
	 * @param string $sid
	 * @param integer $docslimit
	 *   
	 */	
	function get_cached_triples_on_subject_from_sparql_endpoint(	$subject,
																																$src_name,
																																$solr_cache_max_age_in_sec,
																																$sds_sparql_endpoint,
																																$sds_sparql_endpoint_params,
																																&$NAMESPACES,
																																$lang,
																																$sid,
																																$docslimit=10)
	{
		global $RDFLOG;
		
		$DEBUG=0;
		$subject_arr = array();
		$cache_id="LODDOCexpansion.$src_name.$subject.$lang.$docslimit";		
		$dce_ns_url=$NAMESPACES{'dce'};		
		$epp_ns_url=$NAMESPACES{'epp'};		
		$xml_doc_contents=array();
		$XMLHEADER="<?xml version='1.0' encoding='UTF-8'?>";
	
		//cache_src_response($cache_id,$xml_src_content)
    Logger::logAction(27, array('from'=>'get_cached_triples_on_subject_from_sparql_endpoint','msg'=>'Started with cacheid:'.$cache_id),$sid);
    list($xmlCached_content,
            $creationtimestamp,
            $age_in_sec,
            $max_age_in_sec, // this is always set!! even if no data
            $expiring_in_sec) = $xcached = get_cached_src_response($cache_id,$solr_cache_max_age_in_sec);

		if ($DEBUG && 0) 
		{
			print "<hr>CACHE OBJ for cache_id=($cache_id):<br>"; var_dump($xcached);
		}

    if (sparql_cached_content_quality_control($xmlCached_content,$src_name,$age_in_sec,$solr_cache_max_age_in_sec))
		//Cache ok: auspacken
		{
			//"<doc><id>$url_encoded_docid</id><content>$netto_urlencoded_xml</content></doc>";
			
			$sxmldom=simplexml_load_string($xmlCached_content,'SimpleXMLElement', LIBXML_NOCDATA);
			foreach($sxmldom->doc as $XMLDOC)
			{
				//print "<hr>";
				$ddoc_id=urldecode(utf8_decode($XMLDOC->id.""));
				$ddoc=urldecode(utf8_decode($XMLDOC->content.""));
				//print "<hr>DOCXXX: ((".htmlentities($ddoc)."))";
				$xml_doc_contents{$ddoc_id} = $ddoc;
			}
		} // xmldom
		
		##############################################################################
		##############################################################################
		##############################################################################
		else // cache nok
    { // -> call service and re-build cache
      $used_cache=false;
      $timestamp=date("d.m.Y H:i:s");
      $timestamp0=time();
      $age_in_sec=0;
      $expiring_in_sec=$max_age_in_sec;
      if ($DEBUG) {
      	$RDFLOG.= "<br><br>CALLING REMOTE SOURCE DIRECTLY!! ".$url_endpoint."<br><br>";
      }
			
			Logger::logAction(27, array('from'=>'get_cached_triples_on_subject_from_sparql_endpoint','msg'=>"Open $url_endpoint"),$sid);
			
			$subject_buddy= ctype_upper($subject[0])
										? ''
										: strtoupper($subject[0]).substr($subject,1)
										;
			if ($subject_buddy)
			$sparqlquery_documents=<<<EOQ
PREFIX dce: <$dce_ns_url>
select distinct ?docid
{
{
?docid dce:subject "$subject_buddy" .
?docid dce:language ?lang . 
FILTER regex(?lang, "$lang") .
}
UNION
{
?docid dce:subject "$subject" .
?docid dce:language ?lang . 
FILTER regex(?lang, "$lang") .
}
} limit $docslimit
EOQ;
			else
			$sparqlquery_documents=<<<EOQ
PREFIX dce: <$dce_ns_url>
select distinct ?docid
{
?docid dce:subject "$subject" .
?docid dce:language ?lang . 
FILTER regex(?lang, "$lang") .
} limit $docslimit
EOQ;
			$url_endpoint_docs= $sds_sparql_endpoint
								 	.'?query='.urlencode($sparqlquery_documents)
									.'&'.$sds_sparql_endpoint_params;
		
			if ($DEBUG)
			$RDFLOG.= "<br>get_cached_triples_on_subject_from_sparql_endpoint:<br><br>sparqlquery_documents:<br>".show_sparql_query($sparqlquery_documents);
				
			$xml_content = eval_remote_sparql_query_as_xml($sds_sparql_endpoint,$sds_sparql_endpoint_params,$sparqlquery_documents);
			
			$CACHE_CONTENT="<docs>";
			try {
				$sxmldom=simplexml_load_string($xml_content,'SimpleXMLElement', LIBXML_NOCDATA);
			}
			catch (Exception $e)
			{
				fontprint("<br>Error loading result from $src_name - got:",'red');
				fontprint("<br>".htmlentities($xml_content)." ",'black');
			}
			if (!$sxmldom)	
				Logger::logAction(27, array('from'=>'get_cached_triples_on_subject_from_sparql_endpoint','msg'=>"Invalid XML retrieved ($xml_content_len bytes)"),$sid);
			else // valid docs XML
			{
				##############################################
				##############################################
				foreach($sxmldom->results->result as $result)
				{
						$docid=$result->binding->uri.'';
						$docsid[] = $x = separate_namespace($NAMESPACES,$docid,':',false);
						//print "<br>docid:<br>$x";
				} // foreach						
				##############################################
				##############################################
					
				if (is_array($docsid) && count($docsid))
				{
					$k=0;
					//foreach doc: fetch its triples
					##############################################
					##############################################
					foreach($docsid as $docid)
					##############################################
					##############################################
					{
						$k++;
						//We have to backquote the slash in docid to be able 
						//to get some results from the europeana sparql engine
						$docid_quotedslash=str_replace("/","\/",$docid);
						$url_encoded_docid=urlencode($docid);
						
						if ($DEBUG)
						{
							$RDFLOG.="<br>Fetching triples for $k. docid $docid";
						}
						$sparqlquery_triples=<<<EOQ
PREFIX dce: <$dce_ns_url>
PREFIX epp: <$epp_ns_url>
select distinct ?p ?o
{
	$docid_quotedslash ?p ?o .
}
EOQ;
						if ($DEBUG)
							$RDFLOG.= "<br>get_cached_triples_on_subject_from_sparql_endpoint:<br><br>sparqlquery_triples:<br>".show_sparql_query($sparqlquery_triples);
							
						$xml_doc_contents{$docid} = $xml = eval_remote_sparql_query_as_xml($sds_sparql_endpoint,$sds_sparql_endpoint_params,$sparqlquery_triples);
						//Extract the real content od this xml
						$netto_urlencoded_xml=urlencode(str_replace($XMLHEADER,'',$xml));
						
						if ($DEBUG && 0)
						{
							$RDFLOG.="<br>TRIPLE CONTENT for docid $docid:<br>".($netto_urlencoded_xml);	
						}						
						$CACHE_CONTENT.="<doc><id>$url_encoded_docid</id><content>$netto_urlencoded_xml</content></doc>";
				  } // foreach
				}
				$CACHE_CONTENT.="</docs>";
				cache_src_response($cache_id, $CACHE_CONTENT);
			}// valid docs CACHED XML
		} // call service and rebuild cache
		
		###############################
		# Process $xml_doc_contents[] 
		# coming directly or via CACHE
		###############################
		
	  if (is_array($xml_doc_contents) && count($xml_doc_contents))
		{
			if ($DEBUG) $RDFLOG.="<br>SCANNING TRIPLES INSIDE XML DOCUMENTS";
			foreach($xml_doc_contents as $docid => $xml_doc_content)
			{
				if ($DEBUG) $RDFLOG.="<br>SCANNING TRIPLES INSIDE XML DOCUMENT (((".htmlentities($xml_doc_content).")))";
				
				$sxmldom=simplexml_load_string($xml_doc_content,'SimpleXMLElement', LIBXML_NOCDATA);
				
				if (! $sxmldom)
			 		Logger::logAction(27, array('from'=>'get_cached_triples_on_subject_from_sparql_endpoint','msg'=>"Invalid XML retrieved ($xml_content_len bytes)"),$sid);
				else // valid xml for doc triples
				{
					Logger::logAction(27, array('from'=>'get_cached_triples_on_subject_from_sparql_endpoint','msg'=>"Analyse content ($xml_content_len bytes)"),$sid);
					foreach($sxmldom->results->result as $result)
					{
						//print "<hr>";
						$p=$result->binding->uri.'';
						$p = separate_namespace($NAMESPACES,$p,':',false);
						//print "<br><br>p:<br>$p";
									
						$o_literal= $result->binding[1]->literal.'';
						$o_uri= $result->binding[1]->uri.'';
						$o_bnode= $result->binding[1]->bnode.'';
						
						if ($o_uri)
							$o_uri = separate_namespace($NAMESPACES,$o_uri,':',false);
						
						$o=$o_literal
							?l($o_literal) //Use special quote function
							:($o_uri
							 ?$o_uri
							 :$o_bnode);
						
						//Check subject language (since not defined in europeana)		 
						if ($p<>'dce:subject' || ($langt = detectLanguage($o)) == $lang)
						{
							if ($DEBUG) $RDFLOG.= "<br><br>TAKE Triple lang=$lang: ($docid,$p,$o)";
							
							$triples[]=array($docid,$p,$o);	
						}
						else 
						{
							if ($DEBUG) $RDFLOG.="<br>DISCARD subject $o ($langt) since not found to be in lang ($lang) by the used infallible costfree google language detector";
						}
					} // foreach $sxmldom->results->result
				}// valid xml for doc triples
			} // call service and rebuild cache
		}

		$triples_noof=count($triples);
		if ($triples_noof)
		{
			if ($DEBUG)
			{
				$RDFLOG.= "<br>RETURNING $triples_noof SPARQL triples to $subject from $src_name:";
				if (is_array($triples) && count($triples))
				{
					$i=0;
					foreach($triples as $triple)
					{
						$i++;
						list($s,$p,$o)= $triple;
						$RDFLOG.= "<br>$i. <b>TRIPLE</b>: $s $p $o";
					}
				}
			}
		}
		
		$count_received_docs=count($xml_doc_contents);
		Logger::logAction(27, array('from'=>'get_cached_triples_on_subject_from_sparql_endpoint','msg'=>"Exit with $triples_noof triples"),$sid);

		return array($triples,$count_received_docs,$used_cache);
	}	// get_cached_triples_on_subject_from_sparql_endpoint
			
		

		
		
		
	/**
	 * Returns an s string value: subject_uid used by the first triple having value
	 */
	function get_triple_objects($s,$p,&$store,&$NAMESPACES)
	{
		$DEBUG=0;
		global $RDFLOG;
		$rodin_a_ns_url = $NAMESPACES{'rodin_a'};
		$PREFIX="PREFIX rodin_a: <$rodin_a_ns_url> ";
		$o=array();
		
		if($DEBUG) $RDFLOG.="<hr><b>get_triple_objects($s,$p)</b>:";
		
		
		if ($p=='')
		{
			$need_p=true;
			$p = array();
			$QUERY = $PREFIX . " select ?o ?p { $s ?p ?o . }";
		}
		else 
		{
			$QUERY = $PREFIX . " select ?o { $s $p ?o . }";
		}

		if ($DEBUG)
			$RDFLOG.="<br>get_triple_objects($s,$p): <br>".show_sparql_query($QUERY);
			
		if ($rows = $store->query($QUERY, 'rows')) 
		{
			if (is_array($rows) && count($rows)>0)
			{
				foreach($rows as $row)
				{
					if ($need_p)
					{
						$p_full=$row['p'];
						$p[] = separate_namespace($NAMESPACES,$p_full,':',false);
					}
					$o_full=$row['o'];
					$o[] = separate_namespace($NAMESPACES,$o_full,':',false);
				}
			}
		}
		
		if ($DEBUG)
		{
			if ($need_p)
			{
				$pc=count($p);
				
				$RDFLOG.="<br>$pc Predicates found: ";
				foreach($p as $pred)
				{
					$RDFLOG.="<br>pred: $pred";
				}
			}
			
			$oc=count($o);
			$RDFLOG.="<br>$oc Objects found: ";
			foreach($o as $docuid)
			{
				$RDFLOG.="<br>doc: $docuid";
			}
		}
	
		//print "<br>get_triple_subject returns ($literal)=>($s)";
		return array($p,$o);
 	} // get_triple_subject

		
		
		
	/**
	 * calls directly the endpoint,
	 * returns results taken from the endpoint as xml
	 * without caching
	 */	
	function eval_remote_sparql_query_as_xml($sds_sparql_endpoint,$sds_sparql_endpoint_params,$sparqlquery)
	{
		$DEBUG=0;
		$i=0;
		global $RDFLOG;
		$url_endpoint_query= $sds_sparql_endpoint
												 	.'?query='.urlencode($sparqlquery)
													.'&'.$sds_sparql_endpoint_params;
						
		if ($DEBUG)
		$RDFLOG.= "<br>get_cached_triples_on_subject_from_sparql_endpoint:<br><br>$i.sparqlquery:<br>".(str_replace("\n","<br>",htmlentities($sparqlquery)))."<br><br>";
	
		$xml_content=get_file_content($url_endpoint_query);
		//$xml_content=utf8_encode(html_entity_decode($xml_content));
		if ($DEBUG) $RDFLOG.= "<br>XML GOT FROM $src_name:<br>".htmlentities((($sparqlquery)));
		$xml_content_len=strlen($xml_content);
		$valid_xml=true;
		if (datasource_error($xml_content,$src_name))
		{
			fontprint("<hr>ERROR FROM DATASOURCE called on subject ($subject):"
						."<br>Check for CACHE?<br>"
						."<br>QUERY:<br>"
						.str_replace("\n","<br>",htmlentities($sparqlquery)),'red')
						."<br><br>Using url:"
						."<br>$url_endpoint"
						."<br><br>Content got: ".htmlentities($xml_content);
			$valid_xml=false;
		}
		
		return $xml_content;
	}	// eval_remote_sparql_query_as_xml
		
		
		
		
		
		
		
	 function datasource_error(&$txt, $src_name)
	 {
	 	$is_error=false;
	 	switch(strtolower($src_name))
		{
			case 'europeana':
				$is_error = (strstr(substr($txt,0,100),'Error report'));
				break;
			case 'dbpedia':
				$is_error = (strstr(substr($txt,0,100),'FATAL error'));
				break;
		}
	 	return $is_error;
	 }
	 
		
		
		
		
	/**
	 * State using either cache age or content
	 * whether the cache content should be considered valid
	 */
	function sparql_cached_content_quality_control(&$xmlcontent,$src_name,$age_in_sec,$tolerated_age_sec)
	{
		global $RDFLOG;
		$ok = trim($xmlcontent) <> '';
		
		if ($DEBUG) $RDFLOG.="<br>sparql_cached_content_quality_control $src_name: (non empty content)=$ok";
		//Special case empty xml object		
		if ($ok && strstr(strtolower($src_name),'europeana'))
		{
			$ok = ($age_in_sec < $tolerated_age_sec);
			
			if ($DEBUG) $RDFLOG.="<br>sparql_cached_content_quality_control $src_name: (age_in_sec=$age_in_sec < tolerated_age_sec=$tolerated_age_sec)=$ok";
			
			//$sxmldom=simplexml_load_string($xmlcontent,'SimpleXMLElement', LIBXML_NOCDATA);
			//$ok = count($sxmldom->results->result)>0; // there are/where results
		}//europeana	
		 
		return $ok;
	}
		
		
	function discard_subject_word_candidate($token)
	{
		$ttoken=trim($token);
		$discard=false;
		
		$discard = (preg_match($PAT='/^[\W]$/',$ttoken)); // $ttokan is not a word
		//print "<br>Checking discard_subject_word_candidate($ttoken) ... ($discard) with pattern $PAT";
					
		return $discard;
	} // discard_subject_word_candidate
	
	
	function put_to_singular($word)
	{
		global $RDFLOG;
		$DEBUG=0;
		$lang=detectLanguage($word);
		$lastc=substr($word,strlen($word)-1);
		
		
		if ($lang=='en' || $lang=='fr' || $lang=='es')
		{
			if ($lastc=='s')
				$lastsubtract=$lastc;
		}
		else 
		if ($lang=='de')
		{
			
		}
			
		if($lastsubtract)
			$singular_word=substr($word,0,strlen($word)-1);
		else
			$singular_word=$word;
		if ($DEBUG)
			$RDFLOG.="<br>put_to_singular($word,$lang), lastc=($lastc) lastsubtract=($lastsubtract) returning ($singular_word)";
		
		return $singular_word;
	} // put_to_singular
	
	
	
		
 	function cleanup4literal($str)
	{
		global $PROT;
		//$str0=$str;
		//print "<br>cleanup4literal($str)->";
		//Do not want to see the following chars inside the term $txt0
		if (!strstr($str,$PROT))
		{
			//Incase literal is already double quoted:
			if (substr($str,0,1)=='"'
			|| substr($str,strlen($str)-1,1)=='"' )
			{
				$netto_str=trim(substr($str,1,strlen($str)-2));
				$str = '"'.cleanup4token($netto_str).'"';
			}
			else {
				$str = cleanup4token($str);
			}
		}
		//$str = str_replace("'","\\'",$str);
		//print "<br>cleanup4literal ($str0)-->($str)";
		return $str;
	}

	
	
	function cleanup4token($str)
	{
		//print "<br>cleanup4literal($str)->";
		//Do not want to see the following chars inside the term $str
		$pattern = '/[\*&\|!;\'""\\\.\[\]\(\)]+/';
		$replace = '';
		$str= trim(preg_replace($pattern, $replace, $str));
			//print "($str)";
		//$str = str_replace("'","\\'",$str);
		return $str;
	}
	
	
	
	
	/**
	 * Returns an assoc with the number of subject each doc in docs
	 */
	function rank_docs_with_its_subjects($docs_uid,&$store,&$NAMESPACES,&$ranked_result_subjects)
	{
		$DEBUG=0;
		global $RDFLOG;
		$docs_ranked = array();
		if ($DEBUG) $RDFLOG.="<hr><b>rank_docs_with_its_subjects(".implode(',',$docs_uid).")</b>";
		if ($DEBUG) {
			$cr=count($ranked_result_subjects);
			$RDFLOG.="<br>$cr ranked_result_subjects:";
			foreach($ranked_result_subjects as $l=>$R)
			{
				list($rank,$k) = $R;
				$RDFLOG.="<br>$rank - '$l'";
			}
		}
		
		
		
		if (count($docs_uid))
		foreach($docs_uid as $doc_uid)
		{
			if ($DEBUG) $RDFLOG.="<br>Enum subjects to doc $doc_uid:";
			
			$doc_subjects_uid=get_doc_subjects_from_store($doc_uid,$store,$NAMESPACES);
			//Rank documents with respect to the ranked vector addition 
			$docrank=0;
			foreach($doc_subjects_uid as $label=>$subject_uid)
			{
				list($score,$k) = $ranked_result_subjects{$label};
				$docrank+=$score;
				if ($DEBUG) $RDFLOG.="<br>Subject '$label' ranked with: $score";
			} // foreach $doc_subjects_uid
			
			if ($DEBUG) $RDFLOG.="<br>Assigning rank $docrank to doc $doc_uid";
			
			$ranked_docs{$doc_uid}=$docrank;
		} // foreach $docs
		
		if ($DEBUG)
		{
			$oc=count($ranked_docs);
			$RDFLOG.="<br>ranked_docs: "; 
			if (is_array($ranked_docs)&&$oc)
			foreach($ranked_docs as $docuid=>$rank)
			{
				$RDFLOG.="<br>docr: $docuid=>$rank";
			}
			$RDFLOG.="<br>EXIT";
		}
		
		arsort($ranked_docs);
		
		return $ranked_docs;
	} // rank_doc_with_its_subjects
	
	
	
	
	/**
	 * Collects subjects and related subjects relative to $doc_uid
	 * Returns an assoc $doc_subjects_uid{$label}=$sub_uid to be processed
	 * @param $doc_uid - the uid of the doc
	 * @param &$store - reference to local store
	 * @param &$NAMESPACES - reference to global namespaces
	 */
	function 	get_doc_subjects_from_store($doc_uid,&$store,&$NAMESPACES)
	{
		$DEBUG=0;
		global $RDFLOG;
		
		$doc_subjects_uid=array();
		$rodin_ns_url 	=$NAMESPACES{'rodin'};
		$dce_ns_url 		=$NAMESPACES{'dce'};
		$rodin_a_ns_url =$NAMESPACES{'rodin_a'};
		$rodin_e_ns_url =$NAMESPACES{'rodin_e'};
			$QUERY=<<<EOQ
PREFIX rodin: <$rodin_ns_url>
PREFIX dce: <$dce_ns_url>
select distinct ?sub ?label
{
 {
   $doc_uid dce:subject ?sub .
   ?sub rodin:label ?label
 }
 UNION
 {
   $doc_uid dce:subject ?subx .
   ?subx rodin:subject_related ?sub .
   ?sub rodin:label ?label
 }
}
EOQ;

			if ($DEBUG)
				$RDFLOG.="<br>rank_docs_with_its_subjects($doc_uid): <br>".show_sparql_query($QUERY);
				
			if ($rows = $store->query($QUERY, 'rows')) 
			{
				if (is_array($rows) && count($rows))	
				{
					foreach($rows as $row)
					{	
						$sub_full=$row['sub'];
						$sub_uid = separate_namespace($NAMESPACES,$sub_full,':',false);
						
						$label_full=$row['label'];
						$label = separate_namespace($NAMESPACES,$label_full,':',false);	
						$doc_subjects_uid{$label}=$sub_uid;
					}
				}
			}
		return $doc_subjects_uid;
	}
	
	
	/**
	 * Returns a list of subjects taken from the src use corresponding to cache_id
	 */
	function rdf_get_triplesubjects_created_in_src_use_lodfetch($cache_id, &$store, &$NAMESPACES)
	{
		$DEBUG=0;
		global $RDFLOG;
		$subjects				= array();
		$documents			= array();
		$rodin_a_ns_url = $NAMESPACES{'rodin_a'};
		$dce_ns_url 		= $NAMESPACES{'dce'};
		$PREFIXES				="PREFIX rodin_a: <$rodin_a_ns_url> PREFIX dce: <$dce_ns_url>";
		
		$QUERY=<<<EOQ
	$PREFIXES
	select distinct ?srcuse ?subj ?extdoc
	{
		?srcuse rodin_a:cache_id "$cache_id" .
	  ?srcuse rodin_a:expdoc ?extdoc .
	  ?srcuse rodin_a:lodexp_related ?subj .
  }
EOQ;
		
		if ($DEBUG)
			$RDFLOG.="<br><br><b>rdf_get_subjects_created_in_src_use_lodfetch($cache_id):</b><br><br> QUERY: <br>".str_replace("\n","<br>",htmlentities($QUERY));
		if ($rows = $store->query($QUERY, 'rows')) 
		{
			if ($DEBUG)
			$RDFLOG.="<br>rdf_get_subjects_created_in_src_use_lodfetch: processing results";
		
			$src_use_uid=null;
			foreach($rows as $row)
			{			
				$subj_full=$row['subj'];
				$subj = separate_namespace($NAMESPACES,$subj_full,':',false);
				if (!in_array($ent,$subjects))
						$subjects[]=$subj;
				
				$doc_full=$row['extdoc'];
				$doc = separate_namespace($NAMESPACES,$doc_full,':',false);
				if (!in_array($doc,$documents))
						$documents[]=  str_replace("/","\\/",$doc);
				
				if (!$src_use_uid)
				{
					$src_use_uid_full=$row['src_use'];
					$src_use_uid = separate_namespace($NAMESPACES,$src_use_uid_full,':',false);
				}
				
			}
		}
		
		if ($DEBUG)
		{
			$RDFLOG.="<br>rdf_get_subjects_created_in_src_use_lodfetch exiting with ($src_use_uid,$subj)";
			$RDFLOG.="<br>Returned subjects ids:<br>";
			if (is_array($subjects) && count($subjects))
			foreach($subjects as $x) $RDFLOG.="subject id <b>$x</b>";
			$RDFLOG.="<br>Returned documents ids:<br>";
			if (is_array($documents) && count($documents))
			foreach($documents as $x) $RDFLOG.="document id <b>$x</b>";
		}
		return array($src_use_uid,$documents,$subjects);
	} // rdf_get_triples_created_in_src_use_lodfetch
	
	
	
	/**
	 * 
	 */
	function rdf_get_delivered_triples($cache_id, &$store, &$NAMESPACES, $REMOVE_EFFECTIVITY_TESTING)
	{
		$DEBUG=0;
		global $RDFLOG;
		$triples= array();
		//rodin, rodin_a, rdf
		$rodin_a_ns_url = $NAMESPACES{'rodin_a'};
		$PREFIXES				="PREFIX rodin_a: <$rodin_a_ns_url>";
		
		$QUERY=<<<EOQ
	$PREFIXES
	select distinct ?t
	{
		?srcuse rodin_a:cache_id "$cache_id" .
	  ?srcuse rodin_a:delivered ?t .
  }
EOQ;
		
		if ($DEBUG || $REMOVE_EFFECTIVITY_TESTING)
		{
			$decoded_cache_id = base64_decode($cache_id);
			$RDFLOG.="<hr><b>rdf_get_delivered_triples(cache_id=$decoded_cache_id):</b><br> Result to query: <br>".show_sparql_query($QUERY);
		}
		if ($rows = $store->query($QUERY, 'rows')) 
		{
			foreach($rows as $row)
			{			
				$single_triple= $row['t'];
				$triples[] = unfreeze_triple_from_literal_metaobject($single_triple);
			} // foreach
		}
		
		
		if ($DEBUG || $REMOVE_EFFECTIVITY_TESTING)
		{
			$tc=count($rows);
			$RDFLOG."<br>$tc UNFROZEN TRIPLES !!";
			if($tc)
			foreach($triples as $triple)
			{
				list($s,$p,$o) = $triple;
				$RDFLOG.="<br>UNFROZEN: ($s)($p)($o)";
			}
		}
		
		return $triples;
	} // rdf_get_delivered_triples
	
	
	
	/**
	 * Returns a list of subjects taken from the src use corresponding to cache_id
	 */
	function rdf_get_subjects_created_in_src_use_subexp($cache_id, &$store, &$NAMESPACES)
	{
		$subj= array();
		$rodin_a_ns_url = $NAMESPACES{'rodin_a'};
		$dce_ns_url 		= $NAMESPACES{'dce'};
		$PREFIXES				="PREFIX rodin_a: <$rodin_a_ns_url> PREFIX dce: <$dce_ns_url>";
		
		$QUERY=<<<EOQ
	$PREFIXES
	select distinct ?srcuse ?subj
	{
		?srcuse rodin_a:cache_id "$cache_id" .
	  ?srcuse rodin_a:subexp_related ?subj .
  }
EOQ;
		
		if ($rows = $store->query($QUERY, 'rows')) 
		{
			foreach($rows as $row)
			{			
				$subj_full=$row['subj'];
				$subj[] = separate_namespace($NAMESPACES,$subj_full,':',false);
				if (!$src_use_uid)
				{
					$src_use_uid_full=$row['src_use'];
					$src_use_uid = separate_namespace($NAMESPACES,$src_use_uid_full,':',false);
				}
				
			}
		}
		return array($src_use_uid,$subj);
	} // rdf_get_subjects_created_in_src_use_subexp
	
	
	
	/**
	 * Returns $other_source or false
	 * $other_source if there is a dependency from another entity (not coming from $src_use_uid) using $link_predicate
	 * False if there is not such a dependency.
	 * 
	 * Notes: 1) This is in PHP because the used tool ARC2 does not allow Filters like "NOT EXISTS" or "MINUS"
	 *        2) ASK is not supported by ARC2 (true or false are not readable after query)
	 * 
	 * @param $link_predicate - e.g. rodin:subject_related
	 * @param $entity_uid - e.g. a given subject uid
	 * @param $src_use_uid - the current src_use_uid
	 * @param &$RDFresultCLASS - Reference to RDF result class
	 */
	function rdf_check_existence_of_other_dependencies( $link_predicate, $entity_uid, $src_use_uid, &$store, &$NAMESPACES )
	{
		global $RDFLOG;
		$DEBUG=0;
		$rodin_a_ns_url = $NAMESPACES{'rodin_a'};
		$dce_ns_url 		= $NAMESPACES{'dce'};
		$PREFIXES				="PREFIX rodin_a: <$rodin_a_ns_url> PREFIX dce: <$dce_ns_url>";

		//Check if there is some other link from another lodfetch src use 
		//															 or from another subexp src use
		//      												 or from a rodin search		
		
		//USE ALL NAMESPACES ???
		$QUERY=<<<EOQ
	$PREFIXES
	select ?other_source
	{
		{
		  ?subjectx $link_predicate $entity_uid .
		  ?extdoc ?link ?subjectx . 
	    ?other_source rodin_a:expdoc ?extdoc .
		  FILTER ( ?other_source != $src_use_uid ) .
	  }
	  UNION
	  {
		  ?subjectx $link_predicate $entity_uid .
		  ?other_source rodin_a:subexp_related ?subjectx .
		  FILTER ( ?other_source != $src_use_uid ) .
	  }
	  UNION
	  {
	  	?other_source rodin_a:resultdoc ?rdoc .
	  	?rdoc dce:subject $entity_uid .
	  } 
  }
  LIMIT 1
EOQ;
		
		if ($DEBUG)
		{
			$RDFLOG.="<br><br><b>rdf_check_existence_of_other_dependencies( link_predicate=$link_predicate, entity_uid=$entity_uid, src_use_uid=$src_use_uid):</b><br><br>QUERY:<br>"
							 .str_replace("\n","<br>",htmlentities(urldecode($QUERY)));
		}
		
		
		if ($rows = $store->query($QUERY, 'rows')) 
		{
			$row=$rows[0];
			{			
				$other_source_full=$row['other_source'];
				$other_source = separate_namespace($NAMESPACES,$other_source_full,':',false);
				
				if ($DEBUG)
				{
					$RDFLOG.="<br>Other source for entity $entity_uid found: $other_source";
				}
			}
		}
		else
			$other_source=false;
		
		
		if ($DEBUG)
				{
					$RDFLOG.="<br>rdf_check_existence_of_other_dependencies returning: ($other_source)";
				}
		
		return $other_source;
	} // rdf_check_existence_of_other_dependencies
	
	
	
	
	
	
	/**
	 * Queries the selected SRC sources
	 * returns lists of marked results
	 * @param $query 
	 */
	function get_rodin_src_suggestions($query,$USER_ID,$max_suggestions=10)
	{
		$DEBUG=0;
		if (!$USER_ID) print "ERROR - NO USER_ID GIVEN";
		global $SRCDB_DBHOST, $ARCDB_USERNAME, $ARCDB_USERPASS, $PROT;
		$suggestions = array();
		$suggestions_data=array();
		$descriptions=array();
	
		$max_retrieval_results=12; // Limit search engine to n results each SRC
		$max_displayed_results=2; // Limit display in autocomplete to n results
			
		$servicename='autocomplete';
		$SRCS = get_active_SRC_autocomplete_sources( $USER_ID );
		
		if ($DEBUG) print "<br>".count($SRCS)." SRCs ready for autocomplete... on ($query):\n";
		$PREPARED_AUTOCOMPLETE_SOURCES =  get_SRC_THESAURI_RECORDS($SRCS,$USER_ID,$lang='');

		if (count($PREPARED_AUTOCOMPLETE_SOURCES))
		{
			include_once('LanguageDetection.php');
			foreach($PREPARED_AUTOCOMPLETE_SOURCES as $AUTOCOMPLETE_SRC_SOURCE)
			{
				$broaders=$narrowers=$relateds=array();
				$broaders_properties=$narrowers_properties=$relateds_properties=array();
				if ($DEBUG)
				{
					print "<br>SRC: "; var_dump($AUTOCOMPLETE_SRC_SOURCE);
				}
				
				list(	$src_name,
							$IS_REMOTE_SPARQL_ENDPOINT,
							$sds_sparql_endpoint,
							$sds_sparql_endpoint_params,
							$LOCAL_SRC,
							$DISKenginePATH,
							$basic_path_sroot,
							$basic_path_SRCengineInterface,
							$basic_path_SRCengine,
							$CLASS,
							$pathClass,
							$pathSuperClass,
							$AuthUser,
							$AuthPasswd,
							$ID,
							$Protocol,
							$Server,
							$Port,
							$Path_Start,
							$Path_Refine,
							$Servlet_Start,
							$Servlet_Refine,
							$src_parameters,
							$autocomplete_uri ) = $AUTOCOMPLETE_SRC_SOURCE;
			
				if ($IS_REMOTE_SPARQL_ENDPOINT)
				{
					//print "<br>IS_REMOTE_SPARQL_ENDPOINT $src_name";
					if ($autocomplete_uri)
					{
						//Add some further values into $suggestions_data / $suggestions
						//list($suggestions,$suggestions_data,$descriptions) = 
							get_remote_autocomplete(	$src_name,
																				$autocomplete_uri,
																				$lang,
																				$query,
																				$src_parameters,
																				$max_retrieval_results,
																				$max_displayed_results,
																				$suggestions,
																				$suggestions_data,
																				$descriptions,
																				$properties);

					} // $autocomplete_uri
					else
					{
						if ($DEBUG)
							fontprint("<br>get_rodin_src_suggestions($query) empty autocomplete_uri fround for SRC $src_name! - wrong SRC parameters?",'red');
					}
				}
				else // !$IS_REMOTE_SPARQL_ENDPOINT
				{
					//print "<br>! IS_REMOTE_SPARQL_ENDPOINT $src_name";
					// SRCEngineSKOSResult
					$SOLRCLIENT=null; //koll SOLRCLIENT to be sure it is reconstructed as it needs
					$lang=detectLanguage($query); // we hope we can establish a language here, otherwise no SKOS
					$CONTENT = get_from_src_directly( $sid='',
																						$max_retrieval_results, //SKOS results read one more (if possible)
																						$lang, //we have no language here: autocomplete
																						$servicename,
																						$query, // query
				
																						$src_name,
																						$mode='autocomplete',
																						$DISKenginePATH,
																						$basic_path_sroot,
																						$basic_path_SRCengineInterface,
																						$basic_path_SRCengine,
																						$CLASS,
																						$pathClass,
																						$pathSuperClass,
																						$AuthUser,
																						$AuthPasswd  );
					####################################################
					#
					# Beautify SRC name and insert uniquely suggestions
					#
					$src_name=trim(str_replace('SOLR','',$src_name));
					#
					####################################################
					
					if ($CONTENT->broader->results)
						prepare_write_skos_entities(	$CONTENT->broader->results,
																					$max_displayed_results,
																					$query,
																					$src_name,
																					'broader',
																					$broaders,
																					$broaders_properties);

					if ($CONTENT->narrower->results)
						prepare_write_skos_entities(	$CONTENT->narrower->results,
																					$max_displayed_results,
																					$query,
																					$src_name,
																					'narrower',
																					$narrowers,
																					$narrowers_properties);
	
					if ($CONTENT->related->results)
						prepare_write_skos_entities(	$CONTENT->related->results,
																					$max_displayed_results,
																					$query,
																					$src_name,
																					'related',
																					$relateds,
																					$relateds_properties);
					
					if ($DEBUG)
					{
						print "<hr> CONTENT: ";
						var_dump($CONTENT);
						print "<hr>";
						print "<hr>S:";
						if(count($CONTENT->suggested))
						foreach($CONTENT->suggested as $sugg)
							print "<br>SUGG: $sugg";
						print "<hr>B:"; 
						if(count($broaders))
						foreach($broaders as $x)
							print "<br>broader ".($x);
						print "<hr>N:";
						if(count($narrowers))
						foreach($narrowers as $x)
							print "<br>narrower: ".($x);
						print "<hr>R:";
						if(count($relateds))
						foreach($relateds as $x)
							print "<br>related: $x";
					}
					
					
					
					####################################################
					#
					# In case a SKOS node was found, add this to the list
					#
					
					# Put query first in case of SKOS data
					# in order to enhance readability: 
					if ($CONTENT->broader->results 
					|| $CONTENT->narrower->results
					|| $CONTENT->related->results)
					{
						//FORCE inject main label:
						$suggestions_data[]=$query;
						$suggestions[]="<span class='srcname'>$src_name:</span> $query <b>SKOS</b>:";
						$descriptions[]="The following words were found to be SKOS extensions of '$query'";
						$properties[]="src=$src_name;p=name;show=true";
					}
					
					if ($CONTENT->broader->results)
					{
						for($i=0;$i<count($broaders);$i++)
						{
							$x = $broaders[$i];
							if ($x)
							{
								if (!strstr($x,'|'))
								{
									$suggestions_data[]=$x;
									$suggestions[]="<span class='srcname'>$src_name:</span> <b>broader</b> $x";
									$descriptions[]="was found to be a more general concept than '$query'";
								}
								else {
									list($text,$link)=explode('|',$x);
									if (!in_array($text,$suggestions))
									{
										$suggestions_data[]="Click here to see more general terms from $src_name than displayed";
										$suggestions[]="<a href='#' class='seemore' onclick='t(\"$src_name\",\"broader\");return false;'>$text</a>";
										$descriptions[]=trim($link);
									}
								}
								$properties[]=$broaders_properties[$i];
							}
						}
					}
					if ($CONTENT->narrower->results)
					{
						for($i=0;$i<count($narrowers);$i++)
						{
							$x = $narrowers[$i];
							if ($x)
							{
								if (!strstr($x,'|'))
								{
									$suggestions_data[]=$x;
									$suggestions[]="<span class='srcname'>$src_name:</span> <b>narrower</b> $x";
									$descriptions[]="was found to be a more specialized concept than '$query'";
								}
								else {
									list($text,$link)=explode('|',$x);
									if (!in_array($text,$suggestions))
									{
										$suggestions_data[]="Click here to see more specialized terms from $src_name than displayed";
										$suggestions[]="<a href='#' class='seemore' onclick='t(\"$src_name\",\"narrower\");return false;'>$text</a>";
										$descriptions[]=trim($link);
									}
								}
								$properties[]=$narrowers_properties[$i];
							}
						}
					}
					if ($CONTENT->related->results)
					{
						for($i=0;$i<count($relateds);$i++)
						{
							$x = $relateds[$i];
							if ($x)
							{
								if (!strstr($x,'|'))
								{
									$suggestions_data[]=$x;
									$suggestions[]="<span class='srcname'>$src_name:</span> <b>related</b> $x";
									$descriptions[]="was found to be a concept related with '$query'";
								}
								else 
								{
									list($text,$link)=explode('|',$x);
									if (!in_array($text,$suggestions))
									{
										$suggestions_data[]="Click here to see more related terms from $src_name than displayed";
										$suggestions[]="<a href='#' class='seemore' onclick='t(\"$src_name\",\"related\");return false;'>$text</a>";
										$descriptions[]=trim($link);
									}
								}
								$properties[]=$relateds_properties[$i];
							}
						}
					}
					
					//Load found suggestions:
					$i=0;
					$seemoretext="... See more suggestions from $src_name";
					$seemorelink="$PROT://tbd";
					$SUGGESTED_TERMS=$CONTENT->suggested;
					$countentities=count($SUGGESTED_TERMS);
					$max=min($max_displayed_results,$countentities); // extract only what is in
					$further = $countentities - $max_displayed_results;
					
					if ($further==1)
						$seemoretext="... See 1 available further suggestion to '$query' from $src_name";
					else if ($further>1)
						$seemoretext="... See $further further suggestions to '$query' from $src_name";
					
					$suggested=false;
					$hidden=false;
					for($i=0;$i<$max;$i++)
					{
						$sugg=$SUGGESTED_TERMS[$i];
						if ($sugg)
						{
							if ($i<$max_displayed_results || $hidden)
							{
								if (!in_array($sugg,$suggestions_data))
								{
									$suggestions_data[]=$sugg;
									$suggestions[]="<span class='srcname'>$src_name:</span> $sugg";
									$descriptions[]='';
									$properties[]="src=$src_name;p=sugg;".($hidden?"show=false":"show=true");
									$suggested=true;
								}
							}// $max_results
							else if ($suggested && !in_array($seemoretext,$suggestions))
							{
								$suggestions_data[]="Click here to see more related terms from ".$src_name." than displayed";
								$suggestions[]="<a href='#' class='seemore' onclick='t(\"$src_name\",\"sugg\");return false;'>$text</a>";
								$descriptions[]=trim($seemorelink);
								$properties[]="src=$src_name;p=seemore;show=true";
								
								// Add further entities as hidden
								$hidden=true;
							}
						}//foreach
					}
				} // $IS_REMOTE_SPARQL_ENDPOINT
				
			} // foreach
		}
		
		return array($suggestions,$suggestions_data,$descriptions,$properties);
	}
		
		
		
	/**
	 * Returns a vector of entities to be used in an autocomplete engine
	 * In case there are in $COMMASEPARATEDENTITIES more than $max_results entities
	 * It stops at $max_results and returns a link to get more data 
	 */
	function prepare_write_skos_entities(	$COMMASEPARATEDENTITIES,
																					$max_displayed_results,
																					$query,
																					$src_name,
																					$skosprop,
																					&$target_entities,
																					&$suggestion_properties)
	{
		$entities=explode(',',$COMMASEPARATEDENTITIES);
		$countentities=count($entities);
		$max=min($max_displayed_results,$countentities); // extract only what is in
		$further = $countentities - $max_displayed_results;
		if ($further==1)
			$text="... See 1 available further $skosprop item to '$query' from $src_name";
		else if ($further>1)
			$text="... See $further further $skosprop items to '$query' from $src_name";
		
		
		for($i=0;$i<$max;$i++)
		{
			$target_entities[]=$entities[$i];
			$suggestion_properties[]="src=$src_name;p=$skosprop;show=true"; // Javascript properties
		}

		//In case there are more entities than $max_results allows
		//mark seemore and send hidden information on further entities
		if ($countentities > $max_displayed_results)
		{
			$target_entities[]="$text|$link"; 
			$suggestion_properties[]="src=$src_name;p=seemore;show=true";
			
			for($i=$max_displayed_results;$i<$countentities;$i++)
			{
				$target_entities[]=$entities[$i];
				$suggestion_properties[]="src=$src_name;p=$skosprop;show=false"; // Javascript properties
			}
		}
		return array($target_entities,$suggestion_properties);
	}	// prepare_write_skos_entities
		
		
		
		/**
		 * $INITIALISED_SRCs = initialize_SRC($USER_ID)
		 * Returns an array of SRC records
		 * Selected among the SRC records which are temporarily used
		 * 
	 * @param $USER_ID - the user id for which some SRC's are activated
	 * @param &$SRCS - if set, a result from a call like $SRCS = get_active_THESAURI_expansion_sources( $USER_ID );
	 */
	function get_SRC_THESAURI_RECORDS(&$SRCS,$USER_ID,$lang)
	{
		$DEBUG=0;
		global $DOCROOT,$HOST;
		$initialised_src=0;
		if (!$SRCS)
		{
			if ($DEBUG) $RDFLOG.="<br>RENEW SRCs for user id $US";
			$SRCS = get_active_THESAURI_expansion_sources( $USER_ID );
		}
		$SRCrecords = $SRCS['records'];
		
		$NoOfUsableSRC=count($SRCrecords);
		
		$i=0;
		if(is_array($SRCrecords) && count($SRCrecords))
		foreach($SRCrecords as $SRC)
		{
			$i++;
			$src_name=$SRC['Name'];
			$src_path=trim($SRC['Path_Refine']); // = /rodin/eng/fsrc/app/engine/SOZengine/SOZengineSOLR
			$src_sparql_endpoint=trim($SRC['sparql_endpoint']); // = /rodin/eng/fsrc/app/engine/SOZengine/SOZengineSOLR
			$src_sparql_endpoint_params=trim($SRC['sparql_endpoint_params']); // = /rodin/eng/fsrc/app/engine/SOZengine/SOZengineSOLR
						
			//print "<br>src_path: ($src_path)";
			$CLASS=basename($src_path);
			$SUPERCLASS=basename(dirname($src_path));
			$DISKenginePATH=$DOCROOT.str_replace("$SUPERCLASS/$CLASS",'',$src_path);
			$DISKfsrcPATH=dirname($DISKenginePATH);
			
			// Include class paths for SRC ENGINES
			$pathSuperClass=$DISKenginePATH.$SUPERCLASS."/$SUPERCLASS.php";
			$pathClass=$DISKenginePATH.$SUPERCLASS."/$CLASS/$CLASS.php";
			
			$AuthUser				=$SRC['AuthUser'];
			$AuthPasswd			=$SRC['AuthPasswd'];
			$ID							=$SRC['ID'];
			$Protocol				=$SRC['Protocol'];
			$Server					=$SRC['Server'];
			$Port						=$SRC['Port'];
			$Path_Start			=$SRC['Path_Start'];
			$Path_Refine		=$SRC['Path_Refine'];
			$Path_Test			=$SRC['Path_Test'];
			$Servlet_Start	=$SRC['Servlet_Start'];
			$Servlet_Refine	=$SRC['Servlet_Refine'];
			$src_parameters	  =$SRC['src_parameters'];
			$autocomplete_uri	=$SRC['autocomplete_uri'];

			//Is this a classical SRC or nonSKOS a sparql endpoint ?
			$IS_REMOTE_SPARQL_ENDPOINT=(($src_sparql_endpoint_params<>'' || $autocomplete_uri<>'' ));
			
			$basic_path_sroot=str_replace('//','/',$DISKfsrcPATH."/sroot.php");
			$basic_path_SRCengine=str_replace('//','/',$DISKenginePATH.'/SRCengine.php');
			$basic_path_SRCengineInterface=str_replace('//','/',$DISKenginePATH.'/SRCengineInterface.php');
				
			$LOCAL_SRC = ($Server == $HOST);
			
			$initialised_src++;
			if ($DEBUG)
			{
				print "<hr> Name:".$src_name;
				print "<br> CLASS: ".$CLASS;
				print "<br> SUPERCLASS: ".$SUPERCLASS;
				print "<br> PATH: ".$src_path;
				print "<br> DISKfsrcPATH: ".$DISKfsrcPATH;
				print "<br> DISKenginePATH: ".$DISKenginePATH;
			}
			
			$INITIALISED_SRCs[]= array(	$src_name,
																	$IS_REMOTE_SPARQL_ENDPOINT,
																	$src_sparql_endpoint,
																	$src_sparql_endpoint_params,
																	$LOCAL_SRC,
																	$DISKenginePATH,
																	$basic_path_sroot,
																	$basic_path_SRCengineInterface,
																	$basic_path_SRCengine,
																	$CLASS,
																	$pathClass,
																	$pathSuperClass,
																	$AuthUser,
																	$AuthPasswd,
																	$ID,
																	$Protocol,
																	$Server,
																	$Port,
																	$Path_Start,
																	$Path_Refine,
																	$Servlet_Start,
																	$Servlet_Refine,
																	$src_parameters,
																	$autocomplete_uri
																	);
			
		} // foreach $SRC
		return $INITIALISED_SRCs;
	} // get_SRC_THESAURI_RECORDS
	
	
	
	
	
	/**	
	 * 
	 */
	function rdf_delete_triples(&$triples,&$store,$REMOVE_EFFECTIVITY_TESTING)
	{
		$DEBUG=0;
		global $RDFLOG;
		$PREFIXES					= RDFprocessor::$NAMESPACES_PREFIX;
		$NAMESPACES				= RDFprocessor::$NAMESPACES;
		
		$EXPLICIT_TRIPLES=$UNION='';
		foreach($triples as $triple)
		{
			list($s,$p,$o) = $triple;
			$EXPLICIT_TRIPLES.="\n$s $p $o .";
			$UNION .= $UNION?"\n UNION ":'';
			$UNION .=<<<EOP
{ ?s ?p ?o .
FILTER(?s=$s) .
FILTER(?p=$p) .
FILTER(?o=$o) . }
EOP;
		} // foreach $triples
		
		
		if($REMOVE_EFFECTIVITY_TESTING)
		{
			$RDFLOG.="<hr><b>rdf_delete_triples REMOVE_EFFECTIVITY_TESTING</b>";
			$RDFLOG.="<hr><b>outgoing=$outgoing, incoming=$incoming</b>";
			$count_triples_before=count_ARC_triples($store);
$SELECT_TRIPLE_QUALIFICATION=<<<EOC
	$PREFIXES
	select ?s ?p ?o 
	{ $UNION }
EOC;
			//Show triples
			if ($rows = $store->query($SELECT_TRIPLE_QUALIFICATION, 'rows')) 
			{
				$cr=count($rows);
				$RDFLOG.=htmlprint("<br>$cr INVOLVED TRIPLES (to be deleted): <br>USING query: <br>".show_sparql_query($SELECT_TRIPLE_QUALIFICATION),'blue');
				
				foreach($rows as $row)
				{	$i++;
					$s_full=$row['s'];
					$s = separate_namespace($NAMESPACES,$s_full,':',false);
					$p_full=$row['p'];
					$p = separate_namespace($NAMESPACES,$p_full,':',false);
					$o_full=$row['o'];
					$o = separate_namespace($NAMESPACES,$o_full,':',false);
					$RDFLOG.="<br>$i: ($s)($p)($o)";
				}
			}
			else {
				$RDFLOG.=htmlprint("<br>NO TRIPLES QUALIFIED FOR DELETION!"
													."<br>USING QUERY:<br>"
													.show_sparql_query($SELECT_TRIPLE_QUALIFICATION)
													,'red');
			}
		} // $SECURITY_CONTROL

		//Delete all outgoing and incoming nodes from $entity_uid
		$DELETE_LINKS=<<<EOQ
	$PREFIXES
	delete { $EXPLICIT_TRIPLES }
EOQ;
		
		if ($DEBUG)
		{
			$RDFLOG.="<br>rdf_delete_entity($entity_uid$EVTL_LINK)<br><br>DELETEQUERY:<br>"
							 .show_sparql_query($DELETE_LINKS);
		}

		#############################
		$store->query($DELETE_LINKS);
		#############################
	
		if($REMOVE_EFFECTIVITY_TESTING)
		{
			sleep(2); // Sleep 2 seconds to unload DB engine
			$count_triples_after=count_ARC_triples($store);
			//Show triples
			$RDFLOG.="<br>CHECK AGAIN for the $cr INVOLVED TRIPLES (to be deleted): ";
			if ($rows = $store->query($SELECT_TRIPLE_QUALIFICATION, 'rows')) 
			{
				$cr=count($rows);
				$i=0;
				foreach($rows as $row)
				{	$i++;
					$s_full=$row['s'];
					$s = separate_namespace($NAMESPACES,$s_full,':',false);
					$p_full=$row['p'];
					$p = separate_namespace($NAMESPACES,$p_full,':',false);
					$o_full=$row['o'];
					$o = separate_namespace($NAMESPACES,$o_full,':',false);
					$RDFLOG.="<br>$i: ($s)($p)($o)";
				}
			}
			else {
				$RDFLOG.="<br>ALL $cr TRIPLES should have been DELETED ! ";
			}
			$DELTATRIPLES= $count_triples_before - $count_triples_after;
			$RDFLOG.=htmlprint("<br>$DELTATRIPLES (before=$count_triples_before, after=$count_triples_after) TRIPLES where DELETED FROM STORE! ",'green');
			if ($DELTATRIPLES <> $cr) $RDFLOG.=htmlprint("<br>DELETE MISMATCH ... $cr triples given to delete, $DELTATRIPLES deleted?",'red');
		} // $SECURITY_CONTROL		
	} // rdf_delete_triples
	
	
	
	
	/**
	 * Deletes the triples of an src use (rodin_a: namespace)
	 * and all the dependend triples around them (if not dependent from another source)
	 */
	function rdf_delete_entity_on_cacheid($cache_id, &$store, &$NAMESPACES)
	{
		global $RDFLOG;
		$DEBUG=0;
	
		$rodin_a_ns_url = $NAMESPACES{'rodin_a'};
		
		$sparql_deletequery=<<<EOS
PREFIX rodin_a: <$rodin_a_ns_url>
delete {?s ?p ?o}
where
{
  ?s rodin_a:cache_id "$cache_id" .
  ?s ?p ?o .
}
EOS;
//  FILTER( ?p != rodin_a:cache_id ) .
		
		if ($store)
		{
			if ($DEBUG) $RDFLOG.= "<br>querying: ".show_sparql_query($sparql_deletequery);
			$store->query($sparql_deletequery);
		}
	
	} // rdf_delete_entity_on_cacheid
	
	
	
	
	
	
	/**
	 * Constructs/returns a literal containing 1:1 the same information of $single_triple
	 * This information will be used to qualify and delete the same triple on removal actions
	 */
	function freeze_triple_as_literal_metaobject($single_triple)
	{
		list($s,$p,$o) = $single_triple;
		
		//is o a literal?
		$is_literal=(!strstr($o,':'));
		
		$o = $is_literal? l_inverse($o): $o;
		
		$meta_o = l("$s,$p,$o");
		
		return $meta_o;
	} // freeze_triple_as_literal_metaobject($single_triple)
	
	
	/**
	 * Inverse function to freeze_triple_as_literal_metaobject
	 * Returns an array ($s,$p,$o) from $single_triple_metaobject 
	 */
	function unfreeze_triple_from_literal_metaobject($literal_metaobject)
	{
		list($s,$p,$o) = explode(',',$literal_metaobject);
		
		//was $o a literal? 
		$is_literal=(!strstr($o,':'));
		
		$o= $is_literal? l($o): $o;
		
		return array($s,$p,$o);
	} // unfreeze_triple
	
	
	
	function l64($o,$p,&$TOBECODED64)
	{
		
		if ($TOBECODED64{$p})
					$o = base64_encode($o);
		return l($o);
	}
	
	
	
	/**
	 * returns "$str"
	 */
	function l($str)
	{
		//TOGLIERE / da un literale !!!!
		return '"'.addslashes($str).'"';
	}

	function l_inverse($str)
	{
		return str_replace('"','',$str);
	}



	function show_sparql_query($Q)
	{
		global $LINK2RODINSPARQLENDPOINT;
		global $RODINRDFSTORENAME;
		if (!$LINK2RODINSPARQLENDPOINT ) fontprint("Error in show_sparql_query - could not get value for LINK2RODINSPARQLENDPOINT",'red');
		$SPARQLQ=$LINK2RODINSPARQLENDPOINT
						."?storename=$RODINRDFSTORENAME"
						."&QUERY=".urlencode($Q);
		
		$HQ= str_replace("\n","<br>",htmlentities($Q));
		
		return "<a href='$SPARQLQ' title='Click to execute this query directly on RODINs SPARQL ENDPOINT on store $storename' target='_blank'><b>Try query</b> directly</a><br>$HQ";
	}

//$REFERER=get_referer(); // global
$ARCUTILITIES=1;
?>
