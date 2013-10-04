<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title>            e-lib RODIN        </title>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="robots" content="none" />
    <meta name="revisit-after" content="1 week" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="Content-language" content="en-GB" />
    <meta name="author" content="Fabio.Ricci (@semweb.ch) for HEG Geneva" />
    <meta name="copyright" content="HEG and ETH-Bibliothek" />
    <meta name="description" content="HEG - RODIN ELIB - Semantic Portal - enriched by RODIN techologies" />
    <meta name="keywords" content="HEG, Haute Ecole de Gestion, ETH-Bibliothek , University Library , Library , University , eth , library , Homepage , zurich, geneva, switzerland, Knowledge Portal" />
    
    <link rel="stylesheet" href="../css/jqtransform.css" type="text/css" media="all" />
    <link rel="stylesheet" href="../css/reset.css" type="text/css" />
    <link rel="stylesheet" href="../css/styles.css" type="text/css" />
    <link rel="stylesheet" href="../css/rodinelib.css" type="text/css" />
    <link rel="stylesheet" href="../css/rodinBoards.css.php" type="text/css" />
    <link rel="stylesheet" href="../js/autocomplete/styles.css" type="text/css" />

    <!--[if lt IE 9]>
        <style type="text/css">@import url(../js/stylesie.css);</style>
    <![endif]-->
    <script type="text/javascript" src="../../../../gen/u/jquery/jquery-1.8.3.min.js"></script>
    <script type="text/javascript" src="../js/jquery.tools.min.js"></script>
    <script type="text/javascript" src="../js/functions.js.php"></script>
    <script type="text/javascript" src="../js/jquery.jqtransform.js"></script>
    <script type="text/javascript" src="../js/jquery.truncatable.js"></script>
    <script type="text/javascript" src="../js/modernizr.js"></script> 
    
    <script type="text/javascript" src="../../../app/exposh/l10n/en/lang.js"></script> 
    <script type="text/javascript" src="../js/facetBoardInterface.js.php"></script> 
    <script type="text/javascript" src="../js/RODINsemfilters.js.php"></script> 
    <script type="text/javascript" src="../js/tooltip.js.php"></script> 
    <script type="text/javascript" src="../js/autocomplete/jquery.autocomplete.js"></script>
    
<!-- begin swissmetrix for e-lib.ch -->
<script type="text/javascript">

	WANTCONSOLELOG=true;
	LASTUSERQUERY='';
	EXECSEMAPHOR=0;
	METASEARCH_FINISHED=false;
	AUTOCOMPLETECONTAINER_ID=false;
	SELECTEDWORDS=new Array();
	BUTTONLEFT=false;
	ONTOTERMS_REDO_HIGHLIGHTING=true;
	//RIGHT MOUSE HANDLER:
	//SUPPOSES elements of RODINutilities.js:
	if (false)
	{ //deakt
		$(document).ready(function(){ 
		  document.oncontextmenu = function() {return false;};
		
		  $(document).mousedown(function(e){ 
		    if( e.button == 2 ) { 
		      
					if(e.target.className.contains("result-word-hl"))
						filter_resultdocuments_back_on(e.target);
					else 
					if(e.target.className.contains("result-word"))
						filter_resultdocuments_on(e.target);
	 
		      return false; 
		    } 
		    return true; 
		  }); 
		});
	}
	
	
	
	
  var _paq = _paq || [];
  _paq.push(["trackPageView"]);
  _paq.push(["enableLinkTracking"]);
	if(0)
	{
  (function() {
    var u=(("https:" == document.location.protocol) ? "https" : "http") + "://www.swissmetrix.ch/analytics/";
    _paq.push(["setTrackerUrl", u+"smx.php"]);
    _paq.push(["setSiteId", "7"]);
    var d=document, g=d.createElement("script"), s=d.getElementsByTagName("script")[0]; g.type="text/javascript";
    g.defer=true; g.async=true; g.src=u+"smx.js"; s.parentNode.insertBefore(g,s);
  })();
	}
</script>
<!-- end swissmetrix for e-lib.ch -->
	</head>   
<?php

	$filenamex="app/elibroot.php";
	###############################################################
	$max=10; for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
	{ if (file_exists("$updir$filenamex")) 
		{	require_once("$updir$filenamex"); break;}	}

	$filename="$RODINSEGMENT/app/u/FRIdbUtilities.php"; $maxretries=10;
	#######################################
	for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	{if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}}

	include_once("../../../app/exposh/l10n/en/lang.php");

