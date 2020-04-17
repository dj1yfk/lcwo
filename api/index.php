<?

session_start();
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Pragma: no-cache");

function need_login() {
    if (!$_SESSION['uid']) {
        echo '{"msg": "you must log in to use this function"}';
        exit();
    }
}

include("../inc/functions.php");
include("../inc/connectdb.php");
include("../inc/definitions.php");

switch ($_GET['action']) {
case 'get_usergroups':
    get_usergroups();
    break;
case 'set_usergroup_location':
    set_usergroup_location();
    break;
case 'get_wordtraining_collections':
    get_wordtraining_collections();
    break;
case 'get_wordtraining_collection':
    get_wordtraining_collection();
    break;
case 'update_wordtraining':
    update_wordtraining();
    break;
case 'upload_wordtraining':
    upload_wordtraining();
    break;
case 'stats':
    stats();
    break;
}

function stats() {
    global $db;
    global $_GET;

    $item = $_GET['item'];

    if (!in_array($item, array("signups", "koch", "groups", "plaintext", "callsigns", "words", "qtc"))) {
        return;
    }

    switch ($item) {
    case 'signups':
        $query = "select count(*) as count, substr(signupdate, 1, 7) as date from lcwo_users where signupdate > '2000-01-01' group by date;";
        break;
    case 'koch':
        $query = "select count(*) as count, substr(time, 1, 7) as date from lcwo_lessonresults group by date;";
        break;
    case 'groups':
    case 'plaintext':
    case 'callsigns':
    case 'words':
    case 'qtc':
        $query = "select count(*) as count, substr(time, 1, 7) as date from lcwo_".$item."results group by date;";
        break;
    default:
        break;
    }

    $q = mysqli_query($db, $query);
    $out = array();
    while ($d = mysqli_fetch_array($q, MYSQLI_ASSOC)) {
        array_push($out, $d);
    }
    echo json_encode($out);
}

function upload_wordtraining () {
    global $db, $langs, $g_wordeditors;
    need_login();

    if (!in_array($_SESSION['uid'], $g_wordeditors)) {   # FIXME - build a proper permission system
        return;
    }

    $postdata = file_get_contents("php://input");
    $data = json_decode($postdata, true);

    if ($data) {
        array_push($langs, "cw");
        $lang = $data['lang'];
        $collid = $data['collid'] + 0;
        $collection = mysqli_real_escape_string($db, $data['collection']);

        if (!in_array($lang, $langs)) {
            echo '{"msg": "Invalid language."}';
            return;
        }

        if (!isint($collid)) {
            echo '{"msg": "Invalid collection ID."}';
            error_log(print_r($data, 1));
            return;
        }

        $query = "insert into lcwo_words (`lang`, `word`, `lesson`, `collid`, `collection`) VALUES ";
        $query_arr = array();
        foreach ($data['words'] as $w) {
            $word = mysqli_real_escape_string($db, $w['w']);
            $lesson = isint($w['l']) ? $w['l'] : 40;
            array_push($query_arr, "('$lang', '$word', $lesson, $collid, '$collection')");
        }
        $query .= join(',', $query_arr);

        if (mysqli_query($db, $query)) {
            echo '{"msg": "OK"}';
        }
        else {
            echo '{"msg": "Database error. Contact administrator."}';
            error_log($query);
        }
    }
    else {
        echo '{"msg": "Could not decode data."}';
    }
}

