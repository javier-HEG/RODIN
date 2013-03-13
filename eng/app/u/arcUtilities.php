<?php

/**
 * arcUtilities.php ARC2 Utilities
 * Author: Fabio Ricci
 * For HEG
 * Email: fabio.ricci@ggaweb.ch
 * Tel: +41-76-5281961
 */
$PUBBLICATION_URL="http://$HOST/masi/$MASISEGMENT/app/w3s";

//print "PUB URL: ".htmlentities($PUBBLICATION_URL);
$NAMESPACES= array(
		'foaf'	=> 'http://xmlns.com/foaf/0.1/',
		'rdf'		=> 'http://www.w3.org/1999/02/22-rdf-syntax-ns#',
    'rdfs'	=> 'http://www.w3.org/2000/01/rdf-schema#',
    'geo'		=> 'http://www.w3.org/2003/01/geo/wgs84_pos#',
		'dbo'		=> 'http://dbpedia.org/ontology/',
    'dc'		=> 'http://purl.org/dc/elements/1.1/',
    'bio'		=> 'http://vocab.org/bio/0.1/',
    'bibo'	=> 'http://bibliontology.com/bibo/bibo.php#',
    'rodin'	=> $PUBBLICATION_URL.'/resource/',
      		);

$TOBECODED64 = array('dc:description','rodin:title_orig','rodin:subtitle_orig','rodin:genealogic_tree','rodin:abbreviation');


foreach($NAMESPACES as $ns=>$nsurl)
	$NAMESPACES_PREFIX.="PREFIX $ns: <$nsurl>\n";	


