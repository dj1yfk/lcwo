function submitscore (type, val1, val2, val3, response_location) {

// Provide the XMLHttpRequest class for IE 5.x-6.x:
if( typeof XMLHttpRequest == "undefined" ) XMLHttpRequest = function() {
  try { return new ActiveXObject("Msxml2.XMLHTTP.6.0") } catch(e) {}
  try { return new ActiveXObject("Msxml2.XMLHTTP.3.0") } catch(e) {}
  try { return new ActiveXObject("Msxml2.XMLHTTP") } catch(e) {}
  try { return new ActiveXObject("Microsoft.XMLHTTP") } catch(e) {}
  throw new Error( "This browser does not support XMLHttpRequest." )
};

  var url = window.location.href;
  var arr = url.split("/");
  var posturl = "//" + arr[2] + "/api/scoresubmit.php";

  var request =  new XMLHttpRequest();
  request.open("POST", posturl, true);
  request.setRequestHeader("Content-Type",
                           "application/x-www-form-urlencoded");
 
  request.onreadystatechange = function() {
    var done = 4, ok = 200;
    if (request.readyState == done && request.status == ok) {
      if (request.responseText) {
			response_location.innerHTML =  request.responseText;
      }
	  else {
			response_location.innerHTML =  "<b>ERROR: Adding score failed.</b>";
	  }
    }
  };
  request.send('type='+type+'&val1='+val1+'&val2='+val2+'&val3='+val3);



}


