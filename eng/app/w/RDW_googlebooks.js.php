<?php
include_once("../u/RodinWidgetBase.php");

print <<<EOP
// About the http request section
var http_request = false;
var warning_IE_xmlhttp = 'GoogleBooks RODIN widget had a problem storing your result data';
var warning_xmlhttp = 'GoogleBooks RODIN WIDGET had a problem storing your result data';
var warning_bad_storage = 'Dear RODIN user, unfortunately your results could not be stored in the database. Please try your search with this RODIN Widget again later or contact us for help.';

// Process results sent in POST resquest
function googleBookyPOSTalertContents(base_url, chaining_url, warning_xmlhttp, warning_bad_storage) {
	if (http_request.readyState == 4) {
		if (http_request.status == 200) {
			result = http_request.responseText;
			var expr = /(-\d+)\sresults\sfor\s(.+)\sstored\sin\sDB/;
			var res = expr.exec(result);
			var inserted_elems = RegExp.$1; //1st match

			if (inserted_elems < 0)  {
				alert(warning_bad_storage);
				//Call the initial link to reset the process
				window.open(base_url,'_self');
		} else {
			// ok: redirect to next state
			window.open(chaining_url,'_self');
		}
		} 
	 else if (request.status == 404)
			alert("Requested URL does not exist or RODIN server is down");
	 else {
			alert(warning_xmlhttp);
		}
	}
}

function makePOSTRequest(url, parameters, chaining_url, warning_IE_xmlhttp, warning_xmlhttp) {
	http_request = false;
	
	if (window.XMLHttpRequest) { // Mozilla, Safari,...
		http_request = new XMLHttpRequest();
		if (http_request.overrideMimeType) {
			// set type accordingly to anticipated content type
			http_request.overrideMimeType('text/html');
		}
	} else if (window.ActiveXObject) { // IE
		try {
			http_request = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e) {
			try {
				http_request = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e) {
			alert(warning_IE_xmlhttp);
		}
		}
	}
	if (!http_request) {
		alert(warning_xmlhttp);
		return false;
	}
		
	// http_request.onreadystatechange = googleBookyPOSTalertContents; // only for ,,true) asynchronous call
	http_request.open('POST', url, false); //synchronous processing
	http_request.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	http_request.setRequestHeader("Content-length", parameters.length);
	http_request.setRequestHeader("Connection", "close");
	http_request.send(parameters);
  
	//if synchronous processing (false), the call back must be called manually:
	googleBookyPOSTalertContents(url, chaining_url ,warning_xmlhttp,warning_bad_storage);
}

EOP;

?>
