<?php
	#########################################################################
	#
	# WIDGET <Widgetname>
	# AUTHOR Fabio Ricci / HEG, Tel: 076-5281961 / fabio.fr.ricci@hesge.ch
	# DATE 	 1.12.2009
	#
	# PURPOSE: 	Visualize Results of HISTORISCHES LEXIKON DER SCHWEIZ
	#
	# HACKS
	# SPECIAL REMARKS
	#########################################################################
		

	// IMPORTANT!!! CHANGE URL transmission TO POST in order to hide infos !!!!
	
	

	##############################################
	#
	# Some preliminary stuff: 
	# PHP includes:
	include_once("../u/RodinWidgetBase.php");
	include_once "$DOCROOT/$RODINUTILITIES_GEN_URL/simplehtmldom/simple_html_dom.php";
	#
	# AJAX includes:
	#
	# Put all the WIDGET SPECIFIC ajax code here (pls. rename):
	#
	# $MY_AJAX_FILE = make_ajax_widget_filename(/*overriding filename if needed*/);
	#
	# Put all the GENERIC ajax code in ../u/RODINutilities.js.php
	#
	##############################################
		
	// The following is the link to the resource:
	$basesegment="http://www.hls-dhs-dss.ch/";	
	##############################################
	

	##############################################
	##############################################
	#
	# This will print the html header with a Title
	#
	
	
	print_htmlheader("ROBOT");
	##############################################
	##############################################
	# post:
	# http://www.hls-dhs-dss.ch/index.php?
	#		 searchtype=articles|fulltext|nouveaux
	#		&searchstring=Tell
	#		&searchstart=1
	#		&dateletter=01/12/2009
	#		&searchft=simple
	#		&curlg=d
	#		&process=now
	#		&searchlang=d
	#

//Setup http Post call historisches Lexikon
/*
$url="http://www.hls-dhs-dss.ch/index.php";
$data=	"searchtype=articles"
			."&searchstring=TELL"
			."&searchstart=1"
			."&dateletter="
			."&searchft=simple"
			."&curlg=d"
			."&process=now"
			."&searchlang=d";
*/


	###########################################
	#
	# Digitalisiertes Bundesblatt:
	#
/*

http://www.amtsdruckschriften.bar.admin.ch/execQuery.do?
	context=home
	&fields%5B111%5D.name=t_sprache
	&fields%5B111%5D.value=*D*&queryType=pattern
	&queryString=Tell+t_titel_normal_de%3D%28Tell%29
	&queryStringInput=Tell&x=21&y=16
	&fields%5B101%5D.name=a_publikations_date
	&zeitraum_von=01.01.1200
	&zeitraum_bis=31.12.2000
	&fields%5B101%5D.value=01.01.1200+-+12.31.2000
	&fields%5B120%5D.name=t_texteinheit_id
	&fields%5B120%5D.value=
	&cb_druckschrifttyp=Bundesblatt
	&cb_druckschrifttyp=Diplomatische+Dokumente+der+Schweiz
	&cb_druckschrifttyp=Protokolle+des+Bundesrates
	&fields%5B102%5D.name=a_druckschrifttyp_de
	&fields%5B102%5D.value=%22Protokolle+des+Bundesrates%22+OR+%22Diplomatische+Dokumente+der+Schweiz%22+OR+%22Bundesblatt%22
	&searchDomain=library


*/


$search_url="http://www.amtsdruckschriften.bar.admin.ch/execQuery.do?"
	."context=home"
	."&fields%5B111%5D.name=t_sprache"
	."&fields%5B111%5D.value=*D*&queryType=pattern"
	."&queryString=Tell+t_titel_normal_de%3D%28Tell%29"
	."&queryStringInput=Tell&x=21&y=16"
	."&fields%5B101%5D.name=a_publikations_date"
	."&zeitraum_von=01.01.1200"
	."&zeitraum_bis=31.12.2000"
	."&fields%5B101%5D.value=01.01.1200+-+12.31.2000"
	."&fields%5B120%5D.name=t_texteinheit_id"
	."&fields%5B120%5D.value="
	."&cb_druckschrifttyp=Bundesblatt"
	."&cb_druckschrifttyp=Diplomatische+Dokumente+der+Schweiz"
	."&cb_druckschrifttyp=Protokolle+des+Bundesrates"
	."&fields%5B102%5D.name=a_druckschrifttyp_de"
	."&fields%5B102%5D.value=%22Protokolle+des+Bundesrates%22+OR+%22Diplomatische+Dokumente+der+Schweiz%22+OR+%22Bundesblatt%22"
	."&searchDomain=library"
	;

//print "search_url= ((($search_url)))<br>";



$host="http://www.amtsdruckschriften.bar.admin.ch/execQuery.do";
$data="context=home"
	."&fields%5B111%5D.name=t_sprache"
	."&fields%5B111%5D.value=*D*&queryType=pattern"
	."&queryString=Tell+t_titel_normal_de%3D%28Tell%29"
	."&queryStringInput=Tell&x=21&y=16"
	."&fields%5B101%5D.name=a_publikations_date"
	."&zeitraum_von=01.01.1200"
	."&zeitraum_bis=31.12.2000"
	."&fields%5B101%5D.value=01.01.1200+-+12.31.2000"
	."&fields%5B120%5D.name=t_texteinheit_id"
	."&fields%5B120%5D.value="
	."&cb_druckschrifttyp=Bundesblatt"
	."&cb_druckschrifttyp=Diplomatische+Dokumente+der+Schweiz"
	."&cb_druckschrifttyp=Protokolle+des+Bundesrates"
	."&fields%5B102%5D.name=a_druckschrifttyp_de"
	."&fields%5B102%5D.value=%22Protokolle+des+Bundesrates%22+OR+%22Diplomatische+Dokumente+der+Schweiz%22+OR+%22Bundesblatt%22"
	."&searchDomain=library"
	;