$filenamex="app/root.php"; $max=10;
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








	/**
	 * Imports triples into store
	 * @param $storename The store to be used
	 * @param $triples A vector of triples (arrays(s,p,o))
	 * returns: Statistic object reflecting import process
	 * 
	 * Important: the object of a triple must have <> or "" to denote literal
	 */
	function import_triples($storename='rodin',&$triples)
	{
		global $ARCCONFIG, $NAMESPACES_PREFIX;
		$LOCALCONFIG=$ARCCONFIG;
    $LOCALCONFIG{'store_name'}=$storename;
    $store = ARC2::getStore($LOCALCONFIG);
    if (!$store->isSetUp()) {
       $store->setUp();
    }
		
		
		
		$TRIPLETEXT="
		$NAMESPACES_PREFIX
    INSERT INTO <http://localhost/> 
    {";
 	
		
		foreach($triples as $triple)
		{
			$s=$triple[0];
			$p=$triple[1];
			$o=cleanup4literal($triple[2]); // literals might contain ' '' ... addslashes?
			
	  	$TRIPLETEXT.="\n $s $p $o .";
		}
		
		$TRIPLETEXT.='}';
		
		//$debug=1;
		if($debug) print "<br>ARC CONSTRUCTING: <hr>".str_replace("\n","<br>",htmlentities($TRIPLETEXT));	
		
		$num_triples_before=count_ARC_triples($store);
    $num_triples_before_formatted=number_format($num_triples_before, 0, '.', "'");

    //We need on the server at HEG to enhance php execution time limit, 
    //since this server is slowlee and need more time than the local power macs
    set_time_limit ( 1000000 ); // 250h -> Feature in 5.3.0 deprecated, in 5.4.0 deleted - but useful right now
    $rs=NULL;
    $repetitions=0;
    $added_triples=0;
		
    $rs= $store->query($TRIPLETEXT);
    $added_triples = intval($rs['result']['t_count']);
    $repetitions++;
    if (($errs = $store->getErrors())) {

      foreach($errs as $err)
      fontprint("<br>ARC ERROR: $err",'red');
    }
    
		$duration = $rs['query_time'];
		//$added_triples = $rs['result']['t_count'];
		$load_time = $rs['result']['load_time'];

    $num_triples_before_formatted=number_format($num_triples_before, 0, '.', "'");

    $added_triples_formatted=number_format($added_triples, 0, '.', "'");

	  $delta_triples=abs($num_triples_before - $added_triples);
	  $delta_triples_formatted=number_format($delta_triples, 0, '.', "'");
	 
	  $verb= ($delta_triples==0)?'Updated':'Loaded';  
    
		if ($delta_triples > 0)
		$EVTL_ADDED="Added $delta_triples_formatted triples";
		if(0)
		print " <hr>$verb $added_triples_formatted triples in <b>$storename</b> local triple store
            <br>duration: $duration sec
            <br>load_time: $load_time sec
            $EVTL_ADDED
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
    }
    //Avoid updating statistics if no triples added...
    
    
    return $statistics;
		
	} // import_triples



	/**
	 * gets the triples from $storename using s
	 * @param $storename
	 * @param $s
	 */
	function get_triples_p_o_from_s(	
													$storename='rodin',
													$s)
	{
		global $ARCCONFIG, $NAMESPACES, $NAMESPACES_PREFIX;
		$LOCALCONFIG=$ARCCONFIG;
    $LOCALCONFIG{'store_name'}=$storename;
    $store = ARC2::getStore($LOCALCONFIG);
    if (!$store->isSetUp()) {
       $store->setUp();
    }
		$QUERY=$NAMESPACES_PREFIX." select ?p ?o { $s ?p ?o . }";
		
		//print "<br>QUERY get_triples_p_o_from_s: ".htmlentities($QUERY);
		
		if ($rows = $store->query($QUERY, 'rows')) 
		{
			$i=0;
			foreach($rows as $row) 	
			{	$i++;
				$p_short=separate_namespace($NAMESPACES,$row['p'],':',false);
				$o_short=separate_namespace($NAMESPACES,$row['o'],':',false);
				$o_lang=$row['o_lang'];
				$o_short_decoded=decode_64_literal($p_short,$o_short);
				$p_o[]=array($p_short,$o_short_decoded,$o_lang);
			}
		}
		return $p_o;
	} // get_triples_p_o_from_s
	
	
	/**
	 * gets the triples from $storename using p
	 * or all triples from store rodin
	 * @param $storename
	 * @param $p
	 */
	function get_triples_s_o_from_p(	
													$storename='rodin',
													$p)
	{
		global $ARCCONFIG, $NAMESPACES;
		$LOCALCONFIG=$ARCCONFIG;
    $LOCALCONFIG{'store_name'}=$storename;
    $store = ARC2::getStore($LOCALCONFIG);
    if (!$store->isSetUp()) {
       $store->setUp();
    }
 	  $QUERY="select ?s ?o { ?s $p ?o . }";
		
		if ($rows = $store->query($QUERY, 'rows')) 
		{
			$i=0;
			foreach($rows as $row) 	
			{	$i++;
			
				$s_short=separate_namespace($NAMESPACES,$row['s'],':',false);
				$o_short=separate_namespace($NAMESPACES,$row['o'],':',false);
				$o=decode_64_literal($p,$o_short);
				$s_o[]=array($s,$o);
			}
		}
		return $s_o;
	} // get_triples_s_o_from_p
	
	
			
	/**
	 * gets the triples from $storename using p
	 * or all triples from store rodin
	 * @param $storename
	 * @param $p
	 */
	function get_triples_s_from_p_o(	
													$storename='rodin',
													$p,
													$o,
													$RESTRICTIONS='')
	{
		global $ARCCONFIG, $NAMESPACES, $NAMESPACES_PREFIX;
		$LOCALCONFIG=$ARCCONFIG;
    $LOCALCONFIG{'store_name'}=$storename;
    $store = ARC2::getStore($LOCALCONFIG);
    if (!$store->isSetUp()) {
       $store->setUp();
    }
 	  $QUERY=$NAMESPACES_PREFIX." select distinct ?s { ?s $p $o . $RESTRICTIONS }";
		//print "<br>QUERY: ".htmlentities($QUERY);
		if ($rows = $store->query($QUERY, 'rows')) 
		{
			$i=0;
			foreach($rows as $row) 	
			{	$i++;
				$s_short=separate_namespace($NAMESPACES,$row['s'],':',false);
				$s_vals[]=$s_short;
			}
		}
		return $s_vals;
	} // get_triples_s_from_p_o
			
			

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
														
					$POINTERTRIPLEPAGE_S=	$TPAGELINK_S
																?" <a href='$TPAGELINK_S' target='_blank' title='$TITLE_TPAGELINK'>(3P)</a>"
																:'';
					$POINTERTRIPLEPAGE_P=	$TPAGELINK_P
																?" <a href='$TPAGELINK_P' target='_blank' title='$TITLE_TPAGELINK'>(3P)</a>"
																:'';			
					$POINTERTRIPLEPAGE_O=	$TPAGELINK_O
																?" <a href='$TPAGELINK_O' target='_blank' title='$TITLE_TPAGELINK'>(3P)</a>"
																:'';								
				} 
			
				$HTML.= "<tr>";
				$HTML.= "<td align='right'>$i";
				$HTML.= "</td>";
				$HTML.= "<td nowrap>";
				$HTML.= $subject.$POINTERTRIPLEPAGE_S;
				$HTML.= "</td>";
				$HTML.= "<td nowrap>";
				$HTML.= $predicate.$POINTERTRIPLEPAGE_P;
				$HTML.= "</td>";
				$HTML.= "<td nowrap>";
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
		
		
		
		
		function is_abbreviation($abbr,$storename='rodin')
		{
			global $ARCCONFIG, $NAMESPACES,$NAMESPACES_PREFIX;
			$LOCALCONFIG=$ARCCONFIG;
	    $LOCALCONFIG{'store_name'}=$storename;
	    $store = ARC2::getStore($LOCALCONFIG);
	    if (!$store->isSetUp()) {
	       $store->setUp();
	    }
	    //We have to scann all (at the moment) ...
			$abbr=adapt_name_for_abbreviation($abbr,".()");
	    $QUERY="$NAMESPACES_PREFIX select ?o { rodin:$abbr rodin:abbreviation ?o . }";
			$is_abbr=false;
			//print "<br>Check is_abbreviation($abbr) ... ".htmlentities($QUERY)."<br><br>";
			if ($rows = $store->query($QUERY, 'rows')) 
			{
				//print "".count($rows)." ROWS FOUND ";
				$is_abbr=count($rows) == 1;
			}	
			
			return $is_abbr;
		} // 
		
		
		
		/**
		 * Guarantees the triples navigation
		 */
		function print_triplespage($uid,$pagetitle)
		{
			$nix = null;
			$RDFenhancement = new RodinRDFResult($nix,$nix,$nix,$nix);
			$word_id = RodinRDFResult::$ownnamespacename.':'.$uid;
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
		$w_target=strstr($word_id,'rodin:')?"":"target='_blank'";
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
			$p_target=strstr($p,'rodin:')?"":"target='_blank'";
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
					$o_target=strstr($o,'rodin:')?'':"target='_blank'";
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
		$p_target=strstr($p,'rodin:')?"":"target='_blank'";
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
			$s_target=strstr($s,'rodin:')?'':"target='_blank'";
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
			//print "correct_rodin_url($url) filename=$filename dirname=$dirname";
			
			$url=$dirname.'/?token='.$filename;
			
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
		      									 ?"<a class=\"urigray\" href='#' target=\"_blank\" onclick=\"open_ns('$expr2');return false;\" title=\"Click to explore namespace definition\n$exprH\nin a new tab\">$ns</a>"
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
	  } else
	  {
	    $left= separate_namespace($namespaces,$left.$SEP,':');
	  }  
	      
	
	  return array($left,$right,$literal);
	}	
		
		
		
	/**
	 * Load rodin store with the abbreviations and with the edusubjects
	 */
	function initialize_rodin_store()
	{
		$abbreviations = get_abbreviations_from_db();
		$triple=array();
		foreach($abbreviations as $abbr)
		{
			list($ab,$meaning) = $abbr;
			$rodinabbr='rodin:'.adapt_name_for_abbreviation($ab,".()");
			$triple[]=array($rodinabbr,'rodin:abbreviation', l(base64_encode($meaning)));
			//print "<br><br>TRIPLE $rodinabbr,rodin:abbreviation, l(base64_encode($meaning))=".l(base64_encode($meaning));
		}
		
		$edus = get_edusubjects_from_db();
		foreach($edus as $edusObj)
		{
			list($edusubject,$meaning) = $edusObj;
			$edusubject='rodin:'.$edusubject;
			$triple[]=array($edusubject,'rodin:edusubject', l($meaning));
			//print "<br><br>TRIPLE $edusubject,rodin:edusubject, l(($meaning))=".l(($meaning));
		}
		
		$stat=import_triples('rodin',$triple);
	}	
		
		
	function translate_edusubject_triple($edusubject)
	{
		$edusubjectinfos=get_edusubjectsinfo_from_triples();
		$translation='?';
		foreach($edusubjectinfos as $es=>$meaning)
		{
			if($es==$edusubject) 
			{
				$translation=$meaning;
				break;
			}
		}
		return $translation;
	}	
		
	function get_edusubjects_from_triples()
	{
		$edusubjectinfos=get_edusubjectsinfo_from_triples();
		foreach($edusubjectinfos as $es=>$meaning)
		{
			$edusubjects[]=$es;
		}
		
		//var_dump($edusubjectinfos);
		
		return $edusubjects;
	}
	
	function get_edusubjectsinfo_from_triples($storename='rodin')
	{
		global $ARCCONFIG, $NAMESPACES, $NAMESPACES_PREFIX;
		$LOCALCONFIG=$ARCCONFIG;
    $LOCALCONFIG{'store_name'}=$storename;
    $store = ARC2::getStore($LOCALCONFIG);
    if (!$store->isSetUp()) {
       $store->setUp();
    }
    //We have to scann all (at the moment) ...
		$edusubjectinfo=array();
    $QUERY="$NAMESPACES_PREFIX select ?s ?o { ?s rodin:edusubject ?o . }";
		
		if ($rows = $store->query($QUERY, 'rows')) 
		{
			foreach($rows as $row)
			{
				$s_short=separate_namespace($NAMESPACES,$row['s'],':',false);
				$s=substr($s_short,strpos($s_short,':')+1);
				$edusubjectinfo{$s} = $row['o'];
			}
		}	
		//print "<br>get_edusubjectsinfo_from_triples: "; var_dump($edusubjectinfo);
		
		return $edusubjectinfo;
	}
		
		
		
 	function cleanup4literal($str)
	{
		$str = str_replace("'","\\'",$str);
		return $str;
	}
	
	
	
	
	
	

	/**
	 * returns "$str"
	 */
	function l($str)
	{
		return '"'.addslashes($str).'"';
	}


//$REFERER=get_referer(); // global
$ARCUTILITIES=1;
?>
