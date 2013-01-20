<?php
$filenamex="app/root.php";
#######################################
$max=10;
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

// LANGUAGE LABELS FROM PORTANEO:
$filenamex='app/exposh/l10n/'.getApplLang().'/lang.php';
#######################################
$max=10;
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
//if ($ROOT) print "<br>root geladen";
//else print "<br>NO ROOT?<br>";
#######################################

	$FONTRED = "<font style=\"color:red;\">";
	$STYLEFONTSEGMENT=" style=\"color:$COLOR_RODINSEGMENT;font-size:normal;font-weight:bold\" ";
	$STYLEFONTRESULT=" style=\"color:black;font-size:normal;font-weight:bold\" ";
	$STYLEFONTGREEN=" style=\"color:green;font-size:normal;font-weight:bold\" ";
	$STYLEFONTGRAY=" style=\"color:#aaaaaa;font-size:normal;font-weight:bold\" ";
	$FONTRESULT="<font $STYLEFONTRESULT>";
	$FONTRESULTGREEN="<font $STYLEFONTGREEN>";
	$FONTGRAY="<font $STYLEFONTGRAY>";
	$FONTGREEN="<font $STYLEFONTGREEN>";
	$FONTRESULTSEGMENT="<font $STYLEFONTSEGMENT>";
	$FONTGUI=$FONTRESULT;
	$FONTSRC=$FONTRESULTGREEN;
	
	
switch ($RODINSEGMENT) {
	case('eng') : $COLOR_RODINSEGMENT="#006600"; break;
	case('st') : $COLOR_RODINSEGMENT="#660000"; break;
	case('p') : $COLOR_RODINSEGMENT="#000066"; break;
	case('d') : $COLOR_RODINSEGMENT="#336699"; break;
	case('x') : $COLOR_RODINSEGMENT="#DD6600"; break;
	default : $COLOR_RODINSEGMENT="#000000";
}

$COLOR_ORANGE	="#FF6600";
$COLOR_GRAY		="#aaaaaa";
$COLOR_LIGHTGRAY="#bbbbbb";

$SONTWHITE="<font style=\"color:#eeeeee;\">";
$STYLEFONTRESULT=" style=\"color:black;font-size:normal\" ";
$FONTRESULT="<font $STYLEFONTRESULT>";
$FONTRESULTURL="<font style=\"color:blue;font-size:normal;font-weight:bold\">";
$STYLEFONTLABEL =" style=\"color:grey;font-size:normal;font-weight:bold\" ";
$STYLEFONTGREY =" style=\"color:#bbbbbb;font-size:normal;font-weight:bold\" ";
$FONTLABEL ="<font $STYLEFONTLABEL>";
$FONTGREY ="<font $STYLEFONTGREY>";
$ENDFONTLABEL="</font>";
$ENDFONTRESULT="</font>";
$ENDFONTRESULTURL=$ENDFONTRESULT;
$fontVERSION = "<font style=\"font-size:x-small;\">";





function ckeck_sid($sid,$messge)
################################
#
# Returns true, if sid is ok
# otherwise false and issue a warning
#
{
	$ERROR = ($sid=='' || !$sid);
	if ($ERROR)
	{
		fontprint($messge,'red');
	}
	return (!$ERROR);
}



function fri_objectToArray($d) {
  if (is_object($d)) {
    $d = get_object_vars($d);
  }
 return $d;
}


