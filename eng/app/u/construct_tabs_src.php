<!-- $Id: construct_tabs_src.php,v 1.4 2011/02/20 19:59:32 fabio Exp $ -->
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<title>Fabios Tabber Example</title>
<?php
include_once('../u/FRIutilities.php');
print <<<EOP
<link rel="stylesheet" href="$CSS_URL/rodin.css" TYPE="text/css" MEDIA="screen">
<link rel="stylesheet" href="$CSS_URL/tabber_multiple_src.css" TYPE="text/css" MEDIA="screen">
<!--link rel="stylesheet" href="$RODINUTILITIES_GEN_URL/tabber/example-print.css" TYPE="text/css" MEDIA="print" -->
<script src="$RODINUTILITIES_GEN_URL/tabber/prototype.js" type="text/javascript"></script>
EOP;

$SRC=$_GET['src'];

//foreach($SRC as $k=>$x) print "$k=>$x, ";
?>
<script type="text/javascript">

tabidsrc=new Array();

/* Optional: Temporarily hide the "tabber" class so it does not "flash"
   on the page as plain HTML. After tabber runs, the class is changed
   to "tabberlive" and it will appear. */

document.write('<style type="text/css">.tabber{display:none;}<\/style>');

var tabberOptions = {

  onClick: function(argsObj) 
  {
    var t = argsObj.tabber; /* Tabber object */
    var i = argsObj.index; /* Which tab was clicked (0..n) */
    var div = this.tabs[i].div;  /*The tab content div */
    //alert('onClick: '+tabidsrc[i]);
    parent.window.SRC_CURRENT_INTERFACE_ID=tabidsrc[i];
    parent.window.SRC_CURRENT_INTERFACE_TAB_ID=i;
   },
   onLoad: function(argsObj) 
   {
    argsObj.index = 0;
    this.onClick(argsObj);
   },
}
</script>
<script type="text/javascript" src="$RODINUTILITIES_GEN_URL/tabber/tabber.js"></script>

</head>
<body>
<div class="src_multiple">
	<div class="tabber">

<?php
$i= -1;
foreach($SRC as $interface_id=>$interface_name)
{
	$i++;
	$DIVCONTENT = generate_src_refining_fields($interface_id);
	if (strtolower($interface_name)=='all')
		$TITLE="title='Display suggestions merged from all datasources (TO BE IMPL)'";
	else
		$TITLE="title='Display suggestions from refinement source \"$interface_name\"'";
	
print <<<EOP
		<script type="text/javascript">
			tabidsrc[$i]=$interface_id;
		</script>
	     <div class="tabbertab" $TITLE>
		  <h2>$interface_name</h2>
			<table cellspacing="1" cellpadding="0" border=0>
			$DIVCONTENT
			</table>
	     </div>
EOP;
}

?>
	</div>
</div>
</body>
</html>
