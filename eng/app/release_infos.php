
<?php include_once("root.php");
 $MANTIS ="<a href=\"http://195.176.237.62/mantis/\" target=_blank>mantis</a>";
 ?>

<a href="http://campus.hesge.ch/id_bilingue/projekte/rodin/index_de.htm" target=_blank>RODIN</a>
<br />
<br /><b>DEVELOPMENT / RELEASE INFOS</b>
<br />
<br />01.01.2011 Fabio Ricci (FRI) - Tel. 076-5281961
<br />Please report any issue using <?php echo $MANTIS ?> 
<br />
<br />
<br />
<br /><b>RODIN Release 1.0</b>
<br />===================

<br />FACETED BROWSING finished 
<br />Integration GUI SRC finished 
<br />TAG CLOUD PREVIEW integrated on RODIN main page 
<br />
<p>Note: Due to recent porting works from unix to windows 2003 operating system, and after having introduced proxy authorisation for every web access - even for XSL transformations - some widget will not react as aspected, showing unusual charachters and complainint about "unterminated strings".
</p>
Please forgive that (I am working on it) and report a bug issue to <?php echo $MANTIS ?>
 together with the  
<br /> - timestamp - (when did it appear?)
<br /> - name of the widget
<br /> - value used for the query
<br /> - warning/error.
<br />
<br />
<br />
<br />
<p>
<b>Integration with SRC - Details</b>
<br />==============================
<br />
<br />URL used for START &nbsp;: <?php echo $SRCreal; ?>/<b>start</b>?user=id
<br />URL used for REFINE : <?php echo $SRCreal; ?>/<b>refine</b>?params - (see spec)
<br />
<br />CALLS ROGINDUI -> RODINSRC are stored in DB and can be viewed using <a href="<?php echo $SRC_INTERFACE_BASE_URL; ?>/calls.php" target="_blank"> this link</a>

</p>


