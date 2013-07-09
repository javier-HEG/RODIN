
<?php 

include_once("root.php");
include_once("u/arcUtilities.php");
 $NOW=date("d.m.Y_H:i:s");

 $MANTIS ="<a href=\"http://195.176.237.62/mantis/\" target=_blank>mantis</a>";
 $MANTIS_ISSUE_SMOKETEST ="<a href=\"http://195.176.237.62/mantis/bug_report_advanced_page.php?summary=SMOKETEST_ON_{$NOW}_SEGMENT???\" target='_blank'>mantis task</a>"; 
 
 $RODINSMOKETEST_DIR_URL="http://195.176.237.62/rodin/tests/smoketest/";
 $US_LINK = "<a href='mailto:fabio.ricci@ggaweb.ch?subject=RODIN_SMOKE_TEST_ON_{$NOW}_SEGMENT???'>us</a>";
 ?>

<a href="http://campus.hesge.ch/id_bilingue/projekte/rodin/index_fr.asp" target=_blank>RODIN</a>
<br />
<br /><b>DEVELOPMENT / RELEASE INFOS </b>
<br />
<br />4.4.2013 
<br />By Fabio Ricci (FRI) - Tel. 076-5281961
<br />and Javier Belmonte (BEL)
<br />Please report any issue using <?php echo $MANTIS ?> 
<br />
<br />
<br /><b>RODIN Release 2.8.3</b>
<br />===================
<br />SOLR integration for results
<br />Aggregated view for results
<br />Results higlighting
<br />Semantic filtering
<br />Higher speed through SRC optimization
<br />New SKOSxl ontology source SOZ "theSoz" integrated per Dec 18th 2012
<br />New SKOS ontology source RAMEAU integrated per Dec 30th 2013
<br />New SKOS ontology source LOC integrated per Dec 30th 2013
<br />New NON-SKOS ontology source GND integrated per Jan 25th 2013
<br />RDF LAB with LOD input from Europeana on single results
<br />SRC driven AutoComplete with SKOS and description information
<br />RDFization of results, docs importing, ranking of all documents.
<br />NEW ONTOFACETS LAYOUT - more responsive and more compact
<br />Improved GUI - following the suggestions of the great RODIN team
</p>
Please report any bug issue to <?php echo $MANTIS ?>
 together with the  
<br /> - timestamp - (when did it appear?)
<br /> - name of the widget
<br /> - value used for the query
<br /> - warning/error.
<br />  

<p> <i>Feel bored? Need a test?</i> <br>Do not hesitate 
<br>to send <?php print $US_LINK?> or to issue a <?php print $MANTIS_ISSUE_SMOKETEST?>
<br>on a <a href='<?php print $RODINSMOKETEST_DIR_URL?>' target='blank' title='Click to switch to a download area "smoketest" containing rodin test documents to fill and send'>SMOKE or DEEP RODIN Test</a>
</p>
<?php 

	$ADMIN_USING=strstr($_SESSION['RODINADMINEMAILADDR'],'fabio') || strstr($_SESSION['RODINADMINEMAILADDR'],'rodinuser');
  //if ($RODINSEGMENT<>'p')
  {
  	print "<b><i>USEFUL LINKS:</i></b>";
    $SRC_CONTROL_LINK="$WEBROOT$RODINROOT/$RODINSEGMENT/fsrc/app/u/select_src.php?".($ADMIN_USING?'u=2':'');
    $SRC_PEVAL_LINK="$WEBROOT$RODINROOT/$RODINSEGMENT/fsrc/app/u/SOLR_SKOS_partial_evaluator/";
    $SRC_STORE_SKOS_NAVIGATOR_LINK="$WEBROOT$RODINROOT/$RODINSEGMENT/fsrc/app/u/store_skosxl_navigator.php?storename=bnf_rameau";
    $SRC_STORE_SPARQL_LINK="$WEBROOT$RODINROOT/$RODINSEGMENT/fsrc/app/u/lod_sparql_endpoint.php?limit=5&storename=bnf_rameau";
    $SRC_U_LINK="$WEBROOT$RODINROOT/$RODINSEGMENT/fsrc/app/u/";
    $SRC_U_LINK="$WEBROOT$RODINROOT/$RODINSEGMENT/fsrc/app/u";
		$DBRODINLODBROWSER="$WEBROOT$RODINROOT/$RODINSEGMENT/app/lod/resource/a/?token=search*&seeonly=resultdoc";
    
    print "<br><a href='$DBRODINLODBROWSER' target='blank'>dbRODIN LoD browser</a>";
    print "<br><a href='$SRC_STORE_SPARQL_LINK' target='blank'>dbRODIN LoD SPARQL endpoint</a>";
		print "<br><a href='$lodLABHOMEPAGEURL' target='blank'>RODIN lod RDF expansion LAB</a>";
    print "<br><a href='$SRC_CONTROL_LINK' target='blank'>SRC MANAGEMENT FOR CURRENT SEGMENT</a>";
    if ($ADMIN_USING) print "<br><a href='$SRC_PEVAL_LINK' target='blank'>SRC PARTIAL EVALUATOR FOR SOLR</a>";
  }
?>
<!--p>
<b>Integration with SRC - Details</b>
<br />===============================
<br />
<br />URL used for START &nbsp;: <?php echo $SRCreal; ?>/<b>start</b>?user=id
<br />URL used for REFINE : <?php echo $SRCreal; ?>/<b>refine</b>?params - (see spec)
<br />
<br />CALLS ROGINDUI -> RODINSRC (can) be stored in DB and can be viewed using <a href="<?php echo $SRC_INTERFACE_BASE_URL; ?>/calls.php" target="_blank"> this link</a>
<br /-->

<br><b>Integration with SOLR - Details:</b>
<br /><a href="http://localhost:8885/solr/#/"> SOLR ADMIN </a>
<br /><a href="http://localhost:8985"> SOLR LUCID </a>
</p>


