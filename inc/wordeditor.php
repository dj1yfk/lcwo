<?
if (!$_SESSION['uid']) {
		echo "Sorry, you must be logged in to use this function.";
		return 0;
}
?>


<script>

var g_lang = "";
var g_collid = "";
var g_collection = "";
var g_filter = "";
var g_left = 1;     // match against start of word
var g_right = 0;    // match against end of word
var g_middle = 0;    // match against end of word

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
                            o += '<tr><td>' + p[i]['lang'] + '</td><td>' + p[i]['collid'] + '</td><td>' + p[i]['collection'] + "</td><td><a href=\"javascript:load('" + p[i]['lang'] + "', '" + p[i]['collid'] + "', '" + p[i]['collection'] + "', '" + g_filter + "');\">Load</a></td></tr>";
                        }
                        o += '</tbody> </table>';
                        t.innerHTML = o;
                    }
            };
    }
    request.send();
}
load_langs();


// https://www.endyourif.com/set-cursor-position-of-textarea-with-javascript/
function setSelectionRange(input, selectionStart, selectionEnd) {
  if (input.setSelectionRange) {
    input.focus();
    input.setSelectionRange(selectionStart, selectionEnd);
  }
  else if (input.createTextRange) {
    var range = input.createTextRange();
    range.collapse(true);
    range.moveEnd('character', selectionEnd);
    range.moveStart('character', selectionStart);
    range.select();
  }
}

function setCaretToPos (input, pos) {
   setSelectionRange(input, pos, pos);
}



function load(lang, collid, collection, g_filter) {

    // remember which collection we currently work on, for file upload
    g_lang = lang;
    g_collid = collid;
    g_collection = collection;

    document.getElementById('cc').innerHTML = lang + "-" + collid;

    var l = lang + collid;

    var d = document.getElementById('editor');

    var request =  new XMLHttpRequest();
    request.open("GET", '/api/index.php?action=get_wordtraining_collection&id=' + l + '&filter=' + g_filter + '&left=' + g_left + '&right=' + g_right + '&middle=' + g_middle, true);
    request.onreadystatechange = function() {
            var done = 4, ok = 200;
            if (request.readyState == done && request.status == ok) {
                    if (request.responseText) {
                        var p = JSON.parse(request.responseText);
                        var o = '<h2>Editable list of words (' + p.length + ')</h2>';
                        o += 'Filter: <input type="text" id="filter" onkeyup="filter_list(this.value);" value="' +  g_filter + '">';
                        o += ' <input onChange="load_g();" id="filterLeft" type="checkbox" value="1" ' + (g_left == 1 ? 'checked' : '') + '> Match left - ';
                        o += ' <input onChange="load_g();" id="filterRight" type="checkbox" value="1" ' + (g_right == 1 ? 'checked' : '') + '> Match right ';
                        o += ' <input onChange="load_g();" id="filterMiddle" type="checkbox" value="1" ' + (g_middle == 1 ? 'checked' : '') + '> Match middle ';
                        o += '<table> <thead> <tr><th>ID</th><th>Word</th><th>Lesson</th><th>Actions</th></thead><tbody>';
                        for (var i = 0; i < p.length; i++) {
                            o += '<tr><td>' + p[i]['ID'] + '</td><td><input id="w' + p[i]['ID'] + '" onkeyup="upd_lesson(this.id);" type="text" width="20" value="' + p[i]['word'] + '"></td><td><span id="l' + p[i]['ID'] + '">' + p[i]['lesson'] + '</span></td><td><a href="javascript:save(' + p[i]['ID'] + ');">Save</a> <a href="javascript:del(' + p[i]['ID'] + ');">Delete</a> <span id="r'+ p[i]['ID'] + '"></span></td></tr>';
                        }
                        o += '</tbody> </table>';
                        d.innerHTML = o;
						setCaretToPos(document.getElementById("filter"), g_filter.length);
                    }
            };
    }
    request.send();

}

function filter_list (f) {
    g_filter = f;
    load(g_lang, g_collid, g_collection, g_filter);
}

