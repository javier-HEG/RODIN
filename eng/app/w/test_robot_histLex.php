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
	
/*
fulltext title&text:
&searchcont=1

fulltext nur title:
&searchcont=2
*/

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


//$cc = new cURL();

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


  <form id="pageform" name="pageform" method="get" action="index.php">
  <input name="pagename" value="home" type="hidden">
  <input name="pagenum" value="1" type="hidden">
  <input name="searchlang" value="d" type="hidden">
  <input name="searchtype" value="articles" type="hidden">
  <input name="searchft" value="simple" type="hidden">
  <input name="searchstart" value="1" type="hidden">
  <input name="searchcont" value="1" type="hidden">
  <input name="searchstring" value="" type="hidden">
  <input name="dateletter" value="" type="hidden">
  <input name="searchdate" value="" type="hidden">
  <input name="catGEO" value="off" type="hidden">
  <input name="catBIO" value="off" type="hidden">
  <input name="catTEM" value="off" type="hidden">
  <input name="catFAM" value="off" type="hidden">
  <input style="display: none;" value="" type="submit"> 
 </form>
  <div id="barsup">
    <ul>
<li><a class="grayed" href="javascript:SubmitPage(document.pageform, 'contact', 1)">Kontakt</a></li><li>&nbsp;&nbsp;|&nbsp;&nbsp;</li><li><a class="grayed" href="javascript:OpenWindow('english.php')">English window</a></li>    </ul>
  </div>
  <div id="barlang">
<ul><li><a href="switchlang.php?lg=d"><img alt="Historisches Lexikon der Schweiz (HLS)" title="Historisches Lexikon der Schweiz (HLS)" src="img/bg1_d.gif" border="0"></a></li><li><a href="switchlang.php?lg=f"><img alt="Dictionnaire historique de la Suisse (DHS)" title="Dictionnaire historique de la Suisse (DHS)" src="img/bg2_f.gif" border="0"></a></li><li><a href="switchlang.php?lg=i"><img alt="Dizionario storico della Svizzera (DSS)" title="Dizionario storico della Svizzera (DSS)" src="img/bg2_i.gif" border="0"></a></li>    </ul>
  </div>
  <div id="barnav">
    <ul>
<li><a class="grayed" href="javascript:SubmitPage(document.pageform, 'home', 1)">Home</a></li>    <li>&nbsp;&nbsp;|&nbsp;&nbsp;</li>
<li><a class="grayed" href="javascript:SubmitPage(document.pageform, 'actu', 1)">Aktuell</a></li>    <li>&nbsp;&nbsp;|&nbsp;&nbsp;</li>
<li><a class="grayed" href="javascript:SubmitPage(document.pageform, 'project', 1)">HLS und e-HLS</a></li>    <li>&nbsp;&nbsp;|&nbsp;&nbsp;</li>
<li><a class="grayed" href="javascript:SubmitPage(document.pageform, 'collab', 1)">Mitarbeiter</a></li>    <li>&nbsp;&nbsp;|&nbsp;&nbsp;</li>
<li><a class="grayed" href="javascript:SubmitPage(document.pageform, 'presse', 1)">Presse</a></li>    <li>&nbsp;&nbsp;|&nbsp;&nbsp;</li>
<li class="elir"><a class="grayed" target="_new" href="http://www.e-lir.ch/">Lexicon istoric retic</a></li>    </ul>
  </div>

 <div id="barsearch">
  <div id="tableft">
   <div id="tabalpha">
    <div id="tabalphatitle">Alle Artikel:</div>
    <div id="tabalphaletter"><form name="alphaform" method="get" action="index.php"><a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'a')">A</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'b')">B</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'c')">C</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'd')">D</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'e')">E</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'f')">F</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'g')">G</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'h')">H</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'i')">I</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'j')">J</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'k')">K</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'l')">L</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'm')">M</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'n')">N</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'o')">O</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'p')">P</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'q')">Q</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'r')">R</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 's')">S</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 't')">T</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'u')">U</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'v')">V</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'w')">W</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'x')">X</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'y')">Y</a> 
