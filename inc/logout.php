<?

if ($_SESSION["uid"]) {
    mysqli_query($db, "delete from lcwo_online where `UID`=".$_SESSION["uid"]);
}

session_destroy();

echo "<p>".l('youloggedout')."</p>";

?>
<script>
function deleteAllCookies()  {
  var cookies = document.cookie.split(";");

  for (var cookie of cookies) {
    var eqPos = cookie.indexOf("=");
    var name = eqPos > -1 ? cookie.substr(0, eqPos) : cookie;
    document.cookie = name + "=;expires=Thu, 01 Jan 1970 00:00:00 GMT";
  }
}
deleteAllCookies();
</script>