#------------------ ZOOM BUTTONS ----------------
	$zoom_button_width = "20px";
	$zoom_button_height = "20px";
	$DEBUG=$_REQUEST['DEBUG']; if (!$DEBUG) $DEBUG=0;
	global $B_MIN_ICON_SELECTED, $B_MIN_ICON_HOVER, $B_MIN_ICON_NORMAL;
	global $B_TOKEN_ICON_SELECTED, $B_TOKEN_ICON_HOVER, $B_TOKEN_ICON_NORMAL;
	global $B_ALL_ICON_SELECTED, $B_ALL_ICON_HOVER, $B_ALL_ICON_NORMAL;
	global $B_FILTER_ICON_SELECTED, $B_FILTER_ICON_HOVER, $B_FILTER_ICON_NORMAL;
	
	$titleSeeingCompact='Click to show abstracts in results';
	$titleSeeingAll='Click to hide abstracts in results';
	
	$textZoomButtons=<<<EOH
  <script type="text/javascript">
    RESULTFILTEREXPR='';
    TEXTZOOM='token';
	</script>
		<div id="zoomcontroldiv" class="searchOptionDiv">
			<a id="zoomcontrol" class="showingAbstracts"
				style="margin-right:5px"
				title="$titleSeeingAll" 
				onclick="toggle_show_abstracts_in_results(this);"
			>
 				<img id='whitebuttonimg' src='../../../../gen/u/images/white.PNG'/>
			</a>
			<input id="selectedTextZoom" type="hidden" value="" />
			
		</div>
EOH;
?>
<body> 
	<div id="panel">
		<div id="header">
			<div class="line left">
				<a href='#'><img src="../img/logo-elib.png" alt=""/></a>
			</div>

			<div class="left search">
			<!--	<a id="enhanced_search" href="/elib/action/search.do?tab=default_tab&mode=Advanced&scp.scps=&vid=ELIB&fn=search&amp;cmslang=eng-GB" title='Advanced search' class="down">Advanced search</a>
<a href=/en/Suchanleitung title='Help'>Help</a>-->
	      <input type="text" 
	      id="elibsearchinput"
	      class='wait4userinput'
      	onkeyup="if (event.keyCode==13) {show_rodin_search_results(this.value,<?print $DEBUG ?>)} else {METASEARCH_FINISHED=false;}"
      	size="36" value="" placeholder='Search for... '
      />
     
      <div class='searchlupe wait4userinput'
      	onclick="if(this.value!='') {show_rodin_search_results($('#elibsearchinput').val(), <?print $DEBUG ?>)}"
      	>
      	
      </div>
      <?php print $textZoomButtons ?>

			</div>
  			<a href='#' class="navigation line left"></a>
  			<a href='#' class="navigation line left"></a>
  			<a href='#' class="navigation line left"></a>
  		<div class="line left"></div>
		</div>
		<div id="subheader">
			
			<div class="line left logotext">
				<img src="../img/logotext.png" class='elibrodinlogo' alt=""  width="182" />
      			<div class="userarea-container">
			          
			      </div>

				<div id="breadcrumbs" class="breadCrumbsHidden">
						<div id="breadcrumbs_title" onclick="bc_clear_breadscrumbs();"
							title="<?php print lg("titleBreadcrumbsTitle");?>">
							<?php print lg("lblBreadcrumbsTitle"); ?>:
						</div>
						<div id="breadcrumbs_terms"></div>
				</div>
			</div>
			
			<div class="line left servicelinks">                         
			</div>
			<div class="left language">
				<a href="#" id="lang" title='Change Language'>English</a><div id="languages" style="display: none;">
		<div class="overlay">
			<!--<a class="langitem" href="/de/" title='Change Language'>Deutsch</a>-->
			<a class="langitem" href="#" title='Change Language'>English</a>
			<!--<a class="langitem" href="/fr/" title='Change Language'>Français</a>
   				<a class="langitem" href="/it/" title='Change Language'>Italiano</a>-->
	</div>
</div>			</div>
		</div>
		
		<!-- HOME -->
		<div id="content">
			<!-- Start main home content  -->

<div class="col3 left">
 <div class="headline intro">
Search. Find. Refine.<br/>
Welcome to RODIN e-lib
 </div>
 <p class="intro">