<a class="grayed" href="javascript:SubmitAlpha(document.alphaform, 'z')">Z</a> 
      <input style="display: none;" value="" type="submit">      <input name="process" value="now" type="hidden">
      <input name="rankstart" value="0" type="hidden">
      <input name="searchletter" value="" type="hidden">
      <input name="searchtype" value="letters" type="hidden">
      <input name="searchlang" value="d" type="hidden">
      <input name="searchstring" value="Tell" type="hidden">
     </form>
    </div> <!-- tabalphaletter -->
   </div> <!-- tabalpha -->
   <div id="tabconnect">
    <a href="javascript:OpenLogin()"><img id="connect" alt="" title="" src="img/connect_d.gif" border=""></a>  
   </div> <!-- tabconnect -->
  </div> <!-- tableft xxx-->
  <div id="tabsearch" style="background: transparent url(img/fondrond1.gif) no-repeat scroll right bottom; min-height: 130px; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous;">
   
	 
	 
	 
	 
	 
	 <form name="searchform" method="get" action="index.php" >
    <table class="searchoptions" summary="" border="0">
     <tbody><tr><td colspan="3">
     <label for="searchtype" id="searchtitle">Suchen Sie im e-HLS: </label></td></tr><tr><td align="left" valign="top" width="30%">
     <div id="searchchoice">
     <select id="searchtype" name="searchtype" onchange="ChangeSearch()">
<option value="articles">Artikelsuche</option>
<option value="fulltext" selected="selected">Volltextsuche</option>
<option value="nouveaux">Neue Artikel</option>
      </select> <!-- searchtype -->
     </div><!-- searchchoice -->
    </td><td align="left" valign="top">
     <div id="searchinput">
     <input class="searchstring" alt="" size="22" name="searchstring" value="Tell" type="text">
<a title="Suchen" href="javascript:document.searchform.submit()"><img alt="" title="" src="http://www.hls-dhs-dss.ch/img/search.gif" border="0" height="20" width="20"></a>    <input name="searchstart" value="1" type="hidden">
    <input name="dateletter" value="" type="hidden">
    <input name="searchft" value="simple" type="hidden">
    <input name="curlg" value="d" type="hidden">
    <input name="process" value="now" type="hidden">
    <input style="display: none;" value="" type="submit">

    <br>    </div> <!-- searchinput -->
    </td>
    <td>&nbsp;</td>
</tr>
     <tr>
      <td align="right" valign="top">Durchsuche:</td>
      <td align="left" valign="top">
<label class="searchcontlabel"><input class="current" name="searchcont" value="1" onclick="ChangeRCont(0)" checked="checked" type="radio"><a name="searchcont0" class="current" href="javascript:ChangeCont(0)">Titel und Text</a></label><label class="searchcontlabel"><input class="grayed" name="searchcont" value="2" onclick="ChangeRCont(1)" type="radio"><a name="searchcont1" class="grayed" href="javascript:ChangeCont(1)">nur Titel</a></label>
     </td>
<td>&nbsp;</td></tr>
     <tr><td>&nbsp;</td>
     <td align="left" valign="top">
<label class="searchlanglabel"><input class="current" name="searchlang" value="d" onclick="ChangeRLang(0, 'd')" checked="checked" type="radio"><a name="searchlang0" class="current" href="javascript:ChangeLang(0)">Deutsch</a></label><label class="searchlanglabel"><input class="grayed" name="searchlang" value="f" onclick="ChangeRLang(1, 'f')" type="radio"><a name="searchlang1" class="grayed" href="javascript:ChangeLang(1)">Français</a></label><label class="searchlanglabel"><input class="grayed" name="searchlang" value="i" onclick="ChangeRLang(2, 'i')" type="radio"><a name="searchlang2" class="grayed" href="javascript:ChangeLang(2)">Italiano</a></label>
     </td>
<td>&nbsp;</td></tr>
     
   
     <tr><td colspan="3" align="right" valign="top">
   <div id="searchmenu">
    <ul>
<li><a class="grayed" href="javascript:OpenWindow('help.php')">Hilfe</a></li>     <li>&nbsp;&nbsp;|&nbsp;&nbsp;</li>
<li><a class="grayed" href="javascript:OpenWindow('abrev.php')">Abkürzungen</a></li>     <li>&nbsp;&nbsp;|&nbsp;&nbsp;</li>
     <li><a class="grayed" href="javascript:ChangeFtAvance(document.searchform)">Erweiterte Suche</a></li>
    </ul>
   </div>
  </td></tr>
 </tbody></table>   
