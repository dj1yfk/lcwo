<?

include($_SERVER['DOCUMENT_ROOT']."/inc/definitions.php");
include($_SERVER['DOCUMENT_ROOT']."/inc/connectdb.php");

$query = mysqli_query($db, "update lcwo_users set cw_speed=20, cw_eff=10,
cw_tone=600, koch_lesson=1, player=3, vvv=0, course_duration=1,
groups_duration=1, koch_duration=1, lockspeeds=0, randomlength=0,
koch_randomlength=5, groups_randomlength=0, groups_mode='letters',
show_ministat=1 where id=".TESTUSER);

if (!$query) {
		echo "ERROR reset testuser\n";
}



?>