RODIN e-lib is the swiss semantic web portal for academic research<br/>with Linked Open Data access ??
 </p>
</div>
<div class="row1 swiss-map left">
 <img src="../img/swiss_map.png" alt="" />
</div>

<!-- End main home content -->
<form id="newsform" action="" method="get"><input type="hidden" id="newsurl" name="newsid" value="" /></form>

		</div>
	</div>

	<div id="footer">
	<div class="outer"></div>
	<div class="inner">
		<!-- Start dynamic footer index -->
		
		<!--
		<div class="row2 text left">
			<div class="text tcol1 left ">
									
				<h3>Sections</h3>
				<ul>
									<li>
					                         
	                        		<a href="/en" title="Home" >Home</a>                           
		                                                                </li>        
									<li>
							                            	<a href="/en/About-us" title="About us">About us</a>                              
					                                        </li>        
									<li>
							                            	<a href="/en/Offers2" title="Offers">Offers</a>                              
					                                        </li>        
									<li>
							                            	<a href="/en/News" title="News">News</a>                              
					                                        </li>        
								</ul>
																					</div>
			<div class="text left">
															
				<h3>Offers</h3>
				<ul>
									<li>
							                            	<a href="/en/Offers2/Digital-collections" title="Digital collections">Digital collections</a>                              
					                                        </li>        
									<li>
							                            	<a href="/en/Offers2/Specialist-portals" title="Specialist portals">Specialist portals</a>                              
					                                        </li>        
									<li>
							                            	<a href="/en/Offers2/Document-servers" title="Document servers">Document servers</a>                              
					                                        </li>        
									<li>
							                            	<a href="/en/Offers2/Tools" title="Tools">Tools</a>                              
					                                        </li>        
									<li>
							                            	<a href="/en/Offers2/Information-literacy" title="Information literacy ">Information literacy </a>                              
					                                        </li>        
									<li>
							                            	<a href="/en/Offers2/Scientific-publishing" title="Scientific publishing">Scientific publishing</a>                              
					                                        </li>        
								</ul>
															</div>
			<div class="clearfix"></div>
		</div>
		<div class="row2 text left">
			<div class="text tcol1 left">
																					
				<h3>e-lib.ch</h3>
				<ul>
									<li>
							                            	<a href="/en/Contact" title="Contact">Contact</a>                              
					                                        </li>        
									<li>
							                            	<a href="/en/Imprint" title="Imprint">Imprint</a>                              
					                                        </li>        
									<li>
							                            	<a href="/en/Legal-notes" title="Legal notes">Legal notes</a>                              
					                                        </li>        
									<li>
							                            	<a href="/en/About-us/Documents" title="Documents">Documents</a>                              
					                                        </li>        
								</ul>
									</div>
			
										<div class="text col4 last left">
					<h3>Newsletter</h3>
					<p>
					The <a href=/en/Ueber-uns2/Dokumente>Newsletter</a> keeps users regularly informed about the latest developments concerning e-lib.ch
					</p>
					<a class="button" href=/en/Newsletter2><label>Subscribe</label></a>
				</div>			
					</div>
		<div class="clearfix"></div>

		



			<div class="vline"></div>
		-->
		<div class="logosupport">
		</div>
		<div class="clearfix"></div>
	</div>


		<!--div class="text left">
			Copyright 2011 e-lib.ch  |
              <a href=/en/Imprint>Imprint</a>
              | <a href=/en/Betaversion>Beta Version</a>
		</div-->
	</div>
	
<!--begin code snippets for HES Benutzerfreundlichkeitsstudie Webportal-->

<script type="text/javascript">
//var loop11_key = "4d7d05fac0131a36bab38661cf9e8261f8b2d741";
//var l11_host = (("https:" == document.location.protocol) ? "https://" : "http://"); document.write(unescape("%3cscript src='" + l11_host +"www.loop11.com/media/triton/js/embed.js' type='text/javascript'%3e%3c/script%3e"));
</script>

<script type="text/javascript">
setTimeout(function(){var a=document.createElement("script");
var b=document.getElementsByTagName("script")[0];
a.src=document.location.protocol+"//dnn506yrbagrg.cloudfront.net/pages/scripts/0018/1026.js?"+Math.floor(new Date().getTime()/3600000); a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1); </script>

<!--end code snippets for HES/Eliane Blumer Benutzerfreundlichkeitsstudie Webportal-->
<div 	id="elib_tooltip" 
></div>
</body>
</html>