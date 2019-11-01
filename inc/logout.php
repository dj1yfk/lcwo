<?

if ($_SESSION["uid"]) {
    mysqli_query($db, "delete from lcwo_online where `UID`=".$_SESSION["uid"]);
}

session_destroy();

echo "<p>".l('youloggedout')."</p>";

?>
