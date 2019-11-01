<script>
function babel(name, content) {
	var be = document.getElementById('babeledit');
	var bn = document.getElementById('babelname');
	var bi = document.getElementById('babelinput');
	be.style.visibility = 'visible'; 
	bn.innerHTML = name;
	bi.value = content; 
	bi.focus();
}



function babeltx (k,v) {

var encodedv = '';
var lang = 0;
var inpage;

if (k.indexOf("-") == -1) {		/* no - so it's from inpage translate */
	k = k.substr(6);			/* remove babel_ prefix */
	inpage = true;
}
else {							/* normal babelfish, format NAME-LANG */
	var tmp = k.split("-");
	lang = tmp[1];
	k = tmp[0]; 		
	inpage = false;
}


for (i=0;i<v.length;i++) {
	c = v.charCodeAt(i);
	if (!c) continue;
	if (c > 122) {
		encodedv += '&#' + c + ';';
	}
	else {
		encodedv += v.charAt(i);
	}
}

if( typeof XMLHttpRequest == "undefined" ) XMLHttpRequest = function() {
  try { return new ActiveXObject("Msxml2.XMLHTTP.6.0") } catch(e) {}
  try { return new ActiveXObject("Msxml2.XMLHTTP.3.0") } catch(e) {}
  try { return new ActiveXObject("Msxml2.XMLHTTP") } catch(e) {}
  try { return new ActiveXObject("Microsoft.XMLHTTP") } catch(e) {}
  throw new Error( "This browser does not support XMLHttpRequest." )
};

var posturl = "/babelfish.php";

if (inpage) {
	var babel_response = document.getElementById('babelresponse');
}
else {
	var babel_response = document.getElementById('babelresponse-'+k+'-'+lang);
}

 var request =  new XMLHttpRequest();
  request.open("POST", posturl, true);
  request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
 
  request.onreadystatechange = function() {
    var done = 4, ok = 200;
    if (request.readyState == done && request.status == ok) {
      if (request.responseText) {
			babel_response.innerHTML =  request.responseText;

			/* changes on the website */
			var edited = document.getElementsByName('babel_'+k);
			for (var x=0; x < edited.length; x++) {
				edited[x].innerHTML = encodedv;
			}
      }
	  else {
			babel_response.innerHTML =  "<b>ERROR: fail.</b>";
	  }
    }
  };
  request.send('ajax=1&l='+lang+'&k='+k+'&v='+encodeURIComponent(encodedv));
}

</script>