$login = "http://www.amtsdruckschriften.bar.admin.ch/login.do?userId=guest&password=guest";


$cc = new cURL();

//$GET_RESULT = $cc->get($start_url,$data); 
//$GET_RESULT = $cc->get($search_url,$data); 
//$RESULT = $cc->get($login); 
//$RESULT = $cc->get($search_url); 
//$RESULT = $cc->post($host, $data); 

// ersetze   action="login.do"    durch     action="http://www.amtsdruckschriften.bar.admin.ch/login.do"

//$term1_login="login.do";
//$term2_login="http://www.amtsdruckschriften.bar.admin.ch/login.do";


//print "<br>RESULT ($data) <hr>FOUND: (((<br>".$RESULT.")))<br>";




$X =<<<EOT


   

<!-- HEADER -->
  





<!--

	header.jsp

-->
<form id="queryForm" onsubmit="return canSubmitSearch();" method="GET" action="" name="queryForm">

<!-- CONVERA - SB Start 

  <table class="headerFrame" width="100%" cellpadding="0" cellspacing="0" border="0">

-->






<table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tbody><tr>
        <td>
            
            <div id="webHeaderDiv">
                <div id="webLogoDiv">
                    <h1>
                        Schweizerisches Bundesarchiv
                    </h1>
                </div>
                <div id="webHeaderLinks">
                    <div id="adminch">
                        <a href="http://www.admin.ch/ch/index.de.html" target="_blank">
                            Bundesverwaltung admin.ch
                        </a>
                    </div>
                    <div id="depart">
                        <a href="http://www.edi.admin.ch/index.html?lang=de" target="_blank">
                            Eidg. Departement des Innern
                        </a>
                    </div>
                    <h1>
                        <a href="http://www.bar.admin.ch/index.html?lang=de" target="_blank">
                            Schweizerisches Bundesarchiv
                        </a>
                    </h1>
                </div>
            </div>
            
            
            <div id="webServiceNavigationDiv">
                <div id="webServiceContentDiv">
                    <ul>
                        <li>
                            <nobr><a href="http://www.bar.admin.ch/archivgut/00593/00649/index.html?lang=de" class="webServiceContent" accesskey="0">
                                Home
                            </a></nobr>
                        </li>
                        <li>
                            <nobr><a href="javascript:navLink('queryForm', 'showHome.do')" class="webServiceContent" accesskey="1">
                                Einfache Suche
                            </a></nobr>
                        </li>
                        <li>
                            <nobr><a href="javascript:navLink('queryForm', 'advSearch.do')" class="webServiceContent" accesskey="2">
                                Erweiterte Suche
                            </a></nobr>
                        </li>
                        <li>
                            <nobr><a href="javascript:navLink('queryForm', 'showHierarchyDate.do')" class="webServiceContent" accesskey="3">
                                Listensuche nach Datum
                            </a></nobr>
                        </li>
                        <li>
                            <nobr><a href="javascript:navLink('queryForm', 'showHierarchyContent.do')" class="webServiceContent" accesskey="4">
                                Listensuche nach Thema
                            </a></nobr>
                        </li>
                        <li>
                            <nobr><a href="javascript:navLink('queryForm', 'searchHistory.do')" class="webServiceContent" accesskey="5">
                                Suchverlauf
                            </a></nobr>
                        </li>
                        <li>
                            <nobr><a href="showPrefs.do" class="webServiceContent" accesskey="6">
                                Einstellungen
                            </a></nobr>
                        </li>
                        <li>
                            <nobr><a href="help.do?helptag=searchHome" class="webServiceContent" target="_sui_help_window_">
                                Hilfe
                            </a></nobr>
                        </li>
                    </ul>
                </div>
                <ul class="webSpracheDiv">
                    <li class="webSpracheAktivDiv first">
                        <a href="setLanguage.do?lang=DE&amp;currWebPage=searchHome" class="webSpracheAktiv">
                            Deutsch
                        </a>
                    </li>
                    <li class="webSpracheInaktivDiv last">
                        <a href="setLanguage.do?lang=FR&amp;currWebPage=searchHome" class="webSpracheInaktiv">
                            Français
                        </a>
                    </li>
                </ul>
                <ul class="webSpracheDiv" style="clear: right;">
                    <li class="webSpracheInaktivDiv first">
                        <a href="setLanguage.do?lang=IT&amp;currWebPage=searchHome" class="webSpracheInaktiv">
                            Italiano
                        </a>
                    </li>
                </ul>
                
            </div>
            
        </td>
    </tr>
</tbody></table>




<!-- OUTER TABLE  -->
  <table border="0" cellpadding="0" cellspacing="0" width="100%">

	<tbody><tr>

	<!-- LEFT SIDE  -->
	  



<!--

        leftSide.jsp

-->




	<!-- CONTENT TABLE  -->
	  <td valign="top">
		<table border="0" cellpadding="0" cellspacing="0" width="100%"> 


		<!-- Spacer column and push content down.  -->
		  <tbody><tr><td width="10">&nbsp;</td><td>&nbsp;</td></tr>

		<!-- SEARCH PANEL -->
		  <tr><td></td><td>
			
				
</form>





<!--

	searchPanel.jsp



        the search panel for the simple, advanced and reference search 

        with fields and search button.

        additional fields in the advanced view are imported from 

        additional filters.jsp file



        Author: CONVERA SWITZERLAND, SB

        Date:   07.01.2004