</form>













  </div> <!-- tabsearch -->
<div id="tell"><img alt="" title="" src="img/tell.gif" border="0" height="123" width="93"></div> </div> <!-- barsearch -->
<div id="pagecontent">
<div id="result">
<div id="searchhead">Suchresultate '<b>Tell</b>',&nbsp;deutsch:&nbsp;1&nbsp;bis&nbsp;10 von 83<br><form name="ftform" action="index.php" method="get">
Seite:&nbsp;1 <a href="javascript:SubmitFt(document.ftform, 11)">2</a> <a href="javascript:SubmitFt(document.ftform, 21)">3</a> <a href="javascript:SubmitFt(document.ftform, 31)">4</a> <a href="javascript:SubmitFt(document.ftform, 41)">5</a> <a href="javascript:SubmitFt(document.ftform, 51)">6</a> <a href="javascript:SubmitFt(document.ftform, 61)">7</a> <a href="javascript:SubmitFt(document.ftform, 71)">8</a> ... <a title="9" href="javascript:SubmitFt(document.ftform2, 81)">9</a><input name="process" value="now" type="hidden">
<input name="searchtype" value="fulltext" type="hidden">
<input name="searchlang" value="d" type="hidden">
<input name="searchstring" value="Tell" type="hidden">
<input name="searchstart" value="9" type="hidden">
<input name="catGEO" value="off" type="hidden">
<input name="catTEM" value="off" type="hidden">
<input name="catBIO" value="off" type="hidden">
<input name="catFAM" value="off" type="hidden">
<input style="display: none;" value="" type="submit">
&nbsp;&nbsp;<a href="javascript:SubmitFt(document.ftform, 11)">Nächste Seite </a></form>
</div>
 <div class="searchres">
  <div class="searchrestitle">1 <a href="javascript:OpenWindow('/textes/d/D17474.php')">Befreiungstradition</a> </div>
  <div class="searchresdesc">...die den Widerstand weckten, nicht jedoch von <span class="highlight">Tell</span>, dem Burgenbruch und der Bundesschliessung. Voll ausgestaltet erscheint die B. um 1470 im Weissen Buch von Sarnen, das folgende Episoden erzählt: Als Knechte des Landvogts von Unterwalden, Beringer von Landenberg, dem Bauern im Melchi (Gem. Sachseln) Ochsen wegnehmen wollten, habe dessen Sohn sich z...<br>

  <div class="searchresprop">
Zeichen: <b>18205</b><br>
Autor: <b>Peter Kaiser</b><br>
  </div>
 </div>
</div>


 <div class="searchres">
  <div class="searchrestitle">2 <a href="javascript:OpenWindow('/textes/d/D6048.php')">Perrin, Tell</a> </div>
  <div class="searchresdesc">01/12/2009  No 11 Perrin, <span class="highlight">Tell</span> geboren  9.11.1880 (Rodolphe Guillaume <span class="highlight">Tell</span>) Transvaal (Südafrika), gestorben  2.7.1958 La Chaux-de-Fonds, ref., von Noiraigue (heute Gem. Val-de-Travers). Sohn des Paul-Henri, Ingenieurs und Kaufmanns, und der Sophie geb. Blanck.  1) Louise Sophie Robert, 2) Lucie-Agnès-Anna Arndt. Lizentiat der Rechte an der Akad. N...<br>

  <div class="searchresprop">
Zeichen: <b>880</b><br>
Autor: <b>Isabelle Jeannin-Jaquet / BE</b><br>
  </div>
 </div>
</div>


 <div class="searchres">
  <div class="searchrestitle">3 <a href="javascript:OpenWindow('/textes/d/D48300.php')">Fellenberg, Wilhelm Tell von</a> </div>
  <div class="searchresdesc">11/02/2005  No 10 Fellenberg, Wilhelm <span class="highlight">Tell</span> von geboren  9.10.1798 Bern, gestorben  22.03.1880 Merzig (Saarland), ref., von Bern und ab 1857 Ehrenbürger von Merzig. Sohn des Philipp Emanuel ( 9 ).  1829 Anna Rosalie Virginie Boch, Tochter des Johann Franz, Keramikfabrikanten, und der Rosalie Buschmann. Nach Studien und Wanderschaft in Deutschland, F...<br>

  <div class="searchresprop">
