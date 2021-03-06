<?php
#############################################
#
# HEG Geneve
# Fabio Ricci Tel. +41(76)5281961
#
# Main Vars File
#

date_default_timezone_set('Europe/Zurich');

$PROGRAMNAME='RODIN';
$VERSION='2.8.6';
$_SESSION['RODINVERSION'] = $VERSION;
$RODIN_APPNAME = "RODIN";

#############################################
$userAgent = $_SERVER['HTTP_USER_AGENT'];

#############################################
# 20130312FRI - The following is used during 
# development of the rdf lab
$WANT_RFLAB=false;
#
#############################################


#############################################
$HIDE_WIDGETMENU_UC3=0;
$WANT_WIDGET_USER_PREFERENCES=1;
#############################################



if (preg_match("/Mobile/",$userAgent))
	$BROWSER='Mobile';
else
if (preg_match("/Firefox/",$userAgent))
{
	if (preg_match("/Macintosh/",$userAgent))
	$BROWSER='FIREFOX/MAC';
	if (preg_match("/Windows/",$userAgent))
	$BROWSER='FIREFOX/Win';
}
else
if (preg_match("/MSIE/",$userAgent))
{
	if (preg_match("/Windows/",$userAgent))
	$BROWSER='MSIE/Win';
}
//print "<br>BROWSER:$BROWSER";
#############################################


#############################################
# Manually set global variables
#
$DEFAULTRODINSKIN = 'first'; //Can also be 'fresh';
$FACETBOARDMINWIDTH="250";
$FACETBOARDMINWIDTH_PX=$FACETBOARDMINWIDTH."px";
$FACETBOARDMINHEIGHT="240px";//USED?
$FACETBOARDSHOWMAXLISTELEMS=3;

$IDLE_MAXTIMEOUT = -1; //60*10; // seconds, set to -1 to disable
$MAX_DB_RETRIES = 10;
$USLEEP_RETRY=100; # 0.1 sec

// Enable/disable access to ADMIN params on DB (i.e. during install)
$CAN_ACCESS_ADMIN_VAR = true;

// Used to be DBLOG_INFO but it is undefined
$DB_GENERAL_LOG_LEVEL = 0;
#############################################

####################################
# Interface manually set dimensions
# - Zen-filter

$ZEN_ICON_WIDTH = 15;
$ZEN_ICON_HEIGHT = 15;

$IMG_REFINING_WIDTH = 16;
$IMG_REFINING_HEIGHT = 16;

$SEARCHFILTER_TEXT_SIZE = 35;
####################################

#############################################
# Path variables
#
$DOCROOT = $_SERVER['DOCUMENT_ROOT'];
$USER = $_SESSION["user_id"];
if (!$USER) $USER=$_REQUEST["pid"];

$PROT = ($_SERVER['HTTPS']=='on') ? 'https' : 'http';
#$HOST = $_SERVER["SERVER_NAME"];
#$PORT = $_SERVER['SERVER_PORT'];

list($HOST,$PORT) = explode(':',$_SERVER['HTTP_HOST']);

$WEBROOT = "$PROT://$HOST";
$WEBROOT = (intval($PORT) == 80 || intval($PORT) == 0) ? $WEBROOT : "$WEBROOT:$PORT";

$availableSegmentsRE = "eng|heg|st|p|d|x|xxl";
$thisScriptPath = $_SERVER['SCRIPT_NAME'];
$thisScriptDirname = dirname($thisScriptPath);

if ($rodinsegment) // Use what is pre-set
	$RODINSEGMENT =	$rodinsegment;
else
if ($thisScriptPath<>'' && preg_match("/install_rodin.php/",$thisScriptPath)) {
	$CAN_ACCESS_ADMIN_VAR = false;
	$RODINSEGMENT =	'eng';
} else
if (preg_match("/\/($availableSegmentsRE)\/app\/u\/maintain_widgetkeys.php?/",$thisScriptPath,$match))
	$RODINSEGMENT =	$match[1];
else
if (preg_match("/\/(install)\//",$thisScriptDirname))  // During install
	$RODINSEGMENT = 'eng';
else
if (preg_match("/\/($availableSegmentsRE)\//",$thisScriptDirname,$match))
	$RODINSEGMENT =	$match[1];
else
if (preg_match("/\/makeinstall\/admin/",$thisScriptDirname))
	$RODINSEGMENT =	'eng';
else {
	$RODINSEGMENT = '';
	print "<br>RODINSEGMENT not found in ($thisScriptDirname). Possible values are: ($availableSegmentsRE)!";
}

if (false) // If case we need to set the segment manually
	$RODINSEGMENT =	'eng';


//include_once("$RODINSEGMENT/app/tests/TomaNota.php");
$filename="$RODINSEGMENT/app/tests/TomaNota.php"; $max=10;
#######################################
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}


//include_once("$RODINSEGMENT/app/tests/Logger.php");
$filename="$RODINSEGMENT/app/tests/Logger.php"; $max=10;
for ($x=1,$updir='';$x<=$maxretries;$x++,$updir.="../")
	if (file_exists("$updir$filename")) {include_once("$updir$filename");break;}



$candidatePath = str_replace("\\", "/", $thisScriptDirname);