-->










<!-- save the unmodified currWebPage to see if it is the resultpage -->





<!--

	Determine if at least one Library is selected,

		to pass to client-side for checkSearchValid().

		Since we can't exit the loops early, might as well provide a count instead of a flag.

	  This loops the selected libraries, then confirms they don't need registration

		(could come in this way through defaults).

-->
  
  
	
	  
		  
		  
			
		  
		
	  
		  
	  
	
  


<script language="javascript">

// Send variables to client-side.
var currWebPage           = "searchHome";
//alert(currWebPage);
var libCheckedServerCount = "1";

   var language = "de";




   
var queryType = "pattern";
   
   







function canSubmitSearch()
{
alert('begin canSubmitSearch');
//CONVERA - SB Start
// check and set Zeitraum
//  setZeitraum();
setZeitraum_US();
// set druckschrifttyen
setDruckschrifttyp();


// set the Textkategorie Listbox Querystring
setListbox (document.getElementById("textkategorieList"), document.getElementById("textkategorie"));

// set the Anlass Listbox Querystring
setListbox (document.getElementById("anlassList"), document.getElementById("anlass"));

// set the Geschaeftsart Listbox Querystring
setListbox (document.getElementById("geschaeftsartList"), document.getElementById("geschaeftsart"));

// check if reference-nr. field is not empty and set the language to all
var ctxtid = document.getElementById("t_texteinheit_id");
var cspr = document.getElementById("t_sprache");
if (ctxtid != null && cspr != null) {
    if (ctxtid.value != "") {
        cspr.value = "";
        cspr.checked = true;
    }
}

// remove spaces and dots in the reference search field
setReferenceSearchString();


// Titelbetonung 
// switch it off if in boolean mode
if ("searchHome" == "advancedSearch") {
   if (queryType != "boolean") {
      if (document.getElementsByName("titleQueryWeight")[0] != null) {
         if ( document.getElementsByName("titleQueryWeight")[0].checked == true ) {
            setSearchString();
         } else {
            document.getElementsByName("advancedQueryString")[0].value = document.getElementsByName("advancedQueryStringInput")[0].value;
         }
      } 
   } else {
      // boolean mode
      document.getElementsByName("advancedQueryString")[0].value = document.getElementsByName("advancedQueryStringInput")[0].value;
   }
} else {
   // always set Titelbetonung
   if (queryType != "boolean") {
      setSearchString();
   }
} 
  



// Suche in Titel nach Sprachauswahl setzen
setSearchInTitle();

//CONVERA - SB End

  // Check if search conditions are valid.
  if (true)
  {
	// Change context and action to submit query.
	var ctxt = document.getElementById("context");
	if (ctxt != null) {
	alert('change action to '+"http://www.amtsdruckschriften.bar.admin.ch/execQuery.do");
		  if (ctxt.value == "advanced") { 
			  ctxt.value="advsearch";
				
			  var f = documents.queryForm; 
				f.action="http://www.amtsdruckschriften.bar.admin.ch/execQuery.do";
				f.submit();
      }


	  }
		else
		 alert ('no CONTEXT (nix ausgeführt)');

	return true;
  }
  else
  {
    alert("Bitte wählen Sie eine oder mehrere Bibliotheken zur Suche aus.");  // ASSUME: This is the cause of the error. If additional errors, check return value.
    return false;
  }
	
	alert('return canSubmitSearch');

}



function changeSourceType(source) 
{
      document.getElementsByName("searchDomain")[0].value = source;

  var bVal = true;
  var bValNot = false;
  var sources;
  var node;
  var elements;
  var bLib;
  var isSearchInResultsActive = false;

  if (source == "results") {
    bVal = false;
    bValNot = true;
    isSearchInResultsActive = true;
  } else {
    bVal = true;
    bValNot = false;
    isSearchInResultsActive = false;
  }

  sources = document.getElementById("sources");

  if (document.getElementsByName("searchResults")[0].checked != bValNot) {
    document.getElementsByName("searchResults")[0].checked = bValNot;
  }

  if (sources){

    elements = sources.getElementsByTagName("INPUT");
    for(i=0; i < elements.length; i++) {

      node = elements[i];

      if(node.type == "checkbox") {
        if (node.value != "results") {
            if (bVal == false) {
                if (!node.disabled) {
                  if (node.checked != bVal) {
                    node.checked = bVal;
                  }
                }
            }
        }
      }
    }
  }
  
  
  // if advanced search --> reload the form to get the correct textkategorien liste
//  var ctxt = document.getElementById("context");
//  if (ctxt != null) {
//      if (ctxt.value == "advanced") { 
//          document.getElementById("queryForm").action="advSearch.do"
//      }
//  }
    if ("searchHome" == "advancedSearch" && isSearchInResultsActive == false) {
        setDruckschrifttyp();
        submitAction("refreshWordInfo.do");
        //document.getElementById("queryForm").action="refreshWordInfo.do"
        //document.getElementById("queryForm").submit();
    }
}


function checkEnter(e){ //e is event object passed from function invocation
  var characterCode; // literal character code will be stored in this variable

  if(e && e.which){ //if which property of event object is supported (NN4)
    e = e;
    characterCode = e.which; //character code is contained in NN4's which property
  } else {
    e = event;
    characterCode = e.keyCode; //character code is contained in IE's keyCode property
  }

  if(characterCode == 13){ //if generated character code is equal to ascii 13 (if enter key)
    //document.forms[0].submit(); //submit the form
    canSubmitSearch();
    document.getElementById("queryForm").submit();
    return false; 
  } else {
    return true; 
  }
} 