Zeichen: <b>1045</b><br>
Autor: <b>Heidi Lüdi</b><br>
  </div>
 </div>
</div>


 <div class="searchres">
  <div class="searchrestitle">4 <a href="javascript:OpenWindow('/textes/d/D16440.php')">Helvetia (Allegorie)</a> </div>
  <div class="searchresdesc">...nicht weiter individualisierten Krieger oder <span class="highlight">Tell</span> - teilen, sondern nahm jetzt als thronende Einzelfigur den Platz im Bildzentrum ein. Das Veranschaulichungsbedürfnis des aufklärer. Republikanismus räumte der Frauenfigur breiten Raum ein. Nicht jede antike Frauengestalt ist jedoch eine H. In vielen Fällen sollte einzig die allenfalls um andere Idea...<br>

  <div class="searchresprop">
Zeichen: <b>4455</b><br>
Autor: <b>Georg Kreis</b><br>
  </div>
 </div>
</div>


 <div class="searchres">
  <div class="searchrestitle">5 <a href="javascript:OpenWindow('/textes/d/D42206.php')">Freudenberger, Uriel</a> </div>
  <div class="searchresdesc">... eine ebenfalls anonyme Schrift über Wilhelm <span class="highlight">Tell</span> in dt. und franz. Sprache, die F. zusammen mit Gottlieb Emanuel von Haller verfasst hatte. Darin wurde zum ersten Mal im Sinn der Aufklärung öffentlich die <span class="highlight">Tell</span>geschichte als der nord. Sagenwelt entlehnter Mythos bezeichnet. Die Schrift löste heftige Gegenreaktionen, selbst bei den Anhängern der Auf...<br>

  <div class="searchresprop">
Zeichen: <b>1155</b><br>
Autor: <b>Karin Marti-Weissenbach</b><br>
  </div>
 </div>