if (preg_match("/(.\S+)\/$RODINSEGMENT\/(posh|app)/",$candidatePath,$match))
	$RODINROOT = $match[1];
else if (preg_match("/(.\S+)\/makeinstall\/admin/",$candidatePath,$match)
			|| preg_match("/(.\S+)\/install\/osx\/install_rodin.php/",$_SERVER['SCRIPT_NAME'],$match)
			|| preg_match("/(.+)\/(.+)\/fsrc\/app/",$candidatePath,$match)
			|| preg_match("/(.+)\/(.+)\/elib\/app/",$candidatePath,$match)
			|| preg_match("/(.\S+)\/(.\S+)\/fsrc\/app/",$_SERVER['SCRIPT_NAME'],$match)
			|| preg_match("/(.\S+)\/gen\/u/",$_SERVER['SCRIPT_NAME'],$match))
	$RODINROOT = $match[1];
else {
	$RODINROOT='/rodin_wrong_root';
	if (!preg_match('/fsrc/',$thisScriptPath))
	{
		print "<br>SYSTEM ERROR:($thisScriptPath) RODINROOT=$RODINROOT ... in ($candidatePath)";
		print "<br>Please run MAINTENANCE or inform the $RODINADMIN_LINK";
		exit;
	}
}

$UC3RODINROOT = str_replace('rodin','rodinuc3',$RODINROOT);
$RODIN = "$DOCROOT$RODINROOT/$RODINSEGMENT";
$RODINW = "$WEBROOT$RODINROOT/$RODINSEGMENT";
$RODINU = "$RODINROOT/$RODINSEGMENT/app/u";
$RODINRDFSTORENAME='rdfopt_'.$RODINSEGMENT;

//print "<br>root.php: RODINSEGMENT: $RODINSEGMENT";
//print "<br>root.php: RODIN: $RODIN";

$FB_SRC_TCHECKSERVICEURL = "$WEBROOT$RODINROOT/$RODINSEGMENT/fsrc/app/u/checkonto_temp.php";
$STOPWORD_SERVER = "$WEBROOT$RODINROOT/$RODINSEGMENT/fsrc/app/u/stopwords.php";
$TAGCLOUDRESPONDER = "$WEBROOT$RODINROOT/$RODINSEGMENT/app/u/TagCloudResponder.php";
$AUTOCOMPLETERESPONDER = "$WEBROOT$RODINROOT/$RODINSEGMENT/app/u/AutoCompleteResponder.php";
$LINK2RODINSPARQLENDPOINT = "$WEBROOT$RODINROOT/$RODINSEGMENT/fsrc/app/u/lod_sparql_endpoint.php";
$RDFSEMEXPLABURL = "$WEBROOT$RODINROOT/$RODINSEGMENT/app/tests/rdflab.php";
$LANGUAGEDETECTORLOGURL = "$WEBROOT$RODINROOT/$RODINSEGMENT/app/tests/langdetecionexaminer.php";
$RDFIZEURL = "$WEBROOT$RODINROOT/$RODINSEGMENT/app/u/rdfize.php";
$RDFSEMEXP_STOREEXPLORER = "$WEBROOT$RODINROOT/$RODINSEGMENT/fsrc/app/u/lod_sparql_endpoint.php?limit=5&storename=$RODINRDFSTORENAME";
$RODIN_PROFILING_LINK ="$WEBROOT$RODINROOT/usabilityLogFile.txt";
$RODIN_PROFILING_PATH ="$DOCROOT$RODINROOT/usabilityLogFile.txt";


$CURL_COOCKIEDIR = "$DOCROOT$RODINROOT/gen/u/tmp";

$CSS_URL = "$RODINROOT/$RODINSEGMENT/app/css";

$BASESERVERROOT = str_replace("/htdocs","",$DOCROOT);

$POSHDOCROOT = "$RODIN/posh";
$POSHWEBROOT = "$WEBROOT$RODINROOT/$RODINSEGMENT/posh";
$POSHIMGWEBROOT = "$WEBROOT$RODINROOT/$RODINSEGMENT/posh/images";

$RODINCACHE = "$RODINROOT/$RODINSEGMENT/posh/cache";

$RODINUTILITIES_GEN_URL="$RODINROOT/gen/u";
$RODINUTILITIES_CONVERSIONS_URL="$RODINUTILITIES_GEN_URL/conversions";

$MARC_TO_DC_UNQUALIFIED_XSL = "$DOCROOT$RODINUTILITIES_CONVERSIONS_URL/MARC21slim2RDFDC.xsl";

$RODINDATAURI = "$RODINROOT/gen/data";
$RODINBASEDATADIR = "$DOCROOT$RODINROOT/gen/data";
$RODINDATADIR = "http://$HOST$RODINROOT/gen/data";

$RODINIMAGESURL="$RODINROOT/gen/u/images";
$RODINIMAGES="$DOCROOT$RODINIMAGESURL";
$RODINIMAGESWEB="$WEBROOT$RODINIMAGESURL";
$POSHIMAGES="$RODINROOT/$RODINSEGMENT/posh/images";

//Busy-Wheel
$IMG_REFINING = $RODINIMAGESURL . '/wait.gif';
$IMG_REFINING_DONE = $RODINIMAGESURL . '/semantic_web_icon.png';
$IMG_REFINING_TITLE = "Calculating ontological facets to your query ...";

