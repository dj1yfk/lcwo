<br><br><br> 
<div align="center" class="footer">lcwo.net - <strong>Learn Morse Code (CW) Online</strong> by <a class="sLink" href="http://fkurz.net/">Fabian Kurz, DJ5CW</a> (<a href="/impressum">Impressum</a>) - <a href="/privacy">Privacy Policy / Datenschutzinformationen</a></div>
<script>
// keep the session cookie alife as long as user is on the page, refresh every
// 5 minutes
function session_keepalive () {
	var request =  new XMLHttpRequest();
	request.open("GET", "//<?=HOSTNAME;?>/api/index.php?action=keepalive", true);
    request.onreadystatechange = function() {
        var done = 4, ok = 200;
        if (request.readyState == done && request.status == ok) {
            var r = JSON.parse(request.responseText);
            if (r["result"] == false) { // no session
                if (document.getElementById("logoutlink")) {  // but we think we're logged in!
                    document.location.href = "//<?=HOSTNAME;?>";
                }
            }
        }
    }
	request.send();
}
window.setInterval('session_keepalive()', 300000);
</script>