</script>


<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <input id="context" name="context" value="home" type="hidden">



<!-- Display title -->

    <tbody><tr>
        <td></td>
        <td><div style="margin-left: 5px;" class="pageLabel"><nobr>
                    
                        
                        
                            Einfache Suche
                        
                    
                    &nbsp;&nbsp;
        </nobr></div><br></td>
    </tr>

<tr>
    <td>
        <a href="#skipSearchPanel" class="skipNavClass" title="Überspringe Sucheinstellungen"><img src="http://www.amtsdruckschriften.bar.admin.ch/images/layout/SkipNav.gif" alt="" align="absmiddle" border="0" height="1" width="1"></a>
    </td>

    <!-- Input box, Search Button -->
    <td>
        <table width="100%">
            <tbody><tr align="left">
                <td class="text">
                    
                        



                                



                            
                            
                                <!-- set language to filter documents -->
                                <input name="fields[111].name" value="t_sprache" type="hidden">
                                
                                    <input name="fields[111].value" value="*D*" type="hidden">
                                

                                <!-- search always in pattern mode if in simple search mode -->
                                <input name="queryType" value="pattern" id="queryTypeP" testid="input_pattern" type="hidden">
                                
                                <table>
                                    <tbody><tr align="left">
                                        <td class="text">
                                            <label for="queryStringInput"><a href="help.do?helptag=suchbegriffe" target="_blank" class="helpLink" onmouseover="return escape('Geben Sie hier die gewünschten Suchbegriffe ein. Diese werden unscharf durchsucht, d.h. es werden auch Begriffe mit ähnlicher Schreibweise gefunden und es wird eine Treffergüte berechnet. Möchten Sie exakt suchen und dabei die Boolschen Operatoren (AND, OR, WITHIN,...) verwenden, wechseln Sie in die erweiterte Suche.')">Suchbegriffe:</a></label>
                                        </td><td>
                                            <table border="0" cellpadding="0" cellspacing="0"><tbody><tr><td>
                                            <input name="queryString" value="" testid="input_queryString" type="hidden">
                                            <input class="searchQueryString" name="queryStringInput" size="48" value="" testid="input_queryString" type="text">
                                             </td><td>
                                                <!-- CONVERA - SB Start -->
                                                &nbsp;&nbsp;<input id="imgSearch" src="http://www.amtsdruckschriften.bar.admin.ch/images/local_de/btn_search.gif" testid="input_image_submitSearch" alt="Suche" type="image">&nbsp;
                                                <!-- CONVERA - SB end -->
                                                
                                                    
                                                    
                                                        
                                                    
                                                
                                                <nobr>&nbsp;&nbsp;<a class="searchNewLink" href="newSearch.do?newSearch=clear&amp;context=home" testid="a_newsearch" alt="löschen"><img src="http://www.amtsdruckschriften.bar.admin.ch/images/local_de/btn_delete.gif" alt="löschen"></a>
                                                &nbsp;</nobr>
                                                </td></tr></tbody></table>
                                                <!-- CONVERA - SB End -->
                                            </td>
                                    </tr>
                                    

                                    <tr>
                                        <td class="text">
                                            <input name="fields[101].name" value="a_publikations_date" type="hidden">
                                            <label for="zeitraum_von"><a href="help.do?helptag=zeitraum" target="_blank" class="helpLink" onmouseover="return escape('Geben Sie hier ein Datum oder einen Datumsbereich ein, um nur in Dokumenten aus diesem Zeitraum zu suchen.<br>Beispiel:<br>2000 --> 1.1.2000-31.12.2000<br>1.2000 --> 1.1.2000-31.1.2000<br>12.1.2000 --> 12.1.2000')">Zeitraum</a>:&nbsp;</label>
                                        </td><td>
                                            <input class="searchQueryString" size="10" name="zeitraum_von" value="" onblur='checkDate("zeitraum_von")' testid="input_field_von" type="text">
                                            <label class="text" for="zeitraum_bis"> bis </label>
                                            <input class="searchQueryString" size="10" name="zeitraum_bis" value="" onblur='checkDate("zeitraum_bis")' testid="input_field_bis" type="text">
                                            <input class="textbox" name="fields[101].value" value="" testid="input_field_a_publikations_date" type="hidden">
                                            
                                            <input name="fields[120].name" value="t_texteinheit_id" type="hidden">
                                            <input size="50" name="fields[120].value" value="" id="t_texteinheit_id" testid="input_field_t_texteinheit_id" type="hidden">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="text">
                                            <label for="fields[102].value"><a href="help.do?helptag=suchenIn" target="_blank" class="helpLink" onmouseover="return escape('Wählen Sie die gewünschten Druckschrifttypen aus, in welchen gesucht werden soll. <br>Das Bundesblatt umfasst den Zeitraum von 1848 bis heute (für das genaue Jahr siehe \'Home\').<br>Die DDS umfassen den Zeitraum von 1848 bis 1945.')">Suchen in</a>:</label>
                                        </td><td class="text" colspan="2">
                                            <span id="sources">
                                                
                                            

                                            <table border="0" cellpadding="0" cellspacing="0"><tbody><tr><td class="text">
                                            
                                                    <nobr><input checked="checked" size="30" name="cb_druckschrifttyp" value="Bundesblatt" testid="input_cb_field_druckschrifttyp" onclick="javascript:changeSourceType('library')" type="checkbox"><label id="lblSearchDomainR" for="searchDomainR">Bundesblatt</label></nobr>
                                                    