//Zen filter icon
$ZEN_FILTER_ICON = $RODINIMAGESURL . '/funnel.png';
//MoreLikeThis icon
$MLT_ICON = $RODINIMAGESURL . '/docsemfilter16x16.png';

//Tag-cloud icon
$TAG_CLOUD_ICON = $RODINIMAGESURL . '/clock-history.png';

//LOGO
$RODINLOGO=$POSHIMGWEBROOT . '/logo_portal.gif';
#############################################

//ICONS Switching ONTOThesauri on/off
$URL_ICONONTO_ON_OFF="$RODINUTILITIES_GEN_URL/images/ico_close.gif";
$URL_ICONONTO_OFF_ON="$RODINUTILITIES_GEN_URL/images/ico_open.png";


$lodLABHOMEPAGEURL="$RODINROOT/$RODINSEGMENT/app/u/rdfize.php?listwr=on";

$WANT_WIDGET_SEARCH =false;
$WANT_RDF_ANNOTATION=true; 
$WANT_RDF_STORE_INITIALIZED_AT_EVERY_SEARCH=			0    	;

$MANTIS_REPORTISSUE=str_replace(" ","+",
										"http://195.176.237.62/mantis/bug_report_advanced_page.php?summary=Please describe the subject of your issue here"
										."&description=Following issue on $PROGRAMNAME $VERSION: \n\n");
$URL_MANTIS=<<<EOM
<a class="mantisissueurl" 
		target="blank" 
		href="$MANTIS_REPORTISSUE" 
		title="Click to open an issue on $PROGRAMNAME version $VERSION"
		 - For any idea, suggestion or error reporting please
		 - click to open an issue">open an issue</a>&nbsp;
EOM;


########################################################################
# First batch of administrator parameters on DB
#
$PROXY_NAME = getA('PROXY_NAME');
$PROXY_IP = getA('PROXY_IP');
$PROXY_PORT = getA('PROXY_PORT');

$PROXY_AUTH_USERNAME = getA('PROXY_AUTH_USERNAME');
$PROXY_AUTH_PASSWD = getA('PROXY_AUTH_PASSWD');
$PROXY_AUTH_TYPE = getA('PROXY_AUTH_TYPE');

$AUTH_SELF_USERNAME = getA('AUTH_SELF_USERNAME');
$AUTH_SELF_PASSWD = getA('AUTH_SELF_PASSWD');
$AUTH_SELF_TYPE = getA('AUTH_SELF_TYPE');
########################################################################

########################################################################
# Database access administrator parameters
# - Please make sure the DBs & users exist!
#
$RODINDB_HOST = $RODINPOSHDB_HOST = 'localhost';

$RODINDB_DBNAME = getA('RODINDB_DBNAME');
$RODINDB_USERNAME = getA('RODINDB_USERNAME');
$RODINDB_PASSWD = getA('RODINDB_PASSWD');


$WEBSERVICE_USERID = getA('WEBSERVICE_USERID');
$WEBSERVICE_TABNAME = getA('WEBSERVICE_TABNAME');




if(0)
{
print "<br>RODINDB_HOST:$RODINDB_HOST";
print "<br>RODINDB_DBNAME:$RODINDB_DBNAME";
print "<br>RODINDB_USERNAME:$RODINDB_USERNAME";
print "<br>RODINDB_PASSWD:$RODINDB_PASSWD";

print "<br>WEBSERVICE_USERID:$WEBSERVICE_USERID";
print "<br>WEBSERVICE_TABNAME:$WEBSERVICE_TABNAME";

print "<br>WEBROOT:$WEBROOT";
print "<hr>ENV:<br>"; var_dump($_ENV);
print "<hr>SERVER:<br>"; var_dump($_SERVER);

}
$RODINPOSHDB_DBNAME = getA('RODINPOSHDB_DBNAME');
$RODINPOSHDB_USERNAME = getA('RODINPOSHDB_USERNAME');
$RODINPOSHDB_PASSWD = getA('RODINPOSHDB_PASSWD');
$ELIB_RIB_CLIENT = getA('ELIB_RIB_CLIENT');


// We are using a table to keep the adminitration values (getA function)
// which can be generated by the following SQL script

//CREATE TABLE `rodin_eng`.`administration` (
//	`name` VARCHAR( 40 ) NOT NULL COMMENT 'Parameter name',
//	`value` VARCHAR( 80 ) NOT NULL COMMENT 'Parameter value (always as text)',
//	`type` VARCHAR( 10 ) NOT NULL COMMENT 'text or number',
//	`comment` TEXT NOT NULL COMMENT 'Any description',
//	PRIMARY KEY ( `name` )
//) ENGINE = MYISAM ;
########################################################################
//Default Num of results:
$DEFAULT_M = getA('DEFAULT_M');
########################################################################
# SRC Paths
#
$SRCREFIFRAMENAME = 'srcframenamefor'.$ENV['user_id'];

$webRootWithAuthentication ="$PROT://$AUTH_SELF_USERNAME:$AUTH_SELF_PASSWD@$HOST:$PORT";

$SRC_INTERFACE_BASE_URL = $AUTH_SELF_USERNAME <> '' ?
			$webRootWithAuthentication . $RODINROOT . "/$RODINSEGMENT/app/s" :
			"$WEBROOT$RODINROOT/$RODINSEGMENT/app/s";