function objectToArray($d) {
		if (is_object($d)) {
			// Gets the properties of the given object
			// with get_object_vars function
      print "... IS_OBJECT ... ";
			$d = get_object_vars($d);
      
      print "<br>objectToArray<br>d: ";var_dump($d);print "<hr>";
    }
 
		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
      print "... IS_ARRAY ... ";
			return array_map(__FUNCTION__, $d);
		}
		else {
      print "... IS_BOH ... ";
			// Return array
			return $d;
		}
	}
  
  
  function arrayToObject($d) {
		if (is_array($d)) {
			/*
			* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return (object) array_map(__FUNCTION__, $d);
		}
		else {
			// Return object
			return $d;
		}
	}
 



function  make_time_sid($sid)
{
   //20090700.214654.633.fabio
	  $YYYY=substr($sid,0,4);
    	$MM=substr($sid,4,2);
		$DD=substr($sid,6,2);
		$hh=substr($sid,9,2);
		$mm=substr($sid,11,2);
		$ss=substr($sid,13,2);

	  $TIME=substr($sid,7,8);

  	$time_rep="$DD.$MM.$YYYY $hh:$mm:$ss";
    return $time_rep;
}


function str_limit($string,$maxSize,$middle=false)
#######################################
{
	if ($points)
	{
		$phi = $maxSize - 3;
		$punkten = (strlen($string) > $phi );
	}
	else
	{
		$phi=$maxSize;
		$punkten='';
	}
  if (strlen($string) > $maxSize)
  {
  	if ($middle)
    {
    	$meta = ($maxSize / 2);
   		$firstpart= substr($string,0,$meta);

      $lastpart= substr($string,strlen($string) - $meta ,strlen($string));

      $begrenzterText= "$firstpart...$lastpart";
    }
    else
			$begrenzterText = substr($string,0,$phi)."...";

  }
  else
  		$begrenzterText = $string;

	return 	$begrenzterText;
}








function check_internetconn()
#####################################
{
	$res=true;
  $dockeck=true;
	if ($dockeck)
	{
    //print "check_internetconn";
    global $INTERNET_CHECK_TIMEOUT;
    // fsockopen  ( string $hostname  [, int $port= -1  [, int &$errno  [, string &$errstr  [, float $timeout= ini_get("default_socket_timeout")  ]]]] )
    $domain = "www.search.ch";
    $port = 80;
    
    $result=parametrizable_curl("http://$domain:$port");
    
    //print "RESULT OF URLCHECK: (((".htmlentities($result).")))";
    $errorwarning = 'This error indicates that the gateway could not find the IP address of the website you are trying to access';
    
    if ($result=='' || strstr($result,$errorwarning))
    {
      $res=false;
      //print " INTERNET NOK ";
    }
    else
    {
      //print " INTERNET OK ";
      $res=true;
    }
	}
	return $res;
}


function plus($txt)
{
  $txt= str_replace('%20','+',$txt);
  return str_replace(' ','+',$txt);
}





function inform_exc($e)
{
		$ERROR=true;
		$now=date("d.m.Y H:i:s");
		$RODINADMIN="<a href=\"mailto:fabio.fr.ricci@hesge.ch\" subject=\"RODIN\">RODIN administrator</a>";
		print "<hr>System error (Please inform the $RODINADMIN with the exact timeSTAMP ($now)):<br>$FONTRED $e</font><hr>";
		exit;
}


function inform_bad_internal_web($e)
{
		$ERROR=true;
		$now=date("d.m.Y H:i:s");
		$RODINADMIN="<a href=\"mailto:fabio.fr.ricci@hesge.ch\" subject=\"RODIN\">RODIN administrator</a>";
		print "<hr>BAD internal web service (Please inform the $RODINADMIN with the exact timeSTAMP ($now)):<br>$FONTRED $e</font><hr>";
		exit;
}


function inform_bad_external_web($e)
{
		$ERROR=true;
		$now=date("d.m.Y H:i:s");
		$RODINADMIN="<a href=\"mailto:fabio.fr.ricci@hesge.ch\" subject=\"RODIN\">RODIN administrator</a>";
		print "<hr>BAD External web service (Please inform the $RODINADMIN with the exact timeSTAMP ($now)):<br>$FONTRED $e</font><hr>";
		exit;
}

function inform_bad_db($e)
{
		$ERROR=true;
		$now=date("d.m.Y H:i:s");
		$RODINADMIN="<a href=\"mailto:fabio.fr.ricci@hesge.ch\" subject=\"RODIN\">RODIN administrator</a>";
		print "<hr>DB unavailable (Please inform the $RODINADMIN with the exact timeSTAMP ($now)):<br>$FONTRED $e</font><hr>";
		exit;
}



function clean_html($str)
{
  $debug=0;

  if ($debug) print "\n<br>clean_html($str)";
	$str = str_replace("&nbsp;"," ",$str);

	$str = str_replace("&amp;","&",$str);
	$str = str_replace("&raquo;","'",$str);
	$str = str_replace("&rsquo;","'",$str);
  if ($debug) print "\n<br>clean_html($str)";



  return trim($str);
}





function fontprint($txt,$txtcolor="black",$fontsize="12pt",$fontart="arial")
######################
{
print "<font style='color:$txtcolor; font:$fontart;font-size:$fontsize;'>$txt</font>";

}


/*
 * return a string split to the nth occurence of needle:
 */
function splitn($string, $needle, $offset)
{
    $newString = $string;
    $totalPos = 0;
    $length = strlen($needle);
    for($i = 0; $i < $offset; $i++)
    {
        $pos = strpos($newString, $needle);

        // If you run out of string before you find all your needles
        if($pos === false)
            return false;
        $newString = substr($newString, $pos+$length);
        $totalPos += $pos+$length;
    }
    return array(substr($string, 0, $totalPos-$length),substr($string, $totalPos));
}


function splitrn($string, $needle, $offset)
{
  $rstring= strrev($string);
  
  list($rright,$rleft) = splitn($rstring, $needle, $offset);
  return array(strrev($rleft), strrev($rright));
}



####################################################
function genThumbNail($Grafikdatei, $ThumbnailBreite, $thn_Grafikdatei)
####################################################
#
# Generiert eine PNG Datei
#

{
	$ok=$True;
# $Grafikdatei = "image.jpg";
# $ThumbnailBreite = 128;

# print "Grafikdatei $Grafikdatei<br>\n ";
# print "thn_Grafikdatei $thn_Grafikdatei<br>\n ";
 try {
	 $Bilddaten = getimagesize($Grafikdatei);

	 $OriginalBreite = $Bilddaten[0];
	 $OriginalHoehe = $Bilddaten[1];
	 if($OriginalBreite < $ThumbnailBreite)
	 {
		 $ThumbnailBreite=$OriginalBreite;
	 }
	 $Skalierungsfaktor = $OriginalBreite/$ThumbnailBreite;
	 $ThumbnailHoehe = intval($OriginalHoehe/$Skalierungsfaktor);

	 if($Bilddaten[2]==1) #Kein th support f�r gif!!!
	 {
		 $Originalgrafik = ImageCreateFromGIF($Grafikdatei);

		 $Thumbnailgrafik = ImageCreate($ThumbnailBreite, $ThumbnailHoehe);

		 ImageCopyResized(	$Thumbnailgrafik,
							$Originalgrafik, 0, 0, 0, 0,
							$ThumbnailBreite,
							$ThumbnailHoehe,
							$OriginalBreite,
							$OriginalHoehe );
		 ImageGIF($Thumbnailgrafik, $thn_Grafikdatei);
	 }
	 elseif($Bilddaten[2]==2)
	 {
		 $Originalgrafik = ImageCreateFromJPEG($Grafikdatei);
		 $Thumbnailgrafik = ImageCreate($ThumbnailBreite, $ThumbnailHoehe);
		 ImageCopyResized(	$Thumbnailgrafik,
							$Originalgrafik, 0, 0, 0, 0,
							$ThumbnailBreite,
							$ThumbnailHoehe,
							$OriginalBreite,
							$OriginalHoehe);
		 ImageJPEG($Thumbnailgrafik, $thn_Grafikdatei);
	 }
	 elseif($Bilddaten[2]==3)
	 {
		 $Originalgrafik = ImageCreateFromPNG($Grafikdatei);
		 $Thumbnailgrafik = ImageCreate($ThumbnailBreite, $ThumbnailHoehe);
		 ImageCopyResized( 	$Thumbnailgrafik,
							$Originalgrafik, 0, 0, 0, 0,
							$ThumbnailBreite,
							$ThumbnailHoehe,
							$OriginalBreite,
							$OriginalHoehe );
		 ImagePNG($Thumbnailgrafik, $thn_Grafikdatei);
	 }

    } # try
    catch (Exception $e)
    {
       warn_system_down($e);
	   $ok=False;
    }


  return $ok;
} #function genThumbNail

  

function compute_sid($datasource,$remoteuser)
#############################################
#
# Computes a string out of
#
# Date and time
# datasource
# remmoteuser
#
# Returns reverse(dateandtimeascii){remoteuser}{datasource}
#
#
{

	$timestamp=date("Ymd.Hi.s"); // up till seconds
	$microsec=substr(microtime(false),2,6);
	$timestamp.=$microsec;

	return "$timestamp.$remoteuser";

} //compute_sid($datasource,$remoteuser)


function limit($string,$maxSize,$points=true)
#######################################
{
	if ($points)
	{
		$phi = $maxSize - 3;
		$punkten = (strlen($string) > $phi );
	}
	else
	{
		$phi=$maxSize;
		$punkten='';
	}
	$begrenzterText = substr($string,0,$phi);

	if ($punkten) $begrenzterText.='...'; #Anzeige dass abgeschnitten

	return 	$begrenzterText;
}

class EATV
{
	public function __construct($xpointer,$node,$follow,$attribute,$type,$value,$url='',$visible=true,$cqp=false)
    {

		$this->xpointer								=$xpointer;
		$this->node										=$node;
		$this->follow									=$follow;
		$this->attribute							=$attribute;
		$this->type										=$type;
		$this->value									=$value;
		$this->url										=$url;
		$this->visible								=$visible;
		$this->contains_query_parts		=$cqp;
		$this->toshow									=false;
	}

	public $xpointer;
	public $attribute;
	public $type;
	public $value;
	public $url;
	public $visible;
} // ATV



class SR
{
	public $searchid; // class SEARCHID
	public $result; // array of RESULT
} // SR

class SEARCHID
{
	public $sid;
	public $m;
	public $q;
	public $datasource;
} // SEARCHID

class RESULT
{
	public $xpointer;
	public $row; // array of (attribute,type,value,url,visible)
} // RESULT








function is_a_value($v)
#########################
{

	$res=false;
	if (is_numeric($v))
	{
		if ($v > 0)
			$res=true;
	}
	else
	{
		if ($v<>'')
			$res=true;
	}


	return $res;
}



function include_javascript($AJAX_INCLUDE)
#########################################
{
	$ret=<<<EOR
<script type="text/javascript" src="$AJAX_INCLUDE"></script>
EOR;
	return $ret;
}


function inject_javascript($AJAX)
#########################################
{
	$ret=<<<EOR
<script type="text/javascript">
$AJAX
</script>
EOR;
	return $ret;
}



function url_exists($url) 
{
	$cc = new cURL();
	return $cc->url_check($url);
}




function is_in_bag($wert, &$ARR, $searchinvalues=false)
#########################################
#
# Parameters: wert - the value to be checked in 
# 					  ARR  - the vector to be examined
#							x    - if true: search in values, if not: search in keys
{
	$drin=FALSE;
	if (count($ARR)>0)
	foreach ($ARR as $v=>$x)
	{
	
		if ($x)
		{
			if ($x == $wert)
			{
			$drin=TRUE;
				break;
			}
		}
		else
		if ($v == $wert)
		{
			$drin=TRUE;
			break;
		}
	}
	return $drin;
} #is_in_bag


function fri_file_get_contents($url)
{
    try {
        file_get_contents($url);
    }    
    catch (Exception $e)
    {
        inform_bad_internal_web($e);
        return '';
    }
    
}



  function get_local_file_content($URL,$filetype='txt')
  #################################
  #
  # Returns the file as a string
  #
  {
  	if (!$URL)
	{
	 //fontprint("FRI get_file_content ($URL)",'red');
	 return null;
	}
  	try
	{
	  //fontprint("FRI get_file_content ($URL)",'blue');

	 switch ($filetype)
	 {
	 	case('txt'):
	 		$filecontent = file_get_contents($URL);
	 		break;
	 	case('js'):
			 $filecontent = file_get_contents($URL);
			  
	 		break;
	 	case('xml'):
			 $xml_lines = file($URL);
			  // echo $html_lines;
			  foreach($xml_lines as $lineno=>$xml_line)
			  {
			    $str = str_replace("\n"," ",trim($xml_line));
			    $str = str_replace("  "," ",$str);
			     $filecontent.=$str;
			  }	 		
	 		break;
	 	case('php'):
	 		//echo 'PHP';
	 		$filecontent='';
			$handle = @fopen($URL, "r");
			if ($handle) {
			    while (!feof($handle)) {
			        $buffer = fgets($handle, 4096);
			        $filecontent .= $buffer;
			        //echo "<br>PHP: ".($buffer);
			    }
			    fclose($handle);
			    
			}
	 		break;
	 }
		//print "<br> get_file_content($URL,$filetype): returning ((($filecontent)))";
		return $filecontent;
	}
	catch (Exception $e)
	{
		inform_bad_external_web($e);
		return '';
	}
}

/**
 * Returns content in URL as string
 */
function get_file_content($url, $verbose=false) {
	global $AUTH_SELF_USERNAME, $AUTH_SELF_PASSWD;

	if (!$url) {
		return null;
	} else {
		try {
			return parametrizable_curl($url);
		} catch (Exception $e) {
			inform_bad_external_web($e);
		}
	}
}


/*
 * url: data source url to be retrieved
 * Author: Fabio Ricci for HEG
 */
function get_cached_widget_response_curl($url, &$parameters, $options)
{
  global $sid; // KEY for this request
  //print "get_cached_content sid: $sid";
  $cacheurl="$url?".implode('&',$parameters);
  list($timestamp,$cached_datasource_response) = (get_cached_response($cacheurl));
  
   if (! $cached_datasource_response)
   {
     //get the resonse from the data source
     //print "CACHE CONTENT EXPIRED ... CALL AGAIIN";
     $timestamp=0;
     $datasource_response= (parametrizable_curl($url, $parameters, $options));
     
     //print "Got resp: (((".htmlentities($datasource_response).")))";
     
     if (good_response($datasource_response))
     {
       //Store response
       //print "CACHING RESPONSE WITH CACHEURL=($cacheurl)"; exit;
       cache_response($cacheurl,($datasource_response));  
     } // got good response
     
   } // $cached_datasource_response
  else
    $datasource_response = $cached_datasource_response;
  return array($timestamp,$datasource_response); 
}






/*
 * url: data source url to be retrieved
 * Author: Fabio Ricci for HEG
 */
function get_cached_widget_response($url)
{
  global $sid; // KEY for this request
  //print "get_cached_content sid: $sid";
 	
   list($timestamp,$cached_datasource_response) = get_cached_response($url);
  
   if (! $cached_datasource_response)
   {
     //get the resonse from the data source
     //print "CACHE CONTENT EXPIRED ... CALL AGAIIN";
     $timestamp=0;
     $datasource_response=get_file_content($url);
     
     //print "Got resp: (((".htmlentities($datasource_response).")))";
     
     if (good_response($datasource_response))
     {
       //Store response
       cache_response($url,$datasource_response);  
     } // got good response
     
   } // $cached_datasource_response
  else
	{
		
    $datasource_response = $cached_datasource_response;
	}
  return array($timestamp,$datasource_response); 
}






function cache_response($url,$datasource_response)
// Cache response using either database or solr
{
  global $RESULTS_STORE_METHOD;
		
    switch($RESULTS_STORE_METHOD)
    {
      case 'mysql': 
            cache_response_DB($url,$datasource_response);
            break;
      case 'solr':
            cache_response_SOLR($url,$datasource_response);
    }
}  


function cache_response_DB($url,&$datasource_response)
{
  // Still needed???
}



function get_cached_response($url)
{
	   global $RESULTS_STORE_METHOD;
//	$milliseconds = number_format( round(microtime(true) * 1000), 0, '.', "'");
			
//		if (Logger::LOGGER_ACTIVATED) {
//                    $info=array();
//                    $info['name'] = "ENTRY: $milliseconds get_cached_response using $RESULTS_STORE_METHOD ";
//                    $info['msg'] = 'url= '.$url;
//                    Logger::logAction($action=25, $info);
//		}
	
    switch($RESULTS_STORE_METHOD)
    {
      case 'mysql': 
            $cached_datasource_response = get_cached_response_DB($url);
            break;
      case 'solr':
            $cached_datasource_response = get_cached_response_SOLR($url);
    }
		
//		$milliseconds = number_format( round(microtime(true) * 1000), 0, '.', "'");
//		if (Logger::LOGGER_ACTIVATED) {
//                    $info=array();
//                   $info['name'] = "EXIT:  $milliseconds get_cached_response using $RESULTS_STORE_METHOD ";
//                    $info['msg'] = 'Cachetime: '.$cached_datasource_response[0];
//                    Logger::logAction($action=25, $info); 
//		}
		
    return $cached_datasource_response;
} // get_cached_response



function get_cached_response_DB($url)
{
  return '';
}



function get_cached_response_SOLR($url)
{
//include_once("../tests/Logger.php");

  global $SOLR_RODIN_CONFIG;
  global $USER;
  global $RODINSEGMENT;
  
  $CACHED_CONTENT='';
  
  //$solr_user=$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['adapteroptions']['user'];
  $solr_host=$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['adapteroptions']['host'];
  $solr_port=$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['adapteroptions']['port'];
  $solr_path=$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['adapteroptions']['path'];
  //$solr_core=$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['adapteroptions']['core'];
  //$solr_timeout=$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['adapteroptions']['timeout'];
  $rodin_cache_expiry_hour=$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['rodin']['cache_expiring_time_hour'];

   //Query in the last $rodin_cache_expiry_hour for $url ...
  
  $TIME_RANGE=getTimeRangeExpression($rodin_cache_expiry_hour);
  
   $solr_select= "http://$solr_host:$solr_port$solr_path".'select?'
           //."user=$USER"
           ."&q=timestamp:$TIME_RANGE+user:$USER+seg:$RODINSEGMENT+id:".base64_encode($url)
           ."&fl=cached,timestamp"
           ."&rows=1"
           ."&omitHeader=true"
           ;
  
   //print "SOLR select: <a href='$solr_select' target='_blank'> solr zu url</a>";
   
   $cachecontent=file_get_contents($solr_select);
	 
//    //****************************
//    if (Logger::LOGGER_ACTIVATED) {
//                    $info=array();
//                    $info['name'] = 'get_cached_response_SOLR url= '+$url;
//                    $info['msg'] = "got cachecontent: (($cachecontent))";
//                    Logger::logAction($action=25, $info);
//    }
//    if (Logger::LOGGER_ACTIVATED) {
//                    $info=array();
//                    $info['name'] = 'get_cached_response_SOLR solr_select= '+$solr_select;
//                    $info['msg'] = "got cachecontent: (($cachecontent))";
//                    Logger::logAction($action=25, $info);
//    }
//    //****************************

    
   
   $solr_sxml= simplexml_load_string($cachecontent);
//    print "<hr>SOLR_QUERY: <a href='$solr_select' target='_blank'>$solr_select</a><br>";
//      print "<hr>FILE_CONTENT: <br>(((".htmlentities($cachecontent).")))";
//      print "<hr>SOLR_CONTENT: <br>(((".htmlentities($solr_sxml)."))) "; var_dump($solr_sxml); exit;
//      print "<hr>SOLR_RESULT: <br>"; var_dump($solr_sxml);
			//$xpath = "/response/lst/lst/lst[@name='cached']"; // /itas (old)
   
   //Is there a response? how many found elements?
    $FOUND_RES = $solr_sxml->xpath("/response/result"); //find the doc list
//    print "<br>FOUND_RES: "; var_dump($FOUND_RES); exit;
    $FOUND_RES = $FOUND_RES[0];
    $ATTR = $FOUND_RES->attributes();
    $NO_OF_RESULTS= $ATTR['numFound'];
    //print "<hr>Found results: $NO_OF_RESULTS";

    if ($NO_OF_RESULTS > 0)
    {
      $TIME_A = $solr_sxml->xpath("/response/result/doc/date[@name='timestamp']"); //find the doc list
      $TIME = $TIME_A[0];
      //print "<br>TIME_A: "; var_dump($TIME_A); 
      //print "<br>TIME: ".htmlentities($TIME); 

      $CACHED_A = $solr_sxml->xpath("/response/result/doc/arr[@name='cached']/str"); //find the doc list
      $CACHED_CONTENT = $CACHED_A[0];
      //print "<br>CACHED_CONTENT: ".($CACHED_CONTENT); exit;
    }
    else {
    	$TIME=0;
    } 
   
    return array($TIME,$CACHED_CONTENT); // null erstemal
  
} // get_cached_response_SOLR



function cache_response_SOLR($cacheid,$response)
{
  require_once("../u/SOLRinterface/solr_interface.php");
  global $SOLR_RODIN_CONFIG;
  global $SOLARIUMDIR;
  global $USER;
  global $RODINSEGMENT;
  //$solr_user=$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['adapteroptions']['user'];
  $solr_host=$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['adapteroptions']['host'];
  $solr_port=$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['adapteroptions']['port'];
  $solr_path=$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['adapteroptions']['path'];
  $solr_core=$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['adapteroptions']['core'];
  $solr_timeout=$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['adapteroptions']['timeout'];
  //$rodin_cache_expiry_hour=$SOLR_RODIN_CONFIG['cached_rodin_widget_response']['rodin']['cache_expiring_time_hour'];
  // store in SOLR for the last stored response using $url
   
   if (($client=solr_client_init($solr_host,$solr_port,$solr_path,$solr_core,$solr_timeout)))
   {
      // create a new document for the data
      $caching_doc = new Solarium_Document_ReadWrite();

      $caching_doc->id         = base64_encode($cacheid);
      $caching_doc->cacheid    = $cacheid;
      $caching_doc->seg        = $RODINSEGMENT;
      $caching_doc->user       = $USER;
      $caching_doc->cached     = ($response);

      #do NOT reverse index this cached data in body
      $documents= array($caching_doc);
      
      solr_synch_update($sid='',$solr_path,$client,$documents,false,false);
      
    }
    else {
      print "cache_response_SOLR system error init SOLR client";
    }
}



function good_response($datasource_response)
// returns false if some network errors could be scanned in the response
// else returns true
{
  return true; // short up
} // good response





/**
 * Checks the permissions on a filepath
 * starting from the filename up to root
 * and gives a print of the permissions found.
 * @author Fabio Ricci
 */

function filesystempermissionanalyse($path)
{
  if ($path=='')   ; // nix
  else
  {
    if (!file_exists($path))
    {
      print "<br><b>Does not exist</b> $path";
      //filesystempermissionanalyse($lowerdir);
    }
    else
    {  
      $info = read_permissions($path);

      print "<br>$path <b>$info</b>";
      
    }
    //switch analyse recursively down
    $path_parts=pathinfo($path);

    if ($path<>'/')
    {
      $lowerdir=$path_parts['dirname'];
      //print "<br> TRY WITH LOWER ONE: $lowerdir";
      filesystempermissionanalyse($lowerdir);
    }
  }
}





function read_permissions($path)
{
    $perms = fileperms($path);

    if (($perms & 0xC000) == 0xC000) {
        // Socket
        $info = 's';
    } elseif (($perms & 0xA000) == 0xA000) {
        // Symbolic Link
        $info = 'l';
    } elseif (($perms & 0x8000) == 0x8000) {
        // Regular
        $info = '-';
    } elseif (($perms & 0x6000) == 0x6000) {
        // Block special
        $info = 'b';
    } elseif (($perms & 0x4000) == 0x4000) {
        // Directory
        $info = 'd';
    } elseif (($perms & 0x2000) == 0x2000) {
        // Character special
        $info = 'c';
    } elseif (($perms & 0x1000) == 0x1000) {
        // FIFO pipe
        $info = 'p';
    } else {
        // Unknown
        $info = 'u';
    }
    // Owner
    $info .= (($perms & 0x0100) ? 'r' : '-');
    $info .= (($perms & 0x0080) ? 'w' : '-');
    $info .= (($perms & 0x0040) ?
                (($perms & 0x0800) ? 's' : 'x' ) :
                (($perms & 0x0800) ? 'S' : '-'));

    // Group
    $info .= (($perms & 0x0020) ? 'r' : '-');
    $info .= (($perms & 0x0010) ? 'w' : '-');
    $info .= (($perms & 0x0008) ?
                (($perms & 0x0400) ? 's' : 'x' ) :
                (($perms & 0x0400) ? 'S' : '-'));

    // World
    $info .= (($perms & 0x0004) ? 'r' : '-');
    $info .= (($perms & 0x0002) ? 'w' : '-');
    $info .= (($perms & 0x0001) ?
                (($perms & 0x0200) ? 't' : 'x' ) :
                (($perms & 0x0200) ? 'T' : '-'));

    return $info;
}





function getTimeRangeExpression($rodin_cache_expiry_hour)
{
  //; [2012-12-02T22:15:00Z+TO+NOW]
  
  $oldest_allowed  = mktime(date("H")-$rodin_cache_expiry_hour+1, 0, 0, date("m"), date("d"),   date("Y"));
  list($DAY,$MONTH,$YEAR,$HOUR,$MIN,$SEC)=explode('.',date("d.m.Y.H.i.s",$oldest_allowed));
  
  $SOLR_TIME_RANGE="[$YEAR-$MONTH-{$DAY}T{$HOUR}:$MIN:{$SEC}Z+TO+NOW]";
  
  return $SOLR_TIME_RANGE;
}




class fri_rss_reader
#
# Usage: $rss = new fri_rss_reader(url,3)
#
# $rss->n
# $rss->channel
# $rss->item
#
{
  var $channel;
  var $items;
  var $n;

  function fri_rss_reader($feed,$max=10,$debug=0)
  {	
    $rss = file_get_contents( $feed );
    $sx_rss = simplexml_load_string($rss);		
    if (!$sx_rss) {
        echo "<br>Problem loading XML ($rss) (exit)\n";

        foreach(libxml_get_errors() as $error) 
        {
            echo "\t", $error->message;
        }
        exit;
    }
      //echo "Channel Title: " . $rss->channel['title'] . "<p>";
      $namespaces = $sx_rss->getDocNamespaces(true);
    $namespaces[]=''; // even the empty space (for fulltext items)

    $cc=$sx_rss->children();
    foreach($cc as $a=>$v)
    {
      if ($a=='channel')	
      {
        //print "BINGO CHANNEL";
        $this->channel=$v[0];
        break;
      }
    }

    $this->items=array();

    $i=-1;
    foreach($this->channel->children() as $a=>$v)
    {
      //print "<br> add to item";
      //print "<br> item name: $a";
      if ($a=='item')
      {
        $i++;
        if ($i >= $max && 0) {break;}
        else
        {
          //print "<br>ITEM";
          $item_attributes = array();
          foreach($v[0]->children() as $attrname=>$attrvalue)
          {
            $item_attributes{$attrname}=$attrvalue;
            //print "<br>local item $attr=>$val";
          }		
          $this->items[]=$item_attributes;
        }
      }

    }		

    $this->n=count($this->items);
  }

} // class fri_rss_reader



	
	
	

/*
Use it this way:

<?php
// call constructor
$obj = new RemoteFopenViaProxy($insert_request_url, $insert_proxy_name, $insert_proxy_port);
// change settings after object generation
$obj->set_proxy_name($insert_proxy_name);
$obj->set_proxy_port($insert_proxy_port);
$obj->set_request_url($insert_request_url);
$obj->request_via_proxy();
echo $obj->get_result();
*/


class cURL 
{
	var $headers;
	var $user_agent;
	var $compression;
	var $cookie_file;
	var $proxy;
	
	
	function cURL($cookies=FALSE,$cookie='cookies.txt',$compression='gzip',$proxy=true) 
	{
		global $PROXY_NAME;
		$proxy = ($PROXY_NAME != '');
	
		$this->headers[] = 'Accept: image/gif, image/x-bitmap, image/jpeg, image/pjpeg, */*';		
		$this->headers[] = 'Connection: Keep-Alive';
		$this->headers[] = 'Content-type: application/x-www-form-urlencoded;charset=UTF-8';
// 		$this->user_agent = 'Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.2; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)';
// 		$this->user_agent = 'Gecko/20091102 Firefox/3.5.5';
		$this->user_agent = 'Mozilla/5.0 (Macintosh; U; Intel Mac OS X 10.5; de; rv:1.9.1.5) Gecko/20091102 Firefox/3.5.5';
	
		$this->compression=$compression;
		$this->proxy=$proxy;
		$this->cookies=$cookies;
		if ($this->cookies == TRUE) $this->cookie($cookie);
	}
	
	
	function cookie($cookie_file) 
	{
	
		global $CURL_COOCKIEDIR;
		if (file_exists("$CURL_COOCKIEDIR/$cookie_file")) 
		{
			$this->cookie_file="$CURL_COOCKIEDIR/$cookie_file";
		} 
		else 
		{
			$f = fopen("$CURL_COOCKIEDIR/$cookie_file",'w') or $this->error('The cookie file could not be opened. Make sure this directory has the correct permissions');
			$this->cookie_file=$cookie_file;
			fclose($f);
		}
	}
	
	
	function get_authorized($url,$timeout_sec=15,$AUTH='nobody',$PASS='')
	##########################################
	#
	# Use get with simple basic authentication
	#
	{
		global $AUTH_SELF_USERNAME, $AUTH_SELF_PASSWD;
		
		$OLD_AUTH_SELF_USERNAME=$AUTH_SELF_USERNAME;
		$OLD_AUTH_SELF_PASSWD=$AUTH_SELF_PASSWD;
		
		$AUTH_SELF_USERNAME=$AUTH;
		$AUTH_SELF_PASSWD=$PASS;
		$output=$this->get($url,$timeout_sec);

		$AUTH_SELF_USERNAME=$OLD_AUTH_SELF_USERNAME;
		$AUTH_SELF_PASSWD=$OLD_AUTH_SELF_PASSWD;
		return $output;
	}
	
	
	function get($url,$timeout_sec=15,$referer='') 
	{
	
		global $PROXY_NAME;
		global $PROXY_PORT;
		global $PROXY_AUTH_USERNAME;
		global $PROXY_AUTH_PASSWD;
		global $PROXY_AUTH_TYPE;
		global $AUTH_SELF_USERNAME; //auth by a website
		global $AUTH_SELF_PASSWD;
		if (0) print "<br><b>get:url=($url)</b> <br>PROXY_NAME=$PROXY_NAME<br>PROXY_PORT=$PROXY_PORT<br>PROXY_AUTH_USERNAME=$PROXY_AUTH_USERNAME<br>PROXY_AUTH_PASSWD=$PROXY_AUTH_PASSWD<br>AUTH_SELF_USERNAME=$AUTH_SELF_USERNAME<br>AUTH_SELF_PASSWD=$AUTH_SELF_PASSWD<br>PROXY:".$this->proxy;
		
		$process = curl_init($url);
		$errno=curl_errno($process);
			
		if($errno)
		{	
				$return = '<br>Curl Fehler: (' . curl_error($process) . ')<hr>';
				curl_close($process);
					
				if (0)
						 fontprint( "<br>curl ERROR returning: $return",'red');
				return $return	;
		}
		
			curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
			//curl_setopt($process, CURLOPT_HEADER, 1); // want to see headers? not for every Widget
			curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
			if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
			if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
			curl_setopt($process, CURLOPT_VERBOSE, TRUE);  			
			curl_setopt($process,CURLOPT_ENCODING , $this->compression);
			curl_setopt($process, CURLOPT_TIMEOUT, $timeout_sec);
			
			if (($AUTH_SELF_USERNAME))
			{
				if (0) print "<br> <b>using local auth!!</b> using ($AUTH_SELF_USERNAME) for $url";
				curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
				curl_setopt($process, CURLOPT_USERPWD, "$AUTH_SELF_USERNAME:$AUTH_SELF_PASSWD");
			}
			
			//if ($this->proxy) curl_setopt($process, CURLOPT_PROXY, "proxy_ip:proxy_port");
			if ($this->proxy) 
			{	
			  //print "<br>Using CURL PROXY Values...<br>";
				curl_setopt($process, CURLOPT_PROXY, "$PROXY_NAME:$PROXY_PORT");
				curl_setopt($process, CURLOPT_PROXYTYPE, "CURLPROXY_HTTP");
				//curl_setopt($process, CURLOPT_PROXYPORT, $PROXY_PORT); // Already set in CURLOPT_PROXY
				curl_setopt($process, CURLOPT_HTTPAUTH, 	"CURLAUTH_".strtoupper($PROXY_AUTH_TYPE));
				curl_setopt($process, CURLOPT_PROXYUSERPWD, "$PROXY_AUTH_USERNAME:$PROXY_AUTH_PASSWD");
			}
			
			if ($referer<>'') curl_setopt($process, CURLOPT_REFERER, $referer);
			curl_setopt($process, CURLOPT_AUTOREFERER, 1);
			curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($process, CURLOPT_MAXREDIRS, 20);
			curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
			$return = curl_exec($process);
			$errno=curl_errno($process);
			if (0) print "<br>ERRNO (((".$errno.")))";
			if (0) print "<br>RETURN (((".$return.")))";
			
			if(curl_errno($process))
			{
		    	$return = '<br>Curl Fehler: ' . curl_error($process) . '<hr>';
					if (0)
			    	fontprint( "<br>curl ERROR returning: $return",'red');
			    	
			    curl_close($process);
			}
			return $return;	
			
	} // get
	
		
	function url_check($url) 
	{
	
		global $PROXY_NAME;
		global $PROXY_PORT;
		global $PROXY_AUTH_USERNAME;
	  global $PROXY_AUTH_PASSWD;
		global $PROXY_AUTH_TYPE;
		global $AUTH_SELF_USERNAME; //auth by a website
	  global $AUTH_SELF_PASSWD;
		//print "get:url=($url) <br>PROXY_NAME=$PROXY_NAME<br>PROXY_PORT=$PROXY_PORT<br>PROXY_AUTH_USERNAME=$PROXY_AUTH_USERNAME<br>PROXY_AUTH_PASSWD=$PROXY_AUTH_PASSWD<br>AUTH_SELF_USERNAME=$AUTH_SELF_USERNAME<br>AUTH_SELF_PASSWD=$AUTH_SELF_PASSWD<br>proxy:".$this->proxy;
    // Version 4.x supported
    $process = curl_init($url);
		if (false === $process)
    {
        return false;
    }
		else 
		{
			curl_setopt($process, CURLOPT_HEADER, false);
			curl_setopt($process, CURLOPT_FAILONERROR, true);  // this works
			curl_setopt($process, CURLOPT_HTTPHEADER, Array("User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.15) Gecko/20080623 Firefox/2.0.0.15") ); // request as if Firefox   
			curl_setopt($process, CURLOPT_NOBODY, true);
			curl_setopt($process, CURLOPT_RETURNTRANSFER, false);
		
			curl_setopt($process, CURLOPT_TIMEOUT, 15);
			
			if (isset($AUTH_SELF_USERNAME))
			{
				//print "<br> <b>using local auth!!</b> using $userpwd for $url";
				curl_setopt($process, CURLOPT_HTTPAUTH, CURLAUTH_DIGEST);
				curl_setopt($process, CURLOPT_USERPWD, "$AUTH_SELF_USERNAME:$AUTH_SELF_PASSWD");
			}
			
			//if ($this->proxy) curl_setopt($process, CURLOPT_PROXY, �proxy_ip:proxy_port�);
			if ($this->proxy) 
			{	
				//print "<br>Using CURL PROXY Values...<br>";
				curl_setopt($process, CURLOPT_PROXY, "$PROXY_NAME:$PROXY_PORT");
				curl_setopt($process, CURLOPT_PROXYTYPE, "CURLPROXY_HTTP");
				curl_setopt($process, CURLOPT_PROXYPORT, $PROXY_PORT);
				curl_setopt($process, CURLOPT_HTTPAUTH, 	"CURLAUTH_".strtoupper($PROXY_AUTH_TYPE));
				curl_setopt($process, CURLOPT_PROXYUSERPWD, "$PROXY_AUTH_USERNAME:$PROXY_AUTH_PASSWD");
			}
			
			$connectable = curl_exec($process);
			curl_close($process);  
			return $connectable;
		}
	
	} // url_check
	
	
	function post($url,$data,$referer='') 
	{
		global $PROXY_NAME;
		global $PROXY_PORT;
		global $PROXY_AUTH_USERNAME;
	  global $PROXY_AUTH_PASSWD;
		global $PROXY_AUTH_TYPE;
	
		$process = curl_init($url);
			
		curl_setopt($process, CURLOPT_HTTPHEADER, $this->headers);
		//curl_setopt($process, CURLOPT_HEADER, 1);
		curl_setopt($process, CURLOPT_USERAGENT, $this->user_agent);
		if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEFILE, $this->cookie_file);
		if ($this->cookies == TRUE) curl_setopt($process, CURLOPT_COOKIEJAR, $this->cookie_file);
		curl_setopt($process, CURLOPT_ENCODING , $this->compression);
		curl_setopt($process, CURLOPT_TIMEOUT, 15);
		if ($this->proxy) 
		{
			curl_setopt($process, CURLOPT_PROXY, "$PROXY_NAME:$PROXY_PORT");
			curl_setopt($process, CURLOPT_PROXYTYPE, "CURLPROXY_HTTP");
			curl_setopt($process, CURLOPT_PROXYPORT, $PROXY_PORT);
			curl_setopt($process, CURLOPT_HTTPAUTH, 	"CURLAUTH_".strtoupper($PROXY_AUTH_TYPE));
			curl_setopt($process, CURLOPT_PROXYUSERPWD, "$PROXY_AUTH_USERNAME:$PROXY_AUTH_PASSWD");
		}
		
		if ($referer) curl_setopt($process, CURLOPT_REFERER, $referer);
			
		curl_setopt($process, CURLOPT_AUTOREFERER, 1);
		curl_setopt($process, CURLOPT_POSTFIELDS, $data);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($process, CURLOPT_MAXREDIRS, 20);
		curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($process, CURLOPT_POST, 1);
		$return = curl_exec($process);
		curl_close($process);
		return $return;
	} // post
	
	
	
	function error($error) 
	{
		echo "<center><div style='width:500px;border: 3px solid #FFEEFF; padding: 3px; background-color: #FFDDFF;font-family: verdana; font-size: 10px'><b>cURL Error</b><br>$error</div></center>";
		die;
	}
} // class cURL
	

/**
 * Performs the XSL transformation of a XML string using a local XSL file.
 * @param String $xmlString the XML string.
 * @param String $xslFilePath the path to the local XSL file.
 */
function xsl_local_transform($xmlString, $xslFilePath) {
	// load XML file
	$originalXML = new DOMDocument();
	$originalXML->loadXML($xmlString);

	// load XSL file
	$xslStylesheet = new DOMDocument();
	$xslStylesheet->load($xslFilePath, LIBXML_NOCDATA);
	
	// perform XSLT transformation
	$xsltProcessor = new XSLTProcessor();
	$xsltProcessor->importStylesheet($xslStylesheet);
		
	$transformed_xml = $xsltProcessor->transformToXML($originalXML);
		
	return $transformed_xml;
}




function adapt_widgetsareas_on_openclose_widgetmenu()
#############################################
{

	$JAVASCRIPT =<<<EOS
	 <script type='text/javascript'>
	 	adapt_widgetsareas_on_openclose_widgetmenu();
EOS;
	$JAVASCRIPT.=" </script>";

	print $JAVASCRIPT;
}






function make_ontoterm_javascript_code($txt)
#############################################
{
	/*
   * Perform some closing actions
   * informing about the state of meta search
   * and start filtering highlighting
   */
	$CODE =<<<EOS
	 <script type='text/javascript'>
    parent.ONTOTERMS_REDO_HIGHLIGHTING=true;
    parent.mark_ontoterms_on_resultmatch();
	 </script>
EOS;

	return $CODE;
}



function make_uncache_javascript_code($txt)
#############################################
{
	$sid = $_REQUEST['sid'];
	$uid = substr(strrchr($sid, '.'), 1);

  /*
   * Perform some closing actions
   * informing about the state of meta search
   * and start filtering highlighting
   */
	$UNCACHE =<<<EOS
	 <script type='text/javascript'>
	  parent.show_widgets_content_in_aggregated_view();
	 	parent.adapt_widgetsareas_on_openclose_widgetmenu();
	 	parent.FRIdarkProtectionUncache('$txt');
	 	parent.refreshCloudBoard('$uid');
	 </script>
EOS;

	return $UNCACHE;
}


function sxml_get_toptagname($sxml)
{
	return $sxml->getName();
}

function sxml_get_secondtagname($sxml)
{
	$ELEs= $sxml->children();
	$resulttagname=$ELEs[0]->getName();
	return $resulttagname;
}

  function extract_code($xmltag)
	#####################################
	{
	  /* <ns11:subfield code="B">
           <![CDATA[
10010
           ]]>
		*/
		$code='?';
		$PATTERN="/:subfield\scode=\"(.)\"/";
		if (preg_match($PATTERN,$xmltag,$match))
		{
				$code=$match[1];
				//print "<br>? CODE= $code";
		}
		//else print "<br> NO MATCH";
		return $code;
	}


	function get_RODINIMAGE($RODINSEG)
	{
		global $RODINIMAGES,$RODINIMAGESWEB;
		
		$BG_IMAGE_DISK= "$RODINIMAGES/rodin_bg_{$RODINSEG}.png";
		
		if (!file_exists($BG_IMAGE_DISK))
		{
			print "<br> not exists: $BG_IMAGE";
			$RODINSEG='eng';
		}
		$BG_IMAGE= $RODINIMAGESWEB."/rodin_bg_{$RODINSEG}.png";
		
		//print "<br> returning image $BG_IMAGE";
		return $BG_IMAGE;
	}

	
	$BG_IMAGE= get_RODINIMAGE($RODINSEGMENT);
	
	
	
	
	
	function get_xml_params($xmltag)
	#####################################
	#
	# Out of <tag type="intern" institute="MCM" id="1406">tagvalue</tag>
	# returns a vector:
	#
	# v['name'] = Name of the tag
	# v['value'] = value of the tag
	# v['x'] = value of the tag parameter x
	# v['y'] = value of the tag parameter y
	# ...
	#
	{
		$xmltagparam=array();
		$xmltagname=trim(substr($xmltag,1,strpos($xmltag,' ')));
		$PATTERN="/<".$xmltagname."\s(.*)>(.*)<\/".$xmltagname.">/";
		//print "\n<hr />Pattern= ($PATTERN) match: params of ($xmltagname):<br />";
		$res = preg_match($PATTERN,$xmltag,$match);


		if ($res)
		{
			//foreach($match as $m) print "<br /> match $m";
			$xmltagvalue=$match[0];
			$arr=explode(' ',$match[1]);
			foreach($arr as $ele)
			{
				list($paramname,$paramvalue) = explode('=',$ele);
				//print "<br /> setting xmltagparam[$paramname]=$paramvalue";
				$xmltagparam[$paramname]=str_replace(">","",str_replace('"','',$paramvalue)); // dirty ">" cancel
			}
			$xmltagparam['value']=str_replace(">","",$xmltagvalue);
			$xmltagparam['name']=str_replace(">","",$xmltagname);

		}
		//else print "no";

		//print "<br />end match";
		if (0)
		{
		print "\n<br />xmltagparam[name]: ".$xmltagparam['name'];
		print "\n<br />xmltagparam[value]:".$xmltagparam['value'];
		print "\n<br />xmltagparam[type]:".$xmltagparam['type'];
		print "\n<br />xmltagparam[institute]:".$xmltagparam['institute'];
		}

		return $xmltagparam;
	}



	
	
function html_printable_xml($txt)
{
	$txt=str_replace('<',"&lt;",$txt);
	$txt=str_replace('>',"&gt;",$txt);
	return $txt;
}
	
/**
 * Utility function making a CURL access to a web service. The parameters
 * are passed through a GET call but a POST call is possible using the options
 * parameter.
 * 
 * FRI: Optimisation: call the service directly if service is directly on the server
 *
 * @param String $url the base URL of the service.
 * @param array $get the parameters sent to the service.
 * @param array $options additional CURL options.
 */
function parametrizable_curl($url, array $get = array(), array $options = array(CURLOPT_HTTPHEADER => array('Accept:text/xml'))) {
	global $PROXY_NAME, $PROXY_PORT, $PROXY_AUTH_USERNAME, $PROXY_AUTH_PASSWD;
	global $CALLING_TIMEOUT_SEC, $WEBROOT;
	
	$defaults = array(
		CURLOPT_HTTPHEADER => false, // e.g. array('Accept:text/xml')
		CURLOPT_RETURNTRANSFER => TRUE,
		CURLOPT_TIMEOUT => $CALLING_TIMEOUT_SEC
		
		// Options for POST queries
		// CURLOPT_POST => true,
		// CURLOPT_POSTFIELDS => "user=1&service=2"
		
		// Another options include
		// CURLOPT_CONNECTTIMEOUT => 5
	);
	
  
  //We suppose we are always in GET mode (url is complete)
  list($path,$qsparams)=service_is_a_php_on_this_server($url);
  if ($path) // Path was found -> open service directly
  $result = open_direct_php($path,$qsparams,$options);
  else // use curl 
  {
    // Use the proxy configuration only if the URL is remote (not local)
    // and we're not running it directly on the server
    $urlIsRemote = stripos($url, $WEBROOT) === false;

    if ($PROXY_NAME != '' && $urlIsRemote) {
      $defaults[CURLOPT_HTTPPROXYTUNNEL] = "TRUE";
      $defaults[CURLOPT_PROXYTYPE] = "CURLPROXY_HTTP";
      $defaults[CURLOPT_PROXY] = "$PROXY_NAME:$PROXY_PORT";
      $defaults[CURLOPT_PROXYUSERPWD] = "$PROXY_AUTH_USERNAME:$PROXY_AUTH_PASSWD";
    }

    $finalOptions = $options + $defaults;

    if (count($get) > 0) {
      $url .= (strpos($url, '?') === FALSE ? '?' : '') . http_build_query($get);
    }

    $ch = curl_init($url);
    curl_setopt_array($ch, $finalOptions);

    $result = @curl_exec($ch);

  //	if( ! $result = curl_exec($ch)) {
  //		trigger_error(curl_error($ch));
  //	}

    curl_close($ch);
  }
	return $result;
}




/* Returns include path of the service if found */
function service_is_a_php_on_this_server($url)
{
//  print "<br>service_is_a_php_on_this_server($url)<br>";
  global $HOST;
  global $PORT;
  global $DOCROOT;
  
  $FILEPATH=null;
  
  if ($PORT<>'') 
    $PORTEXPR=":$PORT";
  
  $SERVEREXPR="http://$HOST$PORTEXPR";
    
//  print "<br>Checking Server params";
//  print "<br>HOST: $HOST";
//  print "<br>PORT: $PORT";
//  print "<br>DOCROOT: $DOCROOT";
//  print "<br>SERVEREXPR: $SERVEREXPR";
//  print "<br>strstr($url,$SERVEREXPR)=".strstr($url,$SERVEREXPR);

  if (strstr($url,$SERVEREXPR))
  {
    // Try to identify if path is on the same machine
    
    $FILEPATH=str_replace($SERVEREXPR,$DOCROOT,$url);
    // cut str from '?' inclusive on
    $pos = strrpos($FILEPATH, '?');
    if (!($pos === false)) {
      $QSPARAMS=substr($FILEPATH,$pos+1); // stuff rights from '?'
      $FILEPATH=substr($FILEPATH,0,$pos-1); // stuff left of '?'
    }  
    
//    print "<br><br>Try to test existence of file ($FILEPATH)";
    
    if (file_exists($FILEPATH))
    {
//      print " <b>SUCCESS</b> ($FILEPATH) ";
    }
    else {
//      print " NOTFOUND ($FILEPATH) ";
      $FILEPATH='';
  } }
  
//  print "<br>Returning path: $FILEPATH";
//  print "<br>Returning qsparams: $QSPARAMS";
  
  return array($FILEPATH,$QSPARAMS);
}




/*
 * Include the file and start it directly here
 */
function open_direct_php($path,$qsparams,&$options)
{
  //global root vars because we are inside a function
  //and will will include a prog.
  // The following vars are needed from the constructor of the SRCengine and subclasses!!!!!
  // or globals used by protected methods inside these engines...
  global $RODINROOT;
  global $RODINSEGMENT;
  global $TERM_SEPARATOR;
  global $SPARQL_LIMIT_RESULTS;
  global $DEFAULT_MAX_REFINE_RESULTS;
  global $VERBOSE;
  global $SRCDEBUG;
  global $ARCCONFIG;
  global $DBPEDIA_BASE;
  global $DBPEDIA_PREFIX;
  global $WIKIPEDIASEARCH;
  global $DBPEDIA_SPARQL_ENDPOINT;
  
//  global $CAN_ACCESS_ADMIN_VAR;
//  global $MAX_DB_RETRIES, $USLEEP_RETRY;
//	global $verbose, $RODINROOT, $RODINSEGMENT;
//	global $ADMINDBBASENAME, $RODINADMIN_HOST, $RODINADMIN_DBNAME, $RODINADMIN_USERNAME, $RODINADMIN_USERPASS;
//  global $ARCDB_DBNAME,	$ARCDB_USERNAME, $ARCDB_USERPASS, $ARCDB_DBHOST;	
//	global $SRCDB_DBNAME,	$SRCDB_USERNAME, $SRCDB_USERPASS, $SRCDB_DBHOST;	
		
//  print "<br>open_direct_php path: $path";
//  print "<br>open_direct_php qsparams: $qsparams";
  
  //set params as tough these were the right params:
  
  $QS=explode('&',$qsparams);
  $_REQUEST = array(); // init REQUEST!!!
  
  foreach ($QS as $pair) {

    //Check number of occurrences of '=' in $pair
    //if more than one, take the first = as split
    //this can be a base64 value
    $ssc= substr_count($pair,'=');
    if ($ssc>1)
    {
      $key=substr($pair,0,strpos($pair,'='));
      $value=substr($pair,strpos($pair,'=')+1);
      //print "<br>SPECIAL: $key $value";
    }
    else
      list($key,$value) = explode('=',$pair);
  
    $_REQUEST{$key} = $value;
    if ($key=='user') $userfound=true;
  }
  if (!$userfound) { 
    global $user;
    $_REQUEST{'user'} = $user;
  }
  $_REQUEST{'directloading'} = true; // signalize the prog is loaded directly
  
  $CLASSNAME = basename(dirname(($path))); // 
  $_REQUEST{'CLASSNAME'} = $CLASSNAME;
 
  
  require_once $path.'/index.php';
  
  //The program must prepare an $OUTPUT to be returned
  
  return $OUTPUT;
}


#################################################
# $host: hostname ; eg 'example.org'
# $path: request' eg '/index.php?id=123'
# $data_to_send : data to POST after the HTTP header.
#
# if $opts is an  empty array() a standard  HTTP to port 80 request is performed.
#
# set auth['type']='basic' to use plain-text auth,
# digest-auth will be handled automatically if $auth['username'] is set and a 401
# status is encountered. - use auth['type']='nodigest' to override.
#
##
function httpPost($host, $path, $data_to_send,
                  $opts=array('cert'=>"", 'headers'=>0, 'transport' =>'ssl', 'port'=>443),
                  $auth=array('username'=>"", 'password'=>"", 'type'=>"")
                 ) 
#######################################
								 
{
  $transport=''; $port=80;
  if (!empty($opts['transport'])) $transport=$opts['transport'];
  if (!empty($opts['port'])) $port=$opts['port'];
  
	if (empty($opts['transport']))
		
		$remote = "$host:$port";

	else
		$remote=$transport.'://'.$host.':'.$port;

  $context = stream_context_create();
  $result = stream_context_set_option($context, 'ssl', 'verify_host', true);
  if (!empty($opts['cert'])) {
    $result = stream_context_set_option($context, 'ssl', 'cafile', $opts['cert']);
    $result = stream_context_set_option($context, 'ssl', 'verify_peer', true);
  } else {
    $result = stream_context_set_option($context, 'ssl', 'allow_self_signed', true);
  }
  $fp = stream_socket_client($remote, $err, $errstr, 60, STREAM_CLIENT_CONNECT, $context);

  if (!$fp) {
    trigger_error('httpPost error: '.$errstr);
    return NULL;
  }

  $req='';
  $req.="POST $path HTTP/1.1\r\n";
  $req.="Host: $host\r\n";
  if ($auth['type']=='basic' && !empty($auth['username'])) {
    $req.="Authorization: Basic ";
    $req.=base64_encode($auth['username'].':'.$auth['password'])."\r\n";
  }
  elseif ($auth['type']=='digest' && !empty($auth['username'])) {
    $req.='Authorization: Digest ';
    foreach ($auth as $k => $v) {
      if (empty($k) || empty($v)) continue;
      if ($k=='password') continue;
      $req.=$k.'="'.$v.'", ';
    }
    $req.="\r\n";
  }
  $req.="Content-type: text/xml\r\n";
  $req.='Content-length: '. strlen($data_to_send) ."\r\n";
  $req.="Connection: close\r\n\r\n";

  fputs($fp, $req);
  fputs($fp, $data_to_send);

  while(!feof($fp)) { $res .= fgets($fp, 128); }
  fclose($fp);

  if ($auth['type']!='nodigest'
        && !empty($auth['username'])
        && $auth['type']!='digest' # prev. digest AUTH failed.
        && preg_match("/^HTTP\/[0-9\.]* 401 /", $res)) {
    if (1 == preg_match("/WWW-Authenticate: Digest ([^\n\r]*)\r\n/Us", $res, $matches)) {
      foreach (explode(",", $matches[1]) as $i) {
        $ii=explode("=",trim($i),2);
        if (!empty($ii[1]) && !empty($ii[0])) {
          $auth[$ii[0]]=preg_replace("/^\"/",'', preg_replace("/\"$/",'', $ii[1]));
        }
      }
      $auth['type']='digest';
      $auth['uri']='https://'.$host.$path;
      $auth['cnonce']=randomNonce();
      $auth['nc']=1;
      $a1=md5($auth['username'].':'.$auth['realm'].':'.$auth['password']);
      $a2=md5('POST'.':'.$auth['uri']);
      $auth['response']=md5($a1.':'
                           .$auth['nonce'].':'.$auth['nc'].':'
                           .$auth['cnonce'].':'.$auth['qop'].':'.$a2);
      return httpPost($host, $path, $data_to_send, $opts, $auth);
    }
  }

  if (1 != preg_match("/^HTTP\/[0-9\.]* ([0-9]{3}) ([^\r\n]*)/", $res, $matches)) {
    trigger_error('httpPost: invalid HTTP reply.');
    return NULL;
  }

  if ($matches[1] != '200') {
    trigger_error('httpPost: HTTP error: '.$matches[1].' '.$matches[2]);
    return NULL;
  }

  if (!$opts['headers']) {
    $res=preg_replace("/^.*\r\n\r\n/Us",'',$res);
  }
  return $res;
}





function randomNonce($len=0) {
	########################################
  $chars = "ABCDEFGHIJKMNOPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz023456789";
  $i=0; $rv='';
  if ($len < 1) $len= (6+rand()%10);
  while ($i++ < $len) {
    $rv.=$chars[rand() % strlen($chars)];
  }
  return $rv;
}



function get_referer()
{
	$referer = $_SERVER['HTTP_REFERER'];
	if (preg_match("/(.+)\/(.+).php/",$referer,$match))
		$refererx=$match[2];
	return $refererx;	
}


/*
	$DIV_BROADER=generate_div_refinement('b','Broader','#FFCC99','Broader Broader Broader Broader');
	$DIV_NARROVER=generate_div_refinement('n','Narrover','#CCFFCC','Narrower Narrower Narrower Narrower');
	$DIV_RELATED=generate_div_refinement('r','Related','white','Related Related Related Related');
*/	


$COLOR_BROADER='#FFCC99';
$COLOR_NARROWER='#CCFFCC';
$COLOR_RELATED='#6699FF';
$NUMBER_OF_COLS_EACH_FIELD=50;
$NUMBER_OF_ROWS_EACH_FIELD=5; //n+1 displayed
$NUMBER_OF_COLS_EACH_FIELD_RES=65;
$NUMBER_OF_ROWS_EACH_FIELD_RES=2; //n+1 displayed

$RODIN_LOGO_YELLOW="#FFE68C";

//Siehe auch rodin.css f�r die span(s)!!!
$FONTPREFS_TD_QUERY="font-size: large;font-weight:bold;font-family:arial";
$FONTPREFS_TD="font-size: large;font-weight:normal;font-family:arial";
$FONTPREFS_TD_NORMAL="font-size: large;font-weight:bold;font-family:arial";
$TITLESTYLE="text-decoration: none; font-size: medium;font-weight:normal;font-family:arial;color: $RODIN_LOGO_YELLOW";

function generate_src_refining_fields($id)
{
	global $RODINUTILITIES_GEN_URL;
	global $COLOR_BROADER;
	global $COLOR_NARROWER;
	global $COLOR_RELATED;
	global $RODINU;
	global $NUMBER_OF_COLS_EACH_FIELD;
	global $NUMBER_OF_ROWS_EACH_FIELD;
	global $FONTPREFS_TD;
	
	if ($id) $ID_EXT="_$id";
	
	$TXT=<<<EOT
		
	<tr height="10" />
	<tr>
		<td/>
		<td bgcolor='$COLOR_BROADER' 
				style="border:groove"
				valign='top' 
				title='Broader terms found by the selected refinement engine'>
			<span class='refdesc'>&nbsp;Broader&nbsp;</span>
		</td>
		<td bgcolor='white' valign='top' style="border:groove;">
			<span class='reftextarea'>
				<textarea id='b_refinput$ID_EXT' cols="$NUMBER_OF_COLS_EACH_FIELD" rows="$NUMBER_OF_ROWS_EACH_FIELD" style="border:0px;$FONTPREFS_TD"
					title='Change or remove some suggested words or add your own words'
				></textarea>
			</span>
		</td>
		<td width=3/>
		<td valign='top'>
			<input type='button' 
					id='brs_broader$ID_EXT'
					value='Refine search' 
					onClick="q = parent.document.getElementById('query_res');
									 x = document.getElementById('b_refinput$ID_EXT');
									 parent.window.fri_rodin_ReSearch_META(parent.window.cleanup_refine(q.innerHTML+' '+x.value))"  
					title='Refine your query using broader terms'>
		</td>
	</tr>

	<tr height="0" />
	<tr>
		<td/>
		<td bgcolor='$COLOR_NARROWER' 
				style="border:groove;"
				valign='top' 
				title='Narrower terms found by the selected refinement engine'>
			<span class='refdesc'>&nbsp;Narrower&nbsp;</span>
		</td>
		<td bgcolor='white' valign='top' style="border:groove">
			<span class='reftextarea'>
				<textarea id='n_refinput$ID_EXT' cols="$NUMBER_OF_COLS_EACH_FIELD" rows="$NUMBER_OF_ROWS_EACH_FIELD" style="border:0px;$FONTPREFS_TD"
					title='Change or remove some suggested words or add your own words'
				></textarea>
			</span>
		</td>
		<td width=3/>
		<td valign='top'>
			<input type='button' 
					value='Refine search' 							
					id='brs_narrower$ID_EXT'
					onClick="q = parent.document.getElementById('query_res');
								 x = document.getElementById('n_refinput$ID_EXT');
								 parent.window.fri_rodin_ReSearch_META(parent.window.cleanup_refine(q.innerHTML+' '+x.value))"    
				title='Refine your query using narrower terms'>
		</td>
	</tr>
	
	<tr height="0" />
	<tr>
		<td/>
		<td bgcolor='$COLOR_RELATED'; 
				style="border:groove;"
				valign='top' 
				title='Related terms found by the selected refinement engine'>
			<span class='refdesc'>&nbsp;Related&nbsp;</span>
		</td>
		<td bgcolor='white' valign='top' style="border:groove">
			<span class='reftextarea'>
				<textarea id='r_refinput$ID_EXT' cols="$NUMBER_OF_COLS_EACH_FIELD" rows="$NUMBER_OF_ROWS_EACH_FIELD" style="border:0px;$FONTPREFS_TD"
					title='Change or remove some suggested words or add your own words'
				></textarea>
			</span>
		</td>
		<td width=3/>
		<td valign='top'>
			<input type='button' 
					value='Refine search' 							
					id='brs_related$ID_EXT'
					onClick="q = parent.document.getElementById('query_res');
								 x = document.getElementById('r_refinput$ID_EXT');
								 parent.window.fri_rodin_ReSearch_META(parent.window.cleanup_refine(q.innerHTML+' '+x.value))"    
				title='Refine your query using related terms'>
		</td>
	</tr>				
EOT;
	
	return $TXT;
}




function deletequote($txt)
{
	$s = str_replace("'","",$txt);
	return $s;
}

function urlblankencode($txt)
{
	$s = str_replace(" ","%20",$txt);
	return $s;
}



function cleanup4Tooltip($txt)
{
	//Replace quote by backtick (this seems to work in firefox)
	//$tooltip=addslashes(htmlspecialchars(stripslashes($txt)));
	$txt=stripslashes($txt);
	$tooltip=str_replace("'","`",$txt);
	
	return $tooltip;
}



function generate_div_res($content)
###############################
{
	$TXT=<<<EOT
	<div id='cache_res'>
		<table cellspacing="2" cellpadding="0" border=0>
			<tr>
			<td>
				<span class='restextarea'>
					<textarea id='{$short}_res' cols="49" rows="2" disabled
						style="	border:groove;text-align:center"
						title='Results You choose as basis to your query refinements'
					>$content</textarea>
				</span>
				</td>
			</tr>
		</table>
	 </div>
EOT;
	
	return $TXT;
}



function explodeX($delimiters,$string)
{
    $return_array = Array($string); // The array to return
    $d_count = 0;
    while (isset($delimiters[$d_count])) // Loop to loop through all delimiters
    {
        $new_return_array = Array(); 
        foreach($return_array as $el_to_split) // Explode all returned elements by the next delimiter
        {
            $put_in_new_return_array = explode($delimiters[$d_count],$el_to_split);
            foreach($put_in_new_return_array as $substr) // Put all the exploded elements in array to return
            {
                $new_return_array[] = $substr;
            }
        }
        $return_array = $new_return_array; // Replace the previous return array by the next version
        $d_count++;
    }
    return $return_array; // Return the exploded elements
}




function seems_utf8($str) {
 # get length, for utf8 this means bytes and not characters
 $length = strlen($str);  

 # we need to check each byte in the string
 for ($i=0; $i < $length; $i++) {

  # get the byte code 0-255 of the i-th byte
  $c = ord($str[$i]);

  # utf8 characters can take 1-6 bytes, how much
  # exactly is decoded in the first character if 
  # it has a character code >= 128 (highest bit set).
  # For all <= 127 the ASCII is the same as UTF8.
  # The number of bytes per character is stored in 
  # the highest bits of the first byte of the UTF8 
  # character. The bit pattern that must be matched
  # for the different length are shown as comment.
  #
  # So $n will hold the number of additonal characters

  if ($c < 0x80) $n = 0; # 0bbbbbbb
  elseif (($c & 0xE0) == 0xC0) $n=1; # 110bbbbb
  elseif (($c & 0xF0) == 0xE0) $n=2; # 1110bbbb
  elseif (($c & 0xF8) == 0xF0) $n=3; # 11110bbb
  elseif (($c & 0xFC) == 0xF8) $n=4; # 111110bb
  elseif (($c & 0xFE) == 0xFC) $n=5; # 1111110b
  else return false; # Does not match any model

  # the code now checks the following additional bytes
  # First in the if checks that the byte is really inside the
  # string and running over the string end.
  # The second just check that the highest two bits of all 
  # additonal bytes are always 1 and 0 (hexadecimal 0x80)
  # which is a requirement for all additional UTF-8 bytes

  for ($j=0; $j<$n; $j++) { # n bytes matching 10bbbbbb follow ?
   if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
    return false;
  }
 }
 return true;
}




function noparams(&$REQ)
{
	return (count($REQ) == 0);
}


function param_named($paramname,&$REQ)
{
	$found=false;
	foreach($REQ as $Param=>$boh)
		if ($paramname==$Param)
		{
			$found=true;
			break;
		}
		
	return $found;
}



function getApplLang()
{
	return $_SESSION['lang']!=''?$_SESSION['lang']:'en';
}



//$REFERER=get_referer(); // global
$FRIUTILITIES=1;
?>
