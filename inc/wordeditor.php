<?
if (!$_SESSION['uid']) {
		echo "Sorry, you must be logged in to use this function.";
		return 0;
}
?>


<script>

var koch_chars = ['K','M','U','R','E','S','N','A','P','T','L','W', 'I','.','J','Z','=','F','O','Y',',','V','G','5','/','Q','9','2', 'H','3','8','B','?','4','7','C','1','D','6','0','X'];

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
                            o += '<tr><td>' + p[i]['ID'] + '</td><td><input id="w' + p[i]['ID'] + '" onkeyup="upd_lesson(this.id);" type="text" width="20" value="' + p[i]['word'] + '"></td><td><span id="l' + p[i]['ID'] + '">' + p[i]['lesson'] + '</span></td><td><a href="javascript:save(' + p[i]['ID'] + ');">Save</a> <span id="r'+ p[i]['ID'] + '"></span></td></tr>';
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
    var s = word.toUpperCase().split("");

    var minlesson = 1;
    for (var i = 0; i < s.length; i++) {
       var x = koch_chars.indexOf(s[i]);

       // character is not in the Koch char set (e.g. umlauts)
       if (x == -1) {
           minlesson = 40;
       }
       else if (x > minlesson) {
           minlesson = koch_chars.indexOf(s[i]);
       }
    }

    return minlesson;
}

function save (id) {
    var text = document.getElementById('w' + id).value;
    var lesson = document.getElementById('l' + id).innerHTML;
    // https://stackoverflow.com/questions/18749591/encode-html-entities-in-javascript#18750001
    var enc = text.replace(/[\u00A0-\u9999<>\&]/gim, function(i) { return '&#'+i.charCodeAt(0)+';'; });
    
    document.getElementById('r' + id).innerHTML = "";

    var request =  new XMLHttpRequest();
    request.open("POST", '/api/index.php?action=update_wordtraining', true);
    request.onreadystatechange = function() {
            var done = 4, ok = 200;
            if (request.readyState == done && request.status == ok) {
                    if (request.responseText) {
                        var u = JSON.parse(request.responseText);
                        if (u['msg']) {
                            document.getElementById('r' + id).innerHTML = u['msg'];
                        }
                    }
            };
    }
    request.send(JSON.stringify({"ID": id, "word": enc, "lesson": lesson}));
}


</script>

<div id="menu">
<h1 id="header1">Edit word training lists</h1>
<h2>Select a lanuage from the list</h2>
<div id="select_lang">Loading...</div>
</div>

<div id="editor">
</div>