$LOCAL_SRC_START_INTERFACE = "$SRC_INTERFACE_BASE_URL/start/";
$LOCAL_SRC_REFINE_INTERFACE	= "$SRC_INTERFACE_BASE_URL/refine/";
$SRCLINKBASE="$WEBROOT{$RODINROOT}/$RODINSEGMENT/fsrc/app/u";
########################################################################

###########################################
# Service timeouts
#
$FSRC_CURL_TIMEOUT_SEC = 20;
$DBPEDIATIMEOUT_MSEC = $FSRC_CURL_TIMEOUT_SEC * 1000 - 200;

$INTERNET_CHECK_TIMEOUT = 2;

$CALLING_TIMEOUT_SEC = 5;
$WIDGET_SEARCH_MAX = 15000; //msek = 15sec
$SRC_SEARCH_MAX = 15000; //msek = 15sec
$WIDGET_HEIGHT=237;
###########################################

$EPSILON=0.0000000001;

$LANGUAGE_DETECTOR="$WEBROOT$RODINROOT/$RODINSEGMENT/app/u/LanguageDetector.php";
$LANGUAGE_DETECTION="$DOCROOT$RODINROOT/$RODINSEGMENT/app/u/LanguageDetection.php";

#######################################
# SRC Params
#
$SRC_MAXRESULTS = 15;

#ARC DB & Default configuration
	$ARCDB_DBNAME = getA('ARCDB_DBNAME');
	$ARCDB_USERNAME = getA('ARCDB_USERNAME');
	$ARCDB_USERPASS = getA('ARCDB_USERPASS');
	$SRCDB_DBHOST = getA('ARCDB_DBHOST');
	
	$GRAPHVIZ_PATH= getA('GRAPHVIZ_PATH');
	$GRAPHVIZ_TMP_PATH= getA('GRAPHVIZ_TMP_PATH');
					
	$ARCCONFIG = array(
		/* db */
		'db_name' => $ARCDB_DBNAME,
		'db_user' => $ARCDB_USERNAME,
		'db_pwd'  => $ARCDB_USERPASS,
	
		/* store */
		'store_name' => '', // must be set
		'adjust_utf8' => true,
		/* stop after 100 errors */
		'max_errors' => 100,
		/* path to dot ?*/
	  'graphviz_path' => $GRAPHVIZ_PATH,
	  /* tmp dir (default: '/tmp/') */
	  'graphviz_temp' => $GRAPHVIZ_TMP_PATH,
		
		'arcUtilities' => "$DOCROOT$RODINROOT/$RODINSEGMENT/app/u/arcUtilities.php",
	);
	
	
	#SRC DB
	$SRCDB_DBNAME				=getA('SRCDB_DBNAME');
	$SRCDB_USERNAME			=getA('SRCDB_USERNAME');
	$SRCDB_USERPASS			=getA('SRCDB_USERPASS');
	$SRCDB_DBHOST				=getA('SRCDB_DBHOST');

	
########################################################################
# Enabling use of local ARC2 copy of DBPedia
#
$USE_LOCAL_DBPEDIA = true;
$LOCAL_DBPEDIA_DB_NAME = $SRCDB_DBNAME;
$LOCAL_DBPEDIA_ARC_NAME = 'dbpedialocal'; //This database was called so and it stay this way
########################################################################



#######################################
# Google API use variables
#
$GOOGLEAPIKEY = getWK('GOOGLEAPIKEY');


$EXTRAINCLUDE_GOOGLE_LANGUAGE_LOAD = <<<EOI
		<!--script src="http://www.google.com/jsapi?key=$GOOGLEAPIKEY" type="text/javascript"></script-->
		<script type='text/javascript'>
			/*if (typeof(google) != 'undefined') google.load("search", "1");   /*FRI 20111611*/
		</script>
EOI;
#######################################

#######################################
# Text-Zoom Buttons
#
$B_MIN_ICON_NORMAL = "$RODINUTILITIES_GEN_URL/images/button-min-normal.png";
$B_MIN_ICON_SELECTED = "$RODINUTILITIES_GEN_URL/images/button-min-selected.png";
$B_MIN_ICON_HOVER = "$RODINUTILITIES_GEN_URL/images/button-min-hover.png";
$B_TOKEN_ICON_NORMAL = "$RODINUTILITIES_GEN_URL/images/button-token-normal.png";
$B_TOKEN_ICON_SELECTED = "$RODINUTILITIES_GEN_URL/images/button-token-selected.png";
$B_TOKEN_ICON_HOVER = "$RODINUTILITIES_GEN_URL/images/button-token-hover.png";
$B_ALL_ICON_NORMAL = "$RODINUTILITIES_GEN_URL/images/button-all-normal.png";
$B_ALL_ICON_SELECTED = "$RODINUTILITIES_GEN_URL/images/button-all-selected.png";
$B_ALL_ICON_HOVER = "$RODINUTILITIES_GEN_URL/images/button-all-hover.png";
$B_FILTER_ICON_NORMAL = "$RODINUTILITIES_GEN_URL/images/button-filter-normal.png";
$B_FILTER_ICON_SELECTED = "$RODINUTILITIES_GEN_URL/images/button-filter-selected.png";
$B_FILTER_ICON_HOVER = "$RODINUTILITIES_GEN_URL/images/button-filter-hover.png";
#######################################