<tr><td class="text"> 
                                                    <nobr><input checked="checked" size="30" name="cb_druckschrifttyp" value="Amtliches Bulletin der Bundesversammlung" testid="input_cb_field_druckschrifttyp" onclick="javascript:changeSourceType('library')" type="checkbox"><label id="lblSearchDomainR" for="searchDomainR">Amtliches Bulletin der Bundesversammlung</label></nobr>
                                                    
                                                 
                                                 </td></tr><tr><td class="text"> 
                                                    <nobr><input checked="checked" size="30" name="cb_druckschrifttyp" value="Diplomatische Dokumente der Schweiz" testid="input_cb_field_druckschrifttyp" onclick="javascript:changeSourceType('library')" type="checkbox"><label id="lblSearchDomainR" for="searchDomainR">Diplomatische Dokumente der Schweiz</label></nobr>
                                                    
                                                 
                                                 
                                                 </td></tr><tr><td class="text"> 
                                                    <nobr><input checked="checked" size="30" name="cb_druckschrifttyp" value="Protokolle des Bundesrates" testid="input_cb_field_druckschrifttyp" onclick="javascript:changeSourceType('library')" type="checkbox"><label id="lblSearchDomainR" for="searchDomainR">Protokolle des Bundesrates</label></nobr>
                                                    
                                                 
                                                 
                                                     <!-- Hinweis dass die Suche eingeschrÃ¤nkt ist da die Dokumente kein OCR-Text enthalten -->
                                                     <a href="#" class="helpLink" onmouseover="return escape('Für die Protokolle existiert keine Texterkennung. <br>Dadurch kann nicht über den Inhalt der Dokumente gesucht werden')">(Nur eingeschränkte Suche)</a>
                                                 
                                                 </td></tr><tr><td class="text"> </td></tr></tbody></table>
                                                <input name="fields[102].name" value="a_druckschrifttyp_de" type="hidden">
                                                <input name="fields[102].value" value="" type="hidden">

                                                
                                                
                                                <input name="searchDomain" id="searchDomainL" value="library" type="hidden">
                                                <nobr><input name="searchResults" id="searchResults" value="results" onclick="javascript:changeSourceType('results')" type="checkbox"><label id="lblSearchDomainR" for="searchDomainR">Suchergebnisse</label></nobr>
                                                
                                                </span>                  
                                            </td>
                                        </tr>
                                    </tbody></table>
                                
                            
                        </td>
                        <td>
                            <!-- CONVERA - SB Start 

                            &#160;&#160;<input type="image" id="imgSearch" src="images/local/btn_search.gif" testID="input_image_submitSearch" alt="Suche" >

                            CONVERA - SB End -->
                        </td>
                    </tr>
                </tbody></table>
            </td>

        </tr>




        
            

         


<!-- Radio buttons  -->


    
</tbody></table>






<script language="javascript">
//CONVERA - SB Start

function checkDate(inputField)
{
    var dateInput = document.getElementsByName(inputField)[0].value;
    if (dateInput != "") {
      if (!getDateFromFormat(dateInput,"d.M.y") && !getDateFromFormat(dateInput,"M.y") && !getDateFromFormat(dateInput,"y")) {
        document.getElementsByName(inputField)[0].className="inputFieldValError";
        alert('Kein gültiges Datum ');
      } else {
        document.getElementsByName(inputField)[0].className="inputFieldVal";
      }
    }
    return true;
}


function getZeitraum()
{
  var zeitraum = document.getElementsByName("fields[101].value")[0].value;
  if (zeitraum != "") {
    var zeitraumVonBis = zeitraum.split(" - ");
    if (zeitraumVonBis.length == 2) {
        document.getElementsByName("zeitraum_von")[0].value = zeitraumVonBis[0];
        document.getElementsByName("zeitraum_bis")[0].value = zeitraumVonBis[1];
    } else {
        document.getElementsByName("zeitraum_von")[0].value = zeitraumVonBis[0];
    }
  }
}


function setZeitraum()
{
  var zeitraumVon = document.getElementsByName("zeitraum_von")[0].value;
  var zeitraumBis = document.getElementsByName("zeitraum_bis")[0].value;
  var zeitraum = "";
//  alert(zeitraumVon);
//  alert(zeitraumBis);
  
  if (zeitraumVon != "") {
    if (zeitraumBis != "") {
      zeitraum = normalizeDate(zeitraumVon) + " - " + normalizeDate(zeitraumBis);
    } else {
      switch (zeitraumVon.length) {
            //Format 2000
            case 4:  zeitraum = "1.1." + zeitraumVon + " - 31.12." + zeitraumVon; break;
            //Format 1.2000
            case 6:  zeitraum = "1." + zeitraumVon + " - 31." + zeitraumVon; break;
            //Format 10.2000
            case 7:  zeitraum = "1." + zeitraumVon + " - 31." + zeitraumVon; break;
            //Format 1.1.2000
            case 8:  zeitraum = zeitraumVon; break;
            //Format 1.10.2000
            case 9:  zeitraum = zeitraumVon; break;
            //Format 10.10.2000
            case 10:  zeitraum = zeitraumVon; break;
      }
    }
  } else {
    //only zeitraumBis filled
    if (zeitraumBis != "") {
      zeitraum = "<" + zeitraumBis;
    }
  }  
  document.getElementsByName("fields[101].value")[0].value = zeitraum;
//  alert(zeitraum);
}


