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
}

function get_wordtraining_collections() {
    global $db, $enlangnames;
    $q = mysqli_query($db, "select distinct lang, collid, collection from lcwo_words");
    $out = array();
    while ($d = mysqli_fetch_array($q, MYSQLI_ASSOC)) {
        if ($d['collection'] == "") {
            $d['collection'] = $enlangnames[$d['lang']];
        }
        array_push($out, $d);
    }
    echo json_encode($out);
}


function get_wordtraining_collection() {
    global $db;

    $coll = $_GET['id'];

    if (!preg_match('/^[a-z]{2}\d+$/', $coll)) {
        return "[]";
    }

    $lang = substr($coll, 0,2);
    $collid = substr($coll, 2);
    $query = "select ID, word, lesson from lcwo_words where lang='$lang' and collid=$collid";
    error_log($query);
    $q = mysqli_query($db, $query);
    $out = array();
    while ($d = mysqli_fetch_array($q, MYSQLI_ASSOC)) {
        $d['word'] = iconv("ISO-8859-1", "UTF-8//IGNORE", $d['word']);
        array_push($out, $d);
    }
    error_log(count($out));
    $ret = json_encode($out);
    error_log("ret:" . $ret);
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