$ROMAN_REGEX='/^m{0,3}(cm|cd|d?C{0,3})(xc|xl|l?x{0,3})(ix|iv|v?i{0,3})$/';






#######################################
# Other global variables
#
$RODINADMIN_ADMIN_EMAILADDR = getA('RODINADMINEMAILADDR');
$RODINADMIN_LINK = <<<EOL
<a href='mailto:$RODINADMIN_ADMIN_EMAILADDR'
	title='Send a message to the current RODIN administrator $RODINADMIN_ADMIN_EMAILADDR'>RODIN administrator</a>
EOL;

$RODINSKIN = $_GET['skin']; 
if (!$RODINSKIN) $RODINSKIN = getA('SKIN');
if ($RODINSKIN)
{
	//Can we find it also on a disk?
	$SKINDIR="app/css/skins/$RODINSKIN";
	#######################################
	$max=10;
	for ($x=1,$updir='';$x<=$max;$x++,$updir.="../")
	{
		if (file_exists("$updir$SKINDIR"))
		{
			$skinfound=true;
			break;
		}
	}

	if($skinfound)
	{
		$_SESSION['RODINSKIN']=$RODINSKIN;
		$SKINDIR="$updir$SKINDIR";
		//print "SKINDIR FOUND: $SKINDIR";
	}
	else
	{
		$SKINDIR="$RODIN/app/css/skins/$DEFAULTRODINSKIN";
		//print "SKINDIR NOT FOUND AND SET TO: $SKINDIR";
	}
}


if (file_exists("$SKINDIR/RODIN_COLORS.php"))
	include_once("$SKINDIR/RODIN_COLORS.php");

#############################################
#############################################
# END OF VARIABLE DECLARATION
#############################################

#$RESULTS_STORE_METHOD='mysql'; #USE MYSQL TO STORE RODIN RESULTS
$RESULTS_STORE_METHOD='solr'; #USE SOLR TO STORE RODIN RESULTS


#############################################
# SOLR INTEFACE
#############################################

$SOLR_INTERFACE_URI = "$RODIN/app/u/SOLRinterface";

$SOLARIUMURL="$PROT://$HOST$RODINROOT/gen/u/solarium/library/Solarium";
$SOLARIUMDIR="$DOCROOT$RODINROOT/gen/u/solarium/library/Solarium";
$SOLR_PORT = 8885; // FRI !!!

$SOLR_MLT_MINSCORE=1.0; //Accept/show in MLT queries only values showing this or higher scores


# SOLR RODIN CONFIG for collection rodin_result:
$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['user']='rodin';
$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['host']='localhost';
$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['port']=$SOLR_PORT;
$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['path']='/solr/rodin_result/';
$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['core']=null;
$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['timeout']=5;

$SOLR_RODIN_RESULT_URL="http://"
                      .$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['host']
                      .':'
                      .$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['port']
                      .$SOLR_RODIN_CONFIG['rodin_result']['adapteroptions']['path']
                      ;

$SOLR_ADD_DOC_URI="$WEBROOT$RODINROOT/$RODINSEGMENT/app/u/SOLRinterface/add_solr_doc.php";
$SOLR_BRIDGE="$WEBROOT$RODINROOT/$RODINSEGMENT/app/u/SOLRinterface/solr_bridge.php";

# SOLR RODIN CONFIG for collection rodin_search:
$SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['user']='rodin';
$SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['host']='localhost';
$SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['port']=$SOLR_PORT;
$SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['path']='/solr/rodin_search/';
$SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['core']=null;
$SOLR_RODIN_CONFIG['rodin_search']['adapteroptions']['timeout']=5;

# SOLR RODIN CONFIG for collection cached_rodin_widget_response:
$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['adapteroptions']['user']='rodin';
$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['adapteroptions']['host']='localhost';
$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['adapteroptions']['port']=$SOLR_PORT;
$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['adapteroptions']['path']='/solr/cached_rodin_widget_response/';
$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['adapteroptions']['core']=null;
$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['adapteroptions']['timeout']=5;
$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['rodin']['cache_expiring_time_hour']=24*3; //1 day

# SOLR RODIN CONFIG for collection cached_rodin_src_response:
$SOLR_RODIN_CONFIG['cached_rodin_src_response']['adapteroptions']['user']='rodin';
$SOLR_RODIN_CONFIG['cached_rodin_src_response']['adapteroptions']['host']='localhost';
$SOLR_RODIN_CONFIG['cached_rodin_src_response']['adapteroptions']['port']=$SOLR_PORT;
$SOLR_RODIN_CONFIG['cached_rodin_src_response']['adapteroptions']['path']='/solr/cached_rodin_src_response/';
$SOLR_RODIN_CONFIG['cached_rodin_src_response']['adapteroptions']['core']=null;
$SOLR_RODIN_CONFIG['cached_rodin_src_response']['adapteroptions']['timeout']=5;
$SOLR_RODIN_CONFIG['cached_rodin_src_response']['rodin']['cache_expiring_time_hour']=24*7; //1 week