function normalizeDate(inputdate) 
{
//alert("Function normalizeDate aufgerufen");
  //Normalizes the Input of the Date
  var normdate = "";
     switch(inputdate.length) {
            //Format 2000
            case "4":  normdate = "1.1." + inputdate; break;
            //Format 1.2000
            case "6":  normdate = "1." + inputdate; break;
            //Format 10.2000
            case "7":  normdate = "1." + inputdate; break;
            //Format 1.1.2000
            case "8":  normdate = inputdate; break;
            //Format 1.10.2000
            case "9":  normdate = inputdate; break;
            //Format 10.10.2000
            case "10":  normdate = inputdate; break;
            default:  normdate = inputdate; break;
      }
  return normdate;
}
function normalizeDate_US(inputdate, type) 
{
  //Normalizes the Input of the Date
  var normdate = "";
  var a_date = inputdate.split(".");
  if (type == "from") {
     switch(a_date.length) {
            //Format 2000
            case 1:  normdate = "1.1." + a_date[0]; break;
            //Format 1.2000
            case 2:  normdate = a_date[0] + ".1." + a_date[1];  break;
            //Format 10.2000
            case 3:  normdate = a_date[1] + "." + a_date[0] + "." + a_date[2]; break;
            default:  normdate = inputdate; break;
      }
  } else {
     switch(a_date.length) {
            //Format 2000
            case 1:  normdate = "12.31." + a_date[0]; break;
            //Format 1.2000
            case 2:  normdate = a_date[0] + ".31." + a_date[1];  break;
            //Format 1.1.2000
            case 3:  normdate = a_date[1] + "." + a_date[0] + "." + a_date[2]; break;
            default:  normdate = inputdate; break;
      }
  }
  return normdate;
}
function setZeitraum_US()
{
  var zeitraumVon = document.getElementsByName("zeitraum_von")[0].value;
  var zeitraumBis = document.getElementsByName("zeitraum_bis")[0].value;
  var zeitraum = "";
  
  if (zeitraumVon != "") {
    if (zeitraumBis != "") {
      zeitraum = normalizeDate_US(zeitraumVon, 'from') + " - " + normalizeDate_US(zeitraumBis, 'to');
    } else {
      var a_date = zeitraumVon.split(".");
      switch (zeitraumVon.length) {
            //Format 2000
            case 4:  zeitraum = "1.1." + zeitraumVon + " - 12.31." + zeitraumVon; break;
            //Format 1.2000
            case 6:  if (a_date.length != 0) { zeitraum = a_date[0] + ".1." + a_date[1] + " - " + a_date[0] + ".31." + a_date[1];}  break;
            //Format 10.2000
            case 7:  if (a_date.length != 0) { zeitraum = a_date[0] + ".1." + a_date[1] + " - " + a_date[0] + ".31." + a_date[1];}  break;
            //Format 1.1.2000
            case 8:  if (a_date.length == 3) { zeitraum = a_date[1] + "." + a_date[0] + "." + a_date[2];} break;
            //Format 1.10.2000
            case 9:  if (a_date.length == 3) { zeitraum = a_date[1] + "." + a_date[0] + "." + a_date[2];} break;
            //Format 10.10.2000
            case 10:  if (a_date.length == 3) { zeitraum = a_date[1] + "." + a_date[0] + "." + a_date[2];} break;
      }
    }
  } else {
    //only zeitraumBis filled
    if (zeitraumBis != "") {
      //zeitraum = "<" + zeitraumBis;
    }
  }  
  document.getElementsByName("fields[101].value")[0].value = zeitraum;
}
function getZeitraum_US()
{
  var zeitraum = document.getElementsByName("fields[101].value")[0].value;
  if (zeitraum != "") {
    var zeitraumVonBis = zeitraum.split(" - ");
    if (zeitraumVonBis.length == 2) {
        var a_date_von = zeitraumVonBis[0].split(".");
        var a_date_bis = zeitraumVonBis[1].split(".");
        if (a_date_von.length == 3) {
//alert(a_date_von.length);
           var zeitraum_von = a_date_von[1] + "." + a_date_von[0] + "." + a_date_von[2];
        } else {
           var zeitraum_von = zeitraumVonBis[0];
        }
        if (a_date_bis.length == 3) {
           var zeitraum_bis = a_date_bis[1] + "." + a_date_bis[0] + "." + a_date_bis[2];
        } else {
           var zeitraum_bis = zeitraumVonBis[0];
        }
           document.getElementsByName("zeitraum_von")[0].value = zeitraum_von;
           document.getElementsByName("zeitraum_bis")[0].value = zeitraum_bis;
    } else {
        var a_date_von = zeitraumVonBis[0].split(".");
        if (a_date_von.length == 3) {
           var zeitraum_von = a_date_von[1] + "." + a_date_von[0] + "." + a_date_von[2];
        } else {
           var zeitraum_von = zeitraumVonBis[0];
        }
        document.getElementsByName("zeitraum_von")[0].value = zeitraum_von;
    }
  }
}


function setSearchString () {
    // Funktion zur Gewichtung des Titels
	var ctxt = document.getElementById("context");
        var lang = "";
        lang = getQueryLanguage();
	if (ctxt != null) {
		  if ("searchHome" == "advancedSearch" || "searchHome" == "referenceSearch" || "searchHome" == "directSearch") {
                        var queryString = document.getElementsByName("advancedQueryStringInput")[0].value;
//alert("setQueryString" + queryString);
                        if (queryString != "") {
                            queryString = queryString + " t_titel_normal_" + lang + "=(" + queryString + ")";
                            document.getElementsByName("advancedQueryString")[0].value = queryString;
                        } else {
                            //Querystring is empty
                            document.getElementsByName("advancedQueryString")[0].value = "";
                        }
                   } else {
                        // simple search
                        var queryString = document.getElementsByName("queryStringInput")[0].value;
                        if (queryString != "") {
                            queryString = queryString + " t_titel_normal_" + lang + "=(" + queryString + ")";
                            document.getElementsByName("queryString")[0].value = queryString;
                        } else {
                            //Querystring is empty
                            document.getElementsByName("queryString")[0].value = "";
                        }
                   }
        }
}

