function askToCreateDB() {
	var xmlhttp = new XMLHttpRequest();
	
	xmlhttp.open("POST","DBPediaUpdaterResponder.php",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			reportInitialization(xmlhttp.responseXML);
		}
	}
	
	xmlhttp.send("action=initialize");
}

function askToFillFileLists() {
	var lang = document.getElementById("lang_input").value;
	
	var xmlhttp = new XMLHttpRequest();
	
	xmlhttp.open("POST","DBPediaUpdaterResponder.php",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			fillFilesLists(xmlhttp.responseXML);
		}
	}
	
	xmlhttp.send("action=filesToUpdate&lang=" + lang);
}

function askToUploadTempFile(filename) {
	var uploadButton = document.getElementById("upload_" + filename);
	uploadButton.innerHTML = "Processing";
	uploadButton.disabled = true;
	
	var lang = document.getElementById("lang_input").value;
	
	var xmlhttp = new XMLHttpRequest();
	
	xmlhttp.open("POST","DBPediaUpdaterResponder.php",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			reportFileUpload(xmlhttp.responseXML);
		}
	}
	
	xmlhttp.send("action=uploadTempFile&file=" + filename + "&lang=" + lang);
}

function askToUploadFile(filename) {
	var uploadButton = document.getElementById("upload_" + filename);
	uploadButton.innerHTML = "Processing";
	uploadButton.disabled = true;
	
	var lang = document.getElementById("lang_input").value;
	
	var xmlhttp = new XMLHttpRequest();
	
	xmlhttp.open("POST","DBPediaUpdaterResponder.php",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			reportFileUpload(xmlhttp.responseXML);
		}
	}
	
	xmlhttp.send("action=uploadFile&file=" + filename + "&lang=" + lang);
}

function askToRemoveFile(filename) {
	var removeButton = document.getElementById("remove_" + filename);
	removeButton.innerHTML = "Processing";
	removeButton.disabled = true;
	
	var lang = document.getElementById("lang_input").value;
	
	var xmlhttp = new XMLHttpRequest();
	
	xmlhttp.open("POST","DBPediaUpdaterResponder.php",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	
	xmlhttp.onreadystatechange=function() {
		if (xmlhttp.readyState==4 && xmlhttp.status==200) {
			reportFileRemoval(xmlhttp.responseXML);
		}
	}
	
	xmlhttp.send("action=removeFile&file=" + filename + "&lang=" + lang);
}

function fillFilesLists(xmlResponse) {
	var answer = xmlResponse.getElementsByTagName("answer")[0];
	
	var toRemove = answer.getElementsByTagName("remove")[0].getElementsByTagName("file");
	var toUpload = answer.getElementsByTagName("upload")[0].getElementsByTagName("file");
	var upToDate = answer.getElementsByTagName("todate")[0].getElementsByTagName("file");
	var inTemp = answer.getElementsByTagName("intemp")[0].getElementsByTagName("file");
	var time = answer.getElementsByTagName("time")[0].getAttribute("sec");
	
	var upToDateHTML = "<ul>";
	for (i=0;i<upToDate.length;i++) {
		var filenameToDate = upToDate[i].getAttribute("filename");
		
		upToDateHTML += "<li>" + filenameToDate + " ";
		upToDateHTML += "<button id=\"remove_" + filenameToDate + "\" onclick=\"javascript:askToRemoveFile('" + filenameToDate + "');\">Remove!</button>";
		upToDateHTML += "</li>";
	}
	upToDateHTML += "</ul>"
	
	var upToDateDiv = document.getElementById("files_uptodate");
	upToDateDiv.innerHTML = upToDateHTML;
		
	var toRemoveHTML = "<ul>";
	for (i=0;i<toRemove.length;i++) {
		var filenameToRemove = toRemove[i].getAttribute("filename");
		
		toRemoveHTML += "<li>" + filenameToRemove + " ";
		toRemoveHTML += "<button id=\"remove_" + filenameToRemove + "\" onclick=\"javascript:askToRemoveFile('" + filenameToRemove + "');\">Remove!</button>";
		toRemoveHTML += "</li>";
	}
	toRemoveHTML += "</ul>"
	
	var toRemoveDiv = document.getElementById("files_to_remove");
	toRemoveDiv.innerHTML = toRemoveHTML;
	
	var toUploadHTML = "<ul>";
	for (i=0;i<toUpload.length;i++) {
		var filenameToUpload = toUpload[i].getAttribute("filename");
		var fileSize = toUpload[i].getAttribute("size");
		
		toUploadHTML += "<li>" + filenameToUpload + " (" + fileSize + ") ";
		toUploadHTML += "<button id=\"upload_" + filenameToUpload + "\" onclick=\"javascript:askToUploadFile('" + filenameToUpload + "');\">Upload!</button>";
		toUploadHTML += "</li>";
	}
	toUploadHTML += "</ul>"
		
	var toUploadDiv = document.getElementById("files_to_upload");
	toUploadDiv.innerHTML = toUploadHTML;
	
	// Files in temporal Folder
	var inTempHTML = "<ul>";
	for (i=0;i<inTemp.length;i++) {
		var filenameInTemp = inTemp[i].getAttribute("filename");
		var fileSize = inTemp[i].getAttribute("size");
		
		inTempHTML += "<li>" + filenameInTemp + " (" + fileSize + ") ";
		inTempHTML += "<button id=\"upload_" + filenameInTemp + "\" onclick=\"javascript:askToUploadTempFile('" + filenameInTemp + "');\">Upload!</button>";
		inTempHTML += "</li>";
	}
	inTempHTML += "</ul>"
		
	var inTempDiv = document.getElementById("files_in_temp");
	inTempDiv.innerHTML = inTempHTML;
	
	// Refresh status message
	var messageDiv = document.getElementById("message");
	messageDiv.innerHTML = 'Refreshing lists took approx. ' + time + 's.';
}

function reportFileRemoval(xmlResponse) {
	var answer = xmlResponse.getElementsByTagName("answer")[0];
	
	var originalFilename = answer.getElementsByTagName("file")[0].getAttribute("filename");
	var triplets = answer.getElementsByTagName("triplets")[0].getAttribute("count");
	var time = answer.getElementsByTagName("time")[0].getAttribute("sec");
	
	var messageDiv = document.getElementById("message");
	messageDiv.innerHTML = 'Removed ' + triplets + ' triplets (approx. ' + time + 's).';
	
	var removeButton = document.getElementById("remove_" + originalFilename);
	removeButton.innerHTML = "Done";
	removeButton.disabled = true;
}

function reportFileUpload(xmlResponse) {
	var answer = xmlResponse.getElementsByTagName("answer")[0];
	
	var originalFilename = answer.getElementsByTagName("file")[0].getAttribute("filename");
	var triplets = answer.getElementsByTagName("triplets")[0].getAttribute("count");
	var time = answer.getElementsByTagName("time")[0].getAttribute("sec");
	
	var messageDiv = document.getElementById("message");
	messageDiv.innerHTML = 'Imported ' + triplets + ' new triplets (approx. ' + time + 's).';
	
	var uploadButton = document.getElementById("upload_" + originalFilename);
	uploadButton.innerHTML = "Done";
	uploadButton.disabled = true;
}