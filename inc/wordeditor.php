<?
if (!$_SESSION['uid']) {
		echo "Sorry, you must be logged in to use this function.";
		return 0;
}
?>


<script>
function load_langs () {
    var request =  new XMLHttpRequest();
    request.open("GET", '/api/index.php?action=get_wordtraining_collections', true);
    request.onreadystatechange = function() {
            var done = 4, ok = 200;
            if (request.readyState == done && request.status == ok) {
                    if (request.responseText) {
                        var p = JSON.parse(request.responseText);
                        var t = document.getElementById('select_lang');
                        var o = '<table> <thead> <tr><th>Language</th><th>Collection ID</th><th>Description</th><th>Action</th></thead><tbody>';
                        for (var i = 0; i < p.length; i++) {
                            var cid = p[i]['lang'] + p[i]['collid'];
                            o += '<tr><td>' + p[i]['lang'] + '</td><td>' + p[i]['collid'] + '</td><td>' + p[i]['collection'] + "</td><td><a href=\"javascript:load('" + cid + "');\">Load</a></td></tr>";
                        }
                        o += '</tbody> </table>';
                        t.innerHTML = o;
                    }
            };
    }
    request.send();
}
load_langs();

function load(l) {
    var d = document.getElementById('editor');

    var request =  new XMLHttpRequest();
    request.open("GET", '/api/index.php?action=get_wordtraining_collection&id=' + l, true);
    request.onreadystatechange = function() {
            var done = 4, ok = 200;
            if (request.readyState == done && request.status == ok) {
                    if (request.responseText) {
                        var p = JSON.parse(request.responseText);
                        var o = '<table> <thead> <tr><th>ID</th><th>Word</th><th>Lesson</th><th>Actions</th></thead><tbody>';
                        for (var i = 0; i < p.length; i++) {
                            o += '<tr><td>' + p[i]['ID'] + '</td><td><input id="w' + p[i]['ID'] + '" onkeyup="upd_lesson(this.id);" type="text" width="20" value="' + p[i]['word'] + '"></td><td><span id="l' + p[i]['ID'] + '">' + p[i]['lesson'] + '</span></td><td><a href="javascript:save(' + p[i]['ID'] + ');">Save</a></td></tr>';
                        }
                        o += '</tbody> </table>';
                        d.innerHTML = o;
                    }
            };
    }
    request.send();

}

function upd_lesson(i) {
    var id = i.substr(1);
    var ls = document.getElementById('l' + id);
    ls.innerHTML = lesson(document.getElementById(i).value);
}

function lesson (word) {
	return 40;
}

</script>

<div id="menu">
<h1 id="header1">Edit word training lists</h1>
<h2>Select a lanuage from the list</h2>
<div id="select_lang">Loading...</div>
</div>

<div id="editor">
</div>