function getQueryLanguage() {
    // Funktion welche die Sprache aus der Auswahlbox zurÃ¼ckgibt
    var ctxt = document.getElementById("t_sprache");
    var queryLanguage = language;
    if (ctxt != null) {
        var a_queryLanguage = document.getElementsByName(document.getElementById("t_sprache").name);
		if (a_queryLanguage.length > 0)					
		{
			for (i = 0; i < a_queryLanguage.length; i++)
			{
				if (a_queryLanguage[i].checked == true) {	
					queryLanguage = a_queryLanguage[i].value;
                                        queryLanguage = queryLanguage.toLowerCase();
                                        if (queryLanguage == "*d*") {
                                            queryLanguage = "de";
                                        } else if (queryLanguage == "*f*") {
                                            queryLanguage = "fr";
                                        } else if (queryLanguage == "*i*") {
                                            queryLanguage = "it";
                                        } else {
                                            queryLanguage = "fr";
                                        }
                                        
				}
			}
		}
    }
    return queryLanguage;
}

function setSearchInTitle() {
    var ctxt = document.getElementById("searchInTitle");
    if (ctxt != null) {
        ctxt.value = "t_titel_normal_" + getQueryLanguage();
//alert(ctxt.name);
    }
}


function getSearchString() {
    // Funktion zur Gewichtung des Titels (Entfernen des mittels Funktion setSearchString zugefÃ¼gten Strings, damit dieser nicht im Suchfeld erscheint
	var ctxt = document.getElementById("context");

	if (ctxt != null) { 
		  if ("searchHome" == "advancedSearch" || "searchHome" == "referenceSearch" || "searchHome" == "directSearch") {
                        var queryString = document.getElementsByName("advancedQueryString")[0].value;
//alert(queryString);
                        if (queryString != "") {
                           // t_titel_normal_ Eintrag entfernen
                           var position = queryString.indexOf("t_titel_normal_");
                           if (position != "-1") {
                              // Titelbetonung vorhanden
                              // Checkbox fÃ¼r die Titelbetonung setzen
                              if (document.getElementsByName("titleQueryWeight")[0] != null) {
                                 document.getElementsByName("titleQueryWeight")[0].checked = true;
                              }
                              var subQueryString = queryString.substring(0,position-1);
                              document.getElementsByName("advancedQueryStringInput")[0].value = subQueryString;
//alert("Titelbetonung ein: " + subQueryString);
                           } else {
                              // Titelbetonung nicht vorhanden
                              // Checkbox fÃ¼r die Titelbetonung nicht setzen
                              if (document.getElementsByName("titleQueryWeight")[0] != null) {
                                 document.getElementsByName("titleQueryWeight")[0].checked = false;
                              }
                              document.getElementsByName("advancedQueryStringInput")[0].value = queryString;
//alert("Titelbetonung aus: " + queryString);
                           }
                        }
                   } else {
                        // simple search
                        var queryString = document.getElementsByName("queryString")[0].value;
                        if (queryString != "") {
                           // t_titel_normal_ Eintrag entfernen
                           var position = queryString.indexOf("t_titel_normal_");
                           if (position != "-1") {
                                var subQueryString = queryString.substring(0,position-1);
                                document.getElementsByName("queryStringInput")[0].value = subQueryString;
                           } else {
                                document.getElementsByName("queryStringInput")[0].value = queryString;
                           }
                        }
                   }
        }
}



function setDruckschrifttyp() 
{
  var sources;
  var node;
  var elements;
  var bLib;
  var value = "";

  sources = document.getElementById("sources");

  if (sources){

    elements = sources.getElementsByTagName("INPUT");
    for(i=0; i < elements.length; i++) {

      node = elements[i];

      if(node.type == "checkbox") {
        if (node.value != "results") {
        if (!node.disabled) {
          if (node.checked == true) {
            if (value == "") {
value = "\"" + node.value + "\"";
//               value = node.value;
            } else {
value = "\"" + node.value + "\" OR " + value;
//               value = node.value + " OR " + value;
            }
          }
        }
        }
      }
    }
  }
//alert(value);
document.getElementsByName("fields[102].value")[0].value = value;
}

function getDruckschrifttyp() 
{
  var sources;
  var node;
  var elements;
  var bLib;
  var value = document.getElementsByName("fields[102].value")[0].value;

  if (value == "" || value == "null") {
  // falls kein Wert (search in results was performed) --> get last state from session
     value = 'null';
     if (value == "" || value == "null") { // (initial Setting) dann alle selektiert lassen
        value = "";
     }
  }
  if (value != "") { 
value = replaceAll(value, "\"", "");
  var a_values = value.split(" OR ");
  sources = document.getElementById("sources");

  if (sources){

    elements = sources.getElementsByTagName("INPUT");
    for(i=0; i < elements.length; i++) {

      node = elements[i];

      if(node.type == "checkbox") {
        if (node.value != "results") {
        if (!node.disabled) {
          for(k=0; k < a_values.length; k++) {
            if (a_values[k] == node.value) {
              node.checked = true;
              break;
            } else {
              node.checked = false;
            }
          }
        }
        }
      }
    }
  }
  }
}