# SOLR for collection ZBW STW:
$SOLR_RODIN_CONFIG['zbw_stw']['adapteroptions']['user']='rodin';
$SOLR_RODIN_CONFIG['zbw_stw']['adapteroptions']['host']='localhost';
$SOLR_RODIN_CONFIG['zbw_stw']['adapteroptions']['port']=$SOLR_PORT;
$SOLR_RODIN_CONFIG['zbw_stw']['adapteroptions']['path']='/solr/zbw_stw/';
$SOLR_RODIN_CONFIG['zbw_stw']['adapteroptions']['core']=null;
$SOLR_RODIN_CONFIG['zbw_stw']['adapteroptions']['timeout']=5;
$SOLR_RODIN_CONFIG['zbw_stw']['rodin']['cache_expiring_time_hour']=24*7; //1 week
//
# SOLR for collection GESIS theSoz:
$SOLR_RODIN_CONFIG['gesis_thesoz']['adapteroptions']['user']='rodin';
$SOLR_RODIN_CONFIG['gesis_thesoz']['adapteroptions']['host']='localhost';
$SOLR_RODIN_CONFIG['gesis_thesoz']['adapteroptions']['port']=$SOLR_PORT;
$SOLR_RODIN_CONFIG['gesis_thesoz']['adapteroptions']['path']='/solr/gesis_thesoz/';
$SOLR_RODIN_CONFIG['gesis_thesoz']['adapteroptions']['core']=null;
$SOLR_RODIN_CONFIG['gesis_thesoz']['adapteroptions']['timeout']=5;
$SOLR_RODIN_CONFIG['gesis_thesoz']['rodin']['cache_expiring_time_hour']=24*7; //1 week

# SOLR for collection BNF RAMEAU:
$SOLR_RODIN_CONFIG['bnf_rameau']['adapteroptions']['user']='rodin';
$SOLR_RODIN_CONFIG['bnf_rameau']['adapteroptions']['host']='localhost';
$SOLR_RODIN_CONFIG['bnf_rameau']['adapteroptions']['port']=$SOLR_PORT;
$SOLR_RODIN_CONFIG['bnf_rameau']['adapteroptions']['path']='/solr/bnf_rameau/';
$SOLR_RODIN_CONFIG['bnf_rameau']['adapteroptions']['core']=null;
$SOLR_RODIN_CONFIG['bnf_rameau']['adapteroptions']['timeout']=5;
$SOLR_RODIN_CONFIG['bnf_rameau']['rodin']['cache_expiring_time_hour']=24*7; //1 week

# SOLR for collection LOC SH:
$SOLR_RODIN_CONFIG['loc_sh']['adapteroptions']['user']='rodin';
$SOLR_RODIN_CONFIG['loc_sh']['adapteroptions']['host']='localhost';
$SOLR_RODIN_CONFIG['loc_sh']['adapteroptions']['port']=$SOLR_PORT;
$SOLR_RODIN_CONFIG['loc_sh']['adapteroptions']['path']='/solr/loc_sh/';
$SOLR_RODIN_CONFIG['loc_sh']['adapteroptions']['core']=null;
$SOLR_RODIN_CONFIG['loc_sh']['adapteroptions']['timeout']=5;
$SOLR_RODIN_CONFIG['loc_sh']['rodin']['cache_expiring_time_hour']=24*7; //1 week

# SOLR for collection GND:
$SOLR_RODIN_CONFIG['dnb_gnd']['adapteroptions']['user']='rodin';
$SOLR_RODIN_CONFIG['dnb_gnd']['adapteroptions']['host']='localhost';
$SOLR_RODIN_CONFIG['dnb_gnd']['adapteroptions']['port']=$SOLR_PORT;
$SOLR_RODIN_CONFIG['dnb_gnd']['adapteroptions']['path']='/solr/dnb_gnd/';
$SOLR_RODIN_CONFIG['dnb_gnd']['adapteroptions']['core']=null;
$SOLR_RODIN_CONFIG['dnb_gnd']['adapteroptions']['timeout']=5;
$SOLR_RODIN_CONFIG['dnb_gnd']['rodin']['cache_expiring_time_hour']=24*7; //1 week

# SOLR for collection solariumtests:
$SOLR_RODIN_CONFIG['solariumtests']['adapteroptions']['user']='rodin';
$SOLR_RODIN_CONFIG['solariumtests']['adapteroptions']['host']='localhost';
$SOLR_RODIN_CONFIG['solariumtests']['adapteroptions']['port']=$SOLR_PORT;
$SOLR_RODIN_CONFIG['solariumtests']['adapteroptions']['path']='/solr/solariumtests/';
$SOLR_RODIN_CONFIG['solariumtests']['adapteroptions']['core']=null;
$SOLR_RODIN_CONFIG['solariumtests']['adapteroptions']['timeout']=5;

# SOLR for collection subject_ranking:
$SOLR_RODIN_CONFIG['subject_ranking']['adapteroptions']['user']='rodin';
$SOLR_RODIN_CONFIG['subject_ranking']['adapteroptions']['host']='localhost';
$SOLR_RODIN_CONFIG['subject_ranking']['adapteroptions']['port']=$SOLR_PORT;
$SOLR_RODIN_CONFIG['subject_ranking']['adapteroptions']['path']='/solr/subject_ranking/';
$SOLR_RODIN_CONFIG['subject_ranking']['adapteroptions']['core']=null;
$SOLR_RODIN_CONFIG['subject_ranking']['adapteroptions']['timeout']=5;

