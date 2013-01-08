
<?php include_once("root.php");
 $MANTIS ="<a href=\"http://195.176.237.62/mantis/\" target=_blank>mantis</a>";
 ?>

<a href="http://campus.hesge.ch/id_bilingue/projekte/rodin/index_de.htm" target=_blank>RODIN</a>
<br />
<br /><b>DEVELOPMENT / RELEASE INFOS </b>
<br />
<br />18.12.2012 
<br />By Fabio Ricci (FRI) - Tel. 076-5281961
<br />and Javier Belmonte (BEL)
<br />Please report any issue using <?php echo $MANTIS ?> 
<br />
<br />
<br /><b>RODIN Release 2.5</b>
<br />===================
<br />Semantic Filtering introduced
<br />SOLR integration for results
<br />New SKOSxl ontology source SOZ "theSoz" integrated per Dec 18th 2012
<br />
</p>
Please report any bug issue to <?php echo $MANTIS ?>
 together with the  
<br /> - timestamp - (when did it appear?)
<br /> - name of the widget
<br /> - value used for the query
<br /> - warning/error.
<br /> - 
<br /> - Completed features per 12.12.12:
<br /> - --------------------------------
<br /> - Aggregated view (still sth to do...)
<br /> - STW on SOLR
<br /> - Results higlighting
<br /> - Semantic filtering
<br /> - Higher speed through SRC optimization
<?php 
  if ($_ENV['USER']=='fabio' || $_ENV['USER']='riccif')
  {
    $SRC_CONTROL_LINK="$WEBROOT$RODINROOT/$RODINSEGMENT/fsrc/app/u/select_src.php";
    $SRC_PEVAL_LINK="$WEBROOT$RODINROOT/$RODINSEGMENT/fsrc/app/u/SKOS_SOLR_partial_evaluator.php";
    $SRC_U_LINK="$WEBROOT$RODINROOT/$RODINSEGMENT/fsrc/app/u";
    
    print "<br><a href='$SRC_U_LINK' target='blank'>SRC METHODS</a>";
    print "<br><a href='$SRC_CONTROL_LINK' target='blank'>SRC MANAGEMENT FOR CURRENT SEGMENT</a>";
    print "<br><a href='$SRC_PEVAL_LINK' target='blank'>SRC PARTIAL EVALUATOR FOR ZBW</a><br><br>";
  }
?>
<!--p>
<b>Integration with SRC - Details</b>
<br />==============================
<br />
<br />URL used for START &nbsp;: <?php echo $SRCreal; ?>/<b>start</b>?user=id
<br />URL used for REFINE : <?php echo $SRCreal; ?>/<b>refine</b>?params - (see spec)
<br />
<br />CALLS ROGINDUI -> RODINSRC (can) be stored in DB and can be viewed using <a href="<?php echo $SRC_INTERFACE_BASE_URL; ?>/calls.php" target="_blank"> this link</a>
<br /-->
<b>Integration with SOLR - Details</b>
<br />
<br /><a href="http://localhost:8885/solr/#/"> SOLR ADMIN </a>
<br /><a href="http://localhost:8985"> SOLR LUCID </a>
</p>