function setReferenceSearchString () {
    // tolerante Referenzierung (entfernung von LeerschlÃ¤gen, Punkten)
    var ctxt = document.getElementById("t_texteinheit_id");
    if (ctxt != null) {
        var reference = document.getElementsByName(ctxt.name)[0].value;
        // falls Seitenzahl angegeben wird, diese entfernen
        reference = reference.split("/");
        reference = reference[0];
        reference = replaceAll(reference, ".","");
        reference = replaceAll(reference, " ","");
        document.getElementsByName(ctxt.name)[0].value = reference;
    }
}


function setListbox (listbox, hiddenField) {
    // Funktion zur Zusammenstellung der EintrÃ¤ge der Listbox in ein hidden Field
    // listbox:     Das Objekt der Listbox
    // hiddenField: Das hidden Field, in welchem der gesamte Suchstring der entsprechenden
    //              Listbox geschrieben wird.
    // Die in der Listbox selektierten EintrÃ¤ge werden mit OR verknÃ¼pft und
    // in das hidden Field geschrieben.

        var queryStringField = "";
	if (listbox != null) {
            for(k=1; k < listbox.length; k++) { // erster Eintrag "alle" nicht berÃ¼cksichtigen
                if (listbox.options[k].selected == true) {
                    if (queryStringField != "") {
                        queryStringField = queryStringField + " OR " + "\"" + listbox.options[k].text + "\"";
                    } else {
                        queryStringField = "\"" + listbox.options[k].text + "\"";
                    }
                }
            }
            // set hidden field with string
            if (hiddenField != null) {
                hiddenField.value = queryStringField;
            }
        }
}


function getListbox (listbox, hiddenField) {
    // Funktion zur Auswahl der ausgewÃ¤hlten Elemente einer Listbox
    // listbox:     Das Objekt der Listbox
    // hiddenField: Das hidden Field, in welchem der gesamte Suchstring der entsprechenden
    //              Listbox enthalten ist.
    // Die im hidden Field enthaltenen Begriffe werden in der Listbox selektiert

    var queryStringField = "";
    if (hiddenField != null && listbox != null) {
       queryStringField = hiddenField.value;
       if (queryStringField == "") { // erster Eintrag "alle" selektieren
          listbox.options[0].selected = true;
       } else {
          queryStringField = replaceAll(queryStringField, "\"", "");
          queryStringField = queryStringField.split(" OR ");
          for(k=0; k < queryStringField.length; k++) {
             for(i=0; i < listbox.length; i++) {
                if (listbox.options[i].text == queryStringField[k]) {
                   listbox.options[i].selected = true;
                   break;
                }
             }
          }
       }
    }

}

function replaceAll (string, search, replace)
{ var st = string;
  if (search.length == 0)
     return st;
  var idx = st.indexOf(search);
  while (idx >= 0)        
  {  st = st.substring(0,idx) + replace + st.substr(idx+search.length);
     idx = st.indexOf(search);
  }
  return st;
}


//getZeitraum(); 
getZeitraum_US();
getSearchString();
getDruckschrifttyp();

// select the textkategorie items in the listbox from the hidden textkategorie search string ("kategorie 1" OR "kategorie 2")
getListbox (document.getElementById("textkategorieList"), document.getElementById("textkategorie"));

// select the anlass items in the listbox from the hidden anlass search string ("anlass 1" OR "anlass 2")
getListbox (document.getElementById("anlassList"), document.getElementById("anlass"));

// select the geschaeftsart items in the listbox from the hidden geschaeftsart search string ("geschaeftsart 1" OR "geschaeftsart 2")
getListbox (document.getElementById("geschaeftsartList"), document.getElementById("geschaeftsart"));




</script>




			
		  </td></tr>

		  <!-- CONVERA / SB <tr><td><img src="images/blank.gif" width="1" height="10" alt=""/></td></tr> -->

		<!--

		BAR / TABS

		  Align thin column bar to top, to align with tabs div that has the scroll bar.

		  Stop line before right edge, so it balances with spacer column.

	-->
		  <tr>
			
		  </tr>
		  <tr><td><img src="images/blank.gif" alt="" height="4" width="1"></td></tr>

		<!-- PAGE CONTENTS -->
		  <tr><td></td><td valign="top">
		   
				



<!--

        pdfDisclaimer.jsp

-->


  <div class="text" style="margin-left: 8px;">
    <br>
    Hinweis:<br>Die Texte der Suchanwendung wurden mit einer automatischen Texterkennung erstellt. Sie weisen deshalb Texterkennungsfehler auf. Als Referenztexte (beim Zitieren etc.) sollten deshalb nur die pdf-Dokumente verwendet werden, die - als Faksimile der Originaldokumente - geöffnet und gespeichert werden können. 
  
  </div>

		   
		  </td></tr>

	  <!-- Close content table  -->
		</tbody></table>

	
	  </td>
          
          


  <!-- Close outer table  -->
	</tr>
	
	
  </tbody></table>

          





<!--

	footer.jsp

-->

<!-- CONVERA - SB Start 

  <table class="headerFrame" width="100%" cellpadding="0" cellspacing="0" border="0">

-->





<div id="webFooter">
    <div id="webFooterText">
        <span class="webText">
            Schweizerisches Bundesarchiv (BAR)
        </span>
        <br>
        <a href="mailto:bundesarchiv@bar.admin.ch" class="webText" title="Dieser Link öffnet ihr E-Mail-Programm für eine Nachricht an das Bundesarchiv">
            bundesarchiv@bar.admin.ch
        </a>
        &nbsp;|&nbsp;
        <a href="http://www.disclaimer.admin.ch/" class="webText" title="Rechtliche Grundlagen">
            Rechtliches
        </a>
    </div>
</div>



  
EOT;


print $X;








?>