<?


	include("inc/connectdb.php");
	include("inc/functions.php");


	$d = opendir("img/");

	if (!$d) {
		echo "Fail!";
		exit;
	}
	while ($f = readdir($d)) {
			if (preg_match('/userimage(\d+)/', $f, $matches)) {
					echo "<img src='img/$f'> $f - ".uid2uname($matches[1])."<br><hr>\n";
			}
                        if (preg_match('/groupimage(\d+)/', $f, $matches)) {
                                        echo "<img src='img/$f'> $f - <br><hr>\n";
                        }

	}


?>
