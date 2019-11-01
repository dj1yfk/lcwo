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