# SOLR for collection docs_ranking:
$SOLR_RODIN_CONFIG['docs_ranking']['adapteroptions']['user']='rodin';
$SOLR_RODIN_CONFIG['docs_ranking']['adapteroptions']['host']='localhost';
$SOLR_RODIN_CONFIG['docs_ranking']['adapteroptions']['port']=$SOLR_PORT;
$SOLR_RODIN_CONFIG['docs_ranking']['adapteroptions']['path']='/solr/docs_ranking/';
$SOLR_RODIN_CONFIG['docs_ranking']['adapteroptions']['core']=null;
$SOLR_RODIN_CONFIG['docs_ranking']['adapteroptions']['timeout']=5;


# SOLR (general) for ARC collection(s):
$SOLR_RODIN_CONFIG['arc']['adapteroptions']['user']='rodin';
$SOLR_RODIN_CONFIG['arc']['adapteroptions']['host']='localhost';
$SOLR_RODIN_CONFIG['arc']['adapteroptions']['port']=$SOLR_PORT;
$SOLR_RODIN_CONFIG['arc']['adapteroptions']['path']='/solr/arc/'; // should be overwritten
$SOLR_RODIN_CONFIG['arc']['adapteroptions']['core']=null;
$SOLR_RODIN_CONFIG['arc']['adapteroptions']['timeout']=5;


$SOLR_RODIN_LOCKDIR="$DOCROOT$RODINROOT/$RODINSEGMENT/app/data/locks/solr";
#############################################
# END OF SOLR INTEFACE
#############################################


#############################################
# CONSTANTS
#############################################
$txtDefaultSubjectRanked = "default ranked upon subjects relevance";
$txtDefaultRescoredSubjectRanked = "default (adapted) ranked upon subjects relevance";
$txtPosiRanked = "positively ranked by your personal filter";
$txtNegaRanked = "negatively ranked by your personal filter";
 


function get_rodin_skin()
{
	//print " get_rodin_skin() returns: ".$_SESSION['RODINSKIN'];
	return 	$_SESSION['RODINSKIN'];
}



function getA($name)
#########################
#
# This function returns important
# operating params to RODIN
# stores them in the session
# for sake of speed
# This function access only 'rootparam'
#
{
	global $CAN_ACCESS_ADMIN_VAR;
	global $thisScriptPath;
	
	if ($CAN_ACCESS_ADMIN_VAR) 
	{
		if ($val = $_SESSION[$name])
			 ;
		else
		{			
			if ($val = getRodinAdminFromDB($name,'rootparam'))
				$_SESSION[$name]=$val;
		}
		#print "<br>getA($name)=>$val";
		return $val;
	}
	else
	return "installing...nodb";
}

function getWK($name)
#########################
#
# This function returns important
# operating params to RODIN
# stores them in the session
# for sake of speed
# This function access only 'widgetkey' records
#
{
	global $CAN_ACCESS_ADMIN_VAR;

	if ($CAN_ACCESS_ADMIN_VAR)
	{
		if ($val = $_SESSION[$name])
			 ;
		else
		{
			if ($val = getRodinAdminFromDB($name,'widgetkey'))
				$_SESSION[$name]=$val;
		}
		return $val;
	}
	else
	return "installing,,,nodb";
}



#this block should come inside and outside the folloqing function
	$RODINROOTstr					= str_replace("-/","",substr($RODINROOT,1,strlen($RODINROOT)));
	$RODINADMIN_HOST			= 'localhost';
	$ADMINDBBASENAME			= limitusernamelength($RODINROOTstr."_root_");
	$RODINADMIN_DBNAME 		= limitusernamelength($RODINROOTstr."_root_".$RODINSEGMENT);
	$RODINADMIN_USERNAME 	= limitusernamelength($RODINROOTstr."_root_".$RODINSEGMENT);
	$RODINADMIN_USERPASS 	= strrev($RODINADMIN_USERNAME);