</div>


 <div class="searchres">
  <div class="searchrestitle">6 <a href="javascript:OpenWindow('/textes/d/D24482.php')">Denkmäler</a> </div>
  <div class="searchresdesc">... (1580) oder gewisse Brunnenfiguren (Wilhelm <span class="highlight">Tell</span> in Schwyz, 1682; Niklaus von Flüe in Sarnen, 1708).  Die zwischen 1790 und 1810 errichteten D., etwa jenes zu Ehren von Salomon Gessner in Zürich (1793) aufges<span class="highlight">tell</span>te Monument, wurden zerstört oder in der Zeit der Restauration umgewandelt. Diese fand ihren Ausdruck im Löwendenkmal von Luzern (nach ei...<br>

  <div class="searchresprop">
Zeichen: <b>4510</b><br>
Autor: <b>Claude Lapaire / AW</b><br>
  </div>
 </div>
</div>


 <div class="searchres">
  <div class="searchrestitle">7 <a href="javascript:OpenWindow('/textes/d/D43603.php')">Bavaud, Maurice</a> </div>
  <div class="searchresdesc">...hrifts<span class="highlight">tell</span>er Rolf Hochhuth als neuer Wilhelm <span class="highlight">Tell</span> gefeiert. Hochhuths Heroisierung und Mystifizierung von B. wurde durch Klaus Urner in Frage ges<span class="highlight">tell</span>t, der den Fall u.a. auch unter psychohist. Aspekten beleuchtete. Niklaus Meienberg hat dem Thema einen Film und ein Buch gewidmet. Der Bundesrat räumte 1989 und erneut 1998 ein, dass sich die schweiz....<br>

  <div class="searchresprop">
Zeichen: <b>1485</b><br>
Autor: <b>Luc Weibel / AZ</b><br>
  </div>
 </div>
</div>


 <div class="searchres">
  <div class="searchrestitle">8 <a href="javascript:OpenWindow('/textes/d/D41353.php')">Bühler, Gion Antoni</a> </div>
  <div class="searchresdesc">...d Gedichte. Übersetzer von Schillers Wilhelm <span class="highlight">Tell</span> ins Romanische. Herausgeber von Liederbüchern für gemischte und Männerchöre. Mitredaktor der Zeitung Il Grischun, Gründer des Periodikums Il Novellist. 1863 Mitbegründer der Societad Retorumantscha. B.s intensiven Bemühungen um eine einheitliche rätorom. Schriftsprache war kein Erfolg beschieden. Li...<br>

  <div class="searchresprop">
Zeichen: <b>880</b><br>
Autor: <b>Ines Gartmann</b><br>
  </div>
 </div>
</div>


 <div class="searchres">
  <div class="searchrestitle">9 <a href="javascript:OpenWindow('/textes/d/D11835.php')">Gotthelf, Jeremias</a> </div>
  <div class="searchresdesc">...i die seltsame Magd (1843) und Der Knabe des <span class="highlight">Tell</span> (1846). Dass G.s Berliner Verleger Julius Springer die zahlreichen Erzählungen in der Sammlung Erzählungen und Bilder aus dem Volksleben der Schweiz zusammens<span class="highlight">tell</span>en und in Deutschland verbreiten konnte, lässt ihre sowohl regionale als auch überregionale Bedeutung erkennen.  Jeder der 13 Romane G.s e...<br>

  <div class="searchresprop">
Zeichen: <b>2860</b><br>
Autor: <b>Hanns Peter Holl</b><br>
  </div>
 </div>
</div>


 <div class="searchres">
  <div class="searchrestitle">10 <a href="javascript:OpenWindow('/textes/d/D22007.php')">Dorer, Robert</a> </div>
  <div class="searchresdesc">...übingen. Bekannt sind seine Entwürfe für das <span class="highlight">Tell</span>-Monument in Altdorf (UR), das Heinrich-Zschokke-Denkmal in Aarau und das Vadian-Denkmal in St. Gallen. Die idealisierenden Heldengestalten und seine Vorliebe für patriot. Themen entsprachen dem Zeitgeschmack. 1888-90 war D. Mitglied der Eidg. Kunstkommission. Literatur -BLSK, 274, (mit Werk- und Lit...<br>

  <div class="searchresprop">
Zeichen: <b>1155</b><br>
Autor: <b>Tapan Bhattacharya</b><br>
  </div>
 </div>
</div>

<div id="searchfoot"><form name="ftform2" action="index.php" method="get">
Seite:&nbsp;1 <a href="javascript:SubmitFt(document.ftform2, 11)">2</a> <a href="javascript:SubmitFt(document.ftform2, 21)">3</a> <a href="javascript:SubmitFt(document.ftform2, 31)">4</a> <a href="javascript:SubmitFt(document.ftform2, 41)">5</a> <a href="javascript:SubmitFt(document.ftform2, 51)">6</a> <a href="javascript:SubmitFt(document.ftform2, 61)">7</a> <a href="javascript:SubmitFt(document.ftform2, 71)">8</a> ... <a title="9" href="javascript:SubmitFt(document.ftform2, 81)">9</a><input name="process" value="now" type="hidden">
<input name="searchtype" value="fulltext" type="hidden">
<input name="searchlang" value="d" type="hidden">
<input name="searchstring" value="Tell" type="hidden">
<input name="searchstart" value="9" type="hidden">
<input name="catGEO" value="off" type="hidden">
<input name="catTEM" value="off" type="hidden">
<input name="catBIO" value="off" type="hidden">
<input name="catFAM" value="off" type="hidden">
<input style="display: none;" value="" type="submit">
&nbsp;&nbsp;<a href="javascript:SubmitFt(document.ftform2, 11)">Nächste Seite </a></form>
</div>  </div>
<div id="footer">
<img alt="" title="" src="img/footer.gif" height="24" width="800"><div id="impressum"><ul><li><span class="adresse"><b>Historisches Lexikon der Schweiz</b>, Tel. +41 31 313 13 30, Fax +41 31 313 13 39,</span> <script type="text/javascript">MTDHS('info')</script><a href="mailto:info@dhs.ch">info@dhs.ch</a></li><li>&nbsp;&nbsp;|&nbsp;&nbsp;</li><li><a class="grayed" href="javascript:SubmitPage(document.pageform, 'contact', 1)">Kontakt</a></li><li>&nbsp;&nbsp;|&nbsp;&nbsp;</li><li><a class="grayed" href="javascript:SubmitPage(document.pageform, 'impressum', 1)">Impressum</a></li></ul></div>
</div>
 </div>

		


  
EOT;




print $X;






?>