function load_g() {
    g_left = document.getElementById('filterLeft').checked ? 1 : 0;
    g_right = document.getElementById('filterRight').checked ? 1 : 0;
    g_middle = document.getElementById('filterMiddle').checked ? 1 : 0;

    // id middle is checked, we cannot have left and right
    if (g_middle && (g_right || g_left)) {
        g_left = 0;
        g_right = 0;
        document.getElementById('filterLeft').checked = false;
        document.getElementById('filterRight').checked = false;
    }

    load(g_lang, g_collid, g_collection, g_filter);
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

function del (id) {
    if (confirm("really delete")) {
        document.getElementById('w' + id).value = "";
        save(id);	// saving with empty string => delete
    }
}


function save (id) {
    var text = document.getElementById('w' + id).value;
    var lesson = document.getElementById('l' + id).innerHTML;
    // https://stackoverflow.com/questions/18749591/encode-html-entities-in-javascript#18750001
    //var enc = text.replace(/[\u00A0-\u9999<>\&]/gim, function(i) { return '&#'+i.charCodeAt(0)+';'; });
    enc = text;
    
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
                    load_langs();
            };
    }

    if (id == 0) {  // new word
        var lang   = document.getElementById('lang0').value;
        var collection = document.getElementById('c0').value;
       // collection = collection.replace(/[\u00A0-\u9999<>\&]/gim, function(i) { return '&#'+i.charCodeAt(0)+';'; });
    }
    else {
        var lang = "";
        var collection = "";
    }
    request.send(JSON.stringify({"ID": id, "lang": lang, "collection": collection, "word": enc, "lesson": lesson}));
}

function load_file(f) {
    if (f.files[0]) {
        var reader = new FileReader();
        reader.onload = function(f) {
            var filecontents = f.target.result;
            filecontents = filecontents.replace(/\r/g, "");
            var words = filecontents.split("\n");

            var res = document.getElementById("uploadresult");

            var t = "Read a file with " + words.length + " words...<br>";

            var valid = [];
            var dupes = {};

            for (var i = 0; i < words.length; i++) {
                //words[i] = words[i].replace(/[\u00A0-\u9999<>\&]/gim, function(i) { return '&#'+i.charCodeAt(0)+';'; });
                if (words[i].includes(" ") || words[i].length == 0) {
                    t += "Error: Spaces or empty lines not allowed (line " + i + ": '" + words[i] + "')<br>";
                }
                else if (dupes[words[i]]) {
                    t += "Error: Duplicate word (line " + i + ": '" + words[i] + "')<br>";
                }
                else {
                    var l = lesson(words[i]);
                    valid.push({"w": words[i], "l": l });
                    dupes[words[i]] = 1;
                }
            }

            t += " ... " + valid.length + " valid words<br>";
            
            if (g_lang != "" && g_collid != "") {
                t += "Submitting words to collection " + g_collection + " (id: " + g_collid + ", language: " + g_lang + ") to server... reply: ";

                var w = {"collid": g_collid, "collection": g_collection, "lang": g_lang, "words": valid };
                var request =  new XMLHttpRequest();
                request.open("POST", '/api/index.php?action=upload_wordtraining', true);
                request.onreadystatechange = function() {
                    var done = 4, ok = 200;
                    if (request.readyState == done && request.status == ok) {
                        if (request.responseText) {
                            var u = JSON.parse(request.responseText);
                            if (u['msg']) {
                                document.getElementById('uploadresult').innerHTML += u['msg'];
                            }
                        }
                    };
                }
                request.send(JSON.stringify(w));
		
            }
            else {
                t += "No collection selected/loaded. Please select one and then load file again.";
            }

            res.innerHTML = t;
            


        };
        reader.readAsText(f.files[0]);
    }
}

</script>

<div id="menu">
<h1 id="header1">Edit word training lists</h1>
<h2>Select a language from the list</h2>
<div id="select_lang">Loading...</div>
</div>

<br><br>

<h2>Add new words</h2>
<p>Add new words to an existing collection (by selecting the language and entering the collection name), or create a new collection by entering a new name for a collection.</p>
<table>
<tr><th>Language</th><th>Collection name</th><th>Word</th><th>Lesson</th><th>Action</th></tr>
<tr><td>
<select id="lang0" size="1">
<?
foreach ($langs as $lang) {
        if ($lang == $_SESSION['lang']) {
                echo "<option value=\"$lang\" selected>$lang - ".$langnames[$lang]." (".$enlangnames[$lang].")</option>";
        }
        else {
                echo "<option value=\"$lang\">$lang - ".$langnames[$lang]." (".$enlangnames[$lang].")</option>";
        }
}
?>
</select>
</td>
<td><input id="c0" type="text" length="20" value=""></td>
<td><input id="w0" type="text" length="20" value="" onkeyup="upd_lesson('w0');"></td>
<td><span id="l0"></span></td>
<td><a href="javascript:save(0);">Save</a> &nbsp; <span id="r0"></span>
</table>

<br><br>

<h2>Upload text file into current collection (<span id="cc">none selected</span>)</h2>
<input type="file" onchange="load_file(this);">

<div id="uploadresult">
</div>
<br><br>

<div id="editor">
</div>