function update_wordtraining () {
    global $db, $langs, $g_wordeditors;
    need_login();

    if (!in_array($_SESSION['uid'], $g_wordeditors)) {   # FIXME - build a proper permission system
        return;
    }

    $postdata = file_get_contents("php://input");
    $data = json_decode($postdata, true);

    if ($data['lesson'] >= 1 and $data['lesson'] <= 40 and isint($data['ID'])) {
	$word   = mysqli_real_escape_string($db, $data['word']);
	$collection = mysqli_real_escape_string($db, $data['collection']);

        if ($data['ID'] > 0) { # update existing entry
            if ($word) {
                $query = "update lcwo_words set word='$word', lesson=".$data['lesson']." where ID=".$data['ID'];
            }
            else {
                $query = "delete from lcwo_words where ID=".$data['ID'];
            }

            $q = mysqli_query($db, $query);
            if (!$q) {
                error_log("SQL error: ".$query." => ".mysqli_error($db));
                echo '{"msg": "Error"}';
            }
            else {
                echo '{"msg": "OK"}';
            }
        }
        else { // new entry
            // valid lang?
            if (!in_array($data['lang'], $langs)) {
                echo '{"msg": "Invalid language >'.$data['lang'].'<"}';
                return;
            }
            // check for duplicate
            $query = "select count(*) from lcwo_words where lang='".$data['lang']."' and collection='".$collection."' and word='".$word."'";
            $q = mysqli_query($db, $query);
            $r = mysqli_fetch_row($q);
            if ($r[0] == 0) {

                # find correct collection id
                $query = "select collid from lcwo_words where collection='$collection'";
                $q = mysqli_query($db, $query);
                if ($r = mysqli_fetch_row($q)) {
                    $collid= $r[0];
                    error_log("Existing collection with id = $collid");
                }
                else { # collection does not exist yet, select next one
                    $query = "select max(collid) from lcwo_words where lang='".$data['lang']."'";
                    $q = mysqli_query($db, $query);
                    $r = mysqli_fetch_row($q);
                    $collid = $r[0] + 1;
                    error_log("NEW collection $query with id = $collid");
                }

                $query = "insert into lcwo_words (`lang` , `word`, `collid`, `collection`, `lesson`) VALUES ('".$data['lang']."', '".$word."', '".$collid."', '".$collection."', '".$data['lesson']."')";
                if (mysqli_query($db, $query)) {
                    echo '{"msg": "Added new word '.$word.'"}';
                }
                else {
                    echo '{"msg": "DB error"}';
                    error_log(mysqli_error($db));
                }
            }
            else {
                echo '{"msg": "duplicate, not added"}';
            }
        }
    }

}

function get_wordtraining_collections() {
    global $db, $enlangnames;
    $q = mysqli_query($db, "select distinct lang, collid, collection from lcwo_words order by lang asc, collid asc");
    $out = array();
    while ($d = mysqli_fetch_array($q, MYSQLI_ASSOC)) {
        array_push($out, $d);
    }
    echo json_encode($out);
}


function get_wordtraining_collection() {
    global $db;

    $coll = $_GET['id'];
    $filter = mysqli_real_escape_string($db, $_GET['filter']);
    $left = ($_GET['left'] == '1') ? '' : '%'; 
    $right = ($_GET['right'] == '1') ? '' : '%'; 

    if (!preg_match('/^[a-z]{2}\d+$/', $coll)) {
        return "[]";
    }

    $lang = substr($coll, 0,2);
    $collid = substr($coll, 2);
    $query = "select ID, word, lesson from lcwo_words where lang='$lang' and collid=$collid and word like '$left$filter$right' order by word asc";
    error_log($query);
    $q = mysqli_query($db, $query);
    $out = array();
    while ($d = mysqli_fetch_array($q, MYSQLI_ASSOC)) {
        $d['word'] = iconv("ISO-8859-1", "UTF-8//IGNORE", $d['word']);
        array_push($out, $d);
    }
    error_log(count($out));
    $ret = json_encode($out);
#    error_log("ret:" . $ret);
    echo $ret;
}




function get_usergroups () {
    global $db;
    $q = mysqli_query($db, "select gid, groupname, groupdescription, lat, lon from lcwo_usergroups");
    $out = array();
    while ($d = mysqli_fetch_array($q, MYSQLI_ASSOC)) {
        $d['groupname'] = iconv("ISO-8859-1", "UTF-8//IGNORE", $d['groupname']);
        $d['groupdescription'] = iconv("ISO-8859-1", "UTF-8//IGNORE", $d['groupdescription']);
        array_push($out, $d);
    }
    echo json_encode($out);
}

function set_usergroup_location() {
    need_login();
    global $db;
    $gid = $_GET['gid']+0;
    $lat = $_GET['lat']+0;
    $lon = $_GET['lon']+0;

    if (is_numeric($gid) && is_admin_of($_SESSION['uid'], $gid)
    && is_numeric($lat) && is_numeric($lon)) {
        $q = mysqli_query($db, "update lcwo_usergroups set lat=$lat, lon=$lon where gid=$gid;");
        if ($q) {
            echo '{"msg": "ok"}';
        }
        else {
            echo '{"msg": "operation failed"}';
        }
    }
    else {
        echo '{"msg": "invalid parameters"}';
    }
}





?>