function getRodinAdminFromDB($name,$purpose)
#returns administration[name].value
{
	global $MAX_DB_RETRIES, $USLEEP_RETRY;
	global $verbose, $RODINROOT, $RODINSEGMENT;
	global $ADMINDBBASENAME, $RODINADMIN_HOST, $RODINADMIN_DBNAME, $RODINADMIN_USERNAME, $RODINADMIN_USERPASS;
	//PLEASE ADAPT ALSO IN install_rodin.php AND in u/maintain_widgetkey.php
	$RODINROOTstr					= str_replace("-/","",substr($RODINROOT,1,strlen($RODINROOT)));
	$RODINADMIN_HOST			= 'localhost';
	$ADMINDBBASENAME			= limitusernamelength($RODINROOTstr."_root_");
	$RODINADMIN_DBNAME 		= limitusernamelength($RODINROOTstr."_root_".$RODINSEGMENT);
	$RODINADMIN_USERNAME 	= limitusernamelength($RODINROOTstr."_root_".$RODINSEGMENT);
	$RODINADMIN_USERPASS 	= strrev($RODINADMIN_USERNAME);
		//The Admin DB is always= rodin-sync_eng_ADMIN
	//Passwd=rodin-sync User=rodin-sync
	$GETQUERY="SELECT * FROM administration WHERE active=1 AND name = '$name' AND purpose = '$purpose';";
	//print "<br>$GETQUERY";

	//print "mysqli_connect($RODINADMIN_HOST,$RODINADMIN_USERNAME,$RODINADMIN_USERPASS,$RODINADMIN_DBNAME)";
	try {
		$TRIES=0;
		while ($TRIES++ < $MAX_DB_RETRIES
					 && (!$conn = mysqli_connect($RODINADMIN_HOST,$RODINADMIN_USERNAME,$RODINADMIN_USERPASS,$RODINADMIN_DBNAME))) usleep($USLEEP_RETRY);
		if (!$conn)
		{
			print "<br />Could not connect to $RODINADMIN_HOST with :::$RODINADMIN_USERNAME:::$RODINADMIN_USERPASS:::";
			print "<br />Reason : " . mysqli_error($conn);
			fontprint("Could not connect to admin database after $MAX_DB_RETRIES retries.\n", "red");
		}

		if ($verbose)
		{
			print "<br>$conn = mysqli_connect($RODINADMIN_HOST,$RODINADMIN_USERNAME,$RODINADMIN_USERPASS)";
			print "<br>mysqli_select_db($RODINADMIN_DBNAME)";
		}

		
		#Try to get param value from DB

		$TRIES=0;
		while ($TRIES++ < $MAX_DB_RETRIES
					 && (!$resultset = mysqli_query($conn,$GETQUERY))) usleep($USLEEP_RETRY);
		if ($resultset)
		{
			$row = mysqli_fetch_assoc($resultset);
			$value=$row['value'];
			$type=$row['type'];
		}
		else
		{
			print "<br> EMPTY resultset to ($GETQUERY) "
						."<br>please check select rights on user $RODINADMIN_USERNAME and restart RODIN."
						."<br><br> GRANT SELECT ON `$RODINADMIN_DBNAME` . * TO '$RODINADMIN_USERNAME'@'%';"
						."<br><br>(RODIN terminated here)";
			exit;
		}
		mysqli_close($conn);
	}
	catch (Exception $e)
	{
		print("Problem at getRodinAdminFromDB($name): ".$e);
	}

	//print "<br>getRodinAdminFromDB($name) returning ($value)";

	if (strtolower($type)=='number')
		$value=intval($value);
	return $value;
}





function update_admin_key($NEWRODINSEGMENT,$RODINADMIN_USERNAME,$RODINADMIN_USERPASS,$key,$value,$type='text')
####################################################
{
	global $RODINADMIN_HOST, $RODINROOTstr, $RODINSEGMENT;
	#NOTE: ONLY DBNAME in function of $RODINSEGMENT:
	$RODINADMIN_DBNAME 		= limitusernamelength($RODINROOTstr."_root_".$NEWRODINSEGMENT);

	$ok=false;

	if (strtolower($type)=='text')
		$VALUESET=" value='$value' ";
	else
	if (strtolower($type)=='number')
	{
			if ($value==0)
				$VALUESET=" value=0 ";
			else
				$VALUESET=" value=$value ";
	}

	$QUERY="UPDATE `$RODINADMIN_DBNAME`.`administration` SET $VALUESET WHERE name = '$key'";

	try {

		if (!$conn = mysqli_connect($RODINADMIN_HOST,$RODINADMIN_USERNAME,$RODINADMIN_USERPASS,$RODINADMIN_DBNAME))
		{
			print "<br />Could not connect to $RODINADMIN_HOST with :::$RODINADMIN_USERNAME:::$RODINADMIN_USERPASS:::";
			print "<br />Reason : " . mysqli_error($conn);
			fontprint("Could not connect to admin database.\n", "red");
		}

		if ($verbose)
		{
			print "<br>$conn = mysqli_connect($RODINADMIN_HOST,$RODINADMIN_USERNAME,$RODINADMIN_USERPASS,$RODINADMIN_DBNAME)";
		}
		
		if (! $resultset = mysqli_query($conn,$QUERY))
		{
			fontprint("<br>Unable to successfully execute query $QUERY using $RODINADMIN_USERNAME\n", "red");
		}
		$affected_rows=mysqli_affected_rows($conn);

		if ($affected_rows < 0)
		{
			fontprint(  "<br> ERROR SETTING ADMIN KEY '$key' in Administration database ($affected_rows rows on query=(($QUERY)) ). mysqli_error()= ".mysqli_error($conn)
					  ."<br>Please contact your RODIN Administrator."
						."<br><br>(RODIN terminated here)", 'red');
			exit;
		}
		else $ok=true;
		mysqli_close($conn);
	}
	catch (Exception $e)
	{
		print("Problem at update_admin_key($key=$value,$type): ".$e);
	}

	return $ok;

}





function limitusernamelength($uname, $limitlen=16)
{
	$len=strlen($uname);
	if ($len > $limitlen)
	{
		$mitte = $limitlen/2;
		$primo	=substr($uname,0,$mitte);
		$secondo=substr($uname,$len - $mitte, $mitte);
		#print "<br>limitusernamelength($uname) --> $primo$secondo";
		return $primo.$secondo;
	}
	else
	{
		return $uname;
	}
}


$ADMIN_USING=0;


$ROOT=1;

####################################
?>