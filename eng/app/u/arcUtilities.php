<?php

/**
 * arcUtilities.php ARC2 Utilities
 * Author: Fabio Ricci
 * For HEG
 * Email: fabio.ricci@ggaweb.ch
 * Tel: +41-76-5281961
 */

/*The following can be used as mechanism for carrying complex data inside triples using base64 coding: */

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
												  $added_triples,
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
		
		$TOBECODED64=$RDFenhancementCLASS::$TOBECODED64;
		
		$TITLE_TPAGELINK="Click to open rodin w3s triple page in new tab";
		$HTML='';
		//print "<br>QUERY:<br>".htmlentities($QUERY);
		if ($rows = $RDFenhancementCLASS::$store->query($QUERY, 'rows')) 
		{
			$i=0;
			$TRIPLECOUNT = count($rows);
			$STORENAME=$RDFenhancementCLASS::$storename;
			$HTML.= "<table  border='1' class='$tableclass'>";
			$HTML.= "<tr><td colspan='4'><b>$added_triples added triples ($TRIPLECOUNT TRIPLES in STORE '$STORENAME') $TITLE</b></td></tr>";
			foreach($rows as $row) 	
			{	$i++;
			
				$subject	= prettyprintURI($row['s'],$RDFenhancementCLASS::$NAMESPACES) ;
				$predicate= prettyprintURI($row['p'],$RDFenhancementCLASS::$NAMESPACES) ;
				
				$predicatex= printURI($row['p'],$RDFenhancementCLASS::$NAMESPACES) ;
				$object		= prettyprintURI(decode_64_literal($predicatex,$row['o'],$TOBECODED64),$RDFenhancementCLASS::$NAMESPACES) ;
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
			$HTML.= "</table>";
		}
		return $HTML;
	} // print_triples
			

			
			
			
			
		function timestamp_fortripleannotation()
		{
			
			$now =  time() .'_'. str_pad(substr((float)microtime(), 2), 6, '0', STR_PAD_LEFT);
			
			return $now;
		}	
			
			
		
		function decode_64_literal($p,$literal,&$TOBECODED64)
		{
			if ($TOBECODED64{$p})	
					$literal=base64_decode($literal);
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
						
						if (($bc=count($broaders))>0)
						{
							$RDFLOG.="<br><i>$bc Broaders:</i>";
							foreach($broaders as $b)
							$RDFLOG.="<br>&nbsp;&nbsp; $b";
						}
						
						if (($nc=count($narrowers))>0)
						{
							$RDFLOG.="<br><i>$nc Narrowers:</i>";
							foreach($narrowers as $n)
							$RDFLOG.="<br>&nbsp;&nbsp; $n";
						}
	
						if (($rc=count($related))>0)
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
																			$namespace_short,
																			$tableclass='triplepagetable_'.$namespace_short);
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
	function get_triples_as_navi_page(	&$RDFenhancement,
																			$word_id,
																			$pagetitle,
																			$namespace_short,
																			$tableclass='triplepagetable')
	{
		global $RODINLOGO;
		global $URL_MANTIS;
		global $W3SLABHOMEPAGEURL;
		$RDFenhancementCLASS = get_class($RDFenhancement);
		$GRAPH=$RDFenhancementCLASS::$importGraph;
		
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
		$w_full=correct_rodin_url(  get_full_url($word_id,$RDFenhancementCLASS::$NAMESPACES), $RDFenhancementCLASS::$NAMESPACES  );
		$w_target=(strstr($word_id,'rodin:')||strstr($word_id,'rodin_e:')||strstr($word_id,'rodin_a:'))?"":"target='_blank'";
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
			<span class='$TRIPLEPAGECLASS'>$URL_MANTIS</span>
			</td>
		</tr>
		<tr height='20'>
		<td valign='top'>
    <div id="header" >
			  <h2 id="title">About $WORDID_HREF</h2>
		  	<div class="page-resource-uri" title="An entity is connotated by direct (and inverse) properties obtained traversing the graph centerd on the subject '$word_id'">
			    An {$METATYPED}entity $EVTL_ENTITY_TYPE_DEF 
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

	$i=1;
	foreach($p_o as $p=>$ooo)
	{
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
			 
			$even = !($i%2); 
			$bgclass=($even?'even':'odd').'_'.$namespace_short;
		
			$p_full=correct_rodin_url(  get_full_url($p,$RDFenhancementCLASS::$NAMESPACES), $RDFenhancementCLASS::$NAMESPACES  );
			$pns=get_ns($p);
			$pshort=str_replace("$pns:",'',$p);
			$class="literal";
			$p_target=(strstr($p,'rodin:')||strstr($p,'rodin_e')||strstr($p,'rodin_a'))?"":"target='_blank'";
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
					$o_target=(strstr($o,'rodin:')||strstr($o,'rodin_e:')||strstr($o,'rodin_a:'))?'':"target='_blank'";
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
		$p_full=correct_rodin_url(  get_full_url($p,$RDFenhancementCLASS::$NAMESPACES), $RDFenhancementCLASS::$NAMESPACES  );
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
			
			
			$s_full = correct_rodin_url($s_full,$RDFenhancementCLASS::$NAMESPACES);
			$s_short=str_replace("$ns:",'',$s);
			$s_target=(strstr($s,'rodin:')||strstr($s,'rodin_e:')||strstr($s,'rodin_a:'))?'':"target='_blank'";
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
			if (substr($dirname,strlen($dirname)-1,1)<>'/') 
			$dirname.='/';
			
			//in case rodin house address matches ... correct:
			// print "<br><br>dirname: $dirname";
			// print "<br>filename: $filename";
			// print "<br>rodin_a: ".$NAMESPACES{'rodin_a'};
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
		//
		return $url;
	}
	
	
	
	
	/**
	 * retuns the value of 
	 */
	function lookup_firstvalue($s,$p,&$RDFenhancement)
	{
		$o=null;
		$RDFenhancementCLASS = get_class($RDFenhancement);
		
		$QUERY = $RDFenhancementCLASS::$NAMESPACES_PREFIX . " select ?o { $s $p ?o . } limit 1";
		
		//print "<br>Query:<br>".str_replace("\n","<br>",htmlentities(urldecode($QUERY)));
		
		if ($rows = $RDFenhancementCLASS::$store->query($QUERY, 'rows')) 
		{			
				foreach($rows as $row) 	
				{
					$o_full=$row['o'];
					$o = separate_namespace($RDFenhancementCLASS::$NAMESPACES,$o_full,':',false);
					//print " O FOUND ";
					break;
				}
		}
		
		//print "<br>lookup_firstvalue($s,$p) returning ($o)";
		
		return $o;
	} // lookup_firstvalue
	
	
	//Convenience function
	function exists_in_store($s,$p,&$RDFenhancement)
	{
		return (lookup_firstvalue($s,$p,$RDFenhancement) == null);
	}
	
	


	function get_ARC_triples_for_viz(&$store,$searchterm,&$NAMESPACES)
	##################################
	# 
	# Computes the query to SKOS $verb
	# $verb= related, broader, narrower
	{
		if (!$searchterm)
		$QUERY=<<<EOQ
		select ?s ?p ?o 
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
		$debug=0;
	  //OPTIMIZE
	  
	  if (!count($namespaces)) fontprint("<br>separate_namespaces(".htmlentities($term).") called with EMPTY namespaces",'red');
	  if ($debug) print "<br><br>separate_namespace(".htmlentities($term).") ... Test substitution on <b>$term</b>";
	  if (!(strstr($term,'http://')))
	  {
	    if ($debug) print "<br>Exit: Term '<b>$term</b>' already done";
	    return $term;
	  }
	  else
	  {  
	    foreach($namespaces as $ns=>$expr)
	    {
	      $expr2=str_replace("/","\/",$expr);
	      $pattern="/$expr2(.*)/";
	
	      if ($debug) print "<br>Pattern $pattern ";
	
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
	        if ($debug) print " YES ";
	        break;
	      }
	    }
	
	    if (!$matched) 
	    {
	      $returnterm=$term;
	      if ($debug) print " ERROR applying $pattern -> $returnterm<br>";
	    } 
	    else 
	    {
	    	if ($debug) print " SUCCESS: $term -> $returnterm<br>";
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
																											$cache_id,
																											&$NAMESPACES,
																											$lang,
																											$limit=5 )
	{
		$debug=1;
		global $RDFLOG;
		$subject_arr = array();
		if (!$cache_id) $cache_id="relatedsubjects.$src_name.$subject.$lang.$limit";
		
		Logger::logAction(27, array('from'=>'get_related_subjects_from_sparql_endpoint','msg'=>"Start with cache_id $cache_id"));
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

		//print "<br> get_related_subjects_from_sparql_endpoint cache_id=$cache_id";

		if ($debug)
		$RDFLOG.= "<br>get_related_subjects_from_sparql_endpoint:";

		$url_endpoint= $sds_sparql_endpoint
								 	.'?query='.urlencode($sparqlquery)
									.'&'.$sds_sparql_endpoint_params;
		if ($debug) {
      	$RDFLOG.= "<br><br>USING SOLR CACHED SOURCE for subject expansion ".str_replace("\n","<br>",htmlentities(urldecode($url_endpoint)))."<br><br>";
     }
		
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
      	$RDFLOG.= htmlprint("<br><br>CALLING REMOTE SOURCE for subject expansion ".$url_endpoint."<br><br>",'red');
      }
			Logger::logAction(27, array('from'=>'get_related_subjects_from_sparql_endpoint','msg'=>"Open $url_endpoint"));
			
			$xml_content=get_file_content($url_endpoint);
		
			cache_src_response($cache_id,$xml_content);
			$xmlCached_content = $xml_content;
		}
		else {
			if ($debug)
			{
      	$RDFLOG.= htmlprint("<br><br>USING CACHED SOURCE CONTENT for subject expansion ".$url_endpoint."<br><br>",'green');
			}
		}

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
	 		Logger::logAction(27, array('from'=>'get_related_subjects_from_sparql_endpoint','msg'=>"Invalid XML retrieved ($xml_content_len bytes)"));
		}
		
		if ($valid_xml)
		{
			//scan/open $xmlCached_content for later use
			//print "<br>XML GOT FROM $src_name:<br>".htmlentities($xmlCached_content);
			$xml_content_len = strlen($xmlCached_content);
			Logger::logAction(27, array('from'=>'get_related_subjects_from_sparql_endpoint','msg'=>"Analyse content ($xml_content_len bytes)"));
			
			$sxmldom=simplexml_load_string($xmlCached_content,'SimpleXMLElement', LIBXML_NOCDATA);
			//if (is_array($sxmldom->results))
			{
				//print "<br>ARRAY subext $src_name on '$subject': <br>";var_dump($sxmldom->results->result);
				foreach($sxmldom->results->result as $_=>$result)
				{
					$rel_sub=trim($related_sub=$result->binding->literal."");
					if ($rel_sub<>'')
					{
						$subject_candidate = $rel_sub;	
						//Add only iff filtered is once and in the same lang	
						if ($debug) $RDFLOG.="<br>CONSIDER nth related subject ($subject_candidate)";
						insert_filtered_once($subject_candidate,$subject_arr,$lang);
					}
				}
			}
			
			if ($debug)
			{
				$subject_arr_noof=count($subject_arr);
				$RDFLOG.= "<br>RETURNING $subject_arr_noof SPARQL related subjects to $subject from $src_name:";
				foreach($subject_arr as $sub)
				{
					$RDFLOG.= "<br>$sub";
				}
			}
			
			Logger::logAction(27, array('from'=>'get_related_subjects_from_sparql_endpoint','msg'=>"Exit"));
		} // valid xml
		return $subject_arr;
	}	// get_related_subjects_from_sparql_endpoint
		
		
		
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
		
		if (!discard_subject_word_candidate($candidate))
		{
			//The following is the same as on a triple subject but allows minus sign as trade d'union (natural language)
			if (($subject=filter_as_subject($candidate,'/[0-9&!;\\\.\:_\[\]\(\)]+/')))
			{
				//print "==> FILTERED TO: $subject";
				if ($DEBUG) $RDFLOG.="<br>Considering subject '$subject'";
				
				if ($lang=='' || ($langt=detectLanguage($subject))==$lang || ($tolerated_lang<>'' && $langt==$tolerated_lang))
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
	 * @param string $subject
	 * @param string $src_name
	 * @param string $sds_sparql_endpoint
	 * @param string $sds_sparql_endpoint_params
	 * @param vector &$NAMESPACES
	 * @param integer $limit
	 * 	 *  
	 */	
	function get_cached_triples_on_subject_from_sparql_endpoint(	$subject,
																																$subject_uid,
																																$src_name,
																																$solr_cache_max_age_in_sec,
																																$sds_sparql_endpoint,
																																$sds_sparql_endpoint_params,
																																&$NAMESPACES,
																																$lang,
																																$tripleslimit=200)
	{
		global $RDFLOG;
		
		$debug=0;
		$subject_arr = array();
		$cache_id="expansion.$src_name.$subject.$lang.$tripleslimit";		
		$dce_ns_url=$NAMESPACES{'dce'}
;		
		$sparqlquery=<<<EOQ
PREFIX dce: <$dce_ns_url>

select distinct ?s ?p ?o
{
?s dce:subject "$subject" .
?s dce:language ?lang . 
FILTER regex(?lang, "$lang") .
?s ?p ?o 
} limit $tripleslimit
EOQ;

		if ($debug)
		$RDFLOG.= "<br>get_cached_triples_on_subject_from_sparql_endpoint:<br><br>query:<br>".(str_replace("\n","<br>",htmlentities($sparqlquery)))."<br><br>";

		$url_endpoint= $sds_sparql_endpoint
								 	.'?query='.urlencode($sparqlquery)
									.'&'.$sds_sparql_endpoint_params;
		
		//cache_src_response($cache_id,$xml_src_content)
    Logger::logAction(27, array('from'=>'get_cached_triples_on_subject_from_sparql_endpoint','msg'=>'Started with cacheid:'.$cache_id));
    list($xmlCached_content,
            $creationtimestamp,
            $age_in_sec,
            $max_age_in_sec, // this is always set!! even if no data
            $expiring_in_sec) = get_cached_src_response($cache_id,$solr_cache_max_age_in_sec);

    if (! sparql_cached_content_quality_control($xmlCached_content,$src_name,$age_in_sec))
    { // ask service and rebuild cache
      $used_cache=false;
      $timestamp=date("d.m.Y H:i:s");
      $timestamp0=time();
      $age_in_sec=0;
      $expiring_in_sec=$max_age_in_sec;
      if ($debug) {
      	$RDFLOG.= "<br><br>CALLING REMOTE SOURCE ".$url_endpoint."<br><br>";
      }
			
			Logger::logAction(27, array('from'=>'get_cached_triples_on_subject_from_sparql_endpoint','msg'=>"Open $url_endpoint"));
			
			$xml_content=get_file_content($url_endpoint);
			
			$xml_content = utf8_encode($xml_content);
			
			cache_src_response($cache_id, $xml_content);
			$xmlCached_content = $xml_content;
		} 
		else {
			$used_cache=true;
		}

		//scan/open $xmlCached_content for later use
		if ($debug) $RDFLOG.= "<br>XML GOT FROM $src_name:<br>".htmlentities($xmlCached_content);
		
		$xml_content_len=strlen($xml_content);
		$valid_xml=true;
		if (datasource_error($xmlCached_content,$src_name))
		{
			fontprint("<hr>ERROR FROM DATASOURCE called on subject ($subject):"
						."<br>Check for CACHE?<br>"
						."<br>QUERY:<br>"
						.str_replace("\n","<br>",htmlentities($sparqlquery)),'red')
						."<br><br>Using url:"
						."<br>$url_endpoint";
			$valid_xml=false;
	 		Logger::logAction(27, array('from'=>'get_cached_triples_on_subject_from_sparql_endpoint','msg'=>"Invalid XML retrieved ($xml_content_len bytes)"));
		}
		
		
		if ($valid_xml)
		{
	 		Logger::logAction(27, array('from'=>'get_cached_triples_on_subject_from_sparql_endpoint','msg'=>"Analyse content ($xml_content_len bytes)"));
			$sxmldom=simplexml_load_string($xmlCached_content,'SimpleXMLElement', LIBXML_NOCDATA);
			if ($sxmldom)
			{
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
			}
			
			$triples_noof=count($triples);
			
			if ($debug)
			{
				$RDFLOG.= "<br>RETURNING $triples_noof SPARQL triples to $subject from $src_name:";
				foreach($triples as $triple)
				{
					list($$s,$p,$o)= $triple;
					$RDFLOG.= "<br><b>TRIPLE</b>: $s $p $o";
				}
			}
			
			Logger::logAction(27, array('from'=>'get_cached_triples_on_subject_from_sparql_endpoint','msg'=>"Exit with $triples_noof triples"));
		}


		return array($triples,$used_cache);
	}	// get_cached_triples_on_subject_from_sparql_endpoint
			
		
		
		
		
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
			if ($lastc=='n')
				$lastsubtract=$lastc;
		}
			
		if($lastsubtract)
			$singular_word=substr($word,0,strlen($word)-3);
		else
			$singular_word=$word;
		if ($DEBUG)
			$RDFLOG.="<br>put_to_singular($word,$lang), lastc=($lastc) lastsubtract=($lastsubtract) returning ($singular_word)";
		
		return $singular_word;
	} // put_to_singular
	
	
	
		
 	function cleanup4literal($str)
	{
		//$str0=$str;
		//print "<br>cleanup4literal($str)->";
		//Do not want to see the following chars inside the term $txt0
		if (!strstr($str,'http://'))
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
	function rank_docs_with_its_subjects($docs,&$RDFenhancementCLASS,$PREFIX)
	{
		$debug=0;
		global $RDFLOG;
		$docs_ranked = array();
		
		if (count($docs))
		foreach($docs as $doc_uid)
		{
			
			$QUERY=<<<EOQ
				$PREFIX
				select count(?sub) as c
				where
				{
					$doc_uid dce:subject ?sub .
				}
EOQ;

			if ($debug)
				$RDFLOG.="<br>rank_docs_with_its_subjects($doc_uid): <br>".str_replace("\n","<br>",htmlentities($QUERY));
	
			if ($rows = $RDFenhancementCLASS::$store->query($QUERY, 'rows')) 
			{
				if (is_array($rows) && count($rows))	
				{
					$row = $rows[0];
					if($debug) $RDFLOG.="<br>Setting $doc_uid => ".$row['c'];
					$docs_ranked{$doc_uid} = intval($row['c']);
				}
			}
		} // foreach
		
		if ($debug)
		{
			$oc=count($docs_ranked);
			$RDFLOG.="<br>rank_docs_with_its_subjects $oc ranked Objects found: "; 
			foreach($docs_ranked as $docuid=>$rank)
			{
				$RDFLOG.="<br>docr: $docuid=>$rank";
			}
			$RDFLOG.="<br>EXIT";
		}
		
		arsort($docs_ranked);
		
		return $docs_ranked;
	} // rank_doc_with_its_subjects
	
	
	
	
	function get_external_rdf_docs($searchuid, &$RDFenhancementCLASS, $PREFIX2)
	{
		$debug=0;
		global $RDFLOG;
		
		$QUERY_EXT_DOC=<<<EOD
$PREFIX2
select distinct ?docext
where 
{
 ?doc dce:subject ?sub .
 { 
  $searchuid rodin_a:resultdoc ?doc . 
  ?docext dce:subject ?sub .
 }
 UNION
 {
  ?doc rodin:subject_related ?subx .
  ?docext dce:subject ?subx .
 }
 FILTER Regex ( ?docext , "/resource/e/" ) .
}
EOD;

	if ($debug)
			$RDFLOG.="<br>get_external_rdf_docs($s,$p): <br>".str_replace("\n","<br>",htmlentities($QUERY_EXT_DOC));
	
	if ($rows = $RDFenhancementCLASS::$store->query($QUERY_EXT_DOC, 'rows')) 
		{
			if (is_array($rows) && count($rows)>0)
			{	
				foreach($rows as $row)
				{
					$o_full=$row['docext'];
					$o[] = separate_namespace($RDFenhancementCLASS::$NAMESPACES,$o_full,':',false);
				}
			}
		}
		
		if ($debug)
		{
			$oc=count($o);
			$RDFLOG.="<br>$oc Objects found: "; 
			foreach($o as $docuid)
			{
				$RDFLOG.="<br>doc: $docuid";
			}
		}
		return $o;
	} // get_external_rdf_docs
	
	
	

	/**
	 * Returns an s string value: subject_uid used by the first triple having value
	 */
	function get_triple_objects($s,$p,&$RDFenhancementCLASS,$PREFIX)
	{
		$debug=0;
		global $RDFLOG;
		$store=$RDFenhancementCLASS::$store;
		$PREFIX=$PREFIX<>''?$PREFIX:$RDFenhancementCLASS::$NAMESPACES_PREFIX;
		$o=array();
		
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

		if ($debug)
			$RDFLOG.="<br>get_triple_objects($s,$p): <br>".str_replace("\n","<br>",htmlentities($QUERY));
	
		if ($rows = $RDFenhancementCLASS::$store->query($QUERY, 'rows')) 
		{
			if (is_array($rows) && count($rows)>0)
			{
				foreach($rows as $row)
				{
					if ($need_p)
					{
						$p_full=$row['p'];
						$p[] = separate_namespace($RDFenhancementCLASS::$NAMESPACES,$p_full,':',false);
					}
					$o_full=$row['o'];
					$o[] = separate_namespace($RDFenhancementCLASS::$NAMESPACES,$o_full,':',false);
				}
			}
		}
		
		if ($debug)
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
	 * Returns a list of subjects taken from the src use corresponding to cache_id
	 */
	function rdf_get_subjects_created_in_src_use_lodfetch($cache_id,&$RDFresultCLASS)
	{
		
		$rodin_a_ns_url = $RDFresultCLASS::$NAMESPACES{'rodin_a'};
		$dce_ns_url 		= $RDFresultCLASS::$NAMESPACES{'dce'};
		$PREFIXES				="PREFIX rodin_a: <$rodin_a_ns_url> PREFIX dce: <$dce_ns_url>";
		
		$QUERY=<<<EOQ
	$PREFIXES
	select distinct ?srcuse ?subj
	{
		?srcuse rodin_a:cache_id "$cache_id" .
	  ?srcuse rodin_a:expdoc ?extdoc .
	  ?extdoc dce:subject ?subj .
  }
EOQ;
		
		if ($rows = $RDFresultCLASS::$store->query($QUERY, 'rows')) 
		{
			foreach($rows as $row)
			{			
				$subj_full=$row['subj'];
				$subj[] = separate_namespace($RDFresultCLASS::$NAMESPACES,$subj_full,':',false);
				if (!$src_use_uid)
				{
					$src_use_uid_full=$row['src_use'];
					$src_use_uid = separate_namespace($RDFresultCLASS::$NAMESPACES,$src_use_uid_full,':',false);
				}
				
			}
		}
		return array($src_use_uid,$subj);
	} // rdf_get_subjects_created_in_src_use_lodfetch
	
	
	/**
	 * Returns a list of subjects taken from the src use corresponding to cache_id
	 */
	function rdf_get_subjects_created_in_src_use_subexp($cache_id,&$RDFresultCLASS)
	{
		$subj= array();
		$rodin_a_ns_url = $RDFresultCLASS::$NAMESPACES{'rodin_a'};
		$dce_ns_url 		= $RDFresultCLASS::$NAMESPACES{'dce'};
		$PREFIXES				="PREFIX rodin_a: <$rodin_a_ns_url> PREFIX dce: <$dce_ns_url>";
		
		$QUERY=<<<EOQ
	$PREFIXES
	select distinct ?srcuse ?subj
	{
		?srcuse rodin_a:cache_id "$cache_id" .
	  ?srcuse rodin_a:subexp_related ?subj .
  }
EOQ;
		
		if ($rows = $RDFresultCLASS::$store->query($QUERY, 'rows')) 
		{
			foreach($rows as $row)
			{			
				$subj_full=$row['subj'];
				$subj[] = separate_namespace($RDFresultCLASS::$NAMESPACES,$subj_full,':',false);
				if (!$src_use_uid)
				{
					$src_use_uid_full=$row['src_use'];
					$src_use_uid = separate_namespace($RDFresultCLASS::$NAMESPACES,$src_use_uid_full,':',false);
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
	function rdf_check_existence_of_other_dependencies( $link_predicate, $entity_uid, $src_use_uid, &$RDFresultCLASS )
	{
		global $RDFLOG;
		$debug=1;
		$rodin_a_ns_url = $RDFresultCLASS::$NAMESPACES{'rodin_a'};
		$dce_ns_url 		= $RDFresultCLASS::$NAMESPACES{'dce'};
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
		
		if ($debug)
		{
			$RDFLOG.="<br>rdf_check_existence_of_other_dependencies( $link_predicate, $entity_uid, $src_use_uid)<br><br>QUERY:<br>"
							 .str_replace("\n","<br>",htmlentities(urldecode($QUERY)));
		}
		
		
		if ($rows = $RDFresultCLASS::$store->query($QUERY, 'rows')) 
		{
			$row=$rows[0];
			{			
				$other_source_full=$row['other_source'];
				$other_source = separate_namespace($RDFresultCLASS::$NAMESPACES,$other_source_full,':',false);
				
				if ($debug)
				{
					$RDFLOG.="<br>Other source for entity $entity_uid found: $other_source";
				}
			}
		}
		else
			$other_source=false;
		
		
		if ($debug)
				{
					$RDFLOG.="<br>rdf_check_existence_of_other_dependencies returning: ($other_source)";
				}
		
		return $other_source;
	} // rdf_check_existence_of_other_dependencies
	
	
	
	/**
	 * deletes every triple with subject $entity_uid
	 * 
	 * returns NOTHING (since in this case in ARC2 no return value is produced)
	 */
	function rdf_delete_entity($entity_uid,&$linkpredicates,&$RDFresultCLASS)
	{
		global $RDFLOG;
		$debug=1;
		$rodin_a_ns_url = $RDFresultCLASS::$NAMESPACES{'rodin_a'};
		$dce_ns_url 		= $RDFresultCLASS::$NAMESPACES{'dce'};
		$PREFIXES				="PREFIX rodin_a: <$rodin_a_ns_url> PREFIX dce: <$dce_ns_url>";
		
		$linkpredicate=is_array($linkpredicates)&&count($linkpredicates)?$linkpredicates[0]:false; // used until now with one elem 'rodin:subject_related'
		
		if ($linkpredicate)
	{
		$EVTL_LINK=" + $linkpredicate";
		$DELETE_LINKS=<<<EOQ
	$PREFIXES
	delete {
	 $entity_uid ?p ?o .
	 ?sa $linkpredicate $entity_uid .
	}where
	{
	 {
	   $entity_uid ?p ?o .
	 }
	 UNION
	 {
	  $entity_uid ?p ?o .
	  ?sa rodin:subject_related $entity_uid .
	 }
	}
EOQ;
}
		else	
		
		$DELETE_LINKS=<<<EOQ
	$PREFIXES
	delete {?s ?p ?o}
	where
	{
		$entity_uid ?p ?o .
		?s ?p ?o .
  }
EOQ;
		
		if ($debug)
		{
			$RDFLOG.="<br>rdf_delete_entity($entity_uid$EVTL_LINK)<br><br>QUERY:<br>"
							 .str_replace("\n","<br>",htmlentities(urldecode($DELETE_LINKS)));
		}
		
		$RDFresultCLASS::$store->query($DELETE_LINKS);
	} // rdf_delete_entity
	
	
	
	/**
	 * Deletes the triples of an src use (rodin_a: namespace)
	 * and all the dependend triples around them (if not dependent from another source)
	 */
	function rdf_delete_entity_on_cacheid($cache_id,&$link_predicates,&$RDFresultCLASS)
	{
		global $RDFLOG;
		$debug=0;
	
		$rodin_a_ns_url = $RDFresultCLASS::$NAMESPACES{'rodin_a'};
		
		
		
		
		
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
		
		if ($RDFresultCLASS::$store)
		{
			if ($debug) $RDFLOG.= "<br>querying: ".htmlentities($sparql_deletequery);
			$RDFresultCLASS::$store->query($sparql_deletequery);
		}
	
	} // rdf_delete_entity_on_cacheid
	
	
	
	
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

//$REFERER=get_referer(); // global
$ARCUTILITIES=1;
?>
