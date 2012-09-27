<script language="JavaScript" type="text/javascript">
//Gets the browser specific XmlHttpRequest Object
function getXmlHttpRequestObject() {
 if (window.XMLHttpRequest) {
    return new XMLHttpRequest(); //Mozilla, Safari ...
 } else if (window.ActiveXObject) {
    return new ActiveXObject("Microsoft.XMLHTTP"); //IE
 } else {
    //Display our error message
    alert("Your browser doesn't support the XmlHttpRequest object.");
 }
}
 //Our XmlHttpRequest object
var receiveReq = getXmlHttpRequestObject();
//Initiate the AJAX request
function makeRequest(url, param) {
//If our readystate is either not started or finished, initiate a new request
 if (receiveReq.readyState == 4 || receiveReq.readyState == 0) {
   //Set up the connection to captcha_test.html. True sets the request to asyncronous(default) 
   receiveReq.open("POST", url, true);
   //Set the function that will be called when the XmlHttpRequest objects state changes
   receiveReq.onreadystatechange = updatePage(); 

   //Add HTTP headers to the request
   receiveReq.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
   receiveReq.setRequestHeader("Content-length", param.length);
   receiveReq.setRequestHeader("Connection", "close");

   //Make the request
   receiveReq.send(param);
 }   
}
//Called every time our XmlHttpRequest objects state changes
function updatePage() {
 //Check if our response is ready
 if (receiveReq.readyState == 4) {
   //Set the content of the DIV element with the response text
   document.getElementById('result').innerHTML = receiveReq.responseText;
   //Get a reference to CAPTCHA image
   img = document.getElementById('imgCaptcha'); 
   //Change the image
   //img.src = 'create_image.php?' + Math.random();
 }
}
//Called every time when form is perfomed
function getParam(theForm) {
 //Set the URL
 var url = 'captcha.php';
 //Set up the parameters of our AJAX call
 var postStr = theForm.txtCaptcha.name + "=" + encodeURIComponent ( theForm.txtCaptcha.value );
 //Call the function that initiate the AJAX request
 makeRequest(url, postStr);
}
</script>
<?php $code = '';
	 $charset = 'ABCDEFGHKLMNPRSTUVWYZ23456789';
	//$this->charset=34
    for($i = 1, $cslen = strlen($charset); $i <= 4; ++$i) {
	//code += majuscule de (? -> random( entre 0 et taille  txt-1)
      $code .= strtoupper( $charset{rand(0, $cslen - 1)} );
    }
	?>
<form id="frmCaptcha" name="frmCaptcha">
<table>
  <tr>
    <td>
		<img src="securimage_show.php?rand=<?php echo $code?>" id="imgCaptcha" align="absmiddle" />
    </td>
    
	 </tr>
  <tr>
    <td>
      <input id="txtCaptcha" type="text" name="txtCaptcha" value="" maxlength="10" size="32" />
    </td>
  </tr>
  <tr>
    <td>
      <input id="btnCaptcha" type="button" value="Captcha Test" name="btnCaptcha"
          onclick="getParam(document.frmCaptcha)" />
    </td>
  </tr>
</table> 

<div id="result">&nbsp;</div>
</form>